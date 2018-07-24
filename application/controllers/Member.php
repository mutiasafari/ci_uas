<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Member_model','member');
	}

	public function index()
	{
		$this->load->helper('url');
		$this->load->view('member_view');
	}

	public function ajax_list()
	{
		$this->load->helper('url');

		$list = $this->member->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $member) {
			$no++;
			$row = array();
			$row[] = $member->member_name;
			$row[] = $member->member_address;
			$row[] = $member->member_gender;
			$row[] = $member->member_phone;
			if($member->photo)
				$row[] = '<a href="'.base_url('upload/'.$member->photo).'" target="_blank"><img src="'.base_url('upload/'.$member->photo).'" class="img-responsive" /></a>';
			else
				$row[] = '(No photo)';

			//add html for action
			$row[] = '<a class="btn btn-sm btn-primary" href="#" title="Edit" onclick="edit_member('."'".$member->member_id."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_member('."'".$member->member_id."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->member->count_all(),
						"recordsFiltered" => $this->member->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->member->get_by_id($id);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate();
		
		$data = array(
				'member_name' => $this->input->post('member_name'),
				'member_address' => $this->input->post('member_address'),
				'member_gender' => $this->input->post('member_gender'),
				'member_phone' => $this->input->post('member_phone'),
				
			);

		if(!empty($_FILES['photo']['name']))
		{
			$upload = $this->_do_upload();
			$data['photo'] = $upload;
		}

		$insert = $this->member->save($data);

		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate();
		$data = array(
				'member_name' => $this->input->post('member_name'),
				'member_address' => $this->input->post('member_address'),
				'member_gender' => $this->input->post('member_gender'),
				'member_phone' => $this->input->post('member_phone'),
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
			$member = $this->member->get_by_id($this->input->post('member_id'));
			if(file_exists('upload/'.$member->photo) && $member->photo)
				unlink('upload/'.$member->photo);

			$data['photo'] = $upload;
		}

		$this->member->update(array('member_id' => $this->input->post('member_id')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id)
	{
		//delete file
		$member = $this->member->get_by_id($id);
		if(file_exists('upload/'.$member->photo) && $member->photo)
			unlink('upload/'.$member->photo);
		
		$this->member->delete_by_id($id);
		echo json_encode(array("status" => TRUE));
	}

	private function _do_upload()
	{
		$config['upload_path']          = 'upload/';
        $config['allowed_types']        = 'gif|jpg|png';
        $config['max_size']             = 100; //set max size allowed in Kilobyte
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

		if($this->input->post('member_phone') == '')
		{
			$data['inputerror'][] = 'member_phone';
			$data['error_string'][] = 'member_phone is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('member_name') == '')
		{
			$data['inputerror'][] = 'member_name';
			$data['error_string'][] = 'member_name is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('member_address') == '')
		{
			$data['inputerror'][] = 'member_address';
			$data['error_string'][] = 'member_address is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('member_gender') == '')
		{
			$data['inputerror'][] = 'member_gender';
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
