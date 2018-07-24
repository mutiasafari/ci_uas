<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inflow extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Inflow_model','inflow');
	}

	public function index()
	{
		$this->load->helper('url');
		$this->load->view('inflow_view');
	}

	public function ajax_list()
	{
		$this->load->helper('url');

		$list = $this->inflow->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $inflow) {
			$no++;
			$row = array();
			$row[] = $inflow->members_member_id;
			$row[] = $inflow->annotation;
			$row[] = $inflow->inflow_date;
			$row[] = $inflow->inflow_cash;
			if($inflow->photo)
				$row[] = '<a href="'.base_url('upload/'.$inflow->photo).'" target="_blank"><img src="'.base_url('upload/'.$inflow->photo).'" class="img-responsive" /></a>';
			else
				$row[] = '(No photo)';

			//add html for action
			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_inflow('."'".$inflow->inflow_id."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_inflow('."'".$inflow->inflow_id."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->inflow->count_all(),
						"recordsFiltered" => $this->inflow->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->inflow->get_by_id($id);
		$data->inflow_date = ($data->inflow_date == '0000-00-00') ? '' : $data->inflow_date; // if 0000-00-00 set tu empty for datepicker compatibility
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate();
		
		$data = array(
				'members_member_id' => $this->input->post('members_member_id'),
				'annotation' => $this->input->post('annotation'),
				'inflow_date' => $this->input->post('inflow_date'),
				'inflow_cash' => $this->input->post('inflow_cash'),
				
			);

		if(!empty($_FILES['photo']['name']))
		{
			$upload = $this->_do_upload();
			$data['photo'] = $upload;
		}

		$insert = $this->inflow->save($data);

		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate();
		$data = array(
				'members_member_id' => $this->input->post('members_member_id'),
				'annotation' => $this->input->post('annotation'),
				'inflow_date' => $this->input->post('inflow_date'),
				'inflow_cash' => $this->input->post('inflow_cash'),
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
			$inflow = $this->inflow->get_by_id($this->input->post('inflow_id'));
			if(file_exists('upload/'.$inflow->photo) && $inflow->photo)
				unlink('upload/'.$inflow->photo);

			$data['photo'] = $upload;
		}

		$this->inflow->update(array('inflow_id' => $this->input->post('inflow_id')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id)
	{
		//delete file
		$inflow = $this->inflow->get_by_id($id);
		if(file_exists('upload/'.$inflow->photo) && $inflow->photo)
			unlink('upload/'.$inflow->photo);
		
		$this->inflow->delete_by_id($id);
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

		if($this->input->post('members_member_id') == '')
		{
			$data['inputerror'][] = 'members_member_id';
			$data['error_string'][] = 'Member ID is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('annotation') == '')
		{
			$data['inputerror'][] = 'annotation';
			$data['error_string'][] = 'Annotation is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('inflow_date') == '')
		{
			$data['inputerror'][] = 'inflow_date';
			$data['error_string'][] = 'Inflow Date is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('inflow_cash') == '')
		{
			$data['inputerror'][] = 'inflow_cash';
			$data['error_string'][] = 'Inflow Cash is required';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

}
