<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * ***************************************************************
 * Script : 
 * Version : 
 * Date :
 * Author : Pudyasto Adi W.
 * Email : mr.pudyasto@gmail.com
 * Description : 
 * ***************************************************************
 */

/**
 * Description of queries
 *
 * @author Pudyasto
 */
class Kompetensi extends CI_Model
{
	//put your code here
	function __construct()
	{
		parent::__construct();
	}

	function hutangFrom_purchase($purchaseno, $supplier_code, $totalpurchase, $idpengguna, $tglpurchase)
	{
		$cekCredit = $this->cekSupplier_debt($supplier_code);
		if ($cekCredit <= 0) {
			$data = array(
				'supplier_code' => $supplier_code,
				'last_debt_purchaseno' => $purchaseno,
				'last_debt_nominal' => $totalpurchase,
				'saldo_debt' => $totalpurchase,
				'created_by' => $idpengguna,
				'dt_created' => date("Y-m-d H-i-s"),
			);
			$query = $this->db->insert('tbl_debt', $data);
		} else {
			$sql_data = "update tbl_debt ";
			$sql_data .= "set last_debt_purchaseno = '$purchaseno', ";
			$sql_data .= "last_debt_nominal = $totalpurchase, ";
			$sql_data .= "saldo_debt = saldo_debt + $totalpurchase, ";
			$sql_data .= "updated_by = $idpengguna, ";
			$sql_data .= "dt_updated = '" . date("Y-m-d H-i-s") . "' ";
			$sql_data .= "where supplier_code ='$supplier_code' ";
			$query = $this->db->query("$sql_data");
		}

		$data = array(
			'purchaseno' => $purchaseno,
			'nominal' => $totalpurchase,
			'debt_trx_tgl' => $tglpurchase,
			'debt_trx_type' => 1,
			'created_by' => $idpengguna,
			'dt_created' => date("Y-m-d H-i-s"),
		);
		$query = $this->db->insert('tbl_trx_debt', $data);
		return true;
	}

	function cekSupplier_debt($supplier_code)
	{
		$this->db->select('supplier_code');
		$this->db->from('tbl_debt');
		$this->db->where('supplier_code', $supplier_code);
		$query = $this->db->get();
		$result_data = $query->num_rows($query);
		return $result_data;
	}

	function addSupplier_debt($supplier_code, $totalpurchase, $idpengguna)
	{
		$sql_data = "update tbl_debt ";
		$sql_data .= "set saldo_debt = saldo_debt + $totalpurchase, ";
		$sql_data .= "updated_by = $idpengguna, ";
		$sql_data .= "dt_updated = '" . date("Y-m-d H-i-s") . "' ";
		$sql_data .= " where supplier_code ='$supplier_code' ";
		$query = $this->db->query("$sql_data");
		return true;
	}

	function minSupplier_debt($supplier_code, $totalpurchase, $idpengguna)
	{
		$sql_data = "update tbl_debt ";
		$sql_data .= "set saldo_debt = saldo_debt - $totalpurchase, ";
		$sql_data .= "updated_by = $idpengguna, ";
		$sql_data .= "dt_updated = '" . date("Y-m-d H-i-s") . "' ";
		$sql_data .= " where supplier_code ='$supplier_code' ";
		$query = $this->db->query("$sql_data");
		return true;
	}

	function update_trx_debt($purchaseno, $totalpurchase, $idpengguna)
	{
		$data = array(
			'nominal' => $totalpurchase,
			'updated_by' => $idpengguna,
			'dt_updated' => date("Y-m-d H-i-s"),
		);
		$this->db->where('purchaseno', $purchaseno);
		$this->db->update('tbl_trx_debt', $data);
		return true;
	}

	function update_pembelian_lunas($data, $purchaseno)
	{
		$this->db->where('purchaseno', $purchaseno);
		$this->db->update('tbl_header_purchase', $data);
		return true;
	}

	function save_trx_debt($trxno, $totalpurchase, $idpengguna, $tglpurchase)
	{
		$data = array(
			'purchaseno' => $trxno,
			'nominal' => $totalpurchase,
			'debt_trx_tgl' => $tglpurchase,
			'debt_trx_type' => 2,
			'created_by' => $idpengguna,
			'dt_created' => date("Y-m-d H-i-s"),
		);
		$query = $this->db->insert('tbl_trx_debt', $data);
		return true;
	}

