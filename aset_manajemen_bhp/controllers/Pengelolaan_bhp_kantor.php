<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Controller Pengelolaan BHP kantor
 * @created : 2022-01-11
 * @author  : Sriharyo <sriharyo@ugm.ac.id>
 * @company : DSSDI UGM
 */


class Pengelolaan_bhp_kantor extends UGM_Controller
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
        $this->load->model('model_pengelolaan_bhp_kantor', 'model');
        $this->load->model('model_master');
    }

    public function index()
    {
        redirect('aset_manajemen_bhp/pengelolaan_bhp_kantor/view');
    }

    public function view()
    {
        $data['url_add'] = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/add');
        $data['linkDataTable'] = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/view_dtable');
        $this->template->load_view('view', $data);
    }
    public function view_dtable()
    {
        $getData = $this->input->get();
        $columns =  [
            0 => '',
            1 => 'kode_barang',
            2 => 'nama_barang',
            3 => 'merk',
            4 => 'jumlah_stok',
            5 => 'stok_minimal',
            6 => 'lokasi_gudang',
            7 => 'unit',
            8 => ''
        ];

        $search = $getData['search']['value'];
        if ($columns[$getData['order'][0]['column']] == 'kode_barang') {
            $order = $columns[$getData['order'][0]['column']] . " " . 'DESC';
        } else {
            $order = $columns[$getData['order'][0]['column']] . " " . $getData['order'][0]['dir'];
        }
        $offset = $getData['start'];
        $limit = $getData['length'];
        $params = [
            'gudang'        => $getData['gudang'],
            'kode_barang'   => $getData['kode_barang'],
            'status_stok'   => $getData['status_stok']
        ];
        $lastState =    $this->encryption_lib->urlencode(
            '--'. $getData['gudang'].
            '--'. $getData['kode_barang'].
            '--'. $getData['status_stok']
        );
        $totalRows = $this->model->get_num_data($params, $search);
        $dataQuery = $this->model->get_data($limit, $offset, $search, $order,  $params);
        $data = array();
        if ($dataQuery) {
            foreach ($dataQuery as $idx => $row) {
                $encId          = $this->encryption_lib->urlencode($row['atk_id']);
                $barangId       = $this->encryption_lib->urlencode($row['barangId']);
                $ruangId        = $this->encryption_lib->urlencode($row['ruangId']);
                $linkUpdate     = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/update/'.$encId.'/'. $barangId.'/'. $ruangId.'/'.$lastState);
                $btn_ubah       =  '
                    <a 
                    href="' . $linkUpdate . '"
                    class="btn btn-success btn-sm tooltip-trigger"
                    data-toggle="tooltip"
                    data-placement="top"
                    title="Ubah setting stok">
                    <span class="fa fa-pencil"></span>
                    </a>&nbsp;';
                // Modal
                $btn_log        =  '
                    <a 
                    rel="async"
                    ajaxify = "'. modal('Log Sirkulasi BHP Kantor', 'aset_manajemen_bhp', 'pengelolaan_bhp_kantor','log','0', $row['barangId'], $row['ruangId']).'"
                    class="btn btn-info btn-sm tooltip-trigger"
                    data-toggle="tooltip"
                    data-placement="top"
                    title="Log Sirkulasi Barang">
                    <span class="fa fa-clock-o"></span>
                    </a>&nbsp;';
                if($row['jumlah_stok'] <= 0){
                    $linkAdj        = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/adjustmen/' . $barangId . '/' . $ruangId . '/' . $lastState);
                    $btn_adj =  '
                    <a 
                    href="' . $linkAdj . '"
                    class="btn btn-warning btn-sm tooltip-trigger"
                    data-toggle="tooltip"
                    data-placement="top"
                    title="Adjustmen">
                    <span class="fa fa-list"></span>
                    </a>&nbsp;';
                }else{
                    $btn_adj ="";
                }
                $data[] = [
                    $idx + 1 + $offset, 
                    $row['kode_barang'], 
                    $row['nama_barang'], 
                    $row['merk'], 
                    $row['jumlah_stok'], 
                    $row['stok_minimal'], 
                    $row['lokasi_gudang'], 
                    $row['unit'],
                    $btn_ubah.'&nbsp;'. $btn_log.'&nbsp;'. $btn_adj
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
        $data['form_label'] = 'Penambahan Data BHP Kantor';
        $data['url_action'] = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/add_proses');
        $data['url_back'] = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/view');
        $data['satuan'] = $this->model->get_satuan();

        $this->template->load_view('add', $data);
    }

    public function add_proses()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('barcode_kuitansi', 'Barcode Kuitansi ', 'trim|required');
        $this->form_validation->set_rules('id_gudang', 'Data Gudang ', 'trim|required');
        $this->form_validation->set_rules('id_barang', 'Kode Barang ', 'trim|required');
        $this->form_validation->set_rules('tanggal_pembelian', 'Tanggal Pembelian ', 'trim|required');
        $this->form_validation->set_rules('jumlah_barang', 'Jumlah Barang ', 'trim|required');
        $this->form_validation->set_rules('total_barang', 'Total Barang ', 'trim|required');
        $this->form_validation->set_message('required', 'Isian %s Belum Dilengkapi.');

        $user_unit = $this->model->userAsetSession();

        if ($this->form_validation->run($this) == FALSE) {
            $data['form_label'] = 'Penambahan BHP Kantor';
            $data['form_label'] = 'Form Tambah Data';
            $data['url_action'] = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/add_proses');
            $data['url_back'] = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/view');
            $this->template->load_view('add', $data);
        } else {
            $this->aset->trans_begin();
            extract($this->input->post());

            $mstId = $this->model->get_mtsId($id_barang);

            if (!empty($mstId)) {
                $dataMst = array(
                    'invAtkMstJenisPersediaanId' => $id_barang,
                    'invAtkMstSatuanBarang' => $jumlah_barang,
                    'invAtkMstKeteranganLain' => $keterangan,
                    'invAtkMstUserId' => $user_unit['user_id']
                );
                $insert_mst = $this->model->insert_ignore('inv_atk_master', $dataMst);
                if ($insert_mst) {
                    /* $mstStat = [
                        'logAtkRuangId' => '',
                        'logAtkAtkId' => '',
                        'logAtkJmlBrg' => '',
                        'logAtkStatus' => '',
                        'logAtkUnitId' => '',
                        'logAtkTujuanRuangId' => '',
                        'logAtkTgl' => '',
                        'logAtkBAMutasi' => '',
                        'logAtkKeterangan' => '',
                        'logAtkFile' => '',
                    ]; */
                } else {
                    /* $mstStat = [
                        'logAtkRuangId'         => '',
                        'logAtkAtkId'           => '',
                        'logAtkJmlBrg'          => '',
                        'logAtkStatus'          => '',
                        'logAtkUnitId'          => '',
                        'logAtkTujuanRuangId'   => '',
                        'logAtkTgl'             => '',
                        'logAtkBAMutasi'        => '',
                        'logAtkKeterangan'      => '',
                        'logAtkFile'            => ''
                    ]; */

                }
            } else {
                $mstStat = true;
            }

            $dataDetAtk = array(
                'barcode_barang'    => $barcode_barang,
                'mstId'             => $mstId
            );

            $data;

            $data_insert = '';
            $this->model->insert_data('inv_peruntukan_tanah', $data_insert);
            if ($this->model->aset->trans_status() === FALSE) {
                $this->model->aset->trans_rollback();
                $this->ret_css_status = 'danger';
                $this->ret_message = 'Gagal Tambah Data';
            } else {
                $this->model->aset->trans_commit();
                $this->ret_css_status = 'success';
                $this->ret_message = 'Berhasil Tambah Data';
            }
            $this->ret_url = site_url('aset_manajemen_barang/inventarisasi_bhp/view');
            echo json_encode(['status' => $this->ret_css_status, 'msg' => $this->ret_message, 'url' => $this->ret_url]);
            exit;
        }
    }
    // ** Update
    public function update($encId='', $barangId='',$ruangId='',$lastState__)
    {
        $data['id']             = !empty($encId)?$this->encryption_lib->urldecode($encId):'';
        $data['barang']         = $this->encryption_lib->urldecode($barangId);
        $data['ruang']          = $this->encryption_lib->urldecode($ruangId);
        $lastState_             = explode('--', $this->encryption_lib->urldecode($lastState__));
        $lastState              = '?gudang='. $lastState_[0].'&&kode_barang'. $lastState_[1].'&&status_stok'. $lastState_[2];
        $data['lastState']      = $lastState;   
        $data['form_label']     = 'Perubahan Data BHP Kantor';
        $data['url_action']     = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/update_proses/'. $lastState__);
        $data['url_back']       = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/view'. $lastState);

        $dataAtk        = $this->model->get_atk_id($data['id']);
        $dataAtkBarang  = $this->model->get_atk_barang_id($data['barang'], $data['ruang']);
        if(!empty($dataAtk['stockSetId']))
        {
            $data['id_barang']      = $dataAtk['stockSetId'];
            $data['kode_barang']    = $dataAtk['kode_barang'];
            $data['nama_barang']    = $dataAtk['nama_barang'];
            $data['gudang']         = $dataAtk['gudang_nama'];
            $data['stok_minimal']   = $dataAtk['stok_minimal'];
            $data['keterangan']     = $dataAtk['keterangan'];
        }else{
            $data['id_barang']      = $dataAtkBarang['barang_id'];
            $data['kode_barang']    = $dataAtkBarang['kode_barang'];
            $data['nama_barang']    = $dataAtkBarang['nama_barang'];
            $data['gudang']         = $dataAtkBarang['gudang_nama'];
            $data['stok_minimal']   = '0';
            $data['keterangan']     = '';
        }
        $this->template->load_view('update',$data);
    }

    public function update_proses()
    {
        extract($this->input->post());
        if($id){
            $data_atk = [
                'stockSetMin' => $stok_minimal,
                'stcokSetKet' => $keterangan
            ];
            $this->model->update_data('setting_stok_atk', 'stockSetId', $id, $data_atk);
        }else{
            $data_atk = [
                'stockSetMin'=>$stok_minimal,
                'stcokSetKet'=>$keterangan,
                'stockSetJenisPersediaanId' => $barang,
                'stockSetRuangId' => $ruang
            ];
            $this->model->insert_data('setting_stok_atk', $data_atk);
        }
        if ($this->model->aset->trans_status() === FALSE) {
            $this->model->aset->trans_rollback();
            $this->ret_css_status = 'danger';
            $this->ret_message = 'Gagal Ubah Data';
        }else{
            $this->model->aset->trans_commit();
            $this->ret_css_status = 'success';
            $this->ret_message = 'Berhasil Ubah Data';
        }
        $this->ret_url = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/view'. $lastState);
        echo json_encode(['status' => $this->ret_css_status, 'msg' => $this->ret_message, 'url' => $this->ret_url]);
        exit;
    }
  
    // ** Log
    public function log($offset = 0, $barang='', $ruang='', $tanggal_mulai_='', $tanggal_selesai_='')
    {
        $this->load->library('jquery_pagination');
        $postData           = $this->input->post();

        $tanggal_mulai__    = !empty($postData['tanggal_mulai']) ? $postData['tanggal_mulai'] : '';
        $tanggal_mulai      = !empty($tanggal_mulai__) ? $tanggal_mulai__ : $tanggal_mulai_;
        $tanggal_selesai__  = !empty($postData['tanggal_selesai']) ? $postData['tanggal_selesai'] : '';
        $tanggal_selesai    = !empty($tanggal_selesai__) ? $tanggal_selesai__ : $tanggal_selesai_;
                
        $data['log']        = $this->model->get_log($barang, $ruang);
        

        $config['base_url']         = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/log/');
        $config['per_page']         = 10;
        $config['url_location']     = 'modal-data-basic';
        $config['base_filter']      = '/' . $barang . '/' . $ruang;
        $config['uri_segment']      = 4;
        $config['full_tag_open']    = '<ul class="pagination paging urlactive">';
        $config['total_rows']       = $this->model->get_log_list_num($barang, $ruang,$tanggal_mulai,$tanggal_selesai);
        $this->jquery_pagination->initialize($config);
        $data['content']            = $this->model->get_log_list($config['per_page'], $offset, $barang, $ruang, $tanggal_mulai, $tanggal_selesai);
        $data['halaman']            = $this->jquery_pagination->create_links();
        $data['offset']             = $offset;

        $data['linkForm']           = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/log/0/' . $barang . '/'. $ruang);


        $this->load->view('log',$data);
    }
    // ** Adjustmen
    public function adjustmen($barangId,$ruangId, $lastStateKey)
    {
        // get_adj
        $data['barang']         = $this->encryption_lib->urldecode($barangId);
        $data['ruang']          = $this->encryption_lib->urldecode($ruangId);
        $lastState_             = explode('--', $this->encryption_lib->urldecode($lastStateKey));
        $data['lastState']      = '?gudang=' . $lastState_[0] . '&&kode_barang' . $lastState_[1] . '&&status_stok' . $lastState_[2];
        $data['form_label']     = "Penyesuaian Stok";
        $data['url_action']     = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/adjustmen_proses');
        $data['url_back']       = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/view'. $data['lastState']);

        $data['content']        = $this->model->get_adj($data['barang'], $data['ruang']);
        $this->template->load_view('adjustmen',$data);
    }
    public function adjustmen_proses()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('sisa_barang', 'sisa barang ', 'trim|required');
        $this->form_validation->set_message('required', 'Isian %s Belum Dilengkapi.');
        extract($this->input->post());
        if ($this->form_validation->run($this) == FALSE) {
            $data['url_action']     = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/adjustmen_proses');
            $data['url_back']       = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/view' . $lastState);
            $data['form_label']     = "Penyesuaian Stok";
            $data['url_action']     = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/adjustmen_proses');
        }else{
            $data_update = [
                'invAtkGudangJumlah'    => $sisa_barang,
                'sisa_barang'           => $sisa_barang
            ];
        }

    }
    public function list_gudang($offset = 0, $kode_ = '', $nama_ = '')
    {
        $this->load->library('jquery_pagination');
        $postData            = $this->input->post();

        $kode__                   = !empty($postData['kode']) ? $postData['kode'] : '';
        $kode                     = !empty($kode__) ? $kode__ : $kode_;
        $nama__                   = !empty($postData['nama']) ? $postData['nama'] : '';
        $nama                     = !empty($nama__) ? $nama__ : $nama_;


        $config['base_url']         = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/list_gudang/');
        $config['per_page']         = 10;
        $config['url_location']     = 'modal-data-basic';
        $config['base_filter']      = '/' . $kode . '/' . $nama;
        $config['uri_segment']      = 4;
        $config['full_tag_open']    = '<ul class="pagination paging urlactive">';
        $config['total_rows']       = $this->model->num_gudang($kode, $nama);
        $this->jquery_pagination->initialize($config);
        $data['content']            = $this->model->get_gudang($config['per_page'], $offset, $kode, $nama);
        $data['halaman']            = $this->jquery_pagination->create_links();
        $data['offset']             = $offset;

        $data['linkForm']           = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/list_gudang/0/' . $kode);

        $this->load->view('modal_gudang', $data);
    }
    public function list_barang($offset = 0, $kode_ = '', $nama_ = '')
    {
        $postData = $this->input->post();
        $kode__                   = !empty($postData['kode']) ? $postData['kode'] : '';
        $kode                     = !empty($kode__) ? $kode__ : $kode_;
        $nama__                   = !empty($postData['nama']) ? $postData['nama'] : '';
        $nama                     = !empty($nama__) ? $nama__ : $nama_;

        $this->load->library('jquery_pagination');
        $config['base_url']         = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/list_barang/');
        $config['per_page']         = 10;
        $config['url_location']     = 'modal-data-basic';
        $config['base_filter']      =
            '/' . $kode . '/' . $nama;
        $config['uri_segment']      = 4;
        $config['full_tag_open']    = '<ul class="pagination paging urlactive">';
        $config['total_rows']       = $this->model->num_barang($kode, $nama)['jumlah'];
        $this->jquery_pagination->initialize($config);
        $data['content']            = $this->model->get_barang($config['per_page'], $offset, $kode, $nama);
        $data['halaman']            = $this->jquery_pagination->create_links();
        $data['offset']             = $offset;

        $data['linkForm']           = site_url('aset_manajemen_bhp/pengelolaan_bhp_kantor/list_barang/0/' . $kode, $nama);

        $this->load->view('modal_barang', $data);
    }

    // ** Log

}