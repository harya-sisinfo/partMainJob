<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model sewa aset
 * @created : 2021-07-29 10:05:00
 * @author  : sriharyo <srihary@ugm.ac.id>
 * @company : DSDI UGM
*/
class Model_sewa_aset extends CI_Model 
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

	public function get_select(){
		$this->aset->select("
			SQL_CALC_FOUND_ROWS
			sewaId 				AS sewa_id,
			sewaNoSewa 			AS sewa_nomor,
			sewaTanggalMulai 	AS sewa_tanggal_awal,
			sewaJamMulai 		AS sewa_waktu_awal,
			sewaTanggalSelesai 	AS sewa_tanggal_akhir,
			sewaJamSelesai 		AS sewa_waktu_akhir,
			sewaKeterangan 		AS sewa_keterangan,
			sewaJumlahHari 		AS sewa_jumlah_hari,
			sewaNama 			AS sewa_penyewa,
			sewaAlamatPenyewa 	AS sewa_alamat,
			sewaNomorContact 	AS sewa_telp,
			uk.unitkerjaNama 	AS unitkerja,
			uk.unitkerjaId 		AS unitkerjaid,
			mitraNama 			AS mitra,
			IF(
				(
					CONCAT
						(
							sewaTanggalSelesai,' ',sewaJamSelesai
						)
				) < NOW()
				, 1
				, 0)  			AS status_isi,
			IFNULL(
				pengembSewaId,0
				) 				AS pengembalian,
			mitraId 			AS mkId,
			mitraNama 			AS mkNama,
			mitraAlamat			AS mkAlamat,
			mitraTelp			AS mkTelepon,

			", false);
	}

	public function get_join(){
		$this->aset->join("aset_sewa_det", "asetSewaDetSewaid = sewaId", "left");
		$this->aset->join("aset_ref_biaya_sewa", "invBiayaSewaId = asetSewaDetBiayaSewaId", "left");
		$this->aset->join("inventarisasi_detail", "invDetId = invBiayaSewaInvId", "left");
		$this->aset->join("unit_kerja_ref uk", "uk.unitkerjaId = sewaUnitId" , "left");
		$this->aset->join("mitra_ref", "mitraId = sewaMitraId", "left");
		$this->aset->join("aset_sewa_pengembalian", "pengembSewaSewaId = sewaId", "left");
		$this->aset->join("unit_kerja_ref u", "u.unitkerjaId = invDetUnitKerja", "left");
	}

	public function get_where($params,$search){
		if(!empty($search)){
			$this->aset->where("(sewaNoSewa LIKE '%".$search."%' OR sewaKeterangan LIKE '%".$search."%')");
		}
		$kode_sys_unit = $this->userAsetSession();
		$this->aset->where('u.unitkerjaKodeSistem = "'.$kode_sys_unit['unit_system'].'" OR u.unitkerjaKodeSistem LIKE "'.$kode_sys_unit['unit_system'].'%"');
		
		//$this->aset->where("pengembSewaId IS NULL");

		if(!empty($params['kode_sewa'])){
			$this->aset->where("sewaNoSewa",$params['kode_sewa']);
		}
		if(!empty($params['status_isi'])){
			$this->aset->where("IF((CONCAT(sewaTanggalSelesai,' ',sewaJamSelesai)) < NOW(), 1, 0)",$params['status_isi']);
		}
	}

	public function get_data($limit, $offset, $search, $order,  $params){
		$this->get_select();
		$this->get_join();
		$this->get_where($params,$search);
		$this->aset->order_by($order);
		$this->aset->order_by('status_isi','DESC');
		$this->aset->group_by('sewaId');
		$this->aset->limit($limit, $offset);
		return $this->aset->get('aset_sewa')->result_array();
	}

	public function get_num_data($search='',$params){
		$this->aset->select('COUNT(*) AS jumlah');
		$this->get_join();
		$this->get_where($search,$params);
		$this->aset->group_by('sewaId');
		return $this->aset->get('aset_sewa')->row_array();
	}

	public function get_sewa_byid($id)
	{
		$this->get_select();
		$this->get_join();
		$this->aset->where("asetSewaDetSewaid",$id);
		return $this->aset->get('aset_sewa')->row_array();
	}

	public function get_sewa_detail($id='')
	{
		$this->aset->select('
					asetSewaDetId,
					asetSewaDetSewaid,
					asetSewaDetBiayaSewaId,
					asetSewaDetValue,
					asetSewaDetPotongan,
					invDetMstLabel,
					invBiayaSewaId,
					invBiayaSewaKode
					');
		$this->aset->join("aset_ref_biaya_sewa", "invBiayaSewaId = asetSewaDetBiayaSewaId", "left");
		$this->aset->join("inventarisasi_detail", "invDetId = invBiayaSewaInvId", "left");
		$this->aset->where("asetSewaDetSewaid",$id);
		return $this->aset->get('aset_sewa_det')->result_array();
	}
	public function get_unit_kerja(){
		$this->aset->select('unitkerjaId AS id,
			unitkerjaKode AS kode,
			unitkerjaNama AS nama
			');
		$this->aset->where('unitkerjaActive','Y');
		
		return $this->aset->get('unit_kerja_ref')->result_array();
	}
	public function get_mitra($term=''){
		$this->aset->select('
			mitraId as mkId,
	      	mitraNama as mkNama,
	      	mitraAlamat as mkAlamat,
	      	mitraTelp as mkTelepon
			');
		if(!empty($term)){
			$this->aset->where(	'mitraNama LIKE "%'.$term.'%" 
								OR mitraAlamat LIKE "%'.$term.'%" 
								OR mitraTelp LIKE "%'.$term.'%"');
		}
		$this->aset->group_by('mitraId');
		$this->aset->order_by('mitraNama');
		$this->aset->limit(25);
		return $this->aset->get('mitra_ref')->result_array();
	}
// ==> Menampilkan aset pada modal barang 
	
	public function form_get_aset_sewa(){
		$this->aset->join('inventarisasi_detail','invDetId = invBiayaSewaInvId','left');
		$this->aset->join('unit_kerja_ref','unitkerjaId = invDetUnitKerja','left');
		if(!empty($search)){
			$this->aset->where('(invBiayaSewaKode LIKE "%'.$search.'%" 
								OR invDetMstLabel LIKE "%'.$search.'%"
								OR invBiayaSewaNilai LIKE "%'.$search.'%")
								');
		}
		$kode_sys_unit = $this->userAsetSession();
		$this->aset->where('unit_kerja_ref.unitkerjaKodeSistem = "'.$kode_sys_unit['unit_system'].'" OR unit_kerja_ref.unitkerjaKodeSistem LIKE "'.$kode_sys_unit['unit_system'].'%"');
		$this->aset->where('invDetAsetStatusId',1);
		$this->aset->order_by('invDetMstLabel','ASC');
	}
	public function get_aset_sewa($limit,$offset,$search='')
	{
		$this->aset->select('
			invBiayaSewaId AS aset_id,
	   		invBiayaSewaKode AS aset_kode,
	   		invDetMstLabel AS aset_label,
	   		ROUND(invBiayaSewaNilai) AS aset_harga_sewa,
	   		invDetId AS status_id');
		$this->form_get_aset_sewa($search);
		$this->aset->limit($limit,$offset);

		return $this->aset->get('aset_ref_biaya_sewa')->result_array();
	}
	public function num_aset_sewa($search=''){
		$this->aset->select("
			count(*) AS jumlah
			");
		$this->form_get_aset_sewa($search);
		return $this->aset->get('aset_ref_biaya_sewa')->row_array();
	}

// ==> validasi Belum dijalankan
	public function validation_date_by_id($tanggal_mulai,$tanggal_selesai,$jamMulai,$jamSelesai,$aset_id)
	{

		$this->aset->select('sewaId');

		$this->aset->join('aset_sewa_det','asetSewaDetSewaid = sewaId','left');
		$this->aset->join('aset_ref_biaya_sewa','invBiayaSewaId = asetSewaDetBiayaSewaId','left');
		$this->aset->join('inventarisasi_detail','invDetId = invBiayaSewaInvId','left');
		$this->aset->join('unit_kerja_ref','unitkerjaId = sewaUnitId','left');
		$this->aset->join('mitra_ref','mitraId = sewaMitraId','left');

		$this->aset->where('sewaTanggalMulai',$tanggal_mulai);
		$this->aset->where('sewaJamMulai',$tanggal_selesai);
		$this->aset->where('sewaTanggalSelesai',$jamMulai);
		$this->aset->where('sewaJamSelesai',$jamSelesai);
		$this->aset->where('asetSewaDetBiayaSewaId',$aset_id);

		return $this->aset->get('aset_sewa')->row_array();
	}
// ==> validasi Belum dijalankan
	public function validation_date($tanggal_mulai,$tanggal_selesai,$aset_id,$sewa_id='')
	{

		if($sewa_id!=''){
			$where_sewa = "AND sewaId != '$sewa_id'";
		}else{
			$where_sewa = '';
		}

		$queryData = "
		SELECT
			CONCAT(sewaTanggalMulai,' ',sewaJamMulai) AS mulai,
			CONCAT(sewaTanggalSelesai,' ',sewaJamSelesai) AS selesai,
			IF( 
				(	
					'$tanggal_mulai' >= CONCAT(sewaTanggalMulai,' ',sewaJamMulai)
					AND
					'$tanggal_selesai'<=CONCAT(sewaTanggalSelesai,' ',sewaJamSelesai)
					)
				OR
				(
					'$tanggal_mulai' <=CONCAT(sewaTanggalMulai,' ',sewaJamMulai)
					AND
					'$tanggal_selesai'>=CONCAT(sewaTanggalSelesai,' ',sewaJamSelesai)
					) 
				OR 
				(
					'$tanggal_mulai' <= CONCAT(sewaTanggalMulai,' ',sewaJamMulai) 
					AND 
					'$tanggal_selesai'<=CONCAT(sewaTanggalSelesai,' ',sewaJamSelesai) 
					AND '$tanggal_mulai'>=CONCAT(sewaTanggalMulai,' ',sewaJamMulai)
					) 
				OR 
				(
					'$tanggal_selesai'<=CONCAT(sewaTanggalSelesai,' ',sewaJamSelesai)
					AND 
					'$tanggal_mulai'>=CONCAT(sewaTanggalMulai,' ',sewaJamMulai)
					)
				,1,0 ) AS status_isi,

			invDetMstLabel AS label
		FROM aset_sewa
		LEFT JOIN aset_sewa_det
			ON asetSewaDetSewaid = sewaId
		LEFT JOIN aset_ref_biaya_sewa
			ON invBiayaSewaId = asetSewaDetBiayaSewaId
		LEFT JOIN inventarisasi_detail
			ON invDetId = invBiayaSewaInvId
		LEFT JOIN unit_kerja_ref
			ON unitkerjaId = sewaUnitId
		LEFT JOIN mitra_ref
			ON mitraId = sewaMitraId
		WHERE 
			asetSewaDetBiayaSewaId = '$aset_id'
			$where_sewa
		";
		$result = $this->aset->query($queryData)->row_array();
		return $result;
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
    public function delete_data($tabel,$kunci_primer,$id) {
        $this->aset->where($kunci_primer, $id);
        return $this->aset->delete($tabel);
    }
}
