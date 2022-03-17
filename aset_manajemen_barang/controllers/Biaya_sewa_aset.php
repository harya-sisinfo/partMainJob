<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');

/**
 * Controller Biaya_sewa_aset
 * @created : 2021-08-4 10:05:00
 * @author  : Sriharyo <sriharyo@ugm.ac.id>
 * @company : DSSDI UGM
*/
class Biaya_sewa_aset extends UGM_Controller
{
	private $aset;
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
		$this->load->model('Model_biaya_sewa_aset','model');

		$this->ret_url          = site_url('aset_manajemen_barang/biaya_sewa_aset/view');
		$this->date_today       = date("Y-m-d H:i:s");
		$this->total_page       = 20;

		$this->user_id          = $this->session->userdata('__user_id');
		$this->username     	= $this->session->userdata('__username');
		$this->kode_unit        = $this->session->userdata('__objUser')->unit_kerja_kode;
		$this->user_level       = $this->session->userdata('__objUser')->user_level;
	}
	public function index()
	{
		redirect('aset_manajemen_barang/biaya_sewa_aset/view');
	}
	public function view()
	{
		$data['kode_'] 	= (!empty($this->input->get('kode_')))?$this->input->get('kode_'):'';
		$data['label_'] 	= (!empty($this->input->get('label_')))?$this->input->get('label_'):'';

		$data['lastState'] 		= (!empty($lastState))?$this->encryption_lib->urldecode($lastState):'';

		$data['linkAdd'] 		= site_url('aset_manajemen_barang/biaya_sewa_aset/add');
		$data['linkView'] 		= site_url('aset_manajemen_barang/biaya_sewa_aset/view');
		$data['linkDataTable'] 	= site_url('aset_manajemen_barang/biaya_sewa_aset/view_dtable');
		$this->template->load_view("view", $data);
	}
	public function view_dtable()
	{
		$getData = $this->input->get();
		$columns =	[	
			0 => '',
			1 => 'biaya_sewa_kode',	
			2 => 'biaya_sewa_kode_aset',	
			3 => 'biaya_sewa_label',	
			4 => 'biaya_sewa', 
			5 => 'biaya_sewa_rusak',
			6 => 'biaya_sewa_denda',
			7 => '' ];
		$search = $getData['search']['value'];
		if ($columns[$getData['order'][0]['column']] == 'biaya_sewa_kode') { // default order
			$order = $columns[$getData['order'][0]['column']] . " " . 'DESC';
		} else {
			$order = $columns[$getData['order'][0]['column']] . " " . $getData['order'][0]['dir'];
		}
		$offset = $getData['start'];
		$limit = $getData['length'];
		$lastState = '&kode='.$getData['kode'].'&label='.$getData['label'];
		$params = [
			'kode'    => $getData['kode'],
			'label'   => $getData['label']	
		];

		$totalRows = $this->model->get_num_data($params,$search)['jumlah'];
		$dataQuery = $this->model->get_data($limit, $offset, $search, $order,  $params);

		$data = [];

		if ($dataQuery) {
			foreach ($dataQuery as $idx => $row) {
				$encId 		= $this->encryption_lib->urlencode($row['biaya_sewa_id']);
				$linkUpdate = site_url('aset_manajemen_barang/biaya_sewa_aset/update?encId='.$encId.$lastState);
				$linkDelete = site_url('aset_manajemen_barang/biaya_sewa_aset/delete_proses?encId='.$encId.$lastState);
				$data[] = [
					$idx + 1 + $offset
					, $row['biaya_sewa_kode']
					, $row['biaya_sewa_kode_aset']
					, $row['biaya_sewa_label']
					, number_format($row['biaya_sewa'], 2, ',', '.')
					, number_format($row['biaya_sewa_rusak'], 2, ',', '.')
					, number_format($row['biaya_sewa_denda'], 2, ',', '.')
					, '
						<a 
							href="'.$linkUpdate.'"
							class="btn btn-success btn-sm tooltip-trigger"
							data-toggle="tooltip"
							data-placement="top"
							title="Ubah data">
							<span class="fa fa-pencil"></span>
						</a>
						&nbsp;
						<a
							href="'.$linkDelete.'"
							class="btn btn-danger btn-sm tooltip-trigger btn-hapus"
							data-toggle="tooltip" 
							data-placement="top" 
							title="Hapus">
							<span class="fa fa-trash-o"></span>
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
	public function add()
	{
		$getData = $this->input->get();

		$lastState = 	'?kode_='.(!empty($getData['kode'])?$getData['kode']:'').
						'&label_='.(!empty($getData['label'])?$getData['label']:'');
		$data['url_action']	= site_url('aset_manajemen_barang/biaya_sewa_aset/add_proses').$lastState;
		$data['url_back']	= site_url('aset_manajemen_barang/biaya_sewa_aset/view').$lastState;
		$data['form_label'] = 'Tambah Data Biaya Sewa';
		$data['isProses'] 	= 'add';

		$this->template->load_view('add', $data);
	}

	public function add_proses()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('biaya_sewa_label', 'Barang Inventaris ', 'trim|required');
		$this->form_validation->set_rules('biaya_sewa_kode', 'Kode ', 'trim|required');
		$this->form_validation->set_rules('biaya_sewa', 'Nilai Sewa ', 'trim|required');
		$this->form_validation->set_message('required', 'Isian %s Belum Dilengkapi.');
		$getData = $this->input->get();
		$lastState = 	'?kode_='.(!empty($getData['kode_'])?$getData['kode_']:'').
						'&label_='.(!empty($getData['label_'])?$getData['label_']:'');
		if ($this->form_validation->run($this) == FALSE) {
			$data['url_action']	= site_url('aset_manajemen_barang/biaya_sewa_aset/add_proses').$lastState;
			$data['url_back']	= site_url('aset_manajemen_barang/biaya_sewa_aset/view').$lastState;
			$data['form_label'] = 'Tambah Data Biaya Sewa';
			$data['isProses'] 	= 'add';

			$this->template->load_view('add', $data);
		}else{
			extract($this->input->post());
			$this->aset->trans_begin();
			
			$data_insert = array(
                    'invBiayaSewaInvId' 	=> $label_id, 
                    'invBiayaSewaKode' 		=> $biaya_sewa_kode, 
                    'invBiayaSewaNilai' 	=> str_replace('.', '', explode(',', $biaya_sewa)[0]),
                    'invBiayaSewaRusak' 	=> str_replace('.', '', explode(',', $biaya_sewa_rusak)[0]),
                    'invBiayaSewaGanti' 	=> str_replace('.', '', explode(',', $biaya_sewa_denda)[0]),
                    'invBiayaSewaTgl' 		=> $this->date_today,
                    'invBiayaSewaUserId' 	=> $this->model->userAsetSession()['user_id'],
                    'user_username' 		=> $this->username
                );
			$insert_id = $this->model->insert_data('aset_ref_biaya_sewa',$data_insert);
			if ($this->aset->trans_status() === TRUE) {
                $this->aset->trans_commit();
                $this->notice("Berhasil menambah data", "success");
                redirect('aset_manajemen_barang/biaya_sewa_aset/view');
            } else {
                $this->aset->trans_rollback();
                $this->notice("Gagal menyimpan ke dalam database", "danger");
                redirect('aset_manajemen_barang/biaya_sewa_aset/view');
            }
		}
	}
	public function update()
	{
		$getData 			= $this->input->get();
		$id 				= $this->encryption_lib->urldecode($getData['encId']);
		$lastState 			= '?kode_='.(!empty($getData['kode'])?$getData['kode']:'').'&label_='.(!empty($getData['label'])?$getData['label']:'');
		$data['content'] 	= $this->model->get_data_by_id($id);
		$data['content_det']= $this->model->get_barang_inventaris_by_id($data['content']['biaya_sewa_label_id']);
		$data['url_action']	= site_url('aset_manajemen_barang/biaya_sewa_aset/update_proses?id='.$id).$lastState;
		$data['url_back']	= site_url('aset_manajemen_barang/biaya_sewa_aset/view').$lastState;
		$data['form_label'] = 'Ubah Data Biaya Sewa';
		$data['isProses'] 	= 'update';

		$this->template->load_view('update', $data);
	}
	public function update_proses()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('biaya_sewa_label', 'Barang Inventaris ', 'trim|required');
		$this->form_validation->set_rules('biaya_sewa_kode', 'Kode ', 'trim|required');
		$this->form_validation->set_rules('biaya_sewa', 'Nilai Sewa ', 'trim|required');
		$this->form_validation->set_message('required', 'Isian %s Belum Dilengkapi.');

		$getData 	= $this->input->get();
		$lastState 	= 'kode_='.(!empty($getData['kode_'])?$getData['kode_']:'').'&label_='.(!empty($getData['label_'])?$getData['label_']:'');
						
		if ($this->form_validation->run($this) == FALSE) {
			
			$data['content'] 	= $this->model->get_data_by_id();
			$data['url_action']	= site_url('aset_manajemen_barang/biaya_sewa_aset/update_proses?encId='.$this->encryption_lib->urlencode($encId).'&').$lastState;
			$data['url_back']	= site_url('aset_manajemen_barang/biaya_sewa_aset/view?').$lastState;
			$data['form_label'] = 'Ubah Data Biaya Sewa';
			$data['isProses'] 	= 'update';

			$this->template->load_view('update', $data);
		}else{
			extract($this->input->post());
			$this->aset->trans_begin();

			$data_insert = array(
                    'invBiayaSewaInvId' 	=> $label_id, 
                    'invBiayaSewaKode' 		=> $biaya_sewa_kode, 
                    'invBiayaSewaNilai' 	=> str_replace('.', '', explode(',', $biaya_sewa)[0]),
                    'invBiayaSewaRusak' 	=> str_replace('.', '', explode(',', $biaya_sewa_rusak)[0]),
                    'invBiayaSewaGanti' 	=> str_replace('.', '', explode(',', $biaya_sewa_denda)[0]),
                    'invBiayaSewaTgl' 		=> $this->date_today,
                    'invBiayaSewaUserId' 	=> $this->model->userAsetSession()['user_id'],
                    'user_username' 		=> $this->username
                );
			$this->model->update_data('aset_ref_biaya_sewa','invBiayaSewaId',$getData['id'],$data_insert);
			if ($this->aset->trans_status() === TRUE) {
    			$this->aset->trans_commit();
    			$this->ret_css_status 	= 'success';
				$this->ret_message 		= 'Ubah Data berhasil';
    		}else{
    			$this->aset->trans_rollback();
    			$this->ret_css_status 	= 'danger';
				$this->ret_message 		= 'Ubah Data gagal';
    		}
    		$this->ret_url = site_url('aset_manajemen_barang/biaya_sewa_aset/view?'.$lastState);
    		echo json_encode(['status' => $this->ret_css_status, 'msg' => $this->ret_message, 'url' => $this->ret_url]);
    		exit();
		}
	}

	public function delete_proses($id='')
	{
		$getData 			= $this->input->get();
		$this->aset->trans_begin();
		$id = $this->encryption_lib->urldecode($getData['encId']);

		$id_sewa_detail = $this->model->cek_sewa_detail($id);
		if($id_sewa_detail >= 1){
			$this->aset->trans_rollback();
			$this->ret_css_status = 'error';
			$this->ret_message = 'Hapus data gagal, data masih sudah digunakan sewa';
		}else{
			$rs = $this->model->delete_data('aset_ref_biaya_sewa','invBiayaSewaId',$id);
			if ($rs) {
				$this->aset->trans_commit();
				$this->ret_css_status = 'notice';
				$this->ret_message = 'Hapus data berhasil';
			} else {
				$this->aset->trans_rollback();
				$this->ret_css_status = 'error';
				$this->ret_message = 'Hapus data gagal';
			}	
		}
		$this->response->script("$.growl.{$this->ret_css_status}({message: '{$this->ret_message}', size: 'large'});");
		$this->response->send();

	}
	public function list_barang_inventaris($offset=0,$search_='')
	{
		$search = (!empty($search_))?$search_:(!empty($this->input->post('search')))?$this->input->post('search'):'';
		$this->load->library('jquery_pagination');
		$config['base_url'] = site_url('aset_manajemen_barang/biaya_sewa_aset/list_barang_inventaris');
		$config['per_page'] = 10;
		$config['url_location'] = 'modal-data-basic';
		$config['base_filter'] = '/'.(!empty($search))?$search:'';
		$config['uri_segment'] = 4;
		$config['full_tag_open'] = '<ul class="pagination paging urlactive">';
		$config['total_rows'] = $this->model->num_barang_inventaris($search)['jumlah'];
		$this->jquery_pagination->initialize($config);
		$data['content']    = $this->model->get_barang_inventaris($config['per_page'],$offset,$search);
		$data['halaman'] = $this->jquery_pagination->create_links();
		$data['offset'] = $offset;
		$data['linkForm']   = site_url('aset_manajemen_barang/biaya_sewa_aset/list_barang_inventaris');
		$this->load->view('modal_barang_inventaris',$data);
	}
}