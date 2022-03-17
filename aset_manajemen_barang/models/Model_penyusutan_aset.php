<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model penyusutan aset
 * @created : 2021-07-29 10:05:00
 * @author  : sriharyo <srihary@ugm.ac.id>
 * @company : DSDI UGM
*/
class Model_penyusutan_aset extends CI_Model 
{
	public $data = '';
	public function __construct() 
	{
		parent::__construct();
		$this->aset     	= $this->load->database("aset",TRUE);
		$this->simabeka    	= $this->load->database("simabeka",TRUE);
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
	

	public function get_select(){
		$this->aset->select("
			SQL_CALC_FOUND_ROWS
			mstPenystnBarangId AS id,
			invDetKodeBarang AS kode_aset,
			invDetMstLabel AS nama_aset,
			unitkerjaNama,
			mstPenystnNilaiPerolehan,
			mstPenystnNilaiPenyusutan AS nilai_penyusutan,
			mstPenystnNilaiTotalPenyusutan AS total_penyusutan,	
			mstPenystnDisusutkan AS nilai_buku
			", false);
	}

	public function get_join(){
		$this->aset->join("inventarisasi_detail", "mstPenystnBarangId = invDetId");
		$this->aset->join("unit_kerja_ref", "invDetUnitKerja = unitkerjaId","left");
	}

	public function get_where($params,$search){
		$dataUnit = $this->userAsetSession();
		if(!empty($search)){
			$this->aset->where("(invDetMstLabel LIKE '%$search%' OR invDetKodeBarang LIKE '%$search%' OR mstPenystnNilaiPerolehan LIKE '%$search%')");
		}

		if(!empty($params['jenis_kib'])){
			$this->aset->where("invDetGolId",$params['jenis_kib']);
		}else{
			$this->aset->where_in("invDetGolId",array('3','6'));
		}

		if(!empty($params['kode_aset'])){
			$this->aset->where("invDetKodeBarang",$params['kode_aset']);
		}
		
		if(!empty($params['unit_kerja'])){
			$this->aset->where("invDetUnitKerja", $params['unit_kerja']);
		}

		if($this->user_level!=1){
			$this->aset->like('unit_kerja_ref.unitkerjaKodeSistem',$dataUnit['unit_system'],'after');
		}

		$this->aset->where("mstPenystnBarangId IS NOT NULL ");
	}

	public function get_data($limit, $offset, $search, $order,  $params){
		$this->get_select();
		$this->get_join();
		$this->get_where($params,$search);
		$this->aset->order_by($order);
		$this->aset->limit($limit, $offset);
		return $this->aset->get('penyusutan_brg_mst')->result_array();
	}
	public function get_data_all($search, $params){
		$this->get_select();
		$this->get_join();
		$this->get_where($params,$search);
		return $this->aset->get('penyusutan_brg_mst')->result_array();
	}
	public function get_num_data($params,$search){
		$this->aset->select('COUNT(*) AS jumlah');
		$this->get_join();
		$this->get_where($params,$search);
		return $this->aset->get('penyusutan_brg_mst')->row_array();
	}

	public function get_log_query($id)
	{
		$this->aset->select('
			invDetKodeBarang AS kode_aset,
			barangNama,
			mst.invMstLabel AS label_aset,
			mst.invMerek,
			mst.invSpesifikasi,
			mstPenystnNilaiTotalPenyusutan,
			mstPenystnDisusutkan,
			penyusutanMstPeriode,
			penyusutanMstNoBA,
			penyusutanDetNilaiPenyusutan,
			penyusutanDetNilaiAkhir');
		$this->aset->join('inventarisasi_detail','penyusutanDetBrg = invDetId','right');
		$this->aset->join('inventarisasi_mst mst','invDetMstId = invMstId');
		$this->aset->join('barang_ref','invBarangId = barangId');
		$this->aset->join('sub_kelompok_barang_ref','barangSubkelbrgId = subkelbrgId');
		$this->aset->join('kelompok_barang_ref','subkelbrgKelbrgId = kelbrgId');
		$this->aset->join('bidang_barang_ref','kelbrgBidangbrgId = bidangbrgId');
		$this->aset->join('unit_kerja_ref','invDetUnitKerja = unitkerjaId', 'left');
		$this->aset->join('penyusutan_brg_mst','mstPenystnBarangId = invDetId', 'left');
		$this->aset->join('penyusutan_mst','penyusutanDetMst = penyusutanMstId', 'left');
		
		$this->aset->where('invDetId',$id);
		$this->aset->order_by('invKodeAset,invDetKodeBarang','ASC');
	}

	public function get_log($limit,$offset,$id)
	{
		$this->get_log_query($id);
		$this->aset->limit($limit,$offset);
		return $this->aset->get('penyusutan_det');
	}
	public function num_log($id)
	{
		$this->get_log_query($id);
		return $this->aset->get('penyusutan_det');
	}
	public function get_jenis_kib(){
		$this->aset->select('
			golbrgId AS id,
			golbrgNama AS nama
			');
		$this->aset->where_in('golbrgKibKode',array('b'));
		return $this->aset->get('golongan_barang_ref');
	}
	public function get_unit_kerja(){
		$dataUnit = $this->userAsetSession();

		$this->aset->select('unitkerjaId AS id,
			unitkerjaKode AS kode,
			unitkerjaNama AS nama
			');
		/*if($this->user_level==2){
			$this->aset->where('LEFT(unitkerjaKode,2)',substr($this->kode_unit, 0, 2));
		}elseif($this->user_level==3){
			$this->aset->where('unitkerjaKode',$this->kode_unit);
		}*/

		if($this->user_level!=1){
			$this->aset->like('unitkerjaKodeSistem',$dataUnit['unit_system'],'after');
		}

		$this->aset->where('unitkerjaActive','Y');
		return $this->aset->get('unit_kerja_ref');
	}

	public function get_jenis_aset($value='')
	{
		$this->aset->select('
			golbrgId As kib_id,
			golbrgKibKode AS kib_kode,
			SUBSTR(golbrgNama,8) AS kib_nama,
			1 AS nilai_penyusutan
			');
		$this->aset->join('inventarisasi_detail','invDetId = mstPenystnBarangId');
		$this->aset->join('golongan_barang_ref','golbrgId = invDetGolId');

		$this->aset->where("golbrgId IN (3,4,5,6,8)");
		$this->aset->where("invDetBidangbrngId != '601'");
		$this->aset->where('mstPenystnUmrEko > 0');
		$this->aset->where('mstPenystnDisusutkan > 0');

		$this->aset->group_by('golbrgId');

		return $this->aset->get('penyusutan_brg_mst');
	}

	public function cek_transaksi($kkb='')
	{
		$this->aset->select('COUNT(*) as total');
		$this->aset->where('TRIM(transReferensi)=TRIM('.$kkb.')');
		return $this->aset->get('transaksi')->row_array();
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

	public function check_diff_tgl_susut($kib_id,$tglSusut_)
	{
		$data_periode = explode('-',$tglSusut_);
		$tglSusut = $data_periode[1].$data_periode[0];
		$this->aset->select("
			PERIOD_DIFF('{$tglSusut}',DATE_FORMAT(MAX(invDetTglSusut),'%Y%m')) AS monDiff,
			DATE_FORMAT(MAX(invDetTglSusut),'%Y-%m-%d') AS lastSusut
			");
		$this->aset->where("invDetGolId",$kib_id);
		return $this->aset->get("inventarisasi_detail");
	}

	public function cek_kib($golbrgId)
	{
		$this->aset->select('golbrgKibKode AS kib');
		$this->aset->where('golbrgId',$golbrgId);
		return $this->aset->get('golbrgKibKode AS kib');
	}
	
	public function get_aset_back_date($golId,$tglSusut_)
	{
		$periode_ 		= explode('-',$tglSusut_);
		$periode 		= $periode_[1].$periode_[0];
		$tglSusut 		= $periode_[1].'-'.$periode_[0].'-01';
		$jamTglSusut 	= $periode_[1].'-'.$periode_[0].'-01 23:59:59';


		/*$sql_data = '
		SELECT
			invDetId AS id,
			
			IF(
			PERIOD_DIFF('.$periode.',DATE_FORMAT(invDetTglPembelian,"%Y%m")) > invDetUmurEkonomis,
			invDetNilaiPerolehanSatuan,
			PERIOD_DIFF('.$periode.',DATE_FORMAT(invDetTglPembelian,"%Y%m")) * (invDetNilaiPerolehanSatuan/invDetUmurEkonomis)
			) AS totSusut,

			IF(
				invDetNilaiPerolehanSatuan - IF(
					PERIOD_DIFF('.$periode.',DATE_FORMAT(invDetTglPembelian,"%Y%m")) > invDetUmurEkonomis,
						invDetNilaiPerolehanSatuan,
						PERIOD_DIFF('.$periode.',DATE_FORMAT(invDetTglPembelian,"%Y%m")) * (invDetNilaiPerolehanSatuan/invDetUmurEkonomis)
				) < 0,0,
				invDetNilaiPerolehanSatuan - IF(
					PERIOD_DIFF('.$periode.',DATE_FORMAT(invDetTglPembelian,"%Y%m")) > invDetUmurEkonomis,
						invDetNilaiPerolehanSatuan,
						PERIOD_DIFF('.$periode.',DATE_FORMAT(invDetTglPembelian,"%Y%m")) * (invDetNilaiPerolehanSatuan/invDetUmurEkonomis)
				)
			) AS nilBuku,

			IF(
				PERIOD_DIFF('.$periode.',DATE_FORMAT(invDetTglPembelian,"%Y%m")) > invDetUmurEkonomis,
					invDetUmurEkonomis - invDetUmurEkonomis,
					invDetUmurEkonomis - PERIOD_DIFF('.$periode.',DATE_FORMAT(invDetTglPembelian,"%Y%m"))
			) AS sisaUmurEko,

			PERIOD_DIFF('.$periode.',DATE_FORMAT(invDetTglPembelian,"%Y%m")) AS monDiff,

			IFNULL(
			(
				SELECT 
					transKodeBrg
				FROM inv_det_trans 
				WHERE 
					transTglBuku <= "'.$jamTglSusut.'" AND 
					transInvDetId = id.invDetId
				ORDER BY transTglBuku DESC,transJnsTrn ASC 
				LIMIT 1
			),
				IFNULL(
					(
						SELECT 
							CONCAT(idh.kd_brg,idh.no_aset)
						FROM 
							inventarisasi_detail_history idh
						WHERE 
							idh.tgl_buku <= "'.$tglSusut.'" AND
						 	idh.invDetId = id.invDetId
						ORDER BY 
							idh.tgl_buku DESC, 
							idh.jns_trn ASC
						LIMIT 1
					),REPLACE(invDetKodeBarang,".","")
			)
			) AS kdBrg,

			IFNULL( 
				(
				SELECT 
					transUnitId 
				FROM 
					inv_det_trans 
				WHERE 
					transTglBuku <= "'.$jamTglSusut.'" AND 
					transInvDetId = id.invDetId
				ORDER BY 
					transTglBuku DESC,
					transJnsTrn ASC 
				LIMIT 1
				),
			IFNULL(
				(
				SELECT 
					idh.unitId
				FROM 
					inventarisasi_detail_history idh
				WHERE 
					idh.tgl_buku <= "'.$tglSusut.'" AND 
					idh.invDetId = id.invDetId
				ORDER BY idh.tgl_buku DESC, idh.jns_trn ASC
				LIMIT 1
				)
				,invDetUnitKerja
				)
			) AS unit
		FROM 
			inventarisasi_detail id
		JOIN penyusutan_brg_mst pbm ON mstPenystnBarangId = invDetId
		WHERE 
			mstPenystnDisusutkan > 0 AND
   			mstPenystnUmrEko > 0 AND
   			invDetGolId = "'.$golId.'" AND
   			invDetGolId IN (3,4,5,6,8) AND
   			invDetBidangbrngId NOT IN (601,603,604,605,606) AND
   			(
      			invDetBarangId NOT IN (8010101006,8010101999) OR
      			( 
         			invDetBarangId = 8010101999 AND 
         			invDetId IN (291760,291761,833468,833469,833470,833471,833472,833473,833474,833475,833476,833477,833478,833479,833480,833481,833482,833483,833484,833485,833486,833487,833488,833489,833490,833491,833492,833493,833494,833495,833496,833497,833498,833499,833500,833501,833502,833503,833504,833505,833506,833507,833508,833509,833510,833511,833512,833513,833514,833515,833516,833517,833518,833519,833520,833521,833522,833523,833524,833525,833526,833527,833528,833529,833530,833531,833532,833533,833534,833535,833536,833537,833538,833539,833540,833541,845322,845323,845324,845325,845326,845327,845328,845329,845330,845331,845332,845333,845334,845335,845336,845337,845338,845339,845340,845341,845342,845343,845344,845345,845346,845347,845348,845349,845350,845351,845352,845353,845354,845355,845356,845357,845358,845359,845360,845361,845362,845363,845364,845365,845366,845367,845368,845369,845370,845371,845372,845373,845374)
      			)
   			) AND 
   			invDetTglPembelian <= "'.$tglSusut.'" AND
   			invDetTglSusut IS NULL AND
   			mstPenystnTglBukuPerolehan <= "'.$jamTglSusut.'" AND  
   			invDetIsDel = 0
		';*/
		$sql_data = "SELECT * FROM aset_lantai WHERE lantaiId = '2'";
		$query = $this->aset->query($sql_data);

		if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return FALSE;
        }
		// return $data_row->result_array();
	}
	public function insert_detail_penyusutan($kib,$date)
	{
		$period = explode('-', $date);

		$periode_jam = $date.' 23:59:59';
		$date;
		$periode_jam = $date.' 23:59:59'; 
		$date; 
		$periode_jam_2 = $period[1]; 
		$periode_jam_1 = $period[0]; 
		$kib; 
		$date; 
		$periode_jam = $date.' 23:59:59';

		return $this->aset->query("
			INSERT INTO  penyusutan_det (
				penyusutanDetMst,
				penyusutanDetBrg,
				penyusutanDetNilaiPenyusutan,
				penyusutanDetNilaiAkhir,
				penyusutanDetSisaUmrEk,
				penyusutanDetIsTransaksi,
				jnssusut,
				kodeBrg,
				unitId
				)
			SELECT 
			(SELECT MAX(penyusutanMstId) FROM penyusutan_mst) AS penyusutanId,
			mstPenystnBarangId,
			IF(
				(
					(
						IFNULL(
							pd.`penyusutanDetNilaiAkhir`, 
							IF(
								id.`invDetTglSusut` IS NULL,
								id.`invDetNilaiPerolehanSatuan`,
								0
								)
							) 
						+ 
						IFNULL(
							SUM(ikd.invKapDetRph),
							0)
						) -
					((IFNULL(pd.`penyusutanDetNilaiAkhir`, IF(id.`invDetTglSusut` IS NULL,id.`invDetNilaiPerolehanSatuan`,0)) + IFNULL(SUM(ikd.invKapDetRph),0))/(IFNULL(pd.`penyusutanDetSisaUmrEk`,IF(id.`invDetTglSusut` IS NULL,id.`invDetUmurEkonomis`,0))+IFNULL(SUM(ikd.invKapDetUmur),0)))
					) < 0,
				(IFNULL(pd.`penyusutanDetNilaiAkhir`,IF(id.`invDetTglSusut` IS NULL,id.`invDetNilaiPerolehanSatuan`,0)) + IFNULL(SUM(ikd.invKapDetRph),0)),
			((IFNULL(pd.`penyusutanDetNilaiAkhir`,IF(id.`invDetTglSusut` IS NULL,id.`invDetNilaiPerolehanSatuan`,0)) + IFNULL(SUM(ikd.invKapDetRph),0))/(IFNULL(pd.`penyusutanDetSisaUmrEk`,IF(id.`invDetTglSusut` IS NULL,id.`invDetUmurEkonomis`,0))+IFNULL(SUM(ikd.invKapDetUmur),0)))) AS nilSusut, # nilai penyusutan

			(IFNULL(pd.`penyusutanDetNilaiAkhir`,IF(id.`invDetTglSusut` IS NULL,id.`invDetNilaiPerolehanSatuan`,0)) + IFNULL(SUM(ikd.invKapDetRph),0)) - 
			IF(
				(
					(IFNULL(pd.`penyusutanDetNilaiAkhir`,IF(id.`invDetTglSusut` IS NULL,id.`invDetNilaiPerolehanSatuan`,0)) + IFNULL(SUM(ikd.invKapDetRph),0)) - 
					((IFNULL(pd.`penyusutanDetNilaiAkhir`,IF(id.`invDetTglSusut` IS NULL,id.`invDetNilaiPerolehanSatuan`,0)) + IFNULL(SUM(ikd.invKapDetRph),0))/(IFNULL(pd.`penyusutanDetSisaUmrEk`,IF(id.`invDetTglSusut` IS NULL,id.`invDetUmurEkonomis`,0))+IFNULL(SUM(ikd.invKapDetUmur),0)))
					) < 0,
				(IFNULL(pd.`penyusutanDetNilaiAkhir`,IF(id.`invDetTglSusut` IS NULL,id.`invDetNilaiPerolehanSatuan`,0)) + IFNULL(SUM(ikd.invKapDetRph),0)),
			((IFNULL(pd.`penyusutanDetNilaiAkhir`,IF(id.`invDetTglSusut` IS NULL,id.`invDetNilaiPerolehanSatuan`,0)) + IFNULL(SUM(ikd.invKapDetRph),0))/(IFNULL(pd.`penyusutanDetSisaUmrEk`,IF(id.`invDetTglSusut` IS NULL,id.`invDetUmurEkonomis`,0))+IFNULL(SUM(ikd.invKapDetUmur),0)))) AS nilBuku, # nilai akhir/nilai buku
			(IFNULL(pd.`penyusutanDetSisaUmrEk`,IF(id.`invDetTglSusut` IS NULL,id.`invDetUmurEkonomis`,0))+IFNULL(SUM(ikd.invKapDetUmur),0))-1 AS sisaUmur, # sisa umur ekonomis
			'1',
			IF(id.isHentiOps = 1,2,NULL), # jnssusut
			IFNULL(
			( # get kode barang based on tgl buku (UNTESTED!!!)
				SELECT transKodeBrg
				FROM inv_det_trans 
				WHERE transTglBuku <= '$periode_jam' AND transInvDetId = id.invDetId
				ORDER BY transTglBuku DESC, transJnsTrn ASC 
				LIMIT 1
				),
			IFNULL(
				(
					SELECT CONCAT(idh.kd_brg,idh.no_aset)
					FROM inventarisasi_detail_history idh
					WHERE idh.tgl_buku <= '$date' AND idh.invDetId = id.invDetId
					ORDER BY idh.tgl_buku DESC, idh.jns_trn ASC
					LIMIT 1
					)
				,REPLACE(invDetKodeBarang,'.','')
				)
			),
			IFNULL( 
			( # get unit based on tgl buku (UNTESTED!!!!)
				SELECT transUnitId FROM inv_det_trans 
				WHERE transTglBuku <= '$periode_jam' AND transInvDetId = id.invDetId
				ORDER BY transTglBuku DESC, transJnsTrn ASC 
				LIMIT 1
				),
			IFNULL(
				(
					SELECT idh.unitId
					FROM inventarisasi_detail_history idh
					WHERE idh.tgl_buku <= '$date' AND idh.invDetId = id.invDetId
					ORDER BY idh.tgl_buku DESC, idh.jns_trn ASC
					LIMIT 1
					)
				,invDetUnitKerja
				)
			)
			FROM penyusutan_brg_mst pbm
			JOIN inventarisasi_detail id ON mstPenystnBarangId = invDetId
			LEFT JOIN penyusutan_det pd ON pd.penyusutanDetId = pbm.mstPenystnLastSusutDetId
			LEFT JOIN inv_kap_det ikd ON ikd.invKapDetInvId = mstPenystnBarangId AND 
			MONTH(ikd.invKapDetTglBuku) = '$periode_jam_2' AND YEAR(ikd.invKapDetTglBuku) = '$periode_jam_1'
			WHERE 
			mstPenystnBarangId IS NOT NULL

			AND mstPenystnDisusutkan > 0
			AND mstPenystnUmrEko > 0
			AND invDetGolId = '$kib'
			AND invDetGolId != 2 # exclude tanah
			AND invDetBidangbrngId NOT IN (601,603,604,605,606) # exclude aset buku, hewan, dan tumbuhan
			AND (
				invDetBarangId NOT IN (8010101006,8010101999) 
				OR 
				(invDetBarangId = 8010101999 AND invDetId IN 
				#ATB Lainnya yg harus disusutkan (temuan KAP)
					(
						291760,291761,833468,833469,833470,833471,833472,833473,833474,833475,833476,833477,833478,833479,833480,833481,833482,833483,833484,833485,833486,833487,833488,833489,833490,833491,833492,833493,833494,833495,833496,833497,833498,833499,833500,833501,833502,833503,833504,833505,833506,833507,833508,833509,833510,833511,833512,833513,833514,833515,833516,833517,833518,833519,833520,833521,833522,833523,833524,833525,833526,833527,833528,833529,833530,833531,833532,833533,833534,833535,833536,833537,833538,833539,833540,833541,845322,845323,845324,845325,845326,845327,845328,845329,845330,845331,845332,845333,845334,845335,845336,845337,845338,845339,845340,845341,845342,845343,845344,845345,845346,845347,845348,845349,845350,845351,845352,845353,845354,845355,845356,845357,845358,845359,845360,845361,845362,845363,845364,845365,845366,845367,845368,845369,845370,845371,845372,845373,845374
						)
					)
				)
			AND invDetTglPembelian <= '$date'
			AND mstPenystnTglBukuPerolehan <= '$periode_jam'  # agar disusut setelah pembukuan
			AND invDetIsDel = 0

			GROUP BY mstPenystnBarangId
			HAVING (sisaUmur+1) > 0 AND  (nilBuku+nilSusut) > 0
			ORDER BY mstPenystnBarangId;"
		);
}

}