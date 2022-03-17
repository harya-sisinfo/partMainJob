<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');

/**
 * Controller Mutasi_antar_unit
 * @created : 2021-08-4 10:05:00
 * @author  : Sriharyo <sriharyo@ugm.ac.id>
 * @company : DSSDI UGM
 */

class Mutasi_antar_unit extends UGM_Controller
{
	private $aset;
	private $ret_css_status;
	private $ret_message;
	private $ret_url;
	private $u;
	private $data_file_uploaded;
	private $ci_last_generate;

	public function __construct()
	{
		parent::__construct();
		$this->aset = $this->load->database("aset", TRUE);
		$this->load->helper('custom');
		$this->load->library('response');
		$this->load->library('Encryption_lib');
		$this->load->library('Jquery_pagination');
		$this->load->library('form_validation');
		$this->load->model('Model_mutasi_antar_unit', 'model');

		$this->ret_url          = site_url('aset_manajemen_barang/mutasi_antar_unit/view');
		$this->u = '/media/data/aset/mutasi_antar_unit';
		
		// $this->u = $this->config->item('upload_url') . 'mutasi_antar_unit';
		$this->date_today       = date("Y-m-d H:i:s");
		$this->total_page       = 20;

		$this->user_id          = $this->session->userdata('__user_id');
		$this->kode_unit        = $this->session->userdata('__objUser')->unit_kerja_kode;
		$this->user_level       = $this->session->userdata('__objUser')->user_level;
	}
	public function index()
	{
		redirect('aset_manajemen_barang/mutasi_antar_unit/view');
	}
	// ==========================================================
	// ** Menampilkan data mutasi antar unit
	public function view()
	{
		// .:: Filter ::.
		$data['dataUnitKerja'] 		= $this->model->get_unit_kerja()->result_array();

		$data['tglAwal_'] 		= (!empty($this->input->get('tglAwal_'))) ? $this->input->get('tglAwal_') : '';
		$data['tglAkhir_'] 		= (!empty($this->input->get('tglAkhir_'))) ? $this->input->get('tglAkhir_') : '';
		$data['ba_mutasi_'] 		= (!empty($this->input->get('ba_mutasi_'))) ? $this->input->get('ba_mutasi_') : '';
		$data['unit_kerja_'] 	= (!empty($this->input->get('unit_kerja_'))) ? $this->input->get('unit_kerja_') : '';

		$data['lastState'] = (!empty($lastState)) ? $this->encryption_lib->urldecode($lastState) : '';

		// .:: Link Add ::.
		$data['linkAdd'] = site_url('aset_manajemen_barang/mutasi_antar_unit/add');

		// .:: Link View ::.
		$data['linkView'] = site_url('aset_manajemen_barang/mutasi_antar_unit/view');

		// .:: Link Datatable ::.
		$data['linkDataTable'] = site_url('aset_manajemen_barang/mutasi_antar_unit/view_dtable');

		$this->template->load_view("view", $data);
	}
	public function view_dtable()
	{
		$getData = $this->input->get();
		$columns =	[
			0 => '',
			1 => 'berita_acara',
			2 => 'tgl',
			3 => 'unitAsal',
			4 => 'unitTujuan',
			5 => 'jmlBrg',
			6 => 'pic',
			7 => 'isApproved',
			8 => ''
		];

		$search = $getData['search']['value'];
		if ($columns[$getData['order'][0]['column']] == 'a.mtsAsetMstTglMutasi') { // default order
			$order = $columns[$getData['order'][0]['column']] . " " . 'DESC';
		} elseif ($columns[$getData['order'][0]['column']] == 'mtsAsetMstBAMutasi') {
			$order = $columns[$getData['order'][0]['column']] . " " . $getData['order'][0]['dir'];
		} else {
			$order = $columns[$getData['order'][0]['column']] . " " . $getData['order'][0]['dir'];
		}
		$offset = $getData['start'];
		$limit = $getData['length'];
		$lastState =
			'?tglAwal=' . $getData['tglAwal'] .
			'&tglAkhir=' . $getData['tglAkhir'] .
			'&ba_mutasi=' . $getData['ba_mutasi'] .
			'&unit_kerja=' . $getData['unit_kerja'];
		$modalLastState = $this->encryption_lib->urlencode($getData['tglAwal'] . '||' . $getData['tglAkhir'] . '||' . $getData['ba_mutasi'] . '||' . $getData['unit_kerja']);
		$params = [
			'tglAwal' 		=> (!empty($getData['tglAwal'])) ? date('Y-m-d', strtotime($getData['tglAwal'])) : '',
			'tglAkhir' 		=> (!empty($getData['tglAkhir'])) ? date('Y-m-d', strtotime($getData['tglAkhir'])) : '',
			'ba_mutasi' 	=> $getData['ba_mutasi'],
			'unit_kerja' 	=> $getData['unit_kerja']
		];
		$totalRows = $this->model->get_num_data($params, $search);
		$dataQuery = $this->model->get_data($limit, $offset, $search, $order,  $params);

		$data = [];

		if ($dataQuery) {
			foreach ($dataQuery as $idx => $row) {
				$encId 		= $this->encryption_lib->urlencode($row['id']);
				$linkUpdate = site_url('aset_manajemen_barang/mutasi_antar_unit/update/' . $encId . $lastState);
				$linkDelete = site_url('aset_manajemen_barang/mutasi_antar_unit/delete_proses?encId=' . $encId);
				$button_detail 		= '
				<a 
					href=""
					class="btn btn-info btn-sm tooltip-trigger"
					data-toggle="tooltip"
					data-placement="top"
					title="Detail"
					rel="async"
					ajaxify="' .
					modal(
						'Detail Mutasi',
						'aset_manajemen_barang',
						'mutasi_antar_unit',
						'detail',
						$encId
					) . '"
				>
				<span class="fa fa-info-circle"></span>
				</a>&nbsp;
				';
				$button_approval 	= '	
				<a
					href=""
					class="btn btn-warning btn-sm tooltip-trigger"
					data-toggle="tooltip"
					data-placement="top"
					title="Approval"
					rel="async"
					ajaxify="' .
					modal(
						'Approval Mutasi',
						'aset_manajemen_barang',
						'mutasi_antar_unit',
						'approval',
						$encId,
						$modalLastState
					) . '"
				>
				<span class="fa fa-check-circle"></span>
				</a>&nbsp;';
				$button_update = '
				<a 
					href="' . $linkUpdate . '"
					class="btn btn-success btn-sm tooltip-trigger"
					data-toggle="tooltip"
					data-placement="top"
					title="Ubah data"
				>
				<span class="fa fa-pencil"></span>
				</a>&nbsp;';
				$button_delete = '
				<a
				href="' . $linkDelete . '"
				class="btn btn-danger btn-sm tooltip-trigger btn-hapus"
				data-toggle="tooltip" 
				data-placement="top" 
				title="Hapus"
				>
				<span class="fa fa-trash-o"></span>
				</a>';

				if ($this->kode_unit === $row['unitAsalKode']) {
					$statusUser = 'pengerim';
				} else {
					$statusUser = 'penerima';
				}




				if ($row['isApproved'] == 'Y') {
					switch ($row['isApprovedPenerima']) {
						case 'Y':
							$status_approval 	= "
								<span class='label label-success'>
								Disetujui unit asal
								</span>
								<span class='label label-success'>
								Disetujui unit penerima
								</span>
								";
							$btn_approval 	= '';
							$btn_update 	= '';
							$btn_delete 	= '';
							break;
						case 'YC':
							$status_approval 	= "
								<span class='label label-success'>
								Disetujui unit asal
								</span>
								<span class='label label-warning'>
								Disetujui sebagian oleh unit penerima
								</span>
								";
							if ($statusUser == 'penerima') {
								$btn_approval 		= $button_approval;
								$btn_update 		= '';
								$btn_delete 		= '';
							} else {
								$btn_approval 		= '';
								$btn_update 		= '';
								$btn_delete 		= '';
							}
							break;
						case 'N':
							$status_approval 	= "
								<span class='label label-success'>
								Disetujui unit asal
								</span>
								<span class='label label-danger'>
								Tidak disetujui unit penerima
								</span>
								";
							break;
							$btn_approval 		= '';
							$btn_update 		= '';
							$btn_delete 		= '';
						case 'C':
							$status_approval 	= "
								<span class='label label-success'>
								Disetujui unit asal
								</span>&nbsp;
								<span class='label label-warning'>
								Proses pengecekan unit penerima
								</span>
								";
							if ($statusUser == 'penerima') {
								$btn_approval 		= $button_approval;
								$btn_update 		= '';
								$btn_delete 		= '';
							} else {
								$btn_approval 		= '';
								$btn_update 		= '';
								$btn_delete 		= '';
							}
							break;
						default:
							$status_approval 	= "<span class='label label-success'>
								Disetujui Unit Asal
								</span>&nbsp;<span class='label label-warning'>
								Belum disetujui unit penerima
								</span>";
							if ($statusUser == 'penerima') {
								$btn_approval 		= $button_approval;
								$btn_update 		= '';
								$btn_delete 		= '';
							} else {
								$btn_approval 		= '';
								$btn_update 		= '';
								$btn_delete 		= '';
							}
							break;
					}
				} elseif ($row['isApproved'] == 'N') {
					$status_approval 	= "
						<span class='label label-danger'>
							Dibatalkan oleh unit asal
						</span>
					";
					$btn_approval 		= '';
					$btn_update 		= '';
					$btn_delete 		= '';
				} else {
					$status_approval 	= "
						<span class='label label-danger'>
							Belum Approve Unit Asal
						</span>
						<span class='label label-danger'>
							Belum Approve Unit Penerima
						</span>
					";
					if ($statusUser == 'penerima') {
						$btn_approval 	= '';
						$btn_update 	= '';
						$btn_delete 	= '';
					} elseif ($statusUser == 'pengerim') {
						$btn_approval 	= $button_approval;
						$btn_update 	= $button_update;
						$btn_delete 	= $button_delete;
					}
				}

				$btn_detail 	= $button_detail;



				$data[] = [
					$idx + 1 + $offset, $row['berita_acara'], tgl_indo($row['tgl']), $row['unitAsal'], $row['unitTujuan'], $row['jmlBrg'], $row['pic'], $status_approval
					//.'->isApproved : '.$row['isApproved'].'->statusUser : '.$statusUser.'->isApprovedPenerima : '.$row['isApprovedPenerima']
					,
					$btn_detail . $btn_approval . $btn_update . $btn_delete
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
	// ** Tambah data mutasi antar unit
	public function add()
	{
		$getData 					= $this->input->get();
		$data['data_unit_asal'] 	= $this->model->get_unit_kerja('asal')->result_array();
		$data['data_unit_tujuan'] 	= $this->model->get_unit_kerja('tujuan')->result_array();

		$tglAwal 	= (!empty($getData['tglAwal']) ? $getData['tglAwal'] : date('Y' . '-01-01'));
		$tglAkhir 	= (!empty($getData['tglAkhir']) ? $getData['tglAkhir'] : $this->date_today);
		$ba_mutasi 	= (!empty($getData['ba_mutasi']) ? $getData['ba_mutasi'] : '');
		$unit_kerja = (!empty($getData['unit_kerja']) ? $getData['unit_kerja'] : '');

		$lastState = 	'?tglAwal_=' . $tglAwal .
			'&tglAkhir_=' . $tglAkhir .
			'&ba_mutasi_=' . $ba_mutasi .
			'&unit_kerja_=' . $unit_kerja;

		$data['url_action']		= site_url('aset_manajemen_barang/mutasi_antar_unit/add_proses/') . $this->encryption_lib->urlencode($lastState);
		$data['url_back']		= site_url('aset_manajemen_barang/mutasi_antar_unit/view') . $lastState;
		$data['form_label'] 	= 'Tambah Data Mutasi Antar Unit';
		$data['isProses'] 		= 'add';

		$this->template->load_view('add', $data);
	}

	public function add_proses($lastState_)
	{
		$lastState = $this->encryption_lib->urldecode($lastState_);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('unit_asal', 'Unit Kerja Asal', 'required');
		$this->form_validation->set_rules('unit_tujuan', 'Unit Kerja Tujuan', 'required');
		$this->form_validation->set_rules('ba_mutasi', 'Nomor BA Mutasi', 'required');
		$this->form_validation->set_rules('tanggal_mutasi', 'Tanggal Mutasi', 'required');
		$this->form_validation->set_rules('pic', 'PIC', 'required');
		$this->form_validation->set_rules('nip', 'NIP', 'required');
		$this->form_validation->set_rules('detail_', 'List barang ', 'required');
		$this->form_validation->set_message('required', 'Isian %s Belum Dilengkapi.');
		if ($this->form_validation->run($this) == TRUE) {
			$this->aset->trans_begin();
			//$rsUpload = $this->do_upload_file();
			extract($this->input->post());

			// * Upload doc
			if ($_FILES["ba_file"]["tmp_name"][0]) {
				$upload = $this->do_upload_file();
			} else {
				$upload['status'] = true;
				$upload['file_name'] = '';
			}
			if ($upload['status'] === true) {
				$nama_file = $upload['file_name'];
			} else {
				$nama_file = '';
			}

			$data_mutasi_master =
				array(
					'mtsAsetMstUnitIdAsal' 		=> $unit_asal,
					'mtsAsetMstLokasiIdAsal' 	=> null,
					'mtsAsetMstUnitIdTuj' 		=> $unit_tujuan,
					'mtsAsetMstLokasiIdTuj' 	=> null,
					'mtsAsetMstTglMutasi' 		=> date('Y-m-d', strtotime(str_replace('/', '-', substr($tanggal_mutasi, 0, 10)))),
					'mtsAsetMstPIC' 			=> $pic,
					'mtsAsetMstNIP' 			=> $nip,
					'mtsAsetMstBAMutasi' 		=> $ba_mutasi,
					'mtsAsetMstFile' 			=> $nama_file,
					'mtsAsetMstKet' 			=> $keterangan,
					'mstAsetMstTglUbah' 		=> $this->date_today,
					'mstAsetMstUserId' 			=> $this->model->userAsetSession()['user_id']
				);
			$mutasi_master_id = $this->model->insert_data_id('mutasi_aset_mst', $data_mutasi_master);
			foreach ($barang_id as $idx => $value) {
				$data_mutasi_detail = array(
					'mtsAsetDetMstId' 	=> $mutasi_master_id,
					'mtsAsetDetBrgId' 	=> $barang_id[$idx],
					'mtsAsetDetKondId' 	=> $kondisi[$idx]
				);
				$this->model->insert_data('mutasi_aset_det', $data_mutasi_detail);
			}



			if ($this->aset->trans_status() === TRUE) {
				$this->aset->trans_commit();
				if ($upload['status'] === false) {
					$err_upload = ' , <font color="red">Maaf proses upload gagal dilakukan.</font>.';
				} else {
					$err_upload = '.';
				}
				$this->ret_css_status 	= 'success';
				$this->ret_message 		= 'Ubah Data berhasil' . $err_upload;
			} else {
				$this->aset->trans_rollback();
				$this->ret_css_status = 'danger';
				$this->ret_message = 'Ubah Data gagal';
			}
			$this->ret_url = site_url('aset_manajemen_barang/mutasi_antar_unit/view' . $lastState);
			echo json_encode(['status' => $this->ret_css_status, 'msg' => $this->ret_message, 'url' => $this->ret_url]);
			exit();
		} else {
			$getData = $this->input->get();

			$data['data_unit_asal'] 		= $this->model->get_unit_kerja('asal')->result_array();
			$data['data_unit_tujuan'] 	= $this->model->get_unit_kerja('tujuan')->result_array();

			$data['url_action']	= site_url('aset_manajemen_barang/mutasi_antar_unit/add_proses/') . $lastState_;
			$data['url_back']		= site_url('aset_manajemen_barang/mutasi_antar_unit/view') . $lastState;
			$data['form_label'] 	= 'Tambah Data Mutasi Antar Unit';
			$data['isProses'] 	= 'add';

			$this->template->load_view('add', $data);
		}
	}

	// ==========================================================
	// ** Ubah data mutasi antar unit
	public function update($encId)
	{
		$getData 				= $this->input->get();
		$data['id'] 			= $this->encryption_lib->urldecode($encId);
		$data['detail_mutasi']	= $this->model->get_detail_mutasi($data['id']);

		$tglAwal 	= (!empty($getData['tglAwal']) ? $getData['tglAwal'] : date('Y' . '-01-01'));
		$tglAkhir 	= (!empty($getData['tglAkhir']) ? $getData['tglAkhir'] : $this->date_today);
		$ba_mutasi 	= (!empty($getData['ba_mutasi']) ? $getData['ba_mutasi'] : '');
		$unit_kerja = (!empty($getData['unit_kerja']) ? $getData['unit_kerja'] : '');

		$data['data_unit_asal'] 	= $this->model->get_unit_kerja('asal')->result_array();
		$data['data_unit_tujuan'] 	= $this->model->get_unit_kerja('tujuan')->result_array();

		foreach ($data['detail_mutasi'] as $key => $row) {
			$data['unit_asal_id'] 	= $row['unit_asal_id'];
			$data['unit_tujuan_id'] = $row['unit_tujuan_id'];
			$data['ba_mutasi'] 		= $row['ba_mutasi'];
			$data['tanggal_mutasi'] = $row['tgl'];
			$data['pic'] 			= $row['pic'];
			$data['nip'] 			= $row['nip'];
			$data['keterangan'] 	= $row['keterangan'];
			$data['file_mutasi'] 	= $row['FILE'];
			$data['approval'] 		= $row['mtsAsetMstApproved'];
		}
		$data['dataUnitKerja'] 	= $this->model->get_unit_kerja()->result_array();

		$lastState 				= 	'?tglAwal_=' . $getData['tglAwal'] .
			'&tglAkhir_=' . $getData['tglAkhir'] .
			'&ba_mutasi_=' . $getData['ba_mutasi'] .
			'&unit_kerja_=' . $getData['unit_kerja'];

		$data['url_action'] 	= site_url('aset_manajemen_barang/mutasi_antar_unit/update_proses/') . $encId . '/' . $this->encryption_lib->urlencode($lastState);
		$data['url_back']		= site_url('aset_manajemen_barang/mutasi_antar_unit/view') . $lastState;
		$data['url_file']		= (!empty($data['file_mutasi'])) ? base_url() . $this->u . '/' . $data['file_mutasi'] : '';


		$data['form_label'] 	= 'Ubah Data Mutasi Antar Unit';
		$data['isProses'] 		= 'update';
		$this->template->load_view('update', $data);
	}

	public function update_proses($encId, $lastState_)
	{
		$id 			= $this->encryption_lib->urldecode($encId);
		$lastState 		= $this->encryption_lib->urldecode($lastState_);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('unit_asal', 'Unit Kerja Asal', 'required');
		$this->form_validation->set_rules('unit_tujuan', 'Unit Kerja Tujuan', 'required');
		$this->form_validation->set_rules('ba_mutasi', 'Nomor BA Mutasi', 'required');
		$this->form_validation->set_rules('tanggal_mutasi', 'Tanggal Mutasi', 'required');
		$this->form_validation->set_rules('detail_', 'List barang ', 'required');
		$this->form_validation->set_rules('pic', 'PIC', 'required');
		$this->form_validation->set_rules('nip', 'NIP', 'required');
		$this->form_validation->set_message('required', 'Data %s Belum Dilengkapi.');
		if ($this->form_validation->run($this) == TRUE) {
			$this->aset->trans_begin();
			extract($this->input->post());
			/*update_mutasi*/
			// * Upload doc
			if ($_FILES["ba_file"]["tmp_name"][0]) {
				$upload = $this->do_upload_file();
				$oldFilename = $this->input->post('old_ba_file', TRUE);
				$pathFile = realpath(FCPATH . $this->u . '/' . $oldFilename);
				if (file_exists($pathFile) && !empty($oldFilename)) {
					unlink($pathFile);
				}
			} else {
				$upload['status'] = true;
				$upload['file_name'] = '';
			}
			if ($upload['status'] === true) {
				$nama_file = $upload['file_name'];
			} else {
				$nama_file = '';
			}
			$data_mutasi = array(
				'mtsAsetMstUnitIdAsal' 		=> $unit_asal,
				'mtsAsetMstLokasiIdAsal' 	=> null,
				'mtsAsetMstUnitIdTuj' 		=> $unit_tujuan,
				'mtsAsetMstLokasiIdTuj' 	=> null,
				'mtsAsetMstTglMutasi' 		=> date('Y-m-d', strtotime(str_replace('/', '-', substr($tanggal_mutasi, 0, 10)))),
				'mtsAsetMstPIC' 			=> $pic,
				'mtsAsetMstNIP' 			=> $nip,
				'mtsAsetMstBAMutasi' 		=> $ba_mutasi,
				'mtsAsetMstFile' 			=> $nama_file,
				'mtsAsetMstKet' 			=> $keterangan,
				'mstAsetMstTglUbah' 		=> $this->date_today,
				'mstAsetMstUserId' 			=> $this->model->userAsetSession()['user_id']
			);
			$this->model->update_data('mutasi_aset_mst', 'mtsAsetMstId', $id, $data_mutasi);
			$this->model->delete_data('mutasi_aset_det', 'mtsAsetDetMstId', $id);
			foreach ($barang_id as $idx => $value) {
				/*if($kondisi[$idx]=="Baik"){
    				$kondisi_barang = 1;
    			}elseif($kondisi[$idx]=="Rusak Ringan"){
    				$kondisi_barang = 2;
    			}elseif($kondisi[$idx]=="Rusak Berat"){
    				$kondisi_barang = 3;
    			}else{
    				$kondisi_barang = 0;
    			}*/
				$data_mutasi_detail = array(
					'mtsAsetDetMstId' 	=> $id,
					'mtsAsetDetBrgId' 	=> $barang_id[$idx],
					'mtsAsetDetKondId' 	=> $kondisi[$idx]
				);
				$this->model->insert_data('mutasi_aset_det', $data_mutasi_detail);
			}
			if ($this->aset->trans_status() === TRUE) {
				$this->aset->trans_commit();
				if ($upload['status'] === false) {
					$err_upload = ' , <font color="red">Maaf proses upload gagal dilakukan.</font>.';
				} else {
					$err_upload = '.';
				}
				$this->ret_css_status 	= 'success';
				$this->ret_message 		= 'Ubah Data berhasil' . $err_upload;

				//$this->notice("Berhasil menambah data", "success");
				//redirect('aset_manajemen_barang/mutasi_antar_unit/view'.$lastState);
			} else {
				$this->aset->trans_rollback();
				$this->ret_css_status = 'danger';
				$this->ret_message = 'Ubah Data gagal';
				//$this->notice("Gagal menyimpan ke dalam database", "danger");
				//redirect('aset_manajemen_barang/mutasi_antar_unit/view'.$lastState);
			}
			$this->ret_url = site_url('aset_manajemen_barang/mutasi_antar_unit/view' . $lastState);
			echo json_encode(['status' => $this->ret_css_status, 'msg' => $this->ret_message, 'url' => $this->ret_url]);
			exit();
		} else {
			$getData 				= $this->input->get();
			$data['id'] 			= $id;
			$data['detail_mutasi']	= $this->model->get_detail_mutasi($data['id']);

			foreach ($data['detail_mutasi'] as $key => $row) {
				$data['unit_asal_id'] 	= $row['unit_asal_id'];
				$data['unit_tujuan_id'] = $row['unit_tujuan_id'];
				$data['ba_mutasi'] 		= $row['ba_mutasi'];
				$data['tanggal_mutasi'] = $row['tgl'];
				$data['pic'] 			= $row['pic'];
				$data['nip'] 			= $row['nip'];
				$data['keterangan'] 	= $row['keterangan'];
				$data['file_mutasi'] 	= $row['FILE'];
				$data['approval'] 		= $row['mtsAsetMstApproved'];
				$data['approval'] 		= $row['mtsAsetMstApproved'];
			}

			$data['dataUnitKerja'] 	= $this->model->get_unit_kerja()->result_array();

			$data['url_action'] 	= site_url('aset_manajemen_barang/mutasi_antar_unit/update_proses/') . $encId . '/' . $this->encryption_lib->urlencode($lastState);
			$data['url_back']		= site_url('aset_manajemen_barang/mutasi_antar_unit/view') . $lastState;
			$data['form_label'] 	= 'Ubah Data Mutasi Antar Unit';
			$data['isProses'] 		= 'update';
			$this->template->load_view('update', $data);
		}
	}
	// global_fungtion 
	// ** Funstion untuk upload 
	private function do_upload_file()
	{
		$this->load->library('upload');
		$dUpload = realpath(FCPATH . DIRECTORY_SEPARATOR . $this->u); //$this->config->item('upload_url') . 'to';
		$listFile = [];
		foreach ($_FILES["ba_file"]['name'] as &$name) {
			$arrFile = explode(".", $name);
			$ext = array_pop($arrFile);
			$name = date('Ymd_His') . '_' . str_replace(" ", "_", implode("_", $arrFile)) . "." . $ext;
			$listFile[] = $name;
		}

		$this->upload->initialize([
			"allowed_types" => "jpg|jpeg|pdf",
			"upload_path" => $dUpload,
			//'file_name' => $fileName,
			'file_ext_tolower' => TRUE,
			'overwrite' => FALSE,
			'max_size' => 50000,
			'remove_spaces' => TRUE
		]);

		//Perform upload.
		if ($this->upload->do_upload('ba_file')) {
			$ret = [
				'status' => true,
				'file_name' => implode('|', $listFile)
			];
		} else {
			$ret = [
				'status' => false,
				'msg' => $this->upload->display_errors()
			];
		}
		return $ret;
	}
	// ==========================================================
	// ** Approval data mutasi antar unit
	public function approval($encId, $lastState_)
	{
		$data['id'] =  $this->encryption_lib->urldecode($encId);

		$expLastState 	= explode('||', $this->encryption_lib->urldecode($lastState_));
		$lastState 		= '?tglAwal=' . $expLastState[0] . '&tglAkhir=' . $expLastState[1] . '&ba_mutasi=' . $expLastState[2] . '&unit_kerja=' . $expLastState[3];

		$data['list'] 	= $this->model->get_detail_mutasi($data['id']);

		foreach ($data['list'] as $key => $row) {
			$data['unit_asal'] 			= $row['unit_asal'];
			$data['unit_tujuan'] 		= $row['unit_tujuan'];
			$data['unit_tujuan_id'] 	= $row['unit_tujuan_id'];
			$data['lokasi_tujuan_id'] 	= $row['lokasi_tujuan_id'];
			$data['ba_mutasi'] 			= $row['ba_mutasi'];
			$data['tgl_mutasi'] 		= $row['tgl'];
			$data['pic'] 				= $row['pic'] . ' (NIP:<b>' . $row['nip'] . '</b>)';
			$data['pic2'] 				= (!empty($row['pic2'])) ? $row['pic2'] : '';
			$data['nip2'] 				= (!empty($row['nip2'])) ? $row['nip2'] : '';
			$data['keterangan'] 		= $row['keterangan'];
			$data['file_mutasi'] 		= $row['FILE'];
			$data['url_file'] 		= ($row['FILE']) ? base_url($this->u . '/' . $row['FILE']) : '';
			$data['approval'] 			= $row['mtsAsetMstApproved'];

			$dataKodeBarang			= $this->model->get_new_kode_barang($row['unit_tujuan_id'], $row['barangId']);
			if ($row['unit_asal_kode'] == $this->kode_unit) {
				$data['status_unit'] = 'unit_asal';
			} else {
				$data['status_unit'] = 'unit_tujuan';
			}

			switch ($row['mtsAsetMstApproved']) {
				case 'Y':
					$data['approval'] = "Disetujui";
					$data['label-color'] = "label-success";
					break;
				case 'YC':
					$data['approval'] = "Menyetujui Sebagian";
					$data['label-color'] = "label-info";
					break;
				case 'N':
					$data['approval'] = "Tidak Setuju";
					$data['label-color'] = "label-danger";
					break;
				case 'C':
					$data['approval'] = "Proses pengecekan";
					$data['label-color'] = "label-info";
					break;
				default:
					$data['approval'] = "Belum Diajukan";
					$data['label_color'] = "label-warning";
					break;
			}
		}
		$data['url_action'] = site_url("aset_manajemen_barang/mutasi_antar_unit/approval_proses/" . $encId . "/" . $lastState_);
		$this->load->view('approval', $data);
	}

	public function approval_proses($encId, $lastState_)
	{
		$this->load->library('form_validation');
		$lastState 				=  $this->encryption_lib->urldecode($lastState_);
		$getPost 				= $this->input->post();
		$data['id_mutasi'] 		= $this->encryption_lib->urldecode($encId);
		$data['status_unit']	= ($getPost['status_unit']) ? $getPost['status_unit'] : '';
		$data['unit_tujuan_id']	= $getPost['unit_tujuan_id'];

		$data['lokasi_tujuan_id']		= $this->model->get_ruang_trans($getPost['unit_tujuan_id']);

		if ($data['status_unit'] == 'unit_asal') {
			$data['status_approval']	= $getPost['status_approval'];
			$this->form_validation->set_rules('status_approval', 'Status approval ', 'trim|required');
		} else {
			$this->form_validation->set_rules('pic_unit_tujuan', 'PIC ', 'trim|required');
			$this->form_validation->set_rules('nip_unit_tujuan', 'NIP ', 'trim|required');
			if (empty($this->model->get_ruang_trans($data['unit_tujuan_id']))) {
				$this->form_validation->set_rules('nip_unit_tujuan', 'Ruang TRANS ', 'cek_trans');
				$this->form_validation->set_message('cek_trans', '%s untuk Unit Penerima Belum Tersedia. Silahkan Hubungi Admin');
			}
			if (empty($getPost['input_draft'])) {
				foreach ($getPost['id_detail'] as $idx_vid => $row_vid) {
					$this->form_validation->set_rules("status_pemeriksaan_{$getPost['id_detail'][$idx_vid]}", "Status Pemeriksaan {$getPost['kode'][$idx_vid]}", 'trim|required');
				}
			}
		}

		$this->form_validation->set_message('required', 'Isian %s Belum Dilengkapi.');

		if ($this->form_validation->run($this) == TRUE) {
			if ($data['status_unit'] == 'unit_asal') {
				$this->aset->trans_begin();
				$data_mutasi = array(
					'mtsAsetMstApproved' 		=> $getPost['status_approval'],
					'mstAsetMstTglApproved' 	=> $this->date_today,
					'mtsAsetMstApproveUserId' 	=> $this->model->userAsetSession()['user_id']
				);
				$this->model->update_data('mutasi_aset_mst', 'mtsAsetMstId', $data['id_mutasi'], $data_mutasi);
				if ($this->aset->trans_status() === TRUE) {
					$this->aset->trans_commit();
					$ret_css_status = 'success';
					$ret_message = 'Penambahan data berhasil ';
					$ret_url = site_url("aset_manajemen_barang/mutasi_antar_unit/view") . $lastState;
				} else {
					$this->aset->trans_rollback();
					$ret_css_status = 'danger';
					$ret_message = 'Penambahan data gagal ';
					$ret_url = site_url('aset_manajemen_barang/mutasi_antar_unit/approval/' . $encId);
				}
				echo json_encode(array('status' => $ret_css_status, 'msg' => $ret_message, 'url' => $ret_url, 'dest' => '#subcontent-element'));
			} else {

				$data['gedungId']			= 1;
				$data['lokasi_tujuan_id'] 	= $this->model->get_ruang_trans($getPost['unit_tujuan_id'])['ruangId'];
				$data['user_id'] 			= $this->model->userAsetSession()['user_id'];
				$data['pic_unit_tujuan'] 	= $getPost['pic_unit_tujuan'];
				$data['nip_unit_tujuan'] 	= $getPost['nip_unit_tujuan'];

				foreach ($getPost['id_detail'] as $idx => $value) {
					$data['idx'][$idx]['id_detail']				= $getPost['id_detail'][$idx];
					$data['idx'][$idx]['id_barang'] 			= $getPost['id_barang'][$idx];
					$data['idx'][$idx]['kondisi'] 				= $getPost['kondisi'][$idx];
					$data['idx'][$idx]['status_pemeriksaan'] 	= (!empty($getPost['status_pemeriksaan_' . $getPost['id_detail'][$idx]]) ? $getPost['status_pemeriksaan_' . $getPost['id_detail'][$idx]] : '');
				}

				if (!empty($getPost['input_draft'])) {

					$data['status_approval_penerima'] = 'C';
					$this->response_draft($data);
					if ($this->aset->trans_status() === TRUE) {
						$this->aset->trans_commit();
						$ret_css_status = 'success';
						$ret_message = 'Penambahan data berhasil ';
						$ret_url = site_url("aset_manajemen_barang/mutasi_antar_unit/view") . $lastState;
					} else {
						$this->aset->trans_rollback();
						$ret_css_status = 'danger';
						$ret_message = 'Penambahan data gagal ';
						$ret_url = site_url('aset_manajemen_barang/mutasi_antar_unit/approval/' . $encId);
					}
				} else {

					$this->response_approve_penerima($data);

					if ($this->aset->trans_status() === TRUE) {
						$this->aset->trans_commit();
						$ret_css_status = 'success';
						$ret_message = 'Proses approval data berhasil ';
						$ret_url = site_url("aset_manajemen_barang/mutasi_antar_unit/view") . $lastState;
					} else {
						$this->aset->trans_rollback();
						$ret_css_status = 'danger';
						$ret_message = 'Proses approval data gagal ';
						$ret_url = site_url('aset_manajemen_barang/mutasi_antar_unit/approval/' . $encId);
					}
				}

				echo json_encode(array('status' => $ret_css_status, 'msg' => $ret_message, 'url' => $ret_url, 'dest' => '#subcontent-element'));
			}
		} else {
			$ret_css_status = 'error';
			$ret_message = 'Proses gagal, silahkan mengikuti aturan isian yang ditetapkan.';
			echo json_encode(array('csrf_name' => $this->security->get_csrf_token_name(), 'csrf_value' => $this->security->get_csrf_hash(), 'status' => $ret_css_status, 'msg' => $ret_message, 'dest' => '#validation_approval_mutasi_barang', 'html' => validation_errors()));
		}
	}

	public function response_draft($data)
	{
		$this->aset->trans_begin();
		foreach ($data['idx'] as $key_idx => $row_idx) {
			$dataDraft = array(
				'mtsAsetDetStatusTerima' => $row_idx['status_pemeriksaan']
			);
			$whereDraft = array(
				'mtsAsetDetMstId' => $data['id_mutasi'],
				'mtsAsetDetBrgId' => $row_idx['id_barang']
			);
			$this->model->update_data_('mutasi_aset_det', $whereDraft, $dataDraft);
		}
		$dataMutasi = array(
			'mstAsetMstApprovedPenerima' 	=> $data['status_approval_penerima'],
			'mstAsetMstTglApprovePenerima' 	=> $this->date_today,
			'mtsAsetMstPICUnitTuj' 			=> $data['pic_unit_tujuan'],
			'mtsAsetMstNIPUnitTuj' 			=> $data['nip_unit_tujuan'],
			'mtsAsetMstApprovePenerimaId' 	=> $data['user_id']
		);
		$this->model->update_data('mutasi_aset_mst', 'mtsAsetMstId', $data['id_mutasi'], $dataMutasi);
	}

	public function response_approve_penerima($data)
	{
		$this->aset->trans_begin();

		$n_s 	= 0;
		$n_ts 	= 0;
		$n 		= 0;


		foreach ($data['idx'] as $key_idx => $row_idx) {
			// Menjumlahkan hasil pemeriksaan
			$n_s 	= ($row_idx['status_pemeriksaan'] == "S") ? $n_s++ : $n_s;
			$n_ts 	= ($row_idx['status_pemeriksaan'] == "TS") ? $n_ts++ : $n_ts;
			$n++;
			// ------------------------------
			// Menyimpan data status approval penerima
			$data_approval_detail = array(
				'mtsAsetDetStatusTerima' => $row_idx['status_pemeriksaan']
			);
			$this->model->update_data('mutasi_aset_det', 'mtsAsetDetId', $row_idx['id_detail'], $data_approval_detail);

			// ------------------------------
			// Jika pemeriksaan S (Setuju)
			if ($row_idx['status_pemeriksaan'] == 'S') {
				$newKode = array();
				// generate NUP baru dan log perubahan NUP jika mutasiType = 2
				$newKode = $this->model->get_new_kode_barang($data['unit_tujuan_id'], $row_idx['id_barang']);
				if (!empty($newKode)) {
					if ($newKode['isNew'] == '1') {
						// ------------------------------
						$data_last_nup = array(
							'brgId'		=> $newKode['brgId'],
							'nup' 		=> $newKode['nup'],
							'unitId' 	=> $data['unit_tujuan_id']
						);

						$this->model->insert_data('inv_last_nup', $data_last_nup);
						// ------------------------------
					} else {
						// ------------------------------
						$data_table = array(
							'nup' => $newKode['nup'],
							'timestamp' => $this->date_today
						);
						$where_table = array(
							'brgId' => $newKode['brgId'],
							'unitId' => $data['unit_tujuan_id']
						);
						$this->model->update_data_('inv_last_nup', $where_table, $data_table);
						// ------------------------------
					}
					$oldKode = explode('|', $newKode['kodeLama']);
					if (str_replace('.', '', $oldKode[0]) !== $oldKode[1] && !empty($oldKode[1])) {
						$newKode['kodeBarcodeBaru'] = $oldKode[1];
					}
					// ------------------------------
					// * addCodeHist

					$data_inv_kode_his = array(
						'id_detail' => $row_idx['id_detail'],
						'id_barang' => $row_idx['id_barang'],
						'kodeBarangBaru' => $newKode['kodeBarangBaru'],
						'kodeBarcodeBaru' => $newKode['kodeBarcodeBaru'],
						'oldKode0' => $oldKode[0],
						'oldKode1' => $oldKode[1],
						'unit_tujuan_id' =>  $data['unit_tujuan_id']
					);
					$this->model->insert_inv_kode_his($data_inv_kode_his);

					// ------------------------------
					// add inv_det_trans | transfer keluar (302)
					$data_inv_det_trans = array(
						'params_jenis_transaksi' 	=> 302,
						'params_nilai' 				=> -1,
						'params_id_detail' 			=> $row_idx['id_detail'],
						'params_user_id' 			=> $this->model->userAsetSession()['user_id'],
						'params_barang_id'			=> $row_idx['id_barang']
					);
					$this->model->insert_data_inv_det_trans($data_inv_det_trans);
					// ------------------------------
					// add penyusutan_mutasi S03
					$addSusutMutasi = true;
					$isSusut = $this->model->getIsSusut($row_idx['id_barang']);
					if ($isSusut > 0) {
						$data_susut = array(
							'id_detail' => $row_idx['id_detail'],
							'param_1'	=> 'S03',
							'jumlah'    => 1,
							'user'   	=> $this->model->userAsetSession()['user_id'],
							'id_barang'	=> $row_idx['id_barang']
						);
						$this->model->insert_susut_mutasi($data_susut);
					}
				}
				// update data inventarisasi detail dan log perubahan lokasi
				// ------------------------------
				// UpdateDataInventarisasi
				$gedungId = 1;

				if ($newKode['kodeBarangBaru'] <> '' && $newKode['kodeBarcodeBaru'] <> '') {
					$data_inventarisasi = array(
						'invDetKodeBarang' 	=> $newKode['kodeBarangBaru'],
						'invDetBarcode' 	=> $newKode['kodeBarcodeBaru'],
						'invDetGedungId'  	=> $gedungId,
						'invDetRuanganId' 	=> $data['lokasi_tujuan_id'],
						'invDetUnitKerja' 	=> $data['unit_tujuan_id'],
						'invDetKondisiId' 	=> $row_idx['kondisi'],
						'invDetTglBuku' 	=> $this->date_today
					);
					$this->model->update_data('inventarisasi_detail', 'invDetId', $row_idx['id_barang'], $data_inventarisasi);
				} else {
					$data_inventarisasi = array(
						'invDetGedungId'  	=> $gedungId,
						'invDetRuanganId' 	=> $data['lokasi_tujuan_id'],
						'invDetUnitKerja' 	=> $data['unit_tujuan_id'],
						'invDetKondisiId' 	=> $row_idx['kondisi'],
						'invDetTglBuku' 	=> $this->date_today
					);
					$this->model->update_data('inventarisasi_detail', 'invDetId', $row_idx['id_barang'], $data_inventarisasi);
				}
				// ------------------------------
				// add inv_det_trans | transfer masuk (301)
				$data_inv_det_trans = array(
					"params_jenis_transaksi" => 102,
					"params_nilai" => 1,
					"params_id_detail" => $row_idx['id_detail'],
					"params_user_id" => $this->model->userAsetSession()['user_id'],
					"params_barang_id" => $row_idx['id_barang']
				);
				$this->model->insert_data_inv_det_trans($data_inv_det_trans);
				// ------------------------------
				// add penyusutan_mutasi (S01)
				if ($isSusut) {
					$data_susut = array(
						'id_detail' => $row_idx['id_detail'],
						'param_1'	=> 'S01',
						'jumlah'    => -1,
						'user'   	=> $this->model->userAsetSession()['user_id'],
						'id_barang' 		=> $row_idx['id_barang']
					);
					$this->model->insert_susut_mutasi($data_susut);
				}
				// ------------------------------
				// log perubahan lokasi
				$data_log_lokasi = array(
					"invHisInvDetId" => $row_idx['id_barang'],
					"invHisGedungId" => $gedungId,
					"InvHisKampusId" => "(SELECT
					invDetKampus
					FROM inventarisasi_detail
					WHERE invDetId =  '{$row_idx['id_barang']}')",
					"InvHisRuangId" => $data['lokasi_tujuan_id'],
					"InvHisTanggal" => $this->date_today,
					"InvHisMutasiId" => $data['id_mutasi'],
					"InvHisUserId" => $this->model->userAsetSession()['user_id']
				);
				$this->model->insert_data('inventarisasi_lokasi_history', $data_log_lokasi);
				// ------------------------------
				// log perubahan kondisi
				$data_log_kondisi = array(
					"invKondisiBrgBrgDetId" => $row_idx['id_barang'],
					"invKondisiBrgKondId" => $row_idx['kondisi'],
					"invKondisiBrgTglKondisi" => $this->date_today,
					"invKondisiTglHistory" => $this->date_today,
					"invKondisiUserId" => $this->model->userAsetSession()['user_id'],
					"invKondisiSrc" => 'MTS',
					"invKondisiOptId" => $row_idx['id_detail']
				);
				$this->model->insert_data('inv_kondisi_history', $data_log_kondisi);
			}
		}

		if ($n_s == $n && $n_ts == 0) $statusApprove = 'Y'; // approve semua
		if ($n_ts == $n && $n_s == 0) $statusApprove = 'N'; // tidak approve
		if ($n_s > 0 && $n_ts > 0) $statusApprove = 'YC'; // approve sebagian

		if ($n_ts == 0) {
			$statusApprove = 'Y';
		} elseif ($n_s == 0) {
			$statusApprove = 'N';
		} elseif ($n_s > 0 && $n_ts > 0) {
			$statusApprove = 'YC';
		}


		$data_mutasi = array(
			'mtsAsetMstPICUnitTuj' 			=> $data['pic_unit_tujuan'],
			'mtsAsetMstNIPUnitTuj' 			=> $data['nip_unit_tujuan'],
			'mstAsetMstTglApprovePenerima' 	=> $this->date_today,
			'mtsAsetMstApprovePenerimaId' 	=> $this->model->userAsetSession()['user_id'],
			'mstAsetMstApprovedPenerima' 	=> $statusApprove
		);

		$this->model->update_data('mutasi_aset_mst', 'mtsAsetMstId', $data['id_mutasi'], $data_mutasi);
	}

	// ==========================================================
	public function delete_proses()
	{
		$getData = $this->input->get();
		$this->load->library('response');
		$id = $this->encryption_lib->urldecode($getData['encId']);
		$data = $this->model->get_detail_mutasi($id);

		$rs_mts = $this->model->delete_data('mutasi_aset_mst', 'mtsAsetMstId', $id);
		$rs_det = $this->model->delete_data('mutasi_aset_det', 'mtsAsetDetMstId', $id);

		if ($rs_mts && $rs_det) {
			$this->ret_css_status = 'notice';
			$this->ret_message = 'Hapus data berhasil';
		} else {
			$this->ret_css_status = 'error';
			$this->ret_message = 'Hapus data gagal';
		}

		$this->response->script("$.growl.{$this->ret_css_status}({message: '{$this->ret_message}', size: 'large'});");
		$this->response->send();
	}


	public function list_barang($offset = 0, $unit_asal, $search_ = '')
	{
		$data['unit_asal'] = $unit_asal;

		$search = (!empty($search_)) ? $search_ : (!empty($this->input->post('search'))) ? $this->input->post('search') : '';
		$data['search'] = $search;
		$this->load->library('jquery_pagination');
		$config['base_url'] = site_url('aset_manajemen_barang/mutasi_antar_unit/list_barang/');
		$config['per_page'] = 10;
		$config['url_location'] = 'modal-data-basic';
		$config['base_filter'] = '/' . $unit_asal . '/' . $search;
		$config['uri_segment'] = 4;
		$config['full_tag_open'] = '<ul class="pagination paging urlactive">';
		$config['total_rows'] = $this->model->num_barang($unit_asal, $search)['jumlah'];
		$this->jquery_pagination->initialize($config);
		$data['content']    = $this->model->get_barang($config['per_page'], $offset, $unit_asal, $search);
		$data['halaman'] = $this->jquery_pagination->create_links();
		$data['offset'] = $offset;

		$data['linkForm']   = site_url('aset_manajemen_barang/mutasi_antar_unit/list_barang/0/' . $unit_asal . '/' . $search);

		$this->load->view('modal_barang', $data);
	}

	public function detail($encId = '')
	{
		$data['id'] =  $this->encryption_lib->urldecode($encId);
		$data['list'] = $this->model->get_detail_mutasi($data['id']);
		foreach ($data['list'] as $key => $row) {
			$data['unit_asal'] 		= $row['unit_asal'];
			$data['unit_tujuan'] 	= $row['unit_tujuan'];
			$data['ba_mutasi'] 		= $row['ba_mutasi'];
			$data['tgl_mutasi'] 	= $row['tgl'];
			$data['pic'] 			= $row['pic'] . '(NIP:' . $row['nip'] . ')';
			$data['keterangan'] 	= $row['keterangan'];
			$data['file_mutasi'] 	= ($row['FILE']) ? $row['FILE'] : '';
			$data['url_file'] 		= ($row['FILE']) ? base_url($this->u . '/' . $row['FILE']) : '';


			switch ($row['mtsAsetMstApproved']) {
				case 'Y':
					$data['approval'] = "Disetujui";
					$data['label_color'] = "label-success";
					break;
				case 'YC':
					$data['approval'] = "Menyetujui Sebagian";
					$data['label_color'] = "label-info";
					break;
				case 'N':
					$data['approval'] = "Tidak Setuju";
					$data['label_color'] = "label-danger";
					break;
				case 'C':
					$data['approval'] = "Proses pengecekan";
					$data['label_color'] = "label-info";
					break;
				default:
					$data['approval'] = "Belum Diajukan";
					$data['label_color'] = "label-warning";
					break;
			}
		}

		$this->load->view('detail', $data);
	}

	public function get_pic()
	{
		$unit_asal = $this->input->get('unit_asal');
		$data = $this->model->select_pic_by_id($unit_asal);
		$arr['pic_nama'] 	= $data['ttdNama2'];
		$arr['pic_nip'] 	= $data['ttdNip2'];
		echo json_encode($arr);
	}
}