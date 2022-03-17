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
require_once GTFWConfiguration::GetValue('application', 'docroot') . 'module/pengolahan_atk/business/PenambahanAtk.class.php';

class DoAddPenambahanAtk extends JsonResponse
{
   function ProcessRequest()
   {
      $POST = $_POST->AsArray();
      $GET = $_GET->AsArray();
      $objAddAtk = new PenambahanAtkProc();
      $obj = new PenambahanAtk();
      $urlReturn = Dispatcher::Instance()->GetUrl('pengolahan_atk', 'pengolahanAtk', 'view', 'html').'&srch='.$GET['srch'];
      
      if($_POST['btnbalik']!="")
         return array(
         'exec' => 'GtfwAjax.replaceContentWithUrl("subcontent-element","' . $urlReturn . '&ascomponent=1")'
      );

      $mustFill = array('barcodekeu','tgl_beli','barang_id','gudang','jumlah_barang','invAtkBiayaNominal');
      foreach ($POST as $key => $value) {
         if(in_array($key,$mustFill) && empty($value))
            return array( 'exec' => 'alert("Field Bertanda * Wajib Diisi")');
      }
      $cekDate = $obj->checkDate($POST['tgl_beli']);
      if($cekDate['accept'] == 0)
         return array( 'exec' => 'alert("Tanggal Pembelian Harus Lebih Besar Dari '.$cekDate['tgl'].'")');

      $result = $objAddAtk->addAtk();

      if ($result) $addParam = '&err=' . Dispatcher::Instance()->Encrypt('add');
      else return array( 'exec' => 'alert("penambahan data gagal")');


      $urlReturn .= $addParam;

      return array(
         'exec' => 'GtfwAjax.replaceContentWithUrl("subcontent-element","' . $urlReturn . '&ascomponent=1")'
      );
   }
}
?>