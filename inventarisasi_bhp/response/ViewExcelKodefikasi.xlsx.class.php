<?php
ini_set('memory_limit','1024M');
set_time_limit(0);

require_once GTFWConfiguration::GetValue('application', 'docroot').'module/'.Dispatcher::Instance()->mModule.'/business/InventarisasiBhp.class.php';

class ViewExcelKodefikasi extends XlsxResponse
{
   public $Excel;
   
   public function ProcessRequest(){

      $Obj = new InventarisasiBhp();
      $data = $Obj->ExportExcel();

      // set file name
      $filename = 'daftar_kode_barang_sediaan_'.date('dmY').'.xlsx';
      $this->SetFileName($filename);
      $this->SetWriter('Excel2007');
      
      // set sheet
      $sheet = $this->Excel->getActiveSheet(0);

      // set metadata properties data excel
      $metadata = $this->Excel->getProperties();
      
      // set default font setting for document
      $sheet->getDefaultStyle()->getFont()->setName('Arial')->setSize('10');
      
      // setting paging
      $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
      $sheet->getPageSetup()->setFitToWidth(1);
      $sheet->getPageSetup()->setFitToHeight(0);
      $sheet->getPageSetup()->setHorizontalCentered(true);
      $sheet->getPageSetup()->setVerticalCentered(false);
      
      $metadata->setCreator($user);
      $metadata->setLastModifiedBy($user);
      $metadata->setTitle("DAFTAR KODE BARANG SEDIAAN");
      $metadata->setSubject("daft_kd_brg_sedia");
      $metadata->setDescription("Daftar Kode Barang Sediaan printed on ".date('Y-m-d H:i:s', time()));
      $metadata->setKeywords("daft_kd_brg_sedia");
      $metadata->setCategory("gtAset");
      
      // set worksheet name
      $sheet->setTitle('daftar_kode_barang_sediaan');
      $sheet->setShowGridlines(false);
      
      // generate value
      if(count($data) == 0){
         $sheet->setCellValue('A1','Data Tidak Ditemukan');
      }else{
         
         // set coloum
         $sheet->getColumnDimension('A')->setWidth('22');
         $sheet->getColumnDimension('B')->setWidth('100');
         $sheet->getColumnDimension('C')->setWidth('50');
         $sheet->getColumnDimension('D')->setWidth('30');
         
         // set table header
         $headerStyledArray = array(
         	'font' => array(
               'bold' => true, 
               'size' => '12'
            ), 
            'alignment' => array(
               'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 
               'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
         );
         
         $subHeaderStyledArray   = array(
            'font' => array(
               'bold' => true
            ), 
            'alignment' => array(
               'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 
               'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
         );
			
			$subHeaderStyled2Array   = array(
            'borders' => array(
               'bottom' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN, 
                  'color' => array('argb' => 'ff000000')
               )
            ),
            'font' => array(
               'bold' => true
            ), 
            'alignment' => array(
               'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 
               'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
         );

         $subHeaderStyled3Array   = array(
            'borders' => array(
               'top' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN, 
                  'color' => array('argb' => 'ff000000')
               )
            ),
            'font' => array(
               'bold' => true
            ), 
            'alignment' => array(
               'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 
               'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
         );
            
         $styledTableHeaderArray = array(
            'borders' => array(
               'top' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN, 
                  'color' => array('argb' => 'ff000000')
               ),
               'bottom' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN, 
                  'color' => array('argb' => 'ff000000')
               )
            ),
            'font' => array(
               'bold' => true,
               'size' => '11'
            ), 
            'alignment' => array(
               'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 
               'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
					'wrap' => true
            )
         );
         
         $tableCellStyledArray   = array(
            'borders' => array(
               'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN, 
                  'color' => array('argb' => 'ff000000')
               )
            ),
				'alignment' => array(
               'wrap' => true
            )
         );
         
         $tableCellAlignCenterStyledArr   = array(
            'alignment' => array(
               'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )
         );
			
			// header
			$rowOneTitle = "DAFTAR KODE BARANG SEDIAAN"; 
			$rowTwoTitle = GTFWConfiguration::GetValue('organization','company_name');
         
			$sheet->SetCellValue('A1', $rowOneTitle);
			$sheet->mergeCells('A1:B1');
         $sheet->SetCellValue('A2', $rowTwoTitle);
			$sheet->mergeCells('A2:B2');
         $sheet->getStyle('A1:B2')->applyFromArray($subHeaderStyledArray);
         
         // set Header Data Table
			$sheet->SetCellValue('A4','KODE');
			$sheet->SetCellValue('B4','URAIAN');
         $sheet->SetCellValue('C4','AKUN COA');
         $sheet->SetCellValue('D4','STATUS');
			$sheet->getStyle('A4:D4')->applyFromArray($styledTableHeaderArray);
			
			$rows = 5; $gol = $bid = $kel = $skel = $brg = '';
         for ($i=0;$i<count($data);$i++){
            if($gol !== $data[$i]['gol']){
               $gol = $data[$i]['gol'];
               $sheet->SetCellValue('A'.$rows, $data[$i]['kode'][0]);
               $sheet->SetCellValue('B'.$rows, $gol);
               $sheet->getStyle('A'.$rows.':B'.$rows)->applyFromArray($subHeaderStyled3Array);
               $rows++;
            }
            if($bid !== $data[$i]['bid']){
               $bid = $data[$i]['bid'];
               $sheet->SetCellValue('A'.$rows, substr($data[$i]['kode'],0,4));
               $sheet->SetCellValue('B'.$rows, $bid);
               $sheet->getStyle('A'.$rows.':B'.$rows)->applyFromArray($subHeaderStyledArray);
               $rows++;
            }
            if($kel !== $data[$i]['kel']){
               $kel = $data[$i]['kel'];
               $sheet->SetCellValue('A'.$rows, substr($data[$i]['kode'],0,7));
               $sheet->SetCellValue('B'.$rows, $kel);
               $sheet->getStyle('A'.$rows.':B'.$rows)->applyFromArray($subHeaderStyledArray);
               $rows++;
            }
            if($skel !== $data[$i]['skel']){
               $skel = $data[$i]['skel'];
               $sheet->SetCellValue('A'.$rows, substr($data[$i]['kode'],0,10));
               $sheet->SetCellValue('B'.$rows, strtoupper($skel));
               $sheet->getStyle('A'.$rows.':B'.$rows)->applyFromArray($subHeaderStyledArray);
               $rows++;
            }
            if($brg !== $data[$i]['brg']){
               $brg = $data[$i]['brg'];
               $sheet->SetCellValue('A'.$rows, $data[$i]['kode']);
               $sheet->SetCellValue('B'.$rows, strtoupper($brg));
               $sheet->getStyle('A'.$rows.':D'.$rows)->applyFromArray($subHeaderStyled2Array);
               $rows++;
            }
            $sheet->SetCellValue('A'.$rows, $data[$i]['kode'].'.'.$data[$i]['kodeBhp']);
            $sheet->SetCellValue('B'.$rows, $data[$i]['atk']);
            $sheet->SetCellValue('C'.$rows, $data[$i]['coa']);
            $sheet->SetCellValue('D'.$rows, $data[$i]['stat']);
            $rows++;
			}
			
         //$sheet->getStyle('A4:K'.($rows-1))->applyFromArray($tableCellStyledArray);
		}

      // save data
      $this->Save();
   }
}
?>