	function getHeader_hutang($offset, $rows, $data, $data_op, $supplier_code)
	{
		if (!empty($supplier_code)) {
			$this->db->where('hp.supplier_code', $supplier_code);
		}
		if (!empty($data['tglpurchase'])) {
			$arr = explode("/", $data['tglpurchase']);
			$tglpurchase = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('hp.tglpurchase', $tglpurchase);
		}
		if (!empty($data['jto'])) {
			$arr = explode("/", $data['jto']);
			$jto = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('hp.jto', $jto);
		}
		if (!empty($data['purchaseno'])) {
			$this->db->like('hp.purchaseno', $data['purchaseno'], 'both');
		}
		if (!empty($data['soID'])) {
			$this->db->like('hp.soID', $data['soID'], 'both');
		}
		if (!empty($data['supplier_code'])) {
			$this->db->like('hp.supplier_code', $data['supplier_code'], 'both');
		}
		if (!empty($data['supplier_name'])) {
			$this->db->like('s.supplier_name', $data['supplier_name'], 'both');
		}
		if (!empty($data['description'])) {
			$this->db->like('hp.description', $data['description'], 'both');
		}
		if (!empty($data['payrange'])) {
			$this->db->where('hp.payrange', $data['payrange']);
		}
		if (!empty($data['active_status'])) {
			$this->db->where('hp.active_status', $data['active_status']);
		}
		if (!empty($data['paymethod'])) {
			$this->db->where('hp.paymethod', $data['paymethod']);
		}
		if (!empty($data['pay_status'])) {
			$this->db->where('hp.pay_status', $data['pay_status']);
		}
		/*if(!empty($data['print_status'])){
			$this->db->where('hp.print_status', $data['print_status']);
		}*/
		if (!empty($data['totalpurchase'])) {
			if ($data_op['totalpurchase'] == 'equal') {
				$this->db->where('hp.totalpurchase', $data['totalpurchase']);
			} else if ($data_op['totalpurchase'] == 'less') {
				$this->db->where('hp.totalpurchase <=', $data['totalpurchase']);
			} else if ($data_op['totalpurchase'] == 'greater') {
				$this->db->where('hp.totalpurchase >=', $data['totalpurchase']);
			}
		}

		$sub_query_from = "(SELECT sum(nominal) as jlh_pembayaran_hutang,purchaseno FROM tbl_trx_debt ";
		$sub_query_from .= "WHERE (debt_trx_type = 4 or debt_trx_type = 5) and status = 1 GROUP BY purchaseno )";

		$sub_query_from2 = "(SELECT sum(nominal) as pending,purchaseno FROM tbl_trx_debt_tmp ";
		$sub_query_from2 .= "WHERE (debt_trx_type = 4 or debt_trx_type = 5) and status = 1 GROUP BY purchaseno )";

		$this->db->select('hp.*');
		$this->db->select('s.*');
		$this->db->select('db.jlh_pembayaran_hutang');
		$this->db->select('tmp.pending');
		$this->db->select('IF(db.jlh_pembayaran_hutang>0,(hp.totalpurchase - db.jlh_pembayaran_hutang),hp.totalpurchase) as saldo', false);
		$this->db->select('IF(tmp.pending>0,IF(db.jlh_pembayaran_hutang>0,(hp.totalpurchase - db.jlh_pembayaran_hutang - tmp.pending),(hp.totalpurchase - tmp.pending)),0) as saldopending', false);

		//$this->db->select('(hp.totalpurchase - db.jlh_pembayaran_hutang) as saldo');
		$this->db->select('pm.description as paymethod_str');
		$this->db->select('ps.description as pay_status_str');
		$this->db->select('pa.description as active_status_str');
		$this->db->from('tbl_header_purchase hp');
		$this->db->join("$sub_query_from db", 'hp.purchaseno = db.purchaseno', 'left');
		$this->db->join("$sub_query_from2 tmp", 'hp.purchaseno = tmp.purchaseno', 'left');
		$this->db->join('tbl_supplier s', 's.supplier_code = hp.supplier_code');
		$this->db->join('tbl_parameter pm', 'hp.paymethod = pm.id');
		$this->db->join('tbl_parameter ps', 'hp.pay_status = ps.id');
		$this->db->join('tbl_parameter pa', 'hp.active_status = pa.id');
		$this->db->where('hp.post_status !=', '0');
		$this->db->where('pm.name', 'pay_method_sale');
		$this->db->where('ps.name', 'pay_status');
		$this->db->where('pa.name', 'active_status');
		$this->db->where('hp.pay_status', 1);
		$this->db->where('hp.active_status', 1);
		$this->db->order_by('hp.tglpurchase, hp.purchaseno', 'ASC');
		$this->db->limit($rows, $offset);
		$query = $this->db->get();

		return $query->result();
	}

	function getHeader_hutang_page($data, $data_op, $supplier_code)
	{
		if (!empty($supplier_code)) {
			$this->db->where('hp.supplier_code', $supplier_code);
		}
		if (!empty($data['tglpurchase'])) {
			$arr = explode("/", $data['tglpurchase']);
			$tglpurchase = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('hp.tglpurchase', $tglpurchase);
		}
		if (!empty($data['jto'])) {
			$arr = explode("/", $data['jto']);
			$jto = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('hp.jto', $jto);
		}
		if (!empty($data['purchaseno'])) {
			$this->db->like('hp.purchaseno', $data['purchaseno'], 'both');
		}
		if (!empty($data['soID'])) {
			$this->db->like('hp.soID', $data['soID'], 'both');
		}
		if (!empty($data['supplier_code'])) {
			$this->db->like('hp.supplier_code', $data['supplier_code'], 'both');
		}
		if (!empty($data['supplier_name'])) {
			$this->db->like('s.supplier_name', $data['supplier_name'], 'both');
		}
		if (!empty($data['description'])) {
			$this->db->like('hp.description', $data['description'], 'both');
		}
		if (!empty($data['payrange'])) {
			$this->db->where('hp.payrange', $data['payrange']);
		}
		if (!empty($data['active_status'])) {
			$this->db->where('hp.active_status', $data['active_status']);
		}
		if (!empty($data['paymethod'])) {
			$this->db->where('hp.paymethod', $data['paymethod']);
		}
		if (!empty($data['pay_status'])) {
			$this->db->where('hp.pay_status', $data['pay_status']);
		}
		/*if(!empty($data['print_status'])){
			$this->db->where('hp.print_status', $data['print_status']);
		}*/
		if (!empty($data['totalpurchase'])) {
			if ($data_op['totalpurchase'] == 'equal') {
				$this->db->where('hp.totalpurchase', $data['totalpurchase']);
			} else if ($data_op['totalpurchase'] == 'less') {
				$this->db->where('hp.totalpurchase <=', $data['totalpurchase']);
			} else if ($data_op['totalpurchase'] == 'greater') {
				$this->db->where('hp.totalpurchase >=', $data['totalpurchase']);
			}
		}

		$this->db->select('hp.purchaseno');
		$this->db->select('s.*');
		$this->db->from('tbl_header_purchase hp');
		$this->db->join('tbl_supplier s', 's.supplier_code = hp.supplier_code');
		$this->db->where('hp.post_status !=', '0');
		$this->db->where('hp.pay_status', 1);
		$this->db->where('hp.active_status', 1);

		$query = $this->db->get();
		$result_data = $query->num_rows($query);
		return $result_data;
	}

