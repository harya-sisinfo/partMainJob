<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Controller Usulan (bhp) bahan habis pakai
 * @created : 2022-01-11
 * @author  : Sriharyo <sriharyo@ugm.ac.id>
 * @company : DSSDI UGM
 */


class Usulan_bhp extends UGM_Controller {

    private $aset;
    private $total_page = 10;

    public function __construct() {
        parent::__construct();
            $this->load->helper('custom');
        $this->load->library('response');
        $this->load->library('Encryption_lib');
        $this->load->library('Jquery_pagination');
        $this->load->library('form_validation');
        $this->load->model('model_usulan_bhp','model');
        $this->load->model('model_master');
    }

    public function index() {
        redirect('aset_manajemen_bhp/usulan_bhp/view');
    }

    public function view()
    {
        $data['url_add'] = site_url('aset_manajemen_bhp/usulan_bhp/add');
        $data['linkDataTable'] = site_url('aset_manajemen_bhp/usulan_bhp/view_dtable');
        $this->template->load_view('view',$data);
    }
    public function view_dtable()
    {
        $getData = $this->input->get();
        $columns =  [   0 => '',
        1 => 'unitkerjaNama',    
        2 => 'tglUsulan', 
        3 => 'nomorUsulan',    
        4 => 'jumlahItem',
        5 => 'totalUsulan',
        6 => 'status',
        7 => 'userName',
        8 => 'verifikator' ];

        $search = $getData['search']['value'];
        if ($columns[$getData['order'][0]['column']] == 'unitkerjaNama') {
            $order = $columns[$getData['order'][0]['column']] . " " . 'DESC';
        }else{
            $order = $columns[$getData['order'][0]['column']] . " " . $getData['order'][0]['dir'];
        }
        $offset = $getData['start'];
        $limit = $getData['length'];

        $tanggal_mulai      = !empty($getData['tanggal_mulai']) ? $getData['tanggal_mulai'] : '';
        $tanggal_selesai    = !empty($getData['tanggal_selesai'])?$getData['tanggal_selesai']:'';
        $unit_id            = !empty($getData['unit_id'])?$getData['unit_id']:'';
        $nama_barang        = !empty($getData['nama_barang'])?$getData['nama_barang']:'';
        $status             = !empty($getData['status']) ? $getData['status'] : '';
        $params = [
            'tanggal_mulai'     => $tanggal_mulai,
            'tanggal_selesai'   => $tanggal_selesai,
            'unit_id'           => $unit_id,
            'nama_barang'       => $nama_barang,
            'status'            => $status
        ];

        $lastState =     $tanggal_mulai.
        '--' . $tanggal_selesai.
        '--' . $unit_id.
        '--' . $nama_barang.
        '--' .  $status
        ;

        $totalRows      = $this->model->get_num_data($params,$search);
        $dataQuery      = $this->model->get_data($limit, $offset, $search, $order,  $params);
        

        if ($dataQuery) {
            foreach ($dataQuery as $idx => $row) {
                $encId          = $this->encryption_lib->urlencode($row['id']);
                 $btnUpdate      = '<a 
                                    href="'.site_url('aset_manajemen_bhp/usulan_bhp/update/'. $encId.'/'. $lastState).'"
                                    class="btn btn-success btn-sm tooltip-trigger"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="Ubah usulan BHP">
                                        <span class="fa fa-pencil"></span>
                                        </a>&nbsp;';
                $btnDetail      = '<a 
                                    href=""
                                    class="btn btn-info btn-sm tooltip-trigger"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="Detail data usulan BHP"
                                    rel="async"
                                    ajaxify="' .
                                    modal(
                                        'Detail Usulan BHP',
                                        'aset_manajemen_bhp',
                                        'usulan_bhp',
                                        'detail',
                                        $encId
                                        ) . '">
                                        <span class="fa fa-info-circle"></span>
                                        </a>&nbsp;';
                $btnApproval      = '<a 
                                    href="' . site_url('aset_manajemen_bhp/usulan_bhp/approval/' . $encId . '/' . $lastState) . '"
                                    class="btn btn-warning btn-sm tooltip-trigger"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="Approval usulan BHP"
                                    >
                                        <span class="fa fa-check"></span>
                                        </a>&nbsp;'; 
                $data[] = [
					$idx + 1 + $offset
					, $row['unitkerjaNama']
					, tgl_indo($row['tglUsulan'])
					, $row['nomorUsulan']
					, $row['jumlahItem']
					, $row['totalUsulan']
					, $row['status']
					, $row['userName']
					, $row['verifikator']
					, $btnUpdate. $btnDetail . $btnApproval
				];                
            }
        }
        $jsonData = [
			"draw" => intval($getData['draw']),
			"recordsTotal" => intval($totalRows),
			"recordsFiltered" => intval($totalRows),
			"data" => isset($data)?[]:''
		];
		echo json_encode($jsonData);
    }
// ** Tambah Usulan
    public function add()
    {
        $data['form_label']     = 'Tambah Usulan BHP';
        $data['url_action']     = site_url('aset_manajemen_bhp/usulan_bhp/add_proses');
        $data['url_back']       = site_url('aset_manajemen_bhp/usulan_bhp/view');
        $data['unit_kerja']     = $this->model->get_unit_aset();
        $data['nomor_usulan']   = 'usul.'.date('Ymd.His', strtotime($this->date_today));

        $this->template->load_view('add',$data);
    }

