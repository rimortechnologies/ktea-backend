<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Admin_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Email_model');
	}
	public function get_all_status()
	{
		$query = $this->db->get('status');
		$data['data'] = $query->result();
		$data['count'] = $query->num_rows();
		return $data;
	}

	public function get_cities_with_routes()
	{
		$this->db->select('city.id as cityId, city.cityName as cityName');
		$this->db->from('city');
		$this->db->group_by('city.id');
		$query = $this->db->get();

		if (!$query) {
			die("Database error: " . $this->db->error()['message']);
		}

		$cities = $query->result();

		foreach ($cities as &$city) {
			$this->db->select('id as routeId, routeName');
			$this->db->from('route');
			$this->db->where('routeCity', $city->cityId);
			$routesQuery = $this->db->get();

			if (!$routesQuery) {
				die("Database error: " . $this->db->error()['message']);
			}

			$city->routes = $routesQuery->result();
		}

		return $cities;
	}



	public function get_all_units()
	{
		$query = $this->db->get('units');
		$data['data'] = $query->result();
		$data['count'] = $query->num_rows();
		return $data;
	}
	public function update_image($name, $table, $id)
	{

		if ($_FILES[$name]['name'] != "") {


			$config['upload_path']      	= 'uploads/' . $table;
			$config['allowed_types']        = 'gif|jpg|png|jpeg';
			$config['file_name'] 			= uniqid() . "_" . date('YmdHis') . "_" . $table;
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			if (!$this->upload->do_upload($name)) {


				$this->session->set_flashdata('error', $this->upload->display_errors());
			} else {




				$upload 								= $this->upload->data();
				$insert[$name] 							= $upload['file_name'];

				$insert['updated_date'] 				= date('Y-m-d H:i:s');
				$this->db->where('id', $id);
				$this->db->update($table, $insert);
				return $id;
			}
		}
	}
	public function get_all_leaderboardteam()
	{
		$currentMonth = date('M');
		$currentYear = date('Y');

		if (isset($_GET['month_and_year'])) {
			$arr = explode("-", $_GET['month_and_year']);
			if (count($arr) == 2 && is_numeric($arr[0]) && $arr[0] <= 12 && is_numeric($arr[1]) && $arr[1] >= 2000) {
				$currentMonth = date('M', mktime(0, 0, 0, $arr[0], 1));
				$currentYear = $arr[1];
			} else {
				return false;
			}
		}

		if (isset($_GET['year'])) {
			$currentYear = $_GET['year'];
		}

		$this->db->select('id AS teamId');
		$query = $this->db->get('teams');
		$results = $query->result();

		$count = $query->num_rows();

		foreach ($results as $result) {
			$result->month = $currentMonth;
			$result->year = $currentYear;

			// Fetch the score from the leaderboardteam table
			$result->score = $this->db->select('score')->where(['teamId' => $result->teamId, 'month' => $currentMonth, 'year' => $currentYear])->get('leaderboardteam')->row('score');

			// Fetch the teamTarget value from the teams table
			$teamQuery = $this->db->get_where('teams', array('id' => $result->teamId));
			$teamInfo = $teamQuery->row();
			$result->teamTarget = $teamInfo->teamTarget;

			// Fetch all cities associated with the team from the association table
			$cityAssociations = $this->db->select('cityId')->from('association')->where(['role' => 'team', 'bearerId' => $result->teamId])->get()->result();

			$cities = [];
			foreach ($cityAssociations as $cityAssociation) {
				$cityQuery = $this->db->get_where('city', array('id' => $cityAssociation->cityId));
				$cityInfo = $cityQuery->row();
				$cities[] = $cityInfo;
			}

			$teamInfo->cities = $cities;

			// Calculate the percentage
			$percentage = ($result->score / $result->teamTarget) * 100;
			$result->percentage = min($percentage, 100); // Ensure the percentage is not beyond 100%

			if (isset($_GET['populate']) && $_GET['populate'] == true) {
				// Add any additional fields you want to populate here.
				// For example:
				$result->teamId = $teamInfo;
			}

			$data['data'][] = $result;
		}

		// Sort the results in descending order based on the percentage
		usort($data['data'], function ($a, $b) {
			return $b->percentage - $a->percentage;
		});

		$data['count'] = $count;
		return $data;
	}

	public function get_all_leaderboardrep()
	{
		$currentMonth = date('M');
		$currentYear = date('Y');

		if (isset($_GET['month_and_year'])) {
			$arr = explode("-", $_GET['month_and_year']);
			if (count($arr) == 2 && is_numeric($arr[0]) && $arr[0] <= 12 && is_numeric($arr[1]) && $arr[1] >= 2000) {
				$currentMonth = date('M', mktime(0, 0, 0, $arr[0], 1));
				$currentYear = $arr[1];
			} else
				return false;
		}
		if (isset($_GET['year']))  $currentYear = $_GET['year'];

		$this->db->select('id AS repId');
		$query = $this->db->get('rep');
		$results = $query->result();

		$count = $query->num_rows();

		foreach ($results as $result) {
			$result->month = $currentMonth;
			$result->year = $currentYear;
			$result->score = $this->db->select('score')->where(['repId' => $result->repId, 'month' => $currentMonth, 'year' => $currentYear])->get('leaderboardrep')->row('score');
			$repTarget = $this->db->select('repTarget')->where('id', $result->repId)->get('rep')->row('repTarget');

			// Calculate percentage and limit it to a maximum of 100%
			$percentage = min(100, intval(($result->score / $repTarget) * 100));
			$result->percentage = $percentage;

			$associationData = $this->db->select('cityId, routeId')->where(['role' => 'rep', 'bearerId' => $result->repId])->get('association')->result();

			$result->cities = [];

			// foreach ($associationData as $assoc) {
			// 	// Fetch city information
			// 	$cityInfo = $this->db->get_where('city', ['id' => $assoc->cityId])->row();

			// 	// Fetch route information
			// 	$routeInfo = $this->db->get_where('route', ['id' => $assoc->routeId])->row();

			// 	// Append data to the cities array
			// 	$result->cities[] = [
			// 		'cityInfo' => $cityInfo,
			// 		'routeIds' => [
			// 			'routeId' => $routeInfo->id,
			// 			'routeName' => $routeInfo->routeName,
			// 		],
			// 	];
			// }

			$result->cities = [];

			foreach ($associationData as $assoc) {
				// Fetch city information
				$cityInfo = $this->db->get_where('city', ['id' => $assoc->cityId])->row();
				$routeInfo = $this->db->get_where('route', ['id' => $assoc->routeId])->row();
				// Check if the city already exists in the result
				$cityIndex = array_search($cityInfo, array_column($result->cities, 'cityInfo'));

				if ($cityIndex === false) {
					// If the city does not exist, add it to the result
					$result->cities[] = [
						'cityInfo' => $cityInfo,
						'routeIds' => [  // Initialize routeIds as an array
							[
								'routeId' => $routeInfo->id,
								'routeName' => $routeInfo->routeName,
							],
						],
					];
				} else {
					// If the city already exists, check if routeIds field exists
					if (!isset($result->cities[$cityIndex]['routeIds']) || !is_array($result->cities[$cityIndex]['routeIds'])) {
						$result->cities[$cityIndex]['routeIds'] = [];  // Initialize routeIds as an array
					}

					// Add the route to the existing routeIds array
					$result->cities[$cityIndex]['routeIds'][] = [
						'routeId' => $routeInfo->id,
						'routeName' => $routeInfo->routeName,
					];
				}
			}

			// if (isset($_GET['populate']) && $_GET['populate'] == true) {
			// 	// ... (existing code)
			// 	$data['data'][] = $result; // Add the current result to the data array
			// }



			if (isset($_GET['populate']) && $_GET['populate'] == true) {
				$repQuery = $this->db->get_where('rep', array('id' => $result->repId));
				$repInfo = $repQuery->row();
				$repCityQuery = $this->db->get_where('city', array('id' => $repInfo->repCity));
				$repCity = $repCityQuery->row();
				$repInfo->repCity = $repCity;

				$repTeamQuery = $this->db->get_where('teams', array('id' => $repInfo->repTeam));
				$repTeam = $repTeamQuery->row();
				$repInfo->repTeam = $repTeam;
				$result->repId = $repInfo;


				$data['data'][] = $result;
			}
		}

		// Sort the results in descending order based on percentage
		usort($data['data'], function ($a, $b) {
			return $b->percentage - $a->percentage;
		});

		$data['count'] = $count;
		return $data;
	}

	public function get_leaderboardrep_by_repId($repId)
	{
		$currentMonth = date('M');
		$currentYear = date('Y');

		if (isset($_GET['month_and_year'])) {
			$arr = explode("-", $_GET['month_and_year']);
			if (count($arr) == 2 && is_numeric($arr[0]) && $arr[0] <= 12 && is_numeric($arr[1]) && $arr[1] >= 2000) {
				$currentMonth = date('M', mktime(0, 0, 0, $arr[0], 1));
				$currentYear = $arr[1];
			} else {
				return false;
			}
		}

		if (isset($_GET['year'])) {
			$currentYear = $_GET['year'];
		}

		$repTarget = $this->db->select('repTarget')->where('id', $repId)->get('rep')->row('repTarget');
		$score = $this->db->select('score')->where(['repId' => $repId, 'month' => $currentMonth, 'year' => $currentYear])->get('leaderboardrep')->row('score');

		if ($score === null) {
			return false; // No record found for the specific repId, month, and year
		}

		// Calculate percentage and limit it to a maximum of 100%
		$percentage = min(100, intval(($score / $repTarget) * 100));

		// Get the highest scoring person's data
		$highestScoreQuery = $this->db->select('repId, score')->where(['month' => $currentMonth, 'year' => $currentYear])->order_by('score', 'desc')->limit(1)->get('leaderboardrep');
		$highestScoreData = $highestScoreQuery->row();

		$data = array(
			'repId' => $repId,
			'month' => $currentMonth,
			'year' => $currentYear,
			'score' => $score,
			'percentage' => $percentage,
		);

		if (isset($_GET['populate']) && $_GET['populate'] == true) {
			$repQuery = $this->db->get_where('rep', array('id' => $repId));
			$repInfo = $repQuery->row();

			$repCityQuery = $this->db->get_where('city', array('id' => $repInfo->repCity));
			$repCity = $repCityQuery->row();
			$repInfo->repCity = $repCity;

			$repTeamQuery = $this->db->get_where('teams', array('id' => $repInfo->repTeam));
			$repTeam = $repTeamQuery->row();
			$repInfo->repTeam = $repTeam;

			$data['repId'] = $repInfo;
		}

		$difference = null;
		if ($highestScoreData->repId !== $repId) {
			$difference = array(
				'scoreDifference' => $highestScoreData->score - $score,
				'percentageDifference' => $highestScoreData->score - $percentage,
			);
		}

		$result = array(
			'repData' => $data,
			'leader' => $difference,
		);

		return $result;
	}


	public function get_user_by_emailandtype($email, $type)
	{
		$this->db->select('*');
		$this->db->from($type);
		$this->db->where($type . 'Email', $email);
		$query = $this->db->get();
		if ($query->num_rows() != 0) {
			$result = $query->row();
			return $result;
		} else return false;
	}
	public function forget_password($data)
	{

		$this->load->library('bcrypt');
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('type', "Type", 'required|xss_clean|max_length[100]');
		$this->form_validation->set_rules('email', "Email Address", 'required|xss_clean|max_length[100]');

		if ($this->form_validation->run() == false) {
			throw new \Exception("E-mail/User Invalid.");
		} else {
			$user = $this->get_user_by_emailandtype($data['email'], $data['type']);
			if ($user) :
				$this->Email_model->send_email_reset_password($user->id, $data['type']);
				return ("Success");
			else :
				throw new \Exception("E-mail does not exsits.");
			endif;
		}
	}
	public function reset_password_distributor($data)
	{
		$this->load->library('bcrypt');
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);

		$this->form_validation->set_rules('token', 'Token', 'required|max_length[100]');
		$this->form_validation->set_rules('password', 'Password', 'required|max_length[100]');
		$this->form_validation->set_rules('confirm_password', "Confirm Password", 'required|xss_clean|matches[password]');
		if ($this->form_validation->run() == FALSE)  new \Exception('Invalid Password/Confirm Password');
		if (isset($data['token'])) {
			$data['password'] = $this->bcrypt->hash_password($data['password']);
			$update = [
				'token' => null,
				'password' => $data['password']
			];
			$this->db->where('token', $data['token']);
			$this->db->update('distributor', $update);
			if ($this->db->affected_rows() > 0) return true;
			else throw new \Exception('Token Expired');
		} else throw new \Exception('Invalid Token');
	}
	public function reset_password_rep($data)
	{
		$this->load->library('bcrypt');
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		// print_r($data);
		$this->form_validation->set_rules('token', 'Token', 'required|max_length[100]');
		$this->form_validation->set_rules('password', 'Password', 'required|max_length[100]');
		$this->form_validation->set_rules('confirm_password', "Confirm Password", 'required|xss_clean|matches[password]');
		if ($this->form_validation->run() == FALSE)  new \Exception('Invalid Password/Confirm Password');
		if (isset($data['token'])) {
			$data['password'] = $this->bcrypt->hash_password($data['password']);
			$update = [
				'token' => null,
				'password' => $data['password']
			];
			$this->db->where('token', $data['token']);
			$this->db->update('rep', $update);
			if ($this->db->affected_rows() > 0) return true;
			else throw new \Exception('Token Expired');
		} else throw new \Exception('Invalid Token');
	}
	public function reset_password_admin($data)
	{
		$this->load->library('bcrypt');
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);

		$this->form_validation->set_rules('token', 'Token', 'required|max_length[100]');
		$this->form_validation->set_rules('password', 'Password', 'required|max_length[100]');
		$this->form_validation->set_rules('confirm_password', "Confirm Password", 'required|xss_clean|matches[password]');
		if ($this->form_validation->run() == FALSE)  new \Exception('Invalid Password/Confirm Password');
		if (isset($data['token'])) {
			$data['password'] = $this->bcrypt->hash_password($data['password']);
			$update = [
				'token' => null,
				'password' => $data['password']
			];
			$this->db->where('token', $data['token']);
			$this->db->update('admin', $update);
			if ($this->db->affected_rows() > 0) return true;
			else throw new \Exception('Token Expired');
		} else throw new \Exception('Invalid Token');
	}
}
