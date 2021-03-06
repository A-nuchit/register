<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('Add_users');
		$this->load->model('Email');
		$this->load->library('form_validation');
		$this->load->library('session');
		$this->load->model('Get_infos');
		$this->load->model('Accept_users');
		$this->load->model('Insert_post');
		$this->load->model('Del');
	}
	public function index()
	{
		$data ['query'] = $this->Get_infos->get_post();
		$this->load->view('Show_post',$data);
	}
	public function register_form(){
		$this->load->view('welcome_message');
	}
	public function login_form(){
		$this->load->view('Login_form');
	}
	public function home(){
		$this->load->view('Admin_page');
		$this->load->view('Insertpost_form');
	}
	public function show_table()
	{
		$data ['query'] = $this->Get_infos->get_info();
		$this->load->view('Show_table',$data);
	}
	public function show_request(){
		$this->form_validation->set_rules('name', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		if ($this->form_validation->run() == FALSE) {
			if(isset($this->session->userdata['logged_in'])){
				if($this->session->userdata['logged_in']['name'] == "admin"){
					$data ['query'] = $this->Get_infos->get_request();
					$this->load->view('Show_requests',$data);
				}

				else{
					$this->load->view('Guest_page');
					$this->load->view('Insertpost_form');

				}
			}else{
				$this->load->view('Login_form');
			}
		}
		else{
			$this->load->view('Login_form');
		}

	}
	public function show_member(){
		$this->form_validation->set_rules('name', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		if ($this->form_validation->run() == FALSE) {
			if(isset($this->session->userdata['logged_in'])){
				if($this->session->userdata['logged_in']['name'] == "admin"){
					$data ['query'] = $this->Get_infos->get_member();
					$this->load->view('Show_member',$data);
				}

				else{
					$this->load->view('Guest_page');
					$this->load->view('Insertpost_form');

				}
			}else{
				$this->load->view('Login_form');
			}
		}
		else{
			$this->load->view('Login_form');
		}
	}
	public function show_listposts(){
		$this->form_validation->set_rules('name', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		if ($this->form_validation->run() == FALSE) {
			if(isset($this->session->userdata['logged_in'])){
				if($this->session->userdata['logged_in']['name'] == "admin"){
					$data ['query'] = $this->Get_infos->get_post();
					$this->load->view('Show_listpost',$data);
				}

				else{
					$this->load->view('Guest_page');
					$this->load->view('Insertpost_form');

				}
			}else{
				$this->load->view('Login_form');
			}
		}
		else{
			$this->load->view('Login_form');
		}
	}
	public function accept_users(){
		$id = $this->input->get('id');
		$this->Accept_users->accept_user($id);
		$data ['query'] = $this->Get_infos->get_request();
		$this->load->view('Show_requests',$data);
	}
	public function del_users(){
		$id = $this->input->get('id');
		$this->Del->del_user($id);
		$data ['query'] = $this->Get_infos->get_member();
		$this->load->view('Show_member',$data);
	}
	public function del_post(){
		$id = $this->input->get('id');
		$this->Del->del_post($id);
		$data ['query'] = $this->Get_infos->get_post();
		$this->load->view('Show_listpost',$data);
	}
	

	public function login(){
		$this->form_validation->set_rules('name', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		if ($this->form_validation->run() == FALSE) {
			if(isset($this->session->userdata['logged_in'])){
				if($this->session->userdata['logged_in']['name'] == "admin"){
					$this->load->view('Admin_page');
					$this->load->view('Insertpost_form');
				}

				else{
					$this->load->view('Guest_page');
					$this->load->view('Insertpost_form');

				}
			}else{
				$this->load->view('Login_form');
			}
		} else {
			$data = array(
				'name' => $this->input->post('name'),
				'password' => $this->input->post('password')
				);
			 if($this->Add_users->login_user($data)){
			 	$result = $this->Add_users->read_user_information($data['name']);
			 	$session_data = array(
										'name' => $result[0]->name,
										'email' => $result[0]->email,
										);
				$this->session->set_userdata('logged_in', $session_data);
				if($this->session->userdata['logged_in']['name'] == "admin"){
					$this->load->view('Admin_page');
					$this->load->view('Insertpost_form');
				}

				else{
					$this->load->view('Guest_page');
					$this->load->view('Insertpost_form');
				}
			 }
			 else{
			 	$data['message_display'] = 'Name or Password dont ';
				$this->load->view('Login_form',$data);
			 }
		}
	}
	public function add_post(){

		$config['upload_path'] = 'assets/images/';
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
		$config['remove_spaces'] = TRUE;
		$config['file_name'] = $_FILES['featured']['name'];

		$this->load->library("upload",$config);
		if ($this->upload->do_upload('featured')) {
			$uploadData = $this->upload->data();
			$pic = $uploadData['file_name'];
		} else {
			$errors = $this->upload->display_errors();
			echo $errors;
 		}

		$title = $this->input->post('title');
		$content = $this->input->post('content');
		$type = $this->input->post('type');
		$time = date("Y-m-d h:i:sa");

		$data = array('title' => $title,'content' => $content,'type'=> $type, 'time' => $time, 'pic'=>$pic ,'name'=> $this->session->userdata['logged_in']['name'] );
		if($this->Insert_post->add_post($data)){
			if($this->session->userdata['logged_in']['name'] == "admin"){
				$this->load->view('Admin_page');
			}
			else{
				$this->load->view('Guest_page');
				$this->load->view('Insertpost_form');
			}
		}
	}
	public function add_user()
	{
		$name = $this->input->post('name');
		$config['upload_path'] = 'assets/images/';
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
		$config['remove_spaces'] = TRUE;
		$config['file_name'] = $name;
		$this->load->library("upload",$config);
		if ($this->upload->do_upload('featured')) {
			$uploadData = $this->upload->data();
			$pic = $uploadData['file_name'];
		} else {
			$errors = $this->upload->display_errors();
			echo $errors;
 		}
		$lastname = $this->input->post('lastname');
		$email = $this->input->post('email');
		$tel = $this->input->post('tel');
		$pass = $this->input->post('password');
		$c_pass = $this->input->post('confirm-password');
		$this->load->model('Check_mails');
		$check_mail = $this->Check_mails->check_email($email);
		$check_name = $this->Check_mails->check_name($name);
		if( $check_mail == TRUE && $check_name == TRUE )
		{
			echo $check_mail;
			if($pass != $c_pass){
				$data['message_display'] = 'Password dont math!';
				$this->load->view('welcome_message',$data);
			}
			else if( strlen($pass) < 6){
				$data['message_display'] = 'Pless more 6 char';
				$this->load->view('welcome_message',$data);
			}
			else{
			$pass = password_hash($pass, PASSWORD_DEFAULT);
			$data = array('pre_name' => $name,'pre_lastname' => $lastname,'pre_password'=> $pass, 'pre_email' => $email, 'pre_tel'=> $tel, 'pre_pic'=>$pic );
				if($this->Add_users->add($data) && $this->Email->sent_email($name,$lastname,$email,$tel))
				{
					$this->load->view('Login_form');
				}
				else
				{

					$this->load->view('welcome_message');
				}
			}
		}else
		{
			$this->load->view('welcome_message');
			if($check_name != TRUE){
				$this->load->view('Alert_name');
			}
			if($check_mail != TRUE){
				$this->load->view('Alertx');
			}
		}
	}
	public function logout() {
		$sess_array = array(
			'name' => '',
			'email' => ''
				);
		$this->session->unset_userdata('logged_in', $sess_array);
		$this->load->view('Login_form');
	}
}
