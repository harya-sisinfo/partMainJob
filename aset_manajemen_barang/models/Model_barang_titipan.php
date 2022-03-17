<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model barang titipan
 * @created : August 19, 2021 11:04 AM
 * @updated : December 24, 2021 02:10 PM
 * @author  : Alfian Hidayat <hidayat_alfian@ugm.ac.id>
 * @company : DSSDI UGM
**/

class Model_barang_titipan extends CI_Model {
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
            a.invDetId AS id,
            a.invDetGolId as golongan,
            a.invDetKodeBarang AS KIB,
            a.invDetBarangId AS id_barang,
            barangKode AS kode_barang,
            TRIM(a.invDetMstLabel) AS nama_aset,
            IF(
            a.invDetLokasi IS NOT NULL,
            a.invDetLokasi,
            CONCAT(kampusNama,IF(gedungNama IS NULL,'',CONCAT('@@',gedungNama,IF(ruangNama IS NULL,'',CONCAT('@@',ruangNama)))))
            ) AS lokasi_aset,
            unitkerjaNama AS unit_pj,
            unitkerjaKodeSistem,
            LEFT(a.invDetTglPembelian, 4) AS thn_pengadaan,
            a.invDetTglPembelian AS tgl_perolehan,
            a.invDetTglBuku AS tgl_buku,
            a.invDetKodeBarang AS kodeBarang,
            a.invDetMerek AS merek,
            a.invDetSpesifikasi AS spec,
            IF(invDetGolId IN (2,5),invLuasTanah,0) AS luas_tnh,
            IF(invDetGolId IN (2,5),invDetNilaiPerolehanSatuan*invLuasTanah,invDetNilaiPerolehanSatuan) AS nilai_perolehan,
            kepemilikanbrgNama AS status_kepemilikan,
            IF(statusAsetNama != 'Aktif',CONCAT(kondisibrgNama,' (',statusAsetNama,')'),kondisibrgNama) AS kondisi,
            invDetGolId AS golBarang,
            invSPMNumber AS no_ref,
            a.invDetNamaFile AS foto,
            a.invDetUmurEkonomis AS umureko
		", false);
	}

	public function get_join(){ 
	    $this->aset->join("inv_titipan_mst", "a.invDetMstId = invMstId", "left");
        $this->aset->join("barang_ref", "a.invDetBarangId = barangId", "left");
        $this->aset->join("kampus_ref", "a.invDetKampus = kampusId" , "left");
        $this->aset->join("unit_kerja_ref", "a.invDetUnitKerja = unitkerjaId", "left");
        $this->aset->join("kepemilikan_barang_ref", "kepemilikanbrgId = a.invDetKepemilikan" , "left");
		$this->aset->join("kondisi_barang_ref", "kondisibrgId = a.invDetKondisiId" , "left");
        $this->aset->join("gedung", "a.invDetGedungId = gedungId" , "left");
        $this->aset->join("ruang", "a.invDetRuanganId = ruangId" , "left");
        $this->aset->join("aset_ref_aset_status", "statusAsetId = a.invDetAsetStatusId" , "left"); 
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

//		if (!empty($unit_kerja)) {
//          $refUnitKerja = $this->model->get_unit_by_kode($unit_kerja);
//			$this->aset->where("invDetUnitKerja", $refUnitKerja->id);
//		}

		//$this->aset->where("mstPenystnBarangId IS NOT NULL ");
	}

	public function get_data($limit, $offset, $search='', $status='', $unit_kerja=''){
		$this->get_select();
		$this->get_join();
		$this->get_where($search, $status, $unit_kerja);
		//$this->aset->order_by('unitkerjaKode', 'ASC');
		$this->aset->limit($limit, $offset);
		return $this->aset->get('inv_titipan_det a');
	}

	public function get_num_datax($search='', $status='', $unit_kerja=''){
		$this->aset->select('COUNT(*) AS jumlah');
		//$this->get_join();
		$this->get_where($search, $status, $unit_kerja);
		return $this->aset->get('inv_titipan_det a');
	}
    
   	public function get_num_data($search='', $status='', $unit_kerja='') {
   	    $refUnitKerja = $this->model->get_unit_by_kode($unit_kerja);

		$this->aset->select("
            COUNT(*) AS jumlah
		", false);
        
        $this->aset->from('inv_titipan_det');
        $this->aset->where('invDetUnitKerja', $refUnitKerja->id);

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->row();
	}
    
   	public function get_data_unit_kerja() {
		$this->aset->select("
            unitkerjaId AS id,
            unitkerjaKode AS kode,
            unitkerjaNama AS nama
		", false);
        
        $this->aset->from('unit_kerja_ref');
        
		if ($this->user_level==2) {
			//$this->aset->where('LEFT(unitkerjaKode, 2) = ', substr($this->kode_unit, 0, 2));
            $this->aset->where('unitkerjaKode', $this->kode_unit);
            
		} elseif($this->user_level==3) {
			$this->aset->where('unitkerjaKode', $this->kode_unit);
		}
        
		$this->aset->where('unitkerjaActive', 'Y');
        
        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->result();
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
    
   	public function select_max_invmstid(){
		$this->aset->select("
            invMstId AS id
		", false);
        
        $this->aset->from('inv_titipan_mst');
        $this->aset->order_by('invMstId', 'DESC');
        $this->aset->limit(1);

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->row();
	}
    
   	public function select_ugmfw_mapping($username){
		$this->aset->select("
            UserId AS id
		", false);
        
        $this->aset->from('ugmfw_mapping');
        $this->aset->where('user_username', $username);

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->row();
	}
    
   	public function select_unit_kerja_id($kode){
		$this->aset->select("
            unitkerjaId AS id
		", false);
        
        $this->aset->from('unit_kerja_ref');
        $this->aset->where('unitkerjaKode', $kode);

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->row();
	}
    
   	public function select_nomor_barang_titipan($kode, $unit){
		$this->aset->select("
            nup
		", false);
        
        $this->aset->from('inv_titipan_last_nup');
        $this->aset->where('brgId', $kode);
        $this->aset->where('unitId', $unit);

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->row();
	}
    
   	public function select_detail_barang_titipan($id){
		$this->aset->select("
            invDetGolId AS golbrgId,
            golbrgNama,
            id.invDetId,
            IF(
            v.id IS NULL AND vp.id IS NULL,
            CONCAT(invSPMNumber,' (Manual)'),
            IFNULL(v.nomor_spp,vp.nomor_spp)
            ) AS noref,
            invDetBarangId,
            invDetKodeBarang,
            barangNama AS namaAset,
            invDetMstLabel,
            invDetMerek,
            invDetSpesifikasi,
            invDetTglPembelian,
            invDetTglBuku,
            invDetSumberDana,
            sumberdanaNama,
            invDetSatuanBarang,
            satuanbrgNama,
            invDetUmurEkonomis,
            invLuasTanah,
            invNilaiFaktur,
            invDetNilaiPerolehanSatuan,
            invDetLokasi,
            invDetKegunaan,
            invDetKepemilikan,
            kepemilikanbrgNama,
            invDetPenguasaanBarang,
            statuspengbrgNama,
            kondisibrgNama AS kondisi,
            invDetKampus,
            kampusNama,
            CONCAT(gedungNama,IF(ruangNama IS NULL,'',CONCAT(' &raquo; ',ruangNama))) AS lokasiAset,
            invDetGedungId,
            invDetRuanganId,
            gedungId,
            CONCAT(gedungNama,' (',gedungKode,')') AS gedungNama,
            invDetIdentitasBarang,
            statusAsetNama AS statusAset,
            invDetKeteranganLain,
            unitkerjaNama,
            invDetNamaFile,
            IF(mstPenystnNilaiPerolehan IS NULL OR mstPenystnNilaiPerolehan = mstPenystnDisusutkan, 0, 1) AS is_penyusutan
		", false);
        
        $this->aset->from('inv_titipan_mst');
        $this->aset->join('inv_titipan_det id', 'invDetMstId = invMstId');
        $this->aset->join('golongan_barang_ref', 'golbrgId = invDetGolId');
        $this->aset->join('sumber_dana_ref', 'sumberdanaId = invDetSumberDana');
        $this->aset->join('satuan_barang_ref', 'invDetSatuanBarang = satuanbrgId', 'left');
        $this->aset->join('kepemilikan_barang_ref', 'kepemilikanbrgId = invDetKepemilikan');
        $this->aset->join('status_penguasaan_barang_ref', 'statuspengbrgId = invDetPenguasaanBarang');
        $this->aset->join('barang_ref', 'invDetBarangId = barangId');
        $this->aset->join('kampus_ref', 'invDetKampus = kampusId', 'left');
        $this->aset->join('gedung', 'invDetGedungId = gedungId', 'left');
        $this->aset->join('ruang', 'invDetRuanganId = ruangId', 'left');
        $this->aset->join('kondisi_barang_ref', 'kondisibrgId = invDetKondisiId', 'left');
        $this->aset->join('aset_ref_aset_status', 'statusAsetId = invDetAsetStatusId', 'left');
        $this->aset->join('unit_kerja_ref', 'invDetUnitKerja = unitkerjaId');
        $this->aset->join('penyusutan_brg_mst', 'id.invDetId = mstPenystnBarangId', 'left');
        $this->aset->join('inv_map_simpel ims', 'ims.invDetId = id.invDetId', 'left');
        $this->aset->join('v_pembelian_verified v', 'v.id = ims.pembLgsngId', 'left');
        $this->aset->join('inv_map_emonev ime', 'ime.invDetId = id.invDetId', 'left');
        $this->aset->join('v_perintah_bayar_pengadaan vp', 'vp.id = ime.pembPengdnId', 'left');
        $this->aset->where('id.invDetId', $id);

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->row();
	}
    
    // INSERT DATA BARANG TITIPAN (MASTER) ------------------------------------------------------------------------------- //
    public function insert_inv_titipan_mst($data) {
        $query = $this->aset->insert('inv_titipan_mst', $data);
        //echo $this->aset->last_query(); exit;
        return $query;
    }
    
    // INSERT DATA BARANG TITIPAN (DETAIL) ------------------------------------------------------------------------------ //
    public function insert_inv_titipan_det($data) {
        $query = $this->aset->insert('inv_titipan_det', $data);
        //echo $this->aset->last_query(); exit;
        return $query;
    }
}