<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Controller Register transaksi harian
 * @created : 2022-01-11
 * @author  : Sriharyo <sriharyo@ugm.ac.id>
 * @company : DSSDI UGM
 */


class Register_transaksi_harian extends UGM_Controller {
    
    private $aset;
    private $ret_css_status;
	private $ret_message;
	private $ret_url;
	private $ci_last_generate;
    private $total_page = 10;

    public function __construct() {
        parent::__construct();
        $this->load->helper('custom');
        $this->load->library('response');
        $this->load->library('Encryption_lib');
        $this->load->library('Jquery_pagination');
        $this->load->library('form_validation');
        $this->load->model('model_register_transaksi_harian','model');
        
    }

    public function index() {
        redirect('aset_manajemen_bhp/register_transaksi_harian/view');
    }

    public function view()
    {
        $data['url_add']        = site_url('aset_manajemen_bhp/register_transaksi_harian/add');
        $data['linkDataTable']  = site_url('aset_manajemen_bhp/register_transaksi_harian/view_dtable');
        $this->template->load_view('view',$data);
    }
    public function view_dtable()
    {
        $getData = $this->input->get();
        $columns =  [   
            0 => '',
            1 => 'trans_tanggal',    
            2 => 'trans_nomor', 
            3 => 'trans_nama',    
            4 => 'trans_unit',
            5 => 'trans_keterangan',
            6 => '' ];

        $search = !empty($getData['search']['value'])?$getData['search']['value']:'';
        if ($columns[$getData['order'][0]['column']] == 'trans_tanggal') {
            $order = $columns[$getData['order'][0]['column']] . " " . 'DESC';
        }else{
            $order = $columns[$getData['order'][0]['column']] . " " . $getData['order'][0]['dir'];
        }
        $offset = $getData['start'];
        $limit = $getData['length'];
        $params = [
            'notransaksi' => $getData['notransaksi'],
            'nama' => $getData['nama'],
            'tanggal_mulai' => $getData['tanggal_mulai'],
            'tanggal_selesai' => $getData['tanggal_selesai']
        ];
        $totalRows = $this->model->get_num_data($params,$search);
        $dataQuery = $this->model->get_data($limit, $offset, $search, $order,  $params);
        if ($dataQuery) {
            foreach ($dataQuery as $idx => $row) {
                $encId      = $this->encryption_lib->urlencode($row['trans_id']);
                $btn_detail = '<a 
                    rel="async"
                    ajaxify = "' . modal('Detail Transaksi', 'aset_manajemen_bhp', 'register_transaksi_harian', 'detail', $encId) . '"
                    class="btn btn-info btn-sm tooltip-trigger"
                    data-toggle="tooltip"
                    data-placement="top"
                    title="Log Sirkulasi Barang">
                    <span class="fa fa-clock-o"></span>
                    </a>';
                $btn_cetak =
                '<a 
					href="' . site_url('aset_manajemen_bhp/register_transaksi_harian/cetak/'. $encId) . '" 
					target="BLANK_"
					class="btn btn-primary btn-sm tooltip-trigger">
					<span
					class="fa fa-download"></span>
					</a>
					';
                $data[] = [
					$idx + 1 + $offset
					, tgl_indo($row['trans_tanggal'])
					, $row['trans_nomor']
					, $row['trans_nama']
					, $row['trans_unit']
					, $row['trans_keterangan'] 
                    , $btn_cetak.'&nbsp;'.$btn_detail
                ];
            }
        }
        $jsonData = [
			"draw" => intval($getData['draw']),
			"recordsTotal" => intval($totalRows),
			"recordsFiltered" => intval($totalRows),
			"data" => isset($data)? $data:[]
		];
		echo json_encode($jsonData);
    }

    public function detail($encId)
    {
        $id                 = $this->encryption_lib->urldecode($encId);
        $data['content']    = $this->model->get_detail($id);

        $this->load->view('aset_manajemen_bhp/register_transaksi_harian/detail',$data);
    }
    public function cetak($encId)
    {
        $this->load->library('encryption_data');
        $data['id']             = $this->encryption_lib->urldecode($encId);

        $path = './ugmfw-assets/images/logo-ugm-hp.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $dataImg = file_get_contents($path);
        $data['logo'] = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);
        $data['header'] = "Register Transaksi Harian ";
        $html = $this->load->view('aset_manajemen_bhp/register_transaksi_harian/cetak', $data, true);
        
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream("register_transaksi_harian", array("Attachment" => 0));
    }
    public function add()
    {
        $getData    = $this->input->get();

        $notransaksi    = !empty($getData['notransaksi'])?$getData['notransaksi']:'';
        $nama           = !empty($getData['nama'])?$getData['nama']:'';
        $periode        = !empty($getData['periode'])?$getData['periode']:'';

        $data['data_unit_pj'] = $this->model->get_unit_pj(); 

        $data['isProses']   = 'add';
        $data['form_label'] = 'Tambah Data';
        $data['url_action'] = site_url('aset_manajemen_bhp/register_transaksi_harian/add_proses');
        $data['url_back'] = site_url('aset_manajemen_bhp/register_transaksi_harian/view?notransaksi='.$notransaksi.'&nama='.$nama.'$periode='.$periode);

        $this->template->load_view('add',$data);
    }

    public function add_proses()
    {

    }

    public function list_barang($offset = 0,$id_bidang_ = '', $id_kelompok_ = '', $id_sub_kelompok_ = '', $nama_barang_ = ''){
        $getData            = $this->input->get();
        $id_bidang          = !empty($getData['id_bidang_']) ? $getData['id_bidang_'] : (!empty($id_bidang_) ? $id_bidang_ : '');
        $id_kelompok        = !empty($getData['id_kelompok_']) ? $getData['id_kelompok_'] : (!empty($id_kelompok_) ? $id_kelompok_ : '');
        $id_sub_kelompok    = !empty($getData['id_sub_kelompok_']) ? $getData['id_sub_kelompok_'] : (!empty($id_sub_kelompok_) ? $id_sub_kelompok_ : '');
        $nama_barang        = !empty($getData['nama_barang_']) ? $getData['nama_barang_'] : (!empty($nama_barang_) ? $nama_barang_ : '');

        $params = [
            'id_bidang'         => $id_bidang,
            'id_kelompok'       => $id_kelompok,
            'id_sub_kelompok'   => $id_sub_kelompok,
            'nama_barang'       => $nama_barang,
        ];

        $this->load->library('jquery_pagination');
        $config['base_url']         = site_url('aset_manajemen_bhp/register_transaksi_harian/list_barang/');
        $config['per_page']         = 10;
        $config['url_location']     = 'modal-data-basic';
        $config['base_filter']      = '/' .  $id_bidang . ',' . $id_kelompok . ',' . $id_sub_kelompok . ',' . $nama_barang;
        $config['uri_segment']      = 4;
        $config['full_tag_open']    = '<ul class="pagination paging urlactive">';
        $config['total_rows']       = $this->model->get_num_barang($params);

        $this->jquery_pagination->initialize($config);
        $data['content']            = $this->model->get_barang($config['per_page'], $offset, $params);

        $data['halaman']            = $this->jquery_pagination->create_links();
        $data['offset']             = $offset;

        $data['linkForm']           = site_url('aset_manajemen_bhp/register_transaksi_harian/list_barang/0/' . $id_bidang . ',' . $id_kelompok . ',' . $id_sub_kelompok . ',' . $nama_barang); 

        $this->load->view('list_barang', $data);
    }
}