	function getPembayaran_hutang($offset, $rows, $data, $data_op)
	{
		if (!empty($data['debt_trx_tgl_start'])) {
			$arr = explode("/", $data['debt_trx_tgl_start']);
			$debt_trx_tgl_start = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('tc.debt_trx_tgl >=', $debt_trx_tgl_start);
		}
		if (!empty($data['debt_trx_tgl_end'])) {
			$arr = explode("/", $data['debt_trx_tgl_end']);
			$debt_trx_tgl_end = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('tc.debt_trx_tgl <=', $debt_trx_tgl_end);
		}
		if (!empty($data['no_kuitansi'])) {
			$this->db->like('tc.no_kuitansi', $data['no_kuitansi'], 'both');
		}
		if (!empty($data['no_pembayaran'])) {
			$this->db->like('tc.no_pembayaran', $data['no_pembayaran'], 'both');
		}
		if (!empty($data['cara_bayar'])) {
			$this->db->like('tc.cara_bayar', $data['cara_bayar'], 'both');
		}
		if (!empty($data['keterangan'])) {
			$this->db->like('tc.keterangan', $data['keterangan'], 'both');
		}

		if (!empty($data['supplier_name'])) {
			$this->db->like('sp.supplier_name', $data['supplier_name'], 'both');
		}

		//$sub_query_from = "(SELECT sum(nominal) as jlh_pembayaran_hutang,purchaseno FROM tbl_trx_debt ";
		//$sub_query_from .= "WHERE (debt_trx_type = 4 or debt_trx_type = 5) and status = 1 GROUP BY purchaseno )";

		$this->db->select('tc.*');
		$this->db->select('sp.supplier_name');
		$this->db->select('tc.debt_trx_tgl as debt_trx_tgl_start');
		$this->db->select('tc.debt_trx_tgl as debt_trx_tgl_end');
		$this->db->select('sum(tc.nominal) as totalnominal');
		$this->db->select('hs.*');
		$this->db->from('tbl_trx_debt_tmp tc');
		$this->db->join('tbl_header_purchase hs', 'tc.purchaseno = hs.purchaseno', 'left');
		$this->db->join('tbl_supplier sp', 'hs.supplier_code = sp.supplier_code', 'left');
		$this->db->where('hs.post_status !=', '0');
		$this->db->where('hs.pay_status', 1);
		$this->db->order_by('hs.purchaseid', 'ASC');
		$this->db->limit($rows, $offset);
		$this->db->group_by('tc.no_kuitansi');
		$query = $this->db->get();

		return $query->result();
	}

	function getPembayaran_hutang_page($data, $data_op)
	{
		if (!empty($data['debt_trx_tgl_start'])) {
			$arr = explode("/", $data['debt_trx_tgl_start']);
			$debt_trx_tgl_start = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('tc.debt_trx_tgl >=', $debt_trx_tgl_start);
		}
		if (!empty($data['debt_trx_tgl_end'])) {
			$arr = explode("/", $data['debt_trx_tgl_end']);
			$debt_trx_tgl_end = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('tc.debt_trx_tgl <=', $debt_trx_tgl_end);
		}
		if (!empty($data['no_kuitansi'])) {
			$this->db->like('tc.no_kuitansi', $data['no_kuitansi'], 'both');
		}
		if (!empty($data['no_pembayaran'])) {
			$this->db->like('tc.no_pembayaran', $data['no_pembayaran'], 'both');
		}
		if (!empty($data['cara_bayar'])) {
			$this->db->like('tc.cara_bayar', $data['cara_bayar'], 'both');
		}
		if (!empty($data['keterangan'])) {
			$this->db->like('tc.keterangan', $data['keterangan'], 'both');
		}

		if (!empty($data['supplier_name'])) {
			$this->db->like('sp.supplier_name', $data['supplier_name'], 'both');
		}

		$this->db->select('tc.*');
		$this->db->select('tc.debt_trx_tgl as debt_trx_tgl_start');
		$this->db->select('tc.debt_trx_tgl as debt_trx_tgl_end');
		$this->db->select('sum(tc.nominal) as totalnominal');
		$this->db->select('hs.*');
		$this->db->from('tbl_trx_debt_tmp tc');
		$this->db->join('tbl_header_purchase hs', 'tc.purchaseno = hs.purchaseno', 'left');
		$this->db->join('tbl_supplier sp', 'hs.supplier_code = sp.supplier_code', 'left');
		$this->db->where('hs.post_status !=', '0');
		$this->db->where('hs.pay_status', 1);
		$query = $this->db->get();
		$result_data = $query->num_rows($query);
		return $result_data;
	}

	function getGiro_excel($data, $data_op)
	{
		if (!empty($data['debt_trx_tgl_start'])) {
			$arr = explode("/", $data['debt_trx_tgl_start']);
			$debt_trx_tgl_start = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('tc.debt_trx_tgl >=', $debt_trx_tgl_start);
		}
		if (!empty($data['debt_trx_tgl_end'])) {
			$arr = explode("/", $data['debt_trx_tgl_end']);
			$debt_trx_tgl_end = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('tc.debt_trx_tgl <=', $debt_trx_tgl_end);
		}
		if (!empty($data['no_kuitansi'])) {
			$this->db->like('tc.no_kuitansi', $data['no_kuitansi'], 'both');
		}
		if (!empty($data['no_pembayaran'])) {
			$this->db->like('tc.no_pembayaran', $data['no_pembayaran'], 'both');
		}
		if (!empty($data['cara_bayar'])) {
			$this->db->like('tc.cara_bayar', $data['cara_bayar'], 'both');
		}
		if (!empty($data['keterangan'])) {
			$this->db->like('tc.keterangan', $data['keterangan'], 'both');
		}

		$this->db->select('tc.*');
		$this->db->select('sp.*');
		$this->db->select('sum(tc.nominal) as totalnominal');
		$this->db->select('hs.*');
		$this->db->from('tbl_trx_debt_tmp tc');
		$this->db->join('tbl_header_purchase hs', 'tc.purchaseno = hs.purchaseno', 'left');
		$this->db->join('tbl_supplier sp', 'hs.supplier_code = sp.supplier_code', 'left');
		$this->db->where('hs.post_status !=', '0');
		$this->db->where('hs.pay_status', 1);
		$this->db->order_by('tc.debt_trx_tgl', 'ASC');
		$this->db->group_by('tc.no_kuitansi');
		$query = $this->db->get();

		return $query->result();
	}

