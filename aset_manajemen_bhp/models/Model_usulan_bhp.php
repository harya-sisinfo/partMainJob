<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model usulan (bhp) bahan habis pakai
 * @created : 2022-01-11 09:00:00
 * @author  : sriharyo <srihary@ugm.ac.id>
 * @company : DSDI UGM
 */
class Model_usulan_bhp extends CI_Model
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


	public function get_form($params = '')
	{
		if (!empty($params['tanggal_mulai']) || !empty($params['tanggal_selesai'])) {
			$tanggal_mulai 		= empty($params['tanggal_mulai']) ? date('Y-m-d', strtotime(str_replace('/', '-', substr($params['tanggal_selesai'], 0, 10)))) : date('Y-m-d', strtotime(str_replace('/', '-', substr($params['tanggal_selesai'], 0, 10))));
			$tanggal_selesai 	= empty($params['tanggal_selesai']) ? date('Y-m-d', strtotime(str_replace('/', '-', substr($params['tanggal_mulai'], 0, 10)))) : date('Y-m-d', strtotime(str_replace('/', '-', substr($params['tanggal_selesai'], 0, 10))));
			$periode = "(usulanBrgTglUsulan >= $tanggal_mulai OR $tanggal_mulai = '') AND (usulanBrgTglUsulan <= ($tanggal_selesai + INTERVAL 1 DAY) OR $tanggal_selesai = '') AND ";
		} else {
			$periode = "";
		}

		if (!empty($params['unit_id'])) {
			$unit_kerja = "(bhp_usulan_brg.usulanBrgUnitId = " . $params['unit_id'] . " OR " . $params['unit_id'] . " = 'all') AND";
		} else {
			$unit_kerja = "";
		}

		if (!empty($params['nama_barang'])) {
			$nama_barang = "(invAtkNama LIKE '%" . $params['nama_barang'] . "%') AND";
		} else {
			$nama_barang = "";
		}
		$unit_sys = $this->userAsetSession()['unit_system'];

		$this->aset->from("
		(
            SELECT
               usulanBrgId AS id,
               usulanBrgTglUsulan AS tglUsulan,
               usulanBrgNoUsulan AS nomorUsulan,
               unitkerjaNama,
               COUNT(DISTINCT usulanBrgDetId) AS jumlahItem,
               SUM(usulanBrgNilaiUsulan) AS totalUsulan,
               usulanBrgIsApprove,
               
               usulanBrgIsApprove AS status,
               concat(us.UserName,' (',us.REalName,')') AS userName,
               concat(usr.UserName,' (',usr.REalName,')') AS verifikator
            FROM
               bhp_usulan_brg
               LEFT JOIN bhp_usulan_brg_det ON usulanBrgDetUsulanBrgId = usulanBrgId
               LEFT JOIN inv_atk_jenis_ref ON invAtkJenisRefId = usulanBrgDetBrgId
               JOIN unit_kerja_ref ON unitkerjaId = usulanBrgUnitId
               JOIN gtfw_user us ON us.UserId = bhp_usulan_brg.usulanBrgUserId
               LEFT JOIN gtfw_user usr ON usr.UserId = usulanBrgUserApprove
            WHERE
				usulanBrgId IS NOT NULL AND 
				" . $periode . "
				" . $unit_kerja . "
				" . $nama_barang . "              
               (unitkerjaKodeSistem = '" . $unit_sys . "' OR unitkerjaKodeSistem LIKE '" . $unit_sys . "%') 
            GROUP BY
               usulanBrgId
         ) DYN
		");
	}

	public function get_data($limit, $offset, $search, $order,  $params)
	{
		$this->aset->select("
			id,
			tglUsulan,
			nomorUsulan,
			unitkerjaNama,
			jumlahItem,
			totalUsulan,
			usulanBrgIsApprove,
			status,
			userName,
			verifikator");
		$this->get_form($params);
		$this->aset->order_by($order);
		$this->aset->limit($limit, $offset);
		return $this->aset->get()->result_array();
	}

	public function get_num_data($params = "", $search = "")
	{
		$this->aset->select("id");
		$this->get_form($params);
		$query = $this->aset->get();
		$rs = 10;
		if ($query->num_rows() > 0) {
			$rs = $query->num_rows();
		}
		return $rs;
	}

	public function get_unit_aset($id_unit = '')
	{
		$this->aset->select("
			unitkerjaId,
			unitkerjaKodeSistem,
			unitkerjaKode,
			unitkerjaKodeFin,
			unitkerjaKodeSimakbmn,
			unitkerjaKodeEntitas,
			unitkerjaNama,
			unitkerjaNamaPimpinan,
			unitkerjaTipeunitId,
			unitKerjaUnitStatusId,
			unitkerjaParentId,
			unitkerjaActive ");
		if (!empty($id_unit)) {
			$this->aset->where("unitkerjaId", $id_unit);
		}
		return $this->aset->get("unit_kerja_ref")->result_array();
	}
	// ===========================================================================
	// ** List Barang
	public function get_join_barang($params = [])
	{
		$this->aset->join("bidang_barang_ref", "(bidangbrgGolbrgId = golbrgId)");
		$this->aset->join("kelompok_barang_ref", "(kelbrgBidangbrgId = bidangbrgId)");
		$this->aset->join("sub_kelompok_barang_ref", "(subkelbrgKelbrgId = kelbrgId)");
		$this->aset->join("barang_ref", "(barangSubkelbrgId = subkelbrgId)", "left");
		$this->aset->join("satuan_barang_ref", "(barangSatuanbrgId = satuanbrgId)", "left");

		if (!empty($params['id_bidang'])) {
			$this->aset->where("golbrgId", $params['id_bidang']);
		}
		if (!empty($params['id_kelompok'])) {
			$this->aset->where("kelbrgId", $params['id_kelompok']);
		}
		if (!empty($params['id_sub_kelompok'])) {
			$this->aset->where("subkelbrgId", $params['id_sub_kelompok']);
		}
		if (!empty($params['id_sub_kelompok'])) {
			$this->aset->where("
				(
					CONCAT(LPAD(golbrgKode,1,0),'.',
					LPAD(bidangbrgKode,2,0),'.',
					LPAD(kelbrgKode,2,0),'.',
					LPAD(subkelbrgKode,2,0),'.',
					LPAD(barangKode,3,0)
				) LIKE '%" . $params['nama_barang'] . "%' 
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
		
        barangSatuanbrgId as satuan,
		satuanbrgId AS satuan_nama
		");
		$this->get_join_barang($params);
		$this->aset->limit($limit, $offset);

		$data_barang = $this->aset->get('golongan_barang_ref')->result_array();
		if (!empty($data_barang)) {
			foreach ($data_barang as $key => $row) {
				$barang['id'][$key] = $row['id'];
				$barang['kode'][$key] = $row['kode'];
				$barang['nama'][$key] = $row['nama'];
				$barang['satuan'][$key] = $row['satuan'];
				$barang['satuan_nama'][$key] = $row['satuan_nama'];

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
					$barang_[$i]['id']			= $barang['id_kelompok'][$x];
					$barang_[$i]['kode']		= $barang['kode_kelompok'][$x];
					$barang_[$i]['nama']		= $barang['nama_kelompok'][$x];
					$barang_[$i]['satuan']		= '';
					$barang_[$i]['satuan_nama']	= '';
					$barang_[$i]['aksi']		= 'hide';
					$i++;
					$barang_[$i]['id']			= $barang['id_sub_kelompok'][$x];
					$barang_[$i]['kode']		= $barang['kode_sub_kelompok'][$x];
					$barang_[$i]['nama']		= $barang['nama_sub_kelompok'][$x];
					$barang_[$i]['satuan']		= '';
					$barang_[$i]['satuan_nama']	= '';
					$barang_[$i]['aksi']		= 'hide';
					$i++;
					$barang_[$i]['id']			= $barang['id'][$x];
					$barang_[$i]['kode']		= $barang['kode'][$x];
					$barang_[$i]['nama']		= $barang['nama'][$x];
					$barang_[$i]['satuan']		= $barang['satuan'][$x];
					$barang_[$i]['satuan_nama']	= $barang['satuan_nama'][$x];
					$barang_[$i]['aksi']		= 'show';
					$i++;
				} else {
					if ($barang['id_kelompok'][$x] != $barang['id_kelompok'][$x - 1]) {
						$barang_[$i]['id']			= $barang['id_kelompok'][$x];
						$barang_[$i]['kode']		= $barang['kode_kelompok'][$x];
						$barang_[$i]['nama']		= $barang['nama_kelompok'][$x];
						$barang_[$i]['satuan']		= '';
						$barang_[$i]['satuan_nama']	= '';
						$barang_[$i]['aksi']		= 'hide';
						$i++;
					} else {
						if ($barang['id_sub_kelompok'][$x] != $barang['id_sub_kelompok'][$x - 1]) {
							$barang_[$i]['id']			= $barang['id_sub_kelompok'][$x];
							$barang_[$i]['kode']		= $barang['kode_sub_kelompok'][$x];
							$barang_[$i]['nama']		= $barang['nama_sub_kelompok'][$x];
							$barang_[$i]['satuan']		= '';
							$barang_[$i]['satuan_nama']	= '';
							$barang_[$i]['aksi']		= 'hide';
							$i++;
						} else {
							$barang_[$i]['id']		= $barang['id'][$x];
							$barang_[$i]['kode']	= $barang['kode'][$x];
							$barang_[$i]['nama']	= $barang['nama'][$x];
							$barang_[$i]['satuan']		= $barang['satuan'][$x];
							$barang_[$i]['satuan_nama']	= $barang['satuan_nama'][$x];
							$barang_[$i]['aksi']	= 'show';
							$i++;
						}
					}
				}
			}
			for ($y = 0; $y < $i; $y++) {
				$data[] = [
					'id' 			=> $barang_[$y]['id'],
					'kode' 			=> $barang_[$y]['kode'],
					'nama' 			=> $barang_[$y]['nama'],
					'satuan' 		=> $barang_[$y]['satuan'],
					'satuan_nama' 	=> $barang_[$y]['satuan_nama'],
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

	public function get_data_by_id($id)
	{
		$this->aset->select("usulanBrgTglUsulan,
						usulanBrgNoUsulan,
						usulanBrgUnitId,
						unitkerjaNama AS usulanBrgUnitNama");
		$this->aset->join('unit_kerja_ref', 'unitkerjaId = usulanBrgUnitId');
		$this->aset->where('usulanBrgId', $id);
		return $this->aset->get('bhp_usulan_brg')->row_array();
	}
	public  function get_detail_barang($id)
	{
		$this->aset->select("usulanBrgDetId AS id,
			usulanBrgDetKodeAkun AS brgKode,
			usulanBrgDetBrgId,
			invAtkNama as brgNama,
			usulanBrgNilaiHps AS brgHps,
			IFNULL(usulanBrgDetJmlPengadaan, 'Dalam Proses') AS usulanBrgDetJmlApprove,
			usulanBrgNilaiUsulan,
			usulanBrgDetBrgSatuan,
			usulanBrgDetJml,
			usulanBrgDetBrgSpesifikasi AS spesifikasi,
			usulanBrgDetTglPakai AS tglPakai,
			usulanBrgDetStatusVerifikasi,
			usulanBrgDetCatatan");
		$this->aset->join('inv_atk_jenis_ref','invAtkJenisRefId = usulanBrgDetBrgId');
		$this->aset->where('usulanBrgDetUsulanBrgId',$id);
		return $this->aset->get('bhp_usulan_brg_det')->result_array();
	}
	
}
