<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model pengembangan aset langsung
 * @created : August 19, 2021 11:49 AM
 * @author  : Alfian Hidayat <hidayat_alfian@ugm.ac.id>
 * @company : DSSDI UGM
**/

class Model_pengembangan_aset_langsung extends CI_Model {
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
            SQL_CALC_FOUND_ROWS
            ikm.invKapId AS id,
            ikm.invKapTglBuku AS tanggal,
            ikm.invKapKeterangan AS keterangan,
            iks.invSumNoRef AS nomor,
            COUNT(ikd.invKapDetInvId) AS jml_aset,
            ikm.invKapTotal AS total,
            (SELECT ikm.invKapTglBuku > IFNULL(MAX(ttpBukuTgl),-1) FROM tutup_buku_ref) AS cekDate
		", false);
	}

	public function get_join(){
		$this->aset->join("inv_kap_sumber iks", "iks.invSumMstId = ikm.invKapId", "left");
        $this->aset->join("inv_kap_det ikd", "ikd.invKapDetMstId = ikm.invKapId", "left");
        $this->aset->join("inventarisasi_detail id", "id.invDetId = ikd.invKapDetInvId", "left");
		$this->aset->join("unit_kerja_ref ukr", "ukr.unitkerjaId = id.invDetUnitKerja", "left");
	}

	public function get_where($search='', $tahun='', $unit_kerja=''){
		if (!empty($search)) {
			$this->aset->where("(iks.invSumNoRef LIKE '%$search%' OR id.invDetKodeBarang LIKE '%$search%' OR id.invDetMstLabel LIKE '%$search%')");
		}

		if (!empty($tahun)) {
			$this->aset->where("YEAR(ikm.invKapTglBuku)", $tahun);
		}

		if (!empty($unit_kerja)) {
			$this->aset->where("(ukr.unitkerjaKodeSistem = '%$unit_kerja%' OR ukr.unitkerjaKodeSistem LIKE '%$unit_kerja%')");
		}

		//$this->aset->where("mstPenystnBarangId IS NOT NULL ");
	}

	public function get_data($limit, $offset, $search='', $tahun='', $unit_kerja=''){
		$this->get_select();
		$this->get_join();
		$this->get_where($search, $tahun, $unit_kerja);
        $this->aset->group_by('ikm.invKapId');
		$this->aset->order_by('ikm.invKapTglBuku', 'DESC');
		$this->aset->limit($limit, $offset);
		return $this->aset->get('inv_kap_mst ikm');
	}

	public function get_num_data($search='', $tahun='', $unit_kerja=''){
		$this->aset->select('COUNT(*) AS jumlah');
		$this->get_join();
		$this->get_where($search, $tahun, $unit_kerja);
		return $this->aset->get('inv_kap_mst ikm');
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
    
   	public function select_detail_pal($id){
		$this->aset->select("
            m.invKapId AS id,
            m.invKapTglBuku AS tglBuku,
            m.invKapKeterangan AS keterangan,
            s.invSumPembLgsngId AS asal,
            s.invSumNoRef AS noref,
            m.invKapTotal AS total
		");
        
        $this->aset->from('inv_kap_mst m');
        $this->aset->join('inv_kap_sumber s', 's.invSumMstId=m.invKapId', 'left');
        $this->aset->where('m.invKapId', $id);

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->row();
	}
    
   	public function select_rincian_aset($id){
		$this->aset->select("
            kd.invKapDetMstId AS id,
            d.invDetKodeBarang AS kode,
            d.invDetMstLabel AS nama,
            d.invDetMerek AS merek,
            u.unitkerjaNama AS unit,
            kd.invKapDetUmur AS umur,
            kd.invKapDetRph AS nilai
		");
        
        $this->aset->from('inv_kap_det kd');
        $this->aset->join('inventarisasi_detail d', 'd.invDetId=kd.invKapDetInvId', 'left');
        $this->aset->join('unit_kerja_ref u', 'u.unitkerjaId=d.invDetUnitKerja', 'left');
        $this->aset->where('invKapDetMstId', $id);

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->result();
	}
 }