	function getDetailPembayaran($no_kuitansi)
	{
		$sub_query_from = "(SELECT sum(nominal) as jlh_pembayaran_hutang,purchaseno FROM tbl_trx_debt ";
		$sub_query_from .= "WHERE (debt_trx_type = 4 or debt_trx_type = 5) and status = 1 GROUP BY purchaseno )";

		$this->db->select('hs.*');
		$this->db->select('c.*');
		$this->db->select('tc.nominal');


		$this->db->select('db.jlh_pembayaran_hutang');
		$this->db->select('IF(db.jlh_pembayaran_hutang>0,(hs.totalpurchase - db.jlh_pembayaran_hutang),hs.totalpurchase) as saldo', false);
		$this->db->from('tbl_trx_debt_tmp tc');
		$this->db->join("tbl_header_purchase hs", 'tc.purchaseno = hs.purchaseno', 'left');
		$this->db->join("$sub_query_from db", 'hs.purchaseno = db.purchaseno', 'left');
		$this->db->join('tbl_supplier c', 'c.supplier_code = hs.supplier_code');

		$this->db->where('tc.no_kuitansi =', $no_kuitansi);
		$this->db->where('hs.post_status !=', '0');
		$this->db->where('hs.pay_status', 1);
		$this->db->where('hs.active_status', 1);

		$this->db->order_by('hs.purchaseid', 'ASC');
		$query = $this->db->get();

		return $query->result();
	}

	function getDetailPembayaran_page($no_kuitansi)
	{
		$sub_query_from = "(SELECT sum(nominal) as jlh_pembayaran_hutang,purchaseno FROM tbl_trx_debt ";
		$sub_query_from .= "WHERE (debt_trx_type = 4 or debt_trx_type = 5) and status = 1 GROUP BY purchaseno )";

		$this->db->select('hs.*');
		$this->db->select('c.*');
		$this->db->select('tc.nominal');


		$this->db->select('db.jlh_pembayaran_hutang');
		$this->db->select('IF(db.jlh_pembayaran_hutang>0,(hs.totalpurchase - db.jlh_pembayaran_hutang),hs.totalpurchase) as saldo', false);
		$this->db->from('tbl_trx_debt_tmp tc');
		$this->db->join("tbl_header_purchase hs", 'tc.purchaseno = hs.purchaseno', 'left');
		$this->db->join("$sub_query_from db", 'hs.purchaseno = db.purchaseno', 'left');
		$this->db->join('tbl_supplier c', 'c.supplier_code = hs.supplier_code');

		$this->db->where('tc.no_kuitansi =', $no_kuitansi);
		$this->db->where('hs.post_status !=', '0');
		$this->db->where('hs.pay_status', 1);
		$this->db->where('hs.active_status', 1);

		$query = $this->db->get();
		$result_data = $query->num_rows($query);
		return $result_data;
	}

	function delete_pembayaran_tmp($no_kuitansi)
	{
		$this->db->delete('tbl_trx_debt_tmp', array('no_kuitansi' => $no_kuitansi));
		return true;
	}

	function save_confirm_pembayaran_piutang($no_kuitansi, $idpengguna)
	{
		$sql = "INSERT INTO tbl_trx_debt (purchaseno,debt_trx_tgl,debt_trx_type,nominal,status,cara_bayar,no_kuitansi,no_pembayaran,keterangan,created_by,dt_created) ";
		$sql .= "SELECT purchaseno,debt_trx_tgl,debt_trx_type,nominal,status,cara_bayar,no_kuitansi,no_pembayaran,keterangan,created_by,dt_created FROM tbl_trx_debt_tmp ";
		$sql .= "WHERE no_kuitansi = '$no_kuitansi' ";
		$query = $this->db->query("$sql");

		$this->db->delete('tbl_trx_debt_tmp', array('no_kuitansi' => $no_kuitansi));

		$stat = $this->update_status_lunas($no_kuitansi, $idpengguna);
		if ($stat) {
			return true;
		} else {
			return false;
		}
	}

	function update_status_lunas($no_kuitansi, $idpengguna)
	{
		$sql = "SELECT * FROM tbl_trx_debt where no_kuitansi =  '$no_kuitansi' ";
		$query = $this->db->query("$sql");

		foreach ($query->result() as $row) {
			$sql2 = "SELECT sum(nominal) as jlh_pembayaran_hutang FROM tbl_trx_debt ";
			$sql2 .= "WHERE (debt_trx_type = 4 or debt_trx_type = 5) and purchaseno ='$row->purchaseno' GROUP BY purchaseno ";
			$query2 = $this->db->query("$sql2");
			$row2 = $query2->row();

			$sql1 = "SELECT totalpurchase FROM tbl_header_purchase ";
			$sql1 .= "WHERE purchaseno ='$row->purchaseno' ";
			$query1 = $this->db->query("$sql1");
			$row1 = $query1->row();

			if ($row2->jlh_pembayaran_hutang >= $row1->totalpurchase) {
				$data = array(
					'pay_status' => 2,
					'updated_by' => $idpengguna,
					'dt_updated' => date("Y-m-d H-i-s"),
				);
				$this->db->where('purchaseno', $row->purchaseno);
				$this->db->update('tbl_header_purchase', $data);
			}
		}

		return true;
	}

