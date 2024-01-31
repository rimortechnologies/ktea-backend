<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Margin_model extends CI_Model
{
    public function get_all_margin($distributorId)
    {
        if ($distributorId != '') {
            $productReturn = [];
            $query = $this->db->get('product');
            $products = $query->result();

            foreach ($products as $product) {
                $info['id'] = $product->id;
                $info['productName'] = $product->productName;
                $info['productImage'] = $product->productImage;
                $info['productSku'] = $product->productSku;
                $info['productPrice'] = $product->productPrice;
                $info['productWeight'] = $product->productWeight;
                $margins = $this->db->get_where('margin', array('marginDistributorId' => $distributorId, 'marginProductId' => $product->id))->row();
                $info['marginAmount'] = ($margins ? $margins->marginAmount : 0);
                $productReturn[] = $info;
            }
            $data['data'] = $productReturn;
            $data['count'] = count($productReturn);

            return $data;
        }

        $this->db->select('a.*');
        $this->db->from('margin a');
        if (isset($_GET['distributorId']))
            $this->db->where('marginDistributorId', $_GET['distributorId']);
        $query = $this->db->get();
        if ($query->num_rows() != 0) {
            $results = $query->result();
            $count = $query->num_rows();
            $data = array();
            if (isset($_GET['populate']) && $_GET['populate'] == true) :
                foreach ($results as $result) {
                    $distributorQuery = $this->db->get_where('distributor', array('id' => $result->marginDistributorId));
                    $distributorInfo = $distributorQuery->row();
                    $result->marginDistributorId = $distributorInfo;
                    $productQuery = $this->db->get_where('product', array('id' => $result->marginProductId));
                    $productInfo = $productQuery->row();
                    $result->marginProductId = $productInfo;
                    $data['data'][] = $result;
                }
                $data['count'] = $count;
                return $data;
            endif;
            $data['data'] = $results;
            $data['count'] = $count;
            return $data;
        } else {
            return FALSE;
        }
    }

    public function get_margin($distributorId)
    {
        $this->db->select('a.*');
        $this->db->from('margin a');
        $this->db->where('a.id', $distributorId);
        $query = $this->db->get();
        if ($query->num_rows() != 0) {
            $result = $query->row();
            $count = $query->num_rows();
            if (isset($_GET['populate']) && $_GET['populate'] == true) :
                $data = array();
                $distributorQuery = $this->db->get_where('distributor', array('id' => $result->marginDistributorId));
                $distributorInfo = $distributorQuery->row();
                $result->marginDistributorId = $distributorInfo;
                $productQuery = $this->db->get_where('product', array('id' => $result->marginProductId));
                $productInfo = $productQuery->row();
                $result->marginProductId = $productInfo;
                unset($result->password);
                $data['data'] = $result;
                $data['count'] = $count;
                return $data;
            endif;
            $data['data'] = $result;
            $data['count'] = $count;
            return $data;
        } else return false;
    }

    public function create_margin($data)
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('marginDistributorId', 'Margin Distributor Id ', 'required|max_length[50]');
        $this->form_validation->set_rules('marginProductId', 'Margin Product Id', 'required|max_length[100]');
        $this->form_validation->set_rules('marginAmount', 'Margin Amount', 'required|max_length[100]');

        if ($this->form_validation->run() == false) {
            throw new Exception(validation_errors()); // Validation failed
        }
        $insert['marginDistributorId']     = $data['marginDistributorId'];
        $insert['marginProductId']         = $data['marginProductId'];
        $insert['marginAmount']             = $data['marginAmount'];


        $existingMargin = $this->db->get_where('margin', array('marginDistributorId' => $data['marginDistributorId'], 'marginProductId' => $data['marginProductId']))->row();

        if ($existingMargin) {
            // Update existing record
            // $currentMarginAmount = $existingMargin->stockQty;
            // $stockQtyDifference = $data['stockQty'] - $currentMarginAmount;

            // $this->db->where('id', $data['marginProductId']);
            // $this->db->update('product');
            $this->db->set('marginAmount', $insert['marginAmount'], false);
            $this->db->where('id', $existingMargin->id);
            $this->db->update('margin', $insert);
            if ($this->db->affected_rows() > 0) {
                return $this->get_margin($existingMargin->id);
            } else {
                throw new \Exception('Product for this distributor not found');
            }
        } else {
            // Insert new record
            // $this->db->set('marginAmount', $data['marginAmount'], false);
            // $this->db->where('id', $data['marginProductId']);
            // $this->db->update('margin');

            $insert['id'] = generate_uuid();
            // $insert['createdBy'] = getCreatedBy();
            $this->db->insert('margin', $insert);
            if ($this->db->affected_rows() > 0) {
                return $this->get_margin($insert['id']);
            } else {
                throw new \Exception('Product for this distributor not created');
            }
        }
    }
}
