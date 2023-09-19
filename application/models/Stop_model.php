<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Stop_model extends CI_Model {
   public function get_all_stops() {
		$this->db->select('a.*');
		$this->db->from('stop a');
		
		if(isset($_GET['stopRouteId']))
			$this->db->where('stopRouteId',$_GET['stopRouteId']);
		
	if (isset($_GET['search']) and $_GET['search'] != null) {
		
				$search  = urldecode($_GET['search']);
			$filters = [
				'stopName' => $search,
			];
			}
			
			if (isset($filters) && !empty($filters)) {
				$this->db->group_Start();
				$this->db->or_like($filters);
				$this->db->group_End();
			}
	
	
			$limit = (isset($_GET['limit']) && is_numeric($_GET['limit']) && !empty(trim($_GET['limit']))) ? $_GET['limit'] : 10;
			$offset = (isset($_GET['offset']) && is_numeric($_GET['offset']) && !empty(trim($_GET['offset']))) ? $_GET['offset'] : 0;

			if ($limit != null || $offset != null) {
				$this->db->limit($limit, $offset);
			}

		
		
		$query = $this->db->get();
		
		if($query->num_rows() != 0) {
			 $results = $query->result();
			 $count = $query->num_rows();
			 $data = array();
			 if(isset($_GET['populate']) && $_GET['populate']==true):
				foreach($results as $result) {
					$routeQuery = $this->db->get_where('route', array('id' => $result->stopRouteId));
					$routeInfo = $routeQuery->row();
					$result->stopRouteId = $routeInfo;
					$data['data'][] = $result;
				 }
				 $data['count']=$count;
				return $data;
			endif;
			$data['data']=$results;
			$data['count']=$count;
			return $data;
			
			
		} else {
			return FALSE;
		}
		
		
    }
    
    public function get_stop($stopId) {
	
	
		$this->db->select('*');
		$this->db->from('stop a');
		$this->db->where('a.id', $stopId);
		$query = $this->db->get();

		if ($query->num_rows() != 0) {
			$result = $query->row();
			$count = $query->num_rows();
			if(isset($_GET['populate']) && $_GET['populate']==true){
				$routeQuery = $this->db->get_where('route', array('id' => $result->stopRouteId));
					$routeInfo = $routeQuery->row();
					$result->stopRouteId = $routeInfo;
				$data['data'] = $result;
				 $data['count']=$count;
				return $data;
			}
			$data['data']=$result;
			$data['count']=$count;
			return $data;
		}
		return FALSE;
		
		
    }
    
    public function create_stop($data) {
        $this->load->library('form_validation');
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('stopRouteId', 'Stop Route ID', 'required|max_length[100]');
        $this->form_validation->set_rules('stopName', 'Stop Name', 'required|max_length[100]');
        $this->form_validation->set_rules('stopNumber', 'Stop Number', 'required|max_length[100]');
        $this->form_validation->set_rules('isStartingPoint', 'Is Starting Point ', 'required|max_length[100]');
        $this->form_validation->set_rules('isEndingPoint', 'Is Ending Point ', 'required|max_length[100]');
        
		 if ($this->form_validation->run() == false)  return FALSE; 
		 $insert['id'] = generate_uuid();
		 $insert['createdBy'] = getCreatedBy();
		 $insert['stopRouteId'] = $data['stopRouteId'];
		 $insert['stopName'] = $data['stopName'];
		 $insert['stopNumber'] = $data['stopNumber'];
		 $insert['isStartingPoint'] = $data['isStartingPoint'];
		 $insert['isEndingPoint'] = $data['isEndingPoint'];

        $this->db->insert('stop', $insert);
		if($this->db->affected_rows() > 0)return $this->get_stop($insert['id']);
		else return FALSE;
		
		
       
    }
    
    public function update_stop($stopId,$data) {
        $this->load->library('form_validation');
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('stopRouteId', 'Stop Route ID', 'required|max_length[50]');
        $this->form_validation->set_rules('stopName', 'Stop Name', 'required|max_length[100]');
        $this->form_validation->set_rules('stopNumber', 'Stop Number', 'required|max_length[100]');
        $this->form_validation->set_rules('isStartingPoint', 'Is Starting Point ', 'required|max_length[100]');
        $this->form_validation->set_rules('isEndingPoint', 'Is Ending Point ', 'required|max_length[100]');
        
		
		 if ($this->form_validation->run() == false)  return FALSE; // Validation failed
		 $update['stopRouteId'] = $data['stopRouteId'];
		 $update['stopName'] = $data['stopName'];
		 $update['stopNumber'] = $data['stopNumber'];
		 $update['isStartingPoint'] = $data['isStartingPoint'];
		 $update['isEndingPoint'] = $data['isEndingPoint'];
        $this->db->where('id', $stopId);
        $this->db->update('stop', $update);
		if($this->db->affected_rows() > 0)return $this->get_stop($stopId);
		else return FALSE;
		
    }
    
    public function delete_stop($stopId) {
        $this->db->where('id', $stopId);
        $this->db->delete('stop');
		if($this->db->affected_rows() > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
    }
}
?>