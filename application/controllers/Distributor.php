<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Distributor extends REST_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Distributor_model');
		$this->load->library('cors');
        $this->cors->setHeaders();
    }
    
	public function index_get($distributorId='') {
		if($distributorId!=''){
			$distributors =$this->Distributor_model->get_distributor($distributorId);
		}
		else{
			$distributors = $this->Distributor_model->get_all_distributors();
		}
		$this->response([
			'status' => REST_Controller::HTTP_OK,
			'data' => $distributors['data']??[],
			'count' => $distributors['count']??0
			
				], REST_Controller::HTTP_OK);
		
    }
	public function index_post() {
		$request = json_decode(file_get_contents('php://input'),true);
        if(!is_array($request)){
            $this->response([
                'status' => REST_Controller::HTTP_BAD_REQUEST,
                'error' => 'Invalid Request Not a valid json'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
        else {
			$distributorId = $this->Distributor_model->create_Distributor($request);
			if ($distributorId == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST,'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($distributorId) {
				$this->response(['status' => REST_Controller::HTTP_OK,'data'=>$distributorId , 'message' => 'Distributor Inserted successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'error' => 'Failed to Add Distributor.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
        }
    }

    public function index_put($distributorId) {
		$request = json_decode(file_get_contents('php://input'),true);
        if(!is_array($request)){
            $this->response([
                'status' => REST_Controller::HTTP_BAD_REQUEST,
                'error' => 'Invalid Request Not a valid json'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
        else {
			$distributorId = $this->Distributor_model->update_Distributor($distributorId,$request);
			if ($distributorId == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST,'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($distributorId) {
				$this->response(['status' => REST_Controller::HTTP_OK, 'data'=>$distributorId , 'message' => 'Distributor updated successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'error' => 'Failed to Add Distributor.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
        }
    }

    public function index_delete($distributorId) {
		$result = $this->Distributor_model->delete_Distributor($distributorId);
		if ($result) {
			$this->response(['status' => REST_Controller::HTTP_OK,'message' => 'Distributor Deleted successfully.'], REST_Controller::HTTP_OK);
		} else {
			$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'error' => 'Failed to Delete Distributor.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
		}
	
	}
}
?>