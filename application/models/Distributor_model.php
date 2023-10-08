<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Distributor_model extends CI_Model
{
	public function get_all_distributors()
	{

		$conditionsForCount = array();
		$conditions = array();
		$search = '';
		$num_results = 0;


		if (isset($_GET['search']) and $_GET['search'] != null) {

			$search  = urldecode($_GET['search']);
		}

		if (isset($_GET['cityId'])) {
			$conditions['distributorCity'] = $_GET['cityId'];
		}

		if (isset($_GET['status'])) {
			$conditions['distributorActive'] = $_GET['status'];
		}
		if (isset($_GET['routeId'])) {
			$conditions['distributorRoute'] = $_GET['routeId'];
		}


		$countQueryBuilder = clone $this->db;

		$countQueryBuilder->select('COUNT(*) as count')
			->join('city', 'city.id = a.distributorCity', 'left')
			->join('route', 'route.id = a.distributorRoute', 'left')
			->group_start()
			->or_like('a.distributorCompanyName', $search)
			->or_like('a.distributorName', $search)
			->or_like('a.distributorContactNumber', $search)
			->or_like('a.distributorArea', $search)
			->or_like('a.distributorEmail', $search)
			->group_end();

		$conditionsForCount = $conditions;
		$countResult = $countQueryBuilder->from('distributor a')->where($conditionsForCount)->get()->row();
		$num_results = $countResult->count;

		$this->db->group_start()
			->select('a.*, city.cityName,route.routeName')
			->join('city', 'city.id = a.distributorCity', 'left')
			->join('route', 'route.id = a.distributorRoute', 'left')
			->or_like('a.distributorCompanyName', $search)
			->or_like('a.distributorName', $search)
			->or_like('a.distributorContactNumber', $search)
			->or_like('a.distributorArea', $search)
			->or_like('a.distributorEmail', $search)
			->group_end();


		// $this->db->select('a.*');
		// $this->db->from('distributor a');

		$sortField = 'a.created_date';
		$orderBy = 'DESC';
		if (isset($_GET['orderBy'])) {
			if ($_GET['orderBy'] === 'lastPurchase' || $_GET['orderBy'] === '-lastPurchase') {
				$sortField = 'a.lastPurchase';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'weight' || $_GET['orderBy'] === '-weight') {
				$sortField = 'a.totalWeight';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'order' || $_GET['orderBy'] === '-order') {
				$sortField = 'a.totalOrders';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'points' || $_GET['orderBy'] === '-points') {
				$sortField = 'a.totalPoints';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'target' || $_GET['orderBy'] === '-target') {
				$sortField = 'a.distributorTarget';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'status' || $_GET['orderBy'] === '-status') {
				$sortField = 'a.distributorActive';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'name' || $_GET['orderBy'] === '-name') {
				$sortField = 'a.distributorCompanyName';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'phone' || $_GET['orderBy'] === '-phone') {
				$sortField = 'a.distributorContactNumber';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			}
		}

		$limit = (isset($_GET['limit']) && is_numeric($_GET['limit']) && !empty(trim($_GET['limit']))) ? $_GET['limit'] : 10;
		$offset = (isset($_GET['offset']) && is_numeric($_GET['offset']) && !empty(trim($_GET['offset']))) ? $_GET['offset'] : 0;

		$query = $this->db
			->from('distributor a')
			->where($conditions)
			->limit($limit, $offset)
			->order_by($sortField, $orderBy)
			->get();

		if ($query->num_rows() != 0) {
			$results['data'] = $query->result();

			$data = array();
			if (isset($_GET['populate']) && $_GET['populate'] == true) :

				foreach ($results['data'] as $result) {
					$cityQuery = $this->db->get_where('city', array('id' => $result->distributorCity));
					$cityInfo = $cityQuery->row();
					$result->distributorCity = $cityInfo;
					$routeQuery = $this->db->get_where('route', array('id' => $result->distributorRoute));
					$routeInfo = $routeQuery->row();
					$result->distributorRoute = $routeInfo;
					unset($result->password);
					$data['data'][] = $result;
				}
				$data['count'] = $num_results;
				return $data;

			endif;

			return $results;
		} else return false;
	}

	public function get_distributor($distributorId)
	{
		$this->db->select('a.*');
		$this->db->from('distributor a');
		$this->db->where('a.id', $distributorId);
		$query = $this->db->get();
		if ($query->num_rows() != 0) {
			$result = $query->row();
			$count = $query->num_rows();
			$data = array();
			if (isset($_GET['populate']) && $_GET['populate'] == true) :
				$cityQuery = $this->db->get_where('city', array('id' => $result->distributorCity));
				$cityInfo = $cityQuery->row();
				$result->distributorCity = $cityInfo;
				$routeQuery = $this->db->get_where('route', array('id' => $result->distributorRoute));
				$routeInfo = $routeQuery->row();
				$result->distributorRoute = $routeInfo;
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

	public function create_distributor($data)
	{
		$this->load->library('bcrypt');
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('distributorCompanyName', 'Distributor Company Name', 'required|max_length[50]');
		$this->form_validation->set_rules('distributorEmail', 'Distributor Email', 'max_length[200]|valid_email|is_unique[distributor.distributorEmail]');
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
		$insert = [
			'id' => generate_uuid(),
			'createdBy' => getCreatedBy(),
			'distributorCompanyName' => $data['distributorCompanyName'],
			'distributorName' => $data['distributorName'],
			'distributorEmail' => $data['distributorEmail'] ?? '',
			'distributorLat' => $data['distributorLat'],
			'distributorLong' => $data['distributorLong'],
			'distributorContactNumber' => $data['distributorContactNumber'],
			'distributorActive' => $data['distributorActive'],
			'distributorCity' => $data['distributorCity'],
			'distributorRoute' => $data['distributorRoute'],
			'password' => $data['password'],

		];

		$insert['distributorImage'] = $data['distributorImage'];
		//if(isset($data['distributorImage'])){
		//$imagedata = explode(';base64,', $data['distributorImage']);
		//$insert['distributorImage']=uploadImage($imagedata,'distributor',$insert['id']);
		//}
		$this->db->insert('distributor', $insert);
		$this->Email_model->send_email_reset_password($insert['id'], 'distributor');
		return $this->get_distributor($insert['id']);
	}



	public function update_distributor($distributorId, $data)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('distributorCompanyName', 'Distributor Company Name', 'required|max_length[50]');
		$this->form_validation->set_rules('distributorEmail', 'Distributor Email', 'max_length[200]|valid_email');
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
		echo $data['distributorLat'];
		echo $data['distributorLong'];
		$update = [
			'distributorCompanyName' => $data['distributorCompanyName'],
			'distributorName' => $data['distributorName'],
			'distributorEmail' => $data['distributorEmail'] ?? '',
			'distributorLat' => $data['distributorLat'],
			'distributorLong' => $data['distributorLong'],
			'distributorContactNumber' => $data['distributorContactNumber'],
			'distributorActive' => $data['distributorActive'],
			'distributorCity' => $data['distributorCity'],
			'distributorArea' => $data['distributorArea'],
			'distributorRoute' => $data['distributorRoute']
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

	public function delete_distributor($distributorId)
	{
		$distributor  = $this->get_distributor($distributorId);
		if ($distributor) {
			if ($this->db->get_where('stock', array('stockDistributorId' => $distributorId))->row()) return FALSE;
			if ($this->db->get_where('orders ', array('orderDistributorId' => $distributorId))->row()) return FALSE;
			if (isset($distributor->distributorImage)) :
				if ($distributor->distributorImage != null)
					unlink('uploads/distributor/' . $distributor->distributorImage);
			endif;
			$this->db->where('id', $distributorId);
			$this->db->delete('distributor');

			if ($this->db->affected_rows() > 0) return true;
			else return FALSE;
		} else return FALSE;
	}
}
