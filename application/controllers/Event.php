<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Event extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Event_model','event');
	}

	public function index()
	{
		$this->load->helper('url');
		$x['data'] = $this->event->get_member();
		$this->load->view('event_view',$x);
	}

	public function ajax_list()
	{
		$this->load->helper('url');

		// $list = $this->event->get_datatables();
		$list = $this->event->join_table();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $event) {
			$no++;
			$row = array();
			$row[] = $event->event_name;
			$row[] = $event->member_name;
			$row[] = $event->event_start;
			$row[] = $event->event_stop;

			//add html for action
			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_event('."'".$event->event_id."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_event('."'".$event->event_id."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->event->count_all(),
						"recordsFiltered" => $this->event->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->event->get_by_id($id);
		$data->event_start = ($data->event_start == '0000-00-00') ? '' : $data->event_start; // if 0000-00-00 set tu empty for datepicker compatibility
		$data->event_stop = ($data->event_stop == '0000-00-00') ? '' : $data->event_stop;
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate();
		
		$data = array(
				'event_name' => $this->input->post('event_name'),
				'members_member_id' => $this->input->post('members_member_id'),
				'event_start' => $this->input->post('event_start'),
				'event_stop' => $this->input->post('event_stop'),
				
			);


		$insert = $this->event->save($data);

		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate();
		$data = array(
				'event_name' => $this->input->post('event_name'),
				'members_member_id' => $this->input->post('members_member_id'),
				'event_start' => $this->input->post('event_start'),
				'event_stop' => $this->input->post('event_stop'),
			);

		$this->event->update(array('event_id' => $this->input->post('event_id')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id)
	{
		//delete file
		$event = $this->event->get_by_id($id);
		$this->event->delete_by_id($id);
		echo json_encode(array("status" => TRUE));
	}


	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('event_name') == '')
		{
			$data['inputerror'][] = 'event_name';
			$data['error_string'][] = 'event_name is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('members_member_id') == '')
		{
			$data['inputerror'][] = 'members_member_id';
			$data['error_string'][] = 'members_member_id is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('event_start') == '')
		{
			$data['inputerror'][] = 'event_start';
			$data['error_string'][] = 'Start Date is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('event_stop') == '')
		{
			$data['inputerror'][] = 'event_stop';
			$data['error_string'][] = 'Stop Date is required';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

}

/* End of file Event.php */
/* Location: ./application/controllers/Event.php */
