<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Route extends REST_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Route_model');
		$this->load->library('cors');
        $this->cors->setHeaders();
    }
    
	public function index_get($routeId='') {
		if($routeId!='')$routes =$this->Route_model->get_route($routeId);
		else $routes = $this->Route_model->get_all_routes();

		
		$this->response([
			'status' => REST_Controller::HTTP_OK,
			'data' => $routes['data']??[],
			'count' => $routes['count']??0
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
			$result = $this->Route_model->create_route($request);
			if ($result == FALSE) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST,'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($result) {
				$this->response(['status' => REST_Controller::HTTP_OK,'data'=>$result,'message' => 'Route Inserted successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'error' => 'Failed to Add Route.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
        }
    }

    public function index_put($routeId) {
		$request = json_decode(file_get_contents('php://input'),true);
        if(!is_array($request)){
            $this->response([
                'status' => REST_Controller::HTTP_BAD_REQUEST,
                'message' => 'Invalid Request Not a valid json'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
        else {
			$result = $this->Route_model->update_route($routeId,$request);
			if ($result == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST,'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($result) {
				$this->response(['status' => REST_Controller::HTTP_OK,'data'=>$result,'message' => 'Route updated successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'error' => 'Failed to Add Route.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
        }
    }

    public function index_delete($routeId) {
		$result = $this->Route_model->delete_route($routeId);
		if ($result==true) {
			$this->response(['status' => REST_Controller::HTTP_OK,'message' => 'Route Deleted successfully.'], REST_Controller::HTTP_OK);
		} else {
			$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'error' => 'Failed to Delete Route.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
		}
		
	}
}
?>