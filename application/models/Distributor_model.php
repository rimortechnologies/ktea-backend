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
			// Modify the condition to use the 'cityId' from the association table
			$conditions['association.cityId'] = $_GET['cityId'];
		}

		if (isset($_GET['status'])) {
			$conditions['distributorActive'] = $_GET['status'];
		}
		// if (isset($_GET['routeId'])) {
		// 	$routeIds = explode(',', $_GET['routeId']);
		// 	$conditions['association.routeId'] = $routeIds;
		// }
		if (isset($_GET['routeId'])) {
			$routeIds = explode(',', $_GET['routeId']);
			$routeIds = array_map('trim', $routeIds);
			$routeIds = array_filter($routeIds);

			if (!empty($routeIds)) {
				$this->db->where_in('association.routeId', $routeIds);
			}
		}


		$countQueryBuilder = clone $this->db;

		$countQueryBuilder->select('COUNT(DISTINCT a.id) as count,association.routeId', false)
			->join('association association', 'association.bearerId = a.id AND association.routeId IS NOT NULL', 'left')
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
			->select('a.*,association.cityId,association.routeId,GROUP_CONCAT(DISTINCT city.id) as cityIds, GROUP_CONCAT(DISTINCT route.id) as routeIds', false)
			->join('association', 'association.bearerId = a.id', 'left')
			->join('city', 'city.id = association.cityId', 'left')
			->join('route', 'route.id = association.routeId', 'left')
			->like('a.distributorCompanyName', $search)
			->group_end()
			->group_by('a.id');
		// Apply conditions for filtering by cityId or routeId
		if (isset($_GET['cityId'])) {
			$cityId = $_GET['cityId'];
			$this->db->where('association.cityId', $cityId);
		}

		// if (isset($_GET['routeId'])) {
		// 	// $this->db->where_in('association.routeId', $routeIds);
		// 	// $routeIds = explode(',', $_GET['routeId']);
		// 	print_r($routeIds);
		// 	$this->db->where_in('association.routeId', $routeIds);
		// }

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
			} else if ($_GET['orderBy'] === 'city' || $_GET['orderBy'] === '-city') {
				$sortField = 'city.cityName';
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
			$results = $query->result();

			$data = array();

			foreach ($results as $result) {
				// If 'populate' is true, fetch city and route information
				if (isset($_GET['populate']) && $_GET['populate'] == true) {
					$cityIds = explode(',', $result->cityIds);

					$cities = [];
					foreach ($cityIds as $cityId) {
						$cityQuery = $this->db->get_where('city', array('id' => $cityId));
						$cityInfo = $cityQuery->row();

						// Fetch routeIds for each city
						$routeIdsQuery = $this->db->select('association.routeId, route.routeName')  // Include the routeName from the route table
							->from('association')
							->join('route', 'route.id = association.routeId', 'left')  // Join the route table
							->where(['association.bearerId' => $result->id, 'association.cityId' => $cityId])
							->get();

						$routeIds = [];
						foreach ($routeIdsQuery->result() as $routeRow) {
							$routeIds[] = array(
								'routeId' => $routeRow->routeId,
								'routeName' => $routeRow->routeName  // Include the routeName in the result
							);
						}

						$cities[] = array(
							'cityInfo' => $cityInfo,
							'routeIds' => $routeIds
						);
					}

					$result->cities = $cities;
				} else {
					// Otherwise, just provide the city IDs and route IDs
					$result->cities = [];
					$cityIds = explode(',', $result->cityIds);

					foreach ($cityIds as $cityId) {
						// Fetch routeIds for each city
						$routeIdsQuery = $this->db->select('association.routeId')
							->from('association')
							->where(['bearerId' => $result->id, 'cityId' => $cityId])
							->get();

						$routeIds = [];
						foreach ($routeIdsQuery->result() as $routeRow) {
							$routeIds[] = $routeRow->routeId;
						}

						$result->cities[] = array(
							'cityId' => $cityId,
							'routeIds' => $routeIds
						);
					}
				}

				unset($result->cityIds);
				unset($result->routeIds);

				$data['data'][] = $result;
			}

			$data['count'] = $num_results;
			return $data;
		} else {
			return FALSE;
		}
	}

	// public function get_distributor($distributorId)
	// {
	// 	$this->db->select('a.*');
	// 	$this->db->from('distributor a');
	// 	$this->db->where('a.id', $distributorId);
	// 	$query = $this->db->get();
	// 	if ($query->num_rows() != 0) {
	// 		$result = $query->row();
	// 		$count = $query->num_rows();
	// 		$data = array();
	// 		if (isset($_GET['populate']) && $_GET['populate'] == true) :
	// 			$cityQuery = $this->db->get_where('city', array('id' => $result->distributorCity));
	// 			$cityInfo = $cityQuery->row();
	// 			$result->distributorCity = $cityInfo;
	// 			$routeQuery = $this->db->get_where('route', array('id' => $result->distributorRoute));
	// 			$routeInfo = $routeQuery->row();
	// 			$result->distributorRoute = $routeInfo;
	// 			unset($result->password);
	// 			$data['data'] = $result;
	// 			$data['count'] = $count;
	// 			return $data;
	// 		endif;
	// 		$data['data'] = $result;
	// 		$data['count'] = $count;
	// 		return $data;
	// 	} else return false;
	// }

	public function get_distributor($distributorId)
	{
		$this->db->select('a.*');
		$this->db->from('distributor a');
		$this->db->where('a.id', $distributorId);
		$query = $this->db->get();

		if ($query->num_rows() != 0) {
			$result = $query->row();
			$count = $query->num_rows();
			$data = [];

			if (isset($_GET['populate']) && $_GET['populate'] == true) {
				$cityQuery = $this->db->get_where('city', ['id' => $result->distributorCity]);
				$cityInfo = $cityQuery->row();
				$result->distributorCity = $cityInfo;

				// Fetch distributorRoutes from association table
				$routesQuery = $this->db->select('cityId, routeId')
					->from('association')
					->where(['role' => 'distributor', 'bearerId' => $distributorId])
					->get();

				$distributorRoutes = [];

				foreach ($routesQuery->result() as $row) {
					$cityId = $row->cityId;

					// Check if the cityId exists in the result array
					if (!isset($distributorRoutes[$cityId])) {
						$distributorRoutes[$cityId] = [
							'cityId' => $cityId,
							'cityInfo' => $this->db->get_where('city', ['id' => $cityId])->row(),
							'routes' => []
						];
					}

					// Add the routeId to the routes array
					$distributorRoutes[$cityId]['routes'][] = [
						'routeId' => $row->routeId,
						'routeInfo' => $this->db->get_where('route', ['id' => $row->routeId])->row(),
					];
				}

				// Convert the associative array to a simple array
				$result->distributorRoutes = array_values($distributorRoutes);

				unset($result->password);
				$data['data'] = $result;
				$data['count'] = $count;

				return $data;
			} else {
				$cityQuery = $this->db->get_where('city', ['id' => $result->distributorCity]);
				$cityInfo = $cityQuery->row();
				$result->distributorCity = $cityInfo;

				// Fetch distributorRoutes from association table
				$routesQuery = $this->db->select('cityId, routeId')
					->from('association')
					->where(['role' => 'distributor', 'bearerId' => $distributorId])
					->get();

				$distributorRoutes = [];

				foreach ($routesQuery->result() as $row) {
					$cityId = $row->cityId;

					// Check if the cityId exists in the result array
					if (!isset($distributorRoutes[$cityId])) {
						$distributorRoutes[$cityId] = [
							'cityId' => $cityId,
							'routes' => []
						];
					}

					// Add the routeId to the routes array
					$distributorRoutes[$cityId]['routes'][] = $row->routeId;
				}

				// Convert the associative array to a simple array
				$result->distributorRoutes = array_values($distributorRoutes);

				unset($result->password);
				$data['data'] = $result;
				$data['count'] = $count;

				return $data;
			}

			$data['data'] = $result;
			$data['count'] = $count;

			return $data;
		} else {
			return false;
		}
	}


	// public function create_distributor($data)
	// {
	// 	$this->load->library('bcrypt');
	// 	$this->load->library('form_validation');
	// 	$this->form_validation->set_data($data);
	// 	$this->form_validation->set_rules('distributorCompanyName', 'Distributor Company Name', 'required|max_length[50]');
	// 	$this->form_validation->set_rules('distributorEmail', 'Distributor Email', 'max_length[200]|valid_email|is_unique[distributor.distributorEmail]');
	// 	$this->form_validation->set_rules('distributorName', 'Distributor Name', 'required|max_length[100]');
	// 	$this->form_validation->set_rules('distributorContactNumber', 'Distributor Contact Number', 'required|numeric|max_length[10]|min_length[10]');
	// 	$this->form_validation->set_rules('distributorActive', 'Distributor Active', 'required|max_length[100]');
	// 	$this->form_validation->set_rules('distributorCity', 'Distributor City', 'required|max_length[100]');
	// 	$this->form_validation->set_rules('distributorRoute', 'Distributor Route', 'required|max_length[100]');

	// 	if ($this->form_validation->run() == false) {
	// 		return false; // Validation failed
	// 	}
	// 	$data['id'] = generate_uuid();

	// 	$data['password'] = $this->bcrypt->hash_password(getPassword());
	// 	$data['createdBy'] = getCreatedBy();
	// 	$insert = [
	// 		'id' => generate_uuid(),
	// 		'createdBy' => getCreatedBy(),
	// 		'distributorCompanyName' => $data['distributorCompanyName'],
	// 		'distributorName' => $data['distributorName'],
	// 		'distributorEmail' => $data['distributorEmail'] ?? '',
	// 		'distributorLat' => $data['distributorLat'] ?? null,
	// 		'distributorLong' => $data['distributorLong'] ?? null,
	// 		'distributorContactNumber' => $data['distributorContactNumber'],
	// 		'distributorActive' => $data['distributorActive'],
	// 		'distributorCity' => $data['distributorCity'],
	// 		'distributorRoute' => $data['distributorRoute'],
	// 		'distributorTarget' => $data['distributorTarget'],
	// 		'password' => $data['password'],

	// 	];

	// 	$insert['distributorImage'] = $data['distributorImage'];
	// 	$this->db->insert('distributor', $insert);
	// 	$this->Email_model->send_email_reset_password($insert['id'], 'distributor');
	// 	return $this->get_distributor($insert['id']);
	// }


	public function validate_routes($distributorRoutes)
	{
		if (empty($distributorRoutes) || !is_array($distributorRoutes)) {
			$this->form_validation->set_message('validate_routes', 'The distributorRoutes must have at least one route.');
			return false;
		}

		foreach ($distributorRoutes as $routeData) {
			if (!isset($routeData['routes']) || !is_array($routeData['routes']) || empty($routeData['routes'])) {
				$this->form_validation->set_message('validate_routes', 'Each city in distributorRoutes must have at least one route.');
				return false;
			}
		}

		return true;
	}


	public function create_distributor($data)
	{
		$this->load->library('bcrypt');
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);

		$this->form_validation->set_rules('distributorCompanyName', 'Distributor Company Name', 'required|max_length[50]');
		$this->form_validation->set_rules('distributorEmail', 'Distributor Email', 'max_length[200]|valid_email|is_unique[distributor.distributorEmail]');
		$this->form_validation->set_rules('distributorName', 'Distributor Name', 'required|max_length[100]');
		$this->form_validation->set_rules('distributorContactNumber', 'Distributor Contact Number', 'required|numeric|max_length[10]|min_length[10]');
		$this->form_validation->set_rules('distributorActive', 'Distributor Active', 'required|max_length[100]');
		// Custom validation rule for distributorRoutes
		// $this->form_validation->set_rules('distributorRoutes', 'Distributor Routes', 'callback_validate_routes');

		if ($this->form_validation->run() == false) {
			// echo validation_errors();
			return false; // Validation failed
		}

		$this->db->trans_start();

		try {
			// Your existing code for generating UUID, hashing password, etc.
			$data['id'] = generate_uuid();
			$data['password'] = $this->bcrypt->hash_password(getPassword());
			$data['createdBy'] = getCreatedBy();

			// Create an array to insert into the "distributor" table
			$insertDistributor = [
				'id' => $data['id'],
				'createdBy' => $data['createdBy'],
				'distributorCompanyName' => $data['distributorCompanyName'],
				'distributorName' => $data['distributorName'],
				'distributorEmail' => $data['distributorEmail'] ?? '',
				'distributorLat' => $data['distributorLat'] ?? null,
				'distributorLong' => $data['distributorLong'] ?? null,
				'distributorContactNumber' => $data['distributorContactNumber'],
				'distributorActive' => $data['distributorActive'],
				'distributorArea' => $data['distributorArea'],
				'distributorAddress' => $data['distributorAddress'],
				'distributorTarget' => $data['distributorTarget'],
				'distributorImage' => $data['distributorImage'],
				'password' => $data['password'],
			];

			$this->db->insert('distributor', $insertDistributor);

			// Process distributor routes
			if (isset($data['distributorRoutes']) && is_array($data['distributorRoutes'])) {
				foreach ($data['distributorRoutes'] as $routeData) {
					$cityId = $routeData['cityId'];
					$routes = $routeData['routes'];

					// Check if routes are provided for the city
					if (!empty($routes)) {
						foreach ($routes as $routeId) {
							// Create an array to insert into the "association" table
							$insertAssociation = [
								'id' => generate_uuid(),
								'role' => 'distributor',
								'bearerId' => $data['id'],
								'routeId' => $routeId,
								'cityId' => $cityId,
							];

							$this->db->insert('association', $insertAssociation);
							if ($this->db->affected_rows() == 0) {
								// Rollback the transaction and exit the function
								$this->db->trans_rollback();
								return false;
							}
						}
					}
				}
			}
			$this->db->trans_complete();
			$this->Email_model->send_email_reset_password($data['id'], 'distributor');
			return $this->get_distributor($data['id']);
		} catch (Exception $e) {
			// An error occurred, rollback the transaction
			$this->db->trans_rollback();
			return false;
		}
	}



	// public function validate_routes($distributorRoutes)
	// {
	// 	if (empty($distributorRoutes) || !is_array($distributorRoutes)) {
	// 		$this->form_validation->set_message('validate_routes', 'The distributorRoutes must have at least one route.');
	// 		return false;
	// 	}

	// 	foreach ($distributorRoutes as $routeData) {
	// 		if (!isset($routeData['routes']) || !is_array($routeData['routes']) || empty($routeData['routes'])) {
	// 			$this->form_validation->set_message('validate_routes', 'Each city in distributorRoutes must have at least one route.');
	// 			return false;
	// 		}
	// 	}

	// 	return true;
	// }

	public function update_distributor($distributorId, $data)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('distributorCompanyName', 'Distributor Company Name', 'required|max_length[50]');
		$this->form_validation->set_rules('distributorEmail', 'Distributor Email', 'max_length[200]|valid_email');
		$this->form_validation->set_rules('distributorName', 'Distributor Name', 'required|max_length[100]');
		$this->form_validation->set_rules('distributorContactNumber', 'Distributor Contact Number', 'required|numeric|max_length[10]|min_length[10]');
		$this->form_validation->set_rules('distributorActive', 'Distributor Active', 'required|max_length[100]');
		$this->form_validation->set_rules('distributorArea', 'Distributor Area', 'required|max_length[100]');

		if ($this->form_validation->run() == false) {
			return false; // Validation failed
		}

		// Update distributor information
		$update = [
			'distributorCompanyName' => $data['distributorCompanyName'],
			'distributorName' => $data['distributorName'],
			'distributorEmail' => $data['distributorEmail'] ?? '',
			'distributorLat' => $data['distributorLat'] ?? null,
			'distributorLong' => $data['distributorLong'] ?? null,
			'distributorContactNumber' => $data['distributorContactNumber'],
			'distributorActive' => $data['distributorActive'],
			'distributorAddress' => $data['distributorAddress'],
			// 'distributorCity' => $data['distributorCity'],
			'distributorArea' => $data['distributorArea'],
			// 'distributorRoute' => $data['distributorRoute'],
			'distributorTarget' => $data['distributorTarget'],
		];
		$update['distributorImage'] = $data['distributorImage'];

		// Update distributor table
		$this->db->where('id', $distributorId);
		$this->db->update('distributor', $update);

		// Process distributor routes
		if (isset($data['distributorRoutes']) && is_array($data['distributorRoutes'])) {
			// Delete existing associations for the distributor
			$this->db->delete('association', ['bearerId' => $distributorId, 'role' => 'distributor']);

			foreach ($data['distributorRoutes'] as $routeData) {
				$cityId = $routeData['cityId'];
				$routes = $routeData['routes'];

				// Check if routes are provided for the city
				if (!empty($routes)) {
					foreach ($routes as $routeId) {
						// Create an array to insert into the "association" table
						$insertAssociation = [
							'id' => generate_uuid(),
							'role' => 'distributor',
							'bearerId' => $distributorId,
							'routeId' => $routeId,
							'cityId' => $cityId,
						];

						$this->db->insert('association', $insertAssociation);
					}
				}
			}
		}

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
