<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Distributor_model extends CI_Model {
    public function get_all_distributors() {
		$this->db->select('a.*');
		$this->db->from('distributor a');
		if(isset($_GET['cityId']))
			$this->db->where('distributorCity',$_GET['cityId']);
		if(isset($_GET['routeId']))
			$this->db->where('distributorRoute',$_GET['routeId']);
		if(isset($_GET['status']))
			$this->db->where('distributorActive',$_GET['status']);
		
		
		if (isset($_GET['search']) and $_GET['search'] != null) {
				$search  = urldecode($_GET['search']);
			$filters = [
				'distributorCompanyName' => $search,
				'distributorName' => $search,
				'distributorContactNumber' => $search,
				'distributorArea' => $search,
				'distributorEmail'=>$search,
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
		if($query->num_rows() != 0){
			$results['data'] = $query->result();
			$results['count'] = $query->num_rows();
			 
			 $data = array();
			 if(isset($_GET['populate']) && $_GET['populate']==true ):
				
				foreach($results['data'] as $result) {
					$cityQuery = $this->db->get_where('city', array('id' => $result->distributorCity));
					$cityInfo = $cityQuery->row();
					$result->distributorCity = $cityInfo;
					$routeQuery = $this->db->get_where('route', array('id' => $result->distributorRoute));
					$routeInfo = $routeQuery->row();
					$result->distributorRoute = $routeInfo;
					unset($result->password);
					$data['data'][] = $result;
				}
				$data['count']=$results['count'];
				return $data;
			
			endif;
			
			return $results;
		}
		else return false;
    }
    
    public function get_distributor($distributorId) {
		$this->db->select('a.*');
		$this->db->from('distributor a');
		$this->db->where('a.id',$distributorId);         
		$query = $this->db->get(); 
		if($query->num_rows() != 0){
			$result = $query->row();
			$count = $query->num_rows();
			 $data = array();
			 if(isset($_GET['populate']) && $_GET['populate']==true ):
				$cityQuery = $this->db->get_where('city', array('id' => $result->distributorCity));
				$cityInfo = $cityQuery->row();
				$result->distributorCity = $cityInfo;
				$routeQuery = $this->db->get_where('route', array('id' => $result->distributorRoute));
				$routeInfo = $routeQuery->row();
				$result->distributorRoute = $routeInfo;
				unset($result->password);
				$data['data'] = $result;
				$data['count']=$count;
				return $data;
			endif;
			$data['data'] = $result;
			$data['count']=$count;
			return $data;
		}
		else return false;
    }
    
    public function create_distributor($data) {
		$this->load->library('bcrypt');
        $this->load->library('form_validation');
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('distributorCompanyName', 'Distributor Company Name' , 'required|max_length[50]');
		$this->form_validation->set_rules('distributorEmail', 'Distributor Email' , 'max_length[200]|valid_email|is_unique[distributor.distributorEmail]');
        $this->form_validation->set_rules('distributorName', 'Distributor Name', 'required|max_length[100]');
        $this->form_validation->set_rules('distributorLat', 'Distributor Lat', 'required|max_length[100]');
        $this->form_validation->set_rules('distributorLong', 'Distributor Long', 'required|max_length[100]');
        $this->form_validation->set_rules('distributorContactNumber', 'Distributor Contact Number', 'required|numeric|max_length[10]|min_length[10]');
        $this->form_validation->set_rules('distributorActive', 'Distributor Active', 'required|max_length[100]');
        $this->form_validation->set_rules('distributorCity', 'Distributor City', 'required|max_length[100]');
        $this->form_validation->set_rules('distributorRoute', 'Distributor Route', 'required|max_length[100]');
        
        if ($this->form_validation->run() == false) {
            return false; // Validation failed
        }
		$data['id'] = generate_uuid();
		
		$data['password'] = $this->bcrypt->hash_password(getPassword());
		$data['createdBy'] = getCreatedBy();
		$insert=[
			'id'=> generate_uuid(),
			'createdBy'=>getCreatedBy(),
			'distributorCompanyName'=> $data['distributorCompanyName'],
			'distributorName'=> $data['distributorName'],
			'distributorEmail'=> $data['distributorEmail']??'',
			'distributorLat'=> $data['distributorLat'],
			'distributorLong'=> $data['distributorLong'],
			'distributorContactNumber'=> $data['distributorContactNumber'],
			'distributorActive'=> $data['distributorActive'],
			'distributorCity'=> $data['distributorCity'],
			'distributorRoute'=> $data['distributorRoute'],
			'password'=> $data['password'],
			
		];
		
		$insert['distributorImage'] = $data['distributorImage'];
		//if(isset($data['distributorImage'])){
			//$imagedata = explode(';base64,', $data['distributorImage']);
			//$insert['distributorImage']=uploadImage($imagedata,'distributor',$insert['id']);
		//}
        $this->db->insert('distributor', $insert);
        return $this->get_distributor($insert['id']);
    }
	
	
		
    public function update_distributor($distributorId,$data) {
        $this->load->library('form_validation');
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('distributorCompanyName', 'Distributor Company Name' , 'required|max_length[50]');
		$this->form_validation->set_rules('distributorEmail', 'Distributor Email' , 'max_length[200]|valid_email');
        $this->form_validation->set_rules('distributorName', 'Distributor Name', 'required|max_length[100]');
        $this->form_validation->set_rules('distributorLat', 'Distributor Lat', 'required|max_length[100]');
        $this->form_validation->set_rules('distributorLong', 'Distributor Long', 'required|max_length[100]');
        $this->form_validation->set_rules('distributorContactNumber', 'Distributor Contact Number', 'required|numeric|max_length[10]|min_length[10]');
        $this->form_validation->set_rules('distributorActive', 'Distributor Active', 'required|max_length[100]');
        $this->form_validation->set_rules('distributorArea', 'Distributor Area', 'required|max_length[100]');
        $this->form_validation->set_rules('distributorCity', 'Distributor City', 'required|max_length[100]');
        $this->form_validation->set_rules('distributorRoute', 'Distributor Route', 'required|max_length[100]');
        
        if ($this->form_validation->run() == false) {
            return false; // Validation failed
        }
		
		$update=[
			'distributorCompanyName'=> $data['distributorCompanyName'],
			'distributorName'=> $data['distributorName'],
			'distributorEmail'=> $data['distributorEmail']??'',
			'distributorLat'=> $data['distributorLat'],
			'distributorLong'=> $data['distributorLong'],
			'distributorContactNumber'=> $data['distributorContactNumber'],
			'distributorActive'=> $data['distributorActive'],
			'distributorCity'=> $data['distributorCity'],
			'distributorArea'=> $data['distributorArea'],
			'distributorRoute'=> $data['distributorRoute']
		];
		$update['distributorImage'] = $data['distributorImage'];
		//if(isset($data['distributorImage'])){
			//$imagedata = explode(';base64,', $data['distributorImage']);
			//$update['distributorImage']=uploadImage($imagedata,'distributor',$distributorId);
		//}
        $this->db->where('id', $distributorId);
        $this->db->update('distributor', $update);
		return $this->get_distributor($distributorId);
    }
    
    public function delete_distributor($distributorId) {
		$distributor  = $this->get_distributor($distributorId);
		if($distributor){
		if($this->db->get_where('stock', array('stockDistributorId' => $distributorId))->row()) return FALSE;
		if($this->db->get_where('orders ', array('orderDistributorId' => $distributorId))->row()) return FALSE;
			if(isset($distributor->distributorImage)):
				if($distributor->distributorImage!=null)
					unlink('uploads/distributor/'.$distributor->distributorImage);
			endif;
            $this->db->where('id', $distributorId);
            $this->db->delete('distributor');
			
			if($this->db->affected_rows() > 0)return true;
			else return FALSE;
		}
		else return FALSE;
    }
}
?>