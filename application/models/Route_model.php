<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Route_model extends CI_Model
{
	// public function get_all_routes()
	// {
	// 	$this->db->select('a.*');
	// 	$this->db->from('route a');


	// 	if (isset($_GET['routeCity']))  $this->db->where('routeCity', $_GET['routeCity']);


	// 	if (isset($_GET['search']) and $_GET['search'] != null) {
	// 		$search  = urldecode($_GET['search']);
	// 		$filters = [
	// 			'routeName' => $search,

	// 		];
	// 	}

	// 	if (isset($filters) && !empty($filters)) {
	// 		$this->db->group_Start();
	// 		$this->db->or_like($filters);
	// 		$this->db->group_End();
	// 	}


	// 	$limit = (isset($_GET['limit']) && is_numeric($_GET['limit']) && !empty(trim($_GET['limit']))) ? $_GET['limit'] : null;
	// 	$offset = (isset($_GET['offset']) && is_numeric($_GET['offset']) && !empty(trim($_GET['offset']))) ? $_GET['offset'] : 0;

	// 	if ($limit != null || $offset != null) {
	// 		$this->db->limit($limit, $offset);
	// 	}




	// 	if (isset($_GET['routeCity'])) {
	// 		$this->db->where('routeCity', $_GET['routeCity']);
	// 	}

	// 	$query = $this->db->get();


	// 	if ($query->num_rows() != 0) {
	// 		$results = $query->result();
	// 		$count = $query->num_rows();
	// 		$data = array();

	// 		if (isset($_GET['populate']) && $_GET['populate'] == true) {
	// 			foreach ($results as $result) {
	// 				$cityQuery = $this->db->get_where('city', array('id' => $result->routeCity));
	// 				$cityInfo = $cityQuery->row();
	// 				$result->routeCity = $cityInfo;
	// 				$stopQuery = $this->db->get_where('stop', array('stopRouteId' => $result->id));
	// 				$stopInfo = $stopQuery->result();
	// 				$result->stops = $stopInfo;
	// 				$data['data'][] = $result;
	// 			}
	// 			$data['count'] = $count;
	// 			return $data;
	// 		}

	// 		$data['data'] = $results;
	// 		$data['count'] = $count;
	// 		return $data;
	// 	} else {
	// 		return FALSE;
	// 	}
	// }

	public function get_all_routes()
	{
		$this->db->select('a.*,city.cityName');
		$this->db->join('city', 'city.id = a.routeCity', 'left');
		$this->db->from('route a');

		if (isset($_GET['routeCity']))  $this->db->where('routeCity', $_GET['routeCity']);

		if (isset($_GET['search']) && $_GET['search'] != null) {
			$search = urldecode($_GET['search']);
			$filters = [
				'routeName' => $search,
			];
		}

		if (isset($filters) && !empty($filters)) {
			$this->db->group_start();
			$this->db->or_like($filters);
			$this->db->group_end();
		}

		$this->db->where('a.deleted', false);

		// Clone the query for counting
		$countQuery = clone $this->db;

		// Get count before applying limit and offset
		$count = $countQuery->count_all_results();

		$sortField = 'a.created_date';
		$orderBy = 'DESC';

		// Sorting logic
		if (isset($_GET['orderBy'])) {
			$orderField = $_GET['orderBy'];
			$orderPrefix = ($orderField[0] === '-') ? 'DESC' : 'ASC';

			switch (ltrim($orderField, '-')) {
				case 'name':
					$sortField = 'a.routeName';
					break;
				case 'city':
					$sortField = 'city.cityName';
					break;
					// Add more cases as needed
			}

			$orderBy = $orderPrefix;
		}


		$limit = (isset($_GET['limit']) && is_numeric($_GET['limit']) && !empty(trim($_GET['limit']))) ? $_GET['limit'] : null;
		$offset = (isset($_GET['offset']) && is_numeric($_GET['offset']) && !empty(trim($_GET['offset']))) ? $_GET['offset'] : 0;

		if ($limit != null || $offset != null) {
			$this->db->limit($limit, $offset);
		}

		if (isset($_GET['routeCity'])) {
			$this->db->where('routeCity', $_GET['routeCity']);
		}

		$this->db->order_by($sortField, $orderBy);

		$query = $this->db->get();

		if ($query->num_rows() != 0) {
			$results = $query->result();
			$data = array();

			if (isset($_GET['populate']) && $_GET['populate'] == true) {
				foreach ($results as $result) {
					$cityQuery = $this->db->get_where('city', array('id' => $result->routeCity));
					$cityInfo = $cityQuery->row();
					$result->routeCity = $cityInfo;
					$stopQuery = $this->db->get_where('stop', array('stopRouteId' => $result->id));
					$stopInfo = $stopQuery->result();
					$result->stops = $stopInfo;
					$data['data'][] = $result;
				}
				$data['count'] = $count;
				return $data;
			}

			$data['data'] = $results;
			$data['count'] = $count;
			return $data;
		} else {
			return FALSE;
		}
	}



	public function get_route($routeId)
	{

		$this->db->select('*');
		$this->db->from('route a');
		$this->db->where('a.id', $routeId);
		$query = $this->db->get();

		if ($query->num_rows() != 0) {
			$result = $query->row();
			$count = $query->num_rows();
			if (isset($_GET['populate']) && $_GET['populate'] == true) :
				$cityQuery = $this->db->get_where('city', array('id' => $result->routeCity));
				$cityInfo = $cityQuery->row();
				$result->routeCity = $cityInfo;
				$stopQuery = $this->db->get_where('stop', array('stopRouteId' => $routeId));
				$stopInfo = $stopQuery->result();
				$result->stops = $stopInfo;
				$data['data'] = $result;
				$data['count'] = $count;
				return $data;
			endif;
			$data['data'] = $result;
			$data['count'] = $count;
			return $data;
		}
		return FALSE;
	}

	public function create_route($data)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('routeName', 'Route Name', 'required|max_length[100]|is_unique[route.routeName]');
		$this->form_validation->set_rules('routeCity', 'Route City', 'required|max_length[100]');

		if ($this->form_validation->run() == false) {
			return false;
		}

		// Create the main route record
		$insert['id'] = generate_uuid();
		$insert['createdBy'] = getCreatedBy();
		$insert['routeName'] = $data['routeName'];
		$insert['routeCity'] = $data['routeCity'];
		$insert['routeDescription'] = $data['routeDescription'];
		$this->db->insert('route', $insert);

		// Create stop records associated with the route
		if (isset($data['stops']) && is_array($data['stops'])) {
			$totalStops = count($data['stops']);

			foreach ($data['stops'] as $index => $stop) {
				$stopData['id'] = generate_uuid();
				$stopData['createdBy'] = getCreatedBy();
				$stopData['stopRouteId'] = $insert['id'];
				$stopData['stopName'] = $stop['stopName'];
				$stopData['stopNumber'] = $stop['stopNumber'];

				// Set isStartingPoint to true for the first stop
				$stopData['isStartingPoint'] = ($index === 0) ? true : false;

				// Set isEndingPoint to true for the last stop
				$stopData['isEndingPoint'] = ($index === $totalStops - 1) ? true : false;

				$this->db->insert('stop', $stopData);
			}
		}

		return $this->get_route($insert['id']);
	}

	public function get_route_by_name($routeName)
	{
		$query = $this->db->get_where('route', array('routeName' => $routeName));
		return $query->row();
	}
	public function update_route($routeId, $data)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('routeName', 'Route Name', 'required|max_length[100]');
		$this->form_validation->set_rules('routeCity', 'Route City', 'required|max_length[100]');

		if ($this->form_validation->run() == false) return false; // Validation failed
		$existingRoute = $this->get_route($routeId);
		if ($existingRoute && $existingRoute['data']->routeName != $data['routeName']) {
			$this->form_validation->set_rules('routeName', 'Route Name', 'required|max_length[100]|is_unique[route.routeName]');
			if ($this->form_validation->run() == false) return false; // Validation failed
		}
		$update['routeName'] = $data['routeName'];
		$update['routeCity'] = $data['routeCity'];
		$update['routeDescription'] = $data['routeDescription'];
		$this->db->where('id', $routeId);
		$this->db->update('route', $update);

		// Update existing stops and add new stops
		foreach ($data['stops'] as $stop) {
			if (isset($stop['id'])) {
				// Update existing stop
				$updateStop['stopName'] = $stop['stopName'];
				$updateStop['stopNumber'] = $stop['stopNumber'];
				$updateStop['isStartingPoint'] = ($stop['stopNumber'] === 1) ? true : false;
				$updateStop['isEndingPoint'] = ($stop['stopNumber'] === count($data['stops'])) ? true : false;

				$this->db->where('id', $stop['id']);
				$this->db->update('stop', $updateStop);
			} else {
				// Add new stop
				$newStop['id'] = generate_uuid();
				$newStop['createdBy'] = getCreatedBy();
				$newStop['stopRouteId'] = $routeId;
				$newStop['stopName'] = $stop['stopName'];
				$newStop['stopNumber'] = $stop['stopNumber'];
				$newStop['isStartingPoint'] = ($stop['stopNumber'] === 1) ? true : false;
				$newStop['isEndingPoint'] = ($stop['stopNumber'] === count($data['stops'])) ? true : false;

				$this->db->insert('stop', $newStop);
			}
		}


		//foreach($data['stops'] AS $stop):


		//if (isset($stop['id'])) {
		//$stopId = $stop['id'];
		//$updateStop['stopName'] 			= $stop['stopName'];
		//$updateStop['stopNumber'] 		= $stop['stopNumber'];
		///$updateStop['isStartingPoint'] 	= $stop['isStartingPoint'];
		///$updateStop['isEndingPoint'] 	= $stop['isEndingPoint'];
		//$this->db->where('id', $stopId);
		//$this->db->update('stop', $updateStop);
		//} else {
		//$insert['id'] 				= generate_uuid();
		//$insert['createdBy'] 		= getCreatedBy();
		//$insert['stopRouteId'] 		= $routeId;
		//$insert['stopName'] 			= $stop['stopName'];
		//$insert['stopNumber'] 		= $stop['stopNumber'];
		//$insert['isStartingPoint'] 	= $stop['isStartingPoint'];
		//$insert['isEndingPoint'] 	= $stop['isEndingPoint'];
		//$this->db->insert('stop', $insert);
		//}


		//endforeach;


		return $this->get_route($routeId);
	}

	public function delete_route($routeId)
	{

		$route  = $this->get_route($routeId);
		if ($route) {
			$data = array('deleted' => true);

			$this->db->where('id', $routeId);

			$this->db->update('route', $data);
			if ($this->db->affected_rows() > 0) return true;
			else return FALSE;
		} else return FALSE;
	}
}
