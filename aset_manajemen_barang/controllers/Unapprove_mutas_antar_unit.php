<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');

/**
 * Controller Unapprove_mutas_antar_unit
 * @created : 2021-08-4 10:05:00
 * @author  : Sriharyo <sriharyo@ugm.ac.id>
 * @company : DSSDI UGM
*/

class Unapprove_mutas_antar_unit extends UGM_Controller
{
	private $ret_css_status;
	private $ret_message;
	private $ret_url;

	public function __construct() 
	{
		parent::__construct();
		$this->load->helper('custom');
		$this->load->library('response');
		$this->load->library('Encryption_lib');
		$this->load->library('Jquery_pagination');
		$this->load->library('form_validation');
		$this->load->model('Model_unapprove_mutas_antar_unit','model');

		$this->ret_url          = site_url('aset_manajemen_barang/unapprove_mutas_antar_unit/view');
		$this->date_today       = date("Y-m-d H:i:s");
		$this->total_page       = 20;

		$this->user_id          = $this->session->userdata('__user_id');
		$this->kode_unit        = $this->session->userdata('__objUser')->unit_kerja_kode;
		$this->user_level       = $this->session->userdata('__objUser')->user_level;
	}

	public function index()
	{
		redirect('aset_manajemen_barang/unapprove_mutas_antar_unit/view');
	}

	public function view(){
		$data['dataUnitKerja'] 	= $this->model->get_unit_kerja()->result_array();
		$data['tglAwal_'] 		= (!empty($this->input->get('tglAwal_')))?$this->input->get('tglAwal_'):'';
		$data['tglAkhir_'] 		= (!empty($this->input->get('tglAkhir_')))?$this->input->get('tglAkhir_'):'';
		$data['ba_mutasi_'] 	= (!empty($this->input->get('ba_mutasi_')))?$this->input->get('ba_mutasi_'):'';
		$data['unit_kerja_'] 	= (!empty($this->input->get('unit_kerja_')))?$this->input->get('unit_kerja_'):'';
		$data['lastState'] 		= (!empty($lastState))?$this->encryption_lib->urldecode($lastState):'';

		$data['linkView'] 		= site_url('aset_manajemen_barang/unapprove_mutas_antar_unit/view');
		$data['linkDataTable'] 	= site_url('aset_manajemen_barang/unapprove_mutas_antar_unit/view_dtable');
		$this->template->load_view("view", $data);
	}

	public function view_dtable(){
		$getData = $this->input->get();
		$columns =	[	0 => '',
		1 => 'berita_acara',	
		2 => 'tgl',	
		3 => 'unitAsal',	
		4 => 'unitTujuan', 
		5 => 'jmlBrg', 
		6 => 'pic',
		7 => 'isApproved',
		8 => '' ];

		$search = $getData['search']['value'];
		if ($columns[$getData['order'][0]['column']] == 'berita_acara') { // default order
			$order = $columns[$getData['order'][0]['column']] . " " . 'DESC';
		} else {
			$order = $columns[$getData['order'][0]['column']] . " " . $getData['order'][0]['dir'];
		}
		$offset = $getData['start'];
		$limit = $getData['length'];
		$params = [
			'tglAwal'    	=> $getData['tglAwal'],
			'tglAkhir' 		=> $getData['tglAkhir'],
			'ba_mutasi'    => $getData['ba_mutasi'],
			'unit_kerja'   => $getData['unit_kerja']
		];
		$totalRows = $this->model->get_num_data($params,$search)['jumlah'];
		$dataQuery = $this->model->get_data($limit, $offset, $search, $order,  $params);

		$data = [];

		if ($dataQuery) {
			foreach ($dataQuery as $idx => $row) {
				$encId 		= $this->encryption_lib->urlencode($row['id']);
				$linkDelete = site_url('aset_manajemen_barang/unapprove_mutas_antar_unit/delete_proses?encId='.$encId);
				$data[] = [
					$idx + 1 + $offset
					, $row['berita_acara']
					, $row['tgl']
					, $row['unitAsal']
					, $row['unitTujuan']
					, $row['jmlBrg']
					, $row['pic']
					, $row['isApproved']
					, '<a 
							href="'.$linkDelete.'" 
							class="btn btn-danger btn-sm tooltip-trigger btn-unapprove">
							<span class="fa fa-check-circle"></span>
						</a>
						'
				];
			}
		}
		$jsonData = [
			"draw" => intval($getData['draw']),
			"recordsTotal" => intval($totalRows),
			"recordsFiltered" => intval($totalRows),
			"data" => $data
		];
		echo json_encode($jsonData);
	}
		// ==========================================================
	public function delete_proses()
	{
		$getData = $this->input->get();
		$this->load->library('response');
		$id = $this->encryption_lib->urldecode($getData['encId']);
		

		$data_mutasi = array(
			'mtsAsetMstApproved' 		=> NULL,
			'mstAsetMstTglApproved' 	=> NULL,
			'mtsAsetMstApproveUserId' 	=> NULL
		);

		$rs_mts = $this->model->update_data('mutasi_aset_mst','mtsAsetMstId',$id,$data_mutasi);

		if ($rs_mts) {
			$this->ret_css_status = 'notice';
			$this->ret_message = 'Hapus data berhasil';
		} else {
			$this->ret_css_status = 'error';
			$this->ret_message = 'Hapus data gagal';
		}

		$this->response->script("$.growl.{$this->ret_css_status}({message: '{$this->ret_message}', size: 'large'});");
		$this->response->send();
	}
}