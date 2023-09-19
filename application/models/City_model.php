<?php

defined('BASEPATH') OR exit('No direct script access allowed');
class City_model extends CI_Model {
    
    public function get_all_cities() {
		
		if (isset($_GET['search']) and $_GET['search'] != null) {
				$search  = urldecode($_GET['search']);
			$filters = [
				'cityName' => $search,
			];
			}
			
			if (isset($filters) && !empty($filters)) {
				$this->db->group_Start();
				$this->db->or_like($filters);
				$this->db->group_End();
			}
			
			
			$limit = (isset($_GET['limit']) && is_numeric($_GET['limit']) && !empty(trim($_GET['limit']))) ? $_GET['limit'] : 10;
			$offset = (isset($_GET['offset']) && is_numeric($_GET['offset']) && !empty(trim($_GET['offset']))) ? $_GET['offset'] : 0;



        $query= $this->db->get('city');
		
		
		
			if ($limit != null || $offset != null) {
				$this->db->limit($limit, $offset);
			}
			
			
		$data['data']=$query->result();
		$data['count']=$query->num_rows();
		return $data;
			
    }
    
    public function get_city($cityId) {
		$query=  $this->db->get_where('city', array('id' =>$cityId));
		$data['data']=$query->row();
		$data['count']=$query->num_rows();
		return $data;
    }
    
    public function create_city($data) {
        $this->load->library('form_validation');
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('cityName', 'City Name', 'required|max_length[100]|is_unique[city.cityName]');
		$insert=[
			'id'=> generate_uuid(),
			'createdBy'=>getCreatedBy(),
			'cityName'=> $data['cityName']
		];
        if ($this->form_validation->run() == FALSE)  return FALSE; 
        $this->db->insert('city', $insert);
		return $this->get_city($insert['id']);
       
    }
	
	
	public function get_city_by_name($cityName)
    {
        $query = $this->db->get_where('city', array('cityName' => $cityName));
        return $query->row();
    }
    
    public function update_city($cityId,$data) {
        $this->load->library('form_validation');
        $this->form_validation->set_data($data);
		$this->form_validation->set_rules('cityName', 'City Name', 'required|max_length[100]');
		if ($this->form_validation->run() == FALSE)  return FALSE;
		$existingCity = $this->get_city($cityId);
		if ($existingCity && $existingCity['data']->cityName != $data['cityName']) {
			$this->form_validation->set_rules('cityName', 'City Name', 'required|max_length[100]|is_unique[city.cityName]');
			if ($this->form_validation->run() == FALSE)  return FALSE;
		} 
		$update=[
			'cityName'=> $data['cityName']
		];
        $this->db->where('id', $cityId);
        $this->db->update('city', $update);
        return $this->get_city($cityId);
		
    }
    
    public function delete_city($cityId) {
		$rows=0;
		$city  = $this->get_city($cityId);
		if($city){
		if($this->db->get_where('route', array('routeCity' => $cityId))->row()) return FALSE;
		if($this->db->get_where('salesrepresentative', array('salesrepresentativeCity' => $cityId))->row()) return FALSE;
		if($this->db->get_where('retailer', array('retailerCity' => $cityId))->row()) return FALSE;
		if($this->db->get_where('distributor', array('distributorCity' => $cityId))->row()) return FALSE;

            $this->db->where('id', $cityId);
            $this->db->delete('city');
			if($this->db->affected_rows() > 0)return true;
			else return FALSE;

    	
		}
		else return FALSE;
    }
}
?>