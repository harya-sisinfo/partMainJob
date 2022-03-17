<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model Pengelolaan BHP kantor
 * @created : 2022-01-11 09:00:00
 * @author  : sriharyo <srihary@ugm.ac.id>
 * @company : DSDI UGM
 */
class Model_pengelolaan_bhp_kantor extends CI_Model
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

	public function get_join($value = '')
	{
		$this->aset->join('ruang r', 'r.ruangId = iag.invAtkGudangRuangId');
		$this->aset->join('unit_kerja_ref ukr', 'ukr.unitkerjaId = r.ruangUnitId');
		$this->aset->join('inv_atk_brg iab', 'iab.invBrgAtkId = iag.invAtkGudangInvDtlbrgId');
		$this->aset->join('inv_atk_det iad', 'iad.invAtkDetId = iab.invDet');
		$this->aset->join('inv_atk_master iam', 'iam.invAtkMstId = iad.invAtkDetMstId');
		$this->aset->join('inv_atk_jenis_ref iajr', 'iajr.invAtkJenisRefId = iam.invAtkMstJenisPersediaanId');
		$this->aset->join('setting_stok_atk ssa', 'ssa.stockSetJenisPersediaanId = iajr.invAtkJenisRefId AND ssa.stockSetRuangId = iag.invAtkGudangRuangId', 'left');
	}

	public function get_where($params = '', $search = '')
	{
		switch ($params['status_stok']) {
			case 'all':	//$a = 0; $b = 999999;
				$a = 'jumlah_stok >= 0';
				break;
			case '1': //$a = '(stok_minimal*1.1)'; $b = 999999;
				$a = 'jumlah_stok > 0';
				break;
			case '2': //$a = 0; $b = 0;
				$a = 'jumlah_stok = 0';
				break;
			case '3': //$a = 1; $b = 'stok_minimal+5';
				$a = 'jumlah_stok < stokMin';
				break;
			default:
				$a = 'jumlah_stok >= 0';
				break;
		}
		if (!empty($search)) {
			$this->aset->where("
			(
				r.ruangNama LIKE '%" . $search . "%' OR 
				ukr.unitkerjaNama LIKE '%" . $search . "%' 
			)
			");
		}
		$dataUnit = $this->userAsetSession();
		if (!empty($params['gudang'])) {
			$this->aset->where("(iag.invAtkGudangRuangId = '" . $params['gudang'] . "' OR 'all' = '" . $params['gudang'] . "')");
		}
		$this->aset->where("(ukr.`unitkerjaKodeSistem` = '" . $dataUnit['unit_system'] . "' OR ukr.`unitkerjaKodeSistem` LIKE '" . $dataUnit['unit_system'] . "%')");
		$this->aset->group_by("iajr.`invAtkJenisRefId`,iag.`invAtkGudangRuangId`");
		$this->aset->having("(nama_barang LIKE '%" . $params['kode_barang'] . "%' OR kode_barang LIKE '%" . $params['kode_barang'] . "%') AND " . $a);
	}
	public function get_select()
	{
		$this->aset->select("
		ssa.`stockSetId` AS atk_id,
		iajr.`invAtkJenisRefId` AS barangId,
		iajr.`invAtkNama` AS nama_barang,
		CONCAT(
			MID(iajr.`invAtkJenisBarangRefId`, 1, 1), '.', MID(iajr.`invAtkJenisBarangRefId`, 2, 2), '.',
			MID(iajr.`invAtkJenisBarangRefId`, 4, 2), '.', MID(iajr.`invAtkJenisBarangRefId`, 6, 2), '.',
			MID(iajr.`invAtkJenisBarangRefId`, 8, 3), '.', iajr.`invAtkKode`
		) AS kode_barang,
		iad.`invAtkDetBarcode` AS barcode,
		GROUP_CONCAT(DISTINCT iad.`invAtkDetMerk` SEPARATOR ', ') AS merk,
		r.`ruangNama` AS lokasi_gudang,
		iag.`invAtkGudangRuangId` AS ruangId,
		SUM(iag.invAtkGudangJumlah) AS jumlah_stok,
		IFNULL(ssa.`stockSetMin`,'Belum di Setting') AS stok_minimal,
		IFNULL(ssa.`stockSetMin`,5) AS stokMin,
		ukr.`unitkerjaNama` AS unit
			");
	}

	public function get_data($limit, $offset, $search, $order,  $params)
	{
		$this->get_select();
		$this->get_join();
		$this->get_where($params, $search);
		$this->aset->limit($limit, $offset);
		return $this->aset->get('inv_atk_gudang iag')->result_array();
	}
	public function get_satuan()
	{
		$this->aset->select("
			satuanbrgId AS id,
			satuanbrgNama AS name
		");
		$this->aset->order_by('satuanbrgNama');
		return $this->aset->get('satuan_barang_ref')->result_array();
	}

	public function get_num_data($params, $search)
	{
		$this->get_select();
		$this->get_join();
		$this->get_where($params, $search);
		$query = $this->aset->get('inv_atk_gudang iag')->num_rows();
		$rs = 10;

		if ($query > 0) {
			$rs = $query;
		}
		return $rs;
	}

	// ================================================================
	// ** mtsId by id 
	public function get_mtsId($id_barang)
	{
		$this->aset->select('invAtkMstId');
		$this->aset->from('inv_atk_master');
		$this->aset->where("invAtkMstJenisPersediaanId", $id_barang);
		$mstId = $this->aset->get('inv_atk_master')->row_array();
		return $mstId['invAtkMstId'];
	}
	// ================================================================
	// ** list barang 
	public function get_gudang_join($kode = '', $nama = '')
	{
		$data_unit = $this->userAsetSession();
		$this->aset->select("
			ruangId AS id,
			ruangNama AS nama,
			ruangKode AS kode,
			jenisruangNama AS jenis,
			unitkerjaNama AS unit,
			IF(invAtkGudangInvDtlbrgId IS NOT NULL,SUM(invAtkGudangJumlah),-1) AS jmlBhp");

		$this->aset->join('jenis_ruang_ref', 'jenisruangId = ruangJenisRuangId', 'left');
		$this->aset->join('inv_atk_gudang', 'invAtkGudangRuangId = ruangId', 'left');
		$this->aset->join('unit_kerja_ref', 'unitkerjaId = ruangUnitId', 'left');

		$this->aset->group_by('ruangId');

		$this->aset->where("(unitkerjaKodeSistem LIKE '" . $data_unit['unit_system'] . "%' OR unitkerjaKodeSistem LIKE '" . $data_unit['unit_system'] . "')");
		if (!empty($kode)) {
			$this->aset->like('ruangKode', $kode);
		}
		if (!empty($nama)) {
			$this->aset->like('ruangNama', $nama);
		}
	}
	public function get_gudang($limit, $offset, $kode = '', $nama = '')
	{
		$this->get_gudang_join($kode, $nama);

		$this->aset->order_by('jmlBhp', 'DESC');
		$this->aset->limit($limit, $offset);
		return $this->aset->get('ruang')->result_array();
	}
	public function num_gudang($kode, $nama)
	{
		$this->get_gudang_join($kode, $nama);
		$query = $this->aset->get('ruang');
		return $query->num_rows();
	}
	// ================================================================
	// ** list barang 
	public function get_barang_join($kode = '', $nama = '')
	{
		$this->aset->join("bidang_barang_ref", "(`bidangbrgGolbrgId` = `golbrgId`)");
		$this->aset->join("kelompok_barang_ref", "(`kelbrgBidangbrgId` = `bidangbrgId`)");
		$this->aset->join("sub_kelompok_barang_ref", "(`subkelbrgKelbrgId` = `kelbrgId`)");
		$this->aset->join("barang_ref", "(`barangSubkelbrgId` = `subkelbrgId`)");
		$this->aset->join("inv_atk_jenis_ref", "(invAtkJenisBarangRefId = barangId)");
		$this->aset->join("coa_ugm_ref ", "coaKode = coaUGM", "left");

		if (!empty($kode)) {
			$this->aset->like("(CONCAT(LPAD(`golbrgKode`,1,0),'.',LPAD(`bidangbrgKode`,2,0),'.',LPAD(`kelbrgKode`,2,0),'.',LPAD(`subkelbrgKode`,2,0),'.',LPAD(`barangKode`,3,0),'.',`invAtkKode`)", $kode);
		}
		if (!empty($nama)) {
			$this->aset->like("invAtkNama", $nama);
		}
		$this->aset->where("invAtkAktif", 'Y');
	}
	public function get_barang($limit, $offset, $kode = '', $nama = '')
	{
		$this->get_barang_join($kode, $nama);
		$this->aset->select("
		`kelbrgId` as `idKelompok`,
         `subkelbrgId` as `idSubKelompok`,
         `barangId` as `idBarang`,
         `invAtkJenisRefId` as `id`,
         CONCAT(LPAD(`golbrgKode`,1,0),'.',LPAD(`bidangbrgKode`,2,0),'.',LPAD(`kelbrgKode`,2,0),'.', LPAD(`subkelbrgKode`,2,0),'.',LPAD(`barangKode`,3,0),'.',`invAtkKode`) AS `kode`,
         TRIM(`invAtkNama`) as `nama`,
         `barangSatuanbrgId` as `satuan`,
         CONCAT(coaKode,' - ',coaNama) AS coa");
		$this->aset->limit($limit, $offset);
		$this->aset->order_by("`kelbrgNama`,
         `subkelbrgNama`,
         `barangNama`, 
		`invAtkKode`");
		return $this->aset->get('golongan_barang_ref')->result_array();
	}
	public function num_barang($kode = "", $nama = "")
	{
		$this->get_barang_join($kode = '', $nama = '');
		$this->aset->select("COUNT(barangId) as jumlah");
		return $this->aset->get('golongan_barang_ref')->row_array();
	}

	public function get_atk_detail($data)
	{
		$this->aset->select('invAtkDetId');
		$this->aset->where("(invAtkDetBarcode='" . $data['barcode_barang'] . "' OR ''='" . $data['barcode_barang'] . "') AND invAtkDetMstId='" . $data['mstId'] . "'");
		return $this->aset->get('inv_atk_det')->row_array();
	}
	// ** update data
	public function get_atk_id($id = '')
	{
		$this->aset->select("
		stockSetId,
		invAtkDetBarcode     AS barcode,
		CONCAT(LPAD(golbrgKode,1,'0'),'.',LPAD(bidangbrgKode,2,'0'),'.',LPAD(kelbrgKode,2,'0'),'.',LPAD(subkelbrgKode,2,'0'),'.',LPAD(barangKode,3,'0'),'.',invAtkKode) AS kode_barang,
		invAtkNama           AS nama_barang,
		stockSetMin          AS stok_minimal,
		stcokSetKet          AS keterangan,
		invAtkDetLabel       AS label_barang,
		invAtkDetMerk        AS merk,
		invAtkDetSpesifikasi AS spesifikasi,
		ruangId              AS gudang,
		ruangNama            AS gudang_nama");

		$this->aset->where('stockSetId', $id);

		$this->aset->join('inv_atk_jenis_ref', 'stockSetJenisPersediaanId = invAtkJenisRefId', 'left');
		$this->aset->join('barang_ref', 'invAtkJenisBarangRefId = barangId', 'left');
		$this->aset->join('sub_kelompok_barang_ref', 'barangSubkelbrgId = subkelbrgId', 'left');
		$this->aset->join('kelompok_barang_ref', 'subkelbrgKelbrgId = kelbrgId', 'left');
		$this->aset->join('bidang_barang_ref', 'kelbrgBidangbrgId = bidangbrgId', 'left');
		$this->aset->join('golongan_barang_ref', 'bidangbrgGolbrgId = golbrgId', 'left');
		$this->aset->join('ruang', 'stockSetRuangId = ruangId', 'left');
		$this->aset->join('(
			SELECT 
				invAtkMstBarangId,
				invAtkGudangRuangId,
				invAtkDetLabel,
				invAtkDetMerk,
				invAtkDetSpesifikasi,
				invAtkDetBarcode
			FROM 
				inv_atk_master
			LEFT JOIN inv_atk_det ON invAtkDetMstId = invAtkMstId
			LEFT JOIN inv_atk_brg ON invDet = invAtkDetId
			LEFT JOIN inv_atk_gudang ON invAtkGudangInvDtlbrgId = invBrgAtkId
			GROUP BY 
				invAtkMstBarangId,invAtkGudangRuangId
		) a ', 'stockSetBrgId = invAtkMstBarangId AND stockSetRuangId = invAtkGudangRuangId', 'left');

		return $this->aset->get('setting_stok_atk')->row_array();
	}
	public function get_atk_barang_id($barang = '', $ruang = '')
	{
		$this->aset->select("
			invAtkJenisRefId     AS barang_id,
			CONCAT(LPAD(golbrgKode,1,'0'),'.',LPAD(bidangbrgKode,2,'0'),'.',LPAD(kelbrgKode,2,'0'),'.',LPAD(subkelbrgKode,2,'0'),'.',LPAD(barangKode,3,'0'),'.',invAtkKode) AS kode_barang,
			ruangId              AS gudang,
			ruangNama            AS gudang_nama,
			invAtkNama           AS nama_barang,
			invAtkDetBarcode     AS barcode,
			invAtkDetLabel       AS label_barang,
			invAtkDetMerk        AS merk_barang,
			SUM(invAtkGudangJumlah) AS sisa_barang,
			invAtkDetSpesifikasi AS spesifikasi,
			biaya                AS invAtkBiayaNominal,
			satuanbrgNama");
		$this->aset->join('sub_kelompok_barang_ref', 'barangSubkelbrgId = subkelbrgId');
		$this->aset->join('kelompok_barang_ref', 'subkelbrgKelbrgId = kelbrgId');
		$this->aset->join('bidang_barang_ref', 'kelbrgBidangbrgId = bidangbrgId');
		$this->aset->join('golongan_barang_ref', 'bidangbrgGolbrgId = golbrgId');
		$this->aset->join('inv_atk_jenis_ref', 'invAtkJenisBarangRefId = barangId', 'left');
		$this->aset->join('inv_atk_master', 'invAtkMstJenisPersediaanId = invAtkJenisRefId', 'left');
		$this->aset->join('satuan_barang_ref', 'satuanbrgId = invAtkMstSatuanBarang');
		$this->aset->join('inv_atk_det', 'invAtkDetMstId = invAtkMstId', 'left');
		$this->aset->join('inv_atk_brg', 'invDet = invAtkDetId', 'left');
		$this->aset->join('inv_atk_gudang', 'invAtkGudangInvDtlbrgId = invBrgAtkId', 'left');
		$this->aset->join('ruang', 'ruangId = invAtkGudangRuangId', 'left');
		$this->aset->where('invAtkJenisRefId', $barang);
		$this->aset->where('ruangId', $ruang);
		$this->aset->group_by('invAtkJenisRefId, ruangId');
		return $this->aset->get("barang_ref")->row_array();
	}
	// ================================================================
	public function insert_data($tabel, $data)
	{
		return $this->aset->insert($tabel, $data);
	}

	public function insert_ignore($table, $data)
	{
		$insert_query = $this->aset->insert_string($table, $data);
		$insert_query = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $insert_query);
		return $this->aset->query($insert_query);
	}
	public function update_data($tabel, $kunci_primer, $id, $data)
	{
		$this->aset->where($kunci_primer, $id);
		return $this->aset->update($tabel, $data);
	}
	public function delete_data($tabel, $kunci_primer, $id)
	{
		$this->aset->where($kunci_primer, $id);
		return $this->aset->delete($tabel);
	}
	// ================================================================
	public function get_log($barang,$ruang)
	{
		$this->aset->select("
		ruangNama, 
		CONCAT(CAST(c.kode AS CHAR),'.' ,invAtkKode) AS barangKode,
		invAtkNama AS barangNama,
		SUM(invAtkGudangJumlah) AS jumlahSisa
		");

		$this->aset->join('ruang','ruangId = invAtkGudangRuangId','left');
		$this->aset->join('inv_atk_brg','invAtkGudangInvDtlbrgId = invBrgAtkId','left');
		$this->aset->join('inv_atk_det','invAtkDetId = invDet','left');
		$this->aset->join('inv_atk_master','invAtkMstId = invAtkDetMstId','left');
		$this->aset->join('inv_atk_jenis_ref','invAtkJenisRefId = invAtkMstJenisPersediaanId','left');
		$this->aset->join("(
			SELECT barangId,CONCAT(
				LPAD(golbrgKode, 1, '0'),
				'.',
				LPAD(bidangbrgKode, 2, '0'),
				'.',
				LPAD(kelbrgKode, 2, '0'),
				'.',
				LPAD(subkelbrgKode, 2, '0'),
				'.',
				LPAD(barangKode, 3, '0')) AS kode
				FROM barang_ref
				LEFT JOIN sub_kelompok_barang_ref ON barangSubkelbrgId = subkelbrgId 
				LEFT JOIN kelompok_barang_ref ON subkelbrgKelbrgId = kelbrgId 
				LEFT JOIN bidang_barang_ref ON kelbrgBidangbrgId = bidangbrgId 
				LEFT JOIN golongan_barang_ref ON bidangbrgGolbrgId = golbrgId 
			) c", 'c.barangId = invAtkJenisBarangRefId','left');

		$this->aset->where('invAtkJenisRefId',$barang);
		$this->aset->where('ruangId',$ruang);
		$this->aset->get('inv_atk_gudang')->row_array();
	}
	public function get_log_list_join($barang = '', $ruang = '', $tanggal_mulai = '', $tanggal_selesai = '')
	{
		$this->aset->join('inv_atk_brg', 'invBrgAtkId = logAtkAtkId');
		$this->aset->join('inv_atk_det', 'invAtkDetId = invDet');
		$this->aset->join('inv_atk_master', 'invAtkMstId = invAtkDetMstId');
		$this->aset->join('inv_atk_gudang', 'invAtkGudangInvDtlbrgId = invBrgAtkId');
		$this->aset->join('ruang', 'ruangId = logAtkTujuanRuangId', 'left');
		$this->aset->join('unit_kerja_ref ukr2', 'ukr2.unitkerjaId = ruangUnitId', 'left');
		$this->aset->join('inv_map_simpel ims', 'ims.invBrgAtkId = logAtkAtkId', 'left');
		$this->aset->join('inv_map_emonev ime', 'ime.invBrgAtkId = logAtkAtkId', 'left');
		$this->aset->join('v_pembelian_verified v', 'v.id = ims.pembLgsngId', 'left');
		$this->aset->join('v_perintah_bayar_pengadaan vp', 'vp.id = ime.pembPengdnId', 'left');
		$this->aset->join('aset_transaksi_atk ata', 'vp.id = ime.pembPengdnId', 'left');
		$this->aset->join('unit_kerja_ref ukr3', 'vp.id = ime.pembPengdnId', 'left');

		$this->aset->where('invAtkMstJenisPersediaanId', $barang);
		$this->aset->where('logAtkRuangId', $ruang);

		if (!empty($tanggal_mulai)) {
			$this->aset->where("logAtkTgl BETWEEN '".$tanggal_mulai."' AND '".$tanggal_selesai."'");
		} else {
			$this->aset->where("logAtkTgl", date('Y-m-d'));
		}
	}
	public function get_log_list($limit, $offset, $barang='', $ruang='', $tanggal_mulai='', $tanggal_selesai='')
	{
		$this->aset->select("logAtkId as id,
				logAtkTgl,
				invAtkDetBarcode,
				logAtkJmlBrg,
				logAtkHrgSatuan AS hrgsat,
				CASE
					WHEN logAtkStatus = 'Mutasi Ke Unit Lain' THEN ukr2.unitkerjaNama
					WHEN logAtkStatus = 'Mutasi Ke Gudang Lain' THEN CONCAT('&lt;em&gt;',ruangNama,'&lt;/em&gt; - ',ukr2.unitkerjaNama)
					WHEN logAtkStatus = 'Register Transaksi Harian' THEN CONCAT('&lt;em&gt;',ata.transAtkNamaPenerima,'&lt;/em&gt; - ',ukr3.unitkerjaNama)
					ELSE ''
				END AS tujuanBarang,
				logAtkKeterangan,
				CASE 
					WHEN logAtkStatus = 'Penambahan Stock Dengan Usulan' THEN CONCAT(logAtkStatus,IFNULL(CONCAT(' (',logAtkKeterangan,')'),''))
					WHEN logAtkStatus = 'Mutasi Ke Gudang Lain' THEN CONCAT(logAtkStatus,IFNULL(CONCAT(' (',logAtkBAMutasi,')'),''))
					WHEN logAtkStatus = 'Register Transaksi Harian' THEN CONCAT(logAtkStatus,IFNULL(CONCAT(' (',logAtkNoTrans,')'),''))
					ELSE logAtkStatus
				END AS logAtkStatus,
				IF(logAtkStatus = 'Penambahan Stock Dengan Pengadaan',IFNULL(v.nomor_spp,vp.nomor_spp),'') AS nospp");
		
		$this->get_log_list_join($barang, $ruang, $tanggal_mulai, $tanggal_selesai);
		$this->aset->order_by('logAtkTgl');
		$this->aset->limit($limit, $offset);
		$this->aset->get('log_atk')->result_array();
	}
	public function get_log_list_num( $barang = '', $ruang = '', $tanggal_mulai = '', $tanggal_selesai = '')
	{
		$this->aset->select("*");
		$this->get_log_list_join($barang, $ruang, $tanggal_mulai, $tanggal_selesai);
		
		$query = $this->aset->get('log_atk');
		return $query->num_rows();
	}
	// ================================================================
	public function get_adj($barang,$ruang)
	{
		$this->aset->select("
		invAtkJenisRefId     AS barang_id,
		CONCAT(
			LPAD(golbrgKode,1,'0'),'.',
			LPAD(bidangbrgKode,2,'0'),'.',
			LPAD(kelbrgKode,2,'0'),'.',
			LPAD(subkelbrgKode,2,'0'),'.',
			LPAD(barangKode,3,'0'),'.',
			invAtkKode) 	  AS kode_barang,
		ruangId              AS gudang,
		ruangNama            AS gudang_nama,
		invAtkNama           AS nama_barang,
		invAtkDetBarcode     AS barcode,
		invAtkDetLabel       AS label_barang,
		invAtkDetMerk        AS merk_barang,
		SUM(invAtkGudangJumlah) AS sisa_barang,
		invAtkDetSpesifikasi AS spesifikasi,
		biaya                AS invAtkBiayaNominal,
		satuanbrgNama
		");
		$this->aset->join('sub_kelompok_barang_ref', 'barangSubkelbrgId = subkelbrgId');
		$this->aset->join('kelompok_barang_ref', 'subkelbrgKelbrgId = kelbrgId');
		$this->aset->join('bidang_barang_ref', 'kelbrgBidangbrgId = bidangbrgId');
		$this->aset->join('golongan_barang_ref', 'bidangbrgGolbrgId = golbrgId');
		$this->aset->join('inv_atk_jenis_ref', 'invAtkJenisBarangRefId = barangId','left');
		$this->aset->join('inv_atk_master', 'invAtkMstJenisPersediaanId = invAtkJenisRefId','left');
		$this->aset->join('satuan_barang_ref', 'satuanbrgId = invAtkMstSatuanBarang');
		$this->aset->join('inv_atk_det', 'invAtkDetMstId = invAtkMstId','left');
		$this->aset->join('inv_atk_brg', 'invDet = invAtkDetId','left');
		$this->aset->join('inv_atk_gudang', 'invAtkGudangInvDtlbrgId = invBrgAtkId','left');
		$this->aset->join('ruang', 'ruangId = invAtkGudangRuangId');
		
		$this->aset->where('invAtkJenisRefId',$barang);
		$this->aset->where('ruangId ',$ruang);
		$this->aset->group_by('invAtkJenisRefId, ruangId ');
		return $this->aset->get("barang_ref")->row_array();
	}
}