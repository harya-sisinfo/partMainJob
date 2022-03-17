<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Controller Inventarisasi (bhp) bahan habis pakai
 * @created : 2022-01-04
 * @author  : Sriharyo <sriharyo@ugm.ac.id>
 * @company : DSSDI UGM
 */

class Inventarisasi_bhp extends UGM_Controller
{

    private $total_page = 10;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('custom');
        $this->load->library('response');
        $this->load->library('Encryption_lib');
        $this->load->library('Jquery_pagination');
        $this->load->library('form_validation');
        $this->load->model('model_inventarisasi_bhp', 'model');
        $this->load->model('model_master');
    }

    public function index()
    {
        redirect('aset_manajemen_bhp/inventarisasi_bhp/view');
    }

    public function view()
    {
        $data['url_add'] = site_url('aset_manajemen_bhp/inventarisasi_bhp/add');
        $data['linkDataTable'] = site_url('aset_manajemen_bhp/inventarisasi_bhp/view_dtable');
        $this->template->load_view('view', $data);
    }
    // =============================================================================================
    // ** View Datatable
    public function view_dtable()
    {
        $getData = $this->input->get();
        $columns =  [
            0 => '',
            1 => 'kode',
            2 => 'nama',
            3 => 'coa',
            4 => 'stat',
            5 => ''
        ];

        $search = !empty($getData['search']['value']) ? $getData['search']['value'] : '';
        if ($columns[$getData['order'][0]['column']] == 'kode') {
            $order = $columns[$getData['order'][0]['column']] . " " . 'DESC';
        } else {
            $order = $columns[$getData['order'][0]['column']] . " " . $getData['order'][0]['dir'];
        }

        $offset = $getData['start'];
        $limit  = $getData['length'];
        $params = [
            'id_bidang'         => $getData['id_bidang'],
            'id_kelompok'       => $getData['id_kelompok'],
            'id_sub_kelompok'   => $getData['id_sub_kelompok'],
            'nama_barang'       => $getData['nama_barang']
        ];
        $lastState =
            '?id_bidang=' . $getData['id_bidang'] .
            '&id_kelompok=' . $getData['id_kelompok'] .
            '&id_sub_kelompok=' . $getData['id_sub_kelompok'] .
            '&nama_barang=' . $getData['nama_barang'];
        $totalRows = $this->model->get_num_data($params, $search);

        $rowBarang = $this->arrayDataBarang($limit, $offset, $search, $order,  $params, 'get_data');

        if (isset($rowBarang)) {
            $data = array();
            foreach ($rowBarang as $key => $row) {
                if ($row['type'] == 'atk') {
                    $encId      = $this->encryption_lib->urlencode($row['id']);
                    $linkUpdate = site_url('aset_manajemen_bhp/inventarisasi_bhp/update/' . $encId . $lastState);
                    $btn_ubah =  '
                        <a 
                        href="' . $linkUpdate . '"
                        class="btn btn-success btn-sm tooltip-trigger"
                        data-toggle="tooltip"
                        data-placement="top"
                        title="Ubah data">
                        <span class="fa fa-pencil"></span>
                        </a>&nbsp;';
                } else {
                    $encId      = '';
                    $btn_ubah = '';
                }
                $data[]     =
                    [
                        $key + 1 + $offset,
                        $row['kode'],
                        $row['nama'],
                        $row['coa'],
                        $row['stat'],
                        $btn_ubah
                    ];
            }
        } else {
            $data = array();
        }

        $jsonData = [
            "draw" => intval($getData['draw']),
            "recordsTotal" => intval($totalRows),
            "recordsFiltered" => intval($totalRows),
            "data" => $data
        ];
        echo json_encode($jsonData);
    }

    public function arrayDataBarang($limit, $offset, $search = '', $order = [],  $params = [], $modul_data)
    {
        if ($modul_data == 'get_data') {
            $dataQuery = $this->model->get_data($limit, $offset, $search, $order,  $params);
        } elseif ($modul_data == 'get_barang') {
            $dataQuery = $this->model->get_barang($limit, $offset, $search, $params);
        }

        if ($dataQuery) {
            $arrData = [];
            $arrDataBarang = [];
            $x = 0;
            $y = 0;
            $i = 0;

            foreach ($dataQuery as $key_x => $row_x) {

                $arrData['id_kelompok'][$key_x]         = $row_x['idKelompok'];
                $arrData['kode_kelompok'][$key_x]       = $row_x['kode_kelompok'];
                $arrData['nama_kelompok'][$key_x]       = $row_x['nama_kelompok'];

                $arrData['id_sub_kelompok'][$key_x]     = $row_x['idSubKelompok'];
                $arrData['kode_sub_kelompok'][$key_x]   = $row_x['kode_sub_kelompok'];
                $arrData['nama_sub_kelompok'][$key_x]   = $row_x['nama_sub_kelompok'];

                $arrData['id_barang'][$key_x]           = $row_x['idBarang'];
                $arrData['kode_barang'][$key_x]         = $row_x['kode_barang'];
                $arrData['nama_barang'][$key_x]         = $row_x['nama_barang'];

                $arrData['id_atk'][$key_x]              = $row_x['id'];
                $arrData['kode_atk'][$key_x]            = $row_x['kode'];
                $arrData['nama_atk'][$key_x]            = $row_x['nama'];
                $arrData['satuan_atk'][$key_x]          = $row_x['satuan'];
                $arrData['coa_atk'][$key_x]             = $row_x['coa'];
                $arrData['status_atk'][$key_x]          = $row_x['stat'];
                $x++;
            }


            for ($i = 0; $i < $x; $i++) {
                if ($i == 0) {
                    $arrDataBarang['id'][$y]      = '';
                    $arrDataBarang['kode'][$y]    = $arrData['kode_kelompok'][$i];
                    $arrDataBarang['nama'][$y]    = $arrData['nama_kelompok'][$i];
                    $arrDataBarang['satuan'][$y]  = '';
                    $arrDataBarang['coa'][$y]     = '';
                    $arrDataBarang['stat'][$y]    = '';
                    $arrDataBarang['type'][$y]    = 'kelompok';
                    $y++;
                    $arrDataBarang['id'][$y]      = '';
                    $arrDataBarang['kode'][$y]    = $arrData['kode_sub_kelompok'][$i];
                    $arrDataBarang['nama'][$y]    = $arrData['nama_sub_kelompok'][$i];
                    $arrDataBarang['satuan'][$y]  = '';
                    $arrDataBarang['coa'][$y]     = '';
                    $arrDataBarang['stat'][$y]    = '';
                    $arrDataBarang['type'][$y]    = 'sub_kelompok';
                    $y++;
                    $arrDataBarang['id'][$y]      = '';
                    $arrDataBarang['kode'][$y]    = $arrData['kode_barang'][$i];
                    $arrDataBarang['nama'][$y]    = $arrData['nama_barang'][$i];
                    $arrDataBarang['satuan'][$y]  = '';
                    $arrDataBarang['coa'][$y]     = '';
                    $arrDataBarang['stat'][$y]    = '';
                    $arrDataBarang['type'][$y]    = 'barang';
                    $y++;
                    $arrDataBarang['id'][$y]      = $arrData['id_atk'][$i];
                    $arrDataBarang['kode'][$y]    = $arrData['kode_atk'][$i];
                    $arrDataBarang['nama'][$y]    = $arrData['nama_atk'][$i];
                    $arrDataBarang['satuan'][$y]  = $arrData['satuan_atk'][$i];
                    $arrDataBarang['coa'][$y]     = $arrData['coa_atk'][$i];
                    $arrDataBarang['stat'][$y]    = $arrData['status_atk'][$i];
                    $arrDataBarang['type'][$y]    = 'atk';
                    $y++;
                } else {
                    if ($arrData['id_kelompok'][$i] != $arrData['id_kelompok'][$i - 1]) {
                        $arrDataBarang['id'][$y]      = '';
                        $arrDataBarang['kode'][$y]    = $arrData['kode_kelompok'][$i];
                        $arrDataBarang['nama'][$y]    = $arrData['nama_kelompok'][$i];
                        $arrDataBarang['coa'][$y]     = '';
                        $arrDataBarang['stat'][$y]    = '';
                        $arrDataBarang['type'][$y]    = 'kelompok';
                        $y++;
                    }
                    if ($arrData['id_sub_kelompok'][$i] != $arrData['id_sub_kelompok'][$i - 1]) {
                        $arrDataBarang['id'][$y]      = '';
                        $arrDataBarang['kode'][$y]    = $arrData['kode_sub_kelompok'][$i];
                        $arrDataBarang['nama'][$y]    = $arrData['nama_sub_kelompok'][$i];
                        $arrDataBarang['coa'][$y]     = '';
                        $arrDataBarang['stat'][$y]    = '';
                        $arrDataBarang['type'][$y]    = 'sub_kelompok';
                        $y++;
                    }
                    if ($arrData['id_barang'][$i] != $arrData['id_barang'][$i - 1]) {
                        $arrDataBarang['id'][$y]      = '';
                        $arrDataBarang['kode'][$y]    = $arrData['kode_barang'][$i];
                        $arrDataBarang['nama'][$y]    = $arrData['nama_barang'][$i];
                        $arrDataBarang['coa'][$y]     = '';
                        $arrDataBarang['stat'][$y]    = '';
                        $arrDataBarang['type'][$y]    = 'barang';
                        $y++;
                    }
                    if ($arrData['id_atk'][$i] != $arrData['id_atk'][$i - 1]) {
                        $arrDataBarang['id'][$y]      = $arrData['id_atk'][$i];
                        $arrDataBarang['kode'][$y]    = $arrData['kode_atk'][$i];
                        $arrDataBarang['nama'][$y]    = $arrData['nama_atk'][$i];
                        $arrDataBarang['coa'][$y]     = $arrData['coa_atk'][$i];
                        $arrDataBarang['stat'][$y]    = $arrData['status_atk'][$i];
                        $arrDataBarang['type'][$y]    = 'atk';
                        $y++;
                    }
                }
            }

            for ($n = 0; $n < $y; $n++) {
                $rowBarang[]     = [
                    'id' => $arrDataBarang['id'][$n],
                    'kode' => $arrDataBarang['kode'][$n],
                    'nama' => $arrDataBarang['nama'][$n],
                    'coa' => $arrDataBarang['coa'][$n],
                    'stat' => $arrDataBarang['stat'][$n],
                    'type' => $arrDataBarang['type'][$n]
                ];
            }
        } else {
            $rowBarang = [];
        }
        return $rowBarang;
    }
    // =============================================================================================
    // ** Modal Barang **
    public function list_barang($offset = 0, $id_bidang_ = '', $id_kelompok_ = '', $id_sub_kelompok_ = '', $nama_barang_ = '')
    {
        $getData = $this->input->get();
        $search = !empty($getData['search_']) ? $getData['search_'] : (!empty($search_) ? $search_ : '');
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
        $config['base_url']         = site_url('aset_manajemen_bhp/inventarisasi_bhp/list_barang/');
        $config['per_page']         = 10;
        $config['url_location']     = 'modal-data-basic';
        $config['base_filter']      = '/' .  $id_bidang . ',' . $id_kelompok . ',' . $id_sub_kelompok . ',' . $search;
        $config['uri_segment']      = 4;
        $config['full_tag_open']    = '<ul class="pagination paging urlactive">';
        $config['total_rows']       = $this->model->get_num_barang($params);

        $this->jquery_pagination->initialize($config);
        $data['content']            = $this->model->get_barang($config['per_page'], $offset, $params);

        $data['halaman']            = $this->jquery_pagination->create_links();
        $data['offset']             = $offset;

        $data['linkForm']           = site_url('aset_manajemen_bhp/inventarisasi_bhp/list_barang/0/' . $id_bidang . ',' . $id_kelompok . ',' . $id_sub_kelompok . ',' . $search);

        $this->load->view('modal_barang', $data);
    }
    // =============================================================================================
    // ** Modal Bidang **
    public function list_bidang($offset = 0, $search_ = '')
    {
        $search__                   = !empty($this->input->post('search')) ? $this->input->post('search') : '';
        $search                     = !empty($search__) ? $search__ : $search_;
        $data['search']             = $search;

        $this->load->library('jquery_pagination');
        $config['base_url']         = site_url('aset_manajemen_bhp/inventarisasi_bhp/list_bidang/');
        $config['per_page']         = 10;
        $config['url_location']     = 'modal-data-basic';
        $config['base_filter']      = '/' . $search;
        $config['uri_segment']      = 4;
        $config['full_tag_open']    = '<ul class="pagination paging urlactive">';
        $config['total_rows']       = $this->model->num_bidang($search)['jumlah'];
        $this->jquery_pagination->initialize($config);
        $data['content']            = $this->model->get_bidang($config['per_page'], $offset, $search);
        $data['halaman']            = $this->jquery_pagination->create_links();
        $data['offset']             = $offset;

        $data['linkForm']           = site_url('aset_manajemen_bhp/inventarisasi_bhp/list_bidang/0/' . $search);

        $this->load->view('modal_bidang', $data);
    }
    // =============================================================================================
    // ** Modal Kelompok **
    public function list_kelompok($offset = 0, $id_bidang_ = '', $search_ = '')
    {
        $search__                   = !empty($this->input->post('search')) ? $this->input->post('search') : '';
        $search                     = !empty($search__) ? $search__ : $search_;
        $id_bidang                  = !empty($id_bidang_) ? $id_bidang_ : '';

        $params                     = ['search'=> $search, 'id_bidang'=> $id_bidang];

        $data['search']             = $search;
        $data['id_bidang']          = $id_bidang;
        $this->load->library('jquery_pagination');
        $config['base_url']         = site_url('aset_manajemen_bhp/inventarisasi_bhp/list_kelompok/');
        $config['per_page']         = 10;
        $config['url_location']     = 'modal-data-basic';
        $config['base_filter']      = '/' . $id_bidang . '/' . $search;
        $config['uri_segment']      = 4;
        $config['full_tag_open']    = '<ul class="pagination paging urlactive">';

        $config['total_rows']       = $this->model->num_kelompok($params)['jumlah'];
        $this->jquery_pagination->initialize($config);
        $data['content']            = $this->model->get_kelompok($config['per_page'], $offset, $params);
        $data['halaman']            = $this->jquery_pagination->create_links();
        $data['offset']             = $offset;

        $data['linkForm']           = site_url('aset_manajemen_bhp/inventarisasi_bhp/list_kelompok/0/' . $id_bidang .'/' . $search);

        $this->load->view('modal_kelompok', $data);
    }
    // =============================================================================================
    // ** Modal Sub Kelompok **
    public function list_sub_kelompok($offset = 0, $id_bidang_ = '', $id_kelompok_ = '', $search_ = '')
    {
        $search__                   = !empty($this->input->post('search')) ? $this->input->post('search') : '';
        $search                     = !empty($search__) ? $search__ : $search_;
        $id_bidang                  = !empty($id_bidang_) ? $id_bidang_ : '';
        $id_kelompok                = !empty($id_kelompok_) ? $id_kelompok_ : '';

        $params                      = ['search'=> $search, 'id_bidang'=> $id_bidang, 'id_kelompok'=> $id_kelompok]; 

        $data['search']             = $search;

        $this->load->library('jquery_pagination');
        $config['base_url']         = site_url('aset_manajemen_bhp/inventarisasi_bhp/list_sub_kelompok/');
        $config['per_page']         = 10;
        $config['url_location']     = 'modal-data-basic';
        $config['base_filter']      = '/' . $id_bidang . '/' . $id_kelompok. '/' . $search;
        $config['uri_segment']      = 4;
        $config['full_tag_open']    = '<ul class="pagination paging urlactive">';

        $config['total_rows']       = $this->model->num_sub_kelompok($params);
        $this->jquery_pagination->initialize($config);
        $content            = $this->model->get_sub_kelompok($config['per_page'], $offset, $params);
        if (!empty($content)) {

            foreach ($content as $key => $row) {
                $barang['id'][$key]             = $row['id'];
                $barang['kode'][$key]           = $row['kode'];
                $barang['nama'][$key]           = $row['nama'];

                $barang['id_bidang'][$key]      = $row['id_bidang'];
                $barang['kode_bidang'][$key]    = $row['kodeBidang'];
                $barang['nama_bidang'][$key]    = $row['namaBidang'];

                $barang['id_kelompok'][$key]    = $row['id_kelompok'];
                $barang['kode_kelompok'][$key]  = $row['kodeKelompok'];
                $barang['nama_kelompok'][$key]  = $row['namaKelompok'];

            }

            $x = 0;

            for ($i = 0; $i < $key; $i++) {
                if ($i == 0) {
                    $barang_['id'][$x]              = $barang['id_kelompok'][$i];
                    $barang_['kode'][$x]            = $barang['kode_kelompok'][$i];
                    $barang_['nama'][$x]            = $barang['nama_kelompok'][$i];
                    $barang_['aksi'][$x]            = 'hide';

                    $x++;
                    $barang_['id'][$x]              = $barang['id'][$i];
                    $barang_['kode'][$x]            = $barang['kode'][$i];
                    $barang_['nama'][$x]            = $barang['nama'][$i];

                    $barang_['id_bidang'][$x]       = $barang['id_bidang'][$i];
                    $barang_['kode_bidang'][$x]     = $barang['kode_bidang'][$i];
                    $barang_['nama_bidang'][$x]     = $barang['nama_bidang'][$i];

                    $barang_['id_kelompok'][$x]     = $barang['id_kelompok'][$i];
                    $barang_['kode_kelompok'][$x]   = $barang['kode_kelompok'][$i];
                    $barang_['nama_kelompok'][$x]   = $barang['nama_kelompok'][$i];
                    $barang_['aksi'][$x] = 'show';

                    $x++;
                } else {
                    if ($barang['id_kelompok'][$i] != $barang['id_kelompok'][$i - 1]) {
                        $barang_['id'][$x]          = $barang['_kelompok'][$i];
                        $barang_['kode'][$x]        = $barang['kode_kelompok'][$i];
                        $barang_['nama'][$x]        = $barang['nama_kelompok'][$i];
                        $barang_['aksi'][$x]        = 'hide';
                        $x++;
                    }

                    $barang_['id'][$x]              = $barang['id'][$i];
                    $barang_['kode'][$x]            = $barang['kode'][$i];
                    $barang_['nama'][$x]            = $barang['nama'][$i];

                    $barang_['id_bidang'][$x]       = $barang['id_bidang'][$i];
                    $barang_['kode_bidang'][$x]     = $barang['kode_bidang'][$i];
                    $barang_['nama_bidang'][$x]     = $barang['nama_bidang'][$i];

                    $barang_['id_kelompok'][$x]     = $barang['id_kelompok'][$i];
                    $barang_['kode_kelompok'][$x]   = $barang['kode_kelompok'][$i];
                    $barang_['nama_kelompok'][$x]   = $barang['nama_kelompok'][$i];

                    $barang_['aksi'][$x]            = 'show';
                    $x++;
                }
            }
            



            for ($y = 0; $y < $x; $y++) {
                $data['content'][] = [
                    'id'                    => $barang_['id'][$y],
                    'kode'                  => $barang_['kode'][$y],
                    'nama'                  => $barang_['nama'][$y],
                    'id_bidang'             => isset($barang_['id_bidang'][$y])?$barang_['id_bidang'][$y]:'',
                    'nama_bidang'           => isset($barang_['nama_bidang'][$y])? $barang_['nama_bidang'][$y]:'',
                    'kode_bidang'           => isset($barang_['kode_bidang'][$y])? $barang_['kode_bidang'][$y]:'',
                    'id_kelompok'           => isset($barang_['id_kelompok'][$y]) ? $barang_['id_kelompok'][$y]: '',
                    'nama_kelompok'         => isset($barang_['nama_kelompok'][$y]) ? $barang_['nama_kelompok'][$y]: '',
                    'kode_kelompok'         => isset($barang_['kode_kelompok'][$y]) ? $barang_['kode_kelompok'][$y]: '',
                    'aksi'                  => $barang_['aksi'][$y],
                ];
            }
        }
        $data['halaman']            = $this->jquery_pagination->create_links();
        $data['offset']             = $offset;

        $data['linkForm']           = site_url('aset_manajemen_bhp/inventarisasi_bhp/list_sub_kelompok/0/' . $search);

        $this->load->view('modal_sub_kelompok', $data);
    }

    // =============================================================================================
    // ** View Form Tambah Inventarisasi BHP  **
    public function add()
    {
        $getData            = $this->input->get();

        $id_bidang          = !empty($getData['id_bidang']) ? $getData['id_bidang'] : '';
        $id_kelompok        = !empty($getData['id_kelompok']) ? $getData['id_kelompok'] : '';
        $id_sub_kelompok    = !empty($getData['id_sub_kelompok']) ? $getData['id_sub_kelompok'] : '';
        $nama_barang        = !empty($getData['nama_barang']) ? $getData['nama_barang'] : '';

        $data['lastStage']          = '?id_bidang=' . $id_bidang . '&id_kelompok=' . $id_kelompok . '&id_sub_kelompok=' . $id_sub_kelompok . '&nama_barang=' . $nama_barang;

        $data['form_label'] = 'Infentarisasi BHP';
        $data['url_action'] = site_url('aset_manajemen_bhp/inventarisasi_bhp/add_proses');
        $data['url_back'] = site_url('aset_manajemen_bhp/inventarisasi_bhp/view' . $data['lastStage']);
        $this->template->load_view('add', $data);
    }

    // =============================================================================================
    // ** Aksi Tambah Inventarisasi BHP  **
    public function add_proses()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('kode_barang', 'Kode Barang ', 'trim|required');
        $this->form_validation->set_rules('nama_barang', 'Nama Barang ', 'trim|required');
        $this->form_validation->set_rules('detail_barang', 'Detail Barang ', 'trim|required');
        $this->form_validation->set_message('required', 'Isian %s Belum Dilengkapi.');
        if ($this->form_validation->run($this) == FALSE) {
            $data['url_action'] = site_url('aset_manajemen_bhp/peruntukan_tanah/add_proses' . $this->input->post['lastStage']);
            $data['url_back']   = site_url('aset_manajemen_bhp/peruntukan_tanah/view' . $this->input->post['lastStage']);

            $data['form_label'] = 'Tambah Data Peruntukan Tanah';
        } else {
            extract($this->input->post());
            $generate = $this->model->generate_kode($kode_barang);
            $user_id = $this->model->userAsetSession()['user_id'];
            $data_insert = [
                'invAtkJenisBarangRefId'    => $id_barang,
                'invAtkKode'                => $generate,
                'invAtkNama'                => $nama_barang,
                'invAtkHargaSatuan'         => 1,
                'invAtkAktif'               => ($stat) ? 'Y' : 'N',
                'invAtkUserId'              => $user_id,
                'invAtkTglUbah'             => $this->date_today
            ];

            $this->model->insert_data('inv_atk_jenis_ref', $data_insert);
            if ($this->model->aset->trans_status() === FALSE) {
                $this->model->aset->trans_rollback();
                $this->ret_css_status = 'danger';
                $this->ret_message = 'Gagal Tambah Data';
            } else {
                $this->model->aset->trans_commit();
                $this->ret_css_status = 'success';
                $this->ret_message = 'Berhasil Tambah Data';
            }
            $this->ret_url = site_url('aset_manajemen_bhp/inventarisasi_bhp/view');
            echo json_encode(['status' => $this->ret_css_status, 'msg' => $this->ret_message, 'url' => $this->ret_url]);
            exit;
        }
    }
    // =============================================================================================
    // ** View Form Ubah Inventarisasi BHP  *
    public function update($encId)
    {
        $getData            = $this->input->get();

        $id                 = $this->encryption_lib->urlencode($encId);
        $id_bidang          = !empty($getData['id_bidang']) ? $getData['id_bidang'] : '';
        $id_kelompok        = !empty($getData['id_kelompok']) ? $getData['id_kelompok'] : '';
        $id_sub_kelompok    = !empty($getData['id_sub_kelompok']) ? $getData['id_sub_kelompok'] : '';
        $nama_barang        = !empty($getData['nama_barang']) ? $getData['nama_barang'] : '';
        $data['lastStage']  = '?id=' . $encId . '&id_bidang=' . $id_bidang . '&id_kelompok=' . $id_kelompok . '&id_sub_kelompok=' . $id_sub_kelompok . '&nama_barang=' . $nama_barang;
        $data['form_label'] = 'Infentarisasi BHP';

        //$data['content']    = $this->model->get_data_by_id($id);
        $data['url_action'] = site_url('aset_manajemen_bhp/inventarisasi_bhp/update_proses');
        $data['url_back'] = site_url('aset_manajemen_bhp/inventarisasi_bhp/view' . $data['lastStage']);

        $this->template->load_view('update', $data);
    }
}
