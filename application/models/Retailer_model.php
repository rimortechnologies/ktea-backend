<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Retailer_model extends CI_Model
{
	public function get_all_retailers()
	{
		$conditionsForCount = array();
		$conditions = array();
		$search = '';
		$num_results = 0;

		if (isset($_GET['search']) and $_GET['search'] != null) {

			$search  = urldecode($_GET['search']);
		}

		if (isset($_GET['cityId'])) {
			$conditions['retailerCity'] = $_GET['cityId'];
		}

		if (isset($_GET['status'])) {
			$conditions['retailerActive'] = $_GET['status'];
		}


		$countQueryBuilder = clone $this->db;

		if (isset($_GET['routeId'])) {
			$routeIds = explode(',', $_GET['routeId']);
			$countQueryBuilder->where_in('a.retailerRoute', $routeIds);
		}

		// Use where method separately for each condition
		foreach ($conditionsForCount as $field => $value) {
			if ($field === 'retailerRoute' && is_array($value)) {
				$countQueryBuilder->where_in($field, $value);
			} else {
				$countQueryBuilder->where($field, $value);
			}
		}

		$countQueryBuilder->select('COUNT(*) as count')
			->join('city', 'city.id = a.retailerCity', 'left')
			->join('route', 'route.id = a.retailerRoute', 'left')
			->group_start()
			->or_like('a.retailerShopName', $search)
			->or_like('a.retailerName', $search)
			->or_like('a.retailerContactNumber', $search)
			->or_like('a.retailerArea', $search)
			->or_like('a.retailerGst', $search)
			->or_like('a.retailerAddress', $search)
			->or_like('a.retailerEmail', $search)
			->group_end();

		$conditionsForCount = $conditions;
		$countResult = $countQueryBuilder->from('retailer a')->where($conditionsForCount)->get()->row();
		$num_results = $countResult->count;

		$this->db->group_start()
			->select('a.id,a.retailerShopName,a.retailerName,a.retailerGst,a.retailerShopImage,a.retailerLat,a.retailerLong,a.retailerContactNumber,a.retailerActive,a.retailerApproved,a.retailerArea,a.retailerAddress,a.retailerCity,a.retailerRoute,retailerEmail,a.createdBy,a.created_date,a.updated_date,a.retailerImage,a.isAdminAdded, city.cityName,route.routeName')
			->join('city', 'city.id = a.retailerCity', 'left')
			->join('route', 'route.id = a.retailerRoute', 'left')
			->or_like('a.retailerShopName', $search)
			->or_like('a.retailerName', $search)
			->or_like('a.retailerGst', $search)
			->or_like('a.retailerAddress', $search)
			->or_like('a.retailerContactNumber', $search)
			->or_like('a.retailerArea', $search)
			->or_like('a.retailerEmail', $search)
			->group_end();

		if (isset($_GET['routeId'])) {
			$routeIds = explode(',', $_GET['routeId']);
			$this->db->where_in('a.retailerRoute', $routeIds);
		}

		// Use where method separately for each condition in the main query
		foreach ($conditions as $field => $value) {
			if ($field === 'retailerRoute' && is_array($value)) {
				$this->db->where_in($field, $value);
			} else {
				$this->db->where($field, $value);
			}
		}



		$sortField = 'a.created_date';
		$orderBy = 'DESC';
		if (isset($_GET['orderBy'])) {
			if ($_GET['orderBy'] === 'name' || $_GET['orderBy'] === '-name') {
				$sortField = 'a.retailerShopName';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'city' || $_GET['orderBy'] === '-city') {
				$sortField = 'city.cityName';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'status' || $_GET['orderBy'] === '-status') {
				$sortField = 'a.retailerActive';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'phone' || $_GET['orderBy'] === '-phone') {
				$sortField = 'a.retailerContactNumber';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			}
		}


		$limit = (isset($_GET['limit']) && is_numeric($_GET['limit']) && !empty(trim($_GET['limit']))) ? $_GET['limit'] : 10;
		$offset = (isset($_GET['offset']) && is_numeric($_GET['offset']) && !empty(trim($_GET['offset']))) ? $_GET['offset'] : 0;

		$query = $this->db
			->from('retailer a')
			->where($conditions)
			->limit($limit, $offset)
			->order_by($sortField, $orderBy)
			->get();


		if ($query->num_rows() != 0) {
			$results['data'] = $query->result();
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
				$data['count'] = $num_results;
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
		$this->form_validation->set_rules('retailerContactNumber', 'Retailer Contact Number', 'required|numeric|max_length[10]|min_length[10]');
		$this->form_validation->set_rules('retailerActive', 'Retailer Active ', 'max_length[100]');
		$this->form_validation->set_rules('retailerApproved', 'Retailer Approved ', 'max_length[100]');
		$this->form_validation->set_rules('retailerArea', 'Retailer Area ', 'required|max_length[100]');
		// $this->form_validation->set_rules('retailerCity', 'Retailer City ', 'required|max_length[100]');
		$this->form_validation->set_rules('retailerRoute', 'Retailer Route ', 'required|max_length[100]');



		if ($this->form_validation->run() == false) {
			return false;
		}

		// $this->db->trans_start();

		try {

			// Fetch routeCity from route table based on retailerRoute ID
			$routeId = $data['retailerRoute'];
			$routeCity = $this->db->select('routeCity')->get_where('route', ['id' => $routeId])->row('routeCity');

			if (!$routeCity) {
				// Handle the case where routeCity is not found
				return false;
			}

			$insert['retailerCity'] = $routeCity;  // Set retailerCity based on routeCity

			$insert['id'] = generate_uuid();
			$insert['createdBy'] = getCreatedBy();
			$insert['retailerShopName'] = $data['retailerShopName'];
			$insert['retailerEmail'] = $data['retailerEmail'] ?? '';
			$insert['retailerName'] = $data['retailerName'];
			$insert['retailerLat'] = $data['retailerLat'] ?? null;
			$insert['retailerLong'] = $data['retailerLong'] ?? null;
			$insert['retailerContactNumber'] = $data['retailerContactNumber'];
			$insert['retailerActive'] = 1;
			$insert['retailerApproved'] = $data['retailerApproved'] ?? 0;
			$insert['isAdminAdded'] = $data['isAdminAdded'] ?? 0;
			$insert['retailerArea'] = $data['retailerArea'];
			// $insert['retailerCity'] = $data['retailerCity'];
			$insert['retailerGst'] = $data['retailerGst'] ?? '';
			$insert['retailerAddress'] = $data['retailerAddress'] ?? '';
			$insert['retailerTempId'] = $data['retailerTempId'] ?? '';
			$insert['retailerRoute'] = $data['retailerRoute'];

			if (isset($data['timestamp']) && !empty($data['timestamp'])) {
				// If timestamp is set, use it
				$createdDate = date('Y-m-d H:i:s', $data['timestamp']);
			} else {
				// If timestamp is not set, use the current time
				$createdDate = date('Y-m-d H:i:s');
			}

			$insert['created_date'] = $createdDate;

			if (isset($data['retailerImage'])) {
				$insert['retailerImage'] = $data['retailerImage'];
			}


			$insert['password'] = $this->bcrypt->hash_password(getPassword());
			$this->db->insert('retailer', $insert);
			return $this->get_retailer($insert['id']);
		} catch (Exception $e) {
			// An error occurred, rollback the transaction
			print_r($e);
			// $this->db->trans_rollback();
			return false;
		}
	}

	public function update_retailer($retailerId, $data)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('retailerShopName', 'Retailer Shop Name', 'required|max_length[100]');
		$this->form_validation->set_rules('retailerName', 'Retailer Name', 'required|max_length[100]');
		$this->form_validation->set_rules('retailerEmail', 'Retailer Email', 'max_length[200]|valid_email');
		// $this->form_validation->set_rules('retailerLat', 'Retailer Lat', 'required|max_length[100]');
		// $this->form_validation->set_rules('retailerLong', 'Retailer Long', 'required|max_length[100]');
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
		$update['retailerLat'] = $data['retailerLat'] ?? null;
		$update['retailerLong'] = $data['retailerLong'] ?? null;
		$update['retailerContactNumber'] = $data['retailerContactNumber'];
		$update['retailerActive'] = $data['retailerActive'];
		$update['retailerApproved'] = $data['retailerApproved'] ?? 0;
		$update['isAdminAdded'] = $data['isAdminAdded'] ?? 0;
		$update['retailerApproved'] = $update['isAdminAdded'] == 0 ? 0 : 1;
		$update['retailerArea'] = $data['retailerArea'];
		$update['retailerCity'] = $data['retailerCity'];
		$update['retailerGst'] = $data['retailerGst'];
		$update['retailerAddress'] = $data['retailerAddress'];
		$update['retailerRoute'] = $data['retailerRoute'];
		//if(isset($data['retailerShopImage'])){
		//$imagedata = explode(';base64,', $data['retailerShopImage']);
		//$update['retailerShopImage']=uploadImage($imagedata,'retailer',$retailerId);
		//}
		//if(isset($data['retailerImage'])){
		//	$imagedata = explode(';base64,', $data['retailerImage']);
		//$update['retailerImage']=uploadImage($imagedata,'retailer',$retailerId);
		//}
		if (isset($data['retailerImage'])) {
			$update['retailerImage'] = $data['retailerImage'];
		}
		// $update['retailerShopImage'] = $data['retailerShopImage'];
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
