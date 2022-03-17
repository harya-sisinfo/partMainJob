<?php
/**
* @package PenambahanAtk
* @copyright Copyright (c) PT Gamatechno Indonesia
* @author Dyan Galih <galih@gamatechno.com>
* @version 0.1
* @startDate 2010-12-08
* @lastUpdate 2010-12-08
* @description Penambahan Atk
*/

require_once GTFWConfiguration::GetValue( 'application', 'docroot') . 'module/pengolahan_atk/business/PenambahanAtk.class.php';

class PenambahanAtkProc {
   var $cssDone = "notebox-done";
   var $cssFail = "notebox-warning";

   function __construct() {

   }

   public function addAtk()
   {
      $objAddAtk = new PenambahanAtk();

      $result = $objAddAtk->AddAtk($_POST->AsArray());

      return $result;
   }
	
	public function UpdateAtk()
   {
      $objAddAtk = new PenambahanAtk();

      $result = $objAddAtk->UpdateAtk($_POST->AsArray());

      return $result;
   }

}
?>