<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Category_model extends CI_Model
{

	public function get_all_categories()
	{



		if (isset($_GET['search']) and $_GET['search'] != null) {

			$search  = urldecode($_GET['search']);
			$filters = [
				'categoryName' => $search,
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


		$query = $this->db->get('category');
		$data['data'] = $query->result();
		$data['count'] = $query->num_rows();
		return $data;
	}

	public function get_category($categoryId)
	{
		$query = $this->db->get_where('category', array('id' => $categoryId));
		$data['data'] = $query->row();
		$data['count'] = $query->num_rows();
		return $data;
	}

	public function create_category($data)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('categoryName', 'Category Name', 'required|max_length[100]|is_unique[category.categoryName]');
		if ($this->form_validation->run() == false)  return FALSE;
		$insert = [
			'id' => generate_uuid(),
			'categoryName' => $data['categoryName'],
			'createdBy' => getCreatedBy(),
		];
		$this->db->insert('category', $insert);
		return $this->get_category($insert['id']);
	}
	public function get_category_by_name($categoryName)
	{
		$query = $this->db->get_where('category', array('categoryName' => $categoryName));
		return $query->row();
	}
	public function update_category($categoryId, $data)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('categoryName', 'Category Name', 'required|max_length[100]');
		if ($this->form_validation->run() == FALSE)  return FALSE;
		$existingCategory = $this->get_category_by_name($data['categoryName']);

		if ($existingCategory && $existingCategory->id != $categoryId) {
			$this->form_validation->set_rules('categoryName', 'Category Name', 'required|max_length[100]|is_unique[category.categoryName]');
			if ($this->form_validation->run() == false) return false;
		}
		$update = [
			'categoryName' => $data['categoryName']
		];

		if ($this->form_validation->run() == false)  return FALSE; // Validation failed
		$this->db->where('id', $categoryId);
		$this->db->update('category', $update);
		return $this->get_category($categoryId);
	}

	public function delete_category($categoryId)
	{

		$category  = $this->get_category($categoryId);
		if ($category) {
			if ($this->db->get_where('product', array('productCategory' => $categoryId))->row()) return FALSE;


			$this->db->where('id', $categoryId);
			$this->db->delete('category');
			if ($this->db->affected_rows() > 0) return true;
			else return FALSE;
		} else return FALSE;
	}
}
