<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Teams_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Salesrepresentative_model');
	}


	public function get_all_teams()
	{
		$conditionsForCount = array();
		$conditions = array();
		$search = '';
		$num_results = 0;

		if (isset($_GET['search']) and $_GET['search'] != null) {

			$search  = urldecode($_GET['search']);
		}

		if (isset($_GET['cityId'])) {
			$conditions['assoc.cityId'] = $_GET['cityId'];
		}

		if (isset($_GET['status'])) {
			$conditions['status'] = $_GET['status'];
		}
		if (isset($_GET['routeId'])) {
			$conditions['routeId'] = $_GET['routeId'];
		}
		$countQueryBuilder = clone $this->db;

		$countQueryBuilder->select('COUNT(DISTINCT a.id) as count', false)
			->join('association assoc', 'assoc.bearerId = a.id', 'left')
			->group_start()
			->or_like('a.teamName', $search)
			->group_end();


		$conditionsForCount = $conditions;
		$countResult = $countQueryBuilder->from('teams a')->where($conditionsForCount)->get()->row();
		$num_results = $countResult->count;

		// Apply search conditions to the main query builder
		$this->db->group_start()
			->select('a.*,assoc.cityId, GROUP_CONCAT(assoc.cityId) as cityIds', false)
			->join('association assoc', 'assoc.bearerId = a.id', 'left')
			->join('city', 'city.id = assoc.cityId', 'left')
			->like('a.teamName', $search)
			->group_end()
			->group_by('a.id');

		if (isset($_GET['cityId'])) {
			$cityId = $_GET['cityId'];

			// Modify the conditions to use the 'cityId' from the association table
			$this->db->where('assoc.cityId', $cityId);
		}

		$sortField = 'a.created_date';
		$orderBy = 'DESC';
		if (isset($_GET['orderBy'])) {
			if ($_GET['orderBy'] === 'name' || $_GET['orderBy'] === '-name') {
				$sortField = 'a.teamName';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'target' || $_GET['orderBy'] === '-target') {
				$sortField = 'a.teamTarget';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'city' || $_GET['orderBy'] === '-city') {
				$sortField = 'city.cityName';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'createdOn' || $_GET['orderBy'] === '-createdOn') {
				$sortField = 'a.created_date';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'status' || $_GET['orderBy'] === '-status') {
				$sortField = 'a.status';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			}
		}


		$limit = (isset($_GET['limit']) && is_numeric($_GET['limit']) && !empty(trim($_GET['limit']))) ? $_GET['limit'] : 10;
		$offset = (isset($_GET['offset']) && is_numeric($_GET['offset']) && !empty(trim($_GET['offset']))) ? $_GET['offset'] : 0;
		$query = $this->db
			->from('teams a')
			->where($conditions)
			->limit($limit, $offset)
			->order_by($sortField, $orderBy)
			->get();

		if ($query === false) {
			echo $this->db->error()['message'];
			return false;
		}

		if ($query->num_rows() != 0) {
			$results = $query->result();
			// print_r($results);

			$data = array();

			if (isset($_GET['populate']) && $_GET['populate'] == true) {
				foreach ($results as $result) {
					// If 'populate' is true, fetch city information
					$cityIds = explode(',', $result->cityIds);
					$cities = [];

					foreach ($cityIds as $cityId) {
						$cityQuery = $this->db->get_where('city', array('id' => $cityId));
						$cityInfo = $cityQuery->row();
						$cities[] = $cityInfo;
					}

					// Convert the associative array to a simple array
					$result->cities = $cities;
					unset($result->cityIds);

					$data['data'][] = $result;
				}
			} else {
				// If 'populate' is false, provide only city IDs
				foreach ($results as $result) {
					$result->cities = explode(',', $result->cityIds);
					unset($result->cityIds);

					$data['data'][] = $result;
				}
			}

			$data['count'] = $num_results;
			return $data;
		} else {
			return FALSE;
		}
	}

	public function get_teams($id)
	{
		$this->db->select('a.*');
		$this->db->from('teams a');
		$this->db->where('a.id', $id);
		$query = $this->db->get();

		if ($query->num_rows() != 0) {
			$result = $query->row();
			$count = $query->num_rows();
			$data = array();

			if (isset($_GET['populate']) && $_GET['populate'] == true) {
				$teamQuery = $this->db->get_where('rep', array('id' => $result->teamRepId));
				$teamInfo = $teamQuery->row();
				$result->teamRepId = $teamInfo;

				// Fetch team routes from association table
				$routesQuery = $this->db->select('cityId')
					->from('association')
					->where(['role' => 'team', 'bearerId' => $id])
					->get();

				$cities = [];

				foreach ($routesQuery->result() as $row) {
					$cityId = $row->cityId;

					// If 'populate' is true, fetch city information
					if (isset($_GET['populate']) && $_GET['populate'] == true) {
						$cityQuery = $this->db->get_where('city', array('id' => $cityId));
						$cityInfo = $cityQuery->row();
						$cities[] = $cityInfo;
					} else {
						// Otherwise, just provide the city ID
						$cities[] = $cityId;
					}
				}

				// Convert the associative array to a simple array
				$result->cities = $cities;

				$data['data'] = $result;
				return $data;
			}

			$data['data'] = $data['data'] ?? $result;
			$data['count'] = $count;
			return $data;
		} else {
			return false;
		}
	}



	// public function create_teams($data)
	// {
	// 	$this->load->library('form_validation');
	// 	$this->form_validation->set_data($data);
	// 	$this->form_validation->set_rules('teamName', 'Team Name', 'required|max_length[50]');
	// 	$this->form_validation->set_rules('teamRepId', 'Teams Rep Id', 'max_length[100]');
	// 	$this->form_validation->set_rules('teamTarget', 'Teams Target ', 'required');
	// 	$this->form_validation->set_rules('status', 'Status', 'required|max_length[100]');



	// 	if ($this->form_validation->run() == false) {
	// 		return false; // Validation failed
	// 	}

	// 	$this->db->trans_start();

	// 	try {
	// 		$insert['teamName']		= $data['teamName'];
	// 		if ($data['teamRepId']) $insert['teamRepId']	= $data['teamRepId'];
	// 		if ($data['teamTarget']) $insert['teamTarget']	= $data['teamTarget'];
	// 		$insert['status']		= $data['status'];


	// 		$insert['id'] = generate_uuid();
	// 		$insert['createdBy'] = getCreatedBy();
	// 		$this->db->insert('teams', $insert);

	// 		if (isset($data['cities']) && is_array($data['cities'])) {
	// 			foreach ($data['cities'] as $cityId) {
	// 				$insertAssociation = [
	// 					'id' => generate_uuid(),
	// 					'role' => 'team',
	// 					'bearerId' => $insert['id'],
	// 					'cityId' => $cityId,
	// 				];

	// 				$this->db->insert('association', $insertAssociation);
	// 				if ($this->db->affected_rows() == 0) {
	// 					// Rollback the transaction and exit the function
	// 					$this->db->trans_rollback();
	// 					return false;
	// 				}
	// 			}
	// 		}
	// 		$this->db->trans_complete();
	// 		return $this->get_teams($insert['id']);
	// 	} catch (Exception $e) {
	// 		// An error occurred, rollback the transaction
	// 		$this->db->trans_rollback();
	// 		return false;
	// 	}
	// }

	public function create_teams($data)
	{
		try {
			$this->load->library('form_validation');
			$this->form_validation->set_data($data);
			$this->form_validation->set_rules('teamName', 'Team Name', 'required|max_length[50]');
			$this->form_validation->set_rules('teamRepId', 'Teams Rep Id ', 'max_length[100]');
			$this->form_validation->set_rules('teamTarget', 'Teams Target ', 'required');
			$this->form_validation->set_rules('status', 'Stauts', 'required|max_length[100]');
			$this->form_validation->set_rules('cities[]', 'Cities', 'required'); // Update rule for cities

			if ($this->form_validation->run() == false) {
				return false; // Validation failed
			}


			$this->db->trans_start();

			$insert = [
				'teamName'   => $data['teamName'],
				'teamRepId'  => $data['teamRepId'] ?? null,
				'teamTarget' => $data['teamTarget'] ?? null,
				'status'     => $data['status'],
				'id'         => generate_uuid(),
				'createdBy'  => getCreatedBy(),
			];

			$this->db->insert('teams', $insert);

			if ($this->db->affected_rows() === 0) {
				throw new Exception('Failed to insert data into the teams table.');
			}

			if ($data['teamRepId']) {
				// Fetch sales representative info
				$salesRepInfo = $this->Salesrepresentative_model->get_Salesrepresentative($data['teamRepId']);

				if ($salesRepInfo['count'] > 0 && isset($salesRepInfo['data']->cities)) {
					// Check if at least one cityId in the sales rep info is in $data['cities']
					$repCityIds = array_column($salesRepInfo['data']->cities, 'cityId');
					if (count(array_intersect($repCityIds, $data['cities'])) === 0) {
						throw new Exception('No common city found between Rep and team.');
					}
				}
			}

			if (isset($data['cities']) && is_array($data['cities'])) {
				foreach ($data['cities'] as $cityId) {
					$insertAssociation = [
						'id'       => generate_uuid(),
						'role'     => 'team',
						'bearerId' => $insert['id'],
						'cityId'   => $cityId,
					];

					$this->db->insert('association', $insertAssociation);

					if ($this->db->affected_rows() === 0) {
						throw new Exception('Failed to insert data into the association table.');
					}
				}
			}

			$this->db->trans_complete();

			return $this->get_teams($insert['id']);
		} catch (Exception $e) {
			// echo $e->getMessage();
			log_message('error', 'Error in create_teams: ' . $e->getMessage());
			$this->db->trans_rollback();
			throw $e;
			return false;
		}
	}


	public function update_teams($id, $data)
	{
		try {
			$this->load->library('form_validation');
			$this->form_validation->set_data($data);
			$this->form_validation->set_rules('teamName', 'Team Name', 'required|max_length[50]');
			$this->form_validation->set_rules('teamRepId', 'Teams Rep Id ', 'max_length[100]');
			$this->form_validation->set_rules('teamTarget', 'Teams Target ', 'required');
			$this->form_validation->set_rules('status', 'Stauts', 'required|max_length[100]');
			$this->form_validation->set_rules('cities[]', 'Cities', 'required'); // Update rule for cities

			if ($this->form_validation->run() == false) {
				return false; // Validation failed
			}

			// Check if any other team with the same name exists
			if (!$this->is_team_name_unique($id, $data['teamName'])) {
				throw new Exception('Team Name must be unique.');
			}

			$this->db->trans_start(); // Start transaction

			$update['teamName'] = $data['teamName'];
			if ($data['teamRepId']) $update['teamRepId'] = $data['teamRepId'];
			if ($data['teamTarget']) $update['teamTarget'] = $data['teamTarget'];
			$update['status'] = $data['status'];

			// Fetch sales representative info
			$salesRepInfo = $this->Salesrepresentative_model->get_Salesrepresentative($data['teamRepId']);

			if ($salesRepInfo['count'] > 0 && isset($salesRepInfo['data']->cities)) {
				// Check if at least one cityId in the sales rep info is in $data['cities']
				$repCityIds = array_column($salesRepInfo['data']->cities, 'cityId');
				if (count(array_intersect($repCityIds, $data['cities'])) === 0) {
					throw new Exception('No common City found between Rep and team.');
				}
			}

			// Update 'teams' table
			$this->db->where('id', $id);
			$this->db->update('teams', $update);

			if ($this->db->affected_rows() === 0) {
				throw new Exception('Failed to update data in the teams table.');
			}

			// Update 'association' table for cities
			if (isset($data['cities']) && is_array($data['cities'])) {
				// Delete existing associations
				$this->db->where(['role' => 'team', 'bearerId' => $id]);
				$this->db->delete('association');

				// Insert new associations
				foreach ($data['cities'] as $cityId) {
					$insertAssociation = [
						'id'       => generate_uuid(),
						'role'     => 'team',
						'bearerId' => $id,
						'cityId'   => $cityId,
					];

					$this->db->insert('association', $insertAssociation);

					if ($this->db->affected_rows() === 0) {
						throw new Exception('Failed to insert data into the association table.');
					}
				}
			}

			$this->db->trans_complete(); // Complete transaction

			return $this->get_teams($id);
		} catch (Exception $e) {
			log_message('error', 'Error in update_teams: ' . $e->getMessage());
			$this->db->trans_rollback();
			throw $e;
		}
	}


	public function delete_teams($id)
	{
		$team  = $this->get_teams($id);
		if ($team) {

			$this->db->where('id', $id);
			$this->db->delete('teams');

			if ($this->db->affected_rows() > 0) return true;
			else return FALSE;
		} else return FALSE;
	}

	private function is_team_name_unique($id, $teamName)
	{
		$this->db->where('id !=', $id);
		$this->db->where('LOWER(teamName)', strtolower($teamName));
		$query = $this->db->get('teams');
		return $query->num_rows() === 0;
	}
}
