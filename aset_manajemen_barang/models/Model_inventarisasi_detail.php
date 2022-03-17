<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model inventarisasi detail
 * @created : August 12, 2021 02:54 AM
 * @updated : January 20, 2022 13:58 PM
 * @author  : Alfian Hidayat <hidayat_alfian@ugm.ac.id>
 * @company : DSSDI UGM
**/

class Model_inventarisasi_detail extends CI_Model {
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
            a.invDetLokasi AS lokasi,
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
            IF(v.id IS NULL AND vp.id IS NULL,invSPMNumber,'') AS no_ref,
            a.isHentiOps
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

	public function get_where($search='', $kel_coa='', $kondisi='', $unit_kerja='', $sub_unit=''){
		if (!empty($search)) {
			$this->aset->where("(invDetKodeBarang LIKE '%$search%' OR invDetMstLabel LIKE '%$search%')");
		}
        
		if (!empty($kondisi)) {
            $this->aset->where("coaUGM", $kel_coa);
		}

		if (!empty($kondisi)) {
            $this->aset->where("kondisibrgId", $kondisi);
		}

		if (!empty($unit_kerja)) {
			$this->aset->where("unitkerjaKode", $unit_kerja);
		}
        
		if (!empty($sub_unit)) {
			$this->aset->like("unitkerjaKodeSistem", $sub_unit, 'after');
		}
	}

