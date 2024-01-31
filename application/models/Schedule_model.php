<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Schedule_model extends CI_Model
{


	// public function get_all_schedule()
	// {
	// 	$this->db->select('id AS salesRepId');
	// 	$query = $this->db->get('rep');
	// 	$results = $query->result();
	// 	$count = $query->num_rows();

	// 	$today  = date('Y-m-d');


	// 	if (isset($_GET['scheduleDate'])) $today  = date('Y-m-d', strtotime($_GET['scheduleDate']));
	// 	// else $scheduleDate  = date('Y-m-d', strtotime($_GET['scheduleDate']));



	// 	foreach ($results as $result) {


	// 		$result->salesRepId 	= $result->salesRepId;
	// 		$result->scheduleDate 	= $today;
	// 		$result->routeId 		= NULL;

	// 		$schedule  = $this->db->select('*')->where(['salesRepId' => $result->salesRepId, 'scheduleDate' => $today])->get('schedule')->row();
	// 		if ($schedule) :
	// 			$result->routeId 		= $schedule->routeId;
	// 			$result->scheduleDate 	= $today;
	// 		endif;



	// 		if (isset($_GET['populate']) && $_GET['populate'] == true) :


	// 			$salesRepQuery = $this->db->get_where('rep', array('id' => $result->salesRepId));
	// 			$salesRepInfo = $salesRepQuery->row();
	// 			$salesRepCityQuery = $this->db->get_where('city', array('id' => $salesRepInfo->repCity));
	// 			$salesRepCity = $salesRepCityQuery->row();
	// 			$salesRepInfo->repCity = $salesRepCity;
	// 			$salesRepTeamQuery = $this->db->get_where('teams', array('id' => $salesRepInfo->repTeam));
	// 			$salesRepTeam = $salesRepTeamQuery->row();
	// 			$salesRepInfo->repTeam = $salesRepTeam;
	// 			$result->salesRepId = $salesRepInfo;

	// 			if ($result->routeId) :
	// 				$routeQuery = $this->db->get_where('route', array('id' => $result->routeId));
	// 				$routeInfo = $routeQuery->row();
	// 				$result->routeId = $routeInfo;
	// 			endif;


	// 		endif;

	// 		$data['data'][] = $result;
	// 	}
	// 	$data['count'] = $count;
	// 	return $data;
	// }

	public function get_all_schedule()
	{
		$this->db->select('id AS salesRepId, repCity AS cityId');

		// Apply search filter if 'search' parameter is present
		if (isset($_GET['search']) && $_GET['search']) {
			$this->db->like('firstName', $_GET['search']);
		}

		$query = $this->db->get('rep');
		$results = $query->result();
		$count = $query->num_rows();

		$today = date('Y-m-d');

		// Use 'scheduleDate' parameter if present, otherwise default to today
		if (isset($_GET['scheduleDate'])) {
			$today = date('Y-m-d', strtotime($_GET['scheduleDate']));
		}

		$uniqueCityNames = array(); // Added for unique city names

		foreach ($results as $result) {
			$result->salesRepId = $result->salesRepId;
			$result->scheduleDate = $today;
			$result->routeIds = array();
			$result->routeNames = array(); // Added for route names
			$result->uniqueCityNames = array(); // Added for unique city names

			// Apply cityId and routeId filters if present
			if (isset($_GET['cityId'])) {
				$result->cityId = $_GET['cityId'];
				$this->db->where('repCity', $result->cityId);
			}

			if (isset($_GET['routeId'])) {
				$result->routeIds[] = $_GET['routeId'];
				$this->db->where('routeId', $result->routeIds[0]);
			}

			// Fetch all routeIds for the sales representative on the given date
			$routeIdsQuery = $this->db->select('routeId')->where(['salesRepId' => $result->salesRepId, 'scheduleDate' => $today])->get('schedule');
			$routeIdsResult = $routeIdsQuery->result();

			foreach ($routeIdsResult as $routeIdResult) {
				$result->routeIds[] = $routeIdResult->routeId;
			}

			$this->db->order_by('created_date', 'asc');
			if (isset($_GET['populate']) && $_GET['populate'] == true) {
				// Fetch related data for sales representative
				$this->db->select('*');
				$salesRepQuery = $this->db->get_where('rep', array('id' => $result->salesRepId));
				$salesRepInfo = $salesRepQuery->row();

				if ($salesRepInfo) {
					// Fetch related data for sales representative city
					$this->db->select('*');
					$salesRepCityQuery = $this->db->get_where('city', array('id' => $salesRepInfo->repCity));
					$salesRepCity = $salesRepCityQuery->row();
					$salesRepInfo->repCity = $salesRepCity;

					// Fetch related data for sales representative team
					$this->db->select('*');
					$salesRepTeamQuery = $this->db->get_where('teams', array('id' => $salesRepInfo->repTeam));
					$salesRepTeam = $salesRepTeamQuery->row();
					$salesRepInfo->repTeam = $salesRepTeam;

					$result->salesRepId = $salesRepInfo;

					// Fetch related data for each route
					foreach ($result->routeIds as $routeId) {
						$this->db->select('routeName, routeCity'); // Select routeName and routeCity
						$routeQuery = $this->db->get_where('route', array('id' => $routeId));
						$routeInfo = $routeQuery->row();
						$result->routeNames[] = $routeInfo->routeName; // Add routeName to the list

						// Fetch related data for route city
						$this->db->select('cityName'); // Select only cityName
						$cityQuery = $this->db->get_where('city', array('id' => $routeInfo->routeCity));
						$cityInfo = $cityQuery->row();
						$result->uniqueCityNames[$cityInfo->cityName] = $cityInfo->cityName; // Store unique city names
					}
					$result->cities = array_values($result->uniqueCityNames); // Store unique city names
				}
			}

			$data['data'][] = $result;
		}

		// $data['uniqueCityNames'] = array_values($uniqueCityNames); // Convert keys to values in the final result
		$data['count'] = $count;
		return $data;
	}






	public function get_schedule_by_month_and_rep()
	{
		$salesRepId = isset($_GET['salesRepId']) ? $_GET['salesRepId'] : null;
		$monthYear = isset($_GET['monthYear']) ? $_GET['monthYear'] : date('m-Y');

		// Validate salesRepId
		if (!$salesRepId) throw new \Exception('Invalid or missing salesRepId parameter');

		// Validate monthYear
		if (!preg_match('/^(0?[1-9]|1[0-2])-\d{4}$/', $monthYear)) throw new \Exception('Invalid or missing Month/Year parameter');

		// Extract month and year from the provided "01-2023" format
		list($month, $year) = explode('-', $monthYear);

		// Formulate the start and end dates for the specified month and year
		$startDate = date("Y-m-d", strtotime("first day of $year-$month"));
		$endDate = date("Y-m-d", strtotime("last day of $year-$month"));

		// Get the schedule for the specified sales representative, month, and year
		$query = $this->db
			->select('s.*, r.routeName, COUNT(o.orderTrackingId) as orderCount')
			->from('schedule s')
			->join('route r', 's.routeId = r.id', 'left')
			// ->join('city c', 's.cityId = c.id', 'left')
			->join('orders o', 's.salesRepId = o.orderSalesRepId AND DATE(s.scheduleDate) = DATE(o.created_date)', 'left')
			->where(['s.salesRepId' => $salesRepId])
			->where("s.scheduleDate BETWEEN '$startDate' AND '$endDate'", null, false)
			->group_by('s.scheduleDate')  // Group by scheduleDate to get counts for each date
			->order_by('s.scheduleDate', 'ASC')  // Order by scheduleDate in descending order
			->get();
		$results = $query->result();
		$resultArray = is_array($results) ? $results : [$results];
		if ($resultArray) return $resultArray;
		else return [];
	}



	/*	
		$this->db->select('a.id,a.salesRepId,a.scheduleDate,a.routeId');
		$this->db->from('schedule a'); 
		
		
		if(isset($_GET['salesRepId']))
			$this->db->where('salesRepId',$_GET['salesRepId']);
		if(isset($_GET['scheduleDate']))
			$this->db->where('scheduleDate',$_GET['scheduleDate']);
		
	
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
					$salesRepQuery = $this->db->get_where('rep', array('id' => $result->salesRepId));
					$salesRepInfo = $salesRepQuery->row();
					$result->salesRepId = $salesRepInfo;
					
					$routeQuery = $this->db->get_where('route', array('id' => $result->routeId));
					$routeInfo = $routeQuery->row();
					$result->routeId = $routeInfo;
					
					
					$data['data'][] = $result;
				 }
				 $data['count']=$count;
				return $data;
			endif;
			$data['data']=$results;
			$data['count']=$count;
			return $data;
		}
		else return false;*/
	// }

	public function get_schedule($id)
	{
		$this->db->select('a.*');
		$this->db->from('schedule a');
		$this->db->where('a.id', $id);
		$query = $this->db->get();
		if ($query->num_rows() != 0) {
			$result = $query->row();
			$count = $query->num_rows();
			$data = array();
			if (isset($_GET['populate']) && $_GET['populate'] == true) :
				$salesRepQuery = $this->db->get_where('rep', array('id' => $result->salesRepId));
				$salesRepInfo = $salesRepQuery->row();
				$salesRepCityQuery = $this->db->get_where('city', array('id' => $salesRepInfo->repCity));
				$salesRepCity = $salesRepCityQuery->row();
				$salesRepInfo->repCity = $salesRepCity;
				$salesRepTeamQuery = $this->db->get_where('teams', array('id' => $salesRepInfo->repTeam));
				$salesRepTeam = $salesRepTeamQuery->row();
				$salesRepInfo->repTeam = $salesRepTeam;

				$result->salesRepId = $salesRepInfo;



				$routeQuery = $this->db->get_where('route', array('id' => $result->routeId));
				$routeInfo = $routeQuery->row();
				$result->routeId = $routeInfo;


				$data['data'][] = $result;
				$data['count'] = $count;
				return $data;
			endif;
			$data['data'] = $result;
			$data['count'] = $count;
			return $data;
		} else return false;
	}

	// public function get_schedule_by_rep_and_date()
	// {
	// 	$this->db->select('a.salesRepId, a.scheduleDate, GROUP_CONCAT(b.id) as routeIds', false);
	// 	$this->db->from('schedule a');
	// 	$this->db->join('route b', 'a.routeId = b.id', 'left');
	// 	$this->db->where('a.salesRepId', $_GET['salesRepId']);
	// 	$this->db->where('a.scheduleDate', $_GET['scheduleDate']);
	// 	$this->db->group_by('a.salesRepId, a.scheduleDate');

	// 	$query = $this->db->get();

	// 	if ($query->num_rows() !== 0) {
	// 		$result = $query->row();
	// 		$count = $query->num_rows();
	// 		$data = array();

	// 		if (isset($_GET['populate']) && $_GET['populate'] == true) {
	// 			// Fetch related data for sales representative
	// 			$salesRepQuery = $this->db->get_where('rep', array('id' => $result->salesRepId));
	// 			$salesRepInfo = $salesRepQuery->row();

	// 			// Fetch related data for sales representative city
	// 			$salesRepCityQuery = $this->db->get_where('city', array('id' => $salesRepInfo->repCity));
	// 			$salesRepCity = $salesRepCityQuery->row();
	// 			$salesRepInfo->repCity = $salesRepCity;

	// 			// Fetch related data for sales representative team
	// 			$salesRepTeamQuery = $this->db->get_where('teams', array('id' => $salesRepInfo->repTeam));
	// 			$salesRepTeam = $salesRepTeamQuery->row();
	// 			$salesRepInfo->repTeam = $salesRepTeam;

	// 			// Update salesRepId with populated data
	// 			$result->salesRepId = $salesRepInfo;

	// 			// Extract the comma-separated routeIds and convert them into an array
	// 			$routeIds = explode(',', $result->routeIds);

	// 			// Fetch related data for each route and add it to the result
	// 			$result->routes = array();
	// 			foreach ($routeIds as $routeId) {
	// 				$routeQuery = $this->db->get_where('route', array('id' => $routeId));
	// 				$routeInfo = $routeQuery->row();
	// 				$result->routes[] = $routeInfo;
	// 			}

	// 			$data['data'][] = $result;
	// 			$data['count'] = $count;
	// 			return $data;
	// 		}

	// 		$data['data'] = $result;
	// 		$data['count'] = $count;
	// 		return $data;
	// 	} else {
	// 		return array('data' => [], 'count' => 0);
	// 	}
	// }

	public function get_schedule_by_rep_and_date()
	{
		$this->db->select('a.salesRepId, a.scheduleDate, GROUP_CONCAT(b.id) as routeIds', false);
		$this->db->from('schedule a');
		$this->db->join('route b', 'a.routeId = b.id', 'left');
		$this->db->where('a.salesRepId', $_GET['salesRepId']);
		$this->db->where('a.scheduleDate', $_GET['scheduleDate']);
		$this->db->group_by('a.salesRepId, a.scheduleDate');

		$query = $this->db->get();

		if ($query->num_rows() !== 0) {
			$result = $query->row();
			$count = $query->num_rows();
			$data = array();

			if (isset($_GET['populate']) && $_GET['populate'] == true) {
				// Fetch related data for sales representative
				$salesRepQuery = $this->db->get_where('rep', array('id' => $result->salesRepId));
				$salesRepInfo = $salesRepQuery->row();

				// Fetch related data for sales representative city
				$salesRepCityQuery = $this->db->get_where('city', array('id' => $salesRepInfo->repCity));
				$salesRepCity = $salesRepCityQuery->row();
				$salesRepInfo->repCity = $salesRepCity;

				// Fetch related data for sales representative team
				$salesRepTeamQuery = $this->db->get_where('teams', array('id' => $salesRepInfo->repTeam));
				$salesRepTeam = $salesRepTeamQuery->row();
				$salesRepInfo->repTeam = $salesRepTeam;

				// Update salesRepId with populated data
				$result->salesRepId = $salesRepInfo;

				// Fetch related data for routes
				// print_r($result->routeIds);
				// $routeQuery = $this->db->get_where('route', array('id' => $result->routeIds));
				// $routeInfo = $routeQuery->result(); // Use result() to get multiple rows
				// $result->routeIds = $routeInfo;

				$routeIds = explode(',', $result->routeIds);
				$routeQuery = $this->db->where_in('id', $routeIds)->get('route');
				$routeInfo = $routeQuery->result(); // Use result() to get multiple rows
				$result->routeIds = $routeInfo;

				// foreach ($result->routeIds as $routeId) {
				// 	$this->db->select('routeName, routeCity'); // Select routeName and routeCity
				// 	$routeQuery = $this->db->get_where('route', array('id' => $routeId));
				// 	$routeInfo = $routeQuery->row();
				// 	$result->routeNames[] = $routeInfo->routeName; // Add routeName to the list

				// 	// Fetch related data for route city
				// 	$this->db->select('cityName'); // Select only cityName
				// 	$cityQuery = $this->db->get_where('city', array('id' => $routeInfo->routeCity));
				// 	$cityInfo = $cityQuery->row();
				// 	$result->uniqueCityNames[$cityInfo->cityName] = $cityInfo->cityName; // Store unique city names
				// }

				$data['data'][] = $result;
				$data['count'] = $count;
				return $data;
			}

			$data['data'] = $result;
			$data['count'] = $count;
			return $data;
		} else {
			return array('data' => [], 'count' => 0);
		}
	}

	// public function create_schedule($data)
	// {
	// 	$this->load->library('form_validation');
	// 	$this->form_validation->set_data($data);
	// 	$this->form_validation->set_rules('salesRepId', 'Sales Representative', 'required|max_length[50]');
	// 	$this->form_validation->set_rules('scheduleDate', 'Schedule Name', 'required|max_length[100]');
	// 	$this->form_validation->set_rules('routeId', 'Route', 'required|max_length[100]');



	// 	if ($this->form_validation->run() == false) {
	// 		return false; // Validation failed
	// 	}

	// 	$insert['salesRepId'] = $data['salesRepId'];
	// 	$insert['scheduleDate'] = $data['scheduleDate'];
	// 	$insert['routeId'] = $data['routeId'];

	// 	$scheduleDate = date('Y-m-d', strtotime($data['scheduleDate']));
	// 	$salesRepId = $data['salesRepId'];
	// 	$existingSchedule = $this->db->get_where('schedule', array('scheduleDate' => $scheduleDate, 'salesRepId' => $salesRepId))->row();

	// 	if ($existingSchedule) {
	// 		// Update existing record
	// 		$this->db->where('id', $existingSchedule->id);

	// 		if ($this->db->update('schedule', $insert)) {
	// 			return $this->get_schedule($existingSchedule->id);
	// 		} else {
	// 			return false;
	// 		}
	// 	} else {
	// 		// Insert new record
	// 		$insert['id'] = generate_uuid();
	// 		$insert['createdBy'] = getCreatedBy();
	// 		$insert['scheduleDate'] = $scheduleDate;
	// 		$this->db->insert('schedule', $insert);
	// 		if ($this->db->affected_rows() > 0) {
	// 			return $this->get_schedule($insert['id']);
	// 		} else {
	// 			return false;
	// 		}
	// 	}
	// }

	public function create_schedule($data)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('salesRepId', 'Sales Representative', 'required|max_length[50]');
		$this->form_validation->set_rules('scheduleDate', 'Schedule Name', 'required|max_length[100]');
		$this->form_validation->set_rules('routeId', 'Route', 'required|max_length[100]');

		if ($this->form_validation->run() == false) {
			return false; // Validation failed
		}

		// Prepare data for insertion
		$insert['salesRepId'] = $data['salesRepId'];
		$insert['scheduleDate'] = date('Y-m-d', strtotime($data['scheduleDate']));
		$insert['routeId'] = $data['routeId'];

		// Insert new record
		$insert['id'] = generate_uuid();
		$insert['createdBy'] = getCreatedBy();

		$this->db->insert('schedule', $insert);

		if ($this->db->affected_rows() > 0) {
			// Get the inserted record from the 'schedule' table
			$scheduleData = $this->get_schedule($insert['id']);

			// Populate 'routeId' from the 'route' table
			$routeQuery = $this->db->get_where('route', array('id' => $scheduleData['data']->routeId));
			$routeInfo = $routeQuery->row();

			// Update 'routeId' in the returned data with route information
			$scheduleData['data']->routeId = $routeInfo;

			return $scheduleData;
		} else {
			return false;
		}
	}


	public function update_schedule($data)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);

		// Set validation rules for each field
		$this->form_validation->set_rules('salesRepId', 'Sales Representative', 'required|max_length[50]');
		$this->form_validation->set_rules('scheduleDate', 'Schedule Date', 'required|max_length[100]');
		$this->form_validation->set_rules('routeId', 'Route', 'required|max_length[100]');

		// Perform form validation
		if ($this->form_validation->run() === false) {
			return false; // Validation failed
		}

		// Sanitize and prepare data for update
		$update = array(
			'salesRepId' => $data['salesRepId'],
			'scheduleDate' => date('Y-m-d', strtotime($data['scheduleDate'])),
			'routeId' => $data['routeId']
		);

		// Check if the row exists with the specific salesRepId and scheduleDate
		$this->db->where('salesRepId', $data['salesRepId']);
		$this->db->where('scheduleDate', $update['scheduleDate']);
		$query = $this->db->get('schedule');

		// If row not found, create a new row in the 'schedule' table
		if ($query->num_rows() === 0) {
			$this->db->insert('schedule', $update);
		} else {
			// Perform the database update
			$this->db->where('salesRepId', $update['salesRepId']);
			$this->db->where('scheduleDate', $update['scheduleDate']);
			$this->db->update('schedule', $update);
		}

		// Return the updated schedule details
		// return $this->get_schedule_by_rep_and_date($data['salesRepId'], $data['scheduleDate']);

		//     $this->load->library('form_validation');
		//     $this->form_validation->set_data($data);
		//     $this->form_validation->set_rules('salesRepId', 'Sales Representative', 'required|max_length[50]');
		//    $this->form_validation->set_rules('scheduleDate', 'Schedule Name', 'required|max_length[100]');
		//    $this->form_validation->set_rules('routeId', 'Route', 'required|max_length[100]');


		//     if ($this->form_validation->run() == false) {
		//         return false; // Validation failed
		//     }

		// 	$update['salesRepId']=$data['salesRepId'];
		// 	$update['scheduleDate']=$data['scheduleDate'];
		// 	$update['routeId']=$data['routeId'];
		// 	$update['scheduleDate'] =date('Y-m-d', strtotime($data['scheduleDate']));

		return $update;
		//     $this->db->where('id', $id);
		//     $this->db->update('schedule', $update);
		// 	return $this->get_schedule($id);

	}

	public function delete_product($id)
	{
		$schdule  = $this->get_schedule($id);
		if ($schdule) {

			$this->db->where('id', $id);
			$this->db->delete('schedule');

			if ($this->db->affected_rows() > 0) return true;
			else return FALSE;
		} else return FALSE;
	}
}
