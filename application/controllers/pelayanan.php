<?php

class Pelayanan extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('configuration');
        $this->load->model('m_inventory');
        $this->load->model('m_referensi');
        $this->load->model('m_resep');
        $this->load->helper('html');
        date_default_timezone_set('Asia/Jakarta');
    }
    
    function resep($id_resep = null) {
        $data['title'] = 'Resep';
        $data['biaya_apoteker'] =  $this->m_referensi->biaya_apoteker_load_data()->result();
        $id_dokter = $this->input->post('nama_dokter');
        $id_pasien = $this->input->post('nama_pasien');
        if (isset($id_dokter) and $id_dokter != '' and isset($id_pasien) and $id_pasien != '') {
            $data = $this->m_inventory->resep_save();
            die(json_encode($data));
        }
        if ($id_resep != NULL) {
            $data['id_resep'] = $id_resep;
            $data['list_data'] = $this->m_resep->data_resep_muat_data($id_resep)->result();
        }
        $this->load->view('resep', $data);
    }
    
    function penjualan_jasa() {
        $data['title'] = 'Penjualan Jasa';
        $no_rm = $this->input->post('id_pembeli');
        if (isset($no_rm) and $no_rm != '') {
            $data = $this->m_inventory->penjualan_jasa_save($no_rm);
            die(json_encode($data));
        }
        $this->load->view('penjualan-jasa', $data);
    }
    
    function penjualan_nr() {
        $data['title'] = 'Penjualan Non Resep (Bebas)';
        $bank = $this->configuration->instansi_relasi_load_data(null, 'Bank');
        $ddmenu = array();
        $ddmenu[''] = 'Pilih Pembayaran ..';
        foreach ($bank->result_array() as $rows) {
            $ddmenu[$rows['id']] = $rows['nama'];
        }
        $bayar = $this->input->post('bayar');
        if (isset($bayar) and $bayar != '') {
            $data = $this->m_inventory->penjualan_non_resep_save();
            die(json_encode($data));
        }
        $data['list_bank'] = $ddmenu;
        $this->load->view('penjualan-nr', $data);
    }
    
    function penjualan_cetak_nota($id_penjualan, $penjualan = null) {
        $data['title'] = 'Kitir';
        $data['jenis'] = '';
        if ($penjualan != null) {
            $data['title'] = 'Nota Penjualan';
            $data['jenis'] = $penjualan;
        }
        
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['penjualan'] = $this->m_inventory->penjualan_load_data($id_penjualan)->result();
        $this->load->view('inventory/print/nota-penjualan-bebas', $data);
    }
    
    function kitir_cetak_nota($id_resep) {
        $data['title'] = '';
        $data['jenis'] = '';
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        
        $data['penjualan'] = $this->m_resep->nota_load_data($id_resep)->result();
        $this->load->view('inventory/print/kitir', $data);
    }
    
    function kitir($id_resep) {
        $data['title'] = '';
        $data['jenis'] = '';
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        
        $data['penjualan'] = $this->m_resep->cetak_kitir_load_data($id_resep)->result();
        $this->load->view('inventory/print/kitir_cetak', $data);
    }
    
    function cetak_etiket() {
        $data['title'] = 'Etiket';
        $data['list_data'] = $this->m_resep->cetak_etiket($_GET['no_resep'], $_GET['no_r'])->result();
        $this->load->view('inventory/print/etiket',$data);
    }
    
    function cetak_pmr() {
        $data['title'] = 'Patient Medical Record';
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['list_data'] = $this->m_resep->get_data_pmr_penduduk_detail($_GET['id_pasien'])->result();
        $data['rows'] = $this->m_resep->get_data_pmr_penduduk($_GET['id_pasien'])->row();
        $this->load->view('inventory/print/pmr',$data);
    }
    
    function get_jenis_rawat_by_pasien($id_pasien, $no_rm) {
        $data = $this->m_resep->get_jenis_rawat_by_pasien($id_pasien, $no_rm);
        die(json_encode($data));
    }
}
?>
