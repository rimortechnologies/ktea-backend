<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Orders_model extends CI_Model
{

	public function get_all_orders()
	{
		$conditionsForCount = array();
		$conditions = array();

		$type = isset($_GET['type']) ? $_GET['type'] : null;

		if (isset($_GET['retailerId'])) {
			$conditions['orderRetailerId'] = $_GET['retailerId'];
		}
		if (isset($_GET['cityId'])) {
			$conditions['retailer.retailerCity'] = $_GET['cityId'];
		}
		if (isset($_GET['routeId'])) {
			$conditions['retailer.retailerRoute'] = $_GET['routeId'];
		}
		if (isset($_GET['salesRepId'])) {
			$conditions['orderSalesRepId'] = $_GET['salesRepId'];
		}
		if (isset($_GET['salesTeamId'])) {
			$conditions['orderSalesRepTeam'] = $_GET['salesTeamId'];
		}
		if (isset($_GET['repId'])) {
			if ($type === 'rep') {
				$conditions['orderSalesRepId'] = $_GET['repId'];
			} elseif ($type === 'teamLead') {
				$conditions['orderSalesRepTeam'] = $_GET['repId'];
			}
		}
		if (isset($_GET['distributorId'])) {
			// if ($type === 'distributor') {
			$conditions['orderDistributorId'] = $_GET['distributorId'];
			// }
		}
		if (isset($_GET['orderStatus'])) {
			$conditions['orderStatus'] = $_GET['orderStatus'];
		}

		$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
		$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;


		if ($startDate !== null) {
			// Convert DD-MM-YYYY to MySQL date format (YYYY-MM-DD)
			$startDate = date('Y-m-d', strtotime($startDate));

			if ($endDate === null) {
				$endDate = date('Y-m-d H:i:s'); // Current date and time
			} else {
				$endDate = date('Y-m-d', strtotime($endDate)) . ' 23:59:59'; // Convert endDate to MySQL date format
			}

			$conditions['a.created_date >='] = $startDate . ' 00:00:00';
			$conditions['a.created_date <='] = $endDate;
		}

		$search = '';

		if (isset($_GET['search'])) {
			$search = strtolower($_GET['search']);
		}
		// Clone the main database query builder for counting
		$countQueryBuilder = clone $this->db;

		$countQueryBuilder->select('COUNT(*) as count')
			->join('retailer', 'retailer.id = a.orderRetailerId', 'left')
			->join('rep', 'rep.id = a.orderSalesRepId', 'left')
			->join('teams', 'teams.id = a.orderSalesRepTeam', 'left')
			->join('distributor', 'distributor.id = a.orderDistributorId', 'left')
			->group_start()
			->like('orderTrackingId', $search)
			->or_like('retailer.retailerShopName', $search)
			->or_like('retailer.retailerName', $search)
			->or_like('rep.firstName', $search)
			->or_like('rep.lastName', $search)
			->or_like('teams.teamName', $search)
			->or_like('distributor.distributorCompanyName', $search)
			->or_like('distributor.distributorName', $search)
			->group_end();

		$conditionsForCount = $conditions;
		$countResult = $countQueryBuilder->from('orders a')->where($conditionsForCount)->get()->row();
		$num_results = $countResult->count;

		// Apply search conditions to the main query builder
		$this->db->group_start()
			->select('a.*, retailer.retailerShopName, retailer.retailerName, rep.firstName, rep.lastName, teams.teamName, distributor.distributorCompanyName, distributor.distributorName')
			->join('retailer', 'retailer.id = a.orderRetailerId', 'left')
			->join('rep', 'rep.id = a.orderSalesRepId', 'left')
			->join('teams', 'teams.id = a.orderSalesRepTeam', 'left')
			->join('city', 'city.id = retailer.retailerCity', 'left')
			->join('distributor', 'distributor.id = a.orderDistributorId', 'left')
			->like('orderTrackingId', $search)
			->or_like('retailer.retailerShopName', $search)
			->or_like('retailer.retailerName', $search)
			->or_like('rep.firstName', $search)
			->or_like('rep.lastName', $search)
			->or_like('teams.teamName', $search)
			->or_like('distributor.distributorCompanyName', $search)
			->or_like('distributor.distributorName', $search)
			->group_end();


		$sortField = 'created_date';
		$orderBy = 'DESC';
		if (isset($_GET['orderBy'])) {
			if ($_GET['orderBy'] === 'retailer' || $_GET['orderBy'] === '-retailer') {
				$sortField = 'retailer.retailerShopName';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'distributor' || $_GET['orderBy'] === '-distributor') {
				$sortField = 'distributor.distributorCompanyName';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'city' || $_GET['orderBy'] === '-city') {
				$sortField = 'city.cityName';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'createdOn' || $_GET['orderBy'] === '-createdOn') {
				$sortField = 'a.created_date';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'bill' || $_GET['orderBy'] === '-bill') {
				$sortField = 'a.orderAmount';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			} else if ($_GET['orderBy'] === 'status' || $_GET['orderBy'] === '-status') {
				$sortField = 'a.orderStatus';
				$orderBy = (strpos($_GET['orderBy'], '-') === 0) ? 'DESC' : 'ASC';
			}
		}


		$limit = (isset($_GET['limit']) && is_numeric($_GET['limit']) && !empty(trim($_GET['limit']))) ? $_GET['limit'] : 10;
		$offset = (isset($_GET['offset']) && is_numeric($_GET['offset']) && !empty(trim($_GET['offset']))) ? $_GET['offset'] : 0;
		$query = $this->db
			->from('orders a')
			->where($conditions)
			->limit($limit, $offset)
			->order_by($sortField, $orderBy)
			->get();

		if ($query === false) {
			echo $this->db->error()['message'];
			return false;
		}

		if ($query->num_rows() != 0) {
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
					$data['count'] = $num_results;
				}
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
		// echo 'running' . $orderId;
		$this->db->select('a.*');
		$this->db->from('orders a');
		$this->db->where('a.id', $orderId);
		$query = $this->db->get();
		// print_r($query);
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
				// print_r($data);
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

	public function accept_order($data)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('id', 'Order', 'required|max_length[100]');

		if ($this->form_validation->run() == false) {
			// echo 'validation failed';
			return false; // Validation failed
		}

		$orderId = $data['id'];

		// Check if the current order status is "1" (assuming "1" represents cancellable orders)
		$currentStatus = $this->db->get_where('orders', array('id' => $orderId))->row()->orderStatus;
		if ($currentStatus != 1) {
			return false;
		}

		// Update the order status to "4" (canceled)
		$update = array('orderStatus' => 2);
		$this->db->where('id', $orderId);
		$this->db->update('orders', $update);

		// Return the updated order details
		$this->db->where('id', $orderId);
		return $this->db->get('orders')->row();
	}

	public function cancel_order($data)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('id', 'Order', 'required|max_length[100]');

		if ($this->form_validation->run() == false) {
			return false; // Validation failed
		}

		$orderId = $data['id'];

		// Check if the current order status is "1" (assuming "1" represents cancellable orders)
		$currentStatus = $this->db->get_where('orders', array('id' => $orderId))->row()->orderStatus;

		if ($currentStatus != 1) {
			return false;
		}

		// Update the order status to "4" (canceled)
		$update = array('orderStatus' => 4);
		$this->db->where('id', $orderId);
		$this->db->update('orders', $update);

		// Return the updated order details
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

		$totalWeight = 0;
		$homePoint = 0;
		$hotelPoint = 0;

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

			$rewardPoints					+=	$product->productReward * $orderItem['itemQty'];
			$totalWeight					+=	$product->productWeight * $orderItem['itemQty'];
			if ($product->productWeight >= 1000) {
				$hotelPoint					    +=	$product->productReward * $orderItem['itemQty'];
			} else {
				$homePoint					    +=	$product->productReward * $orderItem['itemQty'];
			}

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
		$insert['orderAmount'] = $orderAmount;
		$insert['orderQuantity'] = $orderQuantity;
		$insert['orderItems'] = $orderCount;
		$insert['orderStatus'] = 1;

		$insert['orderWeight']			= $totalWeight;
		$insert['homePoint']			= $homePoint;
		$insert['hotelPoint']			= $hotelPoint;

		// Update the distributor in the distributor table
		$this->db->set('last_purchase', 'NOW()', false);
		$this->db->set('totalWeight', 'totalWeight + ' . $totalWeight, false);
		$this->db->set('totalOrders', 'totalOrders + 1', false);
		$this->db->set('totalPoints', 'totalPoints + ' . $rewardPoints, false);
		$this->db->where('id', $insert['orderDistributorId']);
		$this->db->update('distributor');

		$this->db->set('repOrders', 'repOrders + 1', false);
		$this->db->set('repPoints', 'repPoints + ' . $rewardPoints, false);
		$this->db->where('id', $insert['orderSalesRepId']);
		$this->db->update('rep');

		$this->db->insert('orders', $insert);
		$this->db->where('id', $insert['id']);
		return $this->db->get('orders')->row();
		//$decrement=$this->decrementStock($data['id']);
	}




	public function update_order($orderId, $data)
	{

		$this->db->where('itemOrderId', $orderId);
		$this->db->delete('orderitem');

		$totalWeight = 0;
		$homePoint = 0;
		$hotelPoint = 0;
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
			$totalWeight					+=	$product->productWeight * $orderItem['itemQty'];
			if ($product->productWeight >= 1000) {
				$hotelPoint					    +=	$product->productReward * $orderItem['itemQty'];
			} else {
				$homePoint					    +=	$product->productReward * $orderItem['itemQty'];
			}
			$orderAmount					+=	$order_item['itemTotal'];
			$orderQuantity					+=	$orderItem['itemQty'];
		}

		$update['orderAmount']				= $orderAmount;
		$update['orderQuantity']			= $orderQuantity;
		$update['orderItems']				= $orderCount;
		$update['orderWeight']			= $totalWeight;
		$update['homePoint']			= $homePoint;
		$update['hotelPoint']			= $hotelPoint;

		$this->db->where('id', $orderId);
		$this->db->update('orders', $update);
		$this->db->where('id', $orderId);
		$this->decrementStock($orderId);
		return $this->db->get('orders')->row();
	}


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

			$this->incrementStock($order->id);
			$this->db->where('itemOrderId', $orderId);
			$this->db->delete('orderitem');
			$this->db->where('id', $orderId);
			$this->db->delete('orders');

			if ($this->db->affected_rows() > 0) return true;
			else return FALSE;
		} else return FALSE;
	}


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
			if ($startDate !== null && $endDate !== null) {
				// Convert DD-MM-YYYY to MySQL date format (YYYY-MM-DD)
				$startDate = date('Y-m-d', strtotime($startDate));
				$endDate = date('Y-m-d', strtotime($endDate));

				$this->db->where('created_date >=', $startDate . ' 00:00:00');
				$this->db->where('created_date <=', $endDate . ' 23:59:59');
			}
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
					case 4:
						$orderStatuses['cancelledOrders'] = $row->count;
						break;
				}
				$orderStatuses['totalOrders'] += $row->count;
			}
		}

		return $orderStatuses;
	}


	public function get_admin_analytics()
	{
		$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
		$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;
		// Initialize the response array as an associative array
		$response = array(
			'orderStatuses' => array(
				'pendingOrders' => 0,
				'deliveredOrders' => 0,
				'cancelledOrders' => 0,
				'totalOrders' => 0,
			),
			'cityOrders' => array(),
			'routeOrders' => array(),
			'productOrders' => array(),
			'points' => array(
				'homePoint' => 0,
				'hotelPoint' => 0,
				'orderWeight' => 0,
				'rewardPoints' => 0,
			),
			'averageRewardPoints' => array(
				'salesRep' => 0,
				'distributor' => 0,
				'team' => 0,
				'retailer' => 0,
			),
		);

		$this->db->select('orders.*, retailer.retailerCity, city.cityName, retailer.retailerRoute, route.routeName, orderItem.itemProductId, product.productName, orderItem.itemQty,orders.homePoint, orders.hotelPoint, orders.orderWeight,orders.rewardPoints, orders.orderSalesRepId, orders.orderDistributorId, orders.orderSalesRepTeam, orders.orderRetailerId');
		$this->db->from('orders');

		// Filter by date range
		if ($startDate !== null && $endDate !== null) {
			// Convert DD-MM-YYYY to MySQL date format (YYYY-MM-DD)
			$startDate = date('Y-m-d', strtotime($startDate));
			$endDate = date('Y-m-d', strtotime($endDate));

			$this->db->where('orders.created_date >=', $startDate . ' 00:00:00');
			$this->db->where('orders.created_date <=', $endDate . ' 23:59:59');
		}

		// Join the retailer table
		$this->db->join('retailer', 'retailer.id = orders.orderRetailerId', 'left');

		// Join the city table
		$this->db->join('city', 'city.id = retailer.retailerCity', 'left');

		// Join the route table
		$this->db->join('route', 'route.id = retailer.retailerRoute', 'left');

		// Join the orderItems table
		$this->db->join('orderItem', 'orderItem.itemOrderId = orders.id', 'left');

		// Join the product table using itemProductId
		$this->db->join('product', 'product.id = orderItem.itemProductId', 'left');

		$query = $this->db->get();

		$salesRepRewardPoints = array();
		$distributorRewardPoints = array();
		$teamRewardPoints = array();
		$retailerRewardPoints = array();

		if ($query->num_rows() > 0) {
			// Process orders and calculate orderStatuses, cityOrders, routeOrders
			foreach ($query->result() as $row) {
				// Increment totalOrders count
				$response['orderStatuses']['totalOrders']++;

				// Count orders with different orderStatus values
				switch ($row->orderStatus) {
					case 1:
						$response['orderStatuses']['pendingOrders']++;
						break;
					case 2:
						$response['orderStatuses']['deliveredOrders']++;
						break;
					case 4:
						$response['orderStatuses']['cancelledOrders']++;
						break;
				}

				// Group orders by city
				$cityId = $row->retailerCity;
				$cityName = $row->cityName;
				if (!isset($response['cityOrders'][$cityId])) {
					$response['cityOrders'][$cityId] = array(
						'id' => $cityId,
						'cityName' => $cityName,
						'orderCount' => 0,
					);
				}
				$response['cityOrders'][$cityId]['orderCount']++;

				// Group orders by route
				$routeId = $row->retailerRoute;
				$routeName = $row->routeName;
				if (!isset($response['routeOrders'][$routeId])) {
					$response['routeOrders'][$routeId] = array(
						'id' => $routeId,
						'routeName' => $routeName,
						'orderCount' => 0,
					);
				}
				$response['routeOrders'][$routeId]['orderCount']++;

				// Group orders by product
				$productId = $row->itemProductId;
				$productName = $row->productName;
				$itemQty = $row->itemQty;
				if (!isset($response['productOrders'][$productId])) {
					$response['productOrders'][$productId] = array(
						'id' => $productId,
						'productName' => $productName,
						'totalQty' => 0,
					);
				}
				$response['productOrders'][$productId]['totalQty'] += $itemQty;

				// Calculate points totals
				$response['points']['homePoint'] += $row->homePoint;
				$response['points']['hotelPoint'] += $row->hotelPoint;
				$response['points']['orderWeight'] += $row->orderWeight;
				$response['points']['rewardPoints'] += $row->rewardPoints;

				// Calculate reward points for salesRep, distributor, team, and retailer
				$salesRepId = $row->orderSalesRepId;
				$distributorId = $row->orderDistributorId;
				$teamId = $row->orderSalesRepTeam;
				$retailerId = $row->orderRetailerId;

				if ($salesRepId !== null) {
					if (!isset($salesRepRewardPoints[$salesRepId])) {
						$salesRepRewardPoints[$salesRepId] = 0;
					}
					$salesRepRewardPoints[$salesRepId] += $row->rewardPoints;
				}

				if ($distributorId !== null) {
					if (!isset($distributorRewardPoints[$distributorId])) {
						$distributorRewardPoints[$distributorId] = 0;
					}
					$distributorRewardPoints[$distributorId] += $row->rewardPoints;
				}

				if ($teamId !== null) {
					if (!isset($teamRewardPoints[$teamId])) {
						$teamRewardPoints[$teamId] = 0;
					}
					$teamRewardPoints[$teamId] += $row->rewardPoints;
				}

				if ($retailerId !== null) {
					if (!isset($retailerRewardPoints[$retailerId])) {
						$retailerRewardPoints[$retailerId] = 0;
					}
					$retailerRewardPoints[$retailerId] += $row->rewardPoints;
				}
			}
		}

		// Calculate average reward points for salesRep, distributor, team, and retailer
		$salesRepCount = count($salesRepRewardPoints);
		$distributorCount = count($distributorRewardPoints);
		$teamCount = count($teamRewardPoints);
		$retailerCount = count($retailerRewardPoints);

		$response['averageRewardPoints']['salesRep'] = $salesRepCount > 0 ? array_sum($salesRepRewardPoints) / $salesRepCount : 0;
		$response['averageRewardPoints']['distributor'] = $distributorCount > 0 ? array_sum($distributorRewardPoints) / $distributorCount : 0;
		$response['averageRewardPoints']['team'] = $teamCount > 0 ? array_sum($teamRewardPoints) / $teamCount : 0;
		$response['averageRewardPoints']['retailer'] = $retailerCount > 0 ? array_sum($retailerRewardPoints) / $retailerCount : 0;

		// Convert the associative arrays to indexed arrays
		$response['cityOrders'] = array_values($response['cityOrders']);
		$response['routeOrders'] = array_values($response['routeOrders']);
		$response['productOrders'] = array_values($response['productOrders']);

		return $response;
	}

	public function get_total_counts()
	{
		$totalCounts = [
			'distributors' => $this->db->count_all('distributor'),
			'salesReps' => $this->db->count_all('rep'),
			'retailers' => $this->db->count_all('retailer'),
			'teams' => $this->db->count_all('teams'),
			'products' => $this->db->count_all('product'),
			'routes' => $this->db->count_all('route'),
			'cities' => $this->db->count_all('city'),
		];

		return $totalCounts;
	}
}