	function lapPembayaran_hutang($offset, $rows, $data, $data_op, $tgl_awal, $tgl_akhir, $supplier_code)
	{
		if (!empty($data['tglpurchase'])) {
			$arr = explode("/", $data['tglpurchase']);
			$tglpurchase = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('hp.tglpurchase', $tglpurchase);
		}
		if (!empty($data['jto'])) {
			$arr = explode("/", $data['jto']);
			$jto = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('hp.jto', $jto);
		}

		if (!empty($tgl_awal)) {
			//$arr = explode("/",$tgl_awal);
			//$tgl_awal_search = $arr[2]."-".$arr[0]."-".$arr[1];
			$this->db->where('td.debt_trx_tgl >=', $tgl_awal);
		}

		if (!empty($tgl_akhir)) {
			//$arr = explode("/",$tgl_akhir);
			//$tgl_akhir_search = $arr[2]."-".$arr[0]."-".$arr[1];
			$this->db->where('td.debt_trx_tgl <=', $tgl_akhir);
		}

		if (!empty($data['debt_trx_tgl'])) {
			$arr = explode("/", $data['debt_trx_tgl']);
			$debt_trx_tgl = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('td.debt_trx_tgl', $debt_trx_tgl);
		}
		if (!empty($data['purchaseno'])) {
			$this->db->like('hp.purchaseno', $data['purchaseno'], 'both');
		}
		if (!empty($data['soID'])) {
			$this->db->like('hp.soID', $data['soID'], 'both');
		}
		if (!empty($data['supplier_code'])) {
			$this->db->like('hp.supplier_code', $data['supplier_code'], 'both');
		}
		if (!empty($data['supplier_name'])) {
			$this->db->like('s.supplier_name', $data['supplier_name'], 'both');
		}
		if (!empty($data['description'])) {
			$this->db->like('hp.description', $data['description'], 'both');
		}
		if (!empty($data['payrange'])) {
			$this->db->where('hp.payrange', $data['payrange']);
		}
		if (!empty($data['active_status'])) {
			$this->db->where('hp.active_status', $data['active_status']);
		}
		if (!empty($data['paymethod'])) {
			$this->db->where('hp.paymethod', $data['paymethod']);
		}
		if (!empty($data['pay_status'])) {
			$this->db->where('hp.pay_status', $data['pay_status']);
		}
		if (!empty($data['cara_bayar'])) {
			$this->db->like('td.cara_bayar', $data['cara_bayar'], 'both');
		}
		/*if(!empty($data['print_status'])){
			$this->db->where('hp.print_status', $data['print_status']);
		}*/
		if (!empty($data['totalpurchase'])) {
			if ($data_op['totalpurchase'] == 'equal') {
				$this->db->where('hp.totalpurchase', $data['totalpurchase']);
			} else if ($data_op['totalpurchase'] == 'less') {
				$this->db->where('hp.totalpurchase <=', $data['totalpurchase']);
			} else if ($data_op['totalpurchase'] == 'greater') {
				$this->db->where('hp.totalpurchase >=', $data['totalpurchase']);
			}
		}
		if (!empty($data['nominal'])) {
			if ($data_op['nominal'] == 'equal') {
				$this->db->where('td.nominal', $data['nominal']);
			} else if ($data_op['nominal'] == 'less') {
				$this->db->where('td.nominal <=', $data['nominal']);
			} else if ($data_op['nominal'] == 'greater') {
				$this->db->where('td.nominal >=', $data['nominal']);
			}
		}

		if (!empty($supplier_code)) {
			$this->db->where('hp.supplier_code', $supplier_code);
		}

		$sub_query_from = "(SELECT nominal as saldo_awal,purchaseno FROM tbl_trx_debt ";
		$sub_query_from .= "WHERE debt_trx_type = 1 and status = 1 )";

		$this->db->select('db.saldo_awal');
		$this->db->select('td.*');
		$this->db->select('hp.*');
		$this->db->select('s.*');

		$this->db->select('pm.description as paymethod_str');
		$this->db->select('ps.description as pay_status_str');
		$this->db->select('pa.description as active_status_str');
		$this->db->from('tbl_trx_debt td');
		$this->db->join("$sub_query_from db", 'td.purchaseno = db.purchaseno', 'left');
		$this->db->join('tbl_header_purchase hp', 'td.purchaseno = hp.purchaseno');
		$this->db->join('tbl_supplier s', 's.supplier_code = hp.supplier_code');
		$this->db->join('tbl_parameter pm', 'hp.paymethod = pm.id');
		$this->db->join('tbl_parameter ps', 'hp.pay_status = ps.id');
		$this->db->join('tbl_parameter pa', 'hp.active_status = pa.id');
		$this->db->where('td.debt_trx_tgl >=', $tgl_awal);
		$this->db->where('td.debt_trx_tgl <=', $tgl_akhir);
		$this->db->where('hp.post_status !=', '0');
		$this->db->where('pm.name', 'pay_method_sale');
		$this->db->where('ps.name', 'pay_status');
		$this->db->where('pa.name', 'active_status');
		$this->db->where('td.debt_trx_type', 4);
		//$this->db->order_by('td.debt_trx_tgl', 'DESC');
		$this->db->order_by('td.debt_trxid', 'ASC');
		$this->db->limit($rows, $offset);
		$query = $this->db->get();
		$dataSet = $query->result();
		$this->db->trans_start();
		foreach ($dataSet as $row) {
			$query = "SELECT sum(nominal) as jlh_pembayaran_hutang FROM tbl_trx_debt ";
			$query .= "WHERE (debt_trx_type = 4 or debt_trx_type = 5) and status = 1 ";
			//$query .= "AND debt_trx_tgl <= '$row->debt_trx_tgl' ";
			$query .= "AND debt_trxid < '$row->debt_trxid' ";
			$query .= "AND purchaseno = '$row->purchaseno' ";
			$query .= " GROUP BY purchaseno ";
			$query_sub = $this->db->query("$query");
			$row_sub = $query_sub->row();

			if (isset($row_sub)) {
				$row->saldo_awal = $row->saldo_awal - $row_sub->jlh_pembayaran_hutang;
			}
			$row->saldo_akhir = $row->saldo_awal - $row->nominal;
		}
		$this->db->trans_complete();
		return $dataSet;
	}

