<?php

class Rawatinap extends CI_Controller {

    function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->model('m_rawatinap');
        $this->load->library('session');
        $this->load->helper('login');
        $this->load->helper('functions');
        $this->load->library('form_validation');
        $this->load->helper('url');
    }

    function index() {
        $this->load->view('layout');
    }

    function billing_rawat_inap() {
        $this->load->view('layout');
        $data['title'] = 'Billing Rawat Inap';
        $this->load->view('rawat_inap/billing_rawat_inap', $data);
    }

    function get_data_pasien() {
        $q = $_GET['q'];
        $data = $this->m_rawatinap->data_pasien_muat_data($q);
        return die(json_encode($data));
    }

    function asuransi_kepesertaan_get_data($id_pasien) {
        $data['list_data'] = $this->m_rawatinap->asuransi_kepesertaan_get_data($id_pasien)->result();
        $this->load->view('rawat_inap/asuransi_list', $data);
    }

    function get_data_rawatinap($no_daftar) {
        $data['bed'] = $this->m_rawatinap->get_data_rawatinap($no_daftar);
        $data['no_daftar'] = $no_daftar;
        $this->load->view('rawat_inap/unit_list', $data);
    }

    function get_data_unit() {
        $q = $_GET['q'];
        $data = $this->m_rawatinap->data_unit_muat_data($q);
        return die(json_encode($data));
    }

    function get_data_bed($unit, $kelas) {
        $q = array(
            'unit' => $unit,
            'kelas' => $kelas
        );

        $data = $this->m_rawatinap->data_bed_muat_data($q);
        return die(json_encode($data));
    }

    function save_rawatinap() {
        $no_daftar = $this->input->post('no_daftar');
        // data lama
        $id = $this->input->post('id');
        $out = $this->input->post('out');
        $masuk = $this->input->post('masuk');
        $tarif = $this->input->post('tarif');
        $t_id = $this->input->post('t_id');
        if ($id != null) {
            $update = array(
                'id' => $id, //array
                'out_time' => $out, //array
                'tarif' => $tarif, //array
                'in_time' => $masuk,
                't_id' => $t_id//array
            );
            $this->m_rawatinap->update_bed_data($update);
        }

        // data baru

        $no = $this->input->post('no');
        $in = $this->input->post('in_time');
        if ($no != null) {
            $data = array(
                'no_daftar' => $no_daftar,
                'no_bed' => $no, //array
                'in_time' => $in //array
            );
            $this->m_rawatinap->save_bed_data($data);
        }
        //return die(json_encode($tarif));
        $this->get_data_rawatinap($no_daftar);
    }

    function delete_data_bed($id, $no_daftar) {
        $this->m_rawatinap->delete_bed_data($id);
        $this->get_data_rawatinap($no_daftar);
    }

    function delete_all_bed($no_daftar) {
        $this->m_rawatinap->delete_bed_all($no_daftar);
        $this->get_data_rawatinap($no_daftar);
    }

}

?>