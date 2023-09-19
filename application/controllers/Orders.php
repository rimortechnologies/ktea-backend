<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Orders extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Orders_model');
		$this->load->library(['jwt']);
		$this->load->library('cors');
		$this->cors->setHeaders();
	}
	public function index_get($orderId = '')
	{

		if ($orderId != '') {
			$orders = $this->Orders_model->get_order($orderId);
		} else {
			$orders = $this->Orders_model->get_all_orders();
		}
		$this->response([
			'status' => REST_Controller::HTTP_OK,
			'data' => $orders['data'] ?? [],
			'count' => $orders['count'] ?? 0
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
			$result = $this->Orders_model->create_Order($request);
			if ($result == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($result) {
				$this->response(['status' => REST_Controller::HTTP_OK, 'data' => $result, 'message' => 'Order Inserted successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Add Order.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		}
	}


	public function order_cancel_post()
	{
		$request = json_decode(file_get_contents('php://input'), true);
		if (!is_array($request)) {
			$this->response([
				'status' => REST_Controller::HTTP_BAD_REQUEST,
				'error' => 'Invalid Request Not a valid json'
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			$result = $this->Orders_model->update_Order_status($request, 4);
			if ($result == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($result) {
				$this->response(['status' => REST_Controller::HTTP_OK, 'data' => $result, 'message' => 'Order Updated successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Updated Order.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		}
	}


	// public function order_approve_post()
	// {
	// 	$request = json_decode(file_get_contents('php://input'), true);
	// 	if (!is_array($request)) {
	// 		$this->response([
	// 			'status' => REST_Controller::HTTP_BAD_REQUEST,
	// 			'error' => 'Invalid Request Not a valid json'
	// 		], REST_Controller::HTTP_BAD_REQUEST);
	// 	} else {
	// 		$result = $this->Orders_model->update_Order_status($request, 2);
	// 		if ($result == false) {
	// 			$errors = $this->form_validation->error_array();
	// 			$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
	// 		} elseif ($result) {
	// 			$this->response(['status' => REST_Controller::HTTP_OK, 'data' => $result, 'message' => 'Order Updated successfully.'], REST_Controller::HTTP_OK);
	// 		} else {
	// 			$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Updated Order.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
	// 		}
	// 	}
	// }


	public function accept_order_post($orderId)
	{
		$request = json_decode(file_get_contents('php://input'), true);
		if (!is_array($request)) {
			$this->response([
				'status' => REST_Controller::HTTP_BAD_REQUEST,
				'error' => 'Invalid Request Not a valid json'
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			$result = $this->Orders_model->accept_order($orderId, $request);
			if ($result == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($result) {
				$this->response(['status' => REST_Controller::HTTP_OK, 'data' => $result, 'message' => 'Order Updated successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Updated Order.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		}
	}

	public function my_analytics_get($type = '', $repId = '')
	{
		$result = $this->Orders_model->get_order_analytics($type, $repId);
		if ($result == false) {
			$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => 'Invalid Request data'], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			$this->response([
				'status' => REST_Controller::HTTP_OK,
				'data' => $result
			], REST_Controller::HTTP_OK);
		}
	}


	public function index_put($orderId)
	{
		$request = json_decode(file_get_contents('php://input'), true);
		if (!is_array($request)) {
			$this->response([
				'status' => FALSE,
				'message' => 'Invalid Request Not a valid json'
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			$result = $this->Orders_model->update_Order($orderId, $request);
			if ($result == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($result) {
				$this->response(['status' => REST_Controller::HTTP_OK, 'data' => $result, 'message' => 'Order updated successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'message' => 'Failed to Add Order.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		}
	}

	public function index_delete($orderId)
	{
		$result = $this->Orders_model->delete_Order($orderId);
		if ($result) {
			$this->response(['status' => REST_Controller::HTTP_OK, 'message' => 'Order Deleted successfully.'], REST_Controller::HTTP_OK);
		} else {
			$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'message' => 'Failed to Delete Order.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}
