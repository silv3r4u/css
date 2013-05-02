<?php

class Klinis extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('m_klinis');
        $this->load->model('m_inv_autocomplete');
        $this->load->helper('html');
    }
    
    function index() {
        $this->load->view('registrasi');
    }
    /*Hipertensi*/
    function hipertensi() {
        $data['title'] = 'Hipertensi';
        $this->load->view('hipertensi', $data);
    }
    
    function save() {
        $data = $this->m_klinis->save('Hipertensi');
        die(json_encode($data));
    }
    
    function get_last_penyakit($id) {
        $data['last'] = $this->db->query("select * from riwayat_penyakit where penduduk_id = '$id' and penyakit = 'Hipertensi' order by id desc limit 1")->row();
        $this->load->view('last_penyakit', $data);
    }
    
    function get_last_pemeriksaan($id) {
        $data['lastp'] = $this->db->query("select * from riwayat_pemeriksaan where penduduk_id = '$id' and penyakit = 'Hipertensi' order by id desc limit 1")->row();
        $this->load->view('last_pemeriksaan', $data);
    }
    
    function cetak_excel($id) {
        $data['rows'] = $this->m_inv_autocomplete->load_data_penduduk_pasien(null, $id)->row();
        $data['lastp'] = $this->db->query("select * from riwayat_pemeriksaan where penduduk_id = '$id' and penyakit = 'Hipertensi'")->result();
        $data['last'] = $this->db->query("select * from riwayat_penyakit where penduduk_id = '$id' and penyakit = 'Hipertensi'")->result();
        $this->load->view('hasil_excel',$data);
    }
    /*End of Hipertensi*/
    
    /*Diabetes Melitus*/
    function diabetes() {
        $data['title'] = 'Diabetes Melitus';
        $this->load->view('klinis/diabetes', $data);
    }
    
    function save_diabetes() {
        $data = $this->m_klinis->save('Diabetes Melitus');
        die(json_encode($data));
    }
    
    function get_last_penyakit_diabetes($id) {
        $data['last'] = $this->db->query("select * from riwayat_penyakit where penduduk_id = '$id' and penyakit = 'Diabetes Melitus' order by id desc limit 1")->row();
        $this->load->view('last_penyakit', $data);
    }
    
    function get_last_pemeriksaan_diabetes($id) {
        $data['lastp'] = $this->db->query("select * from riwayat_pemeriksaan where penduduk_id = '$id' and penyakit = 'Diabetes Melitus' order by id desc limit 1")->row();
        $this->load->view('last_pemeriksaan', $data);
    }
    
    function cetak_excel_diabetes($id) {
        $data['rows'] = $this->m_inv_autocomplete->load_data_penduduk_pasien(null, $id)->row();
        $data['lastp'] = $this->db->query("select * from riwayat_pemeriksaan where penduduk_id = '$id' and penyakit = 'Diabetes Melitus'")->result();
        $data['last'] = $this->db->query("select * from riwayat_penyakit where penduduk_id = '$id' and penyakit = 'Diabetes Melitus'")->result();
        $this->load->view('hasil_excel',$data);
    }
    /*End of Diabetes Melitus*/
}
?>