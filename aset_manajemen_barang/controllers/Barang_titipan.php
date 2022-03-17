<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');

/**
 * Controller Barang Titipan
 * @created : August 19, 2021 11:03 AM
 * @updated : December 24, 2021 02:28 PM
 * @author  : Alfian Hidayat <hidayat_alfian@ugm.ac.id>
 * @company : DSSDI UGM
**/

class Barang_titipan extends UGM_Controller {
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
        
		$this->load->model('Model_barang_titipan', 'model');

		$this->ret_url          = site_url('aset_manajemen_barang/barang_titipan/view');
		$this->date_today       = date("Y-m-d H:i:s");
		$this->total_page       = 30;

		$this->user_id          = $this->session->userdata('__user_id');
        $this->user_name        = $this->session->userdata('__username');
		$this->kode_unit        = $this->session->userdata('__objUser')->unit_kerja_kode;
		$this->user_level       = $this->session->userdata('__objUser')->user_level;
	}
    
	public function index() {
		redirect('aset_manajemen_barang/barang_titipan/view');
	}
    
	public function view($offset='', $search='', $status='', $unit_kerja=''){
		$data['linkView'] = site_url('aset_manajemen_barang/barang_titipan/view');
        $data['linkAdd'] = site_url('aset_manajemen_barang/barang_titipan/add');
        
        $data['linkU'] = site_url('aset_manajemen_barang/barang_titipan/update');
        $data['linkD'] = site_url('aset_manajemen_barang/barang_titipan/detail');
        $data['linkK'] = site_url('aset_manajemen_barang/barang_titipan/koordinat');
		
		//$data['dataUnitKerja'] = $this->model->get_unit_kerja()->result_array();
		$data['dataUnitKerja'] = $this->model->get_data_unit_kerja();
        
        if ($unit_kerja=='') {
            $data['kode_unit'] = $this->kode_unit;
        } else {
            $data['kode_unit'] = $unit_kerja;
        }

		if ($this->input->server('REQUEST_METHOD') == 'POST' || !empty($offset)) {
			$data['search']			= (!empty($this->input->post('search', TRUE)))?$this->input->post('search', TRUE):'';
			$data['status']		    = (!empty($this->input->post('status', TRUE)))?$this->input->post('status', TRUE):'';
            $data['unit_kerja']		= (!empty($this->input->post('unit_pj', TRUE)))?$this->input->post('unit_pj', TRUE):'';
			$data['offset']			= 0;
            
			// ==========================================================================
			$this->load->library('jquery_pagination');
			$config['base_url']         = site_url('aset_manajemen_barang/barang_titipan/view/');
			$config['per_page']         = $this->total_page;
			$config['base_filter']      = '/'.$data['search'].'/'.$data['status'].'/'.$data['unit_kerja'];
			$config['uri_segment']      = 4;
			$config['url_location']     = 'subcontent-element';
			$config['total_rows']       = $this->model->get_num_data($data['search'], $data['status'], $data['unit_kerja'])->jumlah;
			$this->jquery_pagination->initialize($config);
			$data['content']            = $this->model->get_data($config['per_page'], $offset, $data['search'], $data['status'], $data['unit_kerja'])->result_array();
			$data['pagination']         = $this->jquery_pagination->create_links();
			// ==========================================================================
		} else {
			$data['search']			= (!empty($search))?$search:'';
			$data['status']		    = (!empty($status))?$status:'';
			$data['unit_kerja']		= (!empty($unit_kerja))?$unit_kerja:'';
			$data['offset']			= $offset;
		}
		// ==========================================================================


        // ==========================================================================
		$this->template->load_view('view', $data);
	}
    
	public function add($gol='2'){
        $data['form_action'] = site_url() . "aset_manajemen_barang/barang_titipan/add_proses";
        
        $data['jenisBarang'] = $this->model->get_golongan_barang();
        
        $data['golongan'] = (!empty($this->input->post('jenis_barang', TRUE)))?$this->input->post('jenis_barang', TRUE):$gol;
            
        $data['kodeBarang'] = $this->model->get_kode_barang($data['golongan']);
        $data['sumberDana'] = $this->model->get_sumber_dana_barang_titipan();
        $data['satuanBarang'] = $this->model->get_satuan_barang();
        $data['kepemilikan'] = $this->model->get_kepemilikan();
        $data['lokasiKampus'] = $this->model->get_kampus();
        $data['statusBarang'] = $this->model->get_status_barang();
        $data['unitPJ'] = $this->model->get_unit_pj();
        
        $data['def_sumber_dana'] = 12;
        $data['def_kepemilikan'] = 3;
        $data['def_status_barang'] = 1;
        $data['def_unit_pj'] = $this->kode_unit;
        
        $data['dataUnit'] = $this->model->get_unit_by_kode($this->kode_unit);
        
        $this->template->load_view('add', $data);
	}
    
    public function add_proses($id=null, $error=null) {
        $aksi = $this->input->post('tombol_proses');
        $golongan = $this->input->post('jenis_barang');
        
        if ($aksi == 'batal') {
            redirect('aset_manajemen_barang/barang_titipan/view');
            
        } elseif ($aksi == 'pilih') {
            redirect('aset_manajemen_barang/barang_titipan/add/'.$golongan);
            
        } elseif ($aksi == 'simpan') {
            $mapping = $this->model->select_ugmfw_mapping($this->user_name);

            $kode_aset = $this->input->post('kode_barang');
            //$golongan = $this->input->post('jenis_barang');
            
            $kode_barang = $golongan.'.'.substr($kode_aset, 1, 2).'.'.substr($kode_aset, 3, 2).'.'.substr($kode_aset, 5, 2).'.'.substr($kode_aset, 7, 3);
            
            $master = array(
                'invBarangId' => $kode_aset,
                'invKodeAset' => $kode_barang,
                'invMstLabel' => $this->input->post('label_barang'),
                'invMerek' => $this->input->post('merk'),
                'invSpesifikasi' => $this->input->post('spesifikasi'),
                'invTglPembelian' => $this->input->post('tanggal_pembelian'),
                'invSumberDana' => $this->input->post('sumber_dana'),
                'invJumlahAset' => $this->input->post('jumlah_barang'),
                'invSatuanBarang' => $this->input->post('satuan'),
                'invLuasTanah' => $this->input->post('luas_tanah'),
                'invNilaiPerolehanSatuan' => $this->input->post('nilai_perolehan'),
                'invNilaiFaktur' => $this->input->post('nilai_kuitansi'),
                'invTotalPerolehan' => $this->input->post('nilai_kuitansi'),
                'invSPMNumber' => $this->input->post('nomor_referensi'),
                'invTglBuku' => $this->input->post('tanggal_pembukuan'),
                'invTglUbah' => date("Y-m-d H:i:s"),
                'invUserId' => $mapping->id
            );
            
            //$this->model->db->trans_begin();
            $mst = $this->model->insert_inv_titipan_mst($master);
            
            if ($mst) {
                //echo 'Berhasil'; exit;
                $max_mst_id = $this->model->select_max_invmstid();
                
                $jmlAset = $this->input->post('jumlah_barang');
                //echo $jmlAset; exit;
                
                $unit_kerja = $this->model->select_unit_kerja_id($this->kode_unit);
                
                for ($i = 0; $i < $jmlAset; $i++) {
                    $cek_nup = $this->model->select_nomor_barang_titipan($kode_aset, $unit_kerja->id);
                    
                    if (!empty($cek_nup)) {
                        $urut = $cek_nup->nup+1;
                    } else {
                        $urut = 1;
                    }
                    
                    $kode_barang = 'BT-'.$golongan.'.'.substr($kode_aset, 1, 2).'.'.substr($kode_aset, 3, 2).'.'.substr($kode_aset, 5, 2).'.'.substr($kode_aset, 7, 3).'.'.$urut;
                    
                    $detail = array(
                        'invDetGolId' => $golongan,
                        'invDetBidangbrngId' => substr($kode_aset, 0, 3),
                        'invDetKelbrgId' => substr($kode_aset, 0, 5),
                        'invDetSubkelbrgId' => substr($kode_aset, 0, 7),
                        'invDetBarangId' => $kode_aset,
                        'invDetMstId' => $max_mst_id->id,
                        'invDetKodeBarang' => $kode_barang,
                        'invDetMstLabel' => $this->input->post('label_barang'),
                        'invDetMerek' => $this->input->post('merk'),
                        'invDetSpesifikasi' => $this->input->post('spesifikasi'),
                        'invDetTglPembelian' => $this->input->post('tanggal_pembelian'),
                        'invDetNilaiResidu' => 1,  // 1 = Default
                        'invDetSumberDana' => $this->input->post('sumber_dana'),
                        'invDetSatuanBarang' => $this->input->post('satuan'),
                        'invDetNilaiPerolehanSatuan' => $this->input->post('nilai_perolehan'),
                        'invDetNJOP' => 1,  // 1 = Default
                        'invDetUmurEkonomis' => 1,  // 1 = Default
                        'invDetLokasi' => $this->input->post('lokasi'),
                        'invDetKegunaan' => $this->input->post('kegunaan'),
                        'invDetPagar' => 'Tidak',   // Tidak = Default
                        'invDetKepemilikan' => $this->input->post('kepemilikan'),
                        'invDetPenguasaanBarang' => $this->input->post('penguasaan'),
                        'invDetKeteranganLain' => $this->input->post('keterangan_lain'),
                        'invDetUnitKerja' => $this->input->post('unit_pj'),
                        'invDetKondisiId' => 1, // 1 = Baik (Default)
                        'invDetNamaFile' => '',
                        'invDetTglBuku' => $this->input->post('tanggal_pembukuan'),
                        'invDetTglUbah' => date("Y-m-d H:i:s"),
                        'invDetUserId' => $mapping->id,
                        'invDetAsetStatusId' => $this->input->post('status_barang'),
                        'invDetIdentitasBarang' => $this->input->post('kib')
                    );
                    
                    $det = $this->model->insert_inv_titipan_det($detail);
                }
            }
            
            $this->url = site_url("aset_manajemen_barang/barang_titipan/view");
            
            if ($det) {
                $result = array('notice', 'Data berhasil ditambah.', $this->url);
            } else {
                $result = array('error', 'Data gagal ditambah.', $this->url);
            }
            
            redirect('aset_manajemen_barang/barang_titipan/view');
        }   
    }
    
   	public function detail($id=null){
        $data['detailAset'] = $this->model->select_detail_barang_titipan($id);
        $data['linkView'] = site_url('aset_manajemen_barang/barang_titipan/view');
        
        $this->template->load_view('rinci', $data);
	}
    
   	public function update($id=null){
   	    $data['form_action'] = site_url() . "aset_manajemen_barang/barang_titipan/add_proses";
        $data['detailAset'] = $this->model->select_detail_barang_titipan($id);
        
        $data['sumberDana'] = $this->model->get_sumber_dana_barang_titipan();
        $data['kepemilikan'] = $this->model->get_kepemilikan();
        $data['dataUnit'] = $this->model->get_unit_by_kode($this->kode_unit);
        
        $this->template->load_view('update', $data);
	}
    
   	public function koordinat($id=null){
        $data['detailAset'] = $this->model->select_detail_barang_titipan($id);
        $data['linkView'] = site_url('aset_manajemen_barang/barang_titipan/view');
        
        $this->template->load_view('koordinat', $data);
	}
}