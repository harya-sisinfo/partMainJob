<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');

/**
 * Controller Pengembangan Aset Langsung
 * @created : August 19, 2021 11:47 AM
 * @updated : Oktober 29, 2021 09:31 AM
 * @author  : Alfian Hidayat <hidayat_alfian@ugm.ac.id>
 * @company : DSSDI UGM
**/

class Pengembangan_aset_langsung extends UGM_Controller
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
        
		$this->load->model('Model_pengembangan_aset_langsung', 'model');

		$this->ret_url          = site_url('aset_manajemen_barang/pengembangan_aset_langsung/view');
		$this->date_today       = date("Y-m-d H:i:s");
		$this->total_page       = 20;

		$this->user_id          = $this->session->userdata('__user_id');
		$this->kode_unit        = $this->session->userdata('__objUser')->unit_kerja_kode;
		$this->user_level       = $this->session->userdata('__objUser')->user_level;
	}
    
	public function index() {
		redirect('aset_manajemen_barang/pengembangan_aset_langsung/view');
	}
    
	public function view($offset='', $search='', $tahun='', $unit_kerja=''){
		$data['linkView'] = site_url('aset_manajemen_barang/pengembangan_aset_langsung/view');
		$data['linkAdd'] = site_url('aset_manajemen_barang/pengembangan_aset_langsung/add');
        $data['linkDet'] = site_url('aset_manajemen_barang/pengembangan_aset_langsung/detail');
		
		$data['dataUnitKerja'] = $this->model->get_unit_kerja()->result_array();

		if ($this->input->server('REQUEST_METHOD') == 'POST' || !empty($offset)) {
			$data['search']			= (!empty($this->input->post('search', TRUE)))?$this->input->post('search', TRUE):'';
			$data['tahun']		    = (!empty($this->input->post('tahun', TRUE)))?$this->input->post('tahun', TRUE):'';
			$data['unit_kerja']		= (!empty($this->input->post('unit_kerja', TRUE)))?$this->input->post('unit_kerja', TRUE):'';
			$data['offset']			= 0;
            
			// ==========================================================================
			$this->load->library('jquery_pagination');
			$config['base_url']         = site_url('aset_manajemen_barang/pengembangan_aset_langsung/view/');
			$config['per_page']         = $this->total_page;
			$config['base_filter']      = '/'.$data['search'].'/'.$data['tahun'].'/'.$data['unit_kerja'];
			$config['uri_segment']      = 4;
			$config['url_location']     = 'subcontent-element';
			$config['total_rows']       = $this->model->get_num_data($data['search'], $data['tahun'], $data['unit_kerja'])->row_array()['jumlah'];
			$this->jquery_pagination->initialize($config);
			$data['content']            = $this->model->get_data($config['per_page'], $offset, $data['search'], $data['tahun'], $data['unit_kerja'])->result_array();
			$data['pagination']         = $this->jquery_pagination->create_links();
			// ==========================================================================
		} else {
			$data['search']			= (!empty($search))?$search:'';
			$data['tahun']		    = (!empty($tahun))?$tahun:'';
			$data['unit_kerja']		= (!empty($unit_kerja))?$unit_kerja:'';
			$data['offset']			= $offset;
		}
		// ==========================================================================


        // ==========================================================================
		$this->template->load_view('view', $data);
	}

   	public function add(){
   	    $data['form_action'] = site_url() . "aset_manajemen_barang/pengembangan_aset_langsung/add_proses";
        
        $this->template->load_view('tambah', $data);
	}
    
   	public function detail($id=null){
        $data['linkView'] = site_url('aset_manajemen_barang/pengembangan_aset_langsung/view');
        $data['detail'] = $this->model->select_detail_pal($id);
        $data['rincianAset'] = $this->model->select_rincian_aset($id);
        
        $this->template->load_view('rinci', $data);
	}
}