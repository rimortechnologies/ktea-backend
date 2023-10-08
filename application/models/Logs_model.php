<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Logs_model extends CI_Model
{
    public function get_order_logs($orderId)
    {
        $this->db->select('a.*');
        $this->db->from('logs a');
        $this->db->where('a.log_type', 'order');
        $this->db->where('a.log_resource', $orderId);
        $query = $this->db->get();
        if ($query->num_rows() != 0) return $query->result_array();
        else return false;
        return $this->db->get('logs')->result();
    }

    public function get_distributor_logs($orderId)
    {
        $this->db->select('a.*');
        $this->db->from('logs a');
        $this->db->where('a.log_type', 'order');
        $this->db->where('a.log_resource', $orderId);
        $query = $this->db->get();
        if ($query->num_rows() != 0) return $query->result_array();
        else return false;
        return $this->db->get('logs')->result();
    }

    public function get_variant($variantId)
    {
        $this->db->select('a.*');
        $this->db->from('lo a');
        $this->db->where('a.id', $variantId);
        $query = $this->db->get();
        if ($query->num_rows() != 0) return $query->row();
        else return false;
    }

    public function create_log($data)
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('variantQty', 'Variant Qty', 'required|max_length[100]');
        $this->form_validation->set_rules('variantProductId', 'Variant Product Id', 'required|max_length[100]');
        $this->form_validation->set_rules('variantUnitId', 'Variant Unit Id', 'required|max_length[100]');
        $this->form_validation->set_rules('variantPrice', 'Variant Price', 'required|max_length[100]');


        if ($this->form_validation->run() == false) {
            return false; // Validation failed
        }
        $insert['variantQty'] = $data['variantQty'];
        $insert['variantProductId'] = $data['variantProductId'];
        $insert['variantUnitId'] = $data['variantUnitId'];
        $insert['variantPrice'] = $data['variantPrice'];
        $insert['createdBy'] = getCreatedBy();
        $insert['id'] = generate_uuid();
        $this->db->insert('variant', $insert);
        return $this->get_variant($insert['id']);
    }

    public function update_variant($variantId, $data)
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('variantQty', 'Variant Qty', 'required|max_length[100]');
        $this->form_validation->set_rules('variantProductId', 'Variant Product Id', 'required|max_length[100]');
        $this->form_validation->set_rules('variantUnitId', 'Variant Unit Id', 'required|max_length[100]');
        $this->form_validation->set_rules('variantPrice', 'Variant Price', 'required|max_length[100]');

        if ($this->form_validation->run() == false) {
            return false; // Validation failed
        }
        $update['variantQty'] = $data['variantQty'];
        $update['variantProductId'] = $data['variantProductId'];
        $update['variantUnitId'] = $data['variantUnitId'];
        $update['variantPrice'] = $data['variantPrice'];

        $this->db->where('id', $variantId);
        $this->db->update('variant', $update);
        return $this->get_variant($variantId);
    }

    public function delete_variant($variantId)
    {
        $variant  = $this->get_variant($variantId);
        if ($variant) {
            $this->db->where('id', $variantId);
            $this->db->delete('variant');

            if ($this->db->affected_rows() > 0) return true;
            else return FALSE;
        } else return FALSE;
    }
}
