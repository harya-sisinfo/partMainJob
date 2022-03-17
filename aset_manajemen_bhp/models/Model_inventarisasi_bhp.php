<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model inventarisasi (bhp) bahan habis pakai
 * @created : 2022-01-04 09:00:00
 * @author  : sriharyo <srihary@ugm.ac.id>
 * @company : DSDI UGM
 */
class Model_inventarisasi_bhp extends CI_Model
{
	public $data = '';
	public function __construct()
	{
		parent::__construct();
		$this->aset     	= $this->load->database("aset", TRUE);
		$this->user_id      = $this->session->userdata('__user_id');
		$this->username     = $this->session->userdata('__username');
		$this->kode_unit    = $this->session->userdata('__objUser')->unit_kerja_kode;
		$this->user_level   = $this->session->userdata('__objUser')->user_level;
	}
// ===========================================================================
// ** User Session
	public function userAsetSession()
	{

		$sqlUser = "
		SELECT a.UserId AS id_user 
		FROM  ugmfw_mapping a 
		WHERE a.user_username = '" . $this->username . "'
		";
		$dataUser = $this->aset->query($sqlUser)->row_array();

		$sqlUnit 				= "
		SELECT uk.unitkerjaKodeSistem  
		FROM unit_kerja_ref AS uk WHERE
		uk.unitkerjaKode = '$this->kode_unit'";
		$dataUnit				= $this->aset->query($sqlUnit)->row_array();

		$data['user_id'] 		= $dataUser['id_user'];
		$data['unit_system'] 	= $dataUnit['unitkerjaKodeSistem'];

		return $data;
	}

// ===========================================================================
// Halaman View 
	public function get_join()
	{
		$this->aset->join('bidang_barang_ref', '(bidangbrgGolbrgId = golbrgId)');
		$this->aset->join('kelompok_barang_ref', '(kelbrgBidangbrgId = bidangbrgId)');
		$this->aset->join('sub_kelompok_barang_ref', '(subkelbrgKelbrgId = kelbrgId)');
		$this->aset->join('barang_ref', '(barangSubkelbrgId = subkelbrgId)');
		$this->aset->join('inv_atk_jenis_ref', '(invAtkJenisBarangRefId = barangId)');
		$this->aset->join('coa_ugm_ref', 'coaKode = coaUGM', 'left');
	}

	public function get_where($params = '', $search = '')
	{
		
		if (!empty($params['id_bidang'])) {
			$this->aset->where('bidangbrgId', $params['id_bidang']);
		}
		if (!empty($params['id_kelompok'])) {
			$this->aset->where('kelbrgId', $params['id_kelompok']);
		}
		if (!empty($params['id_sub_kelompok'])) {
			$this->aset->where('subkelbrgId', $params['id_sub_kelompok']);
		}
		if (!empty($params['nama_barang'])) {
			$this->aset->where("CONCAT(LPAD(golbrgKode,1,0),'.',LPAD(bidangbrgKode,2,0),'.',LPAD(kelbrgKode,2,0),'.',LPAD(subkelbrgKode,2,0),'.',LPAD(barangKode,3,0),'.',invAtkKode) LIKE '%" . $params['nama_barang'] . "%' OR invAtkNama LIKE '%" . $params['nama_barang'] . "%')");
		}
		if(!empty($search))
		{
			$this->aset->where("
			CONCAT(
				LPAD(golbrgKode,1,0),'.',
				LPAD(bidangbrgKode,2,0),'.',
				LPAD(kelbrgKode,2,0),'.',
				LPAD(subkelbrgKode,2,0),'.',
				LPAD(barangKode,3,0),'.',invAtkKode
			) LIKE '%" . $search . "%' 
			OR 
			invAtkNama LIKE '%" . $search . "%') 
			AND 
			CONCAT(coaKode, ' - ', coaNama) like '%$search%'
			
			");
		}
	}
	
