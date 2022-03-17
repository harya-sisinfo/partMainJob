<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');

/**
 * Controller Inventarisasi Detail
 * @created : August 12, 2021 02:51 AM
 * @updated : January 20, 2022 13:58 PM
 * @author  : Alfian Hidayat <hidayat_alfian@ugm.ac.id>
 * @company : DSSDI UGM
**/

class Inventarisasi_detail extends UGM_Controller {
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
        
		$this->load->model('Model_inventarisasi_detail', 'model');

		$this->ret_url          = site_url('aset_manajemen_barang/inventarisasi_detail/view');
		$this->date_today       = date("Y-m-d H:i:s");
		$this->total_page       = 40;
        
		// $this->u = '/media/data/aset/inventarisasi_detail';
		$this->u = $this->config->item('upload_url') . 'inventarisasi_detail';

		$this->user_id          = $this->session->userdata('__user_id');
        $this->user_name        = $this->session->userdata('__username');
		$this->kode_unit        = $this->session->userdata('__objUser')->unit_kerja_kode;
		$this->user_level       = $this->session->userdata('__objUser')->user_level;
	}
    
	public function index() {
		redirect('aset_manajemen_barang/inventarisasi_detail/view');
	}
    
	public function view($offset='', $unit_kerja='', $tampilkan='', $search=''){
		$data['linkView'] = site_url('aset_manajemen_barang/inventarisasi_detail/view');
		$data['linkMP']   = site_url('aset_manajemen_barang/inventarisasi_detail/mutasi_pembelian');
        $data['linkMH']   = site_url('aset_manajemen_barang/inventarisasi_detail/mutasi_hibah');
        $data['linkUP']   = site_url('aset_manajemen_barang/inventarisasi_detail/update');
        $data['linkDA']   = site_url('aset_manajemen_barang/inventarisasi_detail/detail');
        $data['linkDT']   = site_url('aset_manajemen_barang/inventarisasi_detail/transaksi');
        $data['linkKA']   = site_url('aset_manajemen_barang/inventarisasi_detail/koordinat');
        
        $data['dataKelCOA'] = $this->model->get_kelompok_coa();
        $data['dataSumberDana'] = $this->model->get_sumber_dana_pembelian();
        $data['dataKepemilikan'] = $this->model->get_kepemilikan();
        $data['dataKondisi'] = $this->model->get_kondisi();
        $data['dataStatusAset'] = $this->model->get_status_aset();
        
        if ($unit_kerja=='') {
            $data['kode_unit'] = $this->kode_unit;
        } else {
            $data['kode_unit'] = $unit_kerja;
        }
        
        $unit_kerja_sistem = $this->model->get_unit_kerja_kode_sistem($data['kode_unit']);
        //print_r($unit_kerja_sistem); exit;
		$data['dataUnitKerja'] = $this->model->get_data_unit_kerja($unit_kerja_sistem->kode);

		if ($this->input->server('REQUEST_METHOD') == 'POST' || !empty($offset)) {
			$data['search']			   = (!empty($this->input->post('search', TRUE)))?$this->input->post('search', TRUE):'';
            $data['kel_coa']           = (!empty($this->input->post('kel_coa', TRUE)))?$this->input->post('kel_coa', TRUE):'';
            $data['tahun_pengadaan']   = (!empty($this->input->post('tahun_pengadaan', TRUE)))?$this->input->post('tahun_pengadaan', TRUE):'';
            $data['tahun_pembukuan']   = (!empty($this->input->post('tahun_pembukuan', TRUE)))?$this->input->post('tahun_pembukuan', TRUE):'';
            $data['sumber_dana']       = (!empty($this->input->post('sumber_dana', TRUE)))?$this->input->post('sumber_dana', TRUE):'';
            $data['kepemilikan']       = (!empty($this->input->post('kepemilikan', TRUE)))?$this->input->post('kepemilikan', TRUE):'';
            $data['kondisi']           = (!empty($this->input->post('kondisi', TRUE)))?$this->input->post('kondisi', TRUE):'';
			$data['status']		       = (!empty($this->input->post('status', TRUE)))?$this->input->post('status', TRUE):'';
			$data['unit_kerja']		   = (!empty($this->input->post('unit_pj', TRUE)))?$this->input->post('unit_pj', TRUE):'';
			$data['offset']			   = 0;
            
            if (!empty($offset)) {
                $offset = $offset;
            } else {
                $offset = 0;
            }
            
            $data['tampilkan']		   = (!empty($this->input->post('tampilkan', TRUE)))?$this->input->post('tampilkan', TRUE):'';
            
            if ($data['tampilkan']==1) {
                $unitKerjaSistem = $this->model->get_unit_kerja_kode_sistem($data['unit_kerja']);
                //print_r($unitKerjaSistem); exit;
                $data['unit_kerja_sistem'] = $unitKerjaSistem->kode;
                             
            } else {
                $data['unit_kerja_sistem'] = "";
            }
            
            $data['unit_kerja_pilih'] = $this->model->get_unit_kerja_by_kode($data['unit_kerja']);
            
            // SESI ---------------------------------------------------------------------------- //
            $_SESSION['search']             = $data['search'];
            $_SESSION['kel_coa']            = $data['kel_coa'];
            $_SESSION['tahun_pengadaan']    = $data['tahun_pengadaan'];
            $_SESSION['tahun_pembukuan']    = $data['tahun_pembukuan'];
            $_SESSION['sumber_dana']        = $data['sumber_dana'];
            $_SESSION['kepemilikan']        = $data['kepemilikan'];
            $_SESSION['kondisi']            = $data['kondisi'];
            $_SESSION['status']             = $data['status'];
            $_SESSION['unit_kerja']         = $data['unit_kerja'];
            $_SESSION['tampilkan']          = $data['tampilkan'];
            $_SESSION['unit_kerja_sistem']  = $data['unit_kerja_sistem'];
            
			// ====================================================================================
			$this->load->library('jquery_pagination');
			$config['base_url']        = site_url('aset_manajemen_barang/inventarisasi_detail/view/');
			$config['per_page']        = $this->total_page;
			$config['base_filter']     = '/'.$data['unit_kerja'].'/'.$data['tampilkan'].'/'.$data['search'];
			$config['uri_segment']     = 4;
			$config['url_location']    = 'subcontent-element';
			$config['total_rows']      = $this->model->get_num_data($data['search'], $data['kel_coa'], $data['tahun_pengadaan'], $data['tahun_pembukuan'], $data['sumber_dana'], $data['kepemilikan'], $data['kondisi'], $data['status'], $data['unit_kerja'], $data['unit_kerja_sistem'])->jumlah;
			
            $this->jquery_pagination->initialize($config);
			
            $data['content']           = $this->model->get_data($config['per_page'], $offset, $data['search'], $data['kel_coa'], $data['tahun_pengadaan'], $data['tahun_pembukuan'], $data['sumber_dana'], $data['kepemilikan'], $data['kondisi'], $data['status'], $data['unit_kerja'], $data['unit_kerja_sistem']);
			$data['pagination']        = $this->jquery_pagination->create_links();
			// ==========================================================================
		} else {
			$data['search']			   = (!empty($_SESSION['search']))?$_SESSION['search']:'';
            $data['kel_coa']           = (!empty($_SESSION['kel_coa']))?$_SESSION['kel_coa']:'';
            $data['tahun_pengadaan']   = (!empty($_SESSION['tahun_pengadaan']))?$_SESSION['tahun_pengadaan']:'';
            $data['tahun_pembukuan']   = (!empty($_SESSION['tahun_pembukuan']))?$_SESSION['tahun_pembukuan']:'';
            $data['sumber_dana']       = (!empty($_SESSION['sumber_dana']))?$_SESSION['sumber_dana']:'';
            $data['kepemilikan']       = (!empty($_SESSION['kepemilikan']))?$_SESSION['kepemilikan']:'';
            $data['kondisi']           = (!empty($_SESSION['kondisi']))?$_SESSION['kondisi']:'';
			$data['status']		       = (!empty($_SESSION['status']))?$_SESSION['status']:'1';
			$data['unit_kerja']		   = (!empty($_SESSION['unit_kerja']))?$_SESSION['unit_kerja']:'';
            $data['tampilkan']         = (!empty($_SESSION['tampilkan']))?$_SESSION['tampilkan']:'1';
            $data['unit_kerja_sistem'] = (!empty($_SESSION['unit_kerja_sistem']))?$_SESSION['unit_kerja_sistem']:'';
			$data['offset']			   = $offset;
			$data['offset']			   = 0;
            
            if (!empty($offset)) {
                $offset = $offset;
            } else {
                $offset = 0;
            }
            
			// ====================================================================================
			$this->load->library('jquery_pagination');
			$config['base_url']        = site_url('aset_manajemen_barang/inventarisasi_detail/view/');
			$config['per_page']        = $this->total_page;
			$config['base_filter']     = '/'.$data['unit_kerja'].'/'.$data['tampilkan'].'/'.$data['search'];
			$config['uri_segment']     = 4;
			$config['url_location']    = 'subcontent-element';
			$config['total_rows']      = $this->model->get_num_data($data['search'], $data['kel_coa'], $data['tahun_pengadaan'], $data['tahun_pembukuan'], $data['sumber_dana'], $data['kepemilikan'], $data['kondisi'], $data['status'], $data['unit_kerja'], $data['unit_kerja_sistem'])->jumlah;
			
            $this->jquery_pagination->initialize($config);
			
            $data['content']           = $this->model->get_data($config['per_page'], $offset, $data['search'], $data['kel_coa'], $data['tahun_pengadaan'], $data['tahun_pembukuan'], $data['sumber_dana'], $data['kepemilikan'], $data['kondisi'], $data['status'], $data['unit_kerja'], $data['unit_kerja_sistem']);
			$data['pagination']        = $this->jquery_pagination->create_links();
		}
		// ==========================================================================

        // ==========================================================================
		$this->template->load_view('view', $data);
	}

   	public function mutasi_pembelian($gol='2'){
   	    $data['form_action'] = site_url() . "aset_manajemen_barang/inventarisasi_detail/add_proses";
        $data['jenisAset'] = $this->model->get_golongan_barang();
        $data['golongan'] = (!empty($this->input->post('jenis_aset', TRUE)))?$this->input->post('jenis_aset', TRUE):$gol;
            
        //$data['kodeAset'] = $this->model->get_kode_aset($data['golongan']);
        $data['sumberDana'] = $this->model->get_sumber_dana_pembelian();
        $data['satuanBarang'] = $this->model->get_satuan_barang();
        $data['kepemilikan'] = $this->model->get_kepemilikan();
        $data['lokasiKampus'] = $this->model->get_kampus();
        //$data['lokasiAset'] = $this->model->get_lokasi_aset();
        $data['statusAset'] = $this->model->get_status_aset();
        $data['unitPJ'] = $this->model->get_unit_pj();
        
        $data['def_sumber_dana'] = 12;
        $data['def_kepemilikan'] = 1;
        $data['def_status_aset'] = 1;
        $data['def_unit_pj'] = $this->kode_unit;
        
        $data['dataUnit'] = $this->model->get_unit_by_kode($this->kode_unit);
        
        $_SESSION['golongan'] = $data['golongan'];
        
        $this->template->load_view('tambah_mutasi_pembelian', $data);
	}
    
   	public function mutasi_hibah($gol='2'){
   	    $data['form_action'] = site_url() . "aset_manajemen_barang/inventarisasi_detail/add_proses";
        $data['jenisAset'] = $this->model->get_golongan_barang();
        $data['golongan'] = (!empty($this->input->post('jenis_aset', TRUE)))?$this->input->post('jenis_aset', TRUE):$gol;
        
        //$data['kodeAset'] = $this->model->get_kode_aset($data['golongan']);
        $data['sumberDana'] = $this->model->get_sumber_dana_hibah();
        $data['satuanBarang'] = $this->model->get_satuan_barang();
        $data['kepemilikan'] = $this->model->get_kepemilikan();
        $data['lokasiKampus'] = $this->model->get_kampus();
        //$data['lokasiAset'] = $this->model->get_lokasi_aset();
        $data['statusAset'] = $this->model->get_status_aset();
        $data['unitPJ'] = $this->model->get_unit_pj();
        
        $data['def_sumber_dana'] = 8;
        $data['def_kepemilikan'] = 1;
        $data['def_status_aset'] = 1;
        $data['def_unit_pj'] = $this->kode_unit;
        
        $data['dataUnit'] = $this->model->get_unit_by_kode($this->kode_unit);
        
        $_SESSION['golongan'] = $data['golongan'];
        
        $this->template->load_view('tambah_mutasi_hibah', $data);
	}
    
   	public function list_kode_aset($offset=0, $search='') {
		$search   = (!empty($search))?$search:(!empty($this->input->post('search')))?$this->input->post('search'):'';
        $golongan = $_SESSION['golongan'];
		
		$this->load->library('jquery_pagination');
        
		$config['base_url']       = site_url('aset_manajemen_barang/inventarisasi_detail/list_kode_aset/');
		$config['per_page']       = 10;
		$config['url_location']   = 'modal-data-basic';
		$config['base_filter']    = '/'.(!empty($search))?$search:'';
		$config['uri_segment']    = 4;
		$config['full_tag_open']  = '<ul class="pagination paging urlactive">';
		$config['total_rows']     = $this->model->num_kode_aset($golongan, $search)->jumlah;
        
		$this->jquery_pagination->initialize($config);
        
		$data['content']          = $this->model->get_kode_aset($golongan, $config['per_page'], $offset, $search);
		$data['halaman']          = $this->jquery_pagination->create_links();
		$data['offset']           = $offset;

		$data['linkForm']         = site_url('aset_manajemen_barang/inventarisasi_detail/list_kode_aset');
        
		$this->load->view('modal_kode_aset', $data);
	}
    
   	public function list_lokasi_aset($offset=0, $search='') {
		$search = (!empty($search))?$search:(!empty($this->input->post('search')))?$this->input->post('search'):'';
		
		$this->load->library('jquery_pagination');
        
		$config['base_url']       = site_url('aset_manajemen_barang/inventarisasi_detail/list_lokasi_aset/');
		$config['per_page']       = 10;
		$config['url_location']   = 'modal-data-basic';
		$config['base_filter']    = '/'.(!empty($search))?$search:'';
		$config['uri_segment']    = 4;
		$config['full_tag_open']  = '<ul class="pagination paging urlactive">';
		$config['total_rows']     = $this->model->num_lokasi_aset($search)->jumlah;
        
		$this->jquery_pagination->initialize($config);
        
		$data['content']          = $this->model->get_lokasi_aset($config['per_page'], $offset, $search);
		$data['halaman']          = $this->jquery_pagination->create_links();
		$data['offset']           = $offset;

		$data['linkForm']         = site_url('aset_manajemen_barang/inventarisasi_detail/list_lokasi_aset');
        
		$this->load->view('modal_lokasi_aset',$data);
	}
    
    public function add_proses($id=null, $error=null) {
        $aksi = $this->input->post('tombol_proses');
        $golongan = $this->input->post('jenis_aset');
        $form = $this->input->post('form');
        
        if ($aksi == 'batal') {
            redirect('aset_manajemen_barang/inventarisasi_detail/view');
            
        } elseif ($aksi == 'pilih') {
            //echo $form; exit;
            
            if ($form=='pembelian') {
                redirect('aset_manajemen_barang/inventarisasi_detail/mutasi_pembelian/'.$golongan);
                
            } elseif ($form=='hibah') {
                redirect('aset_manajemen_barang/inventarisasi_detail/mutasi_hibah/'.$golongan);
            }
            
        } elseif ($aksi == 'simpan') {
            $mapping = $this->model->select_ugmfw_mapping($this->user_name);

            $kode_aset = $this->input->post('kode_aset');
            //$golongan = $this->input->post('jenis_aset');
            
            $kode_barang = $golongan.'.'.substr($kode_aset, 1, 2).'.'.substr($kode_aset, 3, 2).'.'.substr($kode_aset, 5, 2).'.'.substr($kode_aset, 7, 3);
            
            $master = array(
                'invBarangId' => $kode_aset,
                'invKodeAset' => $kode_barang,
                'invMstLabel' => $this->input->post('label_aset'),
                'invMerek' => $this->input->post('merk'),
                'invSpesifikasi' => $this->input->post('spesifikasi'),
                'invTglPembelian' => $this->input->post('tanggal_pembelian'),
                'invSumberDana' => $this->input->post('sumber_dana'),
                'invJumlahAset' => $this->input->post('jumlah_aset'),
                'invSatuanBarang' => $this->input->post('satuan'),
                'invLuasTanah' => $this->input->post('luas_tanah'),
                'invNilaiPerolehanSatuan' => $this->input->post('nilai_perolehan'),
                'invNilaiFaktur' => $this->input->post('nilai_kuitansi'),
                'invTotalPerolehan' => $this->input->post('nilai_perolehan'),
                'invSPMNumber' => $this->input->post('nomor_referensi'),
                'invTglBuku' => $this->input->post('tanggal_pembukuan'),
                'invTglUbah' => date("Y-m-d H:i:s"),
                'invUserId' => $mapping->id
            );
            
            $this->model->db->trans_begin();
            $mst = $this->model->insert_inventarisasi_mst($master);
            
            if ($mst) {
                //echo 'Berhasil'; exit;
                $max_mst_id = $this->model->select_max_invmstid();
                
                $jmlAset = $this->input->post('jumlah_aset');
                //echo $jmlAset; exit;
                
                $unit_kerja = $this->model->select_unit_kerja_id($this->kode_unit);
                
                for ($i = 0; $i < $jmlAset; $i++) {
                    $cek_nup = $this->model->select_nomor_barang($kode_aset, $unit_kerja->id);
                    
                    if (!empty($cek_nup)) {
                        $urut = $cek_nup->nup+1;
                    } else {
                        $urut = 1;
                    }
                    
                    // SATUAN --------------------------------- //
                    if (!empty($this->input->post('satuan'))) {
                        $satuan = $this->input->post('satuan');
                        
                    } else {
                        $satuan = '';
                    }
                    
                    // FOTO ----------------------------------- //
                    if ($_FILES["foto"]["tmp_name"][0]) {
                    	$upload = $this->do_upload_file();
                    	$nama_file = $upload['file_name'];
                        
                    } else {
                    	$upload['status'] = true; 
                    	$upload['file_name'] = '';
                    }
                    
                    if ($upload['status'] === true) {
                    	$nama_file = $upload['file_name'];
                        
                    } else {
                    	$nama_file = $_FILES["foto"]["tmp_name"][0];			
                    }
                    
                    $kode_barang = $golongan.'.'.substr($kode_aset, 1, 2).'.'.substr($kode_aset, 3, 2).'.'.substr($kode_aset, 5, 2).'.'.substr($kode_aset, 7, 3).'.'.$urut;
                    
                    $detail = array(
                        'invDetGolId' => $golongan,
                        'invDetBidangbrngId' => substr($kode_aset, 0, 3),
                        'invDetKelbrgId' => substr($kode_aset, 0, 5),
                        'invDetSubkelbrgId' => substr($kode_aset, 0, 7),
                        'invDetBarangId' => $kode_aset,
                        'invDetNup' => $urut,
                        'invDetMstId' => $max_mst_id->id,
                        'invDetKodeBarang' => $kode_barang,
                        'invDetMstLabel' => $this->input->post('label_aset'),
                        'invDetMerek' => $this->input->post('merk'),
                        'invDetSpesifikasi' => $this->input->post('spesifikasi'),
                        'invDetTglPembelian' => $this->input->post('tanggal_pembelian'),
                        'invDetNilaiResidu' => $this->input->post('nilai_perolehan'),
                        'invDetSumberDana' => $this->input->post('sumber_dana'),
                        'invDetSatuanBarang' => $satuan,
                        'invDetNilaiPerolehanSatuan' => $this->input->post('nilai_perolehan'),
                        'invDetNJOP' => 1,  // 1 = Default
                        'invDetUmurEkonomis' => 1,  // 1 = Default
                        'invDetLokasi' => $this->input->post('lokasi'),
                        'invDetKegunaan' => $this->input->post('kegunaan'),
                        'invDetPagar' => 'Tidak',   // Tidak = Default
                        'invDetKepemilikan' => $this->input->post('kepemilikan'),
                        'invDetPenguasaanBarang' => $this->input->post('penguasaan'),
                        'invDetKeteranganLain' => $this->input->post('keterangan_lain'),
                        
                        'invDetKampus' => $this->input->post('lokasi_kampus'),
                        'invDetGedungId' => $this->input->post('gedung'),
                        'invDetRuanganId' => $this->input->post('ruang'),
                        
                        'invDetUnitKerja' => $this->input->post('unit_pj'),
                        'invDetKondisiId' => 1, // 1 = Baik (Default)
                        'invDetNamaFile' => $nama_file,
                        'invDetTglBuku' => $this->input->post('tanggal_pembukuan'),
                        'invDetTglUbah' => date("Y-m-d H:i:s"),
                        'invDetUserId' => $mapping->id,
                        'invDetAsetStatusId' => $this->input->post('status_aset'),
                        'invDetIdentitasBarang' => $this->input->post('kib')
                    );
                    
                    $det = $this->model->insert_inventarisasi_detail($detail);
                    
                    $max_detail_id = $this->model->select_max_invdetid();
                    
                    if ($det) {
                        $penyusutan = array(
                            'mstPenystnBarangId' => $max_detail_id->id,
                            'mstPenystnNilaiPerolehan' => $this->input->post('nilai_perolehan'),
                            'mstPenystnNilaiResidu' => 1,  // 1 = Default
                            'mstPenystnUmrEko' => 1,  // 1 = Default
                            'mstPenystnDisusutkan' => $this->input->post('nilai_perolehan'),
                            'mstPenystnNilaiPenyusutan' => 0,  // 0 = Default
                            'mstPenystnNilaiTotalPenyusutan' => 0,  // 0 = Default
                            'mstPenystnTglPerubahan' => date("Y-m-d H:i:s"),
                            'mstPenystnTglBukuPerolehan' => $this->input->post('tanggal_pembukuan')
                        );
                        
                        $exec_penyusutan = $this->model->insert_penyusutan_brg_mst($penyusutan);
                        
                        $transaksi = array(
                            'transInvDetId' => $max_detail_id->id,
                            'transJnsTrn' => 101,  // 101 = Default
                            'transNilai' => $this->input->post('nilai_perolehan'),
                            'transTglBuku' => $this->input->post('tanggal_pembukuan'),
                            'transKeterangan' => 'REINV',
                            'transOptId' => '',
                            'transKodeBrg' => $kode_aset.$urut,
                            'transUnitId' => $this->input->post('id_unit'),
                            'transUserId' => $mapping->id,
                            'transTimestamp' => date("Y-m-d H:i:s")
                        );
                        
                        $exec = $this->model->insert_inv_det_trans($transaksi);
                    }
                }
                
                if ($this->model->db->trans_status() === FALSE) {
                    $this->model->db->trans_rollback();
                    $this->ret_css_status = 'danger';
                    $this->ret_message = 'Data gagal ditambah';
                } else {
                    $this->model->db->trans_commit();
                    $this->ret_css_status = 'success';
                    $this->ret_message = 'Data berhasil ditambah';
                }
                
            } else {
                $this->model->db->trans_rollback();
                $this->ret_css_status = 'danger';
                $this->ret_message = 'Data gagal ditambah';
            }
            
            //$this->ret_url = site_url("aset_manajemen_barang/inventarisasi_detail/view");
            //echo json_encode(array('status' => $this->ret_css_status, 'msg' => $this->ret_message, 'url' => $this->ret_url));
            //exit();
            
//            $this->url = site_url("aset_manajemen_barang/inventarisasi_detail/view");
//            
//            if ($exec) {
//                $result = array('notice', 'Data berhasil ditambah.', $this->url);
//            } else {
//                $result = array('error', 'Data gagal ditambah.', $this->url);
//            }
//            
            redirect('aset_manajemen_barang/inventarisasi_detail/view');
        }   
    }
    
   	// Fungsi untuk upload ------------------------------------------------------------------------------ //
	private function do_upload_file() {
	   
		$this->load->library('upload');
        $dUpload = realpath(FCPATH . DIRECTORY_SEPARATOR . $this->u);
        $listFile = [];
        
        foreach ($_FILES["foto"]['name'] as &$name) {
        	$arrFile = explode(".", $name);
        	$ext = array_pop($arrFile);
        	$name = date('Ymd_His') . '_' . str_replace(" ", "_", implode("_", $arrFile)) . "." . $ext;
        	$listFile[] = $name;
        }

        $this->upload->initialize([
        	"allowed_types" => "jpg|jpeg|pdf",
        	"upload_path" => $dUpload,
        	'file_ext_tolower' => TRUE,
        	'overwrite' => FALSE,
        	'max_size' => 10240,
        	'remove_spaces' => TRUE
        ]);

        //Perform upload.
        if ($this->upload->do_upload('foto')) {
        	$ret = [
        		'status' => true,
        		'file_name' => implode('|', $listFile)
        	];
            
        } else {
        	$ret = [
        		'status' => false,
        		'msg' => $this->upload->display_errors()
        	];
        }
        return $ret;
    }
    
   	public function detail($id=null){
        $data['detailAset'] = $this->model->select_detail_aset($id);
        $data['linkView'] = site_url('aset_manajemen_barang/inventarisasi_detail/view');
        
        $this->template->load_view('rinci_aset', $data);
	}
    
   	public function transaksi($id=null, $unit=null){
        $data['detailTransaksi'] = $this->model->select_detail_transaksi($id, $unit);
        $data['linkView'] = site_url('aset_manajemen_barang/inventarisasi_detail/view');
        
        $data['kode_barang'] = $id;
        $data['kode_unit'] = $unit;
        
        $this->template->load_view('rinci_transaksi', $data);
	}
    
   	public function update($id=null){
   	    $data['form_action'] = site_url() . "aset_manajemen_barang/inventarisasi_detail/add_proses";
        $data['detailAset'] = $this->model->select_detail_aset($id);
        
        $data['sumberDana'] = $this->model->get_sumber_dana_pembelian();
        $data['kepemilikan'] = $this->model->get_kepemilikan();
        $data['dataUnit'] = $this->model->get_unit_by_kode($this->kode_unit);
        
        $this->template->load_view('update', $data);
	}
    
   	public function koordinat($id=null){
        $data['detailAset'] = $this->model->select_detail_aset($id);
        $data['linkView'] = site_url('aset_manajemen_barang/inventarisasi_detail/view');
        
        $this->template->load_view('koordinat_aset', $data);
	}
}