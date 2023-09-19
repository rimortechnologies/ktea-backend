<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Salesrepresentative extends REST_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Salesrepresentative_model');
		$this->load->library('cors');
        $this->cors->setHeaders();
    }
    
	public function index_get($salesrepresentativeId=NULL) {
		if($salesrepresentativeId!=''){
			$salesrepresentative = $this->Salesrepresentative_model->get_salesrepresentative($salesrepresentativeId);
		}
		else{
			$salesrepresentative = $this->Salesrepresentative_model->get_all_Salesrepresentatives();
		}
		$this->response([
			'status' => REST_Controller::HTTP_OK,
			'data' => $salesrepresentative['data']??[],
			'count' => $salesrepresentative['count']??0
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
			$salesrepresentative = $this->Salesrepresentative_model->create_Salesrepresentative($request);
			if ($salesrepresentative == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST,'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($salesrepresentative) {
				$this->response(['status' =>  REST_Controller::HTTP_OK,'data'=>$salesrepresentative,'message' => 'Salesrepresentative Inserted successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'error' => 'Failed to Add Salesrepresentative.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
        }
    }

    public function index_put($salesrepresentativeId) {
		$request = json_decode(file_get_contents('php://input'),true);
        if(!is_array($request)){
            $this->response([
                'status' => REST_Controller::HTTP_BAD_REQUEST,
                'error' => 'Invalid Request Not a valid json'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
        else {
			$salesrepresentative = $this->Salesrepresentative_model->update_Salesrepresentative($salesrepresentativeId,$request);
			if ($salesrepresentative == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST,'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($salesrepresentative) {
				$this->response(['status' => REST_Controller::HTTP_OK,'data'=>$salesrepresentative,'message' => 'Salesrepresentative updated successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'error' => 'Failed to Add Salesrepresentative.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
        }
    }

    public function index_delete($salesrepresentativeId) {
		$result = $this->Salesrepresentative_model->delete_Salesrepresentative($salesrepresentativeId);
		if ($result) {
			$this->response(['status' => REST_Controller::HTTP_OK,'message' => 'Salesrepresentative Deleted successfully.'], REST_Controller::HTTP_OK);
		} else {
			$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'error' => 'Failed to Delete Salesrepresentative.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}
?>