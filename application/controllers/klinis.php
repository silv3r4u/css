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
    
    function hipertensi() {
        $data['title'] = 'Hipertensi';
        $this->load->view('hipertensi', $data);
    }
    
    function save() {
        $data = $this->m_klinis->save();
        die(json_encode($data));
    }
    
    function get_last_penyakit($id) {
        $data['last'] = $this->db->query("select * from riwayat_penyakit where penduduk_id = '$id' order by tanggal desc limit 1")->row();
        $this->load->view('last_penyakit', $data);
    }
    
    function get_last_pemeriksaan($id) {
        $data['lastp'] = $this->db->query("select * from riwayat_pemeriksaan where penduduk_id = '$id' order by tanggal desc limit 1")->row();
        $this->load->view('last_pemeriksaan', $data);
    }
    
    function cetak_excel($id) {
        $data['rows'] = $this->m_inv_autocomplete->load_data_penduduk_pasien(null, $id)->row();
        $data['lastp'] = $this->db->query("select * from riwayat_pemeriksaan where penduduk_id = '$id'")->result();
        $data['last'] = $this->db->query("select * from riwayat_penyakit where penduduk_id = '$id'")->result();
        $this->load->view('hasil_excel',$data);
    }
}
?>