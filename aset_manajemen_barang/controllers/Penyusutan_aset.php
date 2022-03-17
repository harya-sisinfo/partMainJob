<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');

/**
 * Controller Penyusutan_aset
 * @created : 2021-08-4 10:05:00
 * @author  : Sriharyo <sriharyo@ugm.ac.id>
 * @company : DSSDI UGM
*/
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Dompdf\Dompdf;

class Penyusutan_aset extends UGM_Controller
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
		$this->load->model('Model_penyusutan_aset','model');

		$this->ret_url          = site_url('aset_manajemen_barang/penyusutan_aset/view');
		$this->date_today       = date("Y-m-d H:i:s");
		$this->total_page       = 20;

		$this->user_id          = $this->session->userdata('__user_id');
		$this->kode_unit        = $this->session->userdata('__objUser')->unit_kerja_kode;
		$this->user_level       = $this->session->userdata('__objUser')->user_level;
	}
	public function index()
	{
		redirect('aset_manajemen_barang/penyusutan_aset/view');
	}
	public function view()
	{
		$data['dataUnitKerja'] 	= $this->model->get_unit_kerja()->result_array();
		$data['dataJenisKIB'] 	= $this->model->get_jenis_kib()->result_array();
		
		$data['jenis_kib_'] 	= (!empty($this->input->get('jenis_kib_')))?$this->input->get('jenis_kib_'):'';
		$data['kode_aset_'] 	= (!empty($this->input->get('kode_aset_')))?$this->input->get('kode_aset_'):'';
		$data['unit_kerja_'] 	= (!empty($this->input->get('unit_kerja_')))?$this->input->get('unit_kerja_'):'';
		$data['lastState'] 		= (!empty($lastState))?$this->encryption_lib->urldecode($lastState):'';
		
		$data['linkAdd'] 		= site_url('aset_manajemen_barang/penyusutan_aset/add');
		$data['linkPrintPdf'] 	= site_url('aset_manajemen_barang/penyusutan_aset/cetak_pdf');
		$data['linkPrintExcel']	= site_url('aset_manajemen_barang/penyusutan_aset/cetak_excel');
		$data['linkView'] 		= site_url('aset_manajemen_barang/penyusutan_aset/view');
		$data['linkDataTable'] 	= site_url('aset_manajemen_barang/penyusutan_aset/view_dtable');
		$this->template->load_view("view", $data);
	}
	
	public function view_dtable()
	{
		$getData = $this->input->get();
		$columns =	[0 => '',
		1 => 'kode_aset',	
		2 => 'nama_aset',	
		3 => 'unitkerjaNama',	
		4 => 'mstPenystnNilaiPerolehan', 
		5 => 'nilai_penyusutan', 
		6 => 'total_penyusutan',
		7 => 'nilai_buku',
		8 => '' ];

		$search = $getData['search']['value'];
		if ($columns[$getData['order'][0]['column']] == 'kode_aset') {
			$order = $columns[$getData['order'][0]['column']] . " " . 'DESC';
		} else {
			$order = $columns[$getData['order'][0]['column']] . " " . $getData['order'][0]['dir'];
		}
		$params = [
			'jenis_kib'    => $getData['jenis_kib'],
			'kode_aset'    => $getData['kode_aset'],
			'unit_kerja'   => $getData['unit_kerja']	
		];
		$offset = $getData['start'];
		$limit = $getData['length'];

		$lastState = '&jenis_kib='.$getData['jenis_kib'].'&kode_aset='.$getData['kode_aset'].'&unit_kerja='.$getData['unit_kerja'];
		$lastState_ = $this->encryption_lib->urlencode($getData['jenis_kib'].'||'.$getData['kode_aset'].'||'.$getData['unit_kerja']);

		$totalRows = $this->model->get_num_data($params,$search)['jumlah'];
		$dataQuery = $this->model->get_data($limit, $offset, $search, $order,  $params);
		$data = [];

		if ($dataQuery) {
			foreach ($dataQuery as $idx => $row) {
				$encId 		= $this->encryption_lib->urlencode($row['id']);

				$button_log = '<a 
				href=""
				rel="async"
				ajaxify="'.modal('Log Penyusutan '.$row['kode_aset'],'aset_manajemen_barang','penyusutan_aset','log',0,$encId).'" 
				class="btn btn-info btn-sm tooltip-trigger" 
				data-toggle="tooltip"
				data-placement="top" 
				title="Log Penyusutan '.$row['kode_aset'].'">
				<span class="fa fa-list"></span>
				</a>';


				$data[] = [
					$idx + 1 + $offset
					, $row['kode_aset']
					, $row['nama_aset']
					, $row['unitkerjaNama']
					, number_format($row['mstPenystnNilaiPerolehan'],0,",",".")
					, number_format($row['nilai_penyusutan'],0,",",".")
					, number_format($row['total_penyusutan'],0,",",".")
					, number_format($row['nilai_buku'],0,",",".")
					, $button_log
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

	public function log($offset=0,$encId)
	{
		$id 		= $this->encryption_lib->urldecode($encId);
		$this->load->library('jquery_pagination');
		$config['base_url'] = site_url('aset_manajemen_barang/penyusutan_aset/log/');
		$config['per_page'] = 10;
		$config['url_location'] = 'modal-data-basic';
		$config['base_filter'] = '/'.$encId;
		$config['uri_segment'] = 4;
		$config['full_tag_open'] = '<ul class="pagination paging urlactive">';
		$config['total_rows'] = $this->model->num_log($encId)->num_rows();
		$this->jquery_pagination->initialize($config);
		$data['content']    = $this->model->get_log($config['per_page'],$offset,$id)->result_array();
		$data['halaman'] = $this->jquery_pagination->create_links();
		$data['offset'] = $offset;

		if($data['content'])
		{
			foreach ($data['content'] as $key_data => $row_data)
			{
				$data['kode_aset'] 		= $row_data['kode_aset'];
				$data['barangNama'] 	= $row_data['barangNama'];
				$data['label_aset'] 	= $row_data['label_aset'];
				$data['invMerek'] 		= $row_data['invMerek'];
				$data['invSpesifikasi'] = $row_data['invSpesifikasi'];
			}	
		}

		$this->load->view('log',$data);
	}

	public function add()
	{
		$getData = $this->input->get();

		$data['dataUnitKerja'] 		= $this->model->get_unit_kerja()->result_array();
		$data['dataJenisAset'] 		= $this->model->get_jenis_aset()->result_array();

		$lastState = 	'?jenis_kib_='.$getData['jenis_kib'].
		'&kode_aset_='.$getData['kode_aset'].
		'&unit_kerja_='.$getData['unit_kerja'];

		$data['url_action']	= site_url('aset_manajemen_barang/penyusutan_aset/add_proses').$lastState;
		$data['url_back']	= site_url('aset_manajemen_barang/penyusutan_aset/view').$lastState;
		$data['form_label'] = 'Tambah Data Penyusutan Aset';
		$data['isProses'] 	= 'add';

		$this->template->load_view('add', $data);
	}

	public function add_proses()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('periode_penyusutan', 'Periode Penyusutan ', 'trim|required|callback_cek_transaksi');
		$this->form_validation->set_rules('ba_penyusutan', 'BA Penyusutan ', 'trim|required');
		$this->form_validation->set_rules('penanggung_jawab', 'Penanggung Jawab ', 'trim|required');

		$this->form_validation->set_message('required', 'Isian %s Belum Dilengkapi.');

		if ($this->form_validation->run($this) == FALSE) {
			$getData = $this->input->get();
			$lastState = 	'?jenis_kib_='.$getData['jenis_kib_'].'&kode_aset_='.$getData['kode_aset_'].'&unit_kerja_='.$getData['unit_kerja_'];
			$data['url_action']		= site_url('aset_manajemen_barang/penyusutan_aset/add_proses/').$lastState;
			$data['dataUnitKerja'] 		= $this->model->get_unit_kerja()->result_array();
			$data['dataJenisAset'] 		= $this->model->get_jenis_aset()->result_array();
			$data['url_back']		= site_url('aset_manajemen_barang/penyusutan_aset/view').$lastState;
			$data['form_label'] 	= 'Tambah Data Penyusutan Aset';
			$data['isProses'] 		= 'add';
			$this->template->load_view('add', $data);
		}else{
			extract($this->input->post());
			$susutAwal = true;

			$tanggal_periode = ($tanggal_transaksi ? date('Y-m-d', strtotime(str_replace('/', '-', substr($tanggal_transaksi, 0, 10)))) : null);
			// $data_periode = explode('-', $periode_penyusutan);
			// $periode = $data_periode['1'].'-'.$data_periode['0'].'-01';
			// $tglSusut = date("Y-m-d",strtotime($periode));
			

			$asetBackDate = $this->model->get_aset_back_date($kib_id,$periode_penyusutan);

			// print_r($asetBackDate) ;
			echo $asetBackDate->lantaiId;exit();

		}

	}

	public function cek_transaksi()
	{
		$periode_penyusutan = $this->input->post('periode_penyusutan');
		$kib_id = $this->input->post('kib_id');
		$dataIsAllow = $this->model->check_diff_tgl_susut($kib_id,$periode_penyusutan)->row_array();
		$monDiff = (int)$dataIsAllow['monDiff'];
		if($monDiff > 1){
			$this->form_validation->set_message('cek_transaksi', 'Periode Susut Hanya Boleh 1 Bulan Setelah Tgl Susut Terakhir ('.$dataIsAllow['lastSusut'].')');
			return false;
		}elseif($monDiff <= 0){
			$this->form_validation->set_message('cek_transaksi', 'Periode Susut Tidak Boleh Kurang Dari Tgl Susut Terakhir ('.$dataIsAllow['lastSusut'].')');
			return false;
		}else{
			/*ada koneksi db yang belum didapatkan, dimana didalamnya ada tabel yang digunakan, seperti tabel transaksi, transaksi_file dll.*/
			return true;
		}
	}

	public function cetak_excel()
	{

		$getData = $this->input->get();
		$params = [
			'jenis_kib'    => $getData['jenis_kib'],
			'kode_aset'    => $getData['kode_aset'],
			'unit_kerja'   => $getData['unit_kerja']	
		];
		$content = $this->model->get_data_all($getData['search'],$params);

		$objPHPExcel = new PHPExcel();
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);

		$styleArray = [
			'borders' => [
				'allborders' => [
					'style' => PHPExcel_Style_Border::BORDER_THIN
				]
			]
		];
		$styleHeaderArray = [
			'font' => [
				'bold' => true
			],
			'alignment' => [
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			]
		];
		$styleSubHeaderArray = [
			'font' => [
				'bold' => true
			],
			'borders' => [
				'allborders' => [
					'style' => PHPExcel_Style_Border::BORDER_THIN
				]
			]
		];
		$styleCenterArray = [
			'alignment' => [
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			]
		];
		$objWorksheet->getStyle('E:G')->getNumberFormat()->setFormatCode('#,##');

		$objWorksheet->getColumnDimension('A')->setWidth('5');
		$objWorksheet->getColumnDimension('B')->setWidth('16');
		$objWorksheet->getColumnDimension('C')->setWidth('62');
		$objWorksheet->getColumnDimension('D')->setWidth('50');
		$objWorksheet->getColumnDimension('E')->setWidth('20');
		$objWorksheet->getColumnDimension('F')->setWidth('20');
		$objWorksheet->getColumnDimension('G')->setWidth('20');
		$objWorksheet->getColumnDimension('H')->setWidth('20');

		$objWorksheet->getStyle('A2:F2')->applyFromArray($styleHeaderArray);
		$objWorksheet->setCellValue('A2', 'NO');
		$objWorksheet->setCellValue('B2', 'KODE ASET');
		$objWorksheet->setCellValue('C2', 'NAMA ASET');
		$objWorksheet->setCellValue('D2', 'UNIT PJ BARANG');
		$objWorksheet->setCellValue('E2', 'NILAI PEROLEHAN (Rp)');
		$objWorksheet->setCellValue('F2', 'NILAI PENYUSUTAN (Rp)');
		$objWorksheet->setCellValue('G2', 'AKUMULASI PENYUSUTAN (Rp)');
		$objWorksheet->setCellValue('H2', 'Nilai Buku');

		$row = 3;
		if($content){
			foreach ($content as $key => $val) {
				$objWorksheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($styleArray);
				$objWorksheet->setCellValue('A' . $row, $row - 2);
				$objWorksheet->setCellValue('B' . $row, " ".$val['kode_aset']);
				$objWorksheet->setCellValue('C' . $row, " ".$val['nama_aset']);
				$objWorksheet->setCellValue('D' . $row, " ".$val['unitkerjaNama']);
				$objWorksheet->setCellValue('E' . $row, " ".number_format($val['mstPenystnNilaiPerolehan']));
				$objWorksheet->setCellValue('F' . $row, " ".number_format($val['nilai_penyusutan']));
				$objWorksheet->setCellValue('G' . $row, " ".number_format($val['total_penyusutan']));
				$objWorksheet->setCellValue('H' . $row, " ".number_format($val['nilai_buku']));
				$row++;
			}
		}

		$fileName = 'penyusutan_aset_' . date('Ymd_His');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $fileName . '.xls');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

		$objWriter->save('php://output');
	}
	public function cetak_pdf()
	{

		$getData = $this->input->get();
		$params = [
			'jenis_kib'    => $getData['jenis_kib'],
			'kode_aset'    => $getData['kode_aset'],
			'unit_kerja'   => $getData['unit_kerja']	
		];
		$this->load->helper('terbilang');
		$this->load->library('encryption_data');

		$getData = $this->input->get();

		$path = './ugmfw-assets/images/logo-ugm-hp.png';
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$dataImg = file_get_contents($path);
		$data['logo'] = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);


		$data['header'] = "Daftar Penyusutan Aset";
		$html    = $this->load->view('cetak_pdf', $data, true);

		$data['content'] = $this->model->get_data_all($getData['search'],$params);

		$html = $this->load->view('aset_manajemen_barang/penyusutan_aset/cetak_pdf', $data,true);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream("penyusutan_aset",array("Attachment"=>0));
	}
}