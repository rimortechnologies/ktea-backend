<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class City extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('City_model');
		$this->load->library('cors');
		$this->cors->setHeaders();
	}

	public function index_get($cityId = '')
	{
		if ($cityId != '') $cities = $this->City_model->get_city($cityId);
		else $cities = $this->City_model->get_all_cities();
		$this->response([
			'status' => REST_Controller::HTTP_OK,
			'data' => $cities['data'] ?? [],
			'count' => $cities['count'] ?? 0
		], REST_Controller::HTTP_OK);
	}
	public function index_post()
	{
		$request = json_decode(file_get_contents('php://input'), true);
		if (!is_array($request)) {
			$this->response([
				'status' => REST_Controller::HTTP_BAD_REQUEST,
				'error' => 'Invalid Request Not a valid json'
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			$cityId = $this->City_model->create_city($request);
			if ($cityId == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($cityId) {
				$this->response(['status' => REST_Controller::HTTP_OK, 'data' => $cityId, 'message' => 'City Inserted successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Add city.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		}
	}

	public function index_put($cityId)
	{
		$request = json_decode(file_get_contents('php://input'), true);
		if (!is_array($request)) {
			$this->response([
				'status' => REST_Controller::HTTP_BAD_REQUEST,
				'error' => 'Invalid Request Not a valid json'
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			$cityId = $this->City_model->update_city($cityId, $request);
			if ($cityId == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($cityId) {
				$this->response(['status' => REST_Controller::HTTP_OK, 'data' => $cityId, 'message' => 'City updated successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Add city.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		}
	}

	public function index_delete($cityId)
	{
		$result = $this->City_model->delete_city($cityId);
		if ($result == true) {
			$this->response(['status' => REST_Controller::HTTP_OK, 'message' => 'City Deleted successfully.'], REST_Controller::HTTP_OK);
		} else {
			$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Delete city.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}
