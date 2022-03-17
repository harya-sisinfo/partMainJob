<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');

/**
 * Controller Pengembalian_aset
 * @created : 2021-08-4 10:05:00
 * @author  : Sriharyo <sriharyo@ugm.ac.id>
 * @company : DSSDI UGM
*/

use Dompdf\Dompdf;

class Pengembalian_aset extends UGM_Controller
{
	private $ret_css_status;
	private $ret_message;
	private $ret_url;

	public function __construct() 
	{
		parent::__construct();
		$this->aset = $this->load->database("aset", TRUE);
		$this->load->helper('custom');
		$this->load->library('response');
		$this->load->library('Encryption_lib');
		$this->load->library('Jquery_pagination');
		$this->load->library('form_validation');
		$this->load->model('Model_pengembalian_aset','model');

		$this->ret_url          = site_url('aset_manajemen_barang/pengembalian_aset/view');
		$this->date_today       = date("Y-m-d H:i:s");
		$this->total_page       = 20;

		$this->user_id          = $this->session->userdata('__user_id');
		$this->kode_unit        = $this->session->userdata('__objUser')->unit_kerja_kode;
		$this->user_level       = $this->session->userdata('__objUser')->user_level;
	}
	public function index()
	{
		redirect('aset_manajemen_barang/pengembalian_aset/view');
	}

	public function view(){
		$data['linkPrintPdf']  	= site_url('aset_manajemen_barang/pengembalian_aset/cetak_pdf');
		$data['linkView'] 		= site_url('aset_manajemen_barang/pengembalian_aset/view');
		$data['linkDataTable'] 	= site_url('aset_manajemen_barang/pengembalian_aset/view_dtable');
		$this->template->load_view("view", $data);
	}

	public function view_dtable(){
		$getData = $this->input->get();
		$columns =	[0 => '',	
		1 => 'sewa_nomor',	
		2 => 'sewa_tanggal_awal',	
		3 => 'sewa_waktu_awal',	
		4 => 'sewa_tanggal_akhir', 
		5 => 'sewa_waktu_akhir',
		6 => '' ];
		$search = $getData['search']['value'];
		if ($columns[$getData['order'][0]['column']] == 'sewa_nomor') { // default order
			$order = $columns[$getData['order'][0]['column']] . " " . 'DESC';
		} else {
			$order = $columns[$getData['order'][0]['column']] . " " . $getData['order'][0]['dir'];
		}
		
		$offset = $getData['start'];
		$limit = $getData['length'];

		$totalRows = $this->model->get_num_data($search)['jumlah'];
		$dataQuery = $this->model->get_data($limit, $offset, $search, $order);

		$data = [];

		if ($dataQuery) {
			foreach ($dataQuery as $idx => $row) {
				$encId 		= $this->encryption_lib->urlencode($row['sewa_id']);
				$linkUpdate = site_url('aset_manajemen_barang/pengembalian_aset/form/'.$encId);
				$linkCetakPdf = site_url('aset_manajemen_barang/pengembalian_aset/cetak_pdf/'.$encId);
				if($row['status_pengembalian']=='Belum'){
					$btnPengembalian = '<a 
					href="'.$linkUpdate.'" 
					class="btn btn-warning btn-sm tooltip-trigger">
					<span
					class="fa fa-check"></span>
					</a>
					';
					$btnPdf = "";
					$btnDetail 		="";
				}else{
					$btnPengembalian = '';
					$btnPdf = '<a 
					href="'.$linkCetakPdf.'" 
					target="BLANK_"
					class="btn btn-primary btn-sm tooltip-trigger">
					<span
					class="fa fa-download"></span>
					</a>
					';
					$btnDetail 		= '
						<a 
						href=""
						class="btn btn-info btn-sm tooltip-trigger"
						data-toggle="tooltip"
						data-placement="top"
						title="Detail"
						rel="async"
						ajaxify="'.
						modal(
							'Detail Pengembalian',
							'aset_manajemen_barang',
							'pengembalian_aset',
							'view_detail',
							$encId).'"
						>
						<span class="fa fa-info-circle"></span>
						</a>&nbsp;
						';
				}
				
				$data[] = [
					$idx + 1 + $offset
					, $row['sewa_nomor']
					, tgl_indo($row['sewa_tanggal_awal'])
					, $row['sewa_waktu_awal']
					, tgl_indo($row['sewa_tanggal_akhir'])
					, $row['sewa_waktu_akhir']
					, $btnPengembalian.$btnPdf.$btnDetail
				];
			}
		}
		$jsonData = [
			"draw" => intval($getData['draw']),
			"recordsTotal" => intval($totalRows),
			"recordsFiltered" => intval($totalRows),
			"data" => isset($data)?$data:''
		];
		echo json_encode($jsonData);

	}

