<?php
require_once GTFWConfiguration::GetValue( 'application', 'docroot').
   'module/'.Dispatcher::Instance()->mModule.'/business/LogSirkulasi.class.php';

//set Label for module
require_once GTFWConfiguration::GetValue( 'application', 'docroot') .
   'module/label/response/Label.proc.class.php';

class ViewLogInput extends HtmlResponse
{
   function TemplateModule()
   {
      $this->SetTemplateBasedir(GTFWConfiguration::GetValue('application','docroot').
         'module/'.Dispatcher::Instance()->mModule.'/template');
      $this->SetTemplateFile('view_log_input.html');
   }
   
   function ProcessRequest()
   {
      $Obj = new LogSirkulasi;
      
      // inisialisasi messaging
		$msg = Messenger::Instance()->Receive(__FILE__);
      $this->Data = $msg[0][0];
		$this->Pesan = $msg[0][1];
		$this->css = $msg[0][2];
      // ---------
      
      // inisialisasi default value
      if (!empty($this->Data)) $data = $this->Data;
      else
      {
         $data = $Obj->GetDataLogByIdForInput($_GET['id']->Integer()->Raw());
         $data['logAtkJmlBrgOld'] = $data['logAtkJmlBrg'];
      }
      
      Messenger::Instance()->Send(Dispatcher::Instance()->mModule, 'logInput', 'process', 'proc', array($data), Messenger::UntilFetched);
      $return['data'] = $data;
      // ---------
      
      // inisialisasi url
      $return['url']['action'] = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'logInput', 'do', 'html');
      // ---------
      
      return $return;
   }
   
   function TemplateLabeling(){
      $ObjLabel = new GetModuleLabel();
      $arrLabel = $ObjLabel->GetLabel();
      
      //patTemplate name yang memiliki label2 dinamic
      $arrContent=array("content");
      
      for($i=0;$i<count($arrContent);$i++){
         for($j=0;$j<count($arrLabel);$j++){
            $this->mrTemplate->AddVar($arrContent[$i],$arrLabel[$j]['labelCode'],$arrLabel[$j]['labelName']);
         }
      }
   }
   
   function ParseTemplate($data = NULL)
   {
      //set Label
      $this->TemplateLabeling();
      
      // render message box
      if($this->Pesan)
      {
         $msg = '';
         if (count($this->Pesan) > 1) foreach ($this->Pesan as $value)
            $msg .= "\t$value<br/>\n";
         else $msg .= $this->Pesan[0];
         
			$this->mrTemplate->SetAttribute('warning_box', 'visibility', 'visible');
			$this->mrTemplate->AddVar('warning_box', 'ISI_PESAN', $msg);
			$this->mrTemplate->AddVar('warning_box', 'CLASS_PESAN', $this->css);
		}
      // ---------
      
      // Render Default Value
      $this->mrTemplate->AddVars('content', $data['data'], '');
      // ---------
      
      // Render URL
      $this->mrTemplate->AddVars('content', $data['url'], 'URL_');
      // ---------
   }
}
?>