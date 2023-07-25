<?php
class PDF extends FPDF
{
	//var $bc1d;
	//var $bc2d;
	/*var $city;
	var $saleno;
	var $sales;
	var $tglsale;
	var $jto;
	var $description;
	var $nb;
	var $ket1;
	var $ket2;
	var $ket3;
	var $ket4;
	var $ket5;
	
	

	function parsingData($info,$jumlah_halaman,$keterangan) {
		foreach ($info as $key) {
			$this->outlet_name = $key->outlet_name;
			$this->address = $key->address;
			$this->city = $key->city;
			$this->saleno = $key->saleno;
			$this->sales = $key->sales;
			$this->tglsale = $key->tglsale;
			$this->jto = $key->jto;
			$this->description = $key->description;
			$sisa = $jumlah_halaman % 15;
			if($jumlah_halaman % 15 == 0){
				$this->nb = $jumlah_halaman/15;
			}else{
				$this->nb = (($jumlah_halaman-$sisa)/15)+1;
			}
			
		}
		
		foreach ($keterangan as $key) {
			if($key->id == 1){
				$this->ket1 = $key->description;
			}elseif($key->id == 2){
				$this->ket2 = $key->description;
			}elseif($key->id == 3){
				$this->ket3 = $key->description;
			}elseif($key->id == 4){
				$this->ket4 = $key->description;
			}elseif($key->id == 5){
				$this->ket5 = $key->description;
			}
			
		}
	}
	*/
	function Header()
	{
                
	}
 
