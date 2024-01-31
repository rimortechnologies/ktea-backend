<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Stock extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Stock_model');
		$this->load->library('cors');
		$this->cors->setHeaders();
	}

	public function index_get($distributorId = '')
	{
		$stocks = $this->Stock_model->get_all_stocks($distributorId);

		$this->response([
			'status' => REST_Controller::HTTP_OK,
			'data' => $stocks['data'] ?? [],
			'count' => $stocks['count'] ?? 0,
		], REST_Controller::HTTP_OK);
	}

	public function stock_summary_get($distributorId = '')
	{
		$stocks = $this->Stock_model->get_stock_summary($distributorId);
		// echo $stocks;
		$this->response([
			'status' => REST_Controller::HTTP_OK,
			'data' => $stocks ?? [],
			'count' => $stocks['count'] ?? 0,
		], REST_Controller::HTTP_OK);
	}

	public function clear_return_post()
	{
		try {
			$request = json_decode(file_get_contents('php://input'), true);

			// Check if the request data is a valid JSON array
			if (!is_array($request)) {
				$this->response([
					'status' => REST_Controller::HTTP_BAD_REQUEST,
					'error' => 'Invalid Request. Not a valid JSON.'
				], REST_Controller::HTTP_BAD_REQUEST);
			} else {
				// Call the accept_order method from the Orders_model
				$result = $this->Stock_model->clear_return($request);
				// If everything is okay, send the result
				$this->response(['status' => REST_Controller::HTTP_OK, 'data' => $result, 'message' => 'Return Stock Cleared'], REST_Controller::HTTP_OK);
			}
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
				'error' => REST_Controller::HTTP_BAD_REQUEST,
				'message' => 'Invalid Request Not a valid json'
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			$result = $this->Stock_model->create_Stock($request);
			if ($result == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($result) {
				$this->response(['status' => REST_Controller::HTTP_OK, 'data' => $result, 'message' => 'Stock Inserted successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Add Stock.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		}
	}

	public function index_put($stockId)
	{
		$request = json_decode(file_get_contents('php://input'), true);
		if (!is_array($request)) {
			$this->response([
				'status' => REST_Controller::HTTP_BAD_REQUEST,
				'error' => 'Invalid Request Not a valid json'
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			$result = $this->Stock_model->update_Stock($stockId, $request);
			if ($result == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($result) {
				$this->response(['status' => REST_Controller::HTTP_OK, 'data' => $result, 'message' => 'Stock updated successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Update Stock.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		}
	}

	public function index_delete($distributorId, $productId)
	{
		$result = $this->Stock_model->delete_Stock($distributorId, $productId);
		if ($result) {
			$this->response(['status' => REST_Controller::HTTP_OK, 'message' => 'Stock Deleted successfully.'], REST_Controller::HTTP_OK);
		} else {
			$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Delete Stock.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}
