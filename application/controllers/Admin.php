<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Admin extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Admin_model');
		$this->load->library('cors');
		// $this->cors->setHeaders();
	}

	public function units_get()
	{
		$units = $this->Admin_model->get_all_units();
		$this->response([
			'status' => REST_Controller::HTTP_OK,
			'data' => $units['data'] ?? [],
			'count' => $units['count'] ?? 0
		], REST_Controller::HTTP_OK);
	}

	public function cities_with_routes_get()
	{
		// $data = $this->Admin_model->get_cities_with_routes();
		// $this->response([
		// 	'status' => REST_Controller::HTTP_OK,
		// 	'data' => $units['data'] ?? [],
		// ], REST_Controller::HTTP_OK);
		try {
			$data =  $this->Admin_model->get_cities_with_routes();
			$this->response([
				'status' => REST_Controller::HTTP_OK,
				'data' => $data
			], REST_Controller::HTTP_OK);
		} catch (\Exception $e) {
			$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $e->getMessage()], REST_Controller::HTTP_BAD_REQUEST);
		}
	}
	public function status_get()
	{
		$status = $this->Admin_model->get_all_status();
		$this->response([
			'status' => REST_Controller::HTTP_OK,
			'data' => $status
		], REST_Controller::HTTP_OK);
	}
	public function leaderboardteam_get()
	{
		$leaderboardteam = $this->Admin_model->get_all_leaderboardteam();
		if ($leaderboardteam == false) {
			$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => 'Invalid Request data'], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			$this->response([
				'status' => REST_Controller::HTTP_OK,
				'data' => $leaderboardteam['data'] ?? [],
				'count' => $leaderboardteam['count'] ?? 0
			], REST_Controller::HTTP_OK);
		}
	}
	public function leaderboardrep_get($orderId = '')
	{
		$leaderboardrep = $this->Admin_model->get_all_leaderboardrep();
		if ($leaderboardrep == false) {
			$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => 'Invalid Request data'], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			$this->response([
				'status' => REST_Controller::HTTP_OK,
				'data' => $leaderboardrep['data'] ?? [],
				'count' => $leaderboardrep['count'] ?? 0
			], REST_Controller::HTTP_OK);
		}
	}

	public function myreward_get($repId = '')
	{
		// echo $repId;
		$leaderboardrep = $this->Admin_model->get_leaderboardrep_by_repId($repId);
		if ($leaderboardrep == false) {
			$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => 'Invalid Request data'], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			$this->response([
				'status' => REST_Controller::HTTP_OK,
				'data' => $leaderboardrep,
				'count' => 0
			], REST_Controller::HTTP_OK);
		}
	}
	public function forget_password_post()
	{
		try {
			$request = json_decode(file_get_contents('php://input'), true);
			if (!is_array($request)) {
				$this->response([
					'status' => REST_Controller::HTTP_BAD_REQUEST,
					'error' => 'Invalid Request Not a valid json'
				], REST_Controller::HTTP_BAD_REQUEST);
			} else {
				return $this->Admin_model->forget_password($request);
				// if ($id == false) {
				// 	$errors = $this->form_validation->error_array();
				// 	$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
				// } elseif ($id == 'E-mail does not exsits.') {
				// 	$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $id], REST_Controller::HTTP_BAD_REQUEST);
				// } elseif ($id == 'Success') {
				// 	$this->response(['status' =>  REST_Controller::HTTP_OK, 'message' => 'We have sent an email for resetting your password to your email address. Please check your email for next steps.'], REST_Controller::HTTP_OK);
				// } else {
				// 	$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Reset Password.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
				// }
			}
		} catch (\Exception $e) {
			$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $e->getMessage()], REST_Controller::HTTP_BAD_REQUEST);
		}
	}
	public function forget_passwossrd_post()
	{
		try {
			$request = json_decode(file_get_contents('php://input'), true);
			if (!is_array($request)) {
				$this->response([
					'status' => REST_Controller::HTTP_BAD_REQUEST,
					'error' => 'Invalid Request Not a valid json'
				], REST_Controller::HTTP_BAD_REQUEST);
			} else {
				return $this->Admin_model->forget_password($request);
			}
		} catch (\Exception $e) {
			$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $e->getMessage()], REST_Controller::HTTP_BAD_REQUEST);
		}
	}
	public function reset_password_distributor_post()
	{
		try {
			$request = json_decode(file_get_contents('php://input'), true);
			if (!is_array($request)) {
				$this->response([
					'status' => REST_Controller::HTTP_BAD_REQUEST,
					'error' => 'Invalid Request Not a valid json'
				], REST_Controller::HTTP_BAD_REQUEST);
			} else {
				$distributorId = $this->Admin_model->reset_password_distributor($request);
				if ($distributorId == false) {
					$errors = $this->form_validation->error_array();
					$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
				} elseif ($distributorId) {
					$this->response(['status' => REST_Controller::HTTP_OK, 'message' => 'Password Reset Successfully.'], REST_Controller::HTTP_OK);
				} else {
					$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => 'Failed to Add Distributor.'], REST_Controller::HTTP_BAD_REQUEST);
				}
			}
		} catch (\Exception $e) {
			$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $e->getMessage()], REST_Controller::HTTP_BAD_REQUEST);
		}
	}
	public function reset_password_rep_post()
	{
		$request = json_decode(file_get_contents('php://input'), true);
		// print_r($request);
		try {
			//code...
			if (!is_array($request)) {
				$this->response([
					'status' => REST_Controller::HTTP_BAD_REQUEST,
					'error' => 'Invalid Request Not a valid json'
				], REST_Controller::HTTP_BAD_REQUEST);
			} else {
				$repId = $this->Admin_model->reset_password_rep($request);
				if ($repId == false) {
					$errors = $this->form_validation->error_array();
					$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
				} elseif ($repId) {
					$this->response(['status' => REST_Controller::HTTP_OK, 'message' => 'Password Reset Successfully.'], REST_Controller::HTTP_OK);
				} else {
					$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => 'Failed to Add Distributor.'], REST_Controller::HTTP_BAD_REQUEST);
				}
			}
		} catch (\Exception $e) {
			// $this->respon
			$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $e->getMessage()], REST_Controller::HTTP_BAD_REQUEST);
		}
	}
	public function reset_password_admin_post()
	{
		try {
			$request = json_decode(file_get_contents('php://input'), true);
			if (!is_array($request)) {
				$this->response([
					'status' => REST_Controller::HTTP_BAD_REQUEST,
					'error' => 'Invalid Request Not a valid json'
				], REST_Controller::HTTP_BAD_REQUEST);
			} else {
				$adminId = $this->Admin_model->reset_password_admin($request);
				if ($adminId == false) {
					$errors = $this->form_validation->error_array();
					$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
				} elseif ($adminId) {
					$this->response(['status' => REST_Controller::HTTP_OK, 'message' => 'Password Reset Successfully.'], REST_Controller::HTTP_OK);
				} else {
					$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Add Distributor.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
				}
			}
		} catch (\Exception $e) {
			$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $e->getMessage()], REST_Controller::HTTP_BAD_REQUEST);
		}
	}
}
