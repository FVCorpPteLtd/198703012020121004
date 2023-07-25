<?php

/*
 * ***************************************************************
 * Script : 
 * Version : 
 * Date :
 * Author : Fransiscus Tampubolon
 * Email : fransiscus.tpbolon@gmail.com
 * Description : Ujian Kompetensi
 * ***************************************************************
 */

/**
 * Description of Crud
 *
 * @author Pudyasto
 */
class Laporan extends CI_Controller
{
	//put your code here
	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		$this->load->model('Kompetensi');
	}

	public function index()
	{
		$this->load->view('design/header');
		$this->load->view('v_kompetensi');
	}

	public function get_data_rekrutmen()
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'http://103.226.55.159/json/data_rekrutmen.json',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
		));

		$response = curl_exec($curl);
		curl_close($curl);
		$dataRespon = json_decode($response, TRUE);
		$dataRekrutmen = $dataRespon['Form Responses 1'];
		$dataRekrutmenArr = json_encode($dataRekrutmen);
		$dataRekrutmen = json_decode($dataRekrutmenArr);

		// Data Atribut
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'http://103.226.55.159/json/data_attribut.json',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
		));

		$response = curl_exec($curl);
		curl_close($curl);
		$dataAtribut = json_decode($response);
		$dataAtributArr = array();
		$atribut = array();
		foreach ($dataAtribut as $row) {
			$dataAtributArr[$row->id_pendaftar][$row->jenis_attr] = $row->value;
			$atribut[$row->jenis_attr] = $row->jenis_attr;
		}
		foreach ($dataRekrutmen as $row) {
			foreach ($atribut as $key => $data) {
				if (isset($dataAtributArr[$row->id][$key])) {
					$row->$key = $dataAtributArr[$row->id][$key];
				} else {
					$row->$key = "-";
				}
			}
		}

		$data = array();
		$data["rows"] = $dataRekrutmen;
		$data["total"] = count($dataRekrutmen);
		echo json_encode($data);
	}



	public function laporanExcel()
	{
		$tgl_awal = $this->input->get('tgl_awal');
		$tgl_akhir = $this->input->get('tgl_akhir');
		$jto_awal = $this->input->get('jto_awal');
		$jto_akhir = $this->input->get('jto_akhir');
		$customer_code = $this->input->get('customer_code');
		$active_status = $this->input->get('active_status');

		$data = array();
		$data_op = array();
		$filterRulesField = explode("@@", $this->input->get('filterRulesField'));
		$filterRulesValue = explode("@@", $this->input->get('filterRulesValue'));
		$filterRulesOp = explode("@@", $this->input->get('filterRulesOp'));

		for ($i = 0; $i < sizeof($filterRulesField); $i++) {
			$data[$filterRulesField[$i]] = $filterRulesValue[$i];
			$data_op[$filterRulesField[$i]] = $filterRulesOp[$i];
		}

		$dataSet = $this->sale->getPenjualanDetail_excel($tgl_awal, $tgl_akhir, $jto_awal, $jto_akhir, $customer_code, $active_status, $data, $data_op);

		$namaFile = "ListPenjualanDetail.xlsx";
		$this->load->library("PHPExcel/PHPExcel");
		//$objPHPExcel = new PHPExcel();
		$inputFileType = 'Excel2007';
		$inputFileName = 'templates/ListPenjualanDetail.xlsx';

		$objPHPExcelReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objPHPExcelReader->load($inputFileName);
		$objPHPExcel->getProperties()->setCreator($this->session->userdata('nama'))
			->setLastModifiedBy($this->session->userdata('nama'))
			->setTitle("List Penjualan Detail")
			->setSubject("List Penjualan Detail")
			->setDescription("List Penjualan Detail")
			->setKeywords("List Penjualan Detail")
			->setCategory("List Penjualan Detail");
		$i = 2;
		$j = 1;
		foreach ($dataSet as $row) {
			$diskon = '';
			if ($row->disc > 0) {
				$diskon = number_format($row->disc, 1, ",", ".") . '%';
			}

			if ($row->discrp > 0) {
				if (empty($diskon)) {
					$diskon = number_format($row->discrp, 0, ",", ".");
				} else {
					$diskon .= '+' . number_format($row->discrp, 0, ",", ".");
				}
			}
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValueExplicit('A' . $i, $j, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('B' . $i, $row->tglsale, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('C' . $i, $row->jto, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('D' . $i, $row->saleno, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('E' . $i, $row->sales, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('F' . $i, $row->outlet_name, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('G' . $i, $row->grupTagihan, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('H' . $i, $row->city, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('I' . $i, $row->part_code, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('J' . $i, $row->part_name, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('K' . $i, $row->merk, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('L' . $i, $row->motor_type, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('M' . $i, $row->qty, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('N' . $i, $row->price, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('O' . $i, $diskon, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('P' . $i, $row->total, PHPExcel_Cell_DataType::TYPE_NUMERIC);


			$i++;
			$j++;
		}

		$objPHPExcel->getActiveSheet()->setTitle('List Penjualan Detail');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename=' . $namaFile);
		$objWriter->save("php://output");
		//if($saveExcel){
	}

	public function laporanExcelCSV()
	{
		$tgl_awal = $this->input->get('tgl_awal');
		$tgl_akhir = $this->input->get('tgl_akhir');
		$jto_awal = $this->input->get('jto_awal');
		$jto_akhir = $this->input->get('jto_akhir');
		$customer_code = $this->input->get('customer_code');
		$active_status = $this->input->get('active_status');

		$data = array();
		$data_op = array();
		$filterRulesField = explode("@@", $this->input->get('filterRulesField'));
		$filterRulesValue = explode("@@", $this->input->get('filterRulesValue'));
		$filterRulesOp = explode("@@", $this->input->get('filterRulesOp'));

		for ($i = 0; $i < sizeof($filterRulesField); $i++) {
			$data[$filterRulesField[$i]] = $filterRulesValue[$i];
			$data_op[$filterRulesField[$i]] = $filterRulesOp[$i];
		}

		$dataSet = $this->sale->getPenjualanDetail_excel($tgl_awal, $tgl_akhir, $jto_awal, $jto_akhir, $customer_code, $active_status, $data, $data_op);

		$namaFile = "ListPenjualanDetail.csv";
		$this->load->library("PHPExcel/PHPExcel");
		//$objPHPExcel = new PHPExcel();
		$inputFileType = 'Excel2007';
		$inputFileName = 'templates/ListPenjualanDetail.xlsx';

		$objPHPExcelReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objPHPExcelReader->load($inputFileName);
		$objPHPExcel->getProperties()->setCreator($this->session->userdata('nama'))
			->setLastModifiedBy($this->session->userdata('nama'))
			->setTitle("List Penjualan Detail")
			->setSubject("List Penjualan Detail")
			->setDescription("List Penjualan Detail")
			->setKeywords("List Penjualan Detail")
			->setCategory("List Penjualan Detail");
		$i = 2;
		$j = 1;
		foreach ($dataSet as $row) {
			$diskon = '';
			if ($row->disc > 0) {
				$diskon = $row->disc;
			}

			if ($row->discrp > 0) {
				if (empty($diskon)) {
					$diskon = number_format($row->discrp, 0, ",", ".");
				} else {
					$diskon .= '+' . number_format($row->discrp, 0, ",", ".");
				}
			}
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValueExplicit('A' . $i, $j, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('B' . $i, $row->tglsale, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('C' . $i, $row->jto, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('D' . $i, $row->saleno, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('E' . $i, $row->sales, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('F' . $i, $row->outlet_name, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('G' . $i, $row->grupTagihan, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('H' . $i, $row->city, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('I' . $i, $row->part_code, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('J' . $i, $row->part_name, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('K' . $i, $row->merk, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('L' . $i, $row->motor_type, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('M' . $i, $row->qty, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('N' . $i, $row->price, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('O' . $i, $diskon, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('P' . $i, $row->total, PHPExcel_Cell_DataType::TYPE_NUMERIC);


			$i++;
			$j++;
		}

		$objPHPExcel->getActiveSheet()->setTitle('List Penjualan Detail');
		$objWriter = new PHPExcel_Writer_CSV($objPHPExcel);
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename=' . $namaFile);
		$objWriter->save("php://output");
		//if($saveExcel){
	}

	public function laporanExcelCSV_part()
	{
		$tgl_awal = $this->input->get('tgl_awal');
		$tgl_akhir = $this->input->get('tgl_akhir');
		$jto_awal = $this->input->get('jto_awal');
		$jto_akhir = $this->input->get('jto_akhir');
		$customer_code = $this->input->get('customer_code');
		$active_status = $this->input->get('active_status');

		$data = array();
		$data_op = array();
		$filterRulesField = explode("@@", $this->input->get('filterRulesField'));
		$filterRulesValue = explode("@@", $this->input->get('filterRulesValue'));
		$filterRulesOp = explode("@@", $this->input->get('filterRulesOp'));

		for ($i = 0; $i < sizeof($filterRulesField); $i++) {
			$data[$filterRulesField[$i]] = $filterRulesValue[$i];
			$data_op[$filterRulesField[$i]] = $filterRulesOp[$i];
		}

		$dataSet = $this->sale->getPart_PenjualanDetail_excel($tgl_awal, $tgl_akhir, $jto_awal, $jto_akhir, $customer_code, $active_status, $data, $data_op);

		$namaFile = "ListSparepart.xlsx";
		$this->load->library("PHPExcel/PHPExcel");
		//$objPHPExcel = new PHPExcel();
		$inputFileType = 'Excel2007';
		$inputFileName = 'templates/ListSparepart.xlsx';

		$objPHPExcelReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objPHPExcelReader->load($inputFileName);
		$objPHPExcel->getProperties()->setCreator($this->session->userdata('nama'))
			->setLastModifiedBy($this->session->userdata('nama'))
			->setTitle("List Sparepart")
			->setSubject("List Sparepart")
			->setDescription("List Sparepart")
			->setKeywords("List Sparepart")
			->setCategory("List Sparepart");

		$i = 2;
		$j = 1;
		foreach ($dataSet as $row) {
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValueExplicit('A' . $i, $j, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('B' . $i, $row->part_code, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('C' . $i, $row->general_name, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('D' . $i, $row->category, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('E' . $i, $row->merk, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('F' . $i, $row->part_name, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('G' . $i, $row->motor_type, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('H' . $i, $row->satuan, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('I' . $i, $row->supplier, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('J' . $i, $row->modal, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('K' . $i, $row->discModal1, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('L' . $i, $row->discModal2, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('M' . $i, $row->discModal3, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('N' . $i, $row->discModal_rp, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('O' . $i, $row->netModal, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('P' . $i, $row->level_harga1, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('Q' . $i, $row->diskon1, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('R' . $i, $row->level_harga2, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('S' . $i, $row->diskon2, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('T' . $i, $row->level_harga3, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('U' . $i, $row->diskon3, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('V' . $i, $row->level_harga4, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('W' . $i, $row->diskon4, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('X' . $i, $row->level_harga5, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('Y' . $i, $row->diskon5, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('Z' . $i, $row->level_harga6, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('AA' . $i, $row->diskon6, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('AB' . $i, $row->level_harga7, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('AC' . $i, $row->diskon7, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('AD' . $i, $row->level_harga8, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('AE' . $i, $row->diskon8, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('AF' . $i, $row->level_harga9, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('AG' . $i, $row->diskon9, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('AH' . $i, $row->level_harga10, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('AI' . $i, $row->diskon10, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('AJ' . $i, $row->produksi, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('AK' . $i, $row->kode_rak, PHPExcel_Cell_DataType::TYPE_STRING);


			$i++;
			$j++;
		}

		$objPHPExcel->getActiveSheet()->setTitle('List Penjualan Detail');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename=' . $namaFile);
		$objWriter->save("php://output");
	}

	public function laporanShow()
	{
		$tgl_awal = $this->input->get('tgl_awal');
		$tgl_akhir = $this->input->get('tgl_akhir');
		$jto_awal = $this->input->get('jto_awal');
		$jto_akhir = $this->input->get('jto_akhir');
		$customer_code = $this->input->get('customer_code');
		$active_status = $this->input->get('active_status');

		$page = $this->input->post('page');
		$rows = $this->input->post('rows');
		$sort = $this->input->post('sort');

		if (empty($page)) {
			$page = 1;
		}
		if (empty($rows)) {
			$rows = 20;
		}
		$offset = ($page - 1) * $rows;
		if (empty($sort)) {
			$sort = 'hs.customer_code';
		}
		if (empty($order)) {
			$order = 'ASC';
		}

		$data = array();
		$data_op = array();
		$filterRules = !empty($this->input->post('filterRules')) ? ($this->input->post('filterRules')) : '';
		$cond = '1=1';
		if (!empty($filterRules)) {
			$filterRules = json_decode($filterRules);
			foreach ($filterRules as $rule) {
				$rule = get_object_vars($rule);
				$field = $rule['field'];
				$op = $rule['op'];
				$value = $rule['value'];
				if (!empty($value)) {
					$data[$field] = $value;
					$data_op[$field] = $op;
				}
			}
		}

		$total_data = $this->sale->lapPenjualanDetail_page($data, $data_op, $tgl_awal, $tgl_akhir, $jto_awal, $jto_akhir, $customer_code, $active_status);
		$data_simpan = $this->sale->lapPenjualanDetail($offset, $rows, $data, $data_op, $tgl_awal, $tgl_akhir, $jto_awal, $jto_akhir, $customer_code, $active_status);
		foreach ($data_simpan as $row) {
			$row->diskon = '';
			if ($row->disc > 0) {
				$row->diskon = number_format($row->disc, 1, ",", ".") . '%';
			}

			if ($row->discrp > 0) {
				if (empty($diskon)) {
					$row->diskon = number_format($row->discrp, 0, ",", ".");
				} else {
					$row->diskon .= '+' . number_format($row->discrp, 0, ",", ".");
				}
			}
		}

		$data_simpan_arr = array();
		$data_simpan_arr["rows"] = $data_simpan;
		$data_simpan_arr["total"] = $total_data;

		echo json_encode($data_simpan_arr);
	}
}
