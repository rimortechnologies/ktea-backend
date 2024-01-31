<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Hsn_model extends CI_Model
{

    public function get_all_hsn()
    {



        if (isset($_GET['search']) and $_GET['search'] != null) {

            $search  = urldecode($_GET['search']);
            $filters = [
                'hsnName' => $search,
            ];
        }

        if (isset($filters) && !empty($filters)) {
            $this->db->group_Start();
            $this->db->or_like($filters);
            $this->db->group_End();
        }

        $this->db->where('deleted', false);

        $limit = (isset($_GET['limit']) && is_numeric($_GET['limit']) && !empty(trim($_GET['limit']))) ? $_GET['limit'] : 10;
        $offset = (isset($_GET['offset']) && is_numeric($_GET['offset']) && !empty(trim($_GET['offset']))) ? $_GET['offset'] : 0;

        if ($limit != null || $offset != null) {
            $this->db->limit($limit, $offset);
        }


        $query = $this->db->get('hsn');
        $data['data'] = $query->result();
        $data['count'] = $query->num_rows();
        return $data;
    }

    public function get_hsn($hsnId)
    {
        $query = $this->db->get_where('hsn', array('id' => $hsnId));
        $data['data'] = $query->row();
        $data['count'] = $query->num_rows();
        return $data;
    }

    public function create_hsn($data)
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('hsnName', 'HSN Name', 'required|max_length[100]|is_unique[hsn.hsnName]');
        if ($this->form_validation->run() == false)  return FALSE;
        $insert = [
            'id' => generate_uuid(),
            'hsnName' => $data['hsnName']
        ];
        $this->db->insert('hsn', $insert);
        return $this->get_hsn($insert['id']);
    }
    public function get_hsn_by_name($name)
    {
        $query = $this->db->get_where('hsn', array('hsnName' => $name));
        return $query->row();
    }
    public function update_hsn($hsnId, $data)
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('hsnName', 'HSN Name', 'required|max_length[100]');
        if ($this->form_validation->run() == FALSE)  return FALSE;
        $existingHsn = $this->get_hsn_by_name($data['hsnName']);

        if ($existingHsn && $existingHsn->id != $hsnId) {
            $this->form_validation->set_rules('hsnName', 'HSN Name', 'required|max_length[100]|is_unique[hsn.hsnName]');
            if ($this->form_validation->run() == false) return false;
        }
        $update = [
            'hsnName' => $data['hsnName']
        ];

        if ($this->form_validation->run() == false)  return FALSE; // Validation failed
        $this->db->where('id', $hsnId);
        $this->db->update('hsn', $update);
        return $this->get_hsn($hsnId);
    }

    public function delete_hsn($hsnId)
    {

        $hsn  = $this->get_hsn($hsnId);
        if ($hsn) {
            if ($this->db->get_where('product', array('productHsn' => $hsnId))->row()) return FALSE;


            $this->db->where('id', $hsnId);
            $this->db->delete('hsn');
            if ($this->db->affected_rows() > 0) return true;
            else return FALSE;
        } else return FALSE;
    }
}
