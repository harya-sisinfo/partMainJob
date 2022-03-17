<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model register transaksi harian
 * @created : 2022-01-11 09:00:00
 * @author  : sriharyo <srihary@ugm.ac.id>
 * @company : DSDI UGM
*/
class Model_register_transaksi_harian extends CI_Model 
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
		$this->aset->join('aset_transaksi_atk_detil','transAtkDetTransMstId = transAtkMstId',"left");
		$this->aset->join('inv_atk_det','invAtkDetId = transAtkDetAtkId','left');
		$this->aset->join('inv_atk_brg','invDet = invAtkDetId','left');
		$this->aset->join('inv_atk_gudang','invAtkGudangInvDtlbrgId = invBrgAtkId','left');
		$this->aset->join('ruang','ruangId = invAtkGudangRuangId','left');
		$this->aset->join('unit_kerja_ref','unitkerjaId = transAtkMstUnitKerjaId','left');
	}
	public function get_where($params='',$search='')
	{
		$unit_sys = $this->userAsetSession()['unit_system'];

		$this->aset->where("ruangId != '2'");
		$this->aset->where("(unitkerjaKodeSistem = '".$unit_sys."' OR unitkerjaKodeSistem LIKE '%".$unit_sys."%')");
		if(!empty($params['notransaksi'])){
			$this->aset->like('transAtkMstNomor',$params['notransaksi']);
		}
		if(!empty($params['nama'])){
			$this->aset->like('transAtkNamaPenerima ',$params['nama']);
		}
		if(!empty($params['tanggal_mulai']) || !empty($params['tanggal_selesai'])){
			$tanggal_mulai 		= empty($params['tanggal_mulai'])?date('Y-m-d',strtotime(str_replace('/', '-',substr($params['tanggal_mulai'],0,10)))):date('Y-m-d',strtotime(str_replace('/', '-',substr($params['tanggal_mulai'],0,10))));
			$tanggal_selesai 	= empty($params['tanggal_selesai'])?date('Y-m-d',strtotime(str_replace('/', '-',substr($params['tanggal_selesai'],0,10)))):date('Y-m-d',strtotime(str_replace('/', '-',substr($params['tanggal_selesai'],0,10))));
			$this->aset->where("transAtkMstTglEntry BETWEEN '".$tanggal_mulai."' AND '".$tanggal_selesai."' ");
		}

		$this->aset->group_by('transAtkMstId');
		
	}
	public function get_data($limit, $offset, $search, $order,  $params)
	{
		$this->aset->select('
			transAtkMstId        		AS trans_id,
			transAtkMstTglEntry   		AS trans_tanggal,
			transAtkMstNomor      		AS trans_nomor,
			TRIM(transAtkMstKeterangan) AS trans_keterangan,
			TRIM(transAtkNamaPenerima)  AS trans_nama,
			TRIM(unitkerjaNama)   		AS trans_unit
		');
		$this->get_join();
		$this->get_where($params, $search);
		$this->aset->limit($limit, $offset);
		$this->aset->order_by($order);
		$query = $this->aset->get('aset_transaksi_atk');
		$rs = array();
		if ($query->num_rows() > 0) 
		{
			$rs = $query->result_array();
		}
		return $rs;
	}
	public function get_num_data($params,$search)
	{
		$this->aset->select('transAtkMstId AS trans_id');
		$this->get_join();
		$this->get_where($params,$search);
		$query = $this->aset->get('aset_transaksi_atk')->num_rows();
		$rs = 0;
		
		if ($query > 0) {
			$rs = $query;
		  }
		   return $rs;
		
	}
	public function get_unit_pj()
	{
		$unit_sys = $this->userAsetSession()['unit_system'];

		$this->aset->select(
			"LPAD((IF(tempUnitKode IS NULL,a.unitkerjaKode,tempUnitKode))*100,6,'0') AS kodesatker,
			(IF(tempUnitNama IS NULL,a.unitkerjaNama,tempUnitNama)) AS satker,
			(IF(tempUnitId IS NULL,a.unitkerjaId,a.unitkerjaId)) AS id,
			a.unitkerjaKode AS kodeunit,
			(IF(tempUnitNama IS NULL,a.unitkerjaNama,a.unitkerjaNama)) AS unit,
			a.unitkerjaParentId AS parentId,
			(SELECT COUNT(b.unitkerjaId) FROM unit_kerja_ref b WHERE b.unitkerjaParentId = a.unitkerjaId) AS isParent"
		);

		$this->aset->join("
		(
			SELECT 
				unitkerjaId AS tempUnitId,
				unitkerjaKode AS tempUnitKode,
				unitkerjaNama AS tempUnitNama,
				unitkerjaParentId AS tempParentId
			FROM unit_kerja_ref 
			WHERE unitkerjaParentId = 0
			) tmpUnitKerja
		","a.unitkerjaParentId=tmpUnitKerja.tempUnitId","left");

		$this->aset->join("gedung","gedungUnitkerjaId = unitkerjaId","left");
		
		$this->aset->where("(a.unitkerjaKodeSistem LIKE '%".$unit_sys."%')");
		$this->aset->group_by("a.unitkerjaId");
		$this->aset->group_by("a.unitkerjaKodeSistem");
		

		$query = $this->aset->get('unit_kerja_ref a');
		$rs = array();
		if ($query->num_rows() > 0) 
		{
			$rs = $query->result_array();
		}
		return $rs;
	}
	// ===========================================================================
	// ** List Barang
	public function get_join_barang($params = [])
	{
		$this->aset->join("bidang_barang_ref", "(bidangbrgGolbrgId = golbrgId)");
		$this->aset->join("kelompok_barang_ref", "(kelbrgBidangbrgId = bidangbrgId)");
		$this->aset->join("sub_kelompok_barang_ref", "(subkelbrgKelbrgId = kelbrgId)");
		$this->aset->join("barang_ref", "(barangSubkelbrgId = subkelbrgId)");
		$this->aset->join("inv_atk_jenis_ref", "(invAtkJenisBarangRefId = barangId)");
		$this->aset->join("inv_atk_master", "invAtkMstJenisPersediaanId = invAtkJenisRefId", "left");
		$this->aset->join("inv_atk_det", "invAtkDetMstId = invAtkMstId", "left");
		$this->aset->join("inv_atk_brg", "invAtkDetId = invDet", "left");
		$this->aset->join("inv_atk_gudang", "invAtkGudangInvDtlbrgId = invBrgAtkId", "left");
		$this->aset->join("ruang", "ruangId = invAtkGudangRuangId", "left");
		$this->aset->join("unit_kerja_ref", "unitkerjaId = ruangUnitId", "left");


		$this->aset->where("invAtkGudangJumlah != '' ");

		if (!empty($params['id_bidang'])) {
			$this->aset->where("golbrgId", $params['id_bidang']);
		}
		if (!empty($params['id_kelompok'])) {
			$this->aset->where("kelbrgId", $params['id_kelompok']);
		}
		if (!empty($params['id_sub_kelompok'])) {
			$this->aset->where("subkelbrgId", $params['id_sub_kelompok']);
		}
		
		if (!empty($params['nama_barang'])) {
			$this->aset->where("
				CONCAT(
					LPAD(golbrgKode,1,0),'.',
					LPAD(bidangbrgKode,2,0),'.',
					LPAD(kelbrgKode,2,0),'.', 
					LPAD(subkelbrgKode,2,0),'.',
					LPAD(barangKode,3,0),'.',
					invAtkKode) LIKE '%" . $params['nama_barang'] . "%' 
				OR invAtkNama LIKE '%" . $params['nama_barang'] . "%')");
		}
		$this->aset->order_by('golbrgId,kelbrgId,subkelbrgId,barangId,invAtkJenisRefId');
	}
	public function get_barang($limit, $offset,  $params)
	{
		$this->aset->select("
			kelbrgId 			AS idKelompok,
			CONCAT(
				LPAD(golbrgKode,1,0),' . ',
				LPAD(bidangbrgKode,2,0),' . ',
				LPAD(kelbrgKode,2,0)
			) AS kode_kelompok,
			kelbrgNama AS nama_kelompok,

			subkelbrgId 		AS idSubKelompok,
			CONCAT(
				LPAD(golbrgKode,1,0),' . ',
				LPAD(bidangbrgKode,2,0),' . ',
				LPAD(kelbrgKode,2,0),' . ',
				LPAD(subkelbrgKode,2,0)
			) AS kode_sub_kelompok,
			subkelbrgKode AS nama_sub_kelompok,
			barangId 			AS idBarang,
			CONCAT(
				LPAD(golbrgKode,1,0),'.',
				LPAD(bidangbrgKode,2,0),'.',
				LPAD(kelbrgKode,2,0),'.', 
				LPAD(subkelbrgKode,2,0),'.',
				LPAD(barangKode,3,0)
				) 				AS kode_barang,
			barangNama			AS nama_barang,
			invAtkJenisRefId  	AS id,
			MAX(invAtkDetId)   	AS barang_id,
			CONCAT(
				LPAD(golbrgKode,1,0),'.',
				LPAD(bidangbrgKode,2,0),'.',
				LPAD(kelbrgKode,2,0),'.', 
				LPAD(subkelbrgKode,2,0),'.',
				LPAD(barangKode,3,0),'.',
				invAtkKode) 
								AS kode,
			invAtkNama          AS nama,
			invAtkDetMerk       AS barang_merk,
			SUM(
				invAtkGudangJumlah
				) 				AS stok,
			invAtkGudangRuangId AS ruang_id
		");
		$this->get_join_barang($params);
		$this->aset->limit($limit, $offset);

		$data_barang = $this->aset->get('golongan_barang_ref')->result_array();
		if (!empty($data_barang)) {
			$key_ = 1;
			foreach ($data_barang as $key => $row) {
				$barang['id'][$key_] 				= $row['id'];
				$barang['kode'][$key_] 				= $row['kode'];
				$barang['nama'][$key_] 				= $row['nama'];
				$barang['barang_merk'][$key_]		= $row['barang_merk'];
				$barang['stok'][$key_] 				= $row['stok'];
				$barang['ruang_id'][$key_] 			= $row['ruang_id'];

				$barang['id_kelompok'][$key_] 		= $row['idKelompok'];
				$barang['kode_kelompok'][$key_] 		= $row['kode_kelompok'];
				$barang['nama_kelompok'][$key_] 		= $row['nama_kelompok'];

				$barang['id_sub_kelompok'][$key_] 	= $row['idSubKelompok'];
				$barang['kode_sub_kelompok'][$key_] 	= $row['kode_sub_kelompok'];
				$barang['nama_sub_kelompok'][$key_] 	= $row['nama_sub_kelompok'];

				$barang['id_barang'][$key_] 			= $row['idBarang'];
				$barang['kode_barang'][$key_] 		= $row['kode_barang'];
				$barang['nama_barang'][$key_] 		= $row['nama_barang'];
				$key_++;
			}
			// print_r("sadasdasd");print_r($key_);
			// exit();
			
			$i = 1;
			for ($x = 1; $x < $key_; $x++) {
				if ($x == 1) {
					$barang_[$i]['id']			= $barang['id_kelompok'][$x];
					$barang_[$i]['kode']		= $barang['kode_kelompok'][$x];
					$barang_[$i]['nama']		= $barang['nama_kelompok'][$x];
					$barang_[$i]['barang_merk']	= '';
					$barang_[$i]['stok']		= '';
					$barang_[$i]['ruang_id']	= '';
					$barang_[$i]['aksi']		= 'hide';
					$i++;
					$barang_[$i]['id']			= $barang['id_sub_kelompok'][$x];
					$barang_[$i]['kode']		= $barang['kode_sub_kelompok'][$x];
					$barang_[$i]['nama']		= $barang['nama_sub_kelompok'][$x];
					$barang_[$i]['barang_merk']	= '';
					$barang_[$i]['stok']		= '';
					$barang_[$i]['ruang_id']	= '';
					$barang_[$i]['aksi']		= 'hide';
					$i++;
					$barang_[$i]['id']			= $barang['id_barang'][$x];
					$barang_[$i]['kode']		= $barang['kode_barang'][$x];
					$barang_[$i]['nama']		= $barang['nama_barang'][$x];
					$barang_[$i]['barang_merk']	= '';
					$barang_[$i]['stok']		= '';
					$barang_[$i]['ruang_id']	= '';
					$barang_[$i]['aksi']		= 'hide';
					$i++;
					$barang_[$i]['id']			= $barang['id'][$x];
					$barang_[$i]['kode']		= $barang['kode'][$x];
					$barang_[$i]['nama']		= $barang['nama'][$x];
					$barang_[$i]['barang_merk']	= $barang['barang_merk'][$x];
					$barang_[$i]['stok']		= $barang['stok'][$x];
					$barang_[$i]['ruang_id']	= $barang['ruang_id'][$x];
					$barang_[$i]['aksi']		= 'show';
					$i++;
				} else {
					if ($barang['id_kelompok'][$x] != $barang['id_kelompok'][$x - 1]) {
						$barang_[$i]['id']		= $barang['id_kelompok'][$x];
						$barang_[$i]['kode']	= $barang['kode_kelompok'][$x];
						$barang_[$i]['nama']	= $barang['nama_kelompok'][$x];
						$barang_[$i]['barang_merk']	= '';
						$barang_[$i]['stok']		= '';
						$barang_[$i]['ruang_id']	= '';
						$barang_[$i]['aksi']	= 'hide';
						$i++;
					} else {
						if ($barang['id_sub_kelompok'][$x] != $barang['id_sub_kelompok'][$x - 1]) {
							$barang_[$i]['id']		= $barang['id_sub_kelompok'][$x];
							$barang_[$i]['kode']	= $barang['kode_sub_kelompok'][$x];
							$barang_[$i]['nama']	= $barang['nama_sub_kelompok'][$x];
							$barang_[$i]['barang_merk']	= '';
							$barang_[$i]['stok']		= '';
							$barang_[$i]['ruang_id']	= '';
							$barang_[$i]['aksi']		= 'hide';
							$i++;
						} else {
							if($barang['id_barang'][$x] != $barang['id_barang'][$x - 1]){
								$barang_[$i]['id']			= $barang['id_barang'][$x];
								$barang_[$i]['kode']		= $barang['kode_barang'][$x];
								$barang_[$i]['nama']		= $barang['nama_barang'][$x];
								$barang_[$i]['barang_merk']	= '';
								$barang_[$i]['stok']		= '';
								$barang_[$i]['ruang_id']	= '';
								$barang_[$i]['aksi']		= 'hide';
								$i++;
							}else{
								$barang_[$i]['id']			= $barang['id'][$x];
								$barang_[$i]['kode']		= $barang['kode'][$x];
								$barang_[$i]['nama']		= $barang['nama'][$x];
								$barang_[$i]['barang_merk']	= $barang['barang_merk'][$x];
								$barang_[$i]['stok']		= $barang['stok'][$x];
								$barang_[$i]['ruang_id']	= $barang['ruang_id'][$x];
								$barang_[$i]['aksi']		= 'show';
								$i++;
							}
						}
					}
				} 
			}
			
			for ($y = 1; $y < $i; $y++) {
				$data[] = [
					'id' 			=> $barang_[$y]['id'],
					'kode' 			=> $barang_[$y]['kode'],
					'nama' 			=> $barang_[$y]['nama'],
					'barang_merk' 	=> $barang_[$y]['barang_merk'],
					'stok' 			=> $barang_[$y]['stok'],
					'ruang_id' 		=> $barang_[$y]['ruang_id'],
					'aksi' 			=> $barang_[$y]['aksi']
				];
			}
		
		} else {
			$data = [];
		}

		return $data;
	}
	public function get_num_barang($params)
	{
		$this->aset->select("barangId");
		$this->get_join_barang($params);
		$query = $this->aset->get('golongan_barang_ref')->num_rows();
		$rs = 10;

		if ($query > 0) {
			$rs = $query;
		}

		return $rs;
	}
	public function get_detail($id)
	{
		$this->aset->select("
			transAtkMstId              	AS trans_id,
			transAtkMstTglEntry        	AS trans_tanggal,
			transAtkMstNomor           	AS trans_nomor,
			transAtkMstKeterangan      	AS trans_keterangan,
			transAtkNamaPenerima       	AS trans_nama,
			transAtkDetAtkId           	AS barangId,
			CONCAT(
				LPAD(golbrgKode, 1, '0'), '.',
				LPAD(bidangbrgKode, 2, '0'), '.', 
				LPAD(kelbrgKode, 2, '0'), '.', 
				LPAD(subkelbrgKode, 2, '0'), '.', 
				LPAD(barangKode, 3, '0'),'.',
				invAtkKode) 			AS barangKode,
			invAtkNama                 	AS barangNama,
			transAtkDetKeteranganDetil 	AS barangDiskripsi,
			transAtkDetJmlAtk          	AS jumlah,
			transAtkDetNilaiSatuan     	AS lelangBiaya,
			(SELECT
				lelangBiaya*jumlah) 	AS total,
			unitkerjaNama              	AS nama_unit
		");
		$this->aset->join('aset_transaksi_atk_detil', 'transAtkDetTransMstId = transAtkMstId', 'left');
		$this->aset->join('inv_atk_det', 'invAtkDetId = transAtkDetAtkId', 'left');
		$this->aset->join('inv_atk_master', 'invAtkMstId = invAtkDetMstId', 'left');
		$this->aset->join('inv_atk_jenis_ref', 'invAtkMstJenisPersediaanId = invAtkJenisRefId', 'left');
		$this->aset->join('barang_ref', 'invAtkJenisBarangRefId = barangId', 'left');
		$this->aset->join('sub_kelompok_barang_ref', 'subkelbrgId = barangSubkelbrgId', 'left');
		$this->aset->join('kelompok_barang_ref', 'kelbrgId = subkelbrgKelbrgId', 'left');
		$this->aset->join('bidang_barang_ref', 'bidangbrgId = kelbrgBidangbrgId', 'left');
		$this->aset->join('golongan_barang_ref', 'golbrgId = bidangbrgGolbrgId', 'left');
		$this->aset->join('unit_kerja_ref', 'unitkerjaId = transAtkMstUnitKerjaId', 'left');

		$this->aset->where("transAtkMstId", $id);

		return $this->aset->get('aset_transaksi_atk')->row_array();
	}
}