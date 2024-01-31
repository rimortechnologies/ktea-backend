<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Auth_model extends CI_Model
{
	public function login_admin($data)
	{
		$this->load->library('bcrypt');
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('adminEmail', 'Admin Email', 'required');
		$this->form_validation->set_rules('password', "Password", 'required|xss_clean');

		if ($this->form_validation->run() == false) {
			return false; // Validation failed
		}

		$this->db->where('adminEmail', $data['adminEmail']);
		$query = $this->db->get('admin');
		$user = $query->row();
		if (!empty($user)) {
			if (!$this->bcrypt->check_password($data['password'], $user->password)) return false;
			return $user;
		} else
			return false;
	}
	public function login_distributor($data)
	{
		$this->load->library('bcrypt');
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('distributorEmail', 'Distributor Email', 'required|max_length[200]|valid_email');
		$this->form_validation->set_rules('password', "Password", 'required|xss_clean');

		if ($this->form_validation->run() == false) {
			return false; // Validation failed
		}

		$this->db->where('distributorEmail', $data['distributorEmail']);
		$query = $this->db->get('distributor');
		$user = $query->row();

		if (!empty($user)) {
			if (!$this->bcrypt->check_password($data['password'], $user->password)) return false;
			if ($user->distributorActive == 3 || $user->distributorActive == 2) return false;
			return $user;
		} else return false;
	}
	public function login_salesrepresentative($data)
	{
		$this->load->library('bcrypt');
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('repEmail', 'Retailer Email', 'required|max_length[200]|valid_email');
		$this->form_validation->set_rules('password', "Password", 'required|xss_clean');

		if ($this->form_validation->run() == false) {
			return false; // Validation failed
		}

		$this->db->where('repEmail', $data['repEmail']);
		$query = $this->db->get('rep');
		$user = $query->row();
		if ($user) {
			$teamQuery = $this->db->get_where('teams', array('id' => $user->repTeam));
			$teamInfo = $teamQuery->row();

			$user->isLead = 0;
			if ($teamInfo) if ($teamInfo->teamRepId == $user->id) $user->isLead = 1;
		}
		// echo $user;
		if (!empty($user)) {
			if (!$this->bcrypt->check_password($data['password'], $user->password)) throw new \Exception("Invalid Password");
			if ($user->repStatus == 3 || $user->repStatus == 2) throw new \Exception("User Blocked");
			return $user;
		} else throw new \Exception("User info does not exist");
	}
	// public function login_salesrepresentative($data)
	// {
	// 	$this->load->library('bcrypt');
	//     $this->load->library('form_validation');
	//     $this->form_validation->set_data($data);
	// 	$this->form_validation->set_rules('repEmail', 'Retailer Email' , 'required|max_length[200]|valid_email');
	// 	$this->form_validation->set_rules('password', "Password", 'required|xss_clean');

	// 	if ($this->form_validation->run() == false) {
	//         return false; // Validation failed
	//     }

	// 	$this->db->where('repEmail', $data['repEmail']);
	// 	$query = $this->db->get('rep');
	// 	$user= $query->row();

	// 	if (!empty($user)) {
	// 		if (!$this->bcrypt->check_password($data['password'], $user->password)) return false;
	// 		if ($user->salesrepresentativeStatus == 3 || $user->salesrepresentativeStatus == 2) return false;
	// 		return $user;
	// 	} else 
	// 		return false;
	// }

}
