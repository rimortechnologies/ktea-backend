<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Orderitem extends REST_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Orderitem_model');
		$this->load->library('cors');
        $this->cors->setHeaders();
    }
    
	public function getOrderitems_get() {
		$cities = $this->Orderitem_model->get_all_Orderitems();
		$this->response([
			'status' => TRUE,
			'data' => $cities
				], REST_Controller::HTTP_OK);
		
    }
	public function insertOrderitem_post() {
		$request = json_decode(file_get_contents('php://input'),true);
        if(!is_array($request)){
            $this->response([
                'status' => FALSE,
                'message' => 'Invalid Request Not a valid json'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
        else {
			$order = $this->Orderitem_model->create_Orderitem($request);
			if ($order == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST,'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($order) {
				$this->response(['status' => TRUE,'data'=>$order ,'message' => 'Order item Inserted successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => FALSE,'message' => 'Failed to Add Order item.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
        }
    }

    public function updateOrderitem_put() {
		$request = json_decode(file_get_contents('php://input'),true);
        if(!is_array($request)){
            $this->response([
                'status' => FALSE,
                'message' => 'Invalid Request Not a valid json'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
        else {
			$order = $this->Orderitem_model->update_Orderitem($request);
			if ($order == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST,'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($order) {
				$this->response(['status' => TRUE,'data'=>$order ,'message' => 'Order item updated successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => FALSE,'message' => 'Failed to Add Order item.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
        }
    }

    public function deleteOrderitem_delete() {
		$request = json_decode(file_get_contents('php://input'),true);
		if(!is_array($request)){
            $this->response([
                'status' => FALSE,
                'message' => 'Invalid Request Not a valid json'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
		else {
			$result = $this->Orderitem_model->delete_Orderitem($request);
			if ($result == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST,'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($result) {
				$this->response(['status' => TRUE,'message' => 'Order item Deleted successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => FALSE,'message' => 'Failed to Delete Order item.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		}
	}
}
?>