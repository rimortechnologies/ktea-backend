<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Category extends REST_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Category_model');
		$this->load->library('cors');
        $this->cors->setHeaders();
    }
    
	public function index_get($categoryId='') {
		if($categoryId!=''){
			$categories =$this->Category_model->get_category($categoryId);
		}
		else{
			$categories = $this->Category_model->get_all_categories();
		}
		$this->response([
			'status' => REST_Controller::HTTP_OK,
			'data' => $categories['data']??[],
			'count' => $categories['count']??0
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
			$categoryId = $this->Category_model->create_category($request);
			if ($categoryId == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST,'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($categoryId) {
				$this->response(['status' => REST_Controller::HTTP_OK,'data'=>$categoryId,'message' => 'Category Inserted successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'error' => 'Failed to Add category.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
        }
    }

    public function index_put($categoryId) {
		$request = json_decode(file_get_contents('php://input'),true);
        if(!is_array($request)){
            $this->response([
                'status' => REST_Controller::HTTP_BAD_REQUEST,
                'error' => 'Invalid Request Not a valid json'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
        else {
			$categoryId = $this->Category_model->update_category($categoryId,$request);
			if ($categoryId == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST,'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($categoryId) {
				$this->response(['status' => REST_Controller::HTTP_OK,'data'=>$categoryId,'message' => 'Category updated successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'error' => 'Failed to Add category.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
        }
    }

    public function index_delete($categoryId) {
		$result = $this->Category_model->delete_category($categoryId);
		if ($result==true) {
			$this->response(['status' => REST_Controller::HTTP_OK,'message' => 'Category Deleted successfully.'], REST_Controller::HTTP_OK);
		} else {
			$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'error' => 'Failed to Delete category.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
		}	
	}
}
?>