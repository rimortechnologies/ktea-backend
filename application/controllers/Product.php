<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Product extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Product_model');
		$this->load->library('cors');
		$this->cors->setHeaders();
	}

	public function index_get($productId = '')
	{
		if ($productId != '') {
			$products = $this->Product_model->get_product($productId);
		} else {
			$products = $this->Product_model->get_all_products();
		}
		$this->response([
			'status' => REST_Controller::HTTP_OK,
			'data' => $products['data'] ?? [],
			'count' => $products['count'] ?? 0,
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
			$result = $this->Product_model->create_Product($request);
			if ($result == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($result) {
				$this->response(['status' => REST_Controller::HTTP_OK, 'data' => $result, 'message' => 'Product Inserted successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Add Product.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		}
	}

	public function index_put($productId)
	{
		$request = json_decode(file_get_contents('php://input'), true);
		if (!is_array($request)) {
			$this->response([
				'status' => REST_Controller::HTTP_BAD_REQUEST,
				'message' => 'Invalid Request Not a valid json'
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			try {
				$result = $this->Product_model->update_Product($productId, $request);
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
					'error' => [$errorMessage]
				], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		}
	}

	public function index_delete($productId)
	{
		$result = $this->Product_model->delete_Product($productId);
		if ($result) {
			$this->response(['status' => REST_Controller::HTTP_OK, 'message' => 'Product Deleted successfully.'], REST_Controller::HTTP_OK);
		} else {
			$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Delete Product.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}
