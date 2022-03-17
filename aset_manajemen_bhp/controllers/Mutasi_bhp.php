<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Controller Mutasi (bhp) bahan habis pakai
 * @created : 2022-01-11
 * @author  : Sriharyo <sriharyo@ugm.ac.id>
 * @company : DSSDI UGM
 */


class Mutasi_bhp extends UGM_Controller {
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
        $this->load->model('model_mutasi_bhp','model');

    }

    public function index() {
        redirect('aset_manajemen_bhp/mutasi_bhp/view');
    }

    public function view()
    {
        $data['linkDataTable'] = site_url('aset_manajemen_bhp/mutasi_bhp/view_dtable');
        $this->template->load_view('view',$data);
    }
    public function view_dtable()
    {
        $getData = $this->input->get();
        $columns =  [  0 => '',
            1 => 'tgl',    
            2 => 'notrans', 
            3 => 'gdgasal',    
            4 => 'unitasal',
            5 => 'gdgtujuan',
            6 => 'unittujuan',
            7 => 'jumlah',
            8 => 'pic',
            9 => ''
        ];

        $search = $getData['search']['value'];
        if ($columns[$getData['order'][0]['column']] == 'xxx_kode') {
            $order = $columns[$getData['order'][0]['column']] . " " . 'DESC';
        }else{
            $order = $columns[$getData['order'][0]['column']] . " " . $getData['order'][0]['dir'];
        }
        $offset = $getData['start'];
        $limit = $getData['length'];
        $params = [
            'no_ba'         => ($getData['no_ba'])?$getData['no_ba']:'',
            'nama_asal'     => ($getData['nama_asal'])?$getData['nama_asal']:'',
            'nama_penerima' => ($getData['nama_penerima'])?$getData['nama_penerima']:''
        ];
        $totalRows = $this->model->get_num_data($params,$search);
        $dataQuery = $this->model->get_data($limit, $offset, $search, $order,  $params);
        if ($dataQuery) {
            foreach ($dataQuery as $idx => $row) {
                $encId      = $this->encryption_lib->urlencode($row['bhpMutasiMstId']);
                $btn_detail = '<a 
                    rel="async"
                    ajaxify = "' . modal('Detail', 'aset_manajemen_bhp', 'mutasi_bhp', 'detail', $encId) . '"
                    class="btn btn-info btn-sm tooltip-trigger"
                    data-toggle="tooltip"
                    data-placement="top"
                    title="Log Sirkulasi Barang">
                    <span class="fa fa-clock-o"></span>
                    </a>';
                $data[] = [
					$idx + 1 + $offset
					, tgl_indo($row['tgl'])
					, $row['notrans']
					, $row['gdgasal']
					, $row['unitasal']
					, $row['gdgtujuan'] 					
                    , $row['unittujuan']
					, $row['jumlah']
					, $row['pic']
                    , $btn_detail
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
        $id                     = $this->encryption_lib->urldecode($encId);
        $row                    = $this->model->get_detail($id);
        $data['no_ba']          = $row['noBA'][0];
        $data['tanggal']        = $row['tglMutasi'][0];
        $data['unit_asal']      = $row['unitAsal'][0];
        $data['gudang_asal']    = $row['gudangAsal'][0];
        
        $data['conten']         = $row;

        $data['unit_tujuan']    = $row['unitTujuan'][0];
        $data['gudang_tujuan']  = $row['gudangTujuan'][0];
        $data['pic']            = $row['pic'][0];
        $data['keterangan']     = $row['keterangan'][0];

        $this->load->view('detail',$data);

    }
}