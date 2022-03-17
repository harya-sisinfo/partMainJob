<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model unapprove mutas antar unit
 * @created : 2021-07-29 10:05:00
 * @author  : sriharyo <srihary@ugm.ac.id>
 * @company : DSDI UGM
*/
class Model_unapprove_mutas_antar_unit extends CI_Model 
{
	public $data = '';
	public function __construct() 
	{
		parent::__construct();
		$this->aset     	= $this->load->database("aset",TRUE);
		$this->user_id      = $this->session->userdata('__user_id');
		$this->kode_unit    = $this->session->userdata('__objUser')->unit_kerja_kode;
		$this->user_level   = $this->session->userdata('__objUser')->user_level;
	}

	public function get_select(){
		$this->aset->select("
			SQL_CALC_FOUND_ROWS
			mtsAsetMstId AS id,
			mtsAsetMstTglMutasi AS tgl,
			mtsAsetMstBAMutasi AS berita_acara,
			mtsAsetMstPIC AS pic,
			mtsAsetMstApproved AS isApproved,
			(SELECT COUNT(mtsAsetDetId) FROM mutasi_aset_det WHERE mtsAsetDetMstId = mtsAsetMstId) AS jmlBrg,
			c.unitkerjaNama AS unitAsal,
			d.unitkerjaNama AS unitTujuan,
			mstAsetMstApprovedPenerima
			", false);
	}

	public function get_join(){
		$this->aset->join("unit_kerja_ref c", "c.unitkerjaId = a.mtsAsetMstUnitIdAsal","left");
		$this->aset->join("unit_kerja_ref d", "d.unitkerjaId = a.mtsAsetMstUnitIdTuj","left");
	}

	public function get_where($params,$search){
		if(!empty($params['tglAwal'])){
			$this->aset->where("(a.mtsAsetMstTglMutasi BETWEEN '".$params[
				'tglAwal']."' AND '".$params[
					'tglAkhir']."' ) ");
		}

		if(!empty($params['ba_mutasi'])){
			$this->aset->like("mtsAsetMstBAMutasi",$params['ba_mutasi']);
		}

		if(!empty($params['unit_kerja'])&&$params['unit_kerja']!='1'){
			$this->aset->where("d.unitkerjaId", $params['unit_kerja']);
		}
		
		if(!empty($search)){

			$this->aset->where("
				(
				mtsAsetMstId LIKE '%".$search."%' OR 
				mtsAsetMstTglMutasi LIKE '%".$search."%' OR 
				mtsAsetMstBAMutasi LIKE '%".$search."%' OR 
				mtsAsetMstApproved LIKE '%".$search."%' OR 
				d.unitkerjaNama LIKE '%".$search."%' )
				");
		}
		 $this->aset->where('mtsAsetMstApproved IS NOT NULL');
		 $this->aset->where('mtsAsetMstApproved <> "N"');
		 $this->aset->where('(mstAsetMstApprovedPenerima IS NOT NULL OR mstAsetMstApprovedPenerima = "N" OR mstAsetMstApprovedPenerima = "C")');
		
	}

	public function get_data($limit, $offset, $search, $order,  $params){

		$this->get_select();
		$this->get_join();

		$this->get_where($params,$search);
		$this->aset->order_by('a.mtsAsetMstTglMutasi','DESC');
		$this->aset->group_by('mtsAsetMstId');

		$this->aset->limit($limit, $offset);
		return $this->aset->get('mutasi_aset_mst a')->result_array();
	}
	
	public function get_num_data($params,$search){
		$this->aset->select('COUNT(*) AS jumlah');
		$this->get_join();
		$this->get_where($params,$search);
		return $this->aset->get('mutasi_aset_mst a')->row_array();
	}


	public function get_unit_kerja(){
		$this->aset->select('unitkerjaId AS id,
      			unitkerjaKode AS kode,
      			unitkerjaNama AS nama
      			');
		if($this->user_level==2){
			$this->aset->where('LEFT(unitkerjaKode,2)',substr($this->kode_unit, 0, 2));
		}elseif($this->user_level==3){
			$this->aset->where('unitkerjaKode',$this->kode_unit);
		}
		$this->aset->where('unitkerjaActive','Y');
		return $this->aset->get('unit_kerja_ref');
	}

	public function update_data($tabel,$kunci_primer,$id,$data)
	{
		$this->aset->where($kunci_primer, $id);
		return $this->aset->update($tabel, $data);
	}
}