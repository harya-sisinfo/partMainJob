<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model usulan penghapusan aset
 * @created : August 26, 2021 12:18 AM
 * @author  : Alfian Hidayat <hidayat_alfian@ugm.ac.id>
 * @company : DSSDI UGM
**/

class Model_usulan_penghapusan_aset extends CI_Model {
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
            uslHpsId AS id,
            uslHpsTglBuku AS tanggal,
            statusAsetNama AS jenis,
            uslHpsNoRef AS nomor,
            uslHpsKet AS keterangan,
            COUNT(uslHpsDetId) AS jml_aset,
            (SELECT uslHpsTglBuku > IFNULL(MAX(ttpBukuTgl),-1) FROM tutup_buku_ref) AS cekDate,
            (SELECT COUNT(hapusId) FROM penghapusan_mst WHERE hapusUsulId = uslHpsId) AS execHps
		", false);
	}

	public function get_join(){
        $this->aset->join("usul_hapus_det", "uslHpsDetMstId = uslHpsId", "left");
        $this->aset->join("inventarisasi_detail", "invDetId = uslHpsDetInvDetId", "left");
        $this->aset->join("unit_kerja_ref", "unitkerjaId = invDetUnitKerja", "left");
		$this->aset->join("aset_ref_aset_status", "statusAsetId = uslHpsJns", "left");		
	}

	public function get_where($search='', $jenis='', $unit_kerja=''){
		if (!empty($search)) {
			$this->aset->where("(uslHpsNoRef LIKE '%$search%')");
		}

		if (!empty($jenis)) {
			$this->aset->where("uslHpsJns", $jenis);
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
        $this->aset->group_by('uslHpsId');
		$this->aset->order_by('uslHpsTglBuku', 'DESC');
		$this->aset->limit($limit, $offset);
		return $this->aset->get('usul_hapus_mst');
	}

	public function get_num_data($search='', $jenis='', $unit_kerja=''){
		$this->aset->select('COUNT(*) AS jumlah');
		$this->get_where($search, $jenis, $unit_kerja);
		return $this->aset->get('usul_hapus_mst');
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