<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Retailer_model extends CI_Model
{
	public function get_all_retailers()
	{
		$this->db->select('a.*');
		$this->db->from('retailer a');
		if (isset($_GET['cityId']))
			$this->db->where('retailerCity', $_GET['cityId']);
		if (isset($_GET['routeId']))
			$this->db->where('retailerRoute', $_GET['routeId']);
		if (isset($_GET['status']))
			$this->db->where('retailerActive', $_GET['status']);



		if (isset($_GET['search']) and $_GET['search'] != null) {
			$search  = urldecode($_GET['search']);
			$filters = [
				'retailerShopName' => $search,
				'retailerName' => $search,
				'retailerContactNumber' => $search,
				'retailerEmail' => $search,

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
		if ($query->num_rows() != 0) {
			$results['data'] = $query->result();
			$results['count'] = $query->num_rows();
			if (isset($_GET['populate']) && $_GET['populate'] == true) :
				foreach ($results['data'] as $result) {
					$cityQuery = $this->db->get_where('city', array('id' => $result->retailerCity));
					$cityInfo = $cityQuery->row();
					$result->retailerCity = $cityInfo;
					$routeQuery = $this->db->get_where('route', array('id' => $result->retailerRoute));
					$routeInfo = $routeQuery->row();
					$result->retailerRoute = $routeInfo;
					unset($result->password);
					$data['data'][] = $result;
				}



				return $data;


			endif;


			return $results;
		} else return false;
	}

	public function get_retailer($retailerId)
	{
		$this->db->select('a.*');
		$this->db->from('retailer a');
		$this->db->where('a.id', $retailerId);
		$query = $this->db->get();
		if ($query->num_rows() != 0) {
			$result = $query->row();
			$count = $query->num_rows();
			$data = array();
			if (isset($_GET['populate']) && $_GET['populate'] == true) :
				$cityQuery = $this->db->get_where('city', array('id' => $result->retailerCity));
				$cityInfo = $cityQuery->row();
				$result->retailerCity = $cityInfo;
				$routeQuery = $this->db->get_where('route', array('id' => $result->retailerRoute));
				$routeInfo = $routeQuery->row();
				$result->retailerRoute = $routeInfo;
				unset($result->password);
				$data['data'] = $result;
				$data['count'] = $count;
				return $data;
			endif;
			$data['data'] = $result;
			$data['count'] = $count;
			return $data;
		} else return false;
	}

	public function create_retailer($data)
	{
		$this->load->library('bcrypt');
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('retailerShopName', 'Retailer Shop Name', 'required|max_length[100]');
		$this->form_validation->set_rules('retailerEmail', 'Retailer Email', 'max_length[200]|valid_email|is_unique[retailer.retailerEmail]');
		$this->form_validation->set_rules('retailerName', 'Retailer Name', 'required|max_length[100]');
		$this->form_validation->set_rules('retailerLat', 'Retailer Lat', 'required|max_length[100]');
		$this->form_validation->set_rules('retailerLong', 'Retailer Long', 'required|max_length[100]');
		$this->form_validation->set_rules('retailerContactNumber', 'Retailer Contact Number', 'required|numeric|max_length[10]|min_length[10]');
		$this->form_validation->set_rules('retailerActive', 'Retailer Active ', 'max_length[100]');
		$this->form_validation->set_rules('retailerApproved', 'Retailer Approved ', 'max_length[100]');
		$this->form_validation->set_rules('retailerArea', 'Retailer Area ', 'required|max_length[100]');
		$this->form_validation->set_rules('retailerCity', 'Retailer City ', 'required|max_length[100]');
		$this->form_validation->set_rules('retailerRoute', 'Retailer Route ', 'required|max_length[100]');

		if ($this->form_validation->run() == false) {
			return false; // Validation failed
		}
		$insert['id'] = generate_uuid();
		$insert['createdBy'] = getCreatedBy();
		$insert['retailerShopName'] = $data['retailerShopName'];
		$insert['retailerEmail'] = $data['retailerEmail'] ?? '';
		$insert['retailerName'] = $data['retailerName'];
		$insert['retailerLat'] = $data['retailerLat'];
		$insert['retailerLong'] = $data['retailerLong'];
		$insert['retailerContactNumber'] = $data['retailerContactNumber'];
		$insert['retailerActive'] = 1;
		$insert['retailerApproved'] = $data['retailerApproved'] ?? 0;
		$insert['isAdminAdded'] = $data['isAdminAdded'] ?? 0;
		$insert['retailerArea'] = $data['retailerArea'];
		$insert['retailerCity'] = $data['retailerCity'];
		$insert['retailerRoute'] = $data['retailerRoute'];
		//if(isset($data['retailerShopImage'])){
		//$imagedata = explode(';base64,', $data['retailerShopImage']);
		//$insert['retailerShopImage']=uploadImage($imagedata,'retailer',$insert['id']);
		//}
		//if(isset($data['retailerImage'])){
		//	$imagedata = explode(';base64,', $data['retailerImage']);
		//$update['retailerImage']=uploadImage($imagedata,'retailer',$insert['id']);
		//}

		$insert['retailerImage'] = $data['retailerImage'];
		// $insert['retailerShopImage'] = $data['retailerShopImage'];


		$insert['password'] = $this->bcrypt->hash_password(getPassword());
		$this->db->insert('retailer', $insert);
		return $this->get_retailer($insert['id']);
	}

	public function update_retailer($retailerId, $data)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('retailerShopName', 'Retailer Shop Name', 'required|max_length[100]');
		$this->form_validation->set_rules('retailerName', 'Retailer Name', 'required|max_length[100]');
		$this->form_validation->set_rules('retailerEmail', 'Retailer Email', 'max_length[200]|valid_email');
		$this->form_validation->set_rules('retailerLat', 'Retailer Lat', 'required|max_length[100]');
		$this->form_validation->set_rules('retailerLong', 'Retailer Long', 'required|max_length[100]');
		$this->form_validation->set_rules('retailerContactNumber', 'Retailer Contact Number', 'required|numeric|max_length[10]|min_length[10]');
		$this->form_validation->set_rules('retailerActive', 'Retailer Active ', 'required|max_length[100]');
		$this->form_validation->set_rules('retailerApproved', 'Retailer Approved ', 'max_length[100]');
		$this->form_validation->set_rules('retailerArea', 'Retailer Area ', 'required|max_length[100]');
		$this->form_validation->set_rules('retailerCity', 'Retailer City ', 'required|max_length[100]');
		$this->form_validation->set_rules('retailerRoute', 'Retailer Route ', 'required|max_length[100]');

		if ($this->form_validation->run() == false) {
			return false; // Validation failed16990901
		}
		$update['retailerShopName'] = $data['retailerShopName'];
		$update['retailerName'] = $data['retailerName'];
		$update['retailerEmail'] = $data['retailerEmail'] ?? '';
		$update['retailerLat'] = $data['retailerLat'];
		$update['retailerLong'] = $data['retailerLong'];
		$update['retailerContactNumber'] = $data['retailerContactNumber'];
		$update['retailerActive'] = $data['retailerActive'];
		$update['retailerApproved'] = $data['retailerApproved'] ?? 0;
		$update['isAdminAdded'] = $data['isAdminAdded'] ?? 0;
		$update['retailerApproved'] = $update['isAdminAdded'] == 0 ? 0 : 1;
		$update['retailerArea'] = $data['retailerArea'];
		$update['retailerCity'] = $data['retailerCity'];
		$update['retailerRoute'] = $data['retailerRoute'];
		//if(isset($data['retailerShopImage'])){
		//$imagedata = explode(';base64,', $data['retailerShopImage']);
		//$update['retailerShopImage']=uploadImage($imagedata,'retailer',$retailerId);
		//}
		//if(isset($data['retailerImage'])){
		//	$imagedata = explode(';base64,', $data['retailerImage']);
		//$update['retailerImage']=uploadImage($imagedata,'retailer',$retailerId);
		//}

		$update['retailerImage'] = $data['retailerImage'];
		$update['retailerShopImage'] = $data['retailerShopImage'];
		$this->db->where('id', $retailerId);
		$this->db->update('retailer', $update);
		return $this->get_retailer($retailerId);
	}

	public function delete_retailer($retailerId)
	{
		$retailer  = $this->get_retailer($retailerId);
		if ($retailer) {
			if ($this->db->get_where('orders ', array('orderRetailerId' => $retailerId))->row()) return FALSE;
			if (isset($retailer->retailerImage)) :
				if ($retailer->retailerImage != null)
					unlink('uploads/retailer/' . $retailer->retailerImage);
			endif;
			$this->db->where('id', $retailerId);
			$this->db->delete('retailer');

			if ($this->db->affected_rows() > 0) return true;
			else return FALSE;
		} else return FALSE;
	}
}
