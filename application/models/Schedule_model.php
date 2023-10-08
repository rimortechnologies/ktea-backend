<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Schedule_model extends CI_Model
{


	public function get_all_schedule()
	{



		$this->db->select('id AS salesRepId');
		$query = $this->db->get('rep');
		$results = $query->result();
		$count = $query->num_rows();

		$today  = date('Y-m-d');


		if (isset($_GET['scheduleDate'])) $today  = date('Y-m-d', strtotime($_GET['scheduleDate']));
		// else $scheduleDate  = date('Y-m-d', strtotime($_GET['scheduleDate']));



		foreach ($results as $result) {


			$result->salesRepId 	= $result->salesRepId;
			$result->scheduleDate 	= $today;
			$result->routeId 		= NULL;

			$schedule  = $this->db->select('*')->where(['salesRepId' => $result->salesRepId, 'scheduleDate' => $today])->get('schedule')->row();
			if ($schedule) :
				$result->routeId 		= $schedule->routeId;
				$result->scheduleDate 	= $today;
			endif;



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

				if ($result->routeId) :
					$routeQuery = $this->db->get_where('route', array('id' => $result->routeId));
					$routeInfo = $routeQuery->row();
					$result->routeId = $routeInfo;
				endif;


			endif;

			$data['data'][] = $result;
		}
		$data['count'] = $count;
		return $data;
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

	public function get_schedule_by_rep_and_date()
	{
		$this->db->select('a.*');
		$this->db->from('schedule a');
		$this->db->where('a.salesRepId', $_GET['salesRepId']);
		$this->db->where('a.scheduleDate', $_GET['scheduleDate']);
		// $this->db->select('*')->where(['salesRepId' => $_GET['salesRepId'], 'scheduleDate' => $_GET['scheduleDate']]);
		$query = $this->db->get();
		// echo $_GET['salesRepId'];
		// echo $_GET['scheduleDate'];

		if ($query->num_rows() !== 0) {
			$result = $query->row();
			// echo $result;
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

				// Fetch related data for route
				$routeQuery = $this->db->get_where('route', array('id' => $result->routeId));
				$routeInfo = $routeQuery->row();
				$result->routeId = $routeInfo;

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

		$insert['salesRepId'] = $data['salesRepId'];
		$insert['scheduleDate'] = $data['scheduleDate'];
		$insert['routeId'] = $data['routeId'];

		$scheduleDate = date('Y-m-d', strtotime($data['scheduleDate']));
		$salesRepId = $data['salesRepId'];
		$existingSchedule = $this->db->get_where('schedule', array('scheduleDate' => $scheduleDate, 'salesRepId' => $salesRepId))->row();

		if ($existingSchedule) {
			// Update existing record
			$this->db->where('id', $existingSchedule->id);

			if ($this->db->update('schedule', $insert)) {
				return $this->get_schedule($existingSchedule->id);
			} else {
				return false;
			}
		} else {
			// Insert new record
			$insert['id'] = generate_uuid();
			$insert['createdBy'] = getCreatedBy();
			$insert['scheduleDate'] = $scheduleDate;
			$this->db->insert('schedule', $insert);
			if ($this->db->affected_rows() > 0) {
				return $this->get_schedule($insert['id']);
			} else {
				return false;
			}
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
