<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');

/**
 * Controller Sewa_aset
 * @created : 2021-08-4 10:05:00
 * @author  : Sriharyo <sriharyo@ugm.ac.id>
 * @company : DSSDI UGM
*/

class Sewa_aset extends UGM_Controller
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
		$this->load->model('Model_sewa_aset','model');

		$this->ret_url 		= site_url('aset_manajemen_barang/sewa_aset/view');
		$this->date_today 	= date("Y-m-d H:i:s");
		$this->total_page 	= 20;

		$this->user_id 		= $this->session->userdata('__user_id');
		$this->username 	= $this->session->userdata('__username');
		$this->kode_unit 	= $this->session->userdata('__objUser')->unit_kerja_kode;
		$this->user_level 	= $this->session->userdata('__objUser')->user_level;
	}
	public function index()
	{
		redirect('aset_manajemen_barang/sewa_aset/view');
	}

	public function view(){
		$data['kode_sewa_'] 	= (!empty($this->input->get('kode_sewa_')))?$this->input->get('kode_sewa_'):'';
		$data['status_isi_'] 	= (!empty($this->input->get('status_isi_')))?$this->input->get('status_isi_'):'';
		
		$data['lastState'] 		= (!empty($lastState))?$this->encryption_lib->urldecode($lastState):'';
		$data['linkAdd'] 		= site_url('aset_manajemen_barang/sewa_aset/add');
		$data['linkView'] 		= site_url('aset_manajemen_barang/sewa_aset/view');
		$data['linkDataTable'] 	= site_url('aset_manajemen_barang/sewa_aset/view_dtable');
		$this->template->load_view("view", $data);
	}
	
	public function view_dtable(){
		$getData = $this->input->get();
		$columns =	[	
			0 => '',
			1 => 'sewa_nomor',	
			2 => 'unitkerja',	
			3 => 'sewa_tanggal_awal',	
			4 => 'sewa_waktu_awal',	
			5 => 'sewa_tanggal_akhir', 
			6 => 'sewa_waktu_akhir',
			7 => '' ];

			$search = $getData['search']['value'];
		if ($columns[$getData['order'][0]['column']] == 'sewa_nomor') { // default order
			$order = $columns[$getData['order'][0]['column']] . " " . 'DESC';
		} else {
			$order = $columns[$getData['order'][0]['column']] . " " . $getData['order'][0]['dir'];
		}
		$params = [
			'kode_sewa'    => $getData['kode_sewa'],
			'status_isi'   => $getData['status_isi']	
		];
		$offset = $getData['start'];
		$limit = $getData['length'];
		$lastState = '&kode_sewa='.$getData['kode_sewa'].'&status_isi='.$getData['status_isi'];

		$totalRows = $this->model->get_num_data($params,$search)['jumlah'];
		$dataQuery = $this->model->get_data($limit, $offset, $search, $order,  $params);
		
		$data = [];
		
		if ($dataQuery) {
			foreach ($dataQuery as $idx => $row) {
				$encId 		= $this->encryption_lib->urlencode($row['sewa_id']);
				$linkUpdate = site_url('aset_manajemen_barang/sewa_aset/update?encId='.$encId.$lastState);
				$linkCetakPdf = site_url('aset_manajemen_barang/sewa_aset/cetak_pdf/'.$encId);
				

				$btnPdf = '<a 
							href="'.$linkCetakPdf.'" 
							target="BLANK_"
							class="btn btn-primary btn-sm tooltip-trigger">
							<span
							class="fa fa-download"></span>
							</a>
							';
				$btnDetail 	= '
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
								'sewa_aset',
								'view_detail',
								$encId).'"
							>
							<span class="fa fa-info-circle"></span>
							</a>&nbsp;
								';
				if($row['pengembalian']==0){
					$btnUpdate = '<a 
							href="'.$linkUpdate.'" 
							class="btn btn-success btn-sm tooltip-trigger">
							<span
							class="fa fa-pencil"></span>
							</a>
							';
				}else{
					$btnUpdate = "";
				}


				$data[] = [
					$idx + 1 + $offset
					, $row['sewa_nomor']
					, $row['unitkerja']
					, tgl_indo($row['sewa_tanggal_awal'])
					, $row['sewa_waktu_awal']
					, tgl_indo($row['sewa_tanggal_akhir'])
					, $row['sewa_waktu_akhir']
					, $btnPdf.$btnDetail.$btnUpdate
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
		$getData 				= $this->input->get();
		$kode_sewa 				= (!empty($getData['kode_sewa']))?$getData['kode_sewa']:'';
		$status_isi 			= (!empty($getData['status_isi']))?$getData['status_isi']:'';
		$lastState 				= '?kode_sewa_='.$kode_sewa.'&status_isi_='.$status_isi;
		$data['url_action']		= site_url('aset_manajemen_barang/sewa_aset/add_proses').$lastState;
		$data['url_back']		= site_url('aset_manajemen_barang/sewa_aset/view').$lastState;
		$data['dataUnitKerja']	= $this->model->get_unit_kerja();
		$data['form_label'] 	= 'Tambah Data Sewa Aset';
		$data['isProses'] 		= 'add';

		$this->template->load_view('add', $data);
	}
	public function add_proses()
	{	
		$this->load->library('form_validation');
		$this->form_validation->set_rules('tanggal_mulai', 'Tanggal Sewa ', 'trim|required');
		$this->form_validation->set_rules('penyewa', 'Nama Penyewa ', 'trim|required');
		$this->form_validation->set_rules('kontak', 'Telp ', 'trim|required');
		$this->form_validation->set_rules('mkId', 'Mitra ', 'trim|required');
		$this->form_validation->set_rules('unit', 'Unit Kerja  ', 'trim|required');
		$this->form_validation->set_message('required', 'Isian %s Belum Dilengkapi.');

		$getData = $this->input->get();
		$lastState = 	'?kode_sewa_='.$getData['kode_sewa_'].
		'&status_isi_='.$getData['status_isi_'];
		if ($this->form_validation->run($this) == FALSE) {
			$data['url_action']	= site_url('aset_manajemen_barang/sewa_aset/add_proses').$lastState;
			$data['url_back']	= site_url('aset_manajemen_barang/sewa_aset/view').$lastState;
			$data['form_label'] = 'Tambah Data Sewa Aset';
			$data['isProses'] 	= 'add';
			$this->template->load_view('add', $data);
		}else{
			extract($this->input->post());
			$this->aset->trans_start();
			$nomor_sewa = "SW".".".date('Y.His');

			$data_insert = array(
				'sewaNoSewa' 			=> $nomor_sewa, 
				'sewaNama' 				=> $penyewa, 
				'sewaNomorContact' 		=> $kontak,
				'sewaAlamatPenyewa' 	=> $alamat,
				'sewaKeterangan' 		=> $keterangan,
				'sewaJumlahHari' 		=> $jmlHari,
				'sewaTanggalSewa' 		=> $this->date_today,
				'sewaTanggalMulai' 		=> $tanggal_mulai,
				'sewaJamMulai' 			=> $jamMulai,
				'sewaTanggalSelesai' 	=> $tanggal_selesai,
				'sewaJamSelesai' 		=> $jamSelesai,
				'sewaUnitId' 			=> $unit,
				'sewaMitraId' 			=> $mkId,
				'sewaTanggal' 			=> $this->date_today,
				'sewaUserId' 			=> $this->model->userAsetSession()['user_id']
			);
			$insert_id = $this->model->insert_data_id('aset_sewa',$data_insert);

			$n_error = 0;

			foreach ($aset_id as $rowIdx => $value) {


				$data_insert_detail = array(
					'asetSewaDetSewaid'=>$insert_id,
					'asetSewaDetBiayaSewaId'=>$aset_id[$rowIdx],
					'asetSewaDetValue'=>str_replace('.', '', explode(',', $aset_harga_sewa[$rowIdx])[0]),
					'asetSewaDetPotongan'=>str_replace('.', '', explode(',', $aset_harga_potongan[$rowIdx])[0])
				);
				$this->model->insert_data('aset_sewa_det',$data_insert_detail);
			}

			$this->aset->trans_complete();
			if ($this->aset->trans_status() === TRUE) {
				$this->aset->trans_commit();
				$this->notice("Berhasil menambah data", "success");
				redirect('aset_manajemen_barang/sewa_aset/view');
			} else {
				$this->aset->trans_rollback();
				$this->notice("Gagal menyimpan ke dalam database", "danger");
				redirect('aset_manajemen_barang/sewa_aset/view');
			}
		}
	}


	public function update()
	{
		$getData 					= $this->input->get();
		$data['id'] 				= $this->encryption_lib->urldecode($getData['encId']);
		$kode_sewa 					= (!empty($getData['kode_sewa']))?$getData['kode_sewa']:'';
		$status_isi 				= (!empty($getData['status_isi']))?$getData['status_isi']:'';
		$lastState 					= 'kode_sewa_='.$kode_sewa.'&status_isi_='.$status_isi;

		$data['data_sewa']			= $this->model->get_sewa_byid($data['id']);
		$data['data_sewa_detail']	= $this->model->get_sewa_detail($data['id']);
		$data['dataUnitKerja']	= $this->model->get_unit_kerja();

		$data['url_action'] 	= site_url('aset_manajemen_barang/sewa_aset/update_proses?encId=').$getData['encId'].'&'.$lastState;
		$data['url_back']		= site_url('aset_manajemen_barang/sewa_aset/view?').$lastState;
		$data['form_label'] 	= 'Ubah Data Mutasi Antar Unit';
		$data['isProses'] 		= 'update';
		$this->template->load_view('update', $data);
	}

	public function update_proses()
	{
		$this->load->library('form_validation');
		

		$this->form_validation->set_rules('penyewa', 'Nama Penyewa ', 'trim|required');
		$this->form_validation->set_rules('kontak', 'Telp ', 'trim|required');
		$this->form_validation->set_message('required', 'Isian %s Belum Dilengkapi.');

		$getData 		= $this->input->get();
		$id 			= $this->encryption_lib->urldecode($getData['encId']);
		$lastState 		= '?kode_sewa_='.$getData['kode_sewa_'].'&status_isi_='.$getData['status_isi_'];

		if ($this->form_validation->run($this) == FALSE) {
			$data['url_action'] 		= site_url('aset_manajemen_barang/sewa_aset/update_proses/').$getData['encId'].'/'.$lastState;
			$data['url_back']			= site_url('aset_manajemen_barang/sewa_aset/view').$lastState;
			$data['data_sewa']			= $this->model->get_sewa_byid($id);
			$data['data_sewa_detail']	= $this->model->get_sewa_detail($id);
			$data['dataUnitKerja']		= $this->model->get_unit_kerja();
			$data['form_label'] 		= 'Ubah Data Sewa Aset';
			$data['isProses'] 			= 'update';
			$this->template->load_view('update', $data);
		}else{
			extract($this->input->post());
			$this->aset->trans_start();
			$nomor_sewa = "SW".".".date('Y.His');
			$data_update = array(
				'sewaNoSewa' 			=> $nomor_sewa, 
				'sewaNama' 				=> $penyewa, 
				'sewaNomorContact' 		=> $kontak,
				'sewaAlamatPenyewa' 	=> $alamat,
				'sewaKeterangan' 		=> $keterangan,
				'sewaJumlahHari' 		=> $jmlHari,
				'sewaTanggalSewa' 		=> $this->date_today,
				'sewaTanggalMulai' 		=> $tanggal_mulai,
				'sewaJamMulai' 			=> $jamMulai,
				'sewaTanggalSelesai' 	=> $tanggal_selesai,
				'sewaJamSelesai' 		=> $jamSelesai,
				'sewaUnitId' 			=> $unit,
				'sewaMitraId' 			=> $mkId,
				'sewaTanggal' 			=> $this->date_today,
				'sewaUserId' 			=> $this->model->userAsetSession()['user_id']
			);
			$this->model->update_data('aset_sewa','sewaId',$id,$data_update);

			$this->model->delete_data('aset_sewa_det','asetSewaDetSewaid',$id);
			foreach ($aset_id as $rowIdx => $value) {
				$data_insert_detail = array(
					'asetSewaDetSewaid'=>$id,
					'asetSewaDetBiayaSewaId'=>$aset_id[$rowIdx],
					'asetSewaDetValue'=>str_replace('.', '', explode(',', $aset_harga_sewa[$rowIdx])[0]),
					'asetSewaDetPotongan'=>str_replace('.', '', explode(',', $aset_harga_potongan[$rowIdx])[0])
				);
				$this->model->insert_data('aset_sewa_det',$data_insert_detail);
			}

			$this->aset->trans_complete();
			if ($this->aset->trans_status() === TRUE) {
				$this->aset->trans_commit();
				$this->notice("Berhasil mengubah data", "success");
				redirect('aset_manajemen_barang/sewa_aset/view?'.$lastState);
			} else {
				$this->aset->trans_rollback();
				$this->notice("Gagal menyimpan ke dalam database", "danger");
				redirect('aset_manajemen_barang/sewa_aset/view?'.$lastState);
			}
		}
	}


	public function validasi_proses($value='')
	{
		$tanggal_mulai = $this->input->post('tanggal_mulai');
		$tanggal_selesai = $this->input->post('tanggal_selesai');
		$aset_id = $this->input->post('aset_id');

		if (!$tanggal_mulai) {
			$this->form_validation->set_message('validate_table_rincian', 'Rincian harus diisi!');
			return false;
		}
		return true;
	}

	public function form($enc_id='')
	{
		if ($this->input->method(true) == 'POST') {
			$this->load->library('form_validation');
			extract($this->input->post());
			if($isProses=='update'){ //** Proses update

			}else{ //** Proses add

			}
		}else{
			$data['id'] 		= $this->encryption_lib->urldecode($enc_id);
			$data['url_back']	= site_url('aset_manajemen_barang/sewa_aset/view');
			if(!empty($data['id'])||$data['id']!=0){ //** Update Data
				$data['url_action']	= site_url('aset_manajemen_barang/sewa_aset/form/'.$enc_id);
				$data['form_label'] = 'Ubah Data Sewa Aset';
				$data['isProses'] = 'update';
			}else{ //** Add Data
				$data['url_action']	= site_url('aset_manajemen_barang/sewa_aset/form');
				$data['form_label'] = 'Tambah Data Sewa Aset';
				$data['isProses'] = 'add';
			}
			$this->template->load_view('form', $data);
		}
	}
	public function list_barang($offset=0,$search_='')
	{
		

		$search = (!empty($search_))?$search_:(!empty($this->input->post('search')))?$this->input->post('search'):'';
		$data['search'] = $search;
		$this->load->library('jquery_pagination');
		$config['base_url'] = site_url('aset_manajemen_barang/sewa_aset/list_barang/');
		$config['per_page'] = 10;
		$config['url_location'] = 'modal-data-basic';
		$config['base_filter'] = '/'.$search;
		$config['uri_segment'] = 4;
		$config['full_tag_open'] = '<ul class="pagination paging urlactive">';
		$config['total_rows'] = $this->model->num_aset_sewa($search)['jumlah'];
		$this->jquery_pagination->initialize($config);
		$data['content']    = $this->model->get_aset_sewa($config['per_page'],$offset,$search);
		$data['halaman'] = $this->jquery_pagination->create_links();
		$data['offset'] = $offset;

		$data['linkForm']   = site_url('aset_manajemen_barang/sewa_aset/list_barang/0/'.$search);

		$this->load->view('modal_barang',$data);
	}
	public function get_aset_sewa()
	{
		$data = $this->model->get_aset_sewa($this->input->get('term'));
		$i = 0;

		if ($data) {
			foreach ($data AS $row):
				$arr[$i]['aset_id'] 		= $row['aset_id'];
				$arr[$i]['aset_kode'] 		= $row['aset_kode'];
				$arr[$i]['aset_label'] 		= $row['aset_label'];
				$arr[$i]['aset_harga_sewa'] = $row['aset_harga_sewa'];
				$arr[$i]['status_id'] 		= $row['status_id'];
				$i++;
			endforeach;
		} else {
			$arr[$i]['aset_id'] 		= null;
			$arr[$i]['aset_kode'] 		= null;
			$arr[$i]['aset_label'] 		= null;
			$arr[$i]['aset_harga_sewa'] = null;
			$arr[$i]['status_id'] 		= null;
		}

		echo json_encode($arr);
	}
	
	public function get_mitra()
	{
		$data = $this->model->get_mitra($this->input->get('term'));
		$n_mitra = 0;

		if ($data) {
			foreach ($data AS $row):
				$arr[$n_mitra]['mkId'] 		= $row['mkId'];
				$arr[$n_mitra]['mkNama'] 	= $row['mkNama'];
				$arr[$n_mitra]['mkAlamat'] 	= $row['mkAlamat'];
				$arr[$n_mitra]['mkTelepon'] = $row['mkTelepon'];
				$n_mitra++;
			endforeach;
		} else {
			$arr[$n_mitra]['mkId'] 		= null;
			$arr[$n_mitra]['mkNama'] 	= null;
			$arr[$n_mitra]['mkAlamat'] 	= null;
			$arr[$n_mitra]['mkTelepon'] = null;
		}

		echo json_encode($arr);
	}

	public function cetak_pdf($encId)
	{
		$this->load->library('encryption_data');
		// =======================================
		$data['id'] 			= $this->encryption_lib->urldecode($encId);
		$data['content'] 			= $this->model->get_pengembalian($data['id']);
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

		$data['header'] = "Sewa Aset";

		$html = $this->load->view('aset_manajemen_barang/sewa_aset/cetak_pdf', $data,true);
		$dompdf = new Dompdf();
		$dompdf->loadHtml($html);
		$dompdf->render();
		$dompdf->stream("penyusutan_aset",array("Attachment"=>0));

	}

	public function view_detail($encId)
	{
		$data['id'] 	= $this->encryption_lib->urldecode($encId);
		//$data['content'] = $this->model->get_pengembalian($data['id']);
		$data['data_sewa']			= $this->model->get_sewa_byid($data['id']);
		$data['data_sewa_detail']	= $this->model->get_sewa_detail($data['id']);

		/*foreach ($data['content'] as $key_ => $row_) {
			$data['kembali_nomor'] 		= $row_['kembali_nomor'];
			$data['kembali_tanggal'] 	= ($row_['kembali_tanggal']!='0000-00-00')?tgl_indo($row_['kembali_tanggal']):'';
			$data['kembali_keterangan'] = $row_['kembali_keterangan'];
		}*/
		
		$data['kembali_nama'] 		= $this->model->userAsetSession()['user_nama'];

		$this->load->view('view_detail', $data);
	}


}