	function lapPembayaran_hutang_page($data, $data_op, $tgl_awal, $tgl_akhir, $supplier_code)
	{
		if (!empty($data['tglpurchase'])) {
			$arr = explode("/", $data['tglpurchase']);
			$tglpurchase = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('hp.tglpurchase', $tglpurchase);
		}
		if (!empty($data['jto'])) {
			$arr = explode("/", $data['jto']);
			$jto = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('hp.jto', $jto);
		}

		if (!empty($tgl_awal)) {
			//$arr = explode("/",$tgl_awal);
			//$tgl_awal_search = $arr[2]."-".$arr[0]."-".$arr[1];
			$this->db->where('td.debt_trx_tgl >=', $tgl_awal);
		}

		if (!empty($tgl_akhir)) {
			//$arr = explode("/",$tgl_akhir);
			//$tgl_akhir_search = $arr[2]."-".$arr[0]."-".$arr[1];
			$this->db->where('td.debt_trx_tgl <=', $tgl_akhir);
		}

		if (!empty($data['debt_trx_tgl'])) {
			$arr = explode("/", $data['debt_trx_tgl']);
			$debt_trx_tgl = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('td.debt_trx_tgl', $debt_trx_tgl);
		}
		if (!empty($data['purchaseno'])) {
			$this->db->like('hp.purchaseno', $data['purchaseno'], 'both');
		}
		if (!empty($data['soID'])) {
			$this->db->like('hp.soID', $data['soID'], 'both');
		}
		if (!empty($data['supplier_code'])) {
			$this->db->like('hp.supplier_code', $data['supplier_code'], 'both');
		}
		if (!empty($data['supplier_name'])) {
			$this->db->like('s.supplier_name', $data['supplier_name'], 'both');
		}
		if (!empty($data['description'])) {
			$this->db->like('hp.description', $data['description'], 'both');
		}
		if (!empty($data['payrange'])) {
			$this->db->where('hp.payrange', $data['payrange']);
		}
		if (!empty($data['active_status'])) {
			$this->db->where('hp.active_status', $data['active_status']);
		}
		if (!empty($data['paymethod'])) {
			$this->db->where('hp.paymethod', $data['paymethod']);
		}
		if (!empty($data['pay_status'])) {
			$this->db->where('hp.pay_status', $data['pay_status']);
		}
		/*if(!empty($data['print_status'])){
			$this->db->where('hp.print_status', $data['print_status']);
		}*/
		if (!empty($data['totalpurchase'])) {
			if ($data_op['totalpurchase'] == 'equal') {
				$this->db->where('hp.totalpurchase', $data['totalpurchase']);
			} else if ($data_op['totalpurchase'] == 'less') {
				$this->db->where('hp.totalpurchase <=', $data['totalpurchase']);
			} else if ($data_op['totalpurchase'] == 'greater') {
				$this->db->where('hp.totalpurchase >=', $data['totalpurchase']);
			}
		}
		if (!empty($data['nominal'])) {
			if ($data_op['nominal'] == 'equal') {
				$this->db->where('td.nominal', $data['nominal']);
			} else if ($data_op['nominal'] == 'less') {
				$this->db->where('td.nominal <=', $data['nominal']);
			} else if ($data_op['nominal'] == 'greater') {
				$this->db->where('td.nominal >=', $data['nominal']);
			}
		}

		if (!empty($supplier_code)) {
			$this->db->where('hp.supplier_code', $supplier_code);
		}

		$this->db->select('td.debt_trxid');
		$this->db->from('tbl_trx_debt td');
		$this->db->join('tbl_header_purchase hp', 'td.purchaseno = hp.purchaseno');
		$this->db->join('tbl_supplier s', 's.supplier_code = hp.supplier_code');
		$this->db->where('td.debt_trx_type', 4);
		$this->db->where('td.debt_trx_tgl >=', $tgl_awal);
		$this->db->where('td.debt_trx_tgl <=', $tgl_akhir);
		$query = $this->db->get();
		$result_data = $query->num_rows($query);
		return $result_data;
	}

