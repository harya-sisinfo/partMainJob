<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');

/**
 * Controller Penggunaan_aset
 * @created : 2021-08-4 10:05:00
 * @author  : Sriharyo <sriharyo@ugm.ac.id>
 * @company : DSSDI UGM
*/

class Penggunaan_aset extends UGM_Controller
{
	private $ret_css_status;
	private $ret_message;
	private $ret_url;
	private $u;

	public function __construct() 
	{
		parent::__construct();
		$this->load->helper('custom');
		$this->load->library('response');
		$this->load->library('Encryption_lib');
		$this->load->library('Jquery_pagination');
		$this->load->library('form_validation');
		$this->load->model('Model_penggunaan_aset','model');

		$this->ret_url          = site_url('aset_manajemen_barang/penggunaan_aset/view');
		$this->date_today       = date("Y-m-d H:i:s");
		$this->total_page       = 20;

		// $this->u = '/media/data/aset/mutasi_antar_unit';
		$this->u = $this->config->item('upload_url') . 'penggunaan_aset';

		$this->user_id          = $this->session->userdata('__user_id');
		$this->kode_unit        = $this->session->userdata('__objUser')->unit_kerja_kode;
		$this->user_level       = $this->session->userdata('__objUser')->user_level;
	}
	public function index()
	{
		redirect('aset_manajemen_barang/penggunaan_aset/view');
	}

	public function view()
	{
		$data['lastState'] = (!empty($lastState))?$this->encryption_lib->urldecode($lastState):'';
		// .:: Link View ::.
		$data['linkView'] = site_url('aset_manajemen_barang/penggunaan_aset/view');
		// .:: Link Add ::.
		$data['linkAdd'] = site_url('aset_manajemen_barang/penggunaan_aset/add');
		// .:: Link Detail ::.
		$data['linkDetail'] = site_url('aset_manajemen_barang/penggunaan_aset/detail');
		// .:: Link Datatable ::.
		$data['linkDataTable'] = site_url('aset_manajemen_barang/penggunaan_aset/view_dtable');

		$this->template->load_view('view', $data);
	}

