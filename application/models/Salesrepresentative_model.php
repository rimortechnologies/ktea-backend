<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Salesrepresentative_model extends CI_Model
{
	public function get_all_salesrepresentatives()
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
			$conditions['repStatus'] = $_GET['status'];
		}
		// if (isset($_GET['routeId'])) {
		// 	$conditions['association.routeId'] = $_GET['routeId']; // Use the association table's routeId
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

		$countQueryBuilder->select('COUNT(DISTINCT a.id) as count', false)
			->join('association association', 'association.bearerId = a.id AND association.routeId IS NOT NULL', 'left')
			->group_start()
			->or_like('a.firstName', $search)
			->or_like('a.lastName', $search)
			->or_like('a.repContactNumber', $search)
			->or_like('a.repEmail', $search)
			->group_end();

		$conditionsForCount = $conditions;
		$countResult = $countQueryBuilder->from('rep a')->where($conditionsForCount)->get()->row();
		$num_results = $countResult->count;

		$this->db->group_start()
			->select('a.*,association.cityId,association.routeId,GROUP_CONCAT(DISTINCT city.id) as cityIds, GROUP_CONCAT(DISTINCT route.id) as routeIds', false)
			->join('association', 'association.bearerId = a.id', 'left')
			->join('city', 'city.id = association.cityId', 'left')
			->join('route', 'route.id = association.routeId', 'left')
			->like('a.firstName', $search)
			->group_end()
			->group_by('a.id');

		if (isset($_GET['cityId'])) {
			$cityId = $_GET['cityId'];
			$this->db->where('association.cityId', $cityId);
		}

		if (isset($_GET['routeId'])) {
			$routeId = $_GET['routeId'];
			$this->db->where('association.routeId', $routeId);
		}

		$sortField = 'a.created_date';
		$orderBy = 'DESC';
		if (isset($_GET['orderBy'])) {
			if ($_GET['orderBy'] === 'city' || $_GET['orderBy'] === '-city') {
				$sortField = 'city.cityName';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'order' || $_GET['orderBy'] === '-order') {
				$sortField = 'a.repOrders';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'points' || $_GET['orderBy'] === '-points') {
				$sortField = 'a.repPoints';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'target' || $_GET['orderBy'] === '-target') {
				$sortField = 'a.repTarget';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'status' || $_GET['orderBy'] === '-status') {
				$sortField = 'a.repStatus';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'name' || $_GET['orderBy'] === '-name') {
				$sortField = 'a.firstName';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'phone' || $_GET['orderBy'] === '-phone') {
				$sortField = 'a.repContactNumber';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			}
		}

		$limit = (isset($_GET['limit']) && is_numeric($_GET['limit']) && !empty(trim($_GET['limit']))) ? $_GET['limit'] : 10;
		$offset = (isset($_GET['offset']) && is_numeric($_GET['offset']) && !empty(trim($_GET['offset']))) ? $_GET['offset'] : 0;

		$query = $this->db
			->from('rep a')
			->where($conditions)
			->limit($limit, $offset)
			->order_by($sortField, $orderBy)
			->get();

		if ($query->num_rows() != 0) {
			$results = $query->result();
			$count = $query->num_rows();
			$data = array();
			// if (isset($_GET['populate']) && $_GET['populate'] == true) {

			foreach ($results as $result) {
				// If 'populate' is true, fetch city and route information
				if (isset($_GET['populate']) && $_GET['populate'] == true) {
					$cityIds = explode(',', $result->cityIds);

					$cities = [];
					foreach ($cityIds as $cityId) {
						$cityQuery = $this->db->get_where('city', array('id' => $cityId));
						$cityInfo = $cityQuery->row();

						// Fetch routeIds for each city
						$routeIdsQuery = $this->db->select('association.routeId,route.routeName')
							->from('association')
							->join('route', 'route.id = association.routeId', 'left')  // Join the route table
							->where(['bearerId' => $result->id, 'cityId' => $cityId])
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
		// }
	}
	public function get_salesrepresentative($salesrepresentativeId)
	{
		$this->db->select('*');
		$this->db->from('rep a');
		$this->db->where('a.id', $salesrepresentativeId);
		$query = $this->db->get();

		if ($query->num_rows() != 0) {
			$result = $query->row();
			$count = $query->num_rows();
			$data = array();

			if (isset($_GET['populate']) && $_GET['populate'] == true) {
				$teamQuery = $this->db->get_where('teams', array('id' => $result->repTeam));
				$teamInfo = $teamQuery->row();
				$result->repTeam = $teamInfo;


				// Fetch distributorRoutes from association table
				$routesQuery = $this->db->select('cityId, routeId')
					->from('association')
					->where(['role' => 'rep', 'bearerId' => $salesrepresentativeId])
					->get();

				$distributorRoutes = [];

				foreach ($routesQuery->result() as $row) {
					$cityId = $row->cityId;

					// Check if the cityId exists in the result array
					if (!isset($distributorRoutes[$cityId])) {
						$distributorRoutes[$cityId] = [
							// 'cityId' => $cityId,
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
				$result->cities = array_values($distributorRoutes);

				unset($result->password);
				$data['data'] = $result;
				$data['count'] = $count;

				return $data;
			} else {


				// Fetch distributorRoutes from association table
				$routesQuery = $this->db->select('cityId, routeId')
					->from('association')
					->where(['role' => 'rep', 'bearerId' => $salesrepresentativeId])
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
				$result->cities = array_values($distributorRoutes);

				unset($result->password);
				$data['data'] = $result;
				$data['count'] = $count;

				return $data;
			}
		} else {
			return false;
		}
	}

	public function get_my_info($salesRepId)
	{
		// Get sales representative information with associated cities and routes
		$result = $this->db->get_where('rep a', ['a.id' => $salesRepId])->row();

		if (!$result) {
			return false; // Sales representative not found
		}

		// Fetch team information
		$teamQuery = $this->db->get_where('teams', ['id' => $result->repTeam]);
		$teamInfo = $teamQuery->row();
		$result->repTeam = $teamInfo;

		// Fetch distributorRoutes from association table
		$routesQuery = $this->db->select('cityId, routeId')
			->from('association')
			->where(['role' => 'rep', 'bearerId' => $salesRepId])
			->get();

		$distributorRoutes = [];

		foreach ($routesQuery->result() as $row) {
			$cityId = $row->cityId;

			// Check if the cityId exists in the result array
			if (!isset($distributorRoutes[$cityId])) {
				$distributorRoutes[$cityId] = [
					'cityInfo' => $this->db->get_where('city', ['id' => $cityId])->row(),
					'routes'   => [],
				];
			}

			// Add the routeId to the routes array
			$distributorRoutes[$cityId]['routes'][] = [
				'routeId'   => $row->routeId,
				'routeInfo' => $this->db->get_where('route', ['id' => $row->routeId])->row(),
			];
		}

		// Convert the associative array to a simple array
		$result->cities = array_values($distributorRoutes);

		// Extract route ids and route data (id and routeName) from the cities data
		$routeIds = [];
		$allRoutes = [];
		foreach ($result->cities as $city) {
			foreach ($city['routes'] as $route) {
				$routeIds[] = $route['routeId'];
				$allRoutes[] = [
					'id'        => $route['routeInfo']->id,
					'routeName' => $route['routeInfo']->routeName,
				];
			}
		}

		if (empty($routeIds)) {
			return false; // No routes found for the sales representative
		}

		// Get distributors based on route ids
		$distributors = $this->db
			->distinct()
			->select('association.bearerId as distributorId, distributor.distributorCompanyName')
			->from('association')
			->join('distributor', 'distributor.id = association.bearerId')
			->where('association.role', 'distributor')
			->where_in('association.routeId', $routeIds)
			->get()
			->result();

		// Get retailers based on route ids
		$retailers = $this->db
			->distinct()
			->select('retailer.id as retailerId, retailer.retailerShopName')
			->from('retailer')
			->where_in('retailer.retailerRoute', $routeIds)
			->get()
			->result();

		// Get all products from the product table
		$products = $this->db
			->select('*')
			->from('product')
			->get()
			->result();

		// Update the $result with the route data
		// $result->routes = $allRoutes;

		// Prepare and return the final result
		$result = [
			'salesRepInfo' => $result,
			'routes' => $allRoutes,
			'distributors' => $distributors,
			'retailers'    => $retailers,
			'products'     => $products,
		];

		return $result;
	}



	// public function get_salesrepresentative($salesrepresentativeId)
	// {
	// 	$this->db->select('*');
	// 	$this->db->from('rep a');
	// 	$this->db->where('a.id', $salesrepresentativeId);
	// 	$query = $this->db->get();
	// 	if ($query->num_rows() != 0) {
	// 		$result = $query->row();
	// 		$count = $query->num_rows();
	// 		$data = array();
	// 		if (isset($_GET['populate']) && $_GET['populate'] == true) :

	// 			$cityQuery = $this->db->get_where('city', array('id' => $result->repCity));
	// 			$cityInfo = $cityQuery->row();
	// 			$result->repCity = $cityInfo;
	// 			$teamQuery = $this->db->get_where('teams', array('id' => $result->repTeam));
	// 			$teamInfo = $teamQuery->row();

	// 			$result->isLead = 0;
	// 			if ($teamInfo) if ($teamInfo->teamRepId == $result->id) $result->isLead = 1;

	// 			$result->repTeam = $teamInfo;
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

	// public function create_salesrepresentative($data)
	// {
	// 	$this->load->library('bcrypt');
	// 	$this->load->library('form_validation');
	// 	$this->form_validation->set_data($data);
	// 	$this->form_validation->set_rules('firstName', 'Representative  Name', 'required|max_length[50]');
	// 	// $this->form_validation->set_rules('lastName', 'Representative Last Name', 'required|max_length[100]');
	// 	$this->form_validation->set_rules('repEmail', ' Representative Email', 'max_length[200]|valid_email|is_unique[rep.repEmail]');
	// 	$this->form_validation->set_rules('repContactNumber', 'Representative Contact Number', 'required|numeric|max_length[10]|min_length[10]');
	// 	$this->form_validation->set_rules('repTarget', 'Representative Leaderboard Target', 'numeric');
	// 	$this->form_validation->set_rules('repStatus', 'Representative Status', 'required|max_length[100]');
	// 	$this->form_validation->set_rules('repArea', 'Representative Area', 'required|max_length[100]');
	// 	// $this->form_validation->set_rules('repCity', 'Representative City', 'required|max_length[100]');
	// 	$this->form_validation->set_rules('repTeam', 'Representative Team', 'required|max_length[100]');

	// 	if ($this->form_validation->run() == FALSE)  return FALSE;

	// 	$this->db->trans_start();

	// 	try {
	// 		$insert['id'] = generate_uuid();
	// 		$insert['firstName'] = $data['firstName'];
	// 		$insert['repEmail'] = $data['repEmail'] ?? '';
	// 		$insert['repTarget'] = $data['repTarget'] ?? '';
	// 		$insert['repContactNumber'] = $data['repContactNumber'];
	// 		$insert['repStatus'] = $data['repStatus'];
	// 		$insert['repArea'] = $data['repArea'];
	// 		// $insert['repCity'] = $data['repCity'];
	// 		$insert['repTeam'] = $data['repTeam'];
	// 		$insert['createdBy'] = getCreatedBy();
	// 		$insert['password'] = $this->bcrypt->hash_password(getPassword());
	// 		$this->db->insert('rep', $insert);

	// 		if (isset($data['repRoutes']) && is_array($data['repRoutes'])) {
	// 			foreach ($data['repRoutes'] as $routeData) {
	// 				$cityId = $routeData['cityId'];
	// 				$routes = $routeData['routes'];

	// 				// Check if routes are provided for the city
	// 				if (!empty($routes)) {
	// 					foreach ($routes as $routeId) {
	// 						// Create an array to insert into the "association" table
	// 						$insertAssociation = [
	// 							'id' => generate_uuid(),
	// 							'role' => 'rep',
	// 							'bearerId' => $insert['id'],
	// 							'routeId' => $routeId,
	// 							'cityId' => $cityId,
	// 						];

	// 						$this->db->insert('association', $insertAssociation);
	// 						if ($this->db->affected_rows() == 0) {
	// 							// Rollback the transaction and exit the function
	// 							$this->db->trans_rollback();
	// 							return false;
	// 						}
	// 					}
	// 				}
	// 			}
	// 		}
	// 		$this->db->trans_complete();

	// 		$this->Email_model->send_email_reset_password($insert['id'], 'rep');
	// 		return $this->get_salesrepresentative($insert['id']);
	// 	} catch (Exception $e) {
	// 		// An error occurred, rollback the transaction
	// 		$this->db->trans_rollback();
	// 		return false;
	// 	}
	// }

	public function update_salesrepresentative($salesrepresentativeId, $data)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('firstName', 'Representative  Name', 'required|max_length[50]');
		$this->form_validation->set_rules('repEmail', ' Representative Email', 'max_length[200]|valid_email');
		$this->form_validation->set_rules('repContactNumber', 'Representative Contact Number', 'required|numeric|max_length[10]|min_length[10]');
		$this->form_validation->set_rules('repTarget', 'Representative Leaderboard Targt', 'numeric');
		$this->form_validation->set_rules('repStatus', 'Representative Status', 'required|max_length[100]');
		// $this->form_validation->set_rules('repCity', 'Representative City', 'required|max_length[100]');
		$this->form_validation->set_rules('repTeam', 'Representative Team', 'required|max_length[100]');
		if ($this->form_validation->run() == false)  return FALSE;

		$update['firstName'] = $data['firstName'];
		if (isset($data['repContactNumber']))
			$update['repContactNumber'] = $data['repContactNumber'];
		if (isset($data['repEmail']))
			$update['repEmail'] = $data['repEmail'];
		if (isset($data['target']))
			$update['target'] = $data['target'];
		$update['repStatus'] = $data['repStatus'];
		if (isset($data['repArea']))
			$update['repArea'] = $data['repArea'];
		if (isset($data['repTarget'])) $update['repTarget'] = $data['repTarget'];
		// $update['repCity'] = $data['repCity'];
		$update['repTeam'] = $data['repTeam'];
		$this->db->where('id', $salesrepresentativeId);
		$this->db->update('rep', $update);

		if (isset($data['cities']) && is_array($data['cities'])) {
			// Delete existing associations for the distributor
			$this->db->delete('association', ['bearerId' => $salesrepresentativeId, 'role' => 'rep']);

			foreach ($data['cities'] as $routeData) {
				$cityId = $routeData['cityId'];
				$routes = $routeData['routes'];

				// Check if routes are provided for the city
				if (!empty($routes)) {
					foreach ($routes as $routeId) {
						// Create an array to insert into the "association" table
						$insertAssociation = [
							'id' => generate_uuid(),
							'role' => 'rep',
							'bearerId' => $salesrepresentativeId,
							'routeId' => $routeId,
							'cityId' => $cityId,
						];

						$this->db->insert('association', $insertAssociation);
					}
				}
			}
		}

		return $this->get_salesrepresentative($salesrepresentativeId);
	}

	public function delete_salesrepresentative($salesrepresentativeId)
	{

		$this->db->where('id', $salesrepresentativeId);
		$sales_rep = $this->db->get('rep')->row();
		if ($sales_rep) {
			if ($this->db->get_where('orders', array('orderSalesRepId' => $salesrepresentativeId))->row()) return FALSE;
			if ($this->db->get_where('schedule', array('salesRepId' => $salesrepresentativeId))->row()) return FALSE;

			$this->db->where('id', $salesrepresentativeId);
			$this->db->delete('rep');
			if ($this->db->affected_rows() > 0) return true;
			else return FALSE;
		} else return FALSE;
	}
}