	public function get_select()
	{
		$this->aset->select("
			kelbrgId AS idKelompok,
			CONCAT(
				LPAD(golbrgKode, 1, 0),' . ',
				LPAD(bidangbrgKode, 2, 0),' . ',
				LPAD(kelbrgKode, 2, 0)
				) AS kode_kelompok,
			kelbrgNama AS nama_kelompok,

			subkelbrgId AS idSubKelompok,
			CONCAT(
				LPAD(golbrgKode, 1, 0),' . ',
				LPAD(bidangbrgKode, 2, 0),' . ',
				LPAD(kelbrgKode, 2, 0),' . ',
				LPAD(subkelbrgKode, 2, 0)
				) AS kode_sub_kelompok,
			subkelbrgNama AS nama_sub_kelompok,

			barangId AS idBarang,
			CONCAT(
				LPAD(golbrgKode, 1, 0),' . ',
				LPAD(bidangbrgKode, 2, 0),' . ',
				LPAD(kelbrgKode, 2, 0),' . ',
				LPAD(subkelbrgKode, 2, 0),' . ',
				LPAD(barangKode, 3, 0)
				) AS kode_barang, 
			barangNama AS nama_barang,  
			
			invAtkJenisRefId AS id,
			CONCAT(
				LPAD(golbrgKode, 1, 0),
				' . ',
				LPAD(bidangbrgKode, 2, 0),
				' . ',
				LPAD(kelbrgKode, 2, 0),
				' . ',
				LPAD(subkelbrgKode, 2, 0),
				' . ',
				LPAD(barangKode, 3, 0),
				' . ',
				invAtkKode
			) AS kode,
			invAtkNama AS nama,
			barangSatuanbrgId AS satuan,
			CONCAT(coaKode, ' - ', coaNama) AS coa,
			invAtkAktif AS stat 
			");


		$this->aset->order_by('idKelompok,idSubKelompok,idBarang,invAtkKode');
	}

	public function get_data($limit, $offset, $search, $order,  $params)
	{
		$this->get_select();
		$this->get_join();
		$this->get_where($params, $search);
		

		$this->aset->limit($limit, $offset);
		return $this->aset->get('golongan_barang_ref')->result_array();
	}

	public function get_num_data($params = "", $search = "")
	{
		$this->aset->select('invAtkJenisRefId as id');
		$this->get_join();
		$this->get_where($params, $search);
		$query = $this->aset->get('golongan_barang_ref')->num_rows();
		$rs = 10;

		if ($query > 0) {
			$rs = $query;
		}
		return $rs;
	}

// ===========================================================================
// ** modal bidang
	public function get_join_bidang($search = '')
	{

		$this->aset->join("bidang_barang_ref AS b", "(b.bidangbrgGolbrgId = a.golbrgId)");
		$this->aset->join("
		(
			SELECT 
				kelbrgBidangbrgId AS id,
				MAX(kelbrgKode) AS last_code
			FROM
				kelompok_barang_ref
			WHERE 
				kelbrgKode 
		   	GROUP BY 
			   kelbrgBidangbrgId
		) c", "id = bidangbrgId", "left");
		if (!empty($search)) {
			$this->aset->where("
			(bidangbrgNama LIKE '%$search%' 
				OR 
			CONCAT( LPAD(golbrgKode, '1', '0'), '.', LPAD(bidangbrgKode, '2', '0') ) LIKE '%$search%'
			)");
		}
		$this->aset->group_by('golbrgNama,bidangbrgNama');
	}
	public function get_bidang($limit, $offset, $search = '')
	{
		$this->aset->select("
		golbrgId	 AS gol_id,
		golbrgKode AS gol_kode,
		golbrgNama AS gol_nama,
		bidangbrgId AS id,
		CONCAT(
			LPAD(golbrgKode, '1', '0'),
			'.',
			LPAD(bidangbrgKode, '2', '0')
		) AS kode,
		bidangbrgId AS id,
		bidangbrgNama AS nama,
		IFNULL(last_code, 0) AS last_code");
		$this->get_join_bidang($search);
		$this->aset->limit($limit, $offset);
		return $this->aset->get('golongan_barang_ref AS a')->result_array();
	}
	public function num_bidang($search = '')
	{
		$this->aset->select("COUNT(golbrgId) as jumlah");
		$this->get_join_bidang($search);
		$data['jumlah'] = $this->aset->get('golongan_barang_ref as a')->num_rows();
		return $data; 
	}
// ===========================================================================
// ** modal Kelompok
	public function get_join_kelompok($params)
	{
		$this->aset->join("bidang_barang_ref as b", "(b.bidangbrgGolbrgId = a.golbrgId)");
		$this->aset->join("kelompok_barang_ref", "(kelbrgBidangbrgId = bidangbrgId)");

		$this->aset->join("
		(
			SELECT 
				kelbrgBidangbrgId as id, 
				MAX(kelbrgKode) as last_code 
			FROM 
				kelompok_barang_ref 
			GROUP BY kelbrgBidangbrgId
		) c", "id = bidangbrgId", "left");

		if(!empty($params['id_bidang'])){
			$this->aset->where('bidangbrgId', $params['id_bidang']);
		}

		// if (!empty($params['id_sub_kelompok'])) {
		// 	$this->aset->where('bidangbrgId', $params['id_sub_kelompok']);
		// }

		if (!empty($params['search'])) {
			$this->aset->where("CONCAT(golbrgKode,'.',LPAD(bidangbrgKode,'2','0'),'.',LPAD(kelbrgKode,'2','0')) LIKE '%" . $params['search'] . "%' OR kelbrgNama LIKE '%" . $params['search'] . "%')");
		}
	}
	public function get_kelompok($limit, $offset, $params = [])
	{
		$this->aset->select("
		bidangbrgId AS idBidang,
		CONCAT(golbrgKode,'.',LPAD(bidangbrgKode,'2','0')) as kodeBidang,
		bidangbrgNama as namaBidang,
		CONCAT(golbrgKode,'.',LPAD(bidangbrgKode,'2','0'),'.',LPAD(kelbrgKode,'2','0')) as kode,
		kelbrgId as id,
		kelbrgNama as nama,
		IFNULL(last_code,0) last_code");
		$this->get_join_kelompok($params);
		$this->aset->limit($limit, $offset);
		return $this->aset->get('golongan_barang_ref as a')->result_array();
	}

	public function num_kelompok($params = [])
	{
		$this->aset->select("COUNT(kelbrgId) as jumlah");
		$this->get_join_kelompok($params);
		return $this->aset->get('golongan_barang_ref as a')->row_array();
	}

// ===========================================================================
// ** modal sub Kelompok
	public function get_join_sub_kelompok($params=[])
	{
		$this->aset->join("bidang_barang_ref", "(bidangbrgGolbrgId = golbrgId)");
		$this->aset->join("kelompok_barang_ref", "(kelbrgBidangbrgId = bidangbrgId)");
		$this->aset->join("sub_kelompok_barang_ref", "(subkelbrgKelbrgId = kelbrgId)");
		$this->aset->join("
			(
				SELECT 
					barangSubkelbrgId as id, 
					MAX(barangKode) as last_code 
				FROM 
					barang_ref 
				GROUP BY 
					barangSubkelbrgId
			) AS a", "(kelbrgBidangbrgId = bidangbrgId) ", "id = subkelbrgId","left");

		if(!empty($params['id_bidang'])){
			$this->aset->where("bidangbrgId", $params['id_bidang']);
		}

		if(!empty($params['id_kelompok'])){
			$this->aset->where("kelbrgId", $params['id_kelompok']);
		}

		if (!empty($params['search'])) {
			$this->aset->where("
			(
				CONCAT(
					LPAD(golbrgKode, '2', '0'),
					'.',
					LPAD(bidangbrgKode, '2', '0'),
					'.',
					LPAD(kelbrgKode, '2', '0'),
					'.',
					LPAD(subkelbrgKode, '2', '0')
					) LIKE '%". $params['search']. "%' OR 
					subkelbrgNama LIKE '%".$params['search']."%'
					)");
		}
		$this->aset->order_by('kelbrgId');
	}

	public function get_sub_kelompok($limit, $offset, $params = '')
	{
		$this->aset->select("
			bidangbrgId AS id_bidang,
			CONCAT(golbrgKode,'.',LPAD(bidangbrgKode,'2','0')) as kodeBidang,
			bidangbrgNama as namaBidang,

			kelbrgId AS id_kelompok,
			CONCAT(LPAD(golbrgKode,'2','0'),'.',LPAD(bidangbrgKode,'2','0'),'.',LPAD(kelbrgKode,'2','0')) as kodeKelompok,
			kelbrgNama as namaKelompok,
			CONCAT(LPAD(golbrgKode,'2','0'),'.',LPAD(bidangbrgKode,'2','0'),'.',LPAD(kelbrgKode,'2','0'),'.',LPAD(subkelbrgKode,'2','0')) as kode,
			subkelbrgId as id,
			subkelbrgNama as nama,
			IFNULL(last_code,0) last_code
		");
		$this->get_join_sub_kelompok($params);
		$this->aset->limit($limit, $offset);
		return $this->aset->get('golongan_barang_ref')->result_array();
	}
	public function num_sub_kelompok($params=[])
	{
		$this->aset->select("golbrgId");
		$this->get_join_sub_kelompok($params);
		$query = $this->aset->get('golongan_barang_ref')->num_rows();
		$rs = 10;

		if ($query > 0) {
			$rs = $query;
		}

		return $rs;
	}
// ===========================================================================
// ** List Barang
	public function get_join_barang($params=[])
	{
		$this->aset->join("bidang_barang_ref", "(bidangbrgGolbrgId = golbrgId)");
		$this->aset->join("kelompok_barang_ref", "(kelbrgBidangbrgId = bidangbrgId)");
		$this->aset->join("sub_kelompok_barang_ref", "(subkelbrgKelbrgId = kelbrgId)");
		$this->aset->join("barang_ref", "(barangSubkelbrgId = subkelbrgId)","left");
		
		if(!empty($params['id_bidang'])){
			$this->aset->where("golbrgId", $params['id_bidang']);
		}
		if(!empty($params['id_kelompok'])){
			$this->aset->where("kelbrgId", $params['id_kelompok']);
		}
		if(!empty($params['id_sub_kelompok'])){
			$this->aset->where("subkelbrgId", $params['id_sub_kelompok']);
		}
		if(!empty($params['id_sub_kelompok'])){
			$this->aset->where("
				(
					CONCAT(LPAD(golbrgKode,1,0),'.',
					LPAD(bidangbrgKode,2,0),'.',
					LPAD(kelbrgKode,2,0),'.',
					LPAD(subkelbrgKode,2,0),'.',
					LPAD(barangKode,3,0)
				) LIKE '%".$params['nama_barang']."%' 
				OR barangNama LIKE '%" . $params['nama_barang'] . "%')");
		}
		$this->aset->order_by('golbrgId,kelbrgId,subkelbrgId,barangId');
	}
	public function get_barang($limit, $offset,  $params)
	{
		$this->aset->select("
		kelbrgId as idKelompok,
		CONCAT(
			LPAD(golbrgKode,1,0),' . ',
			LPAD(bidangbrgKode,2,0),' . ',
			LPAD(kelbrgKode,2,0)
		) AS kode_kelompok,
		kelbrgNama AS nama_kelompok,

        subkelbrgId as idSubKelompok,
		CONCAT(
			LPAD(golbrgKode,1,0),' . ',
			LPAD(bidangbrgKode,2,0),' . ',
			LPAD(kelbrgKode,2,0),' . ',
			LPAD(subkelbrgKode,2,0)
		) AS kode_sub_kelompok,
		subkelbrgKode AS nama_sub_kelompok,

        barangId as id,
        CONCAT(
			LPAD(golbrgKode,1,0),' . ',
			LPAD(bidangbrgKode,2,0),' . ',
			LPAD(kelbrgKode,2,0),' . ',
			LPAD(subkelbrgKode,2,0),' . ',
			LPAD(barangKode,3,0)) as kode,
        barangNama as nama,
		
        barangSatuanbrgId as satuan
		");
		$this->get_join_barang($params);
		$this->aset->limit($limit, $offset);
		
		$data_barang = $this->aset->get('golongan_barang_ref')->result_array() ;
		if(!empty($data_barang)){
			foreach ($data_barang as $key => $row) {
				$barang['id'][$key] = $row['id'];
				$barang['kode'][$key] = $row['kode'];
				$barang['nama'][$key] = $row['nama'];
				$barang['satuan'][$key] = $row['satuan'];

				$barang['id_kelompok'][$key] = $row['idKelompok'];
				$barang['kode_kelompok'][$key] = $row['kode_kelompok'];
				$barang['nama_kelompok'][$key] = $row['nama_kelompok'];

				$barang['id_sub_kelompok'][$key] = $row['idSubKelompok'];
				$barang['kode_sub_kelompok'][$key] = $row['kode_sub_kelompok'];
				$barang['nama_sub_kelompok'][$key] = $row['nama_sub_kelompok'];
			}
			$i = 0;
			for ($x = 0; $x < $key; $x++) {
				if ($x == 0) {
					$barang_[$i]['id']		= $barang['id_kelompok'][$x];
					$barang_[$i]['kode']	= $barang['kode_kelompok'][$x];
					$barang_[$i]['nama']	= $barang['nama_kelompok'][$x];
					$barang_[$i]['aksi']	= 'hide';
					$i++;
					$barang_[$i]['id']		= $barang['id_sub_kelompok'][$x];
					$barang_[$i]['kode']	= $barang['kode_sub_kelompok'][$x];
					$barang_[$i]['nama']	= $barang['nama_sub_kelompok'][$x];
					$barang_[$i]['aksi']	= 'hide';
					$i++;
					$barang_[$i]['id']		= $barang['id'][$x];
					$barang_[$i]['kode']	= $barang['kode'][$x];
					$barang_[$i]['nama']	= $barang['nama'][$x];
					$barang_[$i]['aksi']	= 'show';
					$i++;
				} else {
					if ($barang['id_kelompok'][$x] != $barang['id_kelompok'][$x - 1]) {
						$barang_[$i]['id']		= $barang['id_kelompok'][$x];
						$barang_[$i]['kode']	= $barang['kode_kelompok'][$x];
						$barang_[$i]['nama']	= $barang['nama_kelompok'][$x];
						$barang_[$i]['aksi']	= 'hide';
						$i++;
					} else {
						if ($barang['id_sub_kelompok'][$x] != $barang['id_sub_kelompok'][$x - 1]) {
							$barang_[$i]['id']		= $barang['id_sub_kelompok'][$x];
							$barang_[$i]['kode']	= $barang['kode_sub_kelompok'][$x];
							$barang_[$i]['nama']	= $barang['nama_sub_kelompok'][$x];
							$barang_[$i]['aksi']	= 'hide';
							$i++;
						} else {
							$barang_[$i]['id']		= $barang['id'][$x];
							$barang_[$i]['kode']	= $barang['kode'][$x];
							$barang_[$i]['nama']	= $barang['nama'][$x];
							$barang_[$i]['aksi']	= 'show';
							$i++;
						}
					}
				}
			}
			for ($y = 0; $y < $i; $y++) {
				$data[] = [
					'id' 	=> $barang_[$y]['id'],
					'kode' 	=> $barang_[$y]['kode'],
					'nama' 	=> $barang_[$y]['nama'],
					'aksi' 	=> $barang_[$y]['aksi']
				];
			}
		}else{
			$data=[];
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
	// ===========================================================================
// ** Generate las kode barang

	public function generate_kode($kode_barang)
	{
		$sqlGenerateKode = "SELECT 
         kelbrgId as idKelompok,
         subkelbrgId as idSubKelompok,
         barangId as idBarang,
         invAtkJenisRefId as id,
         CONCAT(
			 LPAD(golbrgKode,1,0),' . ',
			 LPAD(bidangbrgKode,2,0),' . ',
			 LPAD(kelbrgKode,2,0),'.', 
			 LPAD(subkelbrgKode,2,0),' . ',
			 LPAD(barangKode,3,0),' . ',
			 invAtkKode
			 ) AS kode,
         invAtkNama as nama,
         barangSatuanbrgId as satuan
      FROM 
         golongan_barang_ref
         JOIN bidang_barang_ref ON (bidangbrgGolbrgId = golbrgId)
         JOIN kelompok_barang_ref ON (kelbrgBidangbrgId = bidangbrgId)
         JOIN sub_kelompok_barang_ref ON (subkelbrgKelbrgId = kelbrgId)
         JOIN barang_ref ON (barangSubkelbrgId = subkelbrgId)
			JOIN inv_atk_jenis_ref ON (invAtkJenisBarangRefId = barangId)
      WHERE	
			(CONCAT(
				LPAD(golbrgKode,1,0),' . ',
				LPAD(bidangbrgKode,2,0),' . ',
				LPAD(kelbrgKode,2,0),' . ',
				LPAD(subkelbrgKode,2,0),' . ',
				LPAD(barangKode,3,0),' . ',invAtkKode) LIKE '$kode_barang%')";
		$gnumber = $this->aset->query($sqlGenerateKode)->row_array();
	

		$explode = explode(".", $gnumber['kode']);
		$lenght 	= strlen(intval($explode[5] + 1));
		switch ($lenght) {
			case 1:
				$generate = '00000' . ($explode[5] + 1);
				break;
			case 2:
				$generate = '0000' . ($explode[5] + 1);
				break;
			case 3:
				$generate = '000' . ($explode[5] + 1);
				break;
			case 4:
				$generate = '00' . ($explode[5] + 1);
				break;
			case 5:
				$generate = '0' . ($explode[5] + 1);
				break;
			case 6:
				$generate = ($explode[5] + 1);
				break;
		}
		return $generate;
	}
// ===========================================================================
// ** Transaksi data  
	public function insert_data($tabel, $data)
	{
		return $this->aset->insert($tabel, $data);
	}

}