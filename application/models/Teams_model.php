<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Teams_model extends CI_Model
{


	public function get_all_teams()
	{
		if (isset($_GET['search']) and $_GET['search'] != null) {

			$search  = urldecode($_GET['search']);
			$filters = [
				'teamName' => $search,
			];
		}


		if (isset($_GET['cityId']))
			$this->db->where('cityId', $_GET['cityId']);

		if (isset($_GET['status']))
			$this->db->where('status', $_GET['status']);

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


		$query = $this->db->get('teams');




		$results = $query->result();
		$count = $query->num_rows();

		if (isset($_GET['populate']) && $_GET['populate'] == true) :
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

			$data['count'] = $count;

			return $data;
		endif;



		$data['data'] = $data['data'] ?? $results;
		$data['count'] = $count;


		return $data;
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
		$this->form_validation->set_rules('status', 'Stauts', 'required|max_length[100]');
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
