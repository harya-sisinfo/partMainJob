<?php
/**
 * @package DoAddPenambahanAtk
 * @copyright Copyright (c) PT Gamatechno Indonesia
 * @author Dyan Galih <galih@gamatechno.com>
 * @version 0.1
 * @startDate 2010-12-08
 * @lastUpdate 2010-12-08
 * @description Do Add Penambahan Atk
 */
require_once GTFWConfiguration::GetValue('application', 'docroot') . 'module/pengolahan_atk/response/PenambahanAtk.proc.class.php';

class DoUpdatePenambahanAtk extends JsonResponse
{
	function TemplateModule()
   {
      
   }

   function ProcessRequest()
   {
      $objAddAtk = new PenambahanAtkProc();
      $GET = $_GET->AsArray();
      $urlReturn = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'pengolahanAtk', 'view', 'html').'&srch='.$GET['srch'];

      if($_POST['btnbalik']!="")
         return array(
         'exec' => 'GtfwAjax.replaceContentWithUrl("subcontent-element","' . $urlReturn . '&ascomponent=1")'
      );
      $result = $objAddAtk->UpdateAtk();

      if ($result) $addParam = '&err=' . Dispatcher::Instance()->Encrypt('update');
      else $addParam = '&err=' . Dispatcher::Instance()->Encrypt('noupdate');//return array( 'exec' => 'alert("update data gagal")');

      $urlReturn .= $addParam;

      return array(
         'exec' => 'GtfwAjax.replaceContentWithUrl("subcontent-element","' . $urlReturn . '&ascomponent=1")'
      );
   }
	
	function ParseTemplate($data = NULL)
   {
      
   }
}
?>