<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model mutasi antar unit
 * @created : 2021-07-29 10:05:00
 * @author  : sriharyo <srihary@ugm.ac.id>
 * @company : DSDI UGM
*/
class Model_mutasi_antar_unit extends CI_Model 
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
/*
status penerima
	- Y 	: setuju semua
	- YC  	: setuju sebagian
	- N 	: tidak setuju
	- C 	: proses check
*/
	public function get_select()
	{
		$this->aset->select("
			mtsAsetMstId AS id,
			mtsAsetMstTglMutasi AS tgl,
			mtsAsetMstBAMutasi AS berita_acara,
			mtsAsetMstPIC AS pic,
			mtsAsetMstApproved AS isApproved,
			IF(mstAsetMstApprovedPenerima IS NOT NULL,mstAsetMstApprovedPenerima,0) AS isApprovedPenerima,
			(SELECT COUNT(mtsAsetDetId) FROM mutasi_aset_det WHERE mtsAsetDetMstId = mtsAsetMstId) AS jmlBrg,
			c.unitkerjaNama AS unitAsal,
			c.unitkerjaKode AS unitAsalKode,
			d.unitkerjaNama AS unitTujuan,
			d.unitkerjaKode AS unitTujuanKode,
			IF(mstAsetMstApprovedPenerima IN ('Y','YC','N'),1,0) AS cantDel,
			IF((a.mtsAsetMstApproved = 'Y' AND mstAsetMstApprovedPenerima IN ('Y','YC') AND (mtsAsetMstFile IS NULL OR mtsAsetMstFile = '')),1,0) AS mustUpload,
			IF((a.mtsAsetMstApproved = 'Y' AND mstAsetMstApprovedPenerima IN ('Y','YC') AND (mtsAsetMstFile IS NOT NULL OR mtsAsetMstFile <> '')),1,0)  AS canUpload
			", false);
	}

	public function get_join()
	{
		$this->aset->join("unit_kerja_ref c", "c.unitkerjaId = a.mtsAsetMstUnitIdAsal","left");
		$this->aset->join("unit_kerja_ref d", "d.unitkerjaId = a.mtsAsetMstUnitIdTuj","left");
	}

	public function get_where($params,$search)
	{
		if(!empty($params['tglAwal'])){
			$this->aset->where("(a.mtsAsetMstTglMutasi BETWEEN '".$params[
				'tglAwal']."' AND '".$params[
					'tglAkhir']."' ) ");
		}

		if(!empty($params['ba_mutasi'])){
			$this->aset->like("mtsAsetMstBAMutasi",$params['ba_mutasi']);
		}


		$dataUnit = $this->userAsetSession();
		$this->aset->where("
			(c.unitkerjaKodeSistem LIKE '".$dataUnit['unit_system']."%' OR d.unitkerjaKodeSistem LIKE '".$dataUnit['unit_system']."%' )"
		);
			
		$this->aset->where("IF(d.unitkerjaKodeSistem LIKE '".$dataUnit['unit_system']."%',mtsAsetMstApproved IS NOT NULL,mtsAsetMstId IS NOT NULL)");

		if(!empty($params['unit_kerja']) && $params['unit_kerja']!='1'){
			$this->aset->where("d.unitkerjaId", $params['unit_kerja']);
 
		}
		if(!empty($search)){
			if($search==""){
				$where_approve = ' mtsAsetMstApproved is null';
			}else{

			}
			switch ($search) {
				case 'Approve Unit Penerima':
				$where_approve = 'mtsAsetMstApproved IS NOT NULL ';
				break;
				case 'Belum Approve Unit Penerima':
				$where_approve = 'mtsAsetMstApproved IS NULL ';
				break;
				case 'Approve Unit Penerima':
				$where_approve = '(mstAsetMstApprovedPenerima IS NOT NULL ';
				break;
				default:
				$where_approve = '';
				break;
			}
			$this->aset->where("
				(
				mtsAsetMstId LIKE '%".$search."%' OR 
				mtsAsetMstTglMutasi LIKE '%".$search."%' OR 
				mtsAsetMstBAMutasi LIKE '%".$search."%' OR 
				mtsAsetMstApproved LIKE '%".$search."%' OR 
				".$where_approve." OR 
				d.unitkerjaNama LIKE '%".$search."%' )
				");
		}

		// $this->aset->where("mtsAsetMstId IS NOT NULL ");
	}

	public function get_data($limit, $offset, $search, $order,  $params)
	{

		$this->get_select();
		$this->get_join();

		$this->get_where($params,$search);
		$this->aset->order_by($order);
		$this->aset->order_by('a.mtsAsetMstTglMutasi','DESC');
		$this->aset->group_by('mtsAsetMstId');

		$this->aset->limit($limit, $offset);
		return $this->aset->get('mutasi_aset_mst a')->result_array();
	}
	public function get_num_data($params,$search)
	{
		
		$this->get_select();
		$this->get_join();
		$this->get_where($params,$search);
		$this->aset->order_by('a.mtsAsetMstTglMutasi','DESC');
		$this->aset->group_by('mtsAsetMstId');
		return $this->aset->get('mutasi_aset_mst a')->num_rows();
	}

	public function get_detail_mutasi($id='')
	{
		$this->aset->select("
			mtsAsetMstTglMutasi AS tgl,
			mtsAsetMstBAMutasi AS ba_mutasi,
			mtsAsetDetBrgId AS barangId,

			e.invDetKodeBarang AS kode_barang,
			IFNULL(invHisKodeBarangBaru,e.invDetKodeBarang) AS kode_aset,
			TRIM(barangNama) AS nama_aset,
			TRIM(e.invDetMerek )AS merk,
			e.invDetSpesifikasi AS spesifikasi,
			DATE_FORMAT(e.invDetTglPembelian,'%d-%m-%Y') AS tgl_perolehan,
			satuanbrgNama AS satuan,
			mstPenystnNilaiPerolehan AS nilai_perolehan,
			mstPenystnDisusutkan AS nilai_buku,
			kondisibrgNama AS kondisi,
			kondisibrgId AS kondBrgId,
			TRIM(e.invDetKeteranganLain) AS keteranganDet,
			a.unitkerjaId AS unit_asal_id,
			a.unitkerjaKode AS unit_asal_kode,
			a.unitkerjaNama AS unit_asal,
			b.unitkerjaKode AS unit_tujuan_kode,
			b.unitkerjaNama AS unit_tujuan,
			b.unitkerjaId AS unit_tujuan_id,
			c.ruangNama AS lokasi_asal,
			c.ruangId AS lokasi_asal_id,
			d.ruangNama AS lokasi_tujuan,
			d.ruangId AS lokasi_tujuan_id,
			mtsAsetMstPIC AS pic,
			mtsAsetMstNIP AS nip,
			mtsAsetMstPICUnitTuj AS pic2,
			mtsAsetMstNIPUnitTuj AS nip2,
			TRIM(mtsAsetMstKet) AS keterangan,
			mtsAsetMstFile AS FILE,
			mtsAsetMstApproved,
			mtsAsetDetId as mutasiDetId,
			invHisKodeBarangLama AS kodeLama,
			CONCAT(
			(CASE mtsAsetMstApproved
			WHEN 'Y' THEN CONCAT('Approve Unit Asal',' ( <em>',ua.RealName,' - ',mstAsetMstTglApproved,'</em> )')
			WHEN 'N' THEN CONCAT('Tidak Approve Unit Asal',' ( <em>',ua.RealName,' - ',mstAsetMstTglApproved,'</em> )')
			ELSE 'Belum Approve Unit Asal'
			END),
			IF(
			mtsAsetMstApproved IS NOT NULL,
			CONCAT('<br>',
			(CASE mstAsetMstApprovedPenerima
			WHEN 'Y' THEN CONCAT('Approve Unit Penerima',' ( <em>',ut.RealName,' - ',mstAsetMstTglApprovePenerima,'</em> )')
			WHEN 'YC' THEN CONCAT('Approve Sebagian Unit Penerima',' ( <em>',ut.RealName,' - ',mstAsetMstTglApprovePenerima,'</em> )')
			WHEN 'N' THEN CONCAT('Tidak Approve Unit Penerima',' ( <em>',ut.RealName,' - ',mstAsetMstTglApprovePenerima,'</em> )')
			WHEN 'C' THEN CONCAT('Proses Periksa Unit Penerima',' ( <em>',ut.RealName,' - ',mstAsetMstTglApprovePenerima,'</em> )')
			ELSE 'Belum Approve Unit Penerima'
			END)
			),
			''
			)
			) AS stat,
			mtsAsetDetStatusTerima AS statTerima,
			mstAsetMstApprovedPenerima AS statApprTerima,
			IF(hapusDetId IS NOT NULL,1,0) AS dihapus
			");
		$this->aset->join('mutasi_aset_det','mtsAsetDetMstId = mtsAsetMstId','left');
		$this->aset->join('inventarisasi_kode_history','invHisKodeMutasiDetId = mtsAsetDetId','left');
		$this->aset->join('inventarisasi_detail AS e','mtsAsetDetBrgId = invDetId AND e.invDetIsDel = 0','left');
		$this->aset->join('satuan_barang_ref','satuanbrgId = e.invDetSatuanBarang','left');
		$this->aset->join('penyusutan_brg_mst','mstPenystnBarangId = e.invDetId','left');
		$this->aset->join('kondisi_barang_ref','kondisibrgId = mtsAsetDetKondId','left');
		$this->aset->join('inventarisasi_mst','invDetMstId = invMstId','left');

		$this->aset->join('barang_ref','invBarangId = barangId','left');
		$this->aset->join('sub_kelompok_barang_ref','barangSubkelbrgId = subkelbrgId','left');
		$this->aset->join('kelompok_barang_ref','subkelbrgKelbrgId = kelbrgId','left');
		$this->aset->join('bidang_barang_ref','kelbrgBidangbrgId = bidangbrgId','left');
		$this->aset->join('golongan_barang_ref','bidangbrgGolbrgId = golbrgId','left');

		$this->aset->join('unit_kerja_ref a','a.unitkerjaId = mtsAsetMstUnitIdAsal','left');
		$this->aset->join('unit_kerja_ref b','b.unitkerjaId = mtsAsetMstUnitIdTuj','left');
		$this->aset->join('ruang c','c.ruangId = mtsAsetMstLokasiIdAsal','left');
		$this->aset->join('ruang d','d.ruangId = mtsAsetMstLokasiIdTuj','left');
		$this->aset->join('gtfw_user ua','ua.UserId = mtsAsetMstApproveUserId','left');
		$this->aset->join('gtfw_user ut','ut.UserId = mtsAsetMstApprovePenerimaId','left');
		$this->aset->join('penghapusan_det pd','pd.hapusDetInvDetId = mtsAsetDetBrgId','left');
		$this->aset->where('mtsAsetMstId',$id);
		// $this->aset->where('e.invDetIsDel','0');
		return $this->aset->get("mutasi_aset_mst")->result_array();
	}
	public function get_golongan_by_id($id='')
	{
		$sql_gol = "
		SELECT
		golbrgKibKode
		FROM
		golongan_barang_ref
		WHERE 
		golbrgId = '{$id}'";
		$data_gol=$this->aset->query($sql_gol)->row_array();

		$data['golongan_kode'] = $data_gol['golbrgKibKode'];
		return $data_gol['golbrgKibKode'];
	}

	public function get_unit_kerja($jenis_unit='tujuan')
	{

		$this->aset->select('unitkerjaId AS id,
			unitkerjaKode AS kode,
			unitkerjaNama AS nama,
			t.ttdNama2 AS pic,
			t.ttdNip2 AS nip
			');

		if($jenis_unit=='asal'){
			if($this->user_level==2){
				$this->aset->where('LEFT(unitkerjaKode,2)',substr($this->kode_unit, 0, 2));
			}elseif($this->user_level==3){
				$this->aset->where('unitkerjaKode',$this->kode_unit);
			}
		}

		$this->aset->join('ttd_ref t','t.ttdUnitId = unit_kerja_ref.unitkerjaId','left');
		$this->aset->where('unitkerjaActive','Y');

		return $this->aset->get('unit_kerja_ref');
	}

	public function get_unit_kerja_tujuan()
	{
		$this->aset->select('unitkerjaId AS id,
			unitkerjaKode AS kode,
			unitkerjaNama AS nama,
			t.ttdNama2 AS pic,
			t.ttdNip2 AS nip
			');
		$this->aset->join('ttd_ref t','t.ttdUnitId = unit_kerja_ref.unitkerjaId','left');
		$this->aset->where('unitkerjaActive','Y');

		return $this->aset->get('unit_kerja_ref');
	}
	public function select_pic_by_id($unit_id)
	{
		return $this->aset->get_where('ttd_ref',array('ttdUnitId'=>$unit_id))->row_array();
	}

	public function num_barang($unit,$search='')
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
		$this->aset->where("unit_kerja_ref.unitkerjaId",$unit);
		$this->aset->where("invDetIsDel = 0");
		if(!empty($search)){
			$this->aset->where("(invDetMstLabel LIKE '%".$search."%' OR invDetKodeBarang LIKE '%".$search."%' OR invDetMerek LIKE '%".$search."%')");
		}
		$this->aset->where("invDetAsetStatusId","1");
		$this->aset->group_by("invDetId");

		return $this->aset->get('inventarisasi_detail')->row_array();
	}
	public function get_barang($limit,$offset,$unit,$search='')
	{
		$this->aset->select("
			invDetId AS barang_id,
			invDetKodeBarang AS kode_barang,
			TRIM(invDetMstLabel) AS nama_barang,
			TRIM(invDetMerek) AS merk,
			TRIM(invDetSpesifikasi) AS spesifikasi,
			DATE_FORMAT(invDetTglPembelian,'%d-%m-%Y') AS tgl_perolehan,
			ROUND(mstPenystnNilaiPerolehan, 0) AS nilai_perolehan,
			satuanbrgNama AS satuan,
			kondisibrgNama AS kondisi,
			invDetKeteranganLain AS keterangan
			");
		$this->aset->join("satuan_barang_ref","satuanbrgId = invDetSatuanBarang","left");
		$this->aset->join("kondisi_barang_ref","kondisibrgId = invDetKondisiId","left");
		$this->aset->join("penyusutan_brg_mst","mstPenystnBarangId = invDetId");
		$this->aset->join("unit_kerja_ref","unitkerjaId = invDetUnitKerja");
		$this->aset->join("penghapusan_det","hapusDetInvDetId = invDetId","left");
		$this->aset->where("hapusDetId IS NULL");
		$this->aset->where("invDetUnitKerja",$unit);
		if(!empty($search)){
			$this->aset->where("(invDetMstLabel LIKE '%".$search."%' OR invDetKodeBarang LIKE '%".$search."%' OR invDetMerek LIKE '%".$search."%')");
		}
		$this->aset->where("invDetIsDel = 0");
		$this->aset->where("invDetAsetStatusId","1");
		$this->aset->group_by("invDetId");
		$this->aset->limit($limit,$offset);
		return $this->aset->get('inventarisasi_detail')->result_array();
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
	public function update_data_($tabel,$where,$data)
	{
		$this->aset->where($where);
		return $this->aset->update($tabel, $data);

	}

	public function insert_detail_inv_det_trans($data)
	{
		return $this->aset->query("
			INSERT INTO inv_det_trans
			SELECT 
			id.invDetId,
			{$data['param_1']},
			{$data['param_2']}*(
			(SELECT IFNULL(SUM(idh.rph_aset),0) FROM inventarisasi_detail_history idh WHERE idh.invDetId = id.invDetId) +
			(SELECT IFNULL(SUM(idt.transNilai),0) FROM inv_det_trans idt WHERE idt.transInvDetId = id.invDetId AND 
			(idt.transKeterangan <> 'MTS' OR (idt.transKeterangan = 'MTS' AND idt.transOptId <> '{$data['id_detail']}')))
			),
			NOW(),
			'MTS',
			'{$data['id_detail']}',
			REPLACE(invDetKodeBarang,'.',''),
			invDetUnitKerja,
			'{$data['userId']}',
			NOW()
			FROM inventarisasi_detail id
			WHERE id.invDetId = '{$data['id_barang']}'");
	}

	public function insert_data_inv_det_trans($data)
	{

		return $this->aset->query("
			INSERT INTO inv_det_trans
			SELECT 
			id.invDetId,
			".$data['params_jenis_transaksi'].",
			".$data['params_nilai']."*(
			(SELECT IFNULL(SUM(idh.rph_aset),0) FROM inventarisasi_detail_history idh WHERE idh.invDetId = id.invDetId) +
			(SELECT IFNULL(SUM(idt.transNilai),0) FROM inv_det_trans idt WHERE idt.transInvDetId = id.invDetId AND 
			(idt.transKeterangan <> 'MTS' OR (idt.transKeterangan = 'MTS' AND idt.transOptId <> '".$data['params_id_detail']."')))
			),
			NOW(),
			'MTS',
			'".$data['params_id_detail']."',
			REPLACE(invDetKodeBarang,'.',''),
			invDetUnitKerja,
			'".$data['params_user_id']."',
			NOW()
			FROM inventarisasi_detail id
			WHERE id.invDetId = '".$data['params_barang_id']."'
			");
	}

	public function insert_inv_kode_his($data)
	{
		return $this->aset->query("
			INSERT INTO inventarisasi_kode_history
			(
			invHisKodeInvDetId,
			invHisKodeBarangLama,
			invHisKodeBarangBaru,
			invHisKodeBarcodeLama,
			invHisKodeBarcodeBaru,
			invHisKodeMutasiDetId,
			invHisKodeUnitLama,
			invHisKodeUnitBaru
			) VALUES 
			( 
			{$data['id_barang']},
			'{$data['oldKode0']}',
			'{$data['kodeBarangBaru']}',
			'{$data['oldKode1']}',
			'{$data['kodeBarcodeBaru']}',
			{$data['id_detail']},
			(SELECT invDetUnitKerja FROM inventarisasi_detail WHERE invDetId = '{$data['id_detail']}'),
			{$data['unit_tujuan_id']}
			)
			");
	}

	public function delete_data($tabel,$kunci_primer,$id) 
	{
		$this->aset->where($kunci_primer, $id);
		return $this->aset->delete($tabel);
	}
	
	public function get_new_kode_barang($unitTujuan,$id_barang)
	{
		$this->aset->select("
			IF(iln.nup IS NULL,1,0) AS isNew,
			id.invDetBarangId AS brgId,

			IF(iln.nup IS NULL,1,iln.nup+1) AS nup,

			CONCAT(
			LEFT(id.invDetKodeBarang,15),
			IF(iln.nup IS NULL,1,iln.nup+1)
			) AS kodeBarangBaru,

			IF(
			REPLACE(id.invDetKodeBarang,'.','')+0 = id.invDetBarcode+0 OR id.invDetBarcode IS NULL OR TRIM(id.invDetBarcode) = '',
			CONCAT(id.invDetBarangId,IF(iln.nup IS NULL,1,iln.nup+1)),
			id.invDetBarcode
			) AS kodeBarcodeBaru,
			CONCAT(id.invDetKodeBarang,'|',IFNULL(id.invDetBarcode,'')) AS kodeLama
			");
		$this->aset->join('inv_last_nup iln',"iln.brgId = id.invDetBarangId AND iln.unitId = '".$unitTujuan."'",'left');
		$this->aset->where('id.invDetId',$id_barang);
		return $this->aset->get('inventarisasi_detail id')->row_array();
	}

	public function get_ruang_trans($unit_tujuan_id)
	{
		$this->aset->select("ruangId");
		$this->aset->where("ruangGedungId",1);
		$this->aset->where("ruangUnitId",$unit_tujuan_id);
		return $this->aset->get('ruang')->row_array();
	}

	public function get_cek_aproval($id='')
	{
		$this->aset->select("
			SUM(IF(mtsAsetDetStatusTerima='S',1,0))  AS jml_s,
			SUM(IF(mtsAsetDetStatusTerima='TS',1,0))  AS jml_ts");
		$this->aset->where("mtsAsetDetMstId",$id);
		$this->aset->group_by("mtsAsetDetMstId");
		return $this->aset->group_by("mutasi_aset_det")->row_array();
	}

	public function getIsSusut($id='')
	{
		$this->aset->select("COUNT(penyusutanDetBrg) AS isSusut");
		$this->aset->where("penyusutanDetBrg",$id);
		return $this->aset->get("penyusutan_det")->row_array();
	}

	public function insert_susut_mutasi($data)
	{

		return $this->aset->query("
			INSERT INTO penyusutan_mutasi
			SELECT 
			penyusutanDetBrg,
			REPLACE(invDetKodeBarang,'.',''),
			invDetUnitKerja,
			'{$data['id_detail']}',
			NULL,
			NULL,
			'{$data['param_1']}', 
			{$data['jumlah']}*SUM(penyusutanDetNilaiPenyusutan),
			NOW(),
			'{$data['user']}'
			FROM penyusutan_det
			JOIN inventarisasi_detail ON invDetId = penyusutanDetBrg
			WHERE penyusutanDetBrg = '{$data['id_barang']}'
			");
	}


}