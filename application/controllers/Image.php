<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Image extends REST_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Image_model');
		$this->load->library('cors');
        $this->cors->setHeaders();
    }
    
	
	// public function index_post() {
		
       
	// 		$Image = $this->Image_model->create_Image();
	// 		if ($Image == false) {
	// 			$errors = $this->form_validation->error_array();
	// 			$this->response(['status' => REST_Controller::HTTP_BAD_REQUEST,'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
	// 		} elseif ($Image) {
	// 			$this->response(['status' => REST_Controller::HTTP_OK,'data'=>$Image,'message' => 'Image Created successfully.'], REST_Controller::HTTP_OK);
	// 		} else {
	// 			$this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'error' => 'Failed to Add Image.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
	// 		}
        
    // }

	public function index_post()
{
    $imageData = $this->post('image'); // Assuming you are sending the image data in the 'image' POST parameter
    
    $Image = $this->Image_model->create_Image($imageData);
    
    if ($Image == false) {
        $errors = $this->form_validation->error_array();
        $this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
    } elseif ($Image) {
        $this->response(['status' => REST_Controller::HTTP_OK, 'data' => $Image, 'message' => 'Image Created successfully.'], REST_Controller::HTTP_OK);
    } else {
        $this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Add Image.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
    }
}

  
  
}
