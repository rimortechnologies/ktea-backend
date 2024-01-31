<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Product_model extends CI_Model
{
	public function get_all_products()
	{
		$search = isset($_GET['search']) ? urldecode($_GET['search']) : '';

		$countQueryBuilder = clone $this->db;

		$countQueryBuilder->select('COUNT(*) as count')
			->from('product a')
			->join('category', 'category.id = a.productCategory', 'left')
			->join('hsn', 'hsn.id = a.productHsn', 'left')
			->group_start()
			->or_like('a.productName', $search)
			->or_like('a.productSku', $search)
			->or_like('hsn.hsnName', $search)
			->or_like('category.categoryName', $search)
			->or_like('a.productWeight', $search)
			->group_end();

		$num_results = $countQueryBuilder->get()->row()->count;

		$query = $this->db
			->select('a.*')
			->from('product a')
			->join('category', 'category.id = a.productCategory', 'left')
			->join('hsn', 'hsn.id = a.productHsn', 'left')
			->group_start()
			->or_like('a.productName', $search)
			->or_like('a.productSku', $search)
			->or_like('hsn.hsnName', $search)
			->or_like('category.categoryName', $search)
			->or_like('a.productWeight', $search)
			->group_end();

		$sortField = 'a.created_date';
		$orderBy = 'DESC';

		// Sorting logic
		if (isset($_GET['orderBy'])) {
			$orderField = $_GET['orderBy'];
			$orderPrefix = ($orderField[0] === '-') ? 'DESC' : 'ASC';

			switch (ltrim($orderField, '-')) {
				case 'name':
					$sortField = 'a.productName';
					break;
				case 'category':
					$sortField = 'category.categoryName';
					break;
				case 'price':
					$sortField = 'a.productPrice';
					break;
				case 'weight':
					$sortField = 'a.productWeight';
					break;
				case 'reward':
					$sortField = 'a.productReward';
					break;
				case 'tax':
					$sortField = 'a.productTax';
					break;
					// Add more cases as needed
			}

			$orderBy = $orderPrefix;
		}

		// Pagination logic
		$limit = (isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] > 0) ? (int)$_GET['limit'] : null;
		$offset = (isset($_GET['offset']) && is_numeric($_GET['offset']) && $_GET['offset'] >= 0) ? (int)$_GET['offset'] : 0;

		if ($limit !== null) {
			$query->limit($limit, $offset);
		}

		$query->order_by($sortField, $orderBy);

		$results = $query->get();

		if ($results->num_rows() !== 0) {
			$data['data'] = $results->result();
			foreach ($data['data'] as $result) {
				$categoryInfo = $this->db->get_where('category', ['id' => $result->productCategory])->row();
				$hsnInfo = $this->db->get_where('hsn', ['id' => $result->productHsn])->row();
				$result->productCategory = $categoryInfo;
				$result->productHsn = $hsnInfo;
				// Add more data processing as needed
			}
			$data['count'] = $num_results;
			return $data;
		} else {
			return false;
		}
	}


	public function get_product($productId)
	{
		$this->db->select('a.*');
		$this->db->from('product a');
		$this->db->where('a.id', $productId);
		$query = $this->db->get();
		if ($query->num_rows() != 0) {
			$result = $query->row();
			$count = $query->num_rows();
			$data = array();
			if (isset($_GET['populate']) && $_GET['populate'] == true) :
				$categoryQuery = $this->db->get_where('category', array('id' => $result->productCategory));
				$categoryInfo = $categoryQuery->row();
				$result->productCategory = $categoryInfo;
				$hsnQuery = $this->db->get_where('hsn', array('id' => $result->productHsn));
				$hsnInfo = $hsnQuery->row();
				$result->productHsn = $hsnInfo;
				$data['data'] = $result;
				$data['count'] = $count;
				return $data;
			endif;
			$data['data'] = $result;
			$data['count'] = $count;
			return $data;
		} else return false;
	}

	public function create_product($data)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('productSku', 'Product SKU', 'required|max_length[50]|is_unique[product.productSku]');
		$this->form_validation->set_rules('productName', 'Product Name', 'required|max_length[100]');
		$this->form_validation->set_rules('productReward', 'Product Reward', 'required|max_length[100]');
		$this->form_validation->set_rules('productPrice', 'Product Price', 'required|max_length[100]');
		$this->form_validation->set_rules('productIsActive', 'Product Is Active', 'required|max_length[100]');
		$this->form_validation->set_rules('productTax', 'Product Tax', 'required|max_length[100]');
		$this->form_validation->set_rules('productHsn', 'Product HSN', 'required|max_length[100]');
		$this->form_validation->set_rules('productCategory', 'Product Category', 'required|max_length[100]');
		$this->form_validation->set_rules('productUnit', 'Product Unit', 'required|max_length[100]');
		$this->form_validation->set_rules('productWeight', 'Product Weight', 'required|max_length[100]');


		if ($this->form_validation->run() == false) return false; // Validation failed

		$insert['id'] = generate_uuid();
		$insert['createdBy'] = getCreatedBy();
		//if(isset($data['productImage'])){
		//$imagedata = explode(';base64,', $data['productImage']);
		//$insert['productImage']=uploadImage($imagedata,'product',$insert['id']);
		//}
		$insert['productSku'] = $data['productSku'];
		$insert['productName'] = $data['productName'];
		$insert['productReward'] = $data['productReward'];
		$insert['productPrice'] = $data['productPrice'];
		$insert['productTax'] = $data['productTax'];
		$insert['productHsn'] = $data['productHsn'];
		$insert['productIsActive'] = $data['productIsActive'];
		$insert['productCategory'] = $data['productCategory'];
		$insert['productUnit'] = $data['productUnit'];
		$insert['initialStock'] = isset($data['initialStock']) ? $data['initialStock'] : 0;
		if (isset($data['productImage'])) {
			$insert['productImage'] = $data['productImage'];
		}
		$insert['productWeight'] = $data['productWeight'];
		$this->db->insert('product', $insert);
		return $this->get_product($insert['id']);
	}
	public function get_product_by_sku($productSku)
	{
		$query = $this->db->get_where('product', array('productSku' => $productSku));
		return $query->row();
	}

	// Define the callback function
	public function is_unique_update($value, $params)
	{
		list($table, $field) = explode('.', $params, 2);

		// Extract the record ID from the field name
		list($field, $id) = explode('.', $field, 2);

		// Query the database to check uniqueness, excluding the current record
		$query = $this->db->where($field . ' !=', $id)->where($field, $value)->get($table);

		// If there is a matching record, the value is not unique
		return $query->num_rows() === 0;
	}

	public function update_product($productId, $data)
	{
		try {
			$this->load->library('form_validation');
			$this->form_validation->set_data($data);
			$this->form_validation->set_rules('productSku', 'Product SKU', 'required|max_length[50]');
			$this->form_validation->set_rules('productName', 'Product Name', 'required|max_length[100]');
			$this->form_validation->set_rules('productReward', 'Product Reward', 'required|numeric|max_length[100]');
			$this->form_validation->set_rules('productPrice', 'Product Price', 'required|numeric|max_length[100]');
			$this->form_validation->set_rules('productIsActive', 'Product Is Active', 'required|max_length[100]');
			$this->form_validation->set_rules('productHsn', 'Product Hsn', 'required|max_length[100]');
			$this->form_validation->set_rules('productTax', 'Product Tax', 'required|numeric|max_length[100]');
			$this->form_validation->set_rules('productCategory', 'Product category', 'required|max_length[100]');
			$this->form_validation->set_rules('productUnit', 'Product Unit', 'required|max_length[100]');
			$this->form_validation->set_rules('productWeight', 'Product Weight', 'required|numeric|max_length[100]');
			$this->form_validation->set_rules('productSku', 'Product SKU', 'required|max_length[50]');

			if ($this->form_validation->run() == false) throw new Exception(validation_errors());

			$existingProduct = $this->get_product_by_name_or_sku($data['productName'], $data['productSku'], $productId);

			if ($existingProduct) {
				// Product with the same productName or productSku already exists
				throw new Exception('Another product with the same name or SKU already exists.');
			}

			// $this->db->trans_start(); // Start transaction
			// $existingProduct = $this->get_product($productId);
			// if ($existingProduct && $existingProduct['data']->productSku != $data['productSku']) {
			// 	$this->form_validation->set_rules('productSku', 'Product SKU', 'required|max_length[50]|is_unique[product.productSku]');
			// 	if ($this->form_validation->run() == false) return false; // Validation failed
			// }
			$update['productSku'] = $data['productSku'];
			$update['productName'] = $data['productName'];
			$update['productReward'] = $data['productReward'];
			$update['productPrice'] = $data['productPrice'];
			$update['productIsActive'] = $data['productIsActive'];
			$update['productTax'] = $data['productTax'];
			$update['productHsn'] = $data['productHsn'];
			$update['productCategory'] = $data['productCategory'];
			$update['productUnit'] = $data['productUnit'];
			if (isset($data['productImage'])) {
				$update['productImage'] = $data['productImage'];
			}
			$update['productWeight'] = $data['productWeight'];
			$this->db->where('id', $productId);
			$this->db->update('product', $update);
			return $this->get_product($productId);
		} catch (Exception $e) {
			// echo $e->getMessage();
			log_message('error', 'Error in create_teams: ' . $e->getMessage());
			$this->db->trans_rollback();
			throw $e;
			return false;
		}
	}

	private function get_product_by_name_or_sku($productName, $productSku, $excludeProductId)
	{
		$this->db->where('id !=', $excludeProductId);
		$this->db->group_start();
		$this->db->where('productName', $productName);
		$this->db->or_where('productSku', $productSku);
		$this->db->group_end();
		$query = $this->db->get('product');

		return $query->row();
	}

	public function delete_product($productId)
	{
		$product  = $this->get_product($productId);
		if ($product) {
			if ($this->db->get_where('stock  ', array('stockProductId' => $productId))->row()) return FALSE;
			if ($this->db->get_where('orderItem   ', array('itemProductId' => $productId))->row()) return FALSE;
			if (isset($product->productImage)) :
				if ($product->productImage != null)
					unlink('uploads/product/' . $product->productImage);
			endif;
			$this->db->where('id', $productId);
			$this->db->delete('product');

			if ($this->db->affected_rows() > 0) return true;
			else return FALSE;
		} else return FALSE;
	}
}
