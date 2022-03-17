<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model penghapusan aset
 * @created : August 11, 2021 11:50:00
 * @author  : Alfian Hidayat <hidayat_alfian@ugm.ac.id>
 * @company : DSSDI UGM
**/

class Model_penghapusan_aset extends CI_Model {
	public $data = '';
    
	public function __construct() {
		parent::__construct();
		$this->aset     	= $this->load->database("aset", TRUE);
		$this->user_id      = $this->session->userdata('__user_id');
		$this->kode_unit    = $this->session->userdata('__objUser')->unit_kerja_kode;
		$this->user_level   = $this->session->userdata('__objUser')->user_level;
	}

	public function get_select(){
		$this->aset->select("
            hapusId AS id,
            hapusTglBuku AS tanggal,
            statusAsetNama AS jenis,
            hapusNoRef AS nomor,
            hapusKeterangan AS keterangan,
            (SELECT COUNT(hapusDetId) FROM fin_aset.penghapusan_det WHERE hapusDetMstId=hapusId) AS jml_aset,
            hapusUsulId	
		", false);
	}

	public function get_join(){
		$this->aset->join("aset_ref_aset_status", "hapusJenis = statusAsetId");
		//$this->aset->join("unit_kerja_ref", "invDetUnitKerja = unitkerjaId","left");
	}

	public function get_where($search='', $jenis='', $unit_kerja=''){
		if (!empty($search)) {
			$this->aset->where("(hapusNoRef LIKE '%$search%')");
		}

		if (!empty($jenis)) {
			$this->aset->where("hapusJenis", $jenis);
		}

		if (!empty($unit_kerja)) {
			$this->aset->where("invDetUnitKerja", $unit_kerja);
		}

		//$this->aset->where("mstPenystnBarangId IS NOT NULL ");
	}

	public function get_data($limit, $offset, $search='', $jenis='', $unit_kerja=''){
		$this->get_select();
		$this->get_join();
		$this->get_where($search, $jenis, $unit_kerja);
		$this->aset->order_by('hapusTglBuku', 'DESC');
		$this->aset->limit($limit, $offset);
		return $this->aset->get('penghapusan_mst');
	}

	public function get_num_data($search='', $jenis='', $unit_kerja=''){
		$this->aset->select('COUNT(*) AS jumlah');
		$this->get_join();
		$this->get_where($search, $jenis, $unit_kerja);
		return $this->aset->get('penghapusan_mst');
	}

	public function get_unit_kerja(){
		$this->aset->select("
            unitkerjaId AS id,
            unitkerjaKode AS kode,
            unitkerjaNama AS nama
		");
        
		if ($this->user_level==2) {
			$this->aset->where('LEFT(unitkerjaKode, 2)', substr($this->kode_unit, 0, 2));
            
		} elseif($this->user_level==3) {
			$this->aset->where('unitkerjaKode', $this->kode_unit);
		}
        
		$this->aset->where('unitkerjaActive','Y');
		return $this->aset->get('unit_kerja_ref');
	}
}