<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model biaya sewa aset
 * @created : 2021-07-29 10:05:00
 * @author  : sriharyo <srihary@ugm.ac.id>
 * @company : DSDI UGM
*/
class Model_biaya_sewa_aset extends CI_Model 
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
	public function get_select()
	{
		$this->aset->select("
			SQL_CALC_FOUND_ROWS 
			invBiayaSewaId AS biaya_sewa_id,
			invBiayaSewaKode AS biaya_sewa_kode,
			invDetKodeBarang AS biaya_sewa_kode_aset,
			invDetMstLabel AS biaya_sewa_label,
			invBiayaSewaNilai AS biaya_sewa,
			invBiayaSewaRusak AS biaya_sewa_rusak,
			invBiayaSewaGanti AS biaya_sewa_denda	
			", false);
	}

	public function get_join()
	{
		$this->aset->join("inventarisasi_detail b", "b.invDetId = s.invBiayaSewaInvId");
		$this->aset->join("unit_kerja_ref uk", "b.invDetUnitKerja = uk.unitkerjaId");
	}

	public function get_where($params,$search)
	{
		if(!empty($search)){
			$this->aset->where("(invBiayaSewaKode LIKE '%$search%' OR invDetKodeBarang LIKE '%$search%' OR invDetMstLabel LIKE '%$search%')");
		}

		if(!empty($params['kode'])){
			$this->aset->like("invBiayaSewaKode",$params['kode']);
		}

		if(!empty($params['label'])){
			$this->aset->where("invDetMstLabel", $params['label']);
		}
		$unit_sys = $this->userAsetSession();
		$this->aset->where('uk.unitkerjaKodeSistem = "'.$unit_sys['unit_system'].'" OR uk.unitkerjaKodeSistem LIKE "'.$unit_sys['unit_system'].'%"');
		/*if($this->user_level==3){
			if(substr($this->kode_unit, 0, 2)=='40' || substr($this->kode_unit, 0, 2)=='01'){
				$this->aset->where('LEFT(uk.unitkerjaKode,2)',substr($this->kode_unit, 0, 2));
			}else{
				$this->aset->where('uk.unitkerjaKode',$this->kode_unit);
			}
		}elseif ($this->user_level==2) {
			$this->aset->where('LEFT(uk.unitkerjaKode,2)',substr($this->kode_unit, 0, 2));
		}*/
	}

	public function get_data($limit, $offset, $search, $order,  $params)
	{
		$this->get_select();
		$this->get_join();
		$this->get_where($params,$search);
		$this->aset->order_by('b.invDetMstLabel','ASC');
		$this->aset->limit($limit, $offset);
		return $this->aset->get('aset_ref_biaya_sewa s')->result_array();
	}

	public function get_num_data($search='',$params)
	{
		$this->aset->select('COUNT(*) AS jumlah');
		$this->get_join();
		$this->get_where($search,$params);
		return $this->aset->get('aset_ref_biaya_sewa s')->row_array();
	}

	public function get_data_by_id($id='')
	{
		$this->aset->select("
			invBiayaSewaId AS biaya_sewa_id,
			invBiayaSewaKode AS biaya_sewa_kode,
			invDetKodeBarang AS biaya_sewa_kode_aset,
			invDetMstLabel AS biaya_sewa_label,
			invBiayaSewaInvId AS biaya_sewa_label_id,
			invBiayaSewaNilai AS biaya_sewa,
			invBiayaSewaRusak AS biaya_sewa_rusak,
			invBiayaSewaGanti AS biaya_sewa_denda
			");
		$this->aset->join("inventarisasi_detail b","b.invDetId = s.invBiayaSewaInvId");
		// $this->aset->join("inventarisasi_mst mts ","b.invBiayaSewaInvId = mts.invDetId");
		$this->aset->where('invBiayaSewaId',$id);
		return $this->aset->get('aset_ref_biaya_sewa s')->row_array();
	}

	public function select_barang_inventaris()
	{
		$this->aset->select(" 
			invDetId AS label_id,
			invDetKodeBarang AS label_kode,
			invDetMstLabel AS label_nama,
			CONCAT(
			kampusNama,
			IF(
			gedungNama IS NULL,
			'',
			CONCAT(
			'<br/>&raquo;&nbsp;',
			gedungNama,
			IF(
			ruangNama IS NULL,
			'',
			CONCAT('<br/>&raquo;&nbsp;', ruangNama)
			)
			)
			)
			) AS label_lokasi,
			kepemilikanbrgNama AS label_pemilik,
			kondisibrgNama AS label_kondisi
			",FALSE);
	}

	public function join_barang_inventaris()
	{
		$this->aset->join('inventarisasi_detail b','a.invMstId = b.invDetMstId','left');
		$this->aset->join('barang_ref c','a.invBarangId = barangId','left');
		$this->aset->join('kampus_ref d','b.invDetKampus = d.kampusId','left');
		$this->aset->join('kepemilikan_barang_ref f','f.kepemilikanbrgId = b.invDetKepemilikan','left');
		$this->aset->join('kondisi_barang_ref h','h.kondisibrgId = b.invDetKondisiId','left');
		$this->aset->join('gedung','invDetGedungId = gedungId','left');
		$this->aset->join('ruang','invDetRuanganId = ruangId','left');
		$this->aset->join('unit_kerja_ref e','b.invDetUnitKerja = e.unitkerjaId','left','left');
		
		 
		$this->aset->order_by("b.invDetTglPembelian","DESC");
	}

	public function get_barang_inventaris($limit, $offset=0,$search='')
	{
		$this->select_barang_inventaris();
		$this->join_barang_inventaris();
		if(!empty($search)){
			$this->aset->where("(invDetMstLabel LIKE '%$search%' OR invDetKodeBarang  LIKE '%$search%')");
		}
		$this->aset->where("invDetKondisiId NOT IN ('3', '4')");
		$this->aset->where("invDetAsetStatusId = 1");
		$this->aset->where("invDetId NOT IN (SELECT invBiayaSewaInvId FROM aset_ref_biaya_sewa)");

		
		$unit_sys = $this->userAsetSession();
		$this->aset->where('e.unitkerjaKodeSistem = "'.$unit_sys['unit_system'].'" OR e.unitkerjaKodeSistem LIKE "'.$unit_sys['unit_system'].'%"');
		$this->aset->limit($limit,$offset);
		return $this->aset->get('inventarisasi_mst a')->result_array();
	}

	public function get_barang_inventaris_by_id($id)
	{
		$this->select_barang_inventaris();
		$this->join_barang_inventaris();
		$this->aset->where("invDetKondisiId NOT IN ('3', '4')");
		$this->aset->where("invDetAsetStatusId = 1");
		$this->aset->where("b.invDetId",$id);
		return $this->aset->get('inventarisasi_mst a')->row_array();
	}

	public function num_barang_inventaris($search='')
	{
		$this->aset->select("count(*) AS jumlah");
		$this->join_barang_inventaris($search);
		return $this->aset->get('inventarisasi_mst a')->row_array();
	}

	public function cek_sewa_detail($id)
	{
		$this->aset->select('asetSewaDetId');
		return $this->aset->get_where('aset_sewa_det',array('asetSewaDetBiayaSewaId' => $id ))->num_rows();
	}

	public function insert_data_id($tabel,$data)
	{
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