	function Content($data,$tgl_produksi)
	{

		$this->SetTopMargin(10);
		$this->SetLeftMargin(20);
		$this->SetRightMargin(3);
        
        
        $ya = 10;
        $rw = 21;
        $no = 1;
        $grandtotal = 0;
        foreach ($data as $key) {
        	$this->ln(13);
        	$this->setFont('Helvetica','',10);
	        $this->setFillColor(250,255,255);
	        //$this->SetTextColor(250, 255, 255);
	        $this->cell(57,6,$key->part_name,0,0,'L',1);
	        $this->cell(20,6,'',0,0,'L',1);
	        $this->cell(57,6,$key->part_name,0,1,'L',1);
	        $this->setFont('Helvetica','B',15);
	        $this->cell(57,6,$key->general_name,0,0,'L',1);
	        $this->cell(20,6,'',0,0,'L',1);
	        $this->cell(57,6,$key->general_name,0,1,'L',1);
	        $this->setFont('Helvetica','',10);
	        $this->cell(10,6,'QTY ',0,0,'L',1);
	        $this->setFont('Helvetica','B',15);
	        $this->cell(30,6,$key->qty,0,0,'L',1);
	        $this->setFont('Helvetica','',10);
	        $this->cell(17,6,$tgl_produksi,0,0,'R',1);
	        $this->cell(20,6,'',0,0,'L',1);
	        $this->cell(10,6,'QTY ',0,0,'L',1);
	        $this->setFont('Helvetica','B',15);
	        $this->cell(30,6,$key->qty,0,0,'L',1);
	        $this->setFont('Helvetica','',10);
	        $this->cell(17,6,$tgl_produksi,0,1,'R',1);
	        $this->Image('http://www.mpmsmart.com/mpmmotor/barcode/pdf417_'.$key->part_code.'.png', 20, 40,'57','12.2','png');
	        //$this->Image(base_url().'barcode/pdf417_'.$key->part_code.'.png', 97, 40,'57','12.2','png');
	        /*
	        $this->ln(35);
        	$this->setFont('Helvetica','',10);
	        $this->setFillColor(250,255,255);
	        //$this->SetTextColor(250, 255, 255);
	        $this->cell(57,6,$key->part_name,0,0,'L',1);
	        $this->cell(20,6,'',0,0,'L',1);
	        $this->cell(57,6,$key->part_name,0,1,'L',1);
	        $this->setFont('Helvetica','B',15);
	        $this->cell(57,6,$key->general_name,0,0,'L',1);
	        $this->cell(20,6,'',0,0,'L',1);
	        $this->cell(57,6,$key->general_name,0,1,'L',1);
	        $this->setFont('Helvetica','',10);
	        $this->cell(10,6,'QTY ',0,0,'L',1);
	        $this->setFont('Helvetica','B',15);
	        $this->cell(30,6,$key->qty,0,0,'L',1);
	        $this->setFont('Helvetica','',10);
	        $this->cell(17,6,$tgl_produksi,0,0,'R',1);
	        $this->cell(20,6,'',0,0,'L',1);
	        $this->cell(10,6,'QTY ',0,0,'L',1);
	        $this->setFont('Helvetica','B',15);
	        $this->cell(30,6,$key->qty,0,0,'L',1);
	        $this->setFont('Helvetica','',10);
	        $this->cell(17,6,$tgl_produksi,0,1,'R',1);
	        $this->Image(base_url().'barcode/pdf417_'.$key->part_code.'.png', 20, 93,'57','12.2','png');
	        $this->Image(base_url().'barcode/pdf417_'.$key->part_code.'.png', 97, 93,'57','12.2','png');
	        
	        $this->ln(35);
        	$this->setFont('Helvetica','',10);
	        $this->setFillColor(250,255,255);
	        //$this->SetTextColor(250, 255, 255);
	        $this->cell(57,6,$key->part_name,0,0,'L',1);
	        $this->cell(20,6,'',0,0,'L',1);
	        $this->cell(57,6,$key->part_name,0,1,'L',1);
	        $this->setFont('Helvetica','B',15);
	        $this->cell(57,6,$key->general_name,0,0,'L',1);
	        $this->cell(20,6,'',0,0,'L',1);
	        $this->cell(57,6,$key->general_name,0,1,'L',1);
	        $this->setFont('Helvetica','',10);
	        $this->cell(10,6,'QTY ',0,0,'L',1);
	        $this->setFont('Helvetica','B',15);
	        $this->cell(30,6,$key->qty,0,0,'L',1);
	        $this->setFont('Helvetica','',10);
	        $this->cell(17,6,$tgl_produksi,0,0,'R',1);
	        $this->cell(20,6,'',0,0,'L',1);
	        $this->cell(10,6,'QTY ',0,0,'L',1);
	        $this->setFont('Helvetica','B',15);
	        $this->cell(30,6,$key->qty,0,0,'L',1);
	        $this->setFont('Helvetica','',10);
	        $this->cell(17,6,$tgl_produksi,0,1,'R',1);
	        $this->Image(base_url().'barcode/pdf417_'.$key->part_code.'.png', 20, 146,'57','12.2','png');
	        $this->Image(base_url().'barcode/pdf417_'.$key->part_code.'.png', 97, 146,'57','12.2','png');
	        
	        $this->ln(35);
        	$this->setFont('Helvetica','',10);
	        $this->setFillColor(250,255,255);
	        //$this->SetTextColor(250, 255, 255);
	        $this->cell(57,6,$key->part_name,0,0,'L',1);
	        $this->cell(20,6,'',0,0,'L',1);
	        $this->cell(57,6,$key->part_name,0,1,'L',1);
	        $this->setFont('Helvetica','B',15);
	        $this->cell(57,6,$key->general_name,0,0,'L',1);
	        $this->cell(20,6,'',0,0,'L',1);
	        $this->cell(57,6,$key->general_name,0,1,'L',1);
	        $this->setFont('Helvetica','',10);
	        $this->cell(10,6,'QTY ',0,0,'L',1);
	        $this->setFont('Helvetica','B',15);
	        $this->cell(30,6,$key->qty,0,0,'L',1);
	        $this->setFont('Helvetica','',10);
	        $this->cell(17,6,$tgl_produksi,0,0,'R',1);
	        $this->cell(20,6,'',0,0,'L',1);
	        $this->cell(10,6,'QTY ',0,0,'L',1);
	        $this->setFont('Helvetica','B',15);
	        $this->cell(30,6,$key->qty,0,0,'L',1);
	        $this->setFont('Helvetica','',10);
	        $this->cell(17,6,$tgl_produksi,0,1,'R',1);
	        $this->Image(base_url().'barcode/pdf417_'.$key->part_code.'.png', 20, 199,'57','12.2','png');
	        $this->Image(base_url().'barcode/pdf417_'.$key->part_code.'.png', 97, 199,'57','12.2','png');
	        
	        $this->ln(35);
        	$this->setFont('Helvetica','',10);
	        $this->setFillColor(250,255,255);
	        //$this->SetTextColor(250, 255, 255);
	        $this->cell(57,6,$key->part_name,0,0,'L',1);
	        $this->cell(20,6,'',0,0,'L',1);
	        $this->cell(57,6,$key->part_name,0,1,'L',1);
	        $this->setFont('Helvetica','B',15);
	        $this->cell(57,6,$key->general_name,0,0,'L',1);
	        $this->cell(20,6,'',0,0,'L',1);
	        $this->cell(57,6,$key->general_name,0,1,'L',1);
	        $this->setFont('Helvetica','',10);
	        $this->cell(10,6,'QTY ',0,0,'L',1);
	        $this->setFont('Helvetica','B',15);
	        $this->cell(30,6,$key->qty,0,0,'L',1);
	        $this->setFont('Helvetica','',10);
	        $this->cell(17,6,$tgl_produksi,0,0,'R',1);
	        $this->cell(20,6,'',0,0,'L',1);
	        $this->cell(10,6,'QTY ',0,0,'L',1);
	        $this->setFont('Helvetica','B',15);
	        $this->cell(30,6,$key->qty,0,0,'L',1);
	        $this->setFont('Helvetica','',10);
	        $this->cell(17,6,$tgl_produksi,0,1,'R',1);
	        $this->Image(base_url().'barcode/pdf417_'.$key->part_code.'.png', 20, 252,'57','12.2','png');
	        $this->Image(base_url().'barcode/pdf417_'.$key->part_code.'.png', 97, 252,'57','12.2','png');
        	*/
        	if(!empty(next($data))){
				$this->AddPage('P','Legal');
			}
        }   
	}
	
	function Footer()
	{
		
	}
}

use BG\Barcode\Base1DBarcode as BarCode1D;
use BG\Barcode\Base2DBarcode as BarCode2D;
$bc1d = new BarCode1D();
$bc2d = new BarCode2D();

$bc1d->savePath = $_SERVER['DOCUMENT_ROOT'].'/tmp/';
$bc2d->savePath = './barcode/';

foreach($data as $row){
	$bc2d->getBarcodePNGPath($row->part_code, 'pdf417', $row->part_code, 10, 8.75);
}

$tgl1 = date('Y-m-d');
$tgl2 = date('Y-m-d', strtotime('-75 days', strtotime($tgl1)));
$arr = explode('-',$tgl2);
$bln = $arr[1];
if(substr($arr[1],0,1) == '0'){
	$bln = 'A'.substr($arr[1],1,1);
}
$tgl_produksi = $arr[2].$bln.'M'.substr($arr[0],2,2);


$pdf = new PDF();
//$pdf->parsingData($info,$jumlah_halaman,$keterangan);
$pdf->AliasNbPages();
$pdf->AddPage('P','Legal');
$pdf->Content($data,$tgl_produksi);
$pdf->Output();

