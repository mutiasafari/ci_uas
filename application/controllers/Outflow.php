<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Outflow extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Outflow_model','outflow');
	}

	public function index()
	{
		$this->load->helper('url');
		$this->load->view('outflow_view');
	}

	public function ajax_list()
	{
		$this->load->helper('url');

		$list = $this->outflow->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $outflow) {
			$no++;
			$row = array();
			$row[] = $outflow->events_event_id;
			$row[] = $outflow->annotation;
			$row[] = $outflow->outflow_date;
			$row[] = $outflow->outflow_cash;
			if($outflow->photo)
				$row[] = '<a href="'.base_url('upload/'.$outflow->photo).'" target="_blank"><img src="'.base_url('upload/'.$outflow->photo).'" class="img-responsive" /></a>';
			else
				$row[] = '(No photo)';

			//add html for action
			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_outflow('."'".$outflow->outflow_id."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_outflow('."'".$outflow->outflow_id."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->outflow->count_all(),
						"recordsFiltered" => $this->outflow->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->outflow->get_by_id($id);
		$data->outflow_date = ($data->outflow_date == '0000-00-00') ? '' : $data->outflow_date; // if 0000-00-00 set tu empty for datepicker compatibility
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate();
		
		$data = array(
				'events_event_id' => $this->input->post('events_event_id'),
				'annotation' => $this->input->post('annotation'),
				'outflow_date' => $this->input->post('outflow_date'),
				'outflow_cash' => $this->input->post('outflow_cash'),
				
			);

		if(!empty($_FILES['photo']['name']))
		{
			$upload = $this->_do_upload();
			$data['photo'] = $upload;
		}

		$insert = $this->outflow->save($data);

		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate();
		$data = array(
				'events_event_id' => $this->input->post('events_event_id'),
				'annotation' => $this->input->post('annotation'),
				'outflow_date' => $this->input->post('outflow_date'),
				'outflow_cash' => $this->input->post('outflow_cash'),
			);

		if($this->input->post('remove_photo')) // if remove photo checked
		{
			if(file_exists('upload/'.$this->input->post('remove_photo')) && $this->input->post('remove_photo'))
				unlink('upload/'.$this->input->post('remove_photo'));
			$data['photo'] = '';
		}

		if(!empty($_FILES['photo']['name']))
		{
			$upload = $this->_do_upload();
			
			//delete file
			$outflow = $this->outflow->get_by_id($this->input->post('outflow_id'));
			if(file_exists('upload/'.$outflow->photo) && $outflow->photo)
				unlink('upload/'.$outflow->photo);

			$data['photo'] = $upload;
		}

		$this->outflow->update(array('outflow_id' => $this->input->post('outflow_id')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id)
	{
		//delete file
		$outflow = $this->outflow->get_by_id($id);
		if(file_exists('upload/'.$outflow->photo) && $outflow->photo)
			unlink('upload/'.$outflow->photo);
		
		$this->outflow->delete_by_id($id);
		echo json_encode(array("status" => TRUE));
	}

	private function _do_upload()
	{
		$config['upload_path']          = 'upload/';
        $config['allowed_types']        = 'gif|jpg|png';
        //$config['max_size']             = 100; //set max size allowed in Kilobyte
        $config['max_width']            = 1000; // set max width image allowed
        $config['max_height']           = 1000; // set max height allowed
        $config['file_name']            = round(microtime(true) * 1000); //just milisecond timestamp fot unique name

        $this->load->library('upload', $config);

        if(!$this->upload->do_upload('photo')) //upload and validate
        {
            $data['inputerror'][] = 'photo';
			$data['error_string'][] = 'Upload error: '.$this->upload->display_errors('',''); //show ajax error
			$data['status'] = FALSE;
			echo json_encode($data);
			exit();
		}
		return $this->upload->data('file_name');
	}

	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('events_event_id') == '')
		{
			$data['inputerror'][] = 'events_event_id';
			$data['error_string'][] = 'events_event_id is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('annotation') == '')
		{
			$data['inputerror'][] = 'annotation';
			$data['error_string'][] = 'annotation is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('outflow_date') == '')
		{
			$data['inputerror'][] = 'outflow_date';
			$data['error_string'][] = 'Date of Birth is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('outflow_cash') == '')
		{
			$data['inputerror'][] = 'outflow_cash';
			$data['error_string'][] = 'Please select gender';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}
}

/* End of file Outflow.php */
/* Location: ./application/controllers/Outflow.php */
