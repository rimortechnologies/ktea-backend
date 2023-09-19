<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Orders_model extends CI_Model
{
	// public function get_all_orders()
	// {



	// 	$this->db->select('a.*');
	// 	$this->db->from('orders a');
	// 	if (isset($_GET['retailerId']))
	// 		$this->db->where('orderRetailerId', $_GET['retailerId']);
	// 	if (isset($_GET['repId']))
	// 		$this->db->where('orderSalesRepId', $_GET['repId']);
	// 	if (isset($_GET['distributorId']))
	// 		$this->db->where('orderDistributorId', $_GET['distributorId']);
	// 	if (isset($_GET['orderStatus']))
	// 		$this->db->where('orderStatus', $_GET['orderStatus']);





	// 	$limit = (isset($_GET['limit']) && is_numeric($_GET['limit']) && !empty(trim($_GET['limit']))) ? $_GET['limit'] : 10;
	// 	$offset = (isset($_GET['offset']) && is_numeric($_GET['offset']) && !empty(trim($_GET['offset']))) ? $_GET['offset'] : 0;

	// 	if ($limit != null || $offset != null) {
	// 		$this->db->limit($limit, $offset);
	// 	}



	// 	$query = $this->db->get();




	// 	if ($query->num_rows() != 0) {
	// 		$count = $query->num_rows();
	// 		$results = $query->result();


	// 		// $result->orderItemscount = $orderItemInfo;
	// 		$data = array();
	// 		if (isset($_GET['populate']) && $_GET['populate'] == true) :

	// 			foreach ($results as $result) {
	// 				$retailerQuery = $this->db->get_where('retailer', array('id' => $result->orderRetailerId));
	// 				$retailerInfo = $retailerQuery->row();
	// 				$cityQuery = $this->db->get_where('city', array('id' => $retailerInfo->retailerCity));
	// 				$cityInfo = $cityQuery->row();
	// 				$retailerInfo->retailerCity = $cityInfo;
	// 				$routeQuery = $this->db->get_where('route', array('id' => $retailerInfo->retailerRoute));
	// 				$routeInfo = $routeQuery->row();
	// 				$retailerInfo->retailerRoute = $routeInfo;
	// 				$result->orderRetailerId = $retailerInfo;

	// 				$salesrepQuery = $this->db->get_where('rep', array('id' => $result->orderSalesRepId));
	// 				$repInfo = $salesrepQuery->row();
	// 				$result->orderSalesRepId = $repInfo;

	// 				$distributorQuery = $this->db->get_where('distributor', array('id' => $result->orderDistributorId));
	// 				$distributorInfo = $distributorQuery->row();
	// 				$result->orderDistributorId = $distributorInfo;

	// 				$orderItemQuery = $this->db->get_where('orderitem', array('itemOrderId' => $result->id));
	// 				$orderItemInfo = $orderItemQuery->result();
	// 				$result->orderItems = $orderItemInfo;

	// 				$orderTeamQuery = $this->db->get_where('teams', array('id' => $result->orderSalesRepTeam));
	// 				$orderTeamInfo = $orderTeamQuery->row();
	// 				$result->orderSalesRepTeam = $orderTeamInfo;



	// 				$data['data'][] = $result;
	// 			}
	// 			$data['count'] = $count;
	// 			return $data;

	// 		endif;
	// 		$data['data'] = $results;
	// 		$data['count'] = $count;
	// 		return $data;
	// 	} else {
	// 		return FALSE;
	// 	}
	// }

	public function get_all_orders()
	{
		$this->db->select('a.*');
		$this->db->from('orders a');

		$type = isset($_GET['type']) ? $_GET['type'] : null;

		if (isset($_GET['retailerId'])) {
			$this->db->where('orderRetailerId', $_GET['retailerId']);
		}
		if (isset($_GET['repId'])) {
			if ($type === 'rep') {
				$this->db->where('orderSalesRepId', $_GET['repId']);
			} elseif ($type === 'teamLead') {
				$this->db->where('orderSalesRepTeam', $_GET['repId']);
			}
		}
		if (isset($_GET['distributorId'])) {
			if ($type === 'distributor') {
				$this->db->where('orderDistributorId', $_GET['distributorId']);
			}
		}
		if (isset($_GET['orderStatus'])) {
			$this->db->where('orderStatus', $_GET['orderStatus']);
		}
		// if (isset($_GET['cityId'])) {
		// 	$this->db->where('orderRetailerId', $_GET['retailerId']);
		// }

		// echo $_GET['search'];
		if (isset($_GET['search'])) {
			$search = strtolower($_GET['search']);
			$this->db->group_start();
			$this->db->like('orderTrackingId', $search);
			$this->db->or_like('retailer.retailerShopName', $search);
			$this->db->or_like('retailer.retailerName', $search);
			$this->db->or_like('rep.firstName', $search);
			$this->db->or_like('rep.lastName', $search);
			$this->db->or_like('teams.teamName', $search);
			$this->db->or_like('distributor.distributorCompanyName', $search);
			$this->db->or_like('distributor.distributorName', $search);
			$this->db->group_end();
			$this->db->join('retailer', 'retailer.id = a.orderRetailerId', 'left');
			$this->db->join('rep', 'rep.id = a.orderSalesRepId', 'left');
			$this->db->join('teams', 'teams.id = a.orderSalesRepTeam', 'left');
			$this->db->join('distributor', 'distributor.id = a.orderDistributorId', 'left');
		}

		$orderByCreatedDate = isset($_GET['orderBy']) && $_GET['orderBy'] === '-createdDate';
		$orderBy = $orderByCreatedDate ? 'ASC' : 'DESC';
		$this->db->order_by('created_date', $orderBy);


		// $tempdb = clone $this->db;
		// $tempdb->select('a.*');
		// $tempdb->from('orders a');
		//now we run the count method on this copy
		// $count = $this->db->num_rows();
		// $count = $countQuery->count_all_results();

		$limit = (isset($_GET['limit']) && is_numeric($_GET['limit']) && !empty(trim($_GET['limit']))) ? $_GET['limit'] : 10;
		$offset = (isset($_GET['offset']) && is_numeric($_GET['offset']) && !empty(trim($_GET['offset']))) ? $_GET['offset'] : 0;



		$tempdb = clone $this->db;
		// print_r($tempdb);
		$num_results = $tempdb->count_all_results();
		if ($limit != null || $offset != null) {
			$this->db->limit($limit, $offset);
		}
		$query = $this->db->get();


		if ($query === false) {
			// An error occurred. Display the error message.
			echo $this->db->error()['message'];
			return false;
		}

		if ($query->num_rows() != 0) {
			// $count = $query->num_rows();
			$results = $query->result();

			$data = array();

			if (isset($_GET['populate']) && $_GET['populate'] == true) {
				foreach ($results as $result) {
					$retailerQuery = $this->db->get_where('retailer', array('id' => $result->orderRetailerId));
					$retailerInfo = $retailerQuery->row();
					$cityQuery = $this->db->get_where('city', array('id' => $retailerInfo->retailerCity));
					$cityInfo = $cityQuery->row();
					$retailerInfo->retailerCity = $cityInfo;
					$routeQuery = $this->db->get_where('route', array('id' => $retailerInfo->retailerRoute));
					$routeInfo = $routeQuery->row();
					$retailerInfo->retailerRoute = $routeInfo;
					$result->orderRetailerId = $retailerInfo;

					$salesrepQuery = $this->db->get_where('rep', array('id' => $result->orderSalesRepId));
					$repInfo = $salesrepQuery->row();
					$result->orderSalesRepId = $repInfo;

					$distributorQuery = $this->db->get_where('distributor', array('id' => $result->orderDistributorId));
					$distributorInfo = $distributorQuery->row();
					$result->orderDistributorId = $distributorInfo;

					$orderItemQuery = $this->db->get_where('orderitem', array('itemOrderId' => $result->id));
					$orderItemInfo = $orderItemQuery->result();
					$result->orderItems = $orderItemInfo;

					$orderTeamQuery = $this->db->get_where('teams', array('id' => $result->orderSalesRepTeam));
					$orderTeamInfo = $orderTeamQuery->row();
					$result->orderSalesRepTeam = $orderTeamInfo;
					$data['data'][] = $result;
				}
				// $data['count'] = $count;
				return $data;
			}

			$data['data'] = $results;
			$data['count'] = $num_results;
			return $data;
		} else {
			return FALSE;
		}
	}


	public function get_order($orderId)
	{
		$this->db->select('a.*');
		$this->db->from('orders a');
		$this->db->where('a.id', $orderId);
		$query = $this->db->get();
		if ($query->num_rows() != 0) {
			$result = $query->row();
			$count = $query->num_rows();

			if (isset($_GET['populate']) && $_GET['populate'] == true) :
				$retailerQuery = $this->db->get_where('retailer', array('id' => $result->orderRetailerId));
				$retailerInfo = $retailerQuery->row();
				$cityQuery = $this->db->get_where('city', array('id' => $retailerInfo->retailerCity));
				$cityInfo = $cityQuery->row();
				$retailerInfo->retailerCity = $cityInfo;
				$routeQuery = $this->db->get_where('route', array('id' => $retailerInfo->retailerRoute));
				$routeInfo = $routeQuery->row();
				$retailerInfo->retailerRoute = $routeInfo;
				$result->orderRetailerId = $retailerInfo;
				$salesrepQuery = $this->db->get_where('rep', array('id' => $result->orderSalesRepId));
				$repInfo = $salesrepQuery->row();
				$result->orderSalesRepId = $repInfo;
				$teamQuery = $this->db->get_where('teams', array('id' => $result->orderSalesRepTeam));
				$teamInfo = $teamQuery->row();
				$result->orderSalesRepTeam = $teamInfo;
				$distributorQuery = $this->db->get_where('distributor', array('id' => $result->orderDistributorId));
				$distributorInfo = $distributorQuery->row();
				$result->orderDistributorId = $distributorInfo;

				$orderItemQuery = $this->db->get_where('orderitem', array('itemOrderId' => $result->id));
				$orderItemInfo = $orderItemQuery->result();
				// $result->orderItems = $orderItemInfo;

				// Loop through the order items and populate product info
				foreach ($orderItemInfo as &$item) {
					$productQuery = $this->db->get_where('product', array('id' => $item->itemProductId));
					$productInfo = $productQuery->row();
					$item->product = $productInfo;
				}

				$result->orderItems = $orderItemInfo;




				$data['data'] = $result;
				$data['count'] = $count;
				return $data;

			endif;

			$data['data'] = $result;
			$data['count'] = $count;
			return $data;
		} else {
			return FALSE;
		}
	}


	public function validate_order_items($orderItems)
	{

		$orderItemsArray = json_decode($orderItems, true);

		if (!is_array($orderItemsArray) || empty($orderItemsArray)) {
			$this->form_validation->set_message('validate_order_items', 'The order items are invalid.');
			return false;
		}

		foreach ($orderItemsArray as $item) {
			if (!isset($item['itemProductId']) || !isset($item['itemQty'])) {
				$this->form_validation->set_message('validate_order_items', 'Each order item must have itemProductId and itemQty.');
				return false;
			}
		}

		return true;
	}

	public function update_Order_status($data, $status)
	{


		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('id', 'Order', 'required|max_length[100]');
		if ($this->form_validation->run() == false) {
			return false; // Validation failed
		}
		$orderId  = $data['id'];
		$update['orderStatus']				= $status;
		$this->db->where('id', $orderId);
		$this->db->update('orders', $update);

		$this->db->where('id', $orderId);
		return $this->db->get('orders')->row();
	}

	public function accept_order($data, $status)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('id', 'Order', 'required|max_length[100]');
		if ($this->form_validation->run() == false) {
			return false; // Validation failed
		}
		$orderId  = $data['id'];
		$update['orderStatus']				= $status;
		$this->db->where('id', $orderId);
		$this->db->update('orders', $update);

		$this->db->where('id', $orderId);
		return $this->db->get('orders')->row();
	}

	public function cancel_order($data, $status)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('id', 'Order', 'required|max_length[100]');
		if ($this->form_validation->run() == false) {
			return false; // Validation failed
		}
		$orderId  = $data['id'];
		$update['orderStatus']				= $status;
		$this->db->where('id', $orderId);
		$this->db->update('orders', $update);

		$this->db->where('id', $orderId);
		return $this->db->get('orders')->row();
	}


	// public function create_order($data)
	// {
	// 	$this->load->library('form_validation');
	// 	$this->form_validation->set_data($data);
	// 	$this->form_validation->set_rules('orderRetailerId', 'Order Retailer Id', 'required|max_length[100]');
	// 	$this->form_validation->set_rules('orderSalesRepId', 'orderSalesRepId', 'required|max_length[100]');
	// 	$this->form_validation->set_rules('orderDistributorId', 'orderDistributorId', 'required|max_length[100]');

	// 	if ($this->form_validation->run() == false) {
	// 		return false; // Validation failed
	// 	}

	// 	$insert['id'] = generate_uuid();
	// 	$insert['createdBy'] = getCreatedBy();
	// 	$insert['rewardPoints'] = 0;
	// 	$orderItems = $data['orderItems'];
	// 	$insert['orderRetailerId'] = $data['orderRetailerId'];
	// 	$insert['orderSalesRepId'] = $data['orderSalesRepId'];
	// 	$rep = $this->db->get_where('rep', array('id' => $data['orderSalesRepId']))->row();
	// 	$insert['orderSalesRepTeam'] = $rep->repTeam ?? '';
	// 	$insert['orderDistributorId'] = $data['orderDistributorId'];

	// 	$rewardPoints = 0;
	// 	$orderAmount = 0;
	// 	$orderQuantity = 0;
	// 	$orderCount = 0;
	// 	foreach ($orderItems as $orderItem) {
	// 		$orderCount++;
	// 		$product = $this->db->get_where('product', array('id' => $orderItem['itemProductId']))->row();
	// 		$order_item['id'] = generate_uuid();
	// 		$order_item['itemOrderId'] = $insert['id'];
	// 		$order_item['itemProductId'] = $product->id;
	// 		$order_item['itemPrice'] = $product->productPrice;
	// 		$order_item['itemQty'] = $orderItem['itemQty'];
	// 		$order_item['itemTotal'] = $product->productPrice * $orderItem['itemQty'];
	// 		$this->db->insert('orderitem', $order_item);

	// 		$rewardPoints += $product->productReward * $orderItem['itemQty'];
	// 		$orderAmount += $order_item['itemTotal'];
	// 		$orderQuantity += $orderItem['itemQty'];
	// 	}

	// 	$insert['rewardPoints'] = $rewardPoints;

	// 	// Generate and save orderTrackingId
	// 	$currentYear = date('y'); // Use last 2 digits of year

	// 	$lastOrderTrackingId = $this->db->select('orderTrackingId')->order_by('id', 'desc')->limit(1)->get('orders')->row();
	// 	$increment = ($lastOrderTrackingId) ? intval($lastOrderTrackingId->orderTrackingId) + 2 : 0;
	// 	$orderTrackingId = intval($currentYear . sprintf('%04d', $increment));

	// 	$insert['orderTrackingId'] = $orderTrackingId;

	// 	// ... Rest of the function ...

	// 	$insert['orderAmount'] = $orderAmount;
	// 	$insert['orderQuantity'] = $orderQuantity;
	// 	$insert['orderItems'] = $orderCount;
	// 	$insert['orderStatus'] = 1;

	// 	$this->db->insert('orders', $insert);
	// 	$this->db->where('id', $insert['id']);
	// 	return $this->db->get('orders')->row();
	// 	//$decrement=$this->decrementStock($data['id']);
	// }

	public function create_order($data)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('orderRetailerId', 'Order Retailer Id', 'required|max_length[100]');
		$this->form_validation->set_rules('orderSalesRepId', 'orderSalesRepId', 'required|max_length[100]');
		$this->form_validation->set_rules('orderDistributorId', 'orderDistributorId', 'required|max_length[100]');

		if ($this->form_validation->run() == false) {
			return false; // Validation failed
		}

		$insert['id'] = generate_uuid();
		$insert['createdBy'] = getCreatedBy();
		$insert['rewardPoints'] = 0;
		$orderItems = $data['orderItems'];
		$insert['orderRetailerId'] = $data['orderRetailerId'];
		$insert['orderSalesRepId'] = $data['orderSalesRepId'];
		$rep = $this->db->get_where('rep', array('id' => $data['orderSalesRepId']))->row();
		$insert['orderSalesRepTeam'] = $rep->repTeam ?? '';
		$insert['orderDistributorId'] = $data['orderDistributorId'];

		$rewardPoints = 0;
		$orderAmount = 0;
		$orderQuantity = 0;
		$orderCount = 0;
		foreach ($orderItems as $orderItem) {
			$orderCount++;
			$product = $this->db->get_where('product', array('id' => $orderItem['itemProductId']))->row();
			$order_item['id'] = generate_uuid();
			$order_item['itemOrderId'] = $insert['id'];
			$order_item['itemProductId'] = $product->id;
			$order_item['itemPrice'] = $product->productPrice;
			$order_item['itemQty'] = $orderItem['itemQty'];
			$order_item['itemTotal'] = $product->productPrice * $orderItem['itemQty'];
			$this->db->insert('orderitem', $order_item);

			$rewardPoints += $product->productReward * $orderItem['itemQty'];
			$orderAmount += $order_item['itemTotal'];
			$orderQuantity += $orderItem['itemQty'];
		}

		$insert['rewardPoints'] = $rewardPoints;
		$currentMonth = date('M');
		$currentYear = date('y'); // Last two digits of the year

		$lastInsertedIdQuery = $this->db->query("SELECT MAX(orderTrackingId) AS last_id FROM orders WHERE SUBSTRING(orderTrackingId, 1, 2) = '$currentYear'");
		$lastInsertedId = $lastInsertedIdQuery->row()->last_id;

		if (!$lastInsertedId) {
			$lastInsertedId = 0;
		}

		// Increment last two digits of the last inserted ID by 2
		// echo $lastInsertedId;
		$incrementedId = str_pad($lastInsertedId + 2, 4, '0', STR_PAD_LEFT);

		// Create orderTrackingId
		// $orderTrackingId = $currentYear . $incrementedId;
		$insert['orderTrackingId'] = $incrementedId;

		// Rest of your logic remains unchanged...

		$insert['orderAmount'] = $orderAmount;
		$insert['orderQuantity'] = $orderQuantity;
		$insert['orderItems'] = $orderCount;
		$insert['orderStatus'] = 1;

		$this->db->insert('orders', $insert);
		$this->db->where('id', $insert['id']);
		return $this->db->get('orders')->row();
		//$decrement=$this->decrementStock($data['id']);
	}




	// public function create_order($data)
	// {
	// 	$this->load->library('form_validation');
	// 	$this->form_validation->set_data($data);
	// 	$this->form_validation->set_rules('orderRetailerId', 'Order Retailer Id', 'required|max_length[100]');
	// 	$this->form_validation->set_rules('orderSalesRepId', 'orderSalesRepId', 'required|max_length[100]');
	// 	$this->form_validation->set_rules('orderDistributorId', 'orderDistributorId', 'required|max_length[100]');

	// 	if ($this->form_validation->run() == false) {
	// 		return false; // Validation failed
	// 	}

	// 	$insert['id'] 				= 	generate_uuid();
	// 	$insert['createdBy']		 = 	getCreatedBy();
	// 	$insert['rewardPoints']		=	0;
	// 	$orderItems					=	$data['orderItems'];
	// 	$insert['orderRetailerId']		=	$data['orderRetailerId'];
	// 	$insert['orderSalesRepId']		=	$data['orderSalesRepId'];
	// 	$rep  = $this->db->get_where('rep', array('id' => $data['orderSalesRepId']))->row();
	// 	$insert['orderSalesRepTeam']		=	$rep->repTeam ?? '';
	// 	$insert['orderDistributorId']	=	$data['orderDistributorId'];

	// 	$rewardPoints 				=	0;
	// 	$orderAmount				=	0;
	// 	$orderQuantity				=	0;
	// 	$orderCount					=	0;
	// 	foreach ($orderItems as $orderItem) {
	// 		$orderCount++;
	// 		$product  = $this->db->get_where('product', array('id' => $orderItem['itemProductId']))->row();
	// 		$order_item['id'] 				= generate_uuid();
	// 		$order_item['itemOrderId'] 		= $insert['id'];
	// 		$order_item['itemProductId'] 	= $product->id;
	// 		$order_item['itemPrice'] 		= $product->productPrice;
	// 		$order_item['itemQty'] 			= $orderItem['itemQty'];
	// 		$order_item['itemTotal'] 		= $product->productPrice * $orderItem['itemQty'];
	// 		$this->db->insert('orderitem', $order_item);

	// 		$rewardPoints					+=	$product->productReward * $orderItem['itemQty'];
	// 		$orderAmount					+=	$order_item['itemTotal'];
	// 		$orderQuantity					+=	$orderItem['itemQty'];
	// 	}

	// 	$insert['rewardPoints']				= $rewardPoints;
	// 	$currentMonth = date('M');
	// 	$currentYear = date('Y');

	// 	$existingRecordTeam = $this->db->get_where('leaderboardteam', array('teamId' => $insert['orderSalesRepTeam'], 'month' => $currentMonth, 'year' => $currentYear))->row();

	// 	if ($existingRecordTeam) {
	// 		// Update existing record			
	// 		$this->db->set('score', 'score + ' . $rewardPoints, false);
	// 		$this->db->where('id', $existingRecordTeam->id);
	// 		$this->db->update('leaderboardteam');
	// 	} else {
	// 		// Insert new record

	// 		$insertTeam['id'] = generate_uuid();
	// 		$insertTeam['month'] = $currentMonth;
	// 		$insertTeam['year'] = $currentYear;
	// 		$insertTeam['teamId'] = $insert['orderSalesRepTeam'];
	// 		$insertTeam['score'] = $rewardPoints;
	// 		$this->db->insert('leaderboardteam', $insertTeam);
	// 	}
	// 	$existingRecordRep = $this->db->get_where('leaderboardrep', array('repId' => $insert['orderSalesRepId'], 'month' => $currentMonth, 'year' => $currentYear))->row();
	// 	if ($existingRecordRep) {
	// 		// Update existing record

	// 		$this->db->set('score', 'score + ' . $rewardPoints, false);
	// 		$this->db->where('id', $existingRecordRep->id);
	// 		$this->db->update('leaderboardrep');
	// 	} else {
	// 		// Insert new record
	// 		$insertrep['id'] = generate_uuid();
	// 		$insertrep['month'] = $currentMonth;
	// 		$insertrep['year'] = $currentYear;
	// 		$insertrep['repId'] = $insert['orderSalesRepId'];
	// 		$insertrep['score'] = $rewardPoints;
	// 		$this->db->insert('leaderboardrep', $insertrep);
	// 	}
	// 	$insert['orderAmount']				= $orderAmount;
	// 	$insert['orderQuantity']			= $orderQuantity;
	// 	$insert['orderItems']				= $orderCount;
	// 	$insert['orderStatus']				= 1;

	// 	$this->db->insert('orders', $insert);
	// 	$this->db->where('id', $insert['id']);
	// 	return $this->db->get('orders')->row();
	// 	//$decrement=$this->decrementStock($data['id']);


	// }

	public function update_order($orderId, $data)
	{

		$this->db->where('itemOrderId', $orderId);
		$this->db->delete('orderitem');


		// $update['rewardPoints']		=	0;


		$rewardPoints 				=	0;
		$orderAmount				=	0;
		$orderQuantity				=	0;
		$orderCount					=	0;
		foreach ($data['orderItems'] as $orderItem) {
			$orderCount++;
			$product  = $this->db->get_where('product', array('id' => $orderItem['itemProductId']))->row();
			$order_item['id'] 				= generate_uuid();
			$order_item['itemOrderId'] 		= $orderId;
			$order_item['itemProductId'] 	= $product->id;
			$order_item['itemPrice'] 		= $product->productPrice;
			$order_item['itemQty'] 			= $orderItem['itemQty'];
			$order_item['itemTotal'] 		= $product->productPrice * $orderItem['itemQty'];
			$this->db->insert('orderitem', $order_item);

			$rewardPoints					+=	$product->productReward * $orderItem['itemQty'];
			$orderAmount					+=	$order_item['itemTotal'];
			$orderQuantity					+=	$orderItem['itemQty'];
		}

		$update['orderAmount']				= $orderAmount;
		$update['orderQuantity']			= $orderQuantity;
		$update['orderItems']				= $orderCount;
		$this->db->where('id', $orderId);
		$this->db->update('orders', $update);
		$this->db->where('id', $orderId);
		return $this->db->get('orders')->row();
		//$decrement=$this->decrementStock($orderId);
	}

	// public function update_order($orderId, $data)
	// {
	// 	$this->load->library('form_validation');
	// 	$this->form_validation->set_data($data);
	// 	$this->form_validation->set_rules('orderRetailerId', 'Order Retailer Id', 'required|max_length[100]');
	// 	$this->form_validation->set_rules('orderSalesRepId', 'orderSalesRepId', 'required|max_length[100]');
	// 	$this->form_validation->set_rules('orderDistributorId', 'orderDistributorId', 'required|max_length[100]');
	// 	$this->form_validation->set_rules('orderStatus', 'order status', 'required|max_length[100]');

	// 	if ($this->form_validation->run() == false) {
	// 		return false; // Validation failed
	// 	}

	// 	$this->db->where('itemOrderId', $orderId);
	// 	$this->db->delete('orderitem');


	// 	$update['rewardPoints']		=	0;
	// 	$orderItems						=	$data['orderItems'];
	// 	$update['orderRetailerId']		=	$data['orderRetailerId'];
	// 	$update['orderSalesRepId']		=	$data['orderSalesRepId'];
	// 	$rep  = $this->db->get_where('rep', array('id' => $data['orderSalesRepId']))->row();
	// 	$update['orderSalesRepTeam']		=	$rep->repTeam ?? '';
	// 	$update['orderDistributorId']	=	$data['orderDistributorId'];


	// 	$rewardPoints 				=	0;
	// 	$orderAmount				=	0;
	// 	$orderQuantity				=	0;
	// 	$orderCount					=	0;
	// 	foreach ($orderItems as $orderItem) {
	// 		$orderCount++;
	// 		$product  = $this->db->get_where('product', array('id' => $orderItem['itemProductId']))->row();
	// 		$order_item['id'] 				= generate_uuid();
	// 		$order_item['itemOrderId'] 		= $orderId;
	// 		$order_item['itemProductId'] 	= $product->id;
	// 		$order_item['itemPrice'] 		= $product->productPrice;
	// 		$order_item['itemQty'] 			= $orderItem['itemQty'];
	// 		$order_item['itemTotal'] 		= $product->productPrice * $orderItem['itemQty'];
	// 		$this->db->insert('orderitem', $order_item);

	// 		$rewardPoints					+=	$product->productReward * $orderItem['itemQty'];
	// 		$orderAmount					+=	$order_item['itemTotal'];
	// 		$orderQuantity					+=	$orderItem['itemQty'];
	// 	}

	// 	$update['rewardPoints']				= $rewardPoints;
	// 	$this->db->select('created_date');
	// 	$this->db->where('id', $orderId);
	// 	$date = $this->db->get('orders')->row()->created_date;
	// 	$currentMonth = date('M', strtotime($date));
	// 	$currentYear = date('Y', strtotime($date));
	// 	$existingRecordTeam = $this->db->get_where('leaderboardteam', array('teamId' => $update['orderSalesRepTeam'], 'month' => $currentMonth, 'year' => $currentYear))->row();
	// 	if ($existingRecordTeam) {
	// 		// Update existing record
	// 		$currentScore = $existingRecordTeam->score;
	// 		$scoreDifference = $rewardPoints - $currentScore;

	// 		$this->db->set('score', 'score + ' . $scoreDifference, false);
	// 		$this->db->where('id', $existingRecordTeam->id);
	// 		$this->db->update('leaderboardteam');
	// 	} else {
	// 		// Insert new record

	// 		$insertTeam['id'] = generate_uuid();
	// 		$insertTeam['month'] = $currentMonth;
	// 		$insertTeam['year'] = $currentYear;
	// 		$insertTeam['teamId'] = $update['orderSalesRepTeam'];
	// 		$insertTeam['score'] = $rewardPoints;
	// 		$this->db->insert('leaderboardteam', $insertTeam);
	// 	}
	// 	$existingRecordRep = $this->db->get_where('leaderboardrep', array('repId' => $update['orderSalesRepId'], 'month' => $currentMonth, 'year' => $currentYear))->row();
	// 	if ($existingRecordRep) {
	// 		// Update existing record
	// 		$currentScore = $existingRecordRep->score;
	// 		$scoreDifference = $rewardPoints - $currentScore;

	// 		$this->db->set('score', 'score + ' . $scoreDifference, false);
	// 		$this->db->where('id', $existingRecordRep->id);
	// 		$this->db->update('leaderboardrep');
	// 	} else {
	// 		// Insert new record
	// 		$insertrep['id'] = generate_uuid();
	// 		$insertrep['month'] = $currentMonth;
	// 		$insertrep['year'] = $currentYear;
	// 		$insertrep['repId'] = $update['orderSalesRepId'];
	// 		$insertrep['score'] = $rewardPoints;
	// 		$this->db->insert('leaderboardrep', $insertrep);
	// 	}
	// 	$update['orderAmount']				= $orderAmount;
	// 	$update['orderQuantity']			= $orderQuantity;
	// 	$update['orderItems']				= $orderCount;
	// 	$this->db->where('id', $orderId);
	// 	$this->db->update('orders', $update);
	// 	$this->db->where('id', $orderId);
	// 	return $this->db->get('orders')->row();
	// 	//$decrement=$this->decrementStock($orderId);
	// }

	function incrementStock($orderId)
	{
		$this->db->where('id', $orderId);
		$orderItems = $this->db->get('orderitem')->result();

		foreach ($orderItems as $item) {
			$this->db->select(['stockQty', 'id']);
			$this->db->where('stockProductId', $item['itemProductId']);
			$query = $this->db->get('stock')->row();
			$currentStockQty = $query->stockQty;
			$stockId = $query->id;
			$this->db->set('initialStock', 'initialStock + ' . $item['itemQty'], false);
			$this->db->where('productId', $item['itemProductId']);
			$this->db->update('product');

			$this->db->set('stockQty', 'stockQty +' . $item['itemQty'], false);
			$this->db->where('id', $stockId);
			$this->db->update('stock');
		}
		return true;
	}
	function decrementStock($orderId)
	{
		$this->db->where('id', $orderId);
		$orderItems = $this->db->get('orderitem')->result();

		foreach ($orderItems as $item) {
			$this->db->select(['stockQty', 'id']);
			$this->db->where('stockProductId', $item['itemProductId']);
			$query = $this->db->get('stock')->row();
			$currentStockQty = $query->stockQty;
			$stockId = $query->id;

			$this->db->set('initialStock', 'initialStock - ' . $item['itemQty'], false);
			$this->db->where('productId', $item['itemProductId']);
			$this->db->update('product');

			$this->db->set('stockQty', 'stockQty - ' . $item['itemQty'], false);
			$this->db->where('id', $stockId);
			$this->db->update('stock');
		}
		return true;
	}
	public function delete_order($orderId)
	{

		$this->db->where('id', $orderId);
		$order = $this->db->get('orders')->row();
		if ($order) {
			$date = $order->created_date;
			$currentMonth = date('M', strtotime($date));
			$currentYear = date('Y', strtotime($date));
			$existingRecordRep = $this->db->get_where('leaderboardrep', array('repId' => $order->orderSalesRepId, 'month' => $currentMonth, 'year' => $currentYear))->row();
			if (!empty($existingRecordRep)) {
				$this->db->set('score', 'score - ' . $order->rewardPoints, false);
				$this->db->where('id', $existingRecordRep->id);
				$this->db->update('leaderboardrep');
			}
			$existingRecordTeam = $this->db->get_where('leaderboardteam', array('teamId' => $order->orderSalesRepTeam, 'month' => $currentMonth, 'year' => $currentYear))->row();
			if (!empty($existingRecordTeam)) {
				$this->db->set('score', 'score - ' . $order->rewardPoints, false);
				$this->db->where('id', $existingRecordTeam->id);
				$this->db->update('leaderboardteam');
			}

			//$increment=$this->incrementStock($order->id);
			$this->db->where('itemOrderId', $orderId);
			$this->db->delete('orderitem');
			$this->db->where('id', $orderId);
			$this->db->delete('orders');

			if ($this->db->affected_rows() > 0) return true;
			else return FALSE;
		} else return FALSE;
	}


	// public function get_order_analytics($repId = null)
	// {
	// 	$this->db->select('orderStatus, COUNT(*) as count');
	// 	$this->db->from('orders');
	// 	$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
	// 	$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;

	// 	if ($repId !== null) {
	// 		$this->db->where('orderSalesRepId', $repId);
	// 	}

	// 	if ($startDate !== null && $endDate !== null) {
	// 		$this->db->where('created_date >=', $startDate);
	// 		$this->db->where('created_date <=', $endDate);
	// 	}

	// 	$this->db->group_by('orderStatus');
	// 	$query = $this->db->get();

	// 	$orderStatuses = [
	// 		'pendingOrders' => 0,
	// 		'deliveredOrders' => 0,
	// 		'cancelledOrders' => 0,
	// 		'totalOrders' => 0,
	// 	];

	// 	if ($query->num_rows() > 0) {
	// 		foreach ($query->result() as $row) {
	// 			switch ($row->orderStatus) {
	// 				case 1:
	// 					$orderStatuses['pendingOrders'] = $row->count;
	// 					break;
	// 				case 2:
	// 					$orderStatuses['deliveredOrders'] = $row->count;
	// 					break;
	// 				case 3:
	// 					$orderStatuses['cancelledOrders'] = $row->count;
	// 					break;
	// 			}
	// 			$orderStatuses['totalOrders'] += $row->count;
	// 		}
	// 	}

	// 	return $orderStatuses;
	// }

	public function get_order_analytics($type = null, $id = null)
	{
		$this->db->select('orderStatus, COUNT(*) as count');
		$this->db->from('orders');
		$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
		$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;

		if ($id !== null) {
			switch ($type) {
				case 'rep':
					$this->db->where('orderSalesRepId', $id);
					break;
				case 'distributor':
					$this->db->where('orderDistributorId', $id);
					break;
				case 'teamLead':
					$this->db->where('orderSalesRepTeam', $id);
					break;
			}
		}

		if ($startDate !== null && $endDate !== null) {
			$this->db->where('created_date >=', $startDate);
			$this->db->where('created_date <=', $endDate);
		}

		$this->db->group_by('orderStatus');

		$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
		if ($limit !== null && is_numeric($limit)) {
			$this->db->limit($limit);
		}

		$query = $this->db->get();

		$orderStatuses = [
			'pendingOrders' => 0,
			'deliveredOrders' => 0,
			'cancelledOrders' => 0,
			'totalOrders' => 0,
		];

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				switch ($row->orderStatus) {
					case 1:
						$orderStatuses['pendingOrders'] = $row->count;
						break;
					case 2:
						$orderStatuses['deliveredOrders'] = $row->count;
						break;
					case 3:
						$orderStatuses['cancelledOrders'] = $row->count;
						break;
				}
				$orderStatuses['totalOrders'] += $row->count;
			}
		}

		return $orderStatuses;
	}
}
