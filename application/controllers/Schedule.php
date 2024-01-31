<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Schedule extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Schedule_model');
		$this->load->library('cors');
		$this->cors->setHeaders();
	}

	public function index_get($productId = '')
	{
		if ($productId != '') {
			$schedule = $this->Schedule_model->get_schedule($productId);
		} else {
			$schedule = $this->Schedule_model->get_all_schedule();
		}
		$this->response([
			'status' => REST_Controller::HTTP_OK,
			'data' => $schedule['data'] ?? null,
			'count' => $schedule['count'] ?? 0,
		], REST_Controller::HTTP_OK);
	}

	public function salesrep_schedule_get()
	{

		try {
			// Call the accept_order method from the Orders_model
			$result = $this->Schedule_model->get_schedule_by_month_and_rep();
			// If everything is okay, send the result
			$this->response(['status' => REST_Controller::HTTP_OK, 'data' => $result], REST_Controller::HTTP_OK);
		} catch (Exception $e) {
			// Catch any exceptions and send the error message in the response
			$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => $e->getMessage()], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
		}
	}

	public function today_schedule_get()
	{
		// $request = json_decode(file_get_contents('php://input'), true);
		// echo $request;
		// if (!is_array($request)) {
		// 	$this->response([
		// 		'status' => REST_Controller::HTTP_BAD_REQUEST,
		// 		'error' => 'Invalid Request Not a valid json'
		// 	], REST_Controller::HTTP_BAD_REQUEST);
		// } else {
		try {
			$schedule = $this->Schedule_model->get_schedule_by_rep_and_date();
			$this->response([
				'status' => REST_Controller::HTTP_OK,
				'data' => $schedule['data'][0] ?? [],
				'count' => $schedule['count'] ?? 0,
			], REST_Controller::HTTP_OK);
			// }
		} catch (Exception $e) {
			// Catch any exceptions and send the error message in the response
			$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => $e->getMessage()], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
		}
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
			$result = $this->Schedule_model->create_Schedule($request);
			if ($result == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($result) {
				$this->response(['status' => REST_Controller::HTTP_OK, 'data' => $result, 'message' => 'Schedule Inserted successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Add Schedule.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		}
	}

	public function index_put()
	{
		$request = json_decode(file_get_contents('php://input'), true);
		if (!is_array($request)) {
			$this->response([
				'status' => REST_Controller::HTTP_BAD_REQUEST,
				'error' => 'Invalid Request Not a valid json'
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			$result = $this->Schedule_model->update_Schedule($request);
			if ($result == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($result) {
				$this->response(['status' => REST_Controller::HTTP_OK, 'data' => $result, 'message' => 'Schedule updated successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Add Schedule.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		}
	}

	public function index_delete($id)
	{
		$result = $this->Schedule_model->delete_Schedule($id);
		if ($result) {
			$this->response(['status' => REST_Controller::HTTP_OK, 'message' => 'Schedule Deleted successfully.'], REST_Controller::HTTP_OK);
		} else {
			$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Delete Schedule.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}