	public function view_dtable()
	{
		$getData = $this->input->get();
		$columns =	[	0 => '',
		1 => 'noRef',	
		2 => 'tgl',	
		3 => 'nama',	
		4 => 'nip', 
		5 => 'telp',
		6 => 'jab', 
		7 => 'unit',
		8 => 'jmlAset',
		9 => 'status',
		10 => '' ];

		$search = $getData['search']['value'];
		if ($columns[$getData['order'][0]['column']] == 'noRef') { // default order
			$order = $columns[$getData['order'][0]['column']] . " " . 'DESC';
		}elseif ($columns[$getData['order'][0]['column']] == 'tgl') { 
			$order = $columns[$getData['order'][0]['column']] . " " . $getData['order'][0]['dir'];
		} else {
			$order = $columns[$getData['order'][0]['column']] . " " . $getData['order'][0]['dir'];
		}
		$offset = $getData['start'];
		$limit = $getData['length'];
		$lastState = '&noba='.(!empty($getData['noba'])?$getData['noba']:'').'&stat='.(!empty($getData['stat'])?$getData['stat']:'');
		$modalLastState = $this->encryption_lib->urlencode((!empty($getData['noba'])?$getData['noba']:'').'||'.(!empty($getData['stat'])?$getData['stat']:''));
		$params = [
			'noba' 		=> (!empty($getData['noba'])?$getData['noba']:''),
			'stat' 			=> (!empty($getData['stat'])?$getData['stat']:'')
		];
		$totalRows = $this->model->get_num_data($params,$search);
		$dataQuery = $this->model->get_data($limit, $offset, $search, $order,  $params);

		$data = [];
		if ($dataQuery) 
		{
			foreach ($dataQuery as $idx => $row)
			{
				$encId 		= $this->encryption_lib->urlencode($row['id']);
				$linkUpdate = site_url('aset_manajemen_barang/penggunaan_aset/update?encId='.$encId.$lastState);
				$linkDelete = site_url('aset_manajemen_barang/penggunaan_aset/delete_proses?encId='.$encId);

				//** Tombol aksi
				$button_update = '<a 
				href="'.$linkUpdate.'"
				class="btn btn-success btn-sm tooltip-trigger"
				data-toggle="tooltip"
				data-placement="top"
				title="Ubah data"
				>
				<span class="fa fa-pencil"></span>
				</a>&nbsp;';
				$button_detail = '<a 
				href=""
				class="btn btn-info btn-sm tooltip-trigger"
				data-toggle="tooltip"
				data-placement="top"
				title="Detail"
				rel="async"
				ajaxify="'.
				modal(
					'Detail Mutasi',
					'aset_manajemen_barang',
					'penggunaan_aset',
					'detail',
					$encId).'"
				>
				<span class="fa fa-info-circle"></span>
				</a>&nbsp;';
				$button_delete = '<a
				href="'.$linkDelete.'"
				class="btn btn-danger btn-sm tooltip-trigger btn-hapus"
				data-toggle="tooltip" 
				data-placement="top" 
				title="Hapus"
				>
				<span class="fa fa-trash-o"></span>
				</a>';

				// ** View List
				$data[] = [
					$idx + 1 + $offset
					, $row['noRef']
					, $row['tgl']
					, $row['nama']
					, $row['nip']
					, $row['telp']
					, $row['jab']
					, $row['unit']
					, $row['jmlAset']
					, $row['status']
					, $button_detail.$button_update.$button_delete
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
	
	// =========================================================
	// ** Add
	public function add(){
		$getData 				= $this->input->get();
		$lastState 				= '?noba_='.((!empty($getData['noba']))?$getData['noba']:'').'&stat_='.((!empty($getData['stat']))?$getData['stat']:'');
		$data['url_action']		= site_url('aset_manajemen_barang/penggunaan_aset/add_proses/').$this->encryption_lib->urlencode($lastState);
		$data['data_punit']			= $this->model->get_unit_kerja();
		$data['data_unit']			= $this->model->get_unit_kerja();
		$data['url_back']		= site_url('aset_manajemen_barang/penggunaan_aset/view').$lastState;
		$data['form_label'] 	= 'Tambah Data Penggunaan Aset';
		$data['isProses'] 		= 'add';

		$this->template->load_view('add', $data);

	}
	public function add_proses($lastState_)
	{
	
		$lastState = $this->encryption_lib->urldecode($lastState_);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('noref', 'No. SIP ', 'required');
		$this->form_validation->set_rules('tgl', 'Tanggal ', 'required');
		$this->form_validation->set_rules('stat', 'Status ', 'required');

		$this->form_validation->set_rules('pnama', 'Nama pemberi izin ', 'required');
		$this->form_validation->set_rules('pnip', 'NIP pemberi izin ', 'required');
		$this->form_validation->set_rules('pjab', 'Jabatan pemberi izin ', 'required');
		$this->form_validation->set_rules('punit', 'Unit pemberi izin ', 'required');

		$this->form_validation->set_rules('nama', 'Nama pengguna aset ', 'required');
		$this->form_validation->set_rules('nip', 'NIP pengguna aset ', 'required');
		$this->form_validation->set_rules('jab', 'Jabatan pengguna aset ', 'required');
		$this->form_validation->set_rules('telp', 'Telpon pengguna aset ', 'required');
		$this->form_validation->set_rules('unit', 'Unit pengguna aset ', 'required');


		$this->form_validation->set_message('required', 'Isian %s Belum Dilengkapi.');

		if ($this->form_validation->run($this) == TRUE)
		{
			$this->model->aset->trans_begin();
			extract($this->input->post());

			if ($_FILES["fileSip"]["tmp_name"][0]) 
			{
				$upload = $this->do_upload_file();
				$nama_file = $upload['file_name'];
			}
			else{
				$upload['status'] = true; 
    			$upload['file_name'] = '';
			}

			if ($upload['status'] === true) {
    			$nama_file = $upload['file_name'];
    		}else{
    			$nama_file = '';			
    		}
			
			$data_penggunaan = array(
				'asetGunaNoRef' => $noref,
				'asetGunaTglSip' => date('Y-m-d',strtotime(str_replace('/', '-',substr($tgl,0,10)))),
				'asetGunaFile' => $nama_file,
				'asetGunaAktif' => $stat,
				'asetGunaKet' => $ket,
				'asetGunaNamaPemberi' => $pnama,
				'asetGunaNIPPemberi' => $pnip,
				'asetGunaJbtnPemberi' => $pjab,
				'asetGunaUnitPemberi' => $punit,
				'asetGunaNama' => $nama,
				'asetGunaNIP' => $nip,
				'asetGunaJabatan' => $jab,
				'asetGunaNoTelp' => $telp,
				'asetGunaUnitId' => $unit,
				'asetGunaCreatorId' => $this->model->userAsetSession()['user_id'],
				'asetGunaCreated' => $this->date_today
			);

			$mstId = $this->model->insert_data_id('aset_penggunaan_mst',$data_penggunaan);

			foreach ($inv_det_id as $idx => $value) {
				$data_pegguna_detail = array(
					'asetGunaDetMstId' => $mstId, 
					'asetGunaDetBrgId' => $inv_det_id[$idx], 
					'asetGunaDetKet' => $keterangan[$idx],
					'asetGunaDetKodeBrg' => substr_replace($barang_kode[$idx], '', '.'), 
					'asetGunaDetUnitId' => $unit_id[$idx], 
					'asetGunaDetKmblKond' => $kondisi[$idx]
				);
				$this->model->insert_data('aset_penggunaan_det',$data_pegguna_detail);

			}

			if ($this->model->aset->trans_status() === TRUE) {
    			$this->model->aset->trans_commit();
    			if($upload['status'] === false){
    				$err_upload = ' , <font color="red">Maaf proses upload gagal dilakukan.</font>.';
    			}else{
    				$err_upload = '.';
    			}
    			$this->ret_css_status 	= 'success';
				$this->ret_message 		= 'Tambah Data berhasil'.$err_upload;
    		}else{
    			$this->model->aset->trans_rollback();
    			$this->ret_css_status = 'danger';
				$this->ret_message = 'Tambah Data gagal';
    		}
    		$this->ret_url = site_url('aset_manajemen_barang/penggunaan_aset/view'.$lastState);
    		echo json_encode(['status' => $this->ret_css_status, 'msg' => $this->ret_message, 'url' => $this->ret_url]);
    		exit();
		}else{
			
			$data['url_action']		= site_url('aset_manajemen_barang/penggunaan_aset/add_proses/').$this->encryption_lib->urlencode($lastState);
			$data['data_punit']			= $this->model->get_unit_kerja();
			$data['data_unit']			= $this->model->get_unit_kerja();
			$data['url_back']		= site_url('aset_manajemen_barang/penggunaan_aset/view').$lastState;
			$data['form_label'] 	= 'Tambah Data Penggunaan Aset';
			$data['isProses'] 		= 'add';

			$this->template->load_view('add', $data);
		}
	}
	// =========================================================
	// ** Update
	public function update(){
		$getData 					= $this->input->get();
		$data['encId']				= $getData['encId'];
		$id 						= $this->encryption_lib->urldecode($getData['encId']);
		$lastState 					= '?noba_='.((!empty($getData['noba']))?$getData['noba']:'').'&stat_='.((!empty($getData['stat']))?$getData['stat']:'');
		$data['url_action']			= site_url('aset_manajemen_barang/penggunaan_aset/update_proses/').$this->encryption_lib->urlencode($lastState);

		$data['objPengguna'] 		= $this->model->get_data_by_id($id);
		$data['objPenggunaDetail']	= $this->model->get_data_detail_by_id($id);
		$data['data_punit']			= $this->model->get_unit_kerja();
		$data['data_unit']			= $this->model->get_unit_kerja();

		$data['url_back']			= site_url('aset_manajemen_barang/penggunaan_aset/view').$lastState;
		$data['form_label'] 		= 'Ubah Data Penggunaan Aset';
		$data['isProses'] 			= 'update';
		$this->template->load_view('update', $data);
	}
	public function update_proses($lastState_)
	{
		$lastState = $this->encryption_lib->urldecode($lastState_);
		extract($this->input->post());
		$this->load->library('form_validation');
		$this->form_validation->set_rules('noref', 'No. SIP ', 'required');
		$this->form_validation->set_rules('tgl', 'Tanggal ', 'required');
		$this->form_validation->set_rules('stat', 'Status ', 'required');

		$this->form_validation->set_rules('pnama', 'Nama pemberi izin ', 'required');
		$this->form_validation->set_rules('pnip', 'NIP pemberi izin ', 'required');
		$this->form_validation->set_rules('pjab', 'Jabatan pemberi izin ', 'required');
		$this->form_validation->set_rules('punit', 'Unit pemberi izin ', 'required');

		$this->form_validation->set_rules('nama', 'Nama pengguna aset ', 'required');
		$this->form_validation->set_rules('nip', 'NIP pengguna aset ', 'required');
		$this->form_validation->set_rules('jab', 'Jabatan pengguna aset ', 'required');
		$this->form_validation->set_rules('telp', 'Telpon pengguna aset ', 'required');
		$this->form_validation->set_rules('unit', 'Unit pengguna aset ', 'required');


		$this->form_validation->set_message('required', 'Isian %s Belum Dilengkapi.');

		
		if ($this->form_validation->run($this) == TRUE)
		{
			$this->model->aset->trans_begin();

			$id = $this->encryption_lib->urldecode($encId);
			if ($_FILES["fileSip"]["tmp_name"][0]) 
			{
				$upload = $this->do_upload_file();
				$nama_file = $upload['file_name'];
			}
			else{
				$upload['status'] = true; 
    			$upload['file_name'] = '';
			}

			if ($upload['status'] === true) {
    			$nama_file = $upload['file_name'];
    		}else{
    			$nama_file = $_FILES["fileSip"]["tmp_name"][0];			
    		}

    		$data_penggunaan = array(
				'asetGunaNoRef' => $noref,
				'asetGunaTglSip' => date('Y-m-d',strtotime(str_replace('/', '-',substr($tgl,0,10)))),
				'asetGunaFile' => $nama_file,
				'asetGunaAktif' => $stat,
				'asetGunaKet' => $ket,
				'asetGunaNamaPemberi' => $pnama,
				'asetGunaNIPPemberi' => $pnip,
				'asetGunaJbtnPemberi' => $pjab,
				'asetGunaUnitPemberi' => $punit,
				'asetGunaNama' => $nama,
				'asetGunaNIP' => $nip,
				'asetGunaJabatan' => $jab,
				'asetGunaNoTelp' => $telp,
				'asetGunaUnitId' => $unit,
				'asetGunaCreatorId' => $this->model->userAsetSession()['user_id'],
				'asetGunaCreated' => $this->date_today
			);
			$this->model->update_data(
				'aset_penggunaan_mst',
				'asetGunaId',
				$id,
				$data_penggunaan
			);
			

			$this->model->delete_data('aset_penggunaan_det','asetGunaDetMstId',$id);
			foreach ($inv_det_id as $idx => $value) {
				$data_pegguna_detail = array(
					'asetGunaDetMstId' => $id, 
					'asetGunaDetBrgId' => $inv_det_id[$idx], 
					'asetGunaDetKet' => (!empty($keterangan[$idx])?$keterangan[$idx]:''),
					'asetGunaDetKodeBrg' => substr_replace($barang_kode[$idx], '', '.'), 
					'asetGunaDetUnitId' => $unit_id[$idx], 
					'asetGunaDetKmblKond' => $kondisi[$idx]
				);
				$this->model->insert_data('aset_penggunaan_det',$data_pegguna_detail);
			}
			if ($this->model->aset->trans_status() === TRUE) {
    			$this->model->aset->trans_commit();
    			if($upload['status'] === false){
    				$err_upload = ' , <font color="red">Maaf proses upload gagal dilakukan.</font>.';
    			}else{
    				$err_upload = '.';
    			}
    			$this->ret_css_status 	= 'success';
				$this->ret_message 		= 'Ubah Data berhasil'.$err_upload;
    		}else{
    			$this->model->aset->trans_rollback();
    			$this->ret_css_status = 'danger';
				$this->ret_message = 'Ubah Data gagal';
    		}
    		$this->ret_url = site_url('aset_manajemen_barang/penggunaan_aset/view'.$lastState);
    		echo json_encode(['status' => $this->ret_css_status, 'msg' => $this->ret_message, 'url' => $this->ret_url]);
    		exit();
		}else{		
			$data['encId']				= $encId;

			$id 						= $this->encryption_lib->urldecode($getData['encId']);

			$data['objPengguna'] 		= $this->model->get_data_by_id($id);
		$data['objPenggunaDetail']	= $this->model->get_data_detail_by_id($id);
			$data['data_punit']			= $this->model->get_unit_kerja();
			$data['data_unit']			= $this->model->get_unit_kerja();
			
			$data['url_back']		= site_url('aset_manajemen_barang/penggunaan_aset/view').$lastState;
			$data['url_action']			= site_url('aset_manajemen_barang/penggunaan_aset/update_proses/').$this->encryption_lib->urlencode($lastState);

			$data['form_label'] 	= 'Ubah Data Penggunaan Aset';
			$data['isProses'] 		= 'update';
			$this->template->load_view('update', $data);
		}
	}
	// =========================================================
	// global function 
	// ---------------------------------------------------------
	// ** List Barang
	public function list_barang($offset=0,$unit_asal='',$search_='')
	{
		$data['unit_asal'] = $unit_asal;

		$search = (!empty($search_))?$search_:(!empty($this->input->post('search')))?$this->input->post('search'):'';
		$data['search'] = $search;
		$this->load->library('jquery_pagination');
		$config['base_url'] = site_url('aset_manajemen_barang/penggunaan_aset/list_barang/');
		$config['per_page'] = 10;
		$config['url_location'] = 'modal-data-basic';
		$config['base_filter'] = '/'.$unit_asal.'/'.$search;
		$config['uri_segment'] = 4;
		$config['full_tag_open'] = '<ul class="pagination paging urlactive">';
		$config['total_rows'] = $this->model->num_barang($unit_asal,$search)['jumlah'];
		$this->jquery_pagination->initialize($config);
		$data['content']    = $this->model->get_barang($config['per_page'],$offset,$unit_asal,$search);
		$data['halaman'] = $this->jquery_pagination->create_links();
		$data['offset'] = $offset;

		$data['linkForm']   = site_url('aset_manajemen_barang/penggunaan_aset/list_barang/0/'.$unit_asal.'/'.$search);

		$this->load->view('modal_barang',$data);
	}
	// ** Funstion untuk upload 
	private function do_upload_file()
	{
		$this->load->library('upload');
        $dUpload = realpath(FCPATH . DIRECTORY_SEPARATOR . $this->u);
        $listFile = [];
        foreach ($_FILES["fileSip"]['name'] as &$name) {
        	$arrFile = explode(".", $name);
        	$ext = array_pop($arrFile);
        	$name = date('Ymd_His') . '_' . str_replace(" ", "_", implode("_", $arrFile)) . "." . $ext;
        	$listFile[] = $name;
        }

        $this->upload->initialize([
        	"allowed_types" => "jpg|jpeg|pdf",
        	"upload_path" => $dUpload,
        	'file_ext_tolower' => TRUE,
        	'overwrite' => FALSE,
        	'max_size' => 50000,
        	'remove_spaces' => TRUE
        ]);

        //Perform upload.
        if ($this->upload->do_upload('fileSip')) {
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
	// =========================================================
	// ==========================================================
	public function delete_proses()
	{
		$getData = $this->input->get();
		$this->load->library('response');
		$id = $this->encryption_lib->urldecode($getData['encId']);
		

		$rs_mts = $this->model->delete_data('aset_penggunaan_mst','asetGunaId',$id);
		$rs_det = $this->model->delete_data('aset_penggunaan_det','asetGunaDetMstId',$id);

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
}