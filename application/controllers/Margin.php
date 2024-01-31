<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Margin extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Margin_model');
        $this->load->library('cors');
        $this->cors->setHeaders();
    }

    public function index_get($distributorId = '')
    {
        $stocks = $this->Margin_model->get_all_margin($distributorId);

        $this->response([
            'status' => REST_Controller::HTTP_OK,
            'data' => $stocks['data'] ?? [],
            'count' => $stocks['count'] ?? 0,
        ], REST_Controller::HTTP_OK);
    }


    public function index_post()
    {
        try {
            $request = json_decode(file_get_contents('php://input'), true);
            if (!is_array($request)) {
                $this->response([
                    'error' => REST_Controller::HTTP_BAD_REQUEST,
                    'message' => 'Invalid Request Not a valid json'
                ], REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $result = $this->Margin_model->create_margin($request);
                $this->response(['status' => REST_Controller::HTTP_OK, 'data' => $result, 'message' => 'Margin Updated'], REST_Controller::HTTP_OK);
            }
        } catch (\Exception $e) {
            $this->response(['status' => REST_Controller::HTTP_BAD_REQUEST, 'error' => $e->getMessage()], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
