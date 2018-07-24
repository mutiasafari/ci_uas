<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chart_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function pemasukan()
	{
		$sql = "SELECT sum(inflow_cash) as inflow FROM inflow";
		$result = $this->db->query($sql);
		return $result->row()->inflow;
	}

	public function pengeluaran()
	{
		$sql = "SELECT sum(outflow_cash) as outflow FROM outflow";
		$result = $this->db->query($sql);
		return $result->row()->outflow;
	}

	public function member()
	{
		$sql = "SELECT COUNT(member_id) as member from member";
		$result = $this->db->query($sql);
		return $result->row()->member;
	}


}

/* End of file Chart_model.php */
/* Location: ./application/models/Chart_model.php */