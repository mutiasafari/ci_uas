<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chart extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Chart_model');
	}

	public function index()
	{

		$data['pemasukan'] = $this->Chart_model->pemasukan();
		$data['pengeluaran'] = $this->Chart_model->pengeluaran();
		$data['member'] = $this->Chart_model->member();


		$this->load->view('chart_view', $data);
	
	}



}

/* End of file Chart.php */
/* Location: ./application/controllers/Chart.php */