	function lapPembayaran_hutang_excel($tgl_awal, $tgl_akhir, $supplier_code)
	{
		if (!empty($supplier_code)) {
			$this->db->where('hp.supplier_code', $supplier_code);
		}

		$sub_query_from = "(SELECT nominal as saldo_awal,purchaseno FROM tbl_trx_debt ";
		$sub_query_from .= "WHERE debt_trx_type = 1 and status = 1 )";

		$this->db->select('db.saldo_awal');
		$this->db->select('td.*');
		$this->db->select('hp.*');
		$this->db->select('s.*');

		$this->db->select('pm.description as paymethod_str');
		$this->db->select('ps.description as pay_status_str');
		$this->db->select('pa.description as active_status_str');
		$this->db->from('tbl_trx_debt td');
		$this->db->join("$sub_query_from db", 'td.purchaseno = db.purchaseno', 'left');
		$this->db->join('tbl_header_purchase hp', 'td.purchaseno = hp.purchaseno');
		$this->db->join('tbl_supplier s', 's.supplier_code = hp.supplier_code');
		$this->db->join('tbl_parameter pm', 'hp.paymethod = pm.id');
		$this->db->join('tbl_parameter ps', 'hp.pay_status = ps.id');
		$this->db->join('tbl_parameter pa', 'hp.active_status = pa.id');
		$this->db->where('td.debt_trx_tgl >=', $tgl_awal);
		$this->db->where('td.debt_trx_tgl <=', $tgl_akhir);
		$this->db->where('hp.post_status !=', '0');
		$this->db->where('pm.name', 'pay_method_sale');
		$this->db->where('ps.name', 'pay_status');
		$this->db->where('pa.name', 'active_status');
		$this->db->where('td.debt_trx_type', 4);
		//$this->db->order_by('td.debt_trx_tgl', 'DESC');
		$this->db->order_by('td.debt_trxid', 'ASC');

		$query = $this->db->get();
		$dataSet = $query->result();
		$this->db->trans_start();
		foreach ($dataSet as $row) {
			$query = "SELECT sum(nominal) as jlh_pembayaran_hutang FROM tbl_trx_debt ";
			$query .= "WHERE (debt_trx_type = 4 or debt_trx_type = 5) and status = 1 ";
			//$query .= "AND debt_trx_tgl <= '$row->debt_trx_tgl' ";
			$query .= "AND debt_trxid < '$row->debt_trxid' ";
			$query .= "AND purchaseno = '$row->purchaseno' ";
			$query .= " GROUP BY purchaseno ";
			$query_sub = $this->db->query("$query");
			$row_sub = $query_sub->row();

			if (isset($row_sub)) {
				$row->saldo_awal = $row->saldo_awal - $row_sub->jlh_pembayaran_hutang;
			}
			$row->saldo_akhir = $row->saldo_awal - $row->nominal;
		}
		$this->db->trans_complete();
		return $dataSet;
	}


	function getPembayaran_noteap($offset, $rows, $data, $data_op)
	{
		if (!empty($data['tglpurchase'])) {
			$arr = explode("/", $data['tglpurchase']);
			$tglpurchase = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('hp.tglpurchase', $tglpurchase);
		}
		if (!empty($data['jto'])) {
			$arr = explode("/", $data['jto']);
			$jto = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('hp.jto', $jto);
		}
		if (!empty($data['debt_trx_tgl'])) {
			$arr = explode("/", $data['debt_trx_tgl']);
			$debt_trx_tgl = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('td.debt_trx_tgl', $debt_trx_tgl);
		}
		if (!empty($data['purchaseno'])) {
			$this->db->like('hp.purchaseno', $data['purchaseno'], 'both');
		}
		if (!empty($data['soID'])) {
			$this->db->like('hp.soID', $data['soID'], 'both');
		}
		if (!empty($data['supplier_code'])) {
			$this->db->like('hp.supplier_code', $data['supplier_code'], 'both');
		}
		if (!empty($data['supplier_name'])) {
			$this->db->like('s.supplier_name', $data['supplier_name'], 'both');
		}
		if (!empty($data['description'])) {
			$this->db->like('hp.description', $data['description'], 'both');
		}
		if (!empty($data['payrange'])) {
			$this->db->where('hp.payrange', $data['payrange']);
		}
		if (!empty($data['active_status'])) {
			$this->db->where('hp.active_status', $data['active_status']);
		}
		if (!empty($data['paymethod'])) {
			$this->db->where('hp.paymethod', $data['paymethod']);
		}
		if (!empty($data['pay_status'])) {
			$this->db->where('hp.pay_status', $data['pay_status']);
		}
		/*if(!empty($data['print_status'])){
			$this->db->where('hp.print_status', $data['print_status']);
		}*/
		if (!empty($data['totalpurchase'])) {
			if ($data_op['totalpurchase'] == 'equal') {
				$this->db->where('hp.totalpurchase', $data['totalpurchase']);
			} else if ($data_op['totalpurchase'] == 'less') {
				$this->db->where('hp.totalpurchase <=', $data['totalpurchase']);
			} else if ($data_op['totalpurchase'] == 'greater') {
				$this->db->where('hp.totalpurchase >=', $data['totalpurchase']);
			}
		}
		if (!empty($data['nominal'])) {
			if ($data_op['nominal'] == 'equal') {
				$this->db->where('td.nominal', $data['nominal']);
			} else if ($data_op['nominal'] == 'less') {
				$this->db->where('td.nominal <=', $data['nominal']);
			} else if ($data_op['nominal'] == 'greater') {
				$this->db->where('td.nominal >=', $data['nominal']);
			}
		}

		//$sub_query_from = "(SELECT sum(nominal) as jlh_pembayaran_hutang,purchaseno FROM tbl_trx_debt ";
		//$sub_query_from .= "WHERE (debt_trx_type = 4 or debt_trx_type = 5) and status = 1 GROUP BY purchaseno )";

		$this->db->select('td.*');
		$this->db->select('hp.*');
		$this->db->select('s.*');
		$this->db->select('pm.description as paymethod_str');
		$this->db->select('ps.description as pay_status_str');
		$this->db->select('pa.description as active_status_str');
		$this->db->from('tbl_trx_debt td');
		$this->db->join('tbl_header_purchase hp', 'td.purchaseno = hp.purchaseno');
		$this->db->join('tbl_supplier s', 's.supplier_code = hp.supplier_code');
		$this->db->join('tbl_parameter pm', 'hp.paymethod = pm.id');
		$this->db->join('tbl_parameter ps', 'hp.pay_status = ps.id');
		$this->db->join('tbl_parameter pa', 'hp.active_status = pa.id');
		$this->db->where('hp.post_status !=', '0');
		$this->db->where('pm.name', 'pay_method_sale');
		$this->db->where('ps.name', 'pay_status');
		$this->db->where('pa.name', 'active_status');
		$this->db->where('td.debt_trx_type', 5);
		$this->db->order_by('td.debt_trx_tgl', 'ASC');
		$this->db->limit($rows, $offset);
		$query = $this->db->get();

		return $query->result();
	}

