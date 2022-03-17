<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model reclass aset barang titipan
 * @created : November 12, 2021 10:21 AM
 * @updated : November 12, 2021 10:21 AM
 * @author  : Alfian Hidayat <hidayat_alfian@ugm.ac.id>
 * @company : DSSDI UGM
**/

class Model_reclass_aset_barang_titipan extends CI_Model {
	public $data = '';
    
	public function __construct() {
		parent::__construct();
		$this->aset     	= $this->load->database("aset", TRUE);
		$this->user_id      = $this->session->userdata('__user_id');
		$this->kode_unit    = $this->session->userdata('__objUser')->unit_kerja_kode;
		$this->user_level   = $this->session->userdata('__objUser')->user_level;
	}
        
   	public function select_data_reclass_aset_barang_titipan(){
		$this->aset->select("
            m.invTtpMstId,
            m.invTtpMstNoRef,
            m.invTtpMstTglBuku,
            m.invTtpMstFile,
            m.invTtpMstKet,
            m.invTtpMstStatus,
            m.invTtpUserId,
            m.invTtpMstTimestamp
		");
        
        $this->aset->from('inv_titip_map_mst m');
        $this->aset->join('inv_titip_map_det d', 'd.invTtpDetMstId=m.invTtpMstId', 'left');

        $query = $this->aset->get();
        echo $this->aset->last_query(); exit;
        return $query->row();
	}

	public function get_select(){
		$this->aset->select("
            m.invTtpMstId,
            m.invTtpMstNoRef AS no_ref,
            m.invTtpMstTglBuku AS tgl_buku,
            m.invTtpMstFile AS nama_file,
            m.invTtpMstKet AS keterangan,
            m.invTtpMstStatus AS status,
            m.invTtpUserId,
            m.invTtpMstTimestamp
		", false);
	}

	public function get_join(){ 
        $this->aset->join('inv_titip_map_det d', 'd.invTtpDetMstId=m.invTtpMstId', 'left');
        //$this->aset->join("barang_ref", "a.invDetBarangId = barangId", "left");
        //$this->aset->join("kampus_ref", "a.invDetKampus = kampusId" , "left");
        //$this->aset->join("unit_kerja_ref", "a.invDetUnitKerja = unitkerjaId", "left");
        //$this->aset->join("kepemilikan_barang_ref", "kepemilikanbrgId = a.invDetKepemilikan" , "left");
		//$this->aset->join("kondisi_barang_ref", "kondisibrgId = a.invDetKondisiId" , "left");
        //$this->aset->join("gedung", "a.invDetGedungId = gedungId" , "left");
        //$this->aset->join("ruang", "a.invDetRuanganId = ruangId" , "left");
        //$this->aset->join("aset_ref_aset_status", "statusAsetId = a.invDetAsetStatusId" , "left"); 
	}

	public function get_where($search='', $status='', $unit_kerja=''){
		if (!empty($search)) {
			$this->aset->where("(invDetKodeBarang LIKE '%$search%' OR invDetMstLabel LIKE '%$search%')");
		}

		if (!empty($status)) {
			//$this->aset->where("hapusJenis", $status);
		}

		if (!empty($unit_kerja)) {
			$this->aset->where("unitkerjaKode", $unit_kerja);
		}

		//$this->aset->where("mstPenystnBarangId IS NOT NULL ");
	}

	public function get_data($limit, $offset, $search='', $status='', $unit_kerja=''){
		$this->get_select();
		$this->get_join();
		$this->get_where($search, $status, $unit_kerja);
		//$this->aset->order_by('unitkerjaKode', 'ASC');
		$this->aset->limit($limit, $offset);
		return $this->aset->get('inv_titip_map_mst m');
	}

	public function get_num_data($search='', $status='', $unit_kerja=''){
		$this->aset->select('COUNT(*) AS jumlah');
		//$this->get_join();
		$this->get_where($search, $status, $unit_kerja);
		return $this->aset->get('inv_titip_map_mst m');
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
    
   	public function get_unit_by_kode($kode){
		$this->aset->select("
            unitkerjaId AS id
		", false);
        
        $this->aset->from('unit_kerja_ref');
        $this->aset->where('unitkerjaKode', $kode);

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->row();
	}
    
   	public function get_golongan_barang(){
		$this->aset->select("
            golbrgId AS id,
            golbrgNama AS nama
		", false);
        
        $this->aset->from('golongan_barang_ref');

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->result();
	}
    
   	public function get_kode_barang($gol){
		$this->aset->select("
            kelbrgId AS idKelompok,
            subkelbrgId AS idSubKelompok,
            barangId AS id,
            CONCAT(
                LPAD(`golbrgKode`, 1, 0),
                '.',
                LPAD(`bidangbrgKode`, 2, 0),
                '.',
                LPAD(`kelbrgKode`, 2, 0),
                '.',
                LPAD(`subkelbrgKode`, 2, 0),
                '.',
                LPAD(`barangKode`, 3, 0)
            ) AS kode,
            barangNama AS nama,
            barangSatuanbrgId AS satuan,
            barangUmurEkonomis AS umurEkonomis,
            barangNilaiResidu AS nilaiResidu,
            barangHps AS hps 
		", false);
        
        $this->aset->from('golongan_barang_ref');
        $this->aset->join('bidang_barang_ref', 'bidangbrgGolbrgId=golbrgId');
        $this->aset->join('kelompok_barang_ref', 'kelbrgBidangbrgId=bidangbrgId');
        $this->aset->join('sub_kelompok_barang_ref', 'subkelbrgKelbrgId=kelbrgId');
        $this->aset->join('barang_ref', 'barangSubkelbrgId=subkelbrgId');
        
        $this->aset->where('golbrgId', $gol);
        $this->aset->where('barangAktif', 'Y');

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->result();
	}
    
   	public function get_sumber_dana_barang_titipan(){
		$this->aset->select("
            sumberdanaId AS id,
            sumberdanaNama AS nama
		", false);
        
        $this->aset->from('sumber_dana_ref');
        
        $this->aset->order_by('sumberdanaNama');

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->result();
	}
    
   	public function get_satuan_barang(){
		$this->aset->select("
            satuanbrgId AS id,
            satuanbrgNama AS nama
		", false);
        
        $this->aset->from('satuan_barang_ref');

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->result();
	}
    
   	public function get_kepemilikan(){
		$this->aset->select("
            kepemilikanbrgId AS id,
            kepemilikanbrgNama AS nama
		", false);
        
        $this->aset->from('kepemilikan_barang_ref');
        $this->aset->order_by('kepemilikanbrgId');

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->result();
	}
    
   	public function get_status_barang(){
		$this->aset->select("
            statusAsetId AS id,
            statusAsetNama AS nama
		", false);
        
        $this->aset->from('aset_ref_aset_status');

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->result();
	}
    
   	public function get_unit_pj(){
		$this->aset->select("
            unitkerjaId AS id,
            unitkerjaNama AS nama,
            unitkerjaKode AS kode
		", false);
        
        $this->aset->from('unit_kerja_ref');
        $this->aset->order_by('unitkerjaKode');

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->result();
	}
    
   	public function get_kampus(){
		$this->aset->select("
            kampusId AS id,
            kampusKode AS kode,
            kampusNama AS nama
		", false);
        
        $this->aset->from('kampus_ref');

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->result();
	}
}