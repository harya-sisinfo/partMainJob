<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model mutasi hibah
 * @created : August 26, 2021 02:12 PM
 * @author  : Alfian Hidayat <hidayat_alfian@ugm.ac.id>
 * @company : DSSDI UGM
**/

class Model_mutasi_hibah extends CI_Model {
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
            a.invDetKodeBarang AS kode_barang,
            a.invDetMstLabel AS label_aset,
            a.invDetMerek AS merk,
            a.invDetSpesifikasi AS spesifikasi,
            gedungNama AS gedung,
            ruangNama AS ruang,
            unitkerjaNama AS unit_pj,
            YEAR(invDetTglPembelian) AS thn_pembelian,
            a.invDetNilaiPerolehanSatuan AS nilai_perolehan,
            a.invDetTglBuku AS tgl_buku,
            kondisibrgNama AS kondisi,
            kepemilikanbrgNama AS kepemilikan,
            a.invDetBarcode AS no_barcode,
            a.invDetSertifikat AS no_surat,
            IF(v.id IS NULL AND vp.id IS NULL,invSPMNumber,'') AS no_ref
		", false);
	}

	public function get_join() { 
        $this->aset->join("inventarisasi_mst", "a.invDetMstId = invMstId", "left");
        $this->aset->join("unit_kerja_ref", "a.invDetUnitKerja=unitkerjaId", "left");
		$this->aset->join("kondisi_barang_ref", "kondisibrgId=a.invDetKondisiId" , "left");
        $this->aset->join("kepemilikan_barang_ref", "kepemilikanbrgId=a.invDetKepemilikan" , "left");
        
        $this->aset->join("gedung", "gedungId=a.invDetGedungId" , "left");
        $this->aset->join("ruang", "ruangId=a.invDetRuanganId" , "left");
        
        $this->aset->join("inv_map_simpel m", "m.invDetId = a.invDetId" , "left");
        $this->aset->join("v_pembelian_verified v", "v.id = m.pembLgsngId" , "left");
        $this->aset->join("inv_map_emonev em", "em.invDetId = a.invDetId" , "left");
        $this->aset->join("v_perintah_bayar_pengadaan vp", "vp.id = em.pembPengdnId" , "left");
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
		$this->aset->order_by('unitkerjaKode', 'ASC');
		$this->aset->limit($limit, $offset);
		return $this->aset->get('inventarisasi_detail a');
	}

	public function get_num_data($search='', $status='', $unit_kerja=''){
		$this->aset->select('COUNT(*) AS jumlah');
		$this->get_join();
		$this->get_where($search, $status, $unit_kerja);
		return $this->aset->get('inventarisasi_detail a');
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