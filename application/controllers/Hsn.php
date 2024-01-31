<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Hsn extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Hsn_model');
        $this->load->library('cors');
        $this->cors->setHeaders();
    }

    public function index_get($categoryId = '')
    {
        if ($categoryId != '') {
            $categories = $this->Hsn_model->get_hsn($categoryId);
        } else {
            $categories = $this->Hsn_model->get_all_hsn();
        }
        $this->response([
            'status' => REST_Controller::HTTP_OK,
            'data' => $categories['data'] ?? [],
            'count' => $categories['count'] ?? 0
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
            $categoryId = $this->Hsn_model->create_hsn($request);
            if ($categoryId == false) {
                $errors = $this->form_validation->error_array();
                $this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
            } elseif ($categoryId) {
                $this->response(['status' => REST_Controller::HTTP_OK, 'data' => $categoryId, 'message' => 'HSN Inserted successfully.'], REST_Controller::HTTP_OK);
            } else {
                $this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Add HSN.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function index_put($categoryId)
    {
        $request = json_decode(file_get_contents('php://input'), true);
        if (!is_array($request)) {
            $this->response([
                'status' => REST_Controller::HTTP_BAD_REQUEST,
                'error' => 'Invalid Request Not a valid json'
            ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $categoryId = $this->Hsn_model->update_hsn($categoryId, $request);
            if ($categoryId == false) {
                $errors = $this->form_validation->error_array();
                $this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $errors], REST_Controller::HTTP_BAD_REQUEST);
            } elseif ($categoryId) {
                $this->response(['status' => REST_Controller::HTTP_OK, 'data' => $categoryId, 'message' => 'HSN updated successfully.'], REST_Controller::HTTP_OK);
            } else {
                $this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to Add HSN.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function index_delete($categoryId)
    {
        $result = $this->Hsn_model->delete_hsn($categoryId);
        if ($result == true) {
            $this->response(['status' => REST_Controller::HTTP_OK, 'message' => 'HSN Deleted successfully.'], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR, 'error' => 'Failed to HSN category.'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
