<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Event_model extends CI_Model {

	var $table = 'event';
	var $column_order = array('event_name','members_member_id','event_start','event_stop',null); //set column field database for datatable orderable
	var $column_search = array('event_name','members_member_id'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('event_id' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		
		$this->db->from($this->table);

		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('event_id',$id);
		$query = $this->db->get();

		return $query->row();
	}

	public function save($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('event_id', $id);
		$this->db->delete($this->table);
	}

	public function get_member()
	{
		$hasil = $this->db->query("SELECT * FROM member");
		return $hasil;
	}

	public function get_member_fk($id)
	{
		$hasil = $this->db->query("SELECT * FROM event WHERE members_member_id ='$id'");
		return $hasil->result();
	}

	public function join_table()
	{
		$this->db->select('*');
		$this->db->from('event');
		$this->db->join('member', 'member.member_id = event.members_member_id');
		$query = $this->db->get();
		return $query->result();
	}
}

/* End of file Event_model.php */
/* Location: ./application/models/Event_model.php */
