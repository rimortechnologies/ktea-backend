<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Salesrepresentative_model extends CI_Model
{
	public function get_all_salesrepresentatives()
	{

		$this->db->select('*');
		$this->db->from('rep a');
		if (isset($_GET['repCity']))  $this->db->where('repCity', $_GET['repCity']);


		if (isset($_GET['search']) and $_GET['search'] != null) {
			$search  = urldecode($_GET['search']);
			$filters = [
				'firstName' => $search,
				'lastName' => $search,
				'repEmail' => $search,
				'repContactNumber' => $search,
				'repArea' => $search,
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




		if ($query->num_rows() != 0) {
			$results = $query->result();
			$count = $query->num_rows();
			$data = array();
			if (isset($_GET['populate']) && $_GET['populate'] == true) :

				foreach ($results as $result) {
					$teamQuery = $this->db->get_where('teams', array('id' => $result->repTeam));
					$teamInfo = $teamQuery->row();
					$result->repTeam = $teamInfo;
					$result->isLead = 0;
					if ($teamInfo->teamRepId == $result->id) $result->isLead = 1;

					$cityQuery = $this->db->get_where('city', array('id' => $result->repCity));
					$cityInfo = $cityQuery->row();
					$result->repCity = $cityInfo;

					unset($result->password);
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
	public function get_salesrepresentative($salesrepresentativeId)
	{
		$this->db->select('*');
		$this->db->from('rep a');
		$this->db->where('a.id', $salesrepresentativeId);
		$query = $this->db->get();
		if ($query->num_rows() != 0) {
			$result = $query->row();
			$count = $query->num_rows();
			$data = array();
			if (isset($_GET['populate']) && $_GET['populate'] == true) :

				$cityQuery = $this->db->get_where('city', array('id' => $result->repCity));
				$cityInfo = $cityQuery->row();
				$result->repCity = $cityInfo;
				$teamQuery = $this->db->get_where('teams', array('id' => $result->repTeam));
				$teamInfo = $teamQuery->row();

				$result->isLead = 0;
				if ($teamInfo) if ($teamInfo->teamRepId == $result->id) $result->isLead = 1;

				$result->repTeam = $teamInfo;
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

	public function create_salesrepresentative($data)
	{
		$this->load->library('bcrypt');
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('firstName', 'Representative  Name', 'required|max_length[50]');
		$this->form_validation->set_rules('lastName', 'Representative Last Name', 'required|max_length[100]');
		$this->form_validation->set_rules('repEmail', ' Representative Email', 'max_length[200]|valid_email|is_unique[rep.repEmail]');
		$this->form_validation->set_rules('repContactNumber', 'Representative Contact Number', 'required|numeric|max_length[10]|min_length[10]');
		$this->form_validation->set_rules('repTarget', 'Representative Leaderboard Target', 'numeric');
		$this->form_validation->set_rules('repStatus', 'Representative Status', 'required|max_length[100]');
		$this->form_validation->set_rules('repArea', 'Representative Area', 'required|max_length[100]');
		$this->form_validation->set_rules('repCity', 'Representative City', 'required|max_length[100]');
		$this->form_validation->set_rules('repTeam', 'Representative Team', 'required|max_length[100]');

		if ($this->form_validation->run() == FALSE)  return FALSE;
		$insert['id'] = generate_uuid();
		$insert['firstName'] = $data['firstName'];
		$insert['lastName'] = $data['lastName'];
		$insert['repEmail'] = $data['repEmail'] ?? '';
		$insert['repTarget'] = $data['repTarget'] ?? '';
		$insert['repContactNumber'] = $data['repContactNumber'];
		$insert['repStatus'] = $data['repStatus'];
		$insert['repArea'] = $data['repArea'];
		$insert['repCity'] = $data['repCity'];
		$insert['repTeam'] = $data['repTeam'];
		$insert['createdBy'] = getCreatedBy();
		$insert['password'] = $this->bcrypt->hash_password(getPassword());
		$this->db->insert('rep', $insert);
		return $this->get_salesrepresentative($insert['id']);
	}

	public function update_salesrepresentative($salesrepresentativeId, $data)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('firstName', 'Representative  Name', 'required|max_length[50]');
		$this->form_validation->set_rules('repEmail', ' Representative Email', 'max_length[200]|valid_email');
		$this->form_validation->set_rules('repContactNumber', 'Representative Contact Number', 'required|numeric|max_length[10]|min_length[10]');
		$this->form_validation->set_rules('repTarget', 'Representative Leaderboard Targt', 'numeric');
		$this->form_validation->set_rules('repStatus', 'Representative Status', 'required|max_length[100]');
		$this->form_validation->set_rules('repCity', 'Representative City', 'required|max_length[100]');
		$this->form_validation->set_rules('repTeam', 'Representative Team', 'required|max_length[100]');
		if ($this->form_validation->run() == false)  return FALSE;

		$update['firstName'] = $data['firstName'];
		if (isset($data['lastName']))
			$update['lastName'] = $data['lastName'];
		$update['repContactNumber'] = $data['repContactNumber'];
		if (isset($data['repEmail']))
			$update['repEmail'] = $data['repEmail'];
		if (isset($data['target']))
			$update['target'] = $data['target'];
		$update['repStatus'] = $data['repStatus'];
		if (isset($data['repArea']))
			$update['repArea'] = $data['repArea'];
		if (isset($data['repTarget'])) $update['repTarget'] = $data['repTarget'];
		$update['repCity'] = $data['repCity'];
		$insert['repTeam'] = $data['repTeam'];
		$this->db->where('id', $salesrepresentativeId);
		$this->db->update('rep', $update);
		return $this->get_salesrepresentative($salesrepresentativeId);
	}

	public function delete_salesrepresentative($salesrepresentativeId)
	{

		$this->db->where('id', $salesrepresentativeId);
		$sales_rep = $this->db->get('rep')->row();
		if ($sales_rep) {
			if ($this->db->get_where('orders', array('orderSalesRepId' => $salesrepresentativeId))->row()) return FALSE;
			if ($this->db->get_where('schedule', array('salesRepId' => $salesrepresentativeId))->row()) return FALSE;

			$this->db->where('id', $salesrepresentativeId);
			$this->db->delete('rep');
			if ($this->db->affected_rows() > 0) return true;
			else return FALSE;
		} else return FALSE;
	}
}
