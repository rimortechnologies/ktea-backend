<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Stock_model extends CI_Model
{
	public function get_all_stocks($distributorId)
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
				$stocks = $this->db->get_where('stock', array('stockDistributorId' => $distributorId, 'stockProductId' => $product->id))->row();
				$info['stockCount'] = ($stocks ? $stocks->stockQty : 0);
				$info['returnQty'] = ($stocks ? $stocks->returnQty : 0);
				$productReturn[] = $info;
			}
			$data['data'] = $productReturn;
			$data['count'] = count($productReturn);

			return $data;
		}

		$this->db->select('a.*');
		$this->db->from('stock a');
		if (isset($_GET['distributorId']))
			$this->db->where('stockDistributorId', $_GET['distributorId']);
		$query = $this->db->get();
		if ($query->num_rows() != 0) {
			$results = $query->result();
			$count = $query->num_rows();
			$data = array();
			if (isset($_GET['populate']) && $_GET['populate'] == true) :
				foreach ($results as $result) {
					$distributorQuery = $this->db->get_where('distributor', array('id' => $result->stockDistributorId));
					$distributorInfo = $distributorQuery->row();
					$result->stockDistributorId = $distributorInfo;
					$productQuery = $this->db->get_where('product', array('id' => $result->stockProductId));
					$productInfo = $productQuery->row();
					$result->stockProductId = $productInfo;
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

	public function get_stock($distributorId)
	{
		$this->db->select('a.*');
		$this->db->from('stock a');
		$this->db->where('a.id', $distributorId);
		$query = $this->db->get();
		if ($query->num_rows() != 0) {
			$result = $query->row();
			$count = $query->num_rows();
			if (isset($_GET['populate']) && $_GET['populate'] == true) :
				$data = array();
				$distributorQuery = $this->db->get_where('distributor', array('id' => $result->stockDistributorId));
				$distributorInfo = $distributorQuery->row();
				$result->stockDistributorId = $distributorInfo;
				$productQuery = $this->db->get_where('product', array('id' => $result->stockProductId));
				$productInfo = $productQuery->row();
				$result->stockProductId = $productInfo;
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

	public function get_stock_summary($distributorId)
	{
		$this->db->select('*');
		$this->db->from('product');

		$query = $this->db->get();

		if ($query->num_rows() != 0) {
			$products = $query->result(); // Get all products

			// Initialize counts
			$outOfStockCount = 0;
			$inStockCount = 0;


			foreach ($products as $product) {
				// Check if stock exists for the distributor and product
				$this->db->where('stockDistributorId', $distributorId);
				$this->db->where('stockProductId', $product->id);
				$stockQuery = $this->db->get('stock');

				if ($stockQuery->num_rows() > 0) {
					$stockItem = $stockQuery->row();

					if ($stockItem->stockQty > 0) {
						$inStockCount++;
					} else {
						$outOfStockCount++;
					}
				} else {
					$outOfStockCount++;
				}
			}

			// Calculate total number of products
			$totalProducts = count($products);

			// Prepare the response
			// $data = array();
			$data['outOfStock'] = $outOfStockCount;
			$data['inStock'] = $inStockCount;
			$data['totalProducts'] = $totalProducts;
			// print_r($data);
			return $data;
		} else {
			echo 'error';
			return false;
		}
	}



	public function create_stock($data)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('stockDistributorId', 'Stock Distributor Id ', 'required|max_length[50]');
		$this->form_validation->set_rules('stockProductId', 'Stock Product Id', 'required|max_length[100]');
		$this->form_validation->set_rules('stockQty', 'Stock Qty', 'required|max_length[100]');

		if ($this->form_validation->run() == false) {
			return false; // Validation failed
		}
		$insert['stockDistributorId'] 	= $data['stockDistributorId'];
		$insert['stockProductId'] 		= $data['stockProductId'];
		$insert['stockQty'] 			= $data['stockQty'];


		$existingStock = $this->db->get_where('stock', array('stockDistributorId' => $data['stockDistributorId'], 'stockProductId' => $data['stockProductId']))->row();

		if ($existingStock) {
			// Update existing record
			$currentStockQty = $existingStock->stockQty;
			$stockQtyDifference = $data['stockQty'] - $currentStockQty;

			$this->db->set('initialStock', 'initialStock + ' . $stockQtyDifference, false);
			$this->db->where('id', $data['stockProductId']);
			$this->db->update('product');
			$this->db->where('id', $existingStock->id);
			$this->db->update('stock', $insert);
			if ($this->db->affected_rows() > 0) {
				return $this->get_stock($existingStock->id);
			} else {
				return false;
			}
		} else {
			// Insert new record
			$this->db->set('initialStock', 'initialStock + ' . $data['stockQty'], false);
			$this->db->where('id', $data['stockProductId']);
			$this->db->update('product');

			$insert['id'] = generate_uuid();
			$insert['createdBy'] = getCreatedBy();
			$this->db->insert('stock', $insert);
			if ($this->db->affected_rows() > 0) {
				return $this->get_stock($insert['id']);
			} else {
				return false;
			}
		}
	}

	public function update_stock($stockId, $data)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('stockDistributorId', 'Stock Distributor Id ', 'required|max_length[50]');
		$this->form_validation->set_rules('stockProductId', 'Stock Product Id', 'required|max_length[100]');
		$this->form_validation->set_rules('stockQty', 'Stock Qty', 'required|max_length[100]');

		if ($this->form_validation->run() == false) {
			return false; // Validation failed
		}
		$update['stockDistributorId'] = $data['stockDistributorId'];
		$update['stockProductId'] = $data['stockProductId'];
		$update['stockQty'] = $data['stockQty'];
		$this->db->select('stockQty, stockProductId');
		$this->db->where('id', $stockId);
		$query = $this->db->get('stock');
		$currentStockQty = $query->row()->stockQty;
		$productId = $query->row()->stockProductId;
		$stockQtyDifference = $data['stockQty'] - $currentStockQty;

		$this->db->set('initialStock', 'initialStock + ' . $stockQtyDifference, false);
		$this->db->where('id', $productId);
		$this->db->update('product');


		$this->db->where('id', $stockId);
		$this->db->update('stock', $update);
		return $this->get_stock($stockId);
	}

	public function clear_return($data)
	{
		try {
			$this->load->library('form_validation');
			$this->form_validation->set_data($data);
			$this->form_validation->set_rules('stockDistributorId', 'Stock Distributor Id ', 'required|max_length[50]');
			$this->form_validation->set_rules('stockProductId', 'Stock Product Id', 'required|max_length[100]');

			if ($this->form_validation->run() == false) throw new Exception(validation_errors());

			$this->db->trans_start(); // Start transaction

			$update['stockDistributorId'] = $data['stockDistributorId'];
			$update['stockProductId'] = $data['stockProductId'];

			print_r($update);
			$this->db->set('returnQty', 0);
			$this->db->set('returnClearedOn', 'NOW()', false);
			$this->db->where('stockDistributorId', $update['stockDistributorId']);
			$this->db->where('stockProductId', $update['stockProductId']);
			$this->db->update('stock', $update);

			// Get the updated item's id
			$updatedItemId = $this->db->insert_id();

			$this->db->trans_complete();
			// Return the updated item's id
			return $updatedItemId;
		} catch (Exception $e) {
			// echo $e->getMessage();
			log_message('error', 'Error in create_teams: ' . $e->getMessage());
			$this->db->trans_rollback();
			throw $e;
			return false;
		}
	}


	public function delete_stock($distributorId, $productId)
	{
		$stock  = $this->db->get_where('stock', array('stockDistributorId' => $distributorId, 'stockProductId' => $productId))->row();
		if ($stock) {


			$this->db->select('stockQty, stockProductId');
			$this->db->where('id', $stock->id);
			$query = $this->db->get('stock');
			$currentStockQty = $query->row()->stockQty;
			$productId = $query->row()->stockProductId;


			$this->db->set('initialStock', 'initialStock - ' . $currentStockQty, false);
			$this->db->where('id', $productId);
			$this->db->update('product');



			$this->db->where('id', $stock->id);
			$this->db->delete('stock');

			if ($this->db->affected_rows() > 0) return true;
			else return FALSE;
		} else return FALSE;
	}
}
