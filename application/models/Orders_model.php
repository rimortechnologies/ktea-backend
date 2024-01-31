<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Orders_model extends CI_Model
{

	public function get_order_summary()
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
		$countQueryBuilder->select('COUNT(*) as totalOrders, 
        SUM(CASE WHEN a.orderStatus = 1 THEN 1 ELSE 0 END) as pendingOrders,
        SUM(CASE WHEN a.orderStatus = 4 THEN 1 ELSE 0 END) as cancelledOrders,
        SUM(CASE WHEN a.orderStatus != 4 THEN a.orderAmount ELSE 0 END) as totalAmount,
        SUM(CASE WHEN a.orderStatus != 4 THEN a.orderWeight ELSE 0 END) as totalWeight,
        SUM(CASE WHEN a.orderStatus != 4 THEN a.rewardPoints ELSE 0 END) as totalPoints,
            SUM(CASE WHEN a.orderStatus != 4 THEN a.orderQuantity ELSE 0 END) as totalQty')
			->from('orders a')
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

		// $conditionsForCount = $conditions;

		// $countResult = $countQueryBuilder->from('orders a')->where($conditionsForCount)->get()->row();
		// $countQuery = $countQueryBuilder->get();
		// $countResult = $countQuery->row();
		// Apply additional conditions for filtering if needed
		if (!empty($conditions)) {
			$countQueryBuilder->where($conditions);
		}

		$countQuery = $countQueryBuilder->get();
		$countResult = $countQuery->row();

		return array(
			'totalOrders' => $countResult->totalOrders,
			'pendingOrders' => $countResult->pendingOrders,
			'cancelledOrders' => $countResult->cancelledOrders,
			'totalAmount' => $countResult->totalAmount,
			'totalQty' => $countResult->totalQty,
			'totalPoints' => $countResult->totalPoints,
			'totalWeight' => $countResult->totalWeight
		);
	}


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

		$countQueryBuilder->select('COUNT(*) as count, SUM(CASE WHEN a.orderStatus = 1 THEN 1 ELSE 0 END) as pendingOrders,
        SUM(CASE WHEN a.orderStatus = 4 THEN 1 ELSE 0 END) as cancelledOrders,
        SUM(CASE WHEN a.orderStatus = 2 THEN 1 ELSE 0 END) as finishedOrders,
        SUM(CASE WHEN a.orderStatus != 4 THEN a.orderAmount ELSE 0 END) as totalAmount,
        SUM(CASE WHEN a.orderStatus != 4 THEN a.orderWeight ELSE 0 END) as totalWeight,
        SUM(CASE WHEN a.orderStatus != 4 THEN a.rewardPoints ELSE 0 END) as totalPoints,
            SUM(CASE WHEN a.orderStatus != 4 THEN a.orderQuantity ELSE 0 END) as totalQty')
			->from('orders a')
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

		$countResult = $countQueryBuilder->where($conditionsForCount)->get()->row();
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
			} else if ($_GET['orderBy'] === 'point' || $_GET['orderBy'] === '-point') {
				$sortField = 'a.rewardPoints';
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

					// $orderItemQuery = $this->db->get_where('orderitem', array('itemOrderId' => $result->id));
					// $orderItemInfo = $orderItemQuery->result();
					// $result->orderItems = $orderItemInfo;
					// Fetch orderItems with productName
					$orderItemQuery = $this->db
						->select('oi.*, p.productName')
						->from('orderitem oi')
						->join('product p', 'oi.itemProductId = p.id', 'left')
						->where('oi.itemOrderId', $result->id)
						->get();

					$orderItemInfo = $orderItemQuery->result();
					$result->orderItems = $orderItemInfo;

					$orderTeamQuery = $this->db->get_where('teams', array('id' => $result->orderSalesRepTeam));
					$orderTeamInfo = $orderTeamQuery->row();
					$result->orderSalesRepTeam = $orderTeamInfo;
					$data['data'][] = $result;
					$data['count'] = $num_results;
					$data['summary'] = $countResult;
				}
				return $data;
			}

			$data['data'] = $results;
			$data['count'] = $num_results;
			$data['summary'] = $countResult;
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
			throw new \Exception('Order Id Invalid'); // Validation failed
		}

		$orderId = $data['id'];

		// Check if the current order status is "1" (assuming "1" represents cancellable orders)
		$currentStatus = $this->db->get_where('orders', array('id' => $orderId))->row()->orderStatus;
		if ($currentStatus != 1) {
			throw new \Exception('Order Already Updated');
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
			throw new \Exception('Order Id Invalid');
		}

		$orderId = $data['id'];

		// Check if the current order status is "1" (assuming "1" represents cancellable orders)
		$currentStatus = $this->db->get_where('orders', array('id' => $orderId))->row()->orderStatus;

		if ($currentStatus != 1) {
			throw new \Exception('Order Already Updated');
		}

		// Update the order status to "4" (canceled)
		$update = array('orderStatus' => 4);
		$this->db->where('id', $orderId);
		$this->db->update('orders', $update);



		// Return the updated order details
		$this->db->where('id', $orderId);
		$returnData = $this->db->get('orders')->row();

		$this->db->set('last_purchase', 'NOW()', false);
		$this->db->set('totalWeight', 'totalWeight - ' . $returnData->orderWeight, false);
		$this->db->set('totalOrders', 'totalOrders - 1', false);
		$this->db->set('totalPoints', 'totalPoints - ' . $returnData->rewardPoints, false);
		$this->db->where('id', $returnData->orderDistributorId);
		$this->db->update('distributor');

		$this->db->set('repOrders', 'repOrders - 1', false);
		$this->db->set('repPoints', 'repPoints - ' . $returnData->rewardPoints, false);
		$this->db->where('id', $returnData->orderSalesRepId);
		$this->db->update('rep');

		$date = $returnData->created_date;
		$currentMonth = date('M', strtotime($date));
		$currentYear = date('Y', strtotime($date));
		$existingRecordRep = $this->db->get_where('leaderboardrep', array('repId' => $returnData->orderSalesRepId, 'month' => $currentMonth, 'year' => $currentYear))->row();
		if (!empty($existingRecordRep)) {
			$this->db->set('score', 'score - ' . $returnData->rewardPoints, false);
			$this->db->where('id', $existingRecordRep->id);
			$this->db->update('leaderboardrep');
		} else {
			$data = array(
				'id' => generate_uuid(),
				'repId' => $returnData->orderSalesRepId,
				'month' => $currentMonth,
				'year' => $currentYear,
				'score' => 0
			);
			$this->db->insert('leaderboardrep', $data);
		}

		$existingRecordTeam = $this->db->get_where('leaderboardteam', array('teamId' => $returnData->orderSalesRepTeam, 'month' => $currentMonth, 'year' => $currentYear))->row();
		if (!empty($existingRecordTeam)) {
			$this->db->set('score', 'score - ' . $returnData->rewardPoints, false);
			$this->db->where('id', $existingRecordTeam->id);
			$this->db->update('leaderboardteam');
			// echo 'leaderboard update';
		} else {
			$data = array(
				'id' => generate_uuid(),
				'teamId' => $returnData->orderSalesRepTeam,
				'month' => $currentMonth,
				'year' => $currentYear,
				'score' => 0 // Set an initial score or the desired value
			);

			// print_r($data);
			// Add additional fields as needed

			$this->db->insert('leaderboardteam', $data);
		}

		$this->incrementStock($returnData->id, $returnData->orderDistributorId);

		return $returnData;
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
		try {
			$this->load->library('form_validation');
			$this->form_validation->set_data($data);
			$this->form_validation->set_rules('orderRetailerId', 'Order Retailer Id', 'required|max_length[100]');
			$this->form_validation->set_rules('orderSalesRepId', 'orderSalesRepId', 'required|max_length[100]');
			$this->form_validation->set_rules('orderDistributorId', 'orderDistributorId', 'required|max_length[100]');

			if ($this->form_validation->run() == false) throw new Exception(validation_errors());

			$this->db->trans_start(); // Start transaction

			if ($data['orderRetailerTempId'] && $data['orderRetailerTempId'] !== '') {
				// Fetch retailer Id using temporary ID
				$tempId = $data['orderRetailerTempId'];
				$retailerId = $this->db->select('id')->get_where('retailer', ['retailerTempId' => $tempId])->row('id');

				if (!$retailerId) {
					throw new \Exception('Offline Retailer Not Found');
				} else  $data['orderRetailerId'] = $retailerId;
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

			if (isset($data['timestamp']) && !empty($data['timestamp'])) {
				// If timestamp is set, use it
				$createdDate = date('Y-m-d H:i:s', $data['timestamp']);
			} else {
				// If timestamp is not set, use the current time
				$createdDate = date('Y-m-d H:i:s');
			}

			$totalWeight = 0;
			$homePoint = 0;
			$hotelPoint = 0;

			$rewardPoints = 0;
			$orderAmount = 0;
			$orderQuantity = 0;
			$orderReturnQty = 0;
			$orderCount = 0;
			$totalOfferAmount = 0;
			$totalTaxAmount = 0;
			if (count($orderItems) < 1) throw new \Exception('Need Minimum of One Product');
			foreach ($orderItems as $orderItem) {
				// if (isset($orderItem['itemQty']) && (int) $orderItem['itemQty'] > 0) {
				$orderItem['itemQty'] = isset($orderItem['itemQty']) ? $orderItem['itemQty'] : 0;
				if ((isset($orderItem['itemQty']) && (int) $orderItem['itemQty'] > 0) || (isset($orderItem['returnQty']) && (int) $orderItem['returnQty'] > 0)) {
					$product = $this->db->get_where('product', array('id' => $orderItem['itemProductId']))->row();
					$orderOfferAmount = 0;
					$freeItem = 0;
					$offerType = isset($orderItem['itemOfferType']) ? $orderItem['itemOfferType'] : 'percentage';
					if (isset($orderItem['itemOfferValue'])) {

						if (isset($orderItem['itemFreeItem'])) $freeItem += (int) $orderItem['itemFreeItem'];
						// echo 'offer type' . $orderItem['itemOfferType'];
						if ($offerType === 'percentage') {
							// If the item offer type is 'percentage'
							$percentage = (float) $orderItem['itemOfferValue']; // Convert the percentage to a float
							$productPrice = (float) ($orderItem['itemQty'] * $product->productPrice); // Convert the product price to a float
							$orderOfferAmount = ($percentage / 100) * $productPrice; // Calculate the offer amount
						} else {
							// print_r($orderItem['itemOfferType']);
							// If the item offer type is not 'percentage' (e.g., it could be a fixed value or something else)
							$orderOfferAmount = 0; // Set offerAmount to 0 or handle it based on your specific requirements
							$offerAmount = (float) $orderItem['itemOfferValue']; // Convert the percentage to a float
							$productPrice = (float) ($orderItem['itemQty'] * $product->productPrice); // Convert the product price to a float
							$orderOfferAmount = $offerAmount; // Calculate the offer amount
						}
					}
					$orderCount++;
					$totalOfferAmount += $orderOfferAmount;
					$totalTaxAmount += (float) ($product->productTax * $product->productPrice * $orderItem['itemQty']) / 100;

					$order_item['id'] = generate_uuid();
					$order_item['itemOrderId'] = $insert['id'];
					$order_item['itemProductId'] = $product->id;
					$order_item['itemPrice'] = $product->productPrice;
					$order_item['itemTax'] = $product->productTax ? $product->productTax : 0;
					$order_item['itemQty'] = $orderItem['itemQty'];
					$order_item['itemOfferType'] = $offerType;
					if (isset($orderItem['itemOfferValue'])) {
						$order_item['itemOffer'] = $orderItem['itemOfferValue'];
					}
					$order_item['itemOfferAmount'] = $orderOfferAmount;
					$order_item['itemFreeQty'] = $freeItem;
					$order_item['itemTotal'] = $product->productPrice * $orderItem['itemQty'];

					// $rewardPoints					+=	$product->productReward * $orderItem['itemQty'];
					$rewardPoints += $product->productReward * $orderItem['itemQty'];
					$totalWeight					+=	$product->productWeight * $orderItem['itemQty'];
					if ($product->productWeight >= 1000) {
						$hotelPoint					    +=	$product->productReward * $orderItem['itemQty'];
					} else {
						$homePoint					    +=	$product->productReward * $orderItem['itemQty'];
					}

					$orderAmount += $order_item['itemTotal'];
					$orderQuantity += $orderItem['itemQty'];

					if (isset($orderItem['returnQty']) && (int) $orderItem['returnQty'] > 0) {
						$returnQty = (int) $orderItem['returnQty'];

						// Store returnQty in order_item table
						$order_item['returnQty'] = $returnQty;

						// $stockUpdateData = array(
						// 	'returnQty' => $returnQty
						// );
						// $this->db->where('stockDistributorId', $insert['orderDistributorId']);
						// $this->db->where('stockProductId', $order_item['itemProductId']);
						// $this->db->update('stock', $stockUpdateData);
						$localInsertData = [
							'id' => generate_uuid(),
							'stockDistributorId' => $insert['orderDistributorId'],
							'stockProductId' => $order_item['itemProductId'],
							'returnQty' => $returnQty
						];

						$updateData = [
							'returnQty' => $returnQty,
						];


						// Check if the row exists
						$this->db->where('stockDistributorId', $localInsertData['stockDistributorId']);
						$this->db->where('stockProductId', $localInsertData['stockProductId']);
						$query = $this->db->get('stock');

						if ($query->num_rows() > 0) {
							// Row exists, update it
							$this->db->where('stockDistributorId', $localInsertData['stockDistributorId']);
							$this->db->where('stockProductId', $localInsertData['stockProductId']);
							$this->db->update('stock', $updateData);
						} else {
							// Row doesn't exist, insert it
							$this->db->insert('stock', $localInsertData);
						}

						$orderReturnQty += $order_item['returnQty'];
					}
					$this->db->insert('orderitem', $order_item);
				}
			}

			$insert['rewardPoints'] = $rewardPoints;
			$currentMonth = date('m');
			$currentYear = date('y'); // Last two digits of the year

			// $lastInsertedIdQuery = $this->db->query("SELECT MAX(orderTrackingId) AS last_id FROM orders WHERE SUBSTRING(orderTrackingId, 1, 2) = '$currentYear'");
			// $lastInsertedId = $lastInsertedIdQuery->row()->last_id;
			$lastInsertedIdQuery = $this->db->query("SELECT MAX(orderTrackingId) AS last_id FROM orders WHERE SUBSTRING(orderTrackingId, 1, 2) = '$currentYear' AND SUBSTRING(orderTrackingId, 3, 2) = '$currentMonth'");
			$lastInsertedIdResult = $lastInsertedIdQuery->row();

			if ($lastInsertedIdResult && !is_null($lastInsertedIdResult->last_id)) {
				// If previous ID is found, increment by 2
				$lastInsertedId = $lastInsertedIdResult->last_id + 1;
			} else {
				// If no previous ID is found, create a new ID
				$lastInsertedId = $currentYear . $currentMonth . '001';
			}
			// $incrementedId = str_pad($lastInsertedId + 2, 4, '0', STR_PAD_LEFT);
			$insert['orderTrackingId'] = $lastInsertedId;
			$insert['orderAmount'] = $orderAmount - $totalOfferAmount;
			$insert['orderQuantity'] = $orderQuantity;
			$insert['orderReturnQty'] = $orderReturnQty;
			$insert['orderOfferAmount'] = $totalOfferAmount;
			$insert['orderTaxAmount'] = $totalTaxAmount;
			$insert['orderItems'] = $orderCount;
			$insert['orderStatus'] = 2;

			$insert['orderWeight']			= $totalWeight;
			$insert['homePoint']			= $homePoint;
			$insert['hotelPoint']			= $hotelPoint;

			// Update the distributor in the distributor table
			$this->db->set('last_purchase', 'NOW()', false);
			$this->db->set('totalWeight', 'totalWeight + ' . $totalWeight, false);
			$this->db->set('totalOrders', 'totalOrders + 1', false);
			$this->db->set('totalPoints', 'totalPoints + ' . $rewardPoints, false);
			$this->db->set('homePoints', 'homePoints + ' . $homePoint, false);
			$this->db->set('hotelPoints', 'hotelPoints + ' . $hotelPoint, false);
			$this->db->where('id', $insert['orderDistributorId']);
			$this->db->update('distributor');

			$this->db->set('repOrders', 'repOrders + 1', false);
			$this->db->set('repPoints', 'repPoints + ' . $rewardPoints, false);
			$this->db->set('homePoints', 'homePoints + ' . $homePoint, false);
			$this->db->set('hotelPoints', 'hotelPoints + ' . $hotelPoint, false);
			$this->db->where('id', $insert['orderSalesRepId']);
			$this->db->update('rep');

			// $date = $insert['created_date'];
			$currentMonth = date('M');
			$currentYear = date('Y');
			$existingRecordRep = $this->db->get_where('leaderboardrep', array('repId' => $insert['orderSalesRepId'], 'month' => $currentMonth, 'year' => $currentYear))->row();
			if (!empty($existingRecordRep)) {
				$this->db->set('score', 'score + ' . $rewardPoints, false);
				$this->db->where('id', $existingRecordRep->id);
				$this->db->update('leaderboardrep');
			} else {
				$data = array(
					'id' => generate_uuid(),
					'repId' => $insert['orderSalesRepId'],
					'month' => $currentMonth,
					'year' => $currentYear,
					'score' => $rewardPoints // Set an initial score or the desired value
				);
				// echo 'leaderboard rep create';
				// print_r($data);
				$this->db->insert('leaderboardrep', $data);
			}
			$existingRecordTeam = $this->db->get_where('leaderboardteam', array('teamId' => $insert['orderSalesRepTeam'], 'month' => $currentMonth, 'year' => $currentYear))->row();
			if (!empty($existingRecordTeam)) {
				$this->db->set('score', 'score + ' . $rewardPoints, false);
				$this->db->where('id', $existingRecordTeam->id);
				$this->db->update('leaderboardteam');
			} else {
				$data = array(
					'id' => generate_uuid(),
					'teamId' => $insert['orderSalesRepTeam'],
					'month' => $currentMonth,
					'year' => $currentYear,
					'score' => $rewardPoints // Set an initial score or the desired value
				);

				$this->db->insert('leaderboardteam', $data);
			}


			$insert['created_date'] = $createdDate;


			$this->db->insert('orders', $insert);
			$this->db->where('id', $insert['id']);
			$returnData = $this->db->get('orders')->row();
			$this->decrementStock($insert['id'], $insert['orderDistributorId']);
			$this->db->trans_complete();
			return $returnData;
		} catch (Exception $e) {
			print_r($e);
			log_message('error', 'Error in create_teams: ' . $e->getMessage());
			$this->db->trans_rollback();
			throw $e;
		}
	}

	public function update_order($orderId, $data)
	{
		print_r($data);
		// print_r($orderId);
		// Load form validation library
		// $this->load->library('form_validation');

		// Set form data from input request
		// $this->form_validation->set_data($data);

		// Set validation rules for required fields
		// $this->form_validation->set_rules('orderRetailerId', 'Order Retailer Id', 'required|max_length[100]');
		// $this->form_validation->set_rules('orderSalesRepId', 'orderSalesRepId', 'required|max_length[100]');
		// $this->form_validation->set_rules('orderDistributorId', 'orderDistributorId', 'required|max_length[100]');
		// $this->form_validation->set_rules('orderItems', 'orderItems', 'required');

		// Validate form data
		// if ($this->form_validation->run() == false) {
		// 	// Validation failed, return an error message
		// 	return false;
		// }

		// Retrieve order data from the 'orders' table
		$orderData = $this->db->get_where('orders', array('id' => $orderId))->row();

		// Get order items from the 'orderitem' table based on the order ID
		$orderItems = $this->db->get_where('orderitem', array('itemOrderId' => $orderId))->result_array();

		// Extract the old values of distributor ID, sales rep ID, and sales rep team
		$oldDistributorId = $orderData->orderDistributorId;
		$oldSalesRepId = $orderData->orderSalesRepId;
		$oldSalesRepTeam = $orderData->orderSalesRepTeam;
		$oldRewardPoints = $orderData->rewardPoints;
		$oldweight = $orderData->orderWeight;
		$oldhomePoint = $orderData->homePoint;
		$oldhotelPoint = $orderData->hotelPoint;
		$oldweight = $orderData->orderWeight;

		// Retrieve the current sales rep's team from the 'rep' table
		$rep = $this->db->get_where('rep', array('id' => $oldSalesRepId))->row();
		$updateOrder['orderSalesRepTeam'] = $rep->repTeam ?? '';


		// Initialize variables for calculating order summary
		$totalWeight = 0;
		$homePoint = 0;
		$hotelPoint = 0;

		// Update order items
		// Delete existing order items based on the order ID
		$this->db->delete('orderitem', array('itemOrderId' => $orderId));

		// Process and update order items
		$orderCount = 0; // Count the number of updated order items
		$totalOfferAmount = 0; // Sum of offer amounts for updated items
		$rewardPoints = 0; // Total reward points for updated items
		$orderAmount = 0; // Total order amount for updated items
		$orderQuantity = 0; // Total order quantity for updated items
		$totalTaxAmount = 0;
		$orderReturnQty = 0;

		// foreach ($data['orderItems'] as $orderItem) {
		// 	// Check if the item quantity is greater than 0 for update
		// 	if (isset($orderItem['itemQty']) && (int) $orderItem['itemQty'] > 0) {
		// 		$product = $this->db->get_where('product', array('id' => $orderItem['itemProductId']))->row();

		// 		// Calculate the offer amount based on the item's offer type
		// 		$orderOfferAmount = 0;
		// 		$freeItem = 0;
		// 		$offerType = '';
		// 		if (isset($orderItem['itemOfferValue'])) {

		// 			// Check if free item quantity is specified
		// 			if (isset($orderItem['itemFreeItem'])) $freeItem += (int) $orderItem['itemFreeItem'];

		// 			// Get the offer type from the order item data
		// 			$offerType = isset($orderItem['itemOfferType']) ? $orderItem['itemOfferType'] : 'percentage';

		// 			// Calculate offer amount based on offer type
		// 			if ($offerType === 'percentage') {
		// 				// If offer type is 'percentage'
		// 				$percentage = (float) $orderItem['itemOfferValue']; // Convert percentage to float
		// 				$productPrice = (float) ($orderItem['itemQty'] * $product->productPrice); // Convert product price to float
		// 				$orderOfferAmount = ($percentage / 100) * $productPrice; // Calculate offer amount
		// 			} else {
		// 				// If offer type is not 'percentage' (e.g., fixed value or other)
		// 				$offerAmount = (float) $orderItem['itemOfferValue']; // Convert offer amount to float
		// 				$productPrice = (float) ($orderItem['itemQty'] * $product->productPrice); // Convert product price to float
		// 				$orderOfferAmount = $offerAmount; // Set offer amount
		// 			}
		// 		}

		// 		// Update order item details
		// 		$order_item['id'] = generate_uuid(); // Generate unique order item ID
		// 		$order_item['itemOrderId'] = $orderId; // Set order ID
		// 		$order_item['itemProductId'] = $product->id; // Set product ID
		// 		$order_item['itemPrice'] = $product->productPrice; // Set item price
		// 		$order_item['itemQty'] = $orderItem['itemQty']; // Set item quantity
		// 		$order_item['itemOfferType'] = $offerType; // Set offer type
		// 		$order_item['itemOffer'] = isset($orderItem['itemOfferValue']) ? $orderItem['itemOfferValue'] : null; // Set offer value if present
		// 		$order_item['itemOfferAmount'] = $orderOfferAmount; // Set offer amount
		// 		$order_item['itemFreeQty'] = $freeItem; // Set free item quantity
		// 		$order_item['itemTotal'] = $product->productPrice * $orderItem['itemQty']; // Calculate item total

		// 		// Insert updated order item into the 'orderitem' table
		// 		$this->db->insert('orderitem', $order_item);

		// 		// Update accumulated values for order summary
		// 		$rewardPoints                   +=  $product->productReward * $orderItem['itemQty']; // Add reward points
		// 		$totalWeight                    +=  $product->productWeight * $orderItem['itemQty']; // Add total weight
		// 		if ($product->productWeight >= 1000) {
		// 			$hotelPoint                     +=  $product->productReward * $orderItem['itemQty']; // Add hotel points for heavier items
		// 		} else {
		// 			$homePoint                      +=  $product->productReward * $orderItem['itemQty']; // Add home points for lighter items
		// 		}

		// 		// Update order summary values
		// 		$orderAmount += $order_item['itemTotal'];
		// 		$orderQuantity += $orderItem['itemQty'];

		// 		// Increment order count
		// 		$orderCount++;

		// 		// Sum offer amounts for order summary
		// 		$totalOfferAmount += $orderOfferAmount;
		// 		$totalTaxAmount += (float) ($product->productTax * $product->productPrice * $orderItem['itemQty']) / 100;
		// 	}
		// }
		$orderItems = $data;
		if (count($orderItems) < 1) throw new \Exception('Need Minimum of One Product');
		foreach ($orderItems as $orderItem) {
			// if (isset($orderItem['itemQty']) && (int) $orderItem['itemQty'] > 0) {
			$orderItem['itemQty'] = isset($orderItem['itemQty']) ? $orderItem['itemQty'] : 0;
			if ((isset($orderItem['itemQty']) && (int) $orderItem['itemQty'] > 0) || (isset($orderItem['returnQty']) && (int) $orderItem['returnQty'] > 0)) {
				$product = $this->db->get_where('product', array('id' => $orderItem['itemProductId']))->row();
				$orderOfferAmount = 0;
				$freeItem = 0;
				$offerType = isset($orderItem['itemOfferType']) ? $orderItem['itemOfferType'] : 'percentage';
				if (isset($orderItem['itemOfferValue'])) {

					if (isset($orderItem['itemFreeItem'])) $freeItem += (int) $orderItem['itemFreeItem'];
					// echo 'offer type' . $orderItem['itemOfferType'];
					if ($offerType === 'percentage') {
						// If the item offer type is 'percentage'
						$percentage = (float) $orderItem['itemOfferValue']; // Convert the percentage to a float
						$productPrice = (float) ($orderItem['itemQty'] * $product->productPrice); // Convert the product price to a float
						$orderOfferAmount = ($percentage / 100) * $productPrice; // Calculate the offer amount
					} else {
						// print_r($orderItem['itemOfferType']);
						// If the item offer type is not 'percentage' (e.g., it could be a fixed value or something else)
						$orderOfferAmount = 0; // Set offerAmount to 0 or handle it based on your specific requirements
						$offerAmount = (float) $orderItem['itemOfferValue']; // Convert the percentage to a float
						$productPrice = (float) ($orderItem['itemQty'] * $product->productPrice); // Convert the product price to a float
						$orderOfferAmount = $offerAmount; // Calculate the offer amount
					}
				}
				$orderCount++;
				$totalOfferAmount += $orderOfferAmount;
				$totalTaxAmount += (float) ($product->productTax * $product->productPrice * $orderItem['itemQty']) / 100;

				$order_item['id'] = generate_uuid();
				$order_item['itemOrderId'] = $orderData->orderDistributorId;
				$order_item['itemProductId'] = $product->id;
				$order_item['itemPrice'] = $product->productPrice;
				$order_item['itemTax'] = $product->productTax ? $product->productTax : 0;
				$order_item['itemQty'] = $orderItem['itemQty'];
				$order_item['itemOfferType'] = $offerType;
				if (isset($orderItem['itemOfferValue'])) {
					$order_item['itemOffer'] = $orderItem['itemOfferValue'];
				}
				$order_item['itemOfferAmount'] = $orderOfferAmount;
				$order_item['itemFreeQty'] = $freeItem;
				$order_item['itemTotal'] = $product->productPrice * $orderItem['itemQty'];

				// $rewardPoints					+=	$product->productReward * $orderItem['itemQty'];
				$rewardPoints += $product->productReward * $orderItem['itemQty'];
				$totalWeight					+=	$product->productWeight * $orderItem['itemQty'];
				if ($product->productWeight >= 1000) {
					$hotelPoint					    +=	$product->productReward * $orderItem['itemQty'];
				} else {
					$homePoint					    +=	$product->productReward * $orderItem['itemQty'];
				}

				$orderAmount += $order_item['itemTotal'];
				$orderQuantity += $orderItem['itemQty'];

				if (isset($orderItem['returnQty']) && (int) $orderItem['returnQty'] > 0) {
					$returnQty = (int) $orderItem['returnQty'];

					// Store returnQty in order_item table
					$order_item['returnQty'] = $returnQty;

					// $stockUpdateData = array(
					// 	'returnQty' => $returnQty
					// );
					// $this->db->where('stockDistributorId', $insert['orderDistributorId']);
					// $this->db->where('stockProductId', $order_item['itemProductId']);
					// $this->db->update('stock', $stockUpdateData);
					$localInsertData = [
						'id' => generate_uuid(),
						'stockDistributorId' => $orderData->orderDistributorId,
						'stockProductId' => $order_item['itemProductId'],
						'returnQty' => $returnQty
					];

					$updateData = [
						'returnQty' => $returnQty,
					];


					// Check if the row exists
					$this->db->where('stockDistributorId', $localInsertData['stockDistributorId']);
					$this->db->where('stockProductId', $localInsertData['stockProductId']);
					$query = $this->db->get('stock');

					if ($query->num_rows() > 0) {
						// Row exists, update it
						$this->db->where('stockDistributorId', $localInsertData['stockDistributorId']);
						$this->db->where('stockProductId', $localInsertData['stockProductId']);
						$this->db->update('stock', $updateData);
					} else {
						// Row doesn't exist, insert it
						$this->db->insert('stock', $localInsertData);
					}

					$orderReturnQty += $order_item['returnQty'];
				}
				$this->db->insert('orderitem', $order_item);
			}
		}

		// Update order summary details
		$updateOrder['rewardPoints'] = $rewardPoints;
		$updateOrder['orderAmount'] = $orderAmount - $totalOfferAmount; // Deduct total offer amount from total order amount
		$updateOrder['orderQuantity'] = $orderQuantity;
		$updateOrder['orderOfferAmount'] = $totalOfferAmount;
		$updateOrder['orderItems'] = $orderCount;

		$updateOrder['orderWeight'] = $totalWeight;
		$updateOrder['homePoint'] = $homePoint;
		$updateOrder['hotelPoint'] = $hotelPoint;


		// Update the 'orders' table with the updated order details
		$this->db->where('id', $orderId);
		$this->db->update('orders', $updateOrder);

		// Update the 'distributor' table with updated order details for the order's distributor
		$this->db->set('last_purchase', 'NOW()', false);

		// Adjust new reward points based on old reward points
		$newRewardPoints = max($oldRewardPoints, $rewardPoints); // Take the maximum of old and new reward points
		$this->db->set('totalPoints', 'totalPoints + ' . ($newRewardPoints - $oldRewardPoints), false);

		// Adjust new total weight based on old total weight
		$newTotalWeight = max($orderData->orderWeight, $totalWeight); // Take the maximum of old and new total weight
		$this->db->set('totalWeight', 'totalWeight + ' . ($newTotalWeight - max($orderData->orderWeight, $totalWeight)), false);
		$this->db->where('id', $oldDistributorId);
		$this->db->update('distributor');

		$this->db->set('repPoints', 'repPoints + ' . ($newRewardPoints - $oldRewardPoints), false);
		$this->db->where('id', $oldSalesRepId);
		$this->db->update('rep');

		// Update leaderboard records for sales rep and team based on current date and year
		$currentMonth = date('M'); // Get current month
		$currentYear = date('Y'); // Get current year

		// Update 'leaderboardrep' table
		$existingRecordRep = $this->db->get_where('leaderboardrep', array('repId' => $oldSalesRepId, 'month' => $currentMonth, 'year' => $currentYear))->row(); // Check for existing record
		if (!empty($existingRecordRep)) {
			// Update existing record
			$this->db->set('score', 'score + ' . ($newRewardPoints - $oldRewardPoints), false);
			$this->db->where('id', $existingRecordRep->id);
			$this->db->update('leaderboardrep');
		} else {
			// Create new record if one doesn't exist
			$data = array(
				'id' => generate_uuid(),
				'repId' => $oldSalesRepId,
				'month' => $currentMonth,
				'year' => $currentYear,
				'score' => $rewardPoints // Set initial score or desired value
			);

			// Insert new leaderboard record
			$this->db->insert('leaderboardrep', $data);
		}

		// Update 'leaderboardteam' table
		$existingRecordTeam = $this->db->get_where('leaderboardteam', array('teamId' => $oldSalesRepTeam, 'month' => $currentMonth, 'year' => $currentYear))->row(); // Check for existing record
		if (!empty($existingRecordTeam)) {
			// Update existing record
			$this->db->set('score', 'score + ' . ($newRewardPoints - $oldRewardPoints), false);
			$this->db->where('id', $existingRecordTeam->id);
			$this->db->update('leaderboardteam');
		} else {
			// Create new record if one doesn't exist
			$data = array(
				'id' => generate_uuid(),
				'teamId' => $oldSalesRepTeam,
				'month' => $currentMonth,
				'data' => array(
					'id' => generate_uuid(),
					'teamId' => $oldSalesRepTeam,
					'month' => $currentMonth,
					'year' => $currentYear,
					'score' => $rewardPoints // Set initial score or desired value
				)
			);

			// Insert new leaderboard record
			$this->db->insert('leaderboardteam', $data);
		}

		// Decrement product stock based on updated order items
		if (count($orderItems) > 0) {
			foreach ($orderItems as $orderItem) {
				$productId = $orderItem['itemProductId'];
				$itemQty = $orderItem['itemQty'];
				$this->decrementStock($productId, $itemQty);
			}
		}

		// Retrieve and return the updated order data
		$updatedOrder = $this->db->get_where('orders', array('id' => $orderId))->row();
		return $updatedOrder;
	}




	// public function update_order($orderId, $data)
	// {

	// 	$this->db->where('itemOrderId', $orderId);
	// 	$this->db->delete('orderitem');

	// 	$totalWeight = 0;
	// 	$homePoint = 0;
	// 	$hotelPoint = 0;
	// 	$rewardPoints 				=	0;
	// 	$orderAmount				=	0;
	// 	$orderQuantity				=	0;
	// 	$orderCount					=	0;
	// 	foreach ($data['orderItems'] as $orderItem) {
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
	// 		$totalWeight					+=	$product->productWeight * $orderItem['itemQty'];
	// 		if ($product->productWeight >= 1000) {
	// 			$hotelPoint					    +=	$product->productReward * $orderItem['itemQty'];
	// 		} else {
	// 			$homePoint					    +=	$product->productReward * $orderItem['itemQty'];
	// 		}
	// 		$orderAmount					+=	$order_item['itemTotal'];
	// 		$orderQuantity					+=	$orderItem['itemQty'];
	// 	}

	// 	$update['orderAmount']				= $orderAmount;
	// 	$update['orderQuantity']			= $orderQuantity;
	// 	$update['orderItems']				= $orderCount;
	// 	$update['orderWeight']			= $totalWeight;
	// 	$update['homePoint']			= $homePoint;
	// 	$update['hotelPoint']			= $hotelPoint;

	// 	$this->db->where('id', $orderId);
	// 	$this->db->update('orders', $update);
	// 	$this->decrementStock($orderId);
	// 	return $this->db->get('orders')->row();
	// }


	function incrementStock($orderId, $distributorId = '')
	{
		$this->db->where('itemOrderId', $orderId);
		$orderItems = $this->db->get('orderitem')->result();

		foreach ($orderItems as $item) {
			$this->db->select(['stockQty', 'id']);
			$this->db->where('stockProductId', $item->itemProductId);
			$this->db->where('stockDistributorId', $distributorId);
			$stockQuery = $this->db->get('stock')->row();

			if ($stockQuery) {
				$currentStockQty = $stockQuery->stockQty;
				$stockId = $stockQuery->id;
				$totalQty = $item->itemQty + $item->itemFreeQty;

				// Update product table
				// $this->db->set('initialStock', 'initialStock + ' . $totalQty, false);
				// $this->db->where('itemProductId', $item->itemProductId);
				// $this->db->update('product');

				// Update stock table
				$this->db->set('stockQty', 'stockQty + ' . $totalQty, false);
				$this->db->where('id', $stockId);
				$this->db->update('stock');
			}
		}
		return true;
	}

	// function decrementStock($orderId, $distributorId = '',$orderItemsOld=[], $isUpdate = false)
	// {
	// 	$this->db->where('itemOrderId', $orderId);
	// 	$orderItems = $this->db->get('orderitem')->result();

	// 	foreach ($orderItems as $index => $item) {
	// 		$this->db->select(['stockQty', 'id']);
	// 		$this->db->where('stockProductId', $item->itemProductId);
	// 		$this->db->where('stockDistributorId', $distributorId);
	// 		$stockQuery = $this->db->get('stock')->row();

	// 		if ($stockQuery) {
	// 			$currentStockQty = $stockQuery->stockQty;
	// 			$stockId = $stockQuery->id;

	// 			// Calculate the total quantity based on whether it's an update operation
	// 			$totalQty = (int)$item->itemQty + (int)$item->itemFreeQty;

	// 			// Calculate the difference between new and old quantities
	// 			if($isUpdate)
	// 			{
	// 				$qtyDifference = $totalQty - ($orderItemsOld[$index]['itemQty'] + $orderItemsOld[$index]['itemFreeQty']);

	// 			}

	// 			// Update stock table
	// 			$this->db->set('stockQty', 'stockQty ' . ($qtyDifference >= 0 ? '+' : '-') . ' ' . abs($qtyDifference), false);
	// 			$this->db->where('id', $stockId);
	// 			$this->db->update('stock');
	// 		}
	// 	}
	// 	return true;
	// }

	function decrementStock($orderId, $distributorId = '')
	{
		$this->db->where('itemOrderId', $orderId);
		$orderItems = $this->db->get('orderitem')->result();
		foreach ($orderItems as $item) {
			$this->db->select(['stockQty', 'id']);
			$this->db->where('stockProductId', $item->itemProductId);
			$this->db->where('stockDistributorId', $distributorId);
			$stockQuery = $this->db->get('stock')->row();
			if ($stockQuery) {
				$currentStockQty = $stockQuery->stockQty;
				$stockId = $stockQuery->id;
				$totalQty = $item->itemQty + $item->itemFreeQty;
				$this->db->set('stockQty', 'stockQty - ' . $totalQty, false);
				$this->db->where('id', $stockId);
				$this->db->update('stock');
			}
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
		$this->db->select('orderStatus, COUNT(*) as count, SUM(CASE WHEN orderStatus != 4 THEN orderWeight ELSE 0 END) as totalWeight, SUM(CASE WHEN orderStatus != 4 THEN rewardPoints ELSE 0 END) as totalRewardPoints');
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
			'totalWeight' => 0,
			'totalRewardPoints' => 0,
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
				$orderStatuses['totalWeight'] += $row->totalWeight;
				$orderStatuses['totalRewardPoints'] += $row->totalRewardPoints;
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
			'repOrders' => array(),
			'teamOrders' => array(),
			'productOrders' => array(),
			'orderPoints' => array(
				'cityPoints' => 0,
				'teamPoints' => 0,
				'orderPoints' => 0,
				'repPoints' => 0,
			),
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

		$this->db->select('orders.*, retailer.retailerCity, city.cityName, retailer.retailerRoute,rep.firstName, route.routeName,teams.teamName, orderitem.itemProductId, product.productName, orderitem.itemQty,orders.homePoint, orders.hotelPoint, orders.orderWeight,orders.rewardPoints, orders.orderSalesRepId, orders.orderDistributorId, orders.orderSalesRepTeam, orders.orderRetailerId');
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

		// Join the teams table
		$this->db->join('rep', 'rep.id = orders.orderSalesRepId', 'left');

		// Join the teams table
		$this->db->join('teams', 'teams.id = orders.orderSalesRepTeam', 'left');

		// Join the city table
		$this->db->join('city', 'city.id = retailer.retailerCity', 'left');

		// Join the route table
		$this->db->join('route', 'route.id = retailer.retailerRoute', 'left');

		// Join the orderItems table
		$this->db->join('orderitem', 'orderitem.itemOrderId = orders.id', 'left');

		// Join the product table using itemProductId
		$this->db->join('product', 'product.id = orderitem.itemProductId', 'left');

		$query = $this->db->get();

		$salesRepRewardPoints = array();
		$distributorRewardPoints = array();
		$teamRewardPoints = array();
		$retailerRewardPoints = array();
		// echo 'query' . $query;
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

				// Group orders by sales rep
				$repId = $row->orderSalesRepId;
				$repName = $row->firstName;
				if (!isset($response['repOrders'][$repId])) {
					$response['repOrders'][$repId] = array(
						'id' => $repId,
						'repName' => $repName,
						'orderCount' => 0,
					);
				}
				$response['repOrders'][$repId]['orderCount']++;

				// Group orders by team
				$teamId = $row->orderSalesRepTeam;
				$teamName = $row->teamName;
				if (!isset($response['teamOrders'][$teamId])) {
					$response['teamOrders'][$teamId] = array(
						'id' => $teamId,
						'teamName' => $teamName,
						'orderCount' => 0,
					);
				}
				$response['teamOrders'][$teamId]['orderCount']++;

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
				if ($row->orderStatus != 4) {
					$cityId = $row->retailerCity;
					$routeId = $row->retailerRoute;
					$repId = $row->orderSalesRepId;
					$teamId = $row->orderSalesRepTeam;
					$productId = $row->itemProductId;

					// City points
					if (!isset($response['cityOrders'][$cityId]['orderPoints'])) {
						$response['cityOrders'][$cityId]['orderPoints'] = 0;
					}
					$response['cityOrders'][$cityId]['orderPoints'] += $row->rewardPoints;

					// Route points
					if (!isset($response['routeOrders'][$routeId]['orderPoints'])) {
						$response['routeOrders'][$routeId]['orderPoints'] = 0;
					}
					$response['routeOrders'][$routeId]['orderPoints'] += $row->rewardPoints;

					// Rep points
					if (!isset($response['repOrders'][$repId]['orderPoints'])) {
						$response['repOrders'][$repId]['orderPoints'] = 0;
					}
					$response['repOrders'][$repId]['orderPoints'] += $row->rewardPoints;

					// Team points
					if (!isset($response['teamOrders'][$teamId]['orderPoints'])) {
						$response['teamOrders'][$teamId]['orderPoints'] = 0;
					}
					$response['teamOrders'][$teamId]['orderPoints'] += $row->rewardPoints;

					// Product points
					if (!isset($response['productOrders'][$productId]['orderPoints'])) {
						$response['productOrders'][$productId]['orderPoints'] = 0;
					}
					$response['productOrders'][$productId]['orderPoints'] += $row->rewardPoints;


					// City Weight
					if (!isset($response['cityOrders'][$cityId]['orderWeight'])) {
						$response['cityOrders'][$cityId]['orderWeight'] = 0;
					}
					$response['cityOrders'][$cityId]['orderWeight'] += $row->orderWeight;

					// Route points
					if (!isset($response['routeOrders'][$routeId]['orderWeight'])) {
						$response['routeOrders'][$routeId]['orderWeight'] = 0;
					}
					$response['routeOrders'][$routeId]['orderWeight'] += $row->orderWeight;

					// Rep points
					if (!isset($response['repOrders'][$repId]['orderWeight'])) {
						$response['repOrders'][$repId]['orderWeight'] = 0;
					}
					$response['repOrders'][$repId]['orderWeight'] += $row->orderWeight;

					// Team points
					if (!isset($response['teamOrders'][$teamId]['orderWeight'])) {
						$response['teamOrders'][$teamId]['orderWeight'] = 0;
					}
					$response['teamOrders'][$teamId]['orderWeight'] += $row->orderWeight;

					// Product points
					if (!isset($response['productOrders'][$productId]['orderWeight'])) {
						$response['productOrders'][$productId]['orderWeight'] = 0;
					}
					$response['productOrders'][$productId]['orderWeight'] += $row->orderWeight;




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
		$response['teamOrders'] = array_values($response['teamOrders']);
		$response['repOrders'] = array_values($response['repOrders']);

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
