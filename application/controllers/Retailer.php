<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Retailer extends REST_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Retailer_model');
		$this->load->library('cors');
        $this->cors->setHeaders();
    }
    
	public function index_get($retailerId='') {
		if($retailerId!=''){
			$retailers =$this->Retailer_model->get_retailer($retailerId);
		}
		else{
			$retailers = $this->Retailer_model->get_all_Retailers();
		}
		$this->response([
			'status' => REST_Controller::HTTP_OK,
			'data' => $retailers['data']??[],
			'count' => $retailers['count']??0
				], REST_Controller::HTTP_OK);
		
    }
	public function index_post() {
		$request = json_decode(file_get_contents('php://input'),true);
        if(!is_array($request)){
            $this->response([
                'error' => REST_Controller::HTTP_BAD_REQUEST,
                'message' => 'Invalid Request Not a valid json'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
        else {
			$result = $this->Retailer_model->create_Retailer($request);
			if ($result == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST,'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($result) {
				$this->response(['status' => REST_Controller::HTTP_OK,'data'=>$result,'message' => 'Retailer Inserted successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'error' => 'Failed to Add Retailer.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
        }
    }

    public function index_put($retailerId) {
		$request = json_decode(file_get_contents('php://input'),true);
        if(!is_array($request)){
            $this->response([
                'status' => REST_Controller::HTTP_BAD_REQUEST,
                'error' => 'Invalid Request Not a valid json'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
        else {
			$result = $this->Retailer_model->update_Retailer($retailerId,$request);
			if ($result == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST,'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($result) {
				$this->response(['status' => REST_Controller::HTTP_OK,'data'=>$result,'message' => 'Retailer updated successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'error' => 'Failed to Add Retailer.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
        }
    }

    public function index_delete($retailerId) {
		$result = $this->Retailer_model->delete_Retailer($retailerId);
		if ($result) {
			$this->response(['status' => REST_Controller::HTTP_OK,'message' => 'Retailer Deleted successfully.'], REST_Controller::HTTP_OK);
		} else {
			$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'error' => 'Failed to Delete Retailer.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
		}
	
	}
}
?>