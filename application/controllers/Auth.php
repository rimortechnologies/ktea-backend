<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Auth extends REST_Controller
{
	protected $secretKey = "mysecretkey";
	protected $jwt;
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Auth_model');
		$this->jwt = new JWT();
		$this->load->library('cors');
		// $this->cors->setHeaders();

	}
	public function loginAdmin_post()
	{
		$request = json_decode(file_get_contents('php://input'), true);
		if (!is_array($request)) {
			$this->response([
				'status' => REST_Controller::HTTP_BAD_REQUEST,
				'error' => 'Invalid Request Not a valid json'
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			$admin = $this->Auth_model->login_admin($request);
			if ($admin) {
				$token = array(
					'email' => $admin->adminEmail,
					'password' => $admin->password
				);
				$jwtToken = $this->jwt->encode($token, $this->secretKey, 'HS256');
				$this->response(['status' => REST_Controller::HTTP_OK, 'data' => $admin, 'token' => $jwtToken, 'message' => 'Login successfully.'], REST_Controller::HTTP_OK);
			} else {
				if (validation_errors()) $this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => strip_tags(validation_errors())], REST_Controller::HTTP_BAD_REQUEST);
				else  $this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Login.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		}
	}
	public function loginDistributor_post()
	{
		$request = json_decode(file_get_contents('php://input'), true);
		if (!is_array($request)) {
			$this->response([
				'status' => REST_Controller::HTTP_BAD_REQUEST,
				'error' => 'Invalid Request Not a valid json'
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			$distributor = $this->Auth_model->login_distributor($request);
			if ($distributor) {
				$token = array(
					'email' => $distributor->distributorEmail,
					'password' => $distributor->password
				);
				$jwtToken = $this->jwt->encode($token, $this->secretKey, 'HS256');
				$this->response(['status' => REST_Controller::HTTP_OK, 'data' => $distributor, 'token' => $jwtToken, 'message' => 'Login successfully.'], REST_Controller::HTTP_OK);
			} else {
				$errors = $this->form_validation->error_array();
				if (validation_errors()) $this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
				else $this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Login.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		}
	}
	public function loginSalesrepresentative_post()
	{
		try {
			$request = json_decode(file_get_contents('php://input'), true);
			if (!is_array($request)) {
				$this->response([
					'status' => REST_Controller::HTTP_BAD_REQUEST,
					'error' => 'Invalid Request Not a valid json'
				], REST_Controller::HTTP_BAD_REQUEST);
			} else {
				$sales = $this->Auth_model->login_salesrepresentative($request);
				if ($sales) {
					$token = array(
						'email' => $sales->repEmail,
						'password' => $sales->password
					);
					$jwtToken = $this->jwt->encode($token, $this->secretKey, 'HS256');
					$this->response(['status' => REST_Controller::HTTP_OK, 'id' => $sales->id, 'token' => $jwtToken, 'message' => 'Login successfully.'], REST_Controller::HTTP_OK);
					// return $this->Admin_model->forget_password($request);
				}
			}
		} catch (\Exception $e) {
			$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $e->getMessage()], REST_Controller::HTTP_BAD_REQUEST);
		}
	}
}
