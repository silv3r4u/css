<?php

class User extends CI_Controller {

    function __construct() {
        parent::__construct();
        //is_logged_in();
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->model('m_user');
        $this->load->helper('login');
        $this->load->helper('html');
        $user = $this->session->userdata('user');
        if ($user != '') {
            $data = $this->menu_user_load_data();
            $this->load->view('layout', $data);
        }
    }

    function menu_user_load_data() {
        $id_user = $this->session->userdata('id_user');
        $data['master_menu'] = $this->m_user->module_load_data($id_user)->result();
        return $data;
    }

    function index() {
        $data['title'] = 'Home Sistem Informasi Rumah Sakit';
        $user = $this->session->userdata('user');

        if ($user == '') {
            $this->load->view('login');
        }
        //$this->is_login();
    }

    function login() {
        $jml = $this->m_user->cek_login();
        if (isset($jml->username) and $jml->username != '') {
            $data = array('id_user' => $jml->id, 'user' => $jml->username, 'pass' => $jml->password, 'nama' => $jml->nama, 'id_unit' => $jml->unit_id, 'unit' => $jml->unit);
            $this->session->set_userdata($data);

            redirect(base_url());
        } else {
            redirect(base_url());
        }
    }

    public function is_login() {
        
    }

    function logout() {
        $this->session->sess_destroy();
        redirect(base_url());
    }

    

}

?>