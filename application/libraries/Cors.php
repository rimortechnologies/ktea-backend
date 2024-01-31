<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cors
{
	public function setHeaders()
	{
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST');
		header("Access-Control-Allow-Headers: X-Requested-With");
	}
}
