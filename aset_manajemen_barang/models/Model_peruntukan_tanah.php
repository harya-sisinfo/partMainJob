<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model peruntukan tanah
 * @created : 2021-07-29 10:05:00
 * @author  : sriharyo <srihary@ugm.ac.id>
 * @company : DSDI UGM
*/
class Model_peruntukan_tanah extends CI_Model 
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

	/* ==============================================================  */
	/* Model : get_data, get_num_data
	*/
	public function get_select(){
		$this->aset->select("
			SQL_CALC_FOUND_ROWS
			invPeruntukanTnhId AS idTanah,
			invPeruntukanTnhInvDetId AS invdet,
			b.invDetMstLabel AS nama_aset,
			invPeruntukanTnhKode AS kodeinv,
			peruntukanTanahNama AS peruntukan,
			invPeruntukanTnhPanjang AS panjang,
			invPeruntukanTnhLebar AS lebar,
			invPeruntukanTnhLuas AS luas,
			statusAsetNama AS penggunaan,
			CONCAT(
			invDetKodeBarang,
			'.',
			invPeruntukanTnhKode
			) AS kode 
			", false);
	}
	public function get_join(){
		$this->aset->join("inventarisasi_detail b", "a.invMstId = b.invDetMstId","left");
		$this->aset->join("barang_ref c", "a.invBarangId = barangId","left");
		$this->aset->join("sub_kelompok_barang_ref i", "barangSubkelbrgId = subkelbrgId");
		$this->aset->join("kelompok_barang_ref j", "subkelbrgKelbrgId = kelbrgId");
		$this->aset->join("bidang_barang_ref k", "kelbrgBidangbrgId = bidangbrgId");
		$this->aset->join("golongan_barang_ref l", "bidangbrgGolbrgId = golbrgId");
		$this->aset->join("inv_peruntukan_tanah", "invPeruntukanTnhInvDetId = invDetId AND (invPeruntukanTnhStatusDel != '1' OR invPeruntukanTnhStatusDel is NULL)");
		$this->aset->join("peruntukan_tanah_ref", "invPeruntukanTnhPrtknTnhId = peruntukanTanahId");
		$this->aset->join("aset_ref_aset_status", "statusAsetId = invPeruntukanTnhStatusAsetId");
		$this->aset->where("l.golbrgKibKode","a");
	}
	public function get_where($search=''){
		if(!empty($search)){
			$this->aset->where("(
				statusAsetNama LIKE '%{$search}%' OR 
				CONCAT(
				invDetKodeBarang,
				'.',
				invPeruntukanTnhKode
				) LIKE '%{$search}%') 
				OR ( b.invDetMstLabel LIKE '%{$search}%')
				OR ( invPeruntukanTnhKode LIKE '%{$search}%')
				OR ( peruntukanTanahNama LIKE '%{$search}%')
				OR ( invPeruntukanTnhLuas LIKE '%{$search}%')
				", null, false);
			
		}
		
	}
	public function get_data($limit, $offset, $search, $order){
		$this->get_select();
		$this->get_join();
		$this->get_where($search);
		$this->aset->order_by($order);
		$this->aset->limit($limit, $offset);
		return $this->aset->get('inventarisasi_mst a')->result_array();
	}

	public function get_num_data($search=''){
		$this->get_select('count(*) AS jumlah');
		$this->get_join();
		$this->get_where($search);
		return $this->aset->get('inventarisasi_mst a')->num_rows();
	}

	public function get_data_by_id($id){
		$this->aset->select('invPeruntukanTnhInvDetId AS invdet,
		   b.invDetMstLabel AS nama_aset,
		   invPeruntukanTnhKode AS kodeinv,
		   invPeruntukanTnhPrtknTnhId AS peruntukan,
		   invPeruntukanTnhPanjang AS panjang,
		   invPeruntukanTnhLebar AS lebar,
		   invPeruntukanTnhLuas AS luas,
		   invPeruntukanTnhStatusAsetId AS penggunaan,
		   invPeruntukanTnhKeterangan AS keterangan,
		   invDetKodeBarang AS kode,
		   a.invLuasTanah AS luasHidden,
		   invPeruntukanTnhAlamat AS alamat,
		   invPeruntukanTnhKodePemerintah AS kodepemerintah,
		   invPeruntukanTnhPj AS unitnama,
		   invPeruntukanTnhNoSertipikat AS sertipikat');

		$this->aset->join('inventarisasi_detail b','a.invMstId = b.invDetMstId','left');
		$this->aset->join('barang_ref c','a.invBarangId = barangId','left');
		$this->aset->join('sub_kelompok_barang_ref i','barangSubkelbrgId = subkelbrgId');
		$this->aset->join('kelompok_barang_ref j','subkelbrgKelbrgId = kelbrgId');
		$this->aset->join('bidang_barang_ref k','kelbrgBidangbrgId = bidangbrgId');
		$this->aset->join('golongan_barang_ref l','bidangbrgGolbrgId = golbrgId');
		$this->aset->join('inv_peruntukan_tanah','invPeruntukanTnhInvDetId = invDetId');

		$this->aset->where('l.golbrgKibKode','a');
		$this->aset->where('invPeruntukanTnhId',$id);
		return $this->aset->get('inventarisasi_mst a')->row_array();

	}
	public function select_inventaris_tanah($search='')
	{
		$this->aset->join('inventarisasi_detail b','a.invMstId = b.invDetMstId','left');
		$this->aset->join('barang_ref c','a.invBarangId = c.barangId','left');
		$this->aset->join('sub_kelompok_barang_ref i','c.barangSubkelbrgId = i.subkelbrgId');
		$this->aset->join('kelompok_barang_ref j','i.subkelbrgKelbrgId = j.kelbrgId');
		$this->aset->join('bidang_barang_ref k','j.kelbrgBidangbrgId = k.bidangbrgId');
		$this->aset->join('golongan_barang_ref l','bidangbrgGolbrgId = l.golbrgId');
		$this->aset->join('unit_kerja_ref e','b.invDetUnitKerja = e.unitkerjaId','left');

		$this->aset->where("(l.golbrgKibKode = 'a')");
		if(!empty($search)){
			$this->aset->where("(b.invDetKodeBarang LIKE '%$search%' OR b.invDetMstLabel LIKE '%$search%')");
		}
		$this->aset->order_by("b.invDetTglPembelian","DESC");
	}

	public function num_inventaris_tanah($search='')
	{

		$this->aset->select("count(*) AS jumlah");
		$this->select_inventaris_tanah($search);
		return $this->aset->get('inventarisasi_mst a')->row_array();
	}
	public function get_inventaris_tanah($limit, $offset=0,$search='')
	{
		$this->aset->select("
			b.invDetId AS id,
			b.invDetKodeBarang AS KIB,
			a.invLuasTanah AS luas,
			b.invDetKeteranganLain AS keterangan,
			b.invDetAsetStatusId AS penggunaan,
			e.unitkerjaNama AS unit_pj,
			b.invDetMstLabel AS nama_aset");
		$this->select_inventaris_tanah($search);
		$this->aset->limit($limit,$offset);
		return $this->aset->get('inventarisasi_mst a')->result_array();
	}


	public function get_kode_inventarisasi(){
		$this->aset->select("
			SQL_CALC_FOUND_ROWS b.invDetId AS id,
			invDetKodeBarang AS KIB,
			a.invBarangId AS id_barang,
			c.barangKode AS kode_barang,
			b.invDetMstLabel AS nama_aset,
			CONCAT(
			kampusNama,
			IF(
			gedungNama IS NULL,
			'',
			CONCAT(
			'@@',
			gedungNama,
			IF(
			ruangNama IS NULL,
			'',
			CONCAT('@@', ruangNama)
			)
			)
			)
			) AS lokasi_aset,
			e.unitkerjaNama AS unit_pj,
			LEFT(b.invDetTglPembelian, 4) AS thn_pengadaan,
			IF(
			mstPenystnNilaiPerolehan IS NOT NULL,
			mstPenystnNilaiPerolehan,
			IF(
			b.invDetNilaiPerolehanSatuan IS NULL,
			a.invTotalPerolehan,
			b.invDetNilaiPerolehanSatuan
			)
			) AS nilai_perolehan,
			IF(
			mstPenystnDisusutkan IS NOT NULL,
			mstPenystnDisusutkan,
			IF(
			b.invDetNilaiPerolehanSatuan IS NULL,
			a.invTotalPerolehan,
			b.invDetNilaiPerolehanSatuan
			)
			) AS nilai_buku,
			f.kepemilikanbrgNama AS status_kepemilikan,
			h.kondisibrgNama AS kondisi,
			golbrgId AS golBarang,
			invLuasTanah AS luas,
			invDetKeteranganLain AS keterangan,
			invDetAsetStatusId AS penggunaan
			");
		$this->aset->join('inventarisasi_detail b','a.invMstId = b.invDetMstId','left');
		$this->aset->join('barang_ref c','a.invBarangId = barangId','left');
		$this->aset->join('kampus_ref d','b.invDetKampus = d.kampusId','left');
		$this->aset->join('unit_kerja_ref e','b.invDetUnitKerja = e.unitkerjaId','left');
		$this->aset->join('kepemilikan_barang_ref f','f.kepemilikanbrgId = b.invDetKepemilikan','left');
		$this->aset->join('kondisi_barang_ref h','h.kondisibrgId = b.invDetKondisiId','left');
		$this->aset->join('sub_kelompok_barang_ref i','barangSubkelbrgId = subkelbrgId');
		$this->aset->join('kelompok_barang_ref j','subkelbrgKelbrgId = kelbrgId');
		$this->aset->join('bidang_barang_ref k','kelbrgBidangbrgId = bidangbrgId');
		$this->aset->join('golongan_barang_ref l','bidangbrgGolbrgId = golbrgId');
		$this->aset->join('gedung','invDetGedungId = gedungId','left');
		$this->aset->join('ruang','invDetRuanganId = ruangId','left');
		$this->aset->join('penyusutan_brg_mst','invDetId = mstPenystnBarangId','left');
		$this->aset->join('aset_ref_aset_status','statusAsetId = b.invDetAsetStatusId');

		$this->aset->where("(l.golbrgKibKode = 'a')");
		$this->aset->where("
			(
			b.invDetMstLabel LIKE '%s' 
			OR invDetKodeBarang LIKE '%s'
		)");
		$this->aset->where("
			(
			invDetUnitKerja = '%s'
			OR e.unitkerjaParentId = '%s' OR '1' = '%s'
		)");
		return $this->aset->get('inventarisasi_mst a')->result_array();
	}

	public function get_status_pengguna()
	{
		$this->aset->select('
			statusAsetId   AS id,
			statusAsetNama AS `name`
			');
		return $this->aset->get_where('aset_ref_aset_status',array('statusAsetIsAktif'=>'Y'))->result_array();
	}
	public function get_peruntukan_tanah()
	{
		$this->aset->select('
			peruntukanTanahId AS id,
			peruntukanTanahNama AS `name`
			');
		return $this->aset->get('peruntukan_tanah_ref ')->result_array();
	}

	public function get_generate_kode($kode='')
	{
		$this->aset->select('
			CONCAT(
					invDetKodeBarang,
					".",
					invPeruntukanTnhKode
				) AS kode
			');
		$this->aset->join('inventarisasi_detail b','a.invMstId = b.invDetMstId','left');
		$this->aset->join('barang_ref c','a.invBarangId = barangId','left');
		$this->aset->join('sub_kelompok_barang_ref i','barangSubkelbrgId = subkelbrgId');
		$this->aset->join('kelompok_barang_ref j','subkelbrgKelbrgId = kelbrgId');
		$this->aset->join('bidang_barang_ref k','kelbrgBidangbrgId = bidangbrgId');
		$this->aset->join('golongan_barang_ref l','bidangbrgGolbrgId = golbrgId');
		$this->aset->join('inv_peruntukan_tanah','invPeruntukanTnhInvDetId = invDetId');

		$this->aset->where("l.golbrgKibKode",'a');
		$this->aset->where("invDetKodeBarang",$kode);
		$this->aset->order_by("kode","DESC");
		$this->aset->limit(1);
		
		return $this->aset->get('inventarisasi_mst a')->row_array();
	}

// === Ubah format angka === 
	// ** format_text_to_decimal ** 	
    public function format_text_to_decimal($value)
    {
        $splits = explode(",", $value);
        $splits[0] = explode(".", $splits[0]);
        $splits[0] = implode("", $splits[0]);
        $splits = implode(".", $splits);
        return $splits;
    }

	public function insert_data($tabel,$data)
	{
		return $this->aset->insert($tabel, $data);
	}
	public function update_data($tabel,$kunci_primer,$id,$data)
    {
        $this->aset->where($kunci_primer, $id);
        return $this->aset->update($tabel, $data);
    }
    public function delete_data($tabel,$kunci_primer,$id) {
        $this->aset->where($kunci_primer, $id);
        return $this->aset->delete($tabel);
    }
}
