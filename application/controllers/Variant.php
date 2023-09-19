<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Variant extends REST_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Variant_model');
		$this->load->library('cors');
        $this->cors->setHeaders();
    }
    
	public function index_get($variantId='') {
		if($variantId!=''){
			$variants =$this->Variant_model->get_variant($variantId);
		}
		else{
			$variants = $this->Variant_model->get_all_variants();
		}
		$this->response([
			'status' => REST_Controller::HTTP_OK,
			'data' => $variants??[]
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
			$result = $this->Variant_model->create_Variant($request);
			if ($result == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST,'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($result) {
				$this->response(['status' => TRUE,'data'=>$result,'message' => 'Variant Inserted successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => FALSE,'error' => 'Failed to Add Variant.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
        }
    }

    public function index_put($variantId) {
		$request = json_decode(file_get_contents('php://input'),true);
        if(!is_array($request)){
            $this->response([
                'status' => REST_Controller::HTTP_BAD_REQUEST,
                'error' => 'Invalid Request Not a valid json'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
        else {
			$result = $this->Variant_model->update_Variant($variantId,$request);
			if ($result == false) {
				$errors = $this->form_validation->error_array();
				$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST,'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
			} elseif ($result) {
				$this->response(['status' => REST_Controller::HTTP_OK,'data'=>$result,'message' => 'Variant updated successfully.'], REST_Controller::HTTP_OK);
			} else {
				$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'error' => 'Failed to Update Variant.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
        }
    }

    public function index_delete($variantId) {
		$result = $this->Variant_model->delete_Variant($variantId);
		if ($result) {
			$this->response(['status' => REST_Controller::HTTP_OK,'message' => 'Variant Deleted successfully.'], REST_Controller::HTTP_OK);
		} else {
			$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'error' => 'Failed to Delete Variant.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}
?>