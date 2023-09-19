<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Stop extends REST_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Stop_model');
		$this->load->library('cors');
        $this->cors->setHeaders();
    }
    
	public function index_get($stopId='') {
		if($stopId!='')$stops =$this->Stop_model->get_stop($stopId);
		else $stops = $this->Stop_model->get_all_Stops();
		$this->response([
			'status' => REST_Controller::HTTP_OK,
			'data' => $stops['data']??[],
			'count' => $stops['count']??0,
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
			$stopId = $this->Stop_model->create_Stop($request);
			if ($stopId == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST,'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($stopId) {
				$this->response(['status' => REST_Controller::HTTP_OK,'data'=>$stopId,'message' => 'Stop Inserted successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'error' => 'Failed to Add Stop.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
        }
    }

    public function index_put($stopId) {
		$request = json_decode(file_get_contents('php://input'),true);
        if(!is_array($request)){
            $this->response([
                'status' => REST_Controller::HTTP_BAD_REQUEST,
                'error' => 'Invalid Request Not a valid json'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
        else {
			$stopId = $this->Stop_model->update_Stop($stopId,$request);
			if ($stopId == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST,'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($stopId) {
				$this->response(['status' => REST_Controller::HTTP_OK,'data'=>$stopId,'message' => 'Stop updated successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'error' => 'Failed to Add Stop.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
        }
    }

    public function index_delete($stopId) {
		$result = $this->Stop_model->delete_Stop($stopId);
		if ($result) {
			$this->response(['status' => REST_Controller::HTTP_OK,'message' => 'Stop Deleted successfully.'], REST_Controller::HTTP_OK);
		} else {
			$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'error' => 'Failed to Delete Stop.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
		}
	
	}
}
?>