    public function get_data($limit, $offset, $search='', $coa='', $pengadaan='', $pembukuan='', $sumber_dana='', $kepemilikan='', $kondisi='', $status='', $unit_kerja='', $sub_unit=''){
        if ($coa!="") {
            $cari_coa = ' AND coaUGM='.$coa;
        } else {
            $cari_coa = '';
        }
        
        if ($pengadaan!="") {
            $cari_pengadaan = ' AND YEAR(invDetTglPembelian)='.$pengadaan;
        } else {
            $cari_pengadaan = '';
        }
        
        if ($pembukuan!="") {
            $cari_pembukuan = ' AND YEAR(a.invDetTglBuku)='.$pembukuan;
        } else {
            $cari_pembukuan = '';
        }
        
        if ($sumber_dana!="") {
            $cari_dana = ' AND invDetSumberDana='.$sumber_dana;
        } else {
            $cari_dana = '';
        }
        
        if ($kepemilikan!="") {
            $cari_milik = ' AND a.invDetKepemilikan='.$kepemilikan;
        } else {
            $cari_milik = '';
        }
        
        if ($kondisi!="") {
            $cari_kondisi = ' AND kondisibrgId='.$kondisi;
        } else {
            $cari_kondisi = '';
        }
        
        if ($status!="") {
            $cari_status = ' AND a.invDetAsetStatusId='.$status;
        } else {
            $cari_status = '';
        }
        
  		if (!empty($sub_unit)) {
  		    $cari_unit = ' AND unitkerjaKodeSistem LIKE "'.$sub_unit.'%"';
		} else {
            $cari_unit = ' AND unitkerjaKode="'.$unit_kerja.'"';
		}
        
        $sql = "
        SELECT
            a.invDetId AS id,
            a.invDetKodeBarang AS kode_barang,
            a.invDetMstLabel AS label_aset,
            a.invDetMerek AS merk,
            a.invDetSpesifikasi AS spesifikasi,
            a.invDetLokasi AS lokasi,
            gedungNama AS gedung,
            ruangId,
            ruangKode,
            ruangNama AS ruang,
            unitkerjaId,
            unitkerjaKode,
            unitkerjaNama AS unit_pj,
            YEAR(invDetTglPembelian) AS thn_pembelian,
            a.invDetNilaiPerolehanSatuan AS nilai_perolehan,
            a.invDetTglBuku AS tgl_buku,
            kondisibrgNama AS kondisi,
            statusAsetNama AS status_aset,
            kepemilikanbrgNama AS kepemilikan,
            IFNULL(v.nomor_spp,vp.nomor_spp) AS nomor_spp,
            IFNULL(v.no_surat_pembelian,vp.no_spk) AS nomor_surat,
            IF(v.id IS NULL AND vp.id IS NULL,invSPMNumber,'') AS no_ref,
            a.isHentiOps
        FROM
            inventarisasi_detail a
            LEFT JOIN inventarisasi_mst ON a.invDetMstId = invMstId
            LEFT JOIN barang_ref ON a.invDetBarangId = barangId
            LEFT JOIN kampus_ref ON a.invDetKampus = kampusId
            LEFT JOIN unit_kerja_ref  ON a.invDetUnitKerja = unitkerjaId
            LEFT JOIN kepemilikan_barang_ref ON kepemilikanbrgId = a.invDetKepemilikan
            LEFT JOIN kondisi_barang_ref ON kondisibrgId = a.invDetKondisiId
            LEFT JOIN sub_kelompok_barang_ref ON a.invDetSubkelbrgId = subkelbrgId
            LEFT JOIN kelompok_barang_ref ON a.invDetKelbrgId = kelbrgId
            LEFT JOIN bidang_barang_ref ON a.invDetBidangbrngId = bidangbrgId
            LEFT JOIN golongan_barang_ref ON a.invDetGolId = golbrgId
            LEFT JOIN sumber_dana_ref ON sumberdanaId = invDetSumberDana
            LEFT JOIN gedung ON a.invDetGedungId = gedungId
            LEFT JOIN ruang ON a.invDetRuanganId = ruangId
            LEFT JOIN penyusutan_brg_mst ON invDetId = mstPenystnBarangId
            LEFT JOIN aset_ref_aset_status ON statusAsetId = a.invDetAsetStatusId
            LEFT JOIN do_detail_new ON doDetInvDetId = a.invDetId
            LEFT JOIN inv_map_simpel m ON m.invDetId = a.invDetId
            LEFT JOIN v_pembelian_verified v ON v.id = m.pembLgsngId
            LEFT JOIN inv_map_emonev em ON em.invDetId = a.invDetId
            LEFT JOIN v_perintah_bayar_pengadaan vp ON vp.id = em.pembPengdnId
        WHERE
            (
                invSPMNumber LIKE '%$search%' OR a.invDetMstLabel LIKE '%$search%' OR
                a.invDetKodeBarang LIKE '%$search%' OR v.nomor_spp LIKE '%$search%' OR vp.nomor_spp LIKE '%$search%'
            )
           
           $cari_coa
           
           $cari_pengadaan
           
           $cari_pembukuan
           
           $cari_dana
           
           $cari_milik
           
           $cari_kondisi
           
           $cari_status
           
           $cari_unit
           
           AND a.invDetIsDel = 0
           
           /*tampilkan hasil konversi*/
           
           AND (/*invMstPengadaanId = 1 OR */ invMstPengadaanId IS NULL
           
           /*tampilkan pengadaan yang sudah di DO dan diterima*/ 
           
           OR (/*invMstPengadaanId <> 1 AND */ invMstPengadaanId IS NOT NULL AND doDetId IS NOT NULL AND doDetRuangId IS NOT NULL)) 
           
        ORDER BY a.invDetGolId, a.invDetBidangbrngId,a.invDetKelbrgId, a.invDetSubkelbrgId, a.invDetBarangId, a.invDetNup ASC
        LIMIT $offset, $limit
		";
        
        $query = $this->aset->query($sql);
        //echo $this->aset->last_query(); exit;
        return $query->result();
    }

