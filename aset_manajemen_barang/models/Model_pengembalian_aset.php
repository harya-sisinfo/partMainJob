<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model pengembalian aset
 * @created : 2021-07-29 10:05:00
 * @author  : sriharyo <srihary@ugm.ac.id>
 * @company : DSDI UGM
*/
class Model_pengembalian_aset extends CI_Model 
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
		SELECT 
			a.UserId AS id_user ,
			ugmfw_user.user_nama_lengkap AS nama_user
		FROM  ugmfw_mapping a 
			LEFT JOIN simkeu_2013.ugmfw_user ugmfw_user
				ON a.user_username = ugmfw_user.user_username
		WHERE a.user_username = '".$this->username."'
		";
		$dataUser= $this->aset->query($sqlUser)->row_array();

		$sqlUnit 				= "
		SELECT uk.unitkerjaKodeSistem  
		FROM unit_kerja_ref AS uk WHERE
		uk.unitkerjaKode = '$this->kode_unit'";

		$dataUnit				= $this->aset->query($sqlUnit)->row_array();

		$data['user_id'] 		= $dataUser['id_user'];
		$data['user_nama'] 		= $dataUser['nama_user'];
		$data['unit_system'] 	= $dataUnit['unitkerjaKodeSistem'];

		return $data;
	}
	public function get_select()
	{
		$this->aset->select("
			SQL_CALC_FOUND_ROWS
			sewaId             AS sewa_id,
			sewaNoSewa         AS sewa_nomor,
			sewaTanggalMulai   AS sewa_tanggal_awal,
			sewaJamMulai       AS sewa_waktu_awal,
			sewaTanggalSelesai AS sewa_tanggal_akhir,
			sewaJamSelesai     AS sewa_waktu_akhir,
			sewaKeterangan     AS keterangan,
			a.unitkerjaNama      AS unitkerja,
			mitraNama          AS mitra,
			IF((CONCAT(sewaTanggalSelesai,' ',sewaJamSelesai)) < NOW(), 1, 0) AS status_isi,
			pengembSewaId 		AS pengembalian,
			IF(pengembSewaId IS NULL,'Belum','Sudah') AS status_pengembalian
			", false);
	}

	public function get_join(){
		$this->aset->join("aset_sewa_det", "asetSewaDetSewaid = sewaId","left");
		$this->aset->join("aset_ref_biaya_sewa", "invBiayaSewaId = asetSewaDetBiayaSewaId","left");
		$this->aset->join("inventarisasi_detail", "invDetId = invBiayaSewaInvId","left");
		$this->aset->join("unit_kerja_ref b", "b.unitkerjaId = invDetUnitKerja","left");
		$this->aset->join("unit_kerja_ref a", "a.unitkerjaId = sewaUnitId","left");

		$this->aset->join("mitra_ref", "mitraId = sewaMitraId","left");
		$this->aset->join("aset_sewa_pengembalian", "pengembSewaSewaId = sewaId","left");
	}

	public function get_where($search=''){
		$dataUnit = $this->userAsetSession();
		if(!empty($search)){
			$this->aset->where("
				(invDetMstLabel LIKE '%$search%' OR invDetKodeBarang LIKE '%$search%')");
		}		
		$this->aset->where('(b.unitkerjaKodeSistem LIKE "'.$dataUnit['unit_system'].'%" OR b.unitkerjaKodeSistem ="'.$dataUnit['unit_system'].'")');

	}

	public function get_data($limit, $offset, $search, $order){
		$this->get_select();
		$this->get_join();
		$this->get_where($search);
		$this->aset->order_by($order);
		$this->aset->group_by('sewaId');
		$this->aset->limit($limit, $offset);
		return $this->aset->get('aset_sewa')->result_array();
	}

	public function get_num_data($search=''){
		$this->aset->select('COUNT(*) AS jumlah');
		$this->get_join();
		$this->aset->group_by('sewaId');
		$this->get_where($search);
		return $this->aset->get('aset_sewa')->row_array();
	}


	public function get_data_pengembalian_aset_by_id($sewa_id)
	{
		$this->aset->select("
			sewaId AS sewa_id,
			asetSewaDetSewaid AS sewa_detail_id,
			sewaKeterangan AS keterangan,
			invBiayaSewaId AS asetId,
			invBiayaSewaKode AS asetKode,
			invDetMstLabel AS asetLabel,
			invBiayaSewaRusak AS rusak,
			invBiayaSewaGanti AS ganti,
			sewaTanggalSelesai AS sewa_tanggal_akhir,
			'' AS subKeterangan,
			'' AS sub_keterangan,
			invDetId AS statusId
			");
		$this->aset->join('aset_sewa_det','asetSewaDetSewaid = sewaId','left');
		$this->aset->join('aset_ref_biaya_sewa','invBiayaSewaId = asetSewaDetBiayaSewaId','left');
		$this->aset->join('inventarisasi_detail','invDetId = invBiayaSewaInvId','left');
		$this->aset->where('sewaId',$sewa_id);
		return $this->aset->get('aset_sewa')->result_array();
	}

	public function get_pengembalian($id_sewa)
	{
		$this->aset->select(
			"
			kembali.pengembSewaNomor						AS kembali_nomor,
			kembali.pengembSewaTglPengembalian  			AS kembali_tanggal,
			kembali.pengembSewaKeterangan 					AS kembali_keterangan,
			invBiayaSewaKode           						AS kembali_detail_kode,
			invDetMstLabel                					AS kembali_detail_barang,
			ROUND(kembali_detail.pengembSewaDetBiayaRusak) 	AS kembali_detail_rusak,
			ROUND(kembali_detail.pengembSewaDetBiayaGanti) 	AS kembali_detail_ganti,
			kembali_detail.pengembSewaDetKeterangan 		AS kembali_detail_keterangan,
			ugmfw_user.user_nama_lengkap 					AS kembali_nama
			"
		);
		$this->aset->join('aset_sewa_pengembalian kembali','kembali_detail.pengembSewaDetMstId = kembali.pengembSewaId','left');
		$this->aset->join('ugmfw_mapping','kembali.pengembSewaUserId = ugmfw_mapping.UserId','left');
		$this->aset->join($this->db->database.'.ugmfw_user ugmfw_user','ugmfw_user.user_username = ugmfw_mapping.user_username','left');
		$this->aset->join('aset_ref_biaya_sewa','aset_ref_biaya_sewa.invBiayaSewaId = kembali_detail.pengembSewaDetBiayaSewaId','left');
		$this->aset->join('inventarisasi_detail','inventarisasi_detail.invDetId = aset_ref_biaya_sewa.invBiayaSewaInvId','left');
		$this->aset->where('kembali.pengembSewaSewaId',$id_sewa);
		$this->aset->group_by('kembali_detail.pengembSewaDetId');
		return $this->aset->get('aset_sewa_pengembalian_detil kembali_detail')->result_array();
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
	public function delete_data($tabel,$kunci_primer,$id) {
		$this->aset->where($kunci_primer, $id);
		return $this->aset->delete($tabel);
	}
}