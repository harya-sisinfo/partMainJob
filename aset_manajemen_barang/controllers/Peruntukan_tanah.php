<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');

/**
 * Controller Peruntukan_tanah
 * @created : 2021-07-29 10:05:00
 * @author  : Sriharyo <sriharyo@ugm.ac.id>
 * @company : DSSDI UGM
*/

class Peruntukan_tanah extends UGM_Controller
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
		$this->load->model('Model_peruntukan_tanah','model');
		$this->aset = $this->load->database("aset", TRUE);

		$this->ret_url          = site_url('aset_manajemen_barang/peruntukan_tanah/view');
		$this->date_today       = date("Y-m-d H:i:s");
		$this->total_page       = 20;
		

		$this->user_id          = $this->session->userdata('__user_id');
		$this->kode_unit        = $this->session->userdata('__objUser')->unit_kerja_kode;
		$this->user_level       = $this->session->userdata('__objUser')->user_level;
	}
	public function index()
	{
		redirect('aset_manajemen_barang/peruntukan_tanah/view');
	}
	// ==========================================================================================
	public function view(){
		$data['linkAdd'] = site_url('aset_manajemen_barang/peruntukan_tanah/add');
		$data['linkView'] = site_url('aset_manajemen_barang/peruntukan_tanah/view');
		$data['linkDataTable'] = site_url('aset_manajemen_barang/peruntukan_tanah/view_dtable');
		$this->template->load_view("view", $data);
	}

	public function view_dtable()
	{
		$getData = $this->input->get();
		$columns =  [   0 => '',
		1 => 'invdet',
		2 => 'kodeinv', 
		3 => 'nama_aset',    
		4 => 'peruntukan', 
		5 => 'luas', 
		6 => '' ];
		$search = $getData['search']['value'];
		if ($columns[$getData['order'][0]['column']] == 'invdet') { // default order
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
				$encId      = $this->encryption_lib->urlencode($row['idTanah']);
				$linkUpdate = site_url('aset_manajemen_barang/peruntukan_tanah/update/'.$encId);
				// $linkDelete = site_url('aset_manajemen_barang/peruntukan_tanah/delete/'.$encId);
				$linkDelete = modal('Hapus Data', 'aset_manajemen_barang','peruntukan_tanah', 'delete', $encId);
				$data[] = [
					$idx + 1 + $offset
					, $row['kode']
					, $row['nama_aset']
					, $row['peruntukan']
					, $row['penggunaan']
					, number_format($row['luas'],0,",",".")
					, '
					<a 
					href="'.$linkUpdate.'" 
					class="btn btn-success btn-sm tooltip-trigger">
					<span
					class="fa fa-pencil"></span>
					</a>

					<a rel="async"
					ajaxify="'.$linkDelete.'"
					class="btn btn-danger btn-sm tooltip-trigger"
					data-toggle="tooltip" 
					data-placement="top" 
					title="Hapus"
					>
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
	// ==========================================================================================
	public function add()
	{

		$data['dataStatusPengguna'] 	= $this->model->get_status_pengguna();
		$data['dataPeruntukanTanah'] 	= $this->model->get_peruntukan_tanah();

		$data['form_label'] = 'Tambah Data Peruntukan Tanah';

		$data['isProses'] 	= 'add';

		$data['url_action'] = site_url('aset_manajemen_barang/peruntukan_tanah/add_proses');
		$data['url_back']   = site_url('aset_manajemen_barang/peruntukan_tanah/view');
		
		$this->template->load_view('add', $data);
	}

	public function add_proses(){


		$this->load->library('form_validation');
		$this->form_validation->set_rules('kode', 'Kode inventaris ', 'trim|required');
		if ($this->form_validation->run($this) == FALSE) {
			$data['dataStatusPengguna'] = $this->model->get_status_pengguna();
			$data['dataPeruntukanTanah'] = $this->model->get_peruntukan_tanah();

			$data['form_label'] = 'Tambah Data Peruntukan Tanah';
			$data['isProses'] = 'add';

			$data['url_action'] = site_url('aset_manajemen_barang/peruntukan_tanah/add_proses');
			$data['url_back']   = site_url('aset_manajemen_barang/peruntukan_tanah/view');
			$this->template->load_view('add', $data);
		}else{
			extract($this->input->post());
			$generate = $this->GenerateNumber($kode);
			$this->model->aset->trans_begin();
			$data_insert = array(
				'invPeruntukanTnhInvDetId' 		=> $invdet,
				'invPeruntukanTnhKode' 			=> $generate,
				'invPeruntukanTnhPrtknTnhId' 	=> $peruntukan,
				'invPeruntukanTnhPanjang' 		=> $this->model->format_text_to_decimal($panjang),
				'invPeruntukanTnhLebar' 		=> $this->model->format_text_to_decimal($lebar),
				'invPeruntukanTnhLuas' 			=> $this->model->format_text_to_decimal($luas),
				'invPeruntukanTnhStatusAsetId' 	=> $penggunaan,
				'invPeruntukanTnhKeterangan' 	=> $keterangan,
				'invPeruntukanTnhAlamat' 		=> $alamat,
				'invPeruntukanTnhKodePemerintah'=> $kodepemerintah,
				'invPeruntukanTnhPj' 			=> $unitnama,
				'invPeruntukanTnhNoSertipikat' 	=> $sertipikat,
				'invPeruntukanTnhUserId' 		=> $this->user_id,
				'invPeruntukanTnhTglUbah' 		=> $this->date_today
			);

			$this->model->insert_data('inv_peruntukan_tanah',$data_insert);
			if ($this->model->aset->trans_status() === FALSE) {
				$this->model->aset->trans_rollback();
				$this->ret_css_status = 'danger';
				$this->ret_message = 'Gagal Tambah Data';
			} else {					
				$this->model->aset->trans_commit();
				$this->ret_css_status = 'success';
				$this->ret_message = 'Berhasil Tambah Data';
			}
			$this->ret_url = site_url('aset_manajemen_barang/peruntukan_tanah/view');
			echo json_encode(['status' => $this->ret_css_status, 'msg' => $this->ret_message, 'url' => $this->ret_url]);
			exit;
		}
	}
	// ==========================================================================================
	public function update($encId)
	{
		$data['id'] 			= $this->encryption_lib->urldecode($encId);

		$data['form_label'] = 'Ubah Data Peruntukan Tanah';
		$data['isProses'] = 'update';


		$data['content'] = $this->model->get_data_by_id($data['id']);

		$data['dataStatusPengguna'] = $this->model->get_status_pengguna();
		$data['dataPeruntukanTanah'] = $this->model->get_peruntukan_tanah();


		$data['url_action'] = site_url('aset_manajemen_barang/peruntukan_tanah/update_proses/'.$encId);
		$data['url_back']   = site_url('aset_manajemen_barang/peruntukan_tanah/view');

		$this->template->load_view('update', $data);
	}
	public function update_proses($encId)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('kode', 'Kode inventaris ', 'trim|required');
		if ($this->form_validation->run($this) == FALSE) {
			$data['id'] 			= $this->encryption_lib->urldecode($encId);

			$data['form_label'] = 'Ubah Data Peruntukan Tanah';
			$data['isProses'] = 'update';


			$data['content'] = $this->model->get_data_by_id($data['id']);

			$data['dataStatusPengguna'] = $this->model->get_status_pengguna();
			$data['dataPeruntukanTanah'] = $this->model->get_peruntukan_tanah();


			$data['url_action'] = site_url('aset_manajemen_barang/peruntukan_tanah/update_proses');
			$data['url_back']   = site_url('aset_manajemen_barang/peruntukan_tanah/view');

			$this->template->load_view('update', $data);
		}else{
			extract($this->input->post());
			$this->model->aset->trans_begin();
			$data_update = array(
				'invPeruntukanTnhInvDetId' 		=> $invdet,
				'invPeruntukanTnhKode' 			=> $kodeinv,
				'invPeruntukanTnhPrtknTnhId' 	=> $peruntukan,
				'invPeruntukanTnhPanjang' 		=> $this->model->format_text_to_decimal($panjang),
				'invPeruntukanTnhLebar' 		=> $this->model->format_text_to_decimal($lebar),
				'invPeruntukanTnhLuas' 			=> $this->model->format_text_to_decimal($luas),
				'invPeruntukanTnhStatusAsetId' 	=> $penggunaan,
				'invPeruntukanTnhKeterangan' 	=> $keterangan,
				'invPeruntukanTnhAlamat' 		=> $alamat,
				'invPeruntukanTnhKodePemerintah'=> $kodepemerintah,
				'invPeruntukanTnhPj' 			=> $unitnama,
				'invPeruntukanTnhNoSertipikat' 	=> $sertipikat,
				'invPeruntukanTnhUserId' 		=> $this->user_id,
				'invPeruntukanTnhTglUbah' 		=> $this->date_today
			);
			$this->model->update_data('inv_peruntukan_tanah','invPeruntukanTnhId',$id,$data_update);

			if ($this->model->aset->trans_status() === FALSE) {
				$this->model->aset->trans_rollback();
				$this->ret_css_status = 'danger';
				$this->ret_message = 'Gagal Ubah Data';
			} else {					
				$this->model->aset->trans_commit();
				$this->ret_css_status = 'success';
				$this->ret_message = 'Berhasil Ubah Data';
			}
			$this->ret_url = site_url('aset_manajemen_barang/peruntukan_tanah/view');
			echo json_encode(['status' => $this->ret_css_status, 'msg' => $this->ret_message, 'url' => $this->ret_url]);
			exit;
		}
	}

	// ==========================================================================================


	public function list_inventaris_barang($offset=0,$search_='')
	{
		$search = (!empty($search_))?$search_:(!empty($this->input->post('search')))?$this->input->post('search'):'';
		
		$this->load->library('jquery_pagination');
		$config['base_url'] = site_url('aset_manajemen_barang/peruntukan_tanah/list_inventaris_barang');
		$config['per_page'] = 10;
		$config['url_location'] = 'modal-data-basic';
		$config['base_filter'] = '/'.(!empty($search))?$search:'';
		$config['uri_segment'] = 4;
		$config['full_tag_open'] = '<ul class="pagination paging urlactive">';
		$config['total_rows'] = $this->model->num_inventaris_tanah($search)['jumlah'];
		$this->jquery_pagination->initialize($config);
		$data['content']    = $this->model->get_inventaris_tanah($config['per_page'],$offset,$search);
		$data['halaman'] = $this->jquery_pagination->create_links();
		$data['offset'] = $offset;

		$data['linkForm']   = site_url('aset_manajemen_barang/peruntukan_tanah/list_inventaris_barang');
		$this->load->view('modal_inventaris_barang',$data);
	}
	// ==========================================================================================
	public function GenerateNumber($kode)
	{
		$gnumber = $this->model->get_generate_kode($kode);
		if(!empty($gnumber)){
			$explode = explode(".", $gnumber['kode']);
			$lenght  = strlen(intval($explode[5]+1));
		}else{
			$lenght  = 0;
		}
		switch($lenght){
			case 1: $generate = '00000'.($explode[5]+1); break;
			case 2: $generate = '0000'.($explode[5]+1); break;
			case 3: $generate = '000'.($explode[5]+1); break;
			case 4: $generate = '00'.($explode[5]+1); break;
			case 5: $generate = '0'.($explode[5]+1); break;
			case 6: $generate = ($explode[5]+1); break;
			default: $generate = '000001'; break;
		}
		return $generate;
	}

	public function delete($encId)
	{
		$data['id'] = $this->encryption_lib->urldecode($encId);

		$this->load->view('delete',$data);

	}

	public function delete_proses()
	{
		extract($this->input->post());
		$this->model->aset->trans_begin();

		$data_update = array(
			'invPeruntukanTnhStatusDel'=>1,
			'invPeruntukanTnhKetDel'=>$alasan,
			'invPeruntukanTnhTglDel'=>$this->date_today,
			'invPeruntukanTnhUserDel'=>$this->user_id
		);
		$this->model->update_data('inv_peruntukan_tanah','invPeruntukanTnhId',$id,$data_update);
		$this->ret_css_status   = 'success';
		$this->ret_message      = 'Rincian berhasil ditambah.'.$alasan.'->'.$id;
        
		if ($this->model->aset->trans_status() === FALSE) {
			$this->model->aset->trans_rollback();
			$this->ret_css_status = 'danger';
			$this->ret_message = 'Gagal Menghapus Data';
		} else {					
			$this->model->aset->trans_commit();
			$this->ret_css_status = 'success';
			$this->ret_message = 'Berhasil Menghapus Data';
		}
		$this->ret_url          = site_url('aset_manajemen_barang/peruntukan_tanah/view');

		echo json_encode(array('status'=>$this->ret_css_status,'msg'=>$this->ret_message,'url'=>$this->ret_url,'is_modal'=>1,'dest'=>'subcontent-element'));

	}

}