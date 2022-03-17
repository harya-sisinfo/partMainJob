<?php
/**
 * @package ProcBhpPindahGudang
 * @copyright Copyright (c) PT Gamatechno Indonesia
 * @author Didi Zuliansyah <didi@gamatechno.com>
 * @version 0.1
 * @startDate 2013-01-01
 * @lastUpdate 2013-01-01
 * @description Class ProcBhpPindahGudang
 */

require_once Configuration::Instance()->GetValue( 'application', 'docroot') . 'module/'.Dispatcher::Instance()->mModule.'/business/BhpPindahGudang.class.php';

class ProcBhpPindahGudang {
	var $Obj;
	var $_POST;
	var $msg = array();
	var $pageView;
	var $pageInput;

	var $cssDone = "notebox-done";
	var $cssFail = "notebox-warning";
	var $cssAlert = "notebox-alert";

	function __construct() {
		$this->Obj = new BhpPindahGudang();
		$this->_POST = $_POST->AsArray();
		$this->pageView = Dispatcher::Instance()->GetUrl(Dispatcher::Instance()->mModule, 'BhpPindahGudang', 'view', 'html');
	}
	
	function checkEmptyField(){
		// mandatory field check
		$empty = 0;
		$fieldArr = array('notrans','unitasalid','unittujuanid','gudangasal','gudangtujuan','tglpindah','pic');
		foreach ($fieldArr as $field) if(empty($this->_POST[$field])) $empty++;;
		if(count($this->_POST['barangList']) == 0 ) $empty++;

		if($empty) $this->msg[] = 'Field bertanda * wajib diisi';
		else{
			// check tgl buku
			$cekDate = $this->Obj->checkDate($this->_POST['tglpindah']);
			if($cekDate['accept'] == 0) $this->msg[] = 'Tanggal Mutasi Harus Lebih Besar Dari '.$cekDate['tgl'];

			// check over quota
			$detil = $this->_POST['barangList']; $over = 0;
			foreach($detil as $value) {
				if($value['jumlah'] > $value['stok']){
					$over++;	break;
				}
			}
			if($over) $this->msg[] = 'Jumlah Yg Dimutasikan Melebihi Stok Yang Tersedia';
		}
	}
	
	function returnValue($status,$urlRedirect,$msg,$css){
		$return['status'] = $status;
		$return['msg'] = $msg;
		$return['css'] = $css;
		$return['url'] = $urlRedirect;
		return $return;
	}
	
	function AddBhpPindahGudang(){
		if(isset($this->_POST['simpan'])) {
			$this->checkEmptyField();
			if(count($this->msg) > 0){
				return $this->returnValue(false, '', implode('\n', $this->msg), $this->cssAlert);
			}else{
				$add = $this->Obj->InsertBhpPindahGudang($this->_POST);
				if(!empty($this->Obj->msg)) return $this->returnValue(false, '', $this->Obj->msg, $this->cssAlert);
				else{
					if($add == true){
						$message = 'Penambahan Data Berhasil';
						$css = $this->cssDone;
					}else{
						$message = 'Penambahan Data Gagal';
						$css = $this->cssFail;
					}
					Messenger::Instance()->Send(Dispatcher::Instance()->mModule, 'BhpPindahGudang', 'view', 'html', array($this->_POST, $message, $css),Messenger::NextRequest);
					return $this->returnValue(true, $this->pageView, $msg, $css);
				}
			}
		}
		return $this->returnValue(true, $this->pageView, '', '');
	}
}
?>