    public function add_proses()
    {

    }
// ** Ubah Usulan
    public function update($encId,$lastState){
        $id         = $this->encryption_lib->urldecode($encId);
        $params = explode("--", $lastState);

        $urlLastState = '?tanggal_mulai='.$params[0].'&&tanggal_selesai='. $params[1]. '&&unit_id='. $params[2]. '&&nama_barang='. $params[3]. '&&status='. $params[4] ;
        $data['form_label']     = 'Ubah Usulan BHP';
        $data['url_back']       = site_url('aset_manajemen_bhp/usulan_bhp/view'. $urlLastState);
        $data['url_action']     = site_url('aset_manajemen_bhp/usulan_bhp/update_proses/'. $lastState);
        $data['unit_kerja']     = $this->model->get_unit_aset();

        $data['row_data'] = $this->model->get_data_by_id($id);
        $data['data_detail'] = $this->model->get_detail_barang($id);


        $this->template->load_view('update',$data);
    }
    public function update_proses($lastState){
        $params = explode(" ", $lastState);

    }
// ** Approval Usulan
    public function approval($encId){
        $id          = $this->encryption_lib->urldecode($encId);
        $this->template->load_view('approval');
    }
// ** Detail Usulan
    public function detail ($encId){
        $id          = $this->encryption_lib->urldecode($encId);
        $this->load->view('detail');
    }
// ** Modal list barang
    public function list_barang($offset = 0, $id_bidang_ = '', $id_kelompok_ = '', $id_sub_kelompok_ = '', $nama_barang_ = '')
    {
        $getData = $this->input->get();
        $id_bidang = !empty($getData['id_bidang_']) ? $getData['id_bidang_'] : (!empty($id_bidang_) ? $id_bidang_ : '');
        $id_kelompok = !empty($getData['id_kelompok_']) ? $getData['id_kelompok_'] : (!empty($id_kelompok_) ? $id_kelompok_ : '');
        $id_sub_kelompok = !empty($getData['id_sub_kelompok_']) ? $getData['id_sub_kelompok_'] : (!empty($id_sub_kelompok_) ? $id_sub_kelompok_ : '');
        $nama_barang = !empty($getData['nama_barang_']) ? $getData['nama_barang_'] : (!empty($nama_barang_) ? $nama_barang_ : '');
        $params = [
            'id_bidang' => $id_bidang,
            'id_kelompok' => $id_kelompok,
            'id_sub_kelompok' => $id_sub_kelompok,
            'nama_barang' => $nama_barang,
        ];

        $this->load->library('jquery_pagination');
        $config['base_url']         = site_url('aset_manajemen_bhp/usulan_bhp/list_barang/');
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

        $data['linkForm']           = site_url('aset_manajemen_bhp/usulan_bhp/list_barang/0/' . $id_bidang . ',' . $id_kelompok . ',' . $id_sub_kelompok . ',' . $nama_barang);



        $this->load->view('modal_barang',$data);
    }
}