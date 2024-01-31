<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Teams extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Teams_model');
		$this->load->library('cors');
		$this->cors->setHeaders();
	}

	public function index_get($productId = '')
	{
		if ($productId != '') {
			$teams = $this->Teams_model->get_teams($productId);
		} else {
			$teams = $this->Teams_model->get_all_teams();
		}
		$this->response([
			'status' => REST_Controller::HTTP_OK,
			'data' => $teams['data'] ?? [],
			'count' => $teams['count'] ?? 0,
		], REST_Controller::HTTP_OK);
	}
	// public function index_post()
	// {
	// 	$request = json_decode(file_get_contents('php://input'), true);
	// 	if (!is_array($request)) {
	// 		$this->response([
	// 			'status' => REST_Controller::HTTP_BAD_REQUEST,
	// 			'error' => 'Invalid Request Not a valid json'
	// 		], REST_Controller::HTTP_BAD_REQUEST);
	// 	} else {
	// 		$result = $this->Teams_model->create_Teams($request);
	// 		if ($result == false) {
	// 			$errors = $this->form_validation->error_array();
	// 			$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
	// 		} elseif ($result) {
	// 			$this->response(['status' => REST_Controller::HTTP_OK, 'data' => $result, 'message' => 'Teams Inserted successfully.'], REST_Controller::HTTP_OK);
	// 		} else {
	// 			$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Add Teams.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
	// 		}
	// 	}
	// }

	// Inside the controller function

	public function index_post()
	{
		$request = json_decode(file_get_contents('php://input'), true);

		if (!is_array($request)) {
			$this->response([
				'status' => REST_Controller::HTTP_BAD_REQUEST,
				'error' => 'Invalid request. Not a valid JSON.'
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			try {
				$result = $this->Teams_model->create_teams($request);
				$this->response([
					'status' => REST_Controller::HTTP_OK,
					'data' => $result,
					'message' => 'Teams inserted successfully.'
				], REST_Controller::HTTP_OK);
			} catch (Exception $e) {
				// Handle specific exceptions
				$errorMessage = $e->getMessage();

				$this->response([
					'status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,
					'error' => $errorMessage
				], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		}
	}


	public function index_put($id)
	{
		$request = json_decode(file_get_contents('php://input'), true);
		if (!is_array($request)) {
			$this->response([
				'status' => REST_Controller::HTTP_BAD_REQUEST,
				'error' => 'Invalid Request Not a valid json'
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			try {
				$result = $this->Teams_model->update_Teams($id, $request);
				$this->response([
					'status' => REST_Controller::HTTP_OK,
					'data' => $result,
					'message' => 'Teams Updated successfully.'
				], REST_Controller::HTTP_OK);
			} catch (Exception $e) {
				// Handle specific exceptions
				$errorMessage = $e->getMessage();

				$this->response([
					'status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,
					'error' => $errorMessage
				], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		}
	}

	public function index_delete($id)
	{
		$result = $this->Teams_model->delete_Teams($id);
		if ($result) {
			$this->response(['status' => REST_Controller::HTTP_OK, 'message' => 'Teams Deleted successfully.'], REST_Controller::HTTP_OK);
		} else {
			$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Delete Teams.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}