    public function get_num_data($search='', $coa='', $pengadaan='', $pembukuan='', $sumber_dana='', $kepemilikan='', $kondisi='', $status='', $unit_kerja='', $sub_unit='') {
        if ($coa!="") {
            $cari_coa = ' AND coaUGM='.$coa;
        } else {
            $cari_coa = '';
        }
        
        if ($pengadaan!="") {
            $cari_pengadaan = ' AND YEAR(invDetTglPembelian)='.$pengadaan;
        } else {
            $cari_pengadaan = '';
        }
        
        if ($pembukuan!="") {
            $cari_pembukuan = ' AND YEAR(a.invDetTglBuku)='.$pembukuan;
        } else {
            $cari_pembukuan = '';
        }
        
        if ($sumber_dana!="") {
            $cari_dana = ' AND invDetSumberDana='.$sumber_dana;
        } else {
            $cari_dana = '';
        }
        
        if ($kepemilikan!="") {
            $cari_milik = ' AND a.invDetKepemilikan='.$kepemilikan;
        } else {
            $cari_milik = '';
        }
        
        if ($kondisi!="") {
            $cari_kondisi = ' AND kondisibrgId='.$kondisi;
        } else {
            $cari_kondisi = '';
        }
        
        if ($status!="") {
            $cari_status = ' AND a.invDetAsetStatusId='.$status;
        } else {
            $cari_status = '';
        }
        
  		if (!empty($sub_unit)) {
  		    $cari_unit = ' AND unitkerjaKodeSistem LIKE "'.$sub_unit.'%"';
		} else {
            $cari_unit = ' AND unitkerjaKode="'.$unit_kerja.'"';
		}
    
        $sql = "
        SELECT count(a.invDetId) AS jumlah
        FROM
           inventarisasi_detail a
           LEFT JOIN inventarisasi_mst ON a.invDetMstId = invMstId
           LEFT JOIN barang_ref ON a.invDetBarangId = barangId
           LEFT JOIN kampus_ref ON a.invDetKampus = kampusId
           LEFT JOIN unit_kerja_ref  ON a.invDetUnitKerja = unitkerjaId
           LEFT JOIN kepemilikan_barang_ref ON kepemilikanbrgId = a.invDetKepemilikan
           LEFT JOIN kondisi_barang_ref ON kondisibrgId = a.invDetKondisiId
           LEFT JOIN sub_kelompok_barang_ref ON a.invDetSubkelbrgId = subkelbrgId
           LEFT JOIN kelompok_barang_ref ON a.invDetKelbrgId = kelbrgId
           LEFT JOIN bidang_barang_ref ON a.invDetBidangbrngId = bidangbrgId
           LEFT JOIN golongan_barang_ref ON a.invDetGolId = golbrgId
           LEFT JOIN sumber_dana_ref ON sumberdanaId = invDetSumberDana
           LEFT JOIN gedung ON a.invDetGedungId = gedungId
           LEFT JOIN ruang ON a.invDetRuanganId = ruangId
           LEFT JOIN penyusutan_brg_mst ON a.invDetId = mstPenystnBarangId
           LEFT JOIN aset_ref_aset_status ON statusAsetId = a.invDetAsetStatusId
           LEFT JOIN do_detail_new ON doDetInvDetId = a.invDetId
           LEFT JOIN inv_map_simpel m ON m.invDetId = a.invDetId
           LEFT JOIN v_pembelian_verified v ON v.id = m.pembLgsngId
           LEFT JOIN inv_map_emonev em ON em.invDetId = a.invDetId
           LEFT JOIN v_perintah_bayar_pengadaan vp ON vp.id = em.pembPengdnId
        WHERE
           (
              invSPMNumber LIKE '%$search%' OR a.invDetMstLabel LIKE '%$search%' OR
              a.invDetKodeBarang LIKE '%$search%' OR v.nomor_spp LIKE '%$search%' OR vp.nomor_spp LIKE '%$search%'
           )
           
           $cari_coa
           
           $cari_pengadaan
           
           $cari_pembukuan
           
           $cari_dana
           
           $cari_milik
           
           $cari_kondisi
           
           $cari_status
           
           $cari_unit
           
           AND a.invDetIsDel = 0
           
           /*tampilkan hasil konversi*/
           
           AND (/*invMstPengadaanId = 1 OR */ invMstPengadaanId IS NULL
           
           /*tampilkan pengadaan yang sudah di DO dan diterima*/
           
           OR (/*invMstPengadaanId <> 1 AND */ invMstPengadaanId IS NOT NULL AND doDetId IS NOT NULL AND doDetRuangId IS NOT NULL)) 
		";
        
        $query = $this->aset->query($sql);
        //echo $this->aset->last_query(); exit;
        return $query->row();
    }