	function getPembayaran_noteap_page($data, $data_op)
	{
		if (!empty($data['tglpurchase'])) {
			$arr = explode("/", $data['tglpurchase']);
			$tglpurchase = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('hp.tglpurchase', $tglpurchase);
		}
		if (!empty($data['jto'])) {
			$arr = explode("/", $data['jto']);
			$jto = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('hp.jto', $jto);
		}
		if (!empty($data['debt_trx_tgl'])) {
			$arr = explode("/", $data['debt_trx_tgl']);
			$debt_trx_tgl = $arr[2] . "-" . $arr[0] . "-" . $arr[1];
			$this->db->where('td.debt_trx_tgl', $debt_trx_tgl);
		}
		if (!empty($data['purchaseno'])) {
			$this->db->like('hp.purchaseno', $data['purchaseno'], 'both');
		}
		if (!empty($data['soID'])) {
			$this->db->like('hp.soID', $data['soID'], 'both');
		}
		if (!empty($data['supplier_code'])) {
			$this->db->like('hp.supplier_code', $data['supplier_code'], 'both');
		}
		if (!empty($data['supplier_name'])) {
			$this->db->like('s.supplier_name', $data['supplier_name'], 'both');
		}
		if (!empty($data['description'])) {
			$this->db->like('hp.description', $data['description'], 'both');
		}
		if (!empty($data['payrange'])) {
			$this->db->where('hp.payrange', $data['payrange']);
		}
		if (!empty($data['active_status'])) {
			$this->db->where('hp.active_status', $data['active_status']);
		}
		if (!empty($data['paymethod'])) {
			$this->db->where('hp.paymethod', $data['paymethod']);
		}
		if (!empty($data['pay_status'])) {
			$this->db->where('hp.pay_status', $data['pay_status']);
		}
		/*if(!empty($data['print_status'])){
			$this->db->where('hp.print_status', $data['print_status']);
		}*/
		if (!empty($data['totalpurchase'])) {
			if ($data_op['totalpurchase'] == 'equal') {
				$this->db->where('hp.totalpurchase', $data['totalpurchase']);
			} else if ($data_op['totalpurchase'] == 'less') {
				$this->db->where('hp.totalpurchase <=', $data['totalpurchase']);
			} else if ($data_op['totalpurchase'] == 'greater') {
				$this->db->where('hp.totalpurchase >=', $data['totalpurchase']);
			}
		}
		if (!empty($data['nominal'])) {
			if ($data_op['nominal'] == 'equal') {
				$this->db->where('td.nominal', $data['nominal']);
			} else if ($data_op['nominal'] == 'less') {
				$this->db->where('td.nominal <=', $data['nominal']);
			} else if ($data_op['nominal'] == 'greater') {
				$this->db->where('td.nominal >=', $data['nominal']);
			}
		}

		$this->db->select('td.debt_trxid');
		$this->db->from('tbl_trx_debt td');
		$this->db->join('tbl_header_purchase hp', 'td.purchaseno = hp.purchaseno');
		$this->db->join('tbl_supplier s', 's.supplier_code = hp.supplier_code');
		$this->db->where('td.debt_trx_type', 5);
		$query = $this->db->get();
		$result_data = $query->num_rows($query);
		return $result_data;
	}

	function save_trx_pembayaran($data, $nominal, $idpengguna, $supplier_code)
	{
		$query = $this->db->insert('tbl_trx_debt_tmp', $data);
		return true;
	}

	function save_trx_pembayaran_tmp($data, $nominal, $idpengguna, $supplier_code)
	{
		$query = $this->db->insert('tbl_trx_debt_tmp', $data);
		return true;
	}

	/*function generateNo_kuitansi($tgl){
    	$arr   = explode("-",$tgl);
    	$tahun = $arr[0];
    	$bulan = $arr[1];
    	$sql_data = "select max(substr(no_kuitansi,11,4)) as no_kuitansi ";
    	$sql_data .= "from tbl_trx_debt where substr(no_kuitansi,3,4) = '$tahun' ";
    	$sql_data .= "and substr(no_kuitansi,8,2) = '$bulan' ";
    	$query = $this->db->query("$sql_data");
    	$row = $query->row();
		$no_kuitansi = $row->no_kuitansi;
		$no_kuitansi++;
		$no_kuitansi = 'B/'.$tahun.'/'.$bulan.'/'.str_pad($no_kuitansi,4,"0",STR_PAD_LEFT);
		return $no_kuitansi;
    } */

	function generateNo_kuitansi($tgl)
	{
		$arr   = explode("-", $tgl);
		$tahun = $arr[0];
		$bulan = $arr[1];
		$sql_data = "select max(substr(a.no_kuitansi,11,4)) as no_kuitansi from ";
		$sql_data .= "(select no_kuitansi from tbl_trx_debt where substr(no_kuitansi,3,4) = '$tahun' ";
		$sql_data .= "and substr(no_kuitansi,8,2) = '$bulan' ";
		$sql_data .= "union all ";
		$sql_data .= "select no_kuitansi from tbl_trx_debt_tmp where substr(no_kuitansi,3,4) = '$tahun' ";
		$sql_data .= "and substr(no_kuitansi,8,2) = '$bulan' ) a ";
		$query = $this->db->query("$sql_data");
		$row = $query->row();
		$no_kuitansi = $row->no_kuitansi;
		$no_kuitansi++;
		$no_kuitansi = 'B/' . $tahun . '/' . $bulan . '/' . str_pad($no_kuitansi, 4, "0", STR_PAD_LEFT);
		return $no_kuitansi;
	}
}
