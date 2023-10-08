<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Product_model extends CI_Model {
    public function get_all_products() {
		
		
		$this->db->select('a.*');
		$this->db->from('product a'); 
		
		if(isset($_GET['categoryId']))
			$this->db->where('productCategory',$_GET['categoryId']);
		if(isset($_GET['productIsActive']))
			$this->db->where('productIsActive',$_GET['productIsActive']);
		
		
		
			if (isset($_GET['search']) and $_GET['search'] != null) {
				$search  = urldecode($_GET['search']);
			$filters = [
				'productSku' => $search,
				'productName' => $search,
				'productIsActive'=>$search
				
			];
			}
			
			if (isset($filters) && !empty($filters)) {
				$this->db->group_Start();
				$this->db->or_like($filters);
				$this->db->group_End();
			}
	
	
			$limit = (isset($_GET['limit']) && is_numeric($_GET['limit']) && !empty(trim($_GET['limit']))) ? $_GET['limit'] : 10;
			$offset = (isset($_GET['offset']) && is_numeric($_GET['offset']) && !empty(trim($_GET['offset']))) ? $_GET['offset'] : 0;

			if ($limit != null || $offset != null) {
				$this->db->limit($limit, $offset);
			}




		$query = $this->db->get(); 
		if($query->num_rows() != 0){
			$results = $query->result();
			$count = $query->num_rows();
			 $data = array();
			 if(isset($_GET['populate']) && $_GET['populate']==true ):
				 foreach($results as $result) {
					$categoryQuery = $this->db->get_where('category', array('id' => $result->productCategory));
					$categoryInfo = $categoryQuery->row();
					$result->productCategory = $categoryInfo;
					$data['data'][] = $result;
				 }
				 $data['count']=$count;
				return $data;
			endif;
			$data['data']=$results;
			$data['count']=$count;
			return $data;
		}
		else return false;
    }
    
    public function get_product($productId) {
		$this->db->select('a.*');
		$this->db->from('product a');   
        $this->db->where('a.id',$productId);         
		$query = $this->db->get(); 
		if($query->num_rows() != 0){
			$result = $query->row();
			$count = $query->num_rows();
			 $data = array();
			 if(isset($_GET['populate']) && $_GET['populate']==true ):
					$categoryQuery = $this->db->get_where('category', array('id' => $result->productCategory));
					$categoryInfo = $categoryQuery->row();
					$result->productCategory = $categoryInfo;
					$data['data'] = $result;
				 $data['count']=$count;
				return $data;
			endif;
			$data['data']=$result;
			$data['count']=$count;
			return $data;
		}
		else return false;
    }
    
    public function create_product($data) {
        $this->load->library('form_validation');
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('productSku', 'Product SKU', 'required|max_length[50]|is_unique[product.productSku]');
        $this->form_validation->set_rules('productName', 'Product Name', 'required|max_length[100]');
        $this->form_validation->set_rules('productReward', 'Product Reward', 'required|max_length[100]');
        $this->form_validation->set_rules('productPrice', 'Product Price', 'required|max_length[100]');
        $this->form_validation->set_rules('productIsActive', 'Product Is Active', 'required|max_length[100]');
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
		$insert['productIsActive'] = $data['productIsActive'];
		$insert['productCategory'] = $data['productCategory'];
		$insert['productUnit'] = $data['productUnit'];
		$insert['initialStock'] = isset($data['initialStock'])?$data['initialStock']:0;
		$insert['productImage'] = $data['productImage'];
		$insert['productWeight'] = $data['productWeight'];
        $this->db->insert('product', $insert);
		return $this->get_product($insert['id']);
    }
    public function get_product_by_sku($productSku)
    {
        $query = $this->db->get_where('product', array('productSku' => $productSku));
        return $query->row();
    }
    public function update_product($productId,$data) {
        $this->load->library('form_validation');
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('productSku', 'Product SKU', 'required|max_length[50]');
        $this->form_validation->set_rules('productName', 'Product Name', 'required|max_length[100]');
        $this->form_validation->set_rules('productReward', 'Product Reward', 'required|max_length[100]');
        $this->form_validation->set_rules('productPrice', 'Product Price', 'required|max_length[100]');
        $this->form_validation->set_rules('productIsActive', 'Product Is Active', 'required|max_length[100]');
		$this->form_validation->set_rules('productCategory', 'Product category', 'required|max_length[100]');
		$this->form_validation->set_rules('productUnit', 'Product Unit', 'required|max_length[100]');
		$this->form_validation->set_rules('productWeight', 'Product Weight', 'required|max_length[100]');
        
        if ($this->form_validation->run() == false) return false; // Validation failed
		$existingProduct = $this->get_product($productId);
		if ($existingProduct && $existingProduct['data']->productSku != $data['productSku']) {
			$this->form_validation->set_rules('productSku', 'Product SKU', 'required|max_length[50]|is_unique[product.productSku]');
			if ($this->form_validation->run() == false) return false; // Validation failed
		} 
		$update['productSku'] = $data['productSku'];
		$update['productName'] = $data['productName'];
		$update['productReward'] = $data['productReward'];
		$update['productPrice'] = $data['productPrice'];
		$update['productIsActive'] = $data['productIsActive'];
		$update['productCategory'] = $data['productCategory'];
		$update['productUnit'] = $data['productUnit'];
		$update['productImage'] = $data['productImage'];
		$update['productWeight'] = $data['productWeight'];
		//if(isset($data['productImage'])){
			//$imagedata = explode(';base64,', $data['productImage']);
			//$update['productImage']=uploadImage($imagedata,'product',$productId);
		//}
        $this->db->where('id', $productId);
        $this->db->update('product', $update);
		return $this->get_product($productId);
    }
    
    public function delete_product($productId) {
		$product  = $this->get_product($productId);
		if($product){
			if($this->db->get_where('stock  ', array('stockProductId' => $productId))->row()) return FALSE;
			if($this->db->get_where('orderItem   ', array('itemProductId' => $productId))->row()) return FALSE;
			if(isset($product->productImage)):
				if($product->productImage!=null)
					unlink('uploads/product/'.$product->productImage);
			endif;
            $this->db->where('id', $productId);
            $this->db->delete('product');
			
			if($this->db->affected_rows() > 0)return true;
			else return FALSE;
		}
		else return FALSE;
    }
}