   	public function get_data_unit_kerja($kode) {
		$this->aset->select("
            unitkerjaId AS id,
            unitkerjaKode AS kode,
            unitkerjaNama AS nama
		", false);
        
        $this->aset->from('unit_kerja_ref');
        $this->aset->like('unitkerjaKodeSistem', $kode);
		$this->aset->where('unitkerjaActive', 'Y');
        $this->aset->order_by('unitkerjaKodeSistem ASC');
        
        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->result();
	}
    
   	public function get_unit_kerja_by_kode($kode) {
		$this->aset->select("
            unitkerjaId AS id,
            unitkerjaKode AS kode,
            unitkerjaNama AS nama
		", false);
        
        $this->aset->from('unit_kerja_ref');
        $this->aset->where('unitkerjaKode', $kode);
              
        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->row();
	}

   	public function get_unit_kerja_kode_sistem($unit) {
		$this->aset->select("
            unitkerjaKodeSistem AS kode
		", false);
        
        $this->aset->from('unit_kerja_ref');
		$this->aset->where('unitkerjaKode', $unit);
		$this->aset->where('unitkerjaActive', 'Y');
        
        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->row();
	}

   	public function get_kode_aset($gol, $limit, $offset, $search){
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
        $this->aset->like('barangNama', $search);
        
        $this->aset->limit($limit, $offset);

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->result();
	}
    
   	public function num_kode_aset($gol, $search){
		$this->aset->select("
            COUNT(*) AS jumlah
		", false);
        
        $this->aset->from('golongan_barang_ref');
        $this->aset->join('bidang_barang_ref', 'bidangbrgGolbrgId=golbrgId');
        $this->aset->join('kelompok_barang_ref', 'kelbrgBidangbrgId=bidangbrgId');
        $this->aset->join('sub_kelompok_barang_ref', 'subkelbrgKelbrgId=kelbrgId');
        $this->aset->join('barang_ref', 'barangSubkelbrgId=subkelbrgId');
        
        $this->aset->where('golbrgId', $gol);
        $this->aset->where('barangAktif', 'Y');
        $this->aset->like('barangNama', $search);

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->row();
	}
    
   	public function get_kelompok_coa(){
		$this->aset->select("
            coaKode AS id,
            CONCAT(coaKode, ' - ', coaNama) AS nama 
		", false);
        
        $this->aset->from('coa_ugm_ref');
        
        $kodes = array('B', 'D');
        $this->aset->where_in('coaGroupKode', $kodes);
        $this->aset->not_like('coaNama', 'Akumulasi', 'after');
        
        $this->aset->order_by('coaKode', 'ASC');

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->result();
	}
    
   	public function get_sumber_dana_pembelian(){
		$this->aset->select("
            sumberdanaId AS id,
            sumberdanaNama AS nama
		", false);
        
        $this->aset->from('sumber_dana_ref');
        
        $this->aset->where('sumberdanaId!=', 8);
        $this->aset->where('sumberdanaId!=', 9);
        $this->aset->order_by('sumberdanaNama');

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->result();
	}
    
   	public function get_sumber_dana_hibah(){
		$this->aset->select("
            sumberdanaId AS id,
            sumberdanaNama AS nama
		", false);
        
        $this->aset->from('sumber_dana_ref');
        
        $this->aset->where('sumberdanaId', 8);
        $this->aset->or_where('sumberdanaId', 9);
        $this->aset->order_by('sumberdanaNama');

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
    
   	public function get_kondisi(){
		$this->aset->select("
            kondisibrgId AS id,
            kondisibrgNama AS nama
		", false);
        
        $this->aset->from('kondisi_barang_ref');
        $this->aset->order_by('kondisibrgId');

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->result();
	}
    
   	public function get_status_aset(){
		$this->aset->select("
            statusAsetId AS id,
            statusAsetNama AS nama
		", false);
        
        $this->aset->from('aset_ref_aset_status');
        $this->aset->order_by('statusAsetNama', 'ASC');

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
        $this->aset->where('golbrgId!=', 1);
        $this->aset->where('golbrgId!=', 7);
        $this->aset->where('golbrgId!=', 9);

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
    
   	public function get_lokasi_aset($limit, $offset, $search) {
		$this->aset->select("
            r.ruangId AS id,
            r.ruangKode AS kode,
            r.ruangNama AS ruang,
            g.gedungNama AS gedung,
            r.ruangGedungId AS gedung_id,
            r.ruangLantaiId AS lantai_id
		", false);
        
        $this->aset->from('ruang r');
        $this->aset->join('gedung g', 'g.gedungId=r.ruangGedungId' , 'left');
        
        $this->aset->like('g.gedungNama', $search);
        $this->aset->or_like('r.ruangNama', $search);
        
        $this->aset->order_by('r.ruangKode', 'ASC');
        $this->aset->limit($limit, $offset);

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->result();
	}
    
   	public function num_lokasi_aset($search=''){
		$this->aset->select("
            COUNT(*) AS jumlah
		", false);
        
        $this->aset->from('ruang r');
        $this->aset->join('gedung g', 'g.gedungId=r.ruangGedungId' , 'left');
        
        $this->aset->like('g.gedungNama', $search);
        $this->aset->or_like('r.ruangNama', $search);
        
        $this->aset->order_by('r.ruangKode', 'ASC');

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->row();
	}
    
   	public function select_max_invmstid(){
		$this->aset->select("
            invMstId AS id
		", false);
        
        $this->aset->from('inventarisasi_mst');
        $this->aset->order_by('invMstId', 'DESC');
        $this->aset->limit(1);

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->row();
	}
    
   	public function select_max_invdetid(){
		$this->aset->select("
            invDetId AS id
		", false);
        
        $this->aset->from('inventarisasi_detail');
        $this->aset->order_by('invDetId', 'DESC');
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
    
   	public function select_nomor_barang($kode, $unit){
		$this->aset->select("
            nup
		", false);
        
        $this->aset->from('inv_last_nup');
        $this->aset->where('brgId', $kode);
        $this->aset->where('unitId', $unit);

        $query = $this->aset->get();
        //echo $this->aset->last_query(); exit;
        return $query->row();
	}    
    
   	public function select_detail_aset($id){
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
        
        $this->aset->from('inventarisasi_mst');
        $this->aset->join('inventarisasi_detail id', 'invDetMstId = invMstId');
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
    
    public function select_detail_transaksi($kode, $unit) {
        $kd_brg = SUBSTR($kode, 0, 10);
        $no_aset = SUBSTR($kode, 10, 1);
        
        $sql = "
        SELECT
           u.kodeBrg,
           CONCAT(
              MID(u.kodeBrg, 1, 1), '.', MID(u.kodeBrg, 2, 2), '.',MID(u.kodeBrg, 4, 2), '.', 
              MID(u.kodeBrg, 6, 2), '.', MID(u.kodeBrg, 8, 3)
           ) AS kodeSubKel,
           br.barangNama AS brgNama,
           u.nup,
           DATE_FORMAT(u.tglBeli, '%d-%m-%Y') AS tglBeli,
           DATE_FORMAT(u.tglBuku, '%d-%m-%Y') AS tglBuku,
           u.umurEko,
           IF(u.diff <= 0, 0, u.diff) AS sisaUmur,
           u.jnsTrn,
           jtr.jnsTrnNama AS jnsUr,
           u.nilai,
           unitkerjaNama AS unit,
           u.unitId
        FROM (
        # from SIMAKBMN (<= 2015-12-31)
        SELECT 
           CONCAT(kd_brg,no_aset)+0 AS kodeBrg,
           kd_brg+0 AS brgId,
           no_aset+0 AS nup,
           id.invDetTglPembelian AS tglBeli,
           tgl_buku AS tglBuku,
           id.invDetUmurEkonomis AS umurEko,
           id.invDetUmurEkonomis - PERIOD_DIFF(DATE_FORMAT(tgl_buku,'%Y%m'),DATE_FORMAT(id.invDetTglPembelian,'%Y%m')) AS diff,
           jns_trn AS jnsTrn,
           rph_aset AS nilai,
           unitId+0 AS unitId,
           CONCAT('idh_',idh.invDetId) AS uid
        FROM inventarisasi_detail_history idh
        JOIN inventarisasi_detail id ON id.invDetId = idh.invDetId
        WHERE kd_brg = '$kd_brg' AND no_aset = '$no_aset' AND unitId = '$unit'
        UNION
        # inv_det_trans (>= 2016)
        SELECT
           transKodeBrg+0 AS kodeBrg,
           LEFT(transKodeBrg,10)+0 AS brgId,
           RIGHT(transKodeBrg,LENGTH(transKodeBrg)-10)+0 AS nup,
           id.invDetTglPembelian AS tglBeli,
           DATE_FORMAT(transTglBuku,'%%Y-%%m-%%d') AS tglBuku,
           id.invDetUmurEkonomis AS umurEko,
           CASE
           WHEN transKeterangan LIKE 'KAP%%' THEN 
              (
                 (
                    SELECT invKapDetUmur FROM inv_kap_det ikd 
                    WHERE ikd.invKapDetMstId = transOptId+0 AND ikd.invKapDetInvId = transInvDetId
                 ) + IFNULL(
                    (
                       SELECT IFNULL(penyusutanDetSisaUmrEk,0) 
                       FROM penyusutan_det pd 
                       JOIN penyusutan_mst pm ON pm.penyusutanMstId = penyusutanDetMst
                       WHERE 
                          pm.penyusutanMstPeriode = LAST_DAY(DATE_SUB(transTglBuku, INTERVAL 1 MONTH)) AND 
                          pd.penyusutanDetBrg = transInvDetId
                       LIMIT 1
                    ),0
                 )
              )
           WHEN transKeterangan = 'MTS' THEN 
              (
                 SELECT IFNULL(penyusutanDetSisaUmrEk,0) 
                 FROM penyusutan_det pd 
                 JOIN penyusutan_mst pm ON pm.penyusutanMstId = penyusutanDetMst
                 WHERE 
                    pm.penyusutanMstPeriode <= transTglBuku AND 
                    pd.penyusutanDetBrg = transInvDetId
                 ORDER BY penyusutanDetId DESC
                 LIMIT 1
              )
           ELSE id.invDetUmurEkonomis - PERIOD_DIFF(DATE_FORMAT(transTglBuku,'%%Y%%m'),DATE_FORMAT(id.invDetTglPembelian,'%%Y%%m'))
           END AS diff,
           transJnsTrn AS jnsTrn,
           transNilai AS nilai,
           transUnitId+0 AS unitId,
           CONCAT('idt_',transInvDetId) AS uid
        FROM inv_det_trans
        JOIN inventarisasi_detail id ON id.invDetId = transInvDetId
        WHERE transKodeBrg = '$kode' AND transUnitId = '$unit'
        UNION
        # penyusutan
        SELECT
           kodeBrg+0 AS kodeBrg,
           LEFT(kodeBrg,10)+0 AS brgId,
           RIGHT(kodeBrg,LENGTH(kodeBrg)-10)+0 AS nup,
           id.invDetTglPembelian AS tglBeli,
           penyusutanMstPeriode AS tglBuku,
           id.invDetUmurEkonomis AS umurEko,
           IF(
              penyusutanDetSisaUmrEk > 0, penyusutanDetSisaUmrEk,
              id.invDetUmurEkonomis - PERIOD_DIFF(
                 DATE_FORMAT(penyusutanMstPeriode,'%Y%m'),DATE_FORMAT(id.invDetTglPembelian,'%Y%m')
              )
           )AS diff,
           CASE penyusutanMstKet
           WHEN 'psusut' THEN 'S01'
           WHEN 'rsusut' THEN 'S02'
           WHEN 'msusut' THEN 'S03'
           WHEN 'HPS' THEN 'S03'
           ELSE 'S04'
           END AS jnsTrn,
           -penyusutanDetNilaiPenyusutan AS nilai,
           unitId+0 AS unitId,
           CONCAT('sst_',penyusutanDetId) AS uid
        FROM penyusutan_det
        JOIN inventarisasi_detail id ON id.invDetId = penyusutanDetBrg
        JOIN penyusutan_mst ON penyusutanMstId = penyusutanDetMst
        WHERE kodeBrg = '$kode' AND unitId = '$unit'
        UNION
        # penyusutan_mutasi
        SELECT
           susutKodeBrg+0 AS kodeBrg,
           LEFT(susutKodeBrg,10)+0 AS brgId,
           RIGHT(susutKodeBrg,LENGTH(susutKodeBrg)-10)+0 AS nup,
           id.invDetTglPembelian AS tglBeli,
           DATE_FORMAT(susutTglBuku,'%%Y-%%m-%%d') AS tglBuku,
           id.invDetUmurEkonomis AS umurEko,
           (
              SELECT IFNULL(penyusutanDetSisaUmrEk,0) 
              FROM penyusutan_det pd 
              JOIN penyusutan_mst pm ON pm.penyusutanMstId = penyusutanDetMst
              WHERE 
                 pm.penyusutanMstPeriode <= susutTglBuku AND 
                 pd.penyusutanDetBrg = susutInvDetId
              ORDER BY penyusutanDetId DESC
              LIMIT 1
           ) AS diff,
           #id.invDetUmurEkonomis - PERIOD_DIFF(DATE_FORMAT(susutTglBuku,'%%Y%%m'),DATE_FORMAT(id.invDetTglPembelian,'%%Y%%m'))-1 AS diff,
           susutJnsTrn AS jnsTrn,
           susutRph AS nilai,
           susutUnitId+0 AS unitId,
           CONCAT('sstm_',susutInvDetId) AS uid
        FROM penyusutan_mutasi
        JOIN inventarisasi_detail id ON id.invDetId = susutInvDetId
        WHERE susutKodeBrg = '$kode' AND susutUnitId = '$unit'
        ) u
        LEFT JOIN unit_kerja_ref ON unitkerjaId = u.unitId
        LEFT JOIN barang_ref br ON br.barangId = u.brgId
        LEFT JOIN jenis_transaksi_ref jtr ON jtr.`jnsTrnKode` = u.jnsTrn
        ORDER BY u.kodeBrg,u.unitId,u.tglBuku,u.jnsTrn
		";
        
        $query = $this->aset->query($sql);
        //echo $this->aset->last_query(); exit;
        return $query->result();
	}


    // INSERT DATA INVENTARISASI (MASTER) ------------------------------------------------------------------------------- //
    public function insert_inventarisasi_mst($data) {
        $query = $this->aset->insert('inventarisasi_mst', $data);
        //echo $this->aset->last_query(); exit;
        return $query;
    }
    
    // INSERT DATA INVENTARISASI (DETAIL) ------------------------------------------------------------------------------ //
    public function insert_inventarisasi_detail($data) {
        $query = $this->aset->insert('inventarisasi_detail', $data);
        //echo $this->aset->last_query(); exit;
        return $query;
    }
    
    // INSERT DATA PENYUSUTAN BARANG (MASTER) -------------------------------------------------------------------------- //
    public function insert_penyusutan_brg_mst($data) {
        $query = $this->aset->insert('penyusutan_brg_mst', $data);
        //echo $this->aset->last_query(); exit;
        return $query;
    }
    
    // INSERT DATA TRANSAKSI INVENTARIS (DETAIL) ----------------------------------------------------------------------- //
    public function insert_inv_det_trans($data) {
        $query = $this->aset->insert('inv_det_trans', $data);
        //echo $this->aset->last_query(); exit;
        return $query;
    }
}