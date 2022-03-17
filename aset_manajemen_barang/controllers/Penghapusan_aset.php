<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');

/**
 * Controller Penghapusan Aset
 * @created : August 11, 2021 11:46:00
 * @author  : Alfian Hidayat <hidayat_alfian@ugm.ac.id>
 * @company : DSSDI UGM
**/

class Penghapusan_aset extends UGM_Controller
{
	private $ret_css_status;
	private $ret_message;
	private $ret_url;

	public function __construct() {
		parent::__construct();
		$this->load->helper('custom');
        
		$this->load->library('response');
		$this->load->library('Encryption_lib');
		$this->load->library('Jquery_pagination');
		$this->load->library('form_validation');
        
		$this->load->model('Model_penghapusan_aset', 'model');

		$this->ret_url          = site_url('aset_manajemen_barang/penghapusan_aset/view');
		$this->date_today       = date("Y-m-d H:i:s");
		$this->total_page       = 20;

		$this->user_id          = $this->session->userdata('__user_id');
		$this->kode_unit        = $this->session->userdata('__objUser')->unit_kerja_kode;
		$this->user_level       = $this->session->userdata('__objUser')->user_level;
	}
    
	public function index() {
		redirect('aset_manajemen_barang/penghapusan_aset/view');
	}
    
	public function view($offset='', $search='', $jenis='', $unit_kerja=''){
		$data['linkView'] = site_url('aset_manajemen_barang/penghapusan_aset/view');
		$data['linkAdd'] = site_url('aset_manajemen_barang/penghapusan_aset/add');
		
		$data['dataUnitKerja'] = $this->model->get_unit_kerja()->result_array();

		if ($this->input->server('REQUEST_METHOD') == 'POST' || !empty($offset)) {
			$data['search']			= (!empty($this->input->post('search', TRUE)))?$this->input->post('search', TRUE):'';
			$data['jenis']		    = (!empty($this->input->post('jenis', TRUE)))?$this->input->post('jenis', TRUE):'';
			$data['unit_kerja']		= (!empty($this->input->post('unit_kerja', TRUE)))?$this->input->post('unit_kerja', TRUE):'';
			$data['offset']			= 0;
            
			// ==========================================================================
			$this->load->library('jquery_pagination');
			$config['base_url']         = site_url('aset_manajemen_barang/penghapusan_aset/view/');
			$config['per_page']         = $this->total_page;
			$config['base_filter']      = '/'.$data['search'].'/'.$data['jenis'].'/'.$data['unit_kerja'];
			$config['uri_segment']      = 4;
			$config['url_location']     = 'subcontent-element';
			$config['total_rows']       = $this->model->get_num_data($data['search'], $data['jenis'], $data['unit_kerja'])->row_array()['jumlah'];
			$this->jquery_pagination->initialize($config);
			$data['content']            = $this->model->get_data($config['per_page'], $offset, $data['search'], $data['jenis'], $data['unit_kerja'])->result_array();
			$data['pagination']         = $this->jquery_pagination->create_links();
			// ==========================================================================
		} else {
			$data['search']			= (!empty($search))?$search:'';
			$data['jenis']		    = (!empty($jenis))?$jenis:'';
			$data['unit_kerja']		= (!empty($unit_kerja))?$unit_kerja:'';
			$data['offset']			= $offset;
		}
		// ==========================================================================


        // ==========================================================================
		$this->template->load_view('view', $data);
	}

	public function add($id=null){
        $data['form_action'] = site_url() . "aset_manajemen_barang/penghapusan_aset/add_proses";
        
        $this->template->load_view('tambah', $data);
	}
}