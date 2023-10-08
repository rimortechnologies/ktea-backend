<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Teams_model extends CI_Model
{


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
			$conditions['cityId'] = $_GET['cityId'];
		}

		if (isset($_GET['status'])) {
			$conditions['status'] = $_GET['status'];
		}
		if (isset($_GET['routeId'])) {
			$conditions['routeId'] = $_GET['routeId'];
		}
		$countQueryBuilder = clone $this->db;

		$countQueryBuilder->select('COUNT(*) as count')
			->join('city', 'city.id = a.cityId', 'left')
			->group_start()
			->or_like('a.teamName', $search)
			->group_end();

		$conditionsForCount = $conditions;
		$countResult = $countQueryBuilder->from('teams a')->where($conditionsForCount)->get()->row();
		$num_results = $countResult->count;

		// Apply search conditions to the main query builder
		$this->db->group_start()
			->select('a.*, city.cityName')
			->join('city', 'city.id = a.cityId', 'left')
			->like('a.teamName', $search)
			->group_end();

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

			$data = array();



			if (isset($_GET['populate']) && $_GET['populate'] == true) {
				foreach ($results as $result) {
					$teamQuery = $this->db->get_where('rep', array('id' => $result->teamRepId));
					$teamInfo = $teamQuery->row();
					$result->teamRepId = $teamInfo;

					$routeQuery = $this->db->get_where('route', array('id' => $result->routeId));
					$routeInfo = $routeQuery->row();
					$result->routeId = $routeInfo;
					unset($result->routeId);

					$memberQuery = $this->db->get_where('rep', array('repTeam' => $result->id));
					$memberInfo = $memberQuery->result();
					$result->memberInfo = $memberInfo;

					$teamQuery = $this->db->get_where('city', array('id' => $result->cityId));
					$teamInfo = $teamQuery->row();
					$result->cityId = $teamInfo;
					$data['data'][] = $result;
				}

				$data['count'] = $num_results;
				return $data;
			}

			$data['data'] = $data['data'] ?? $results;
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
			if (isset($_GET['populate']) && $_GET['populate'] == true) :
				$teamQuery = $this->db->get_where('rep', array('id' => $result->teamRepId));
				$teamInfo = $teamQuery->row();
				$result->teamRepId = $teamInfo;


				$routeQuery = $this->db->get_where('route', array('id' => $result->routeId));
				$routeInfo = $routeQuery->row();
				$result->routeId = $routeInfo;
				unset($result->routeId);

				$teamQuery = $this->db->get_where('city', array('id' => $result->cityId));
				$teamInfo = $teamQuery->row();
				$result->cityId = $teamInfo;
				$data['data'] = $result;
				return $data;
			endif;
			$data['data'] = $data['data'] ?? $result;
			$data['count'] = $count;
			return $data;
		} else return false;
	}

	public function create_teams($data)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('teamName', 'Team Name', 'required|max_length[50]');
		$this->form_validation->set_rules('teamRepId', 'Teams Rep Id', 'max_length[100]');
		$this->form_validation->set_rules('teamTarget', 'Teams Target ', 'required');
		$this->form_validation->set_rules('cityId', 'City Id', 'required|max_length[100]');
		$this->form_validation->set_rules('status', 'Status', 'required|max_length[100]');
		//$this->form_validation->set_rules('routeId', 'Route Id ', 'required|max_length[100]');



		if ($this->form_validation->run() == false) {
			return false; // Validation failed
		}

		$insert['teamName']		= $data['teamName'];
		if ($data['teamRepId']) $insert['teamRepId']	= $data['teamRepId'];
		if ($data['teamTarget']) $insert['teamTarget']	= $data['teamTarget'];
		// $insert['teamRepId']	= $data['teamRepId'];
		$insert['status']		= $data['status'];

		$insert['cityId'] = $data['cityId'];
		//$insert['routeId']=$data['routeId'];

		$insert['id'] = generate_uuid();
		$insert['createdBy'] = getCreatedBy();
		$this->db->insert('teams', $insert);
		if ($this->db->affected_rows() > 0) {
			return $this->get_teams($insert['id']);
		} else {
			return false;
		}
	}

	public function update_teams($id, $data)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('teamName', 'Team Name', 'required|max_length[50]');
		$this->form_validation->set_rules('teamRepId', 'Teams Rep Id ', 'max_length[100]');
		$this->form_validation->set_rules('teamTarget', 'Teams Target ', 'required');
		$this->form_validation->set_rules('status', 'Stauts', 'required|max_length[100]');
		$this->form_validation->set_rules('cityId', 'City Id ', 'required|max_length[100]');
		//$this->form_validation->set_rules('routeId', 'Route Id ', 'required|max_length[100]');



		if ($this->form_validation->run() == false) {
			return false; // Validation failed
		}

		$update['teamName'] = $data['teamName'];
		// $update['teamRepId'] = $data['teamRepId'];
		if ($data['teamRepId']) $update['teamRepId'] = $data['teamRepId'];
		if ($data['teamTarget']) $update['teamTarget'] = $data['teamTarget'];
		$update['status']		= $data['status'];
		$update['cityId'] = $data['cityId'];
		//$update['routeId']=$data['routeId'];


		$this->db->where('id', $id);
		$this->db->update('teams', $update);
		return $this->get_teams($id);
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
}