	public function form($encId){
		$data['id'] 			= $this->encryption_lib->urldecode($encId);

		if ($this->input->method(true) == 'POST') {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('tanggal', 'tanggal pengembalian ', 'required');
			$this->form_validation->set_rules('keterangan', 'keterangan ', 'required');
			$this->form_validation->set_message('required', 'Isian %s Belum Dilengkapi.');
			if ($this->form_validation->run($this) == TRUE) {
				extract($this->input->post());
				$this->aset->trans_start();
				$generate = 'PSA'.'-'.date('Ymd-His');
				$data_insert = array(
					'pengembSewaSewaId' => $sewa_id,
					'pengembSewaNomor'=> $generate,
					'pengembSewaKeterangan'=> $keterangan,
					'pengembSewaTglPengembalian'=> date('Y-m-d',strtotime(str_replace('/', '-',substr($tanggal,0,10)))),
					'pengembSewaTgl'=> $this->date_today,
					'pengembSewaUserId'=> $this->model->userAsetSession()['user_id']
				);
				$insert_id = $this->model->insert_data_id('aset_sewa_pengembalian',$data_insert);

				foreach ($sewa_detail_id as $rowIdx => $value) {
					$data_detail_insert = array(
						'pengembSewaDetMstId' => $insert_id,
						'pengembSewaDetBiayaSewaId' => $asetId[$rowIdx],
						'pengembSewaDetBiayaRusak' => $rusak[$rowIdx],
						'pengembSewaDetBiayaGanti' => $ganti[$rowIdx],
						'pengembSewaDetKeterangan' => $subKeterangan[$rowIdx]
					);
					$this->model->insert_data('aset_sewa_pengembalian_detil',$data_detail_insert);
				}

				$this->aset->trans_complete();
				if ($this->aset->trans_status() === TRUE) {
					$this->aset->trans_commit();
					$this->notice("Berhasil mengubah data", "success");
					redirect('aset_manajemen_barang/pengembalian_aset/view');
				} else {
					$this->aset->trans_rollback();
					$this->notice("Gagal menyimpan ke dalam database", "danger");
					redirect('aset_manajemen_barang/pengembalian_aset/view');
				}
			}else{
				$ret_css_status = 'error';
				$ret_message = 'Proses gagal, silahkan mengikuti aturan isian yang ditetapkan.';
				echo json_encode(array('csrf_name' => $this->security->get_csrf_token_name(), 'csrf_value' => $this->security->get_csrf_hash(), 'status' => $ret_css_status, 'msg' => $ret_message, 'dest' => '#validation_approval_mutasi_barang', 'html' => validation_errors()));
			}
		}else{
			$data['url_action'] 	= site_url('aset_manajemen_barang/pengembalian_aset/form/'.$encId);
			$data['url_back'] 	= site_url('aset_manajemen_barang/pengembalian_aset/view');

			$data['content'] = $this->model->get_data_pengembalian_aset_by_id($data['id']);
			foreach ($data['content'] as $key => $row) {
				$data['keterangan'] 	= $row['keterangan'];
				$data['sewa_id'] 		= $row['sewa_id'];
			}

			$this->template->load_view('form', $data);
		}
	}

	public function cetak_pdf($encId)
	{
		$this->load->library('encryption_data');
		// =======================================
		$data['id'] 			= $this->encryption_lib->urldecode($encId);
		$data['content'] = $this->model->get_pengembalian($data['id']);
		foreach ($data['content'] as $key_ => $row_) {
			$data['kembali_nomor'] 		= $row_['kembali_nomor'];
			$data['kembali_tanggal'] 	= ($row_['kembali_tanggal']!='0000-00-00')?tgl_indo($row_['kembali_tanggal']):'';
			$data['kembali_keterangan'] = $row_['kembali_keterangan'];
		}
		$data['kembali_nama'] 		= $this->model->userAsetSession()['user_nama'];
		// =======================================

		$path = './ugmfw-assets/images/logo-ugm-hp.png';
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$dataImg = file_get_contents($path);
		$data['logo'] = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);

		$data['header'] = "Daftar Pengembalian Aset";

		$html = $this->load->view('aset_manajemen_barang/pengembalian_aset/cetak_pdf', $data,true);
		$dompdf = new Dompdf();
		$dompdf->loadHtml($html);
		$dompdf->render();
		$dompdf->stream("penyusutan_aset",array("Attachment"=>0));

	}

	public function view_detail($encId)
	{
		$data['id'] 	= $this->encryption_lib->urldecode($encId);
		$data['content'] = $this->model->get_pengembalian($data['id']);
		foreach ($data['content'] as $key_ => $row_) {
			$data['kembali_nomor'] 		= $row_['kembali_nomor'];
			$data['kembali_tanggal'] 	= ($row_['kembali_tanggal']!='0000-00-00')?tgl_indo($row_['kembali_tanggal']):'';
			$data['kembali_keterangan'] = $row_['kembali_keterangan'];
		}
		
		$data['kembali_nama'] 		= $this->model->userAsetSession()['user_nama'];

		$this->load->view('view_detail', $data);
	}
}