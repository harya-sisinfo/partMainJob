<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model penggunaan aset
 * @created : 2021-07-29 10:05:00
 * @author  : sriharyo <srihary@ugm.ac.id>
 * @company : DSDI UGM
*/
class Model_penggunaan_aset extends CI_Model 
{
	public $data = '';
	public function __construct() 
	{
		parent::__construct();
		$this->aset     	= $this->load->database("aset",TRUE);
		$this->user_id      = $this->session->userdata('__user_id');
		$this->username     = $this->session->userdata('__username');
		$this->kode_unit    = $this->session->userdata('__objUser')->unit_kerja_kode;
		$this->user_level   = $this->session->userdata('__objUser')->user_level;
	}
	
	public function userAsetSession()
	{

		$this->aset->select('a.UserId AS id_user');
		$this->aset->where('a.user_username',$this->username);
		//$sql_user 			= $this->aset->get('ugmfw_mapping a');
		$dataAsetUser 		= $this->aset->get('ugmfw_mapping a')->row_array();
		$data['user_id'] 	= $dataAsetUser['id_user'];
		return $data;
	}
	public function get_select()
	{
		$this->aset->select("SQL_CALC_FOUND_ROWS
			asetGunaId AS id,
			asetGunaTglSip AS tgl,
			asetGunaNoRef AS noRef,
			asetGunaNama AS nama,
			asetGunaNIP AS nip,
			asetGunaJabatan AS jab,
			asetGunaNoTelp AS telp,
			COUNT(asetGunaDetId) AS jmlAset,
			CONCAT(unitkerjaNama,' (',unitkerjaKode,')') AS unit,
			IF(asetGunaAktif = 'Y','Aktif','Expired') AS `status`,
			asetGunaAktif AS stat,
			asetGunaFile AS fileSip
			", false);
	}
	public function get_join()
	{
		$this->aset->join("aset_penggunaan_det", "asetGunaDetMstId = asetGunaId","left");
		$this->aset->join("unit_kerja_ref", "unitkerjaId = asetGunaUnitId","left");
		$this->aset->group_by("id");

	}

	public function get_where($params,$search)
	{
		if($this->user_level==2 || $this->user_level==3)
		{
			$this->aset->where("unit_kerja_ref.unitkerjaKode",$this->kode_unit);
		}
		if(!empty($search))
		{
			$this->aset->where("
				(
				tgl LIKE '%".$search."%' OR 
				noRef LIKE '%".$search."%' OR 
				nama LIKE '%".$search."%' OR 
				nip LIKE '%".$search."%' OR 
				jab LIKE '%".$search."%' )
				");
		}
		if(!empty($params['noba']))
		{
			$this->aset->like("noRef",$params['noba']);
		}
		if(!empty($params['stat']))
		{
			$this->aset->like("asetGunaAktif",$params['stat']);
		}
	}
	public function get_data($limit, $offset, $search, $order,  $params)
	{
		$this->get_select();
		$this->get_join();
		$this->get_where($params,$search);
		$this->aset->limit($limit, $offset);
		return $this->aset->get('aset_penggunaan_mst')->result_array();
	}

	public function get_num_data($params,$search)
	{
		
		$this->get_select();
		$this->get_join();
		$this->get_where($params,$search);
		
		
		return $this->aset->get('aset_penggunaan_mst')->num_rows();
	}

	public function num_barang($search='')
	{
		$this->aset->select("
			count(*) AS jumlah
			");
		$this->aset->join("satuan_barang_ref","satuanbrgId = invDetSatuanBarang","left");
		$this->aset->join("kondisi_barang_ref","kondisibrgId = invDetKondisiId","left");
		$this->aset->join("penyusutan_brg_mst","mstPenystnBarangId = invDetId");
		$this->aset->join("unit_kerja_ref","unitkerjaId = invDetUnitKerja");
		$this->aset->join("penghapusan_det","hapusDetInvDetId = invDetId","left");
		$this->aset->where("hapusDetId IS NULL");
		
		$this->aset->where("invDetIsDel = 0");
		if(!empty($search)){
			$this->aset->where("(invDetMstLabel LIKE '%".$search."%' OR invDetKodeBarang LIKE '%".$search."%' OR invDetMerek LIKE '%".$search."%')");
		}
		$this->aset->where("invDetAsetStatusId","1");
		$this->aset->group_by("invDetId");

		return $this->aset->get('inventarisasi_detail')->row_array();
	}


	public function get_barang($limit,$offset,$search='',$unit='')
	{
		$this->aset->select("
			id.invDetId AS label_id,
			invDetKodeBarang AS label_kode,
			invDetMstLabel AS label_nama,
			invDetMerek AS merk,
			invDetSpesifikasi AS spec,
			DATE_FORMAT(invDetTglPembelian,'%d-%m-%Y') AS tgl_beli,
			unitkerjaId AS unit_id,
			unitkerjaNama AS unit_nama,
			invDetKondisiId AS kondisi,
			id.invDetNilaiPerolehanSatuan AS nilOleh
			");

		$this->aset->join("unit_kerja_ref","unitkerjaId = invDetUnitKerja","left");
		$this->aset->join("penghapusan_det","hapusDetInvDetId = invDetId","left");
		$this->aset->join("penyusutan_brg_mst","mstPenystnBarangId = invDetId","left");

		$this->aset->where("hapusDetId IS NULL");

		
		if(!empty($search)){
			$this->aset->where("(invDetMstLabel LIKE '%".$search."%' OR invDetKodeBarang LIKE '%".$search."%')");
		}
		if(!empty($unit)){
			$this->aset->where("unitkerjaNama LIKE '".$unit."'");
		}
		$this->aset->where("invDetAsetStatusId",'1');
		$this->aset->where("invDetIsDel","0");
		$this->aset->where("hapusDetId IS NULL");
		$this->aset->group_by("invDetId");
		$this->aset->limit($limit,$offset);
		return $this->aset->get('inventarisasi_detail id')->result_array();
	}

	public function get_unit_kerja()
	{
		$this->aset->select('unitkerjaId AS id,
			unitkerjaKode AS kode,
			unitkerjaNama AS nama
			');

		
		$this->aset->where('unitkerjaActive','Y');

		return $this->aset->get('unit_kerja_ref')->result_array();
	}


	public function get_data_by_id($id)
	{
		$this->aset->select("
			aset_penggunaan_mst.asetGunaNoRef,
			DATE_FORMAT(aset_penggunaan_mst.asetGunaTglSip,'%d-%m-%Y') AS asetGunaTglSip,
			aset_penggunaan_mst.asetGunaNama,
			aset_penggunaan_mst.asetGunaNIP,
			aset_penggunaan_mst.asetGunaJabatan,
			aset_penggunaan_mst.asetGunaNoTelp,
			aset_penggunaan_mst.asetGunaFile,
			aset_penggunaan_mst.asetGunaUnitId,
			aset_penggunaan_mst.asetGunaNamaPemberi,
			aset_penggunaan_mst.asetGunaNIPPemberi,
			aset_penggunaan_mst.asetGunaJbtnPemberi,
			aset_penggunaan_mst.asetGunaUnitPemberi,
			aset_penggunaan_mst.asetGunaKet,
			aset_penggunaan_mst.asetGunaAktif,
			aset_penggunaan_mst.asetGunaCreatorId,
			aset_penggunaan_mst.asetGunaCreated");
		$this->aset->where('aset_penggunaan_mst.asetGunaId',$id);
		return $this->aset->get('aset_penggunaan_mst')->row_array();
	}

	public function get_data_detail_by_id($id)
	{
		$this->aset->select("
			aset_penggunaan_det.asetGunaDetId,
			aset_penggunaan_det.asetGunaDetMstId,
			aset_penggunaan_det.asetGunaDetBrgId,
			aset_penggunaan_det.asetGunaDetKet,
			aset_penggunaan_det.asetGunaDetKodeBrg,
			aset_penggunaan_det.asetGunaDetUnitId,
			aset_penggunaan_det.asetGunaDetKmblKond,
			aset_penggunaan_det.asetGunaDetKmblKet,
			inventarisasi_detail.invDetMstLabel AS barang_nama,
			inventarisasi_detail.invDetMerek AS barang_merk,
			DATE_FORMAT(inventarisasi_detail.invDetTglPembelian,'%d-%m-%Y') AS barang_tanggal,
			inventarisasi_detail.invDetNilaiPerolehanSatuan AS barang_nilai_perolehan,
			unitkerjaId AS unit_id,
			unitkerjaNama AS unit_nama,
			");
		$this->aset->join('inventarisasi_detail','inventarisasi_detail.invDetId=aset_penggunaan_det.asetGunaDetBrgId','left');
		$this->aset->join("unit_kerja_ref","aset_penggunaan_det.asetGunaDetUnitId = unit_kerja_ref.unitkerjaId","left");
		$this->aset->where('aset_penggunaan_det.asetGunaDetMstId',$id);
		return $this->aset->get('aset_penggunaan_det')->result_array();
	}

   public function insert_data_id($tabel,$data) {
        $this->aset->insert($tabel, $data);
        $rs = $this->aset->insert_id();
        return $rs;
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

	public function delete_data($tabel,$kunci_primer,$id) 
	{
		$this->aset->where($kunci_primer, $id);
		return $this->aset->delete($tabel);
	}

}
/*
noref
tgl
fileSip[]
stat
ket

pnama
pnip
pjab
punit

nama
nip
jab
telp
unit

label_id[]
label_kode[]
label_nama[]
merk[]
spec[]
tgl_beli[]
unit[]
nilOleh[]
*/	