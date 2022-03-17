<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model mutasi (bhp) bahan habis pakai
 * @created : 2022-01-11 09:00:00
 * @author  : sriharyo <srihary@ugm.ac.id>
 * @company : DSDI UGM
*/
class Model_mutasi_bhp extends CI_Model 
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

		$sqlUser = "
		SELECT a.UserId AS id_user 
		FROM  ugmfw_mapping a 
		WHERE a.user_username = '".$this->username."'
		";
		$dataUser= $this->aset->query($sqlUser)->row_array();

		$sqlUnit 				= "
		SELECT uk.unitkerjaKodeSistem  
		FROM unit_kerja_ref AS uk WHERE
		uk.unitkerjaKode = '$this->kode_unit'";

		$dataUnit				= $this->aset->query($sqlUnit)->row_array();

		$data['user_id'] 		= $dataUser['id_user'];
		$data['unit_system'] 	= $dataUnit['unitkerjaKodeSistem'];

		return $data;
	}

	public function get_join($value='')
	{
		$this->aset->join('bhp_mutasi_det','bhpMutasiDetMstId = bhpMutasiMstId','left');
		$this->aset->join('unit_kerja_ref u1','u1.unitkerjaId = bhpMutasiMstUnitIdAsal','left');
		$this->aset->join('unit_kerja_ref u2','u2.unitkerjaId = bhpMutasiMstUnitIdTuj','left');
		$this->aset->join('ruang r1','r1.ruangId = bhpMutasiMstGudangIdAsal','left');
		$this->aset->join('ruang r2','r2.ruangId = bhpMutasiMstGudangIdTuj','left');
		$this->aset->join('inv_atk_jenis_ref','invAtkJenisRefId = bhpMutasiDetInvAtkId','left');
	}
	public function get_where($params='')
	{
		$this->aset->like('bhpMutasiMstBA',$params['no_ba']);
		//$this->aset->like('invAtkNama',$params['nama']);
		$this->aset->where('(r1.ruangNama like "'.$params['nama_asal'].'" OR u1.unitkerjaNama LIKE "'.$params['nama_asal'].'")');
		$this->aset->where('(r2.ruangNama like "'.$params['nama_penerima'].'" OR u2.unitkerjaNama LIKE "'.$params['nama_penerima'].'")');

		/* 
		$this->aset->where('bidangbrgId'	,$params['id_bidang']);
		$this->aset->where('kelbrgId' 		,$params['id_kelompok']);
		$this->aset->where('subkelbrgId'	,$params['id_sub_kelompok']);
		$this->aset->where('golbrgId'		,$params['id_sub_kelompok']); 
		*/
	}
	public function get_data($limit, $offset, $search, $order,  $params)
	{
		$this->aset->select("
		
		bhpMutasiMstId,
		bhpMutasiMstTglMutasi AS tgl,
		bhpMutasiMstBA AS notrans,
	   	CONCAT(r1.ruangNama,' (',r1.ruangKode,')') AS gdgasal,
		u1.unitkerjaNama AS unitasal,
		CONCAT(r2.ruangNama,' (',r1.ruangKode,')') AS gdgtujuan,
	   	u2.unitkerjaNama AS unittujuan,
		invAtkNama AS namabarang,
		SUM(bhpMutasiDetJmlBhp) AS jumlah,
		bhpMutasiMstPIC AS pic,
	  	IF('%s' = u2.unitkerjaKodeSistem,1,0) AS penerima
			");
		$this->get_join();
		$this->aset->order_by($order);
		// $this->get_where($params);
		$this->aset->limit($limit, $offset);
		$query = $this->aset->get('bhp_mutasi_mst');
		$rs = array();
		if ($query->num_rows() > 0) {
			$rs = $query->result_array();
		  }
		  return $rs;
	}

	public function get_num_data($params,$search)
	{
		//print_r("asdasdasd");exit();
		$this->aset->select('*');
		$this->get_join();
		$this->get_where($params,$search);
		$query = $this->aset->get('bhp_mutasi_mst')->num_rows();
		$rs = 10;
		
		if ($query > 0) {
			$rs = $query->row_array();
		  }
		   return $rs;
		
	}

	public function get_detail($encId)
	{
		$this->aset->select("
		bhpMutasiMstBA AS noBA,
   		bhpMutasiMstTglMutasi AS tglMutasi,
   		u1.unitkerjaNama AS unitAsal,
   		r1.ruangNama AS gudangAsal,
   		u2.unitkerjaNama AS unitTujuan,
   		r2.ruangNama AS gudangTujuan,
   		bhpMutasiMstPIC AS pic,
   		bhpMutasiMstKet AS keterangan,
		CONCAT(
			MID(invAtkJenisBarangRefId, 1, 1), '.', MID(invAtkJenisBarangRefId, 2, 2), '.',
			MID(invAtkJenisBarangRefId, 4, 2), '.', MID(invAtkJenisBarangRefId, 6, 2), '.', 
			MID(invAtkJenisBarangRefId, 8, 3),'.',invAtkKode
		) AS kdBrg,
		invAtkNama AS namaBrg,
		bhpMutasiDetJmlBhp AS jumlah
	   ");

	   $this->aset->join('bhp_mutasi_det', 'bhpMutasiDetMstId = bhpMutasiMstId');
	   $this->aset->join('ruang r1', 'r1.ruangId = bhpMutasiMstGudangIdAsal');
	   $this->aset->join('unit_kerja_ref u1', 'u1.unitkerjaId = bhpMutasiMstUnitIdAsal');
	   $this->aset->join('ruang r2', 'r2.ruangId = bhpMutasiMstGudangIdTuj');
	   $this->aset->join('unit_kerja_ref u2', 'u2.unitkerjaId = bhpMutasiMstUnitIdTuj');
	   $this->aset->join('inv_atk_jenis_ref', 'invAtkJenisRefId = bhpMutasiDetInvAtkId');

	   $this->aset->where('bhpMutasiMstId',$encId);
	   return $this->aset->get('bhp_mutasi_mst')->result_array();
	}
}