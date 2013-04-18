<?php

class Billing extends CI_Controller {

    function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->model('m_billing');
        $this->load->library('session');
        $this->load->helper('login');
        $this->load->helper('html');
        $this->load->helper('functions');
        $this->load->library('form_validation');
        $this->load->helper('url');
        date_default_timezone_set('Asia/Jakarta');
    }

    function index($no_daftar = null) {
        $data['title'] = 'Rekap Billing Pasien';
        $this->load->view('layout');
        if ($no_daftar != null) {
            $data['pasien'] = $this->m_billing->get_data_pasien($no_daftar);
            if ($data['pasien'] != null) {
                $data['asuransi'] = $this->m_billing->asuransi_kepesertaan_get_data($data['pasien']->id)->result();
            }

            $this->load->view('billing/billing', $data);
        } else {
            $this->load->view('billing/billing', $data);
        }
    }

    function pembayaran($var1 = null, $var2 = null) {
        $this->load->view('layout');

        $data = null;
        if ($var1 != null and $var1 == 'saveok') {
            if ($var2 != null) {
                $data['attribute'] = $this->m_billing->data_kunjungan_muat_data($var2);
                $data['list_data'] = $this->m_billing->load_data_tagihan($var2)->result();
            }
        }
        $data['title'] = 'Pembayaran Billing';
        $this->load->view('billing/pembayaran', $data);
    }

    function get_data_pasien() {
        $q = $_GET['q'];
        $data = $this->m_billing->data_pasien_muat_data($q);
        return die(json_encode($data));
    }

    function get_data_kunjungan() {
        $q = $_GET['q'];
        $data = $this->m_billing->data_kunjungan_muat_data($q);
        return die(json_encode($data));
    }

    function asuransi_kepesertaan_get_data($id_pasien) {
        $data['list_data'] = $this->m_billing->asuransi_kepesertaan_get_data($id_pasien)->result();
        $this->load->view('billing/asuransi_list', $data);
    }

    function load_data($id_pasien) {
        $data['list_data'] = $this->m_billing->penjualan_barang_load_data($id_pasien, 'true')->result();
        $data['jasa_list_data'] = $this->m_billing->penjualan_jasa_detail_load_data($id_pasien)->result();
        $data['rawat_inap'] = $this->m_billing->rawat_inap_detail_load_data($id_pasien)->result();
        $this->load->view('billing/billing_list_data', $data);
    }

    function load_data_pembayaran($id_kunjungan) {
        //$data['list_data'] = $this->m_billing->penjualan_barang_load_data($id_kunjungan, 'true')->result();
        //$data['jasa_list_data'] = $this->m_billing->penjualan_jasa_detail_load_data($id_kunjungan)->result();
        $data['list_data'] = $this->m_billing->load_data_tagihan($id_kunjungan)->result();
        $data['total_data'] = $this->m_billing->load_data_tagihan($id_kunjungan)->num_rows();
        $data['rows'] = $this->m_billing->load_data_tagihan($id_kunjungan)->row();
        //$rows = $this->m_billing->data_kunjungan_muat_data_total($id_kunjungan);
        //$data['totallica'] = $rows->total_jasa + (($rows->total_barang == NULL)?0:$rows->total_barang);

        $this->load->view('billing/list_pembayaran', $data);
    }

    function total_tagihan($id_kunjungan_billing) {
        $tb = $this->m_billing->data_kunjungan_muat_data_total_barang($id_kunjungan_billing);
        $tj = $this->m_billing->data_kunjungan_muat_data_total_jasa($id_kunjungan_billing);
        $ti = $this->m_billing->data_rawat_inap_tagihan($id_kunjungan_billing)->row();
        $total_pembayaran = $this->m_billing->total_pembayaran($id_kunjungan_billing);
        $total = ($tb->total_barang + $tj->total_jasa + $ti->total_rawat_inap);
        die(json_encode(array('fuck' => $total, 'you' => $total_pembayaran->total_pembayaran)));
    }

    function load_data_tagihan() {
        $data['list_data'] = $this->m_billing->load_data_tagihan($id_pasien)->result();
        $this->load->view('billing/tagihan', $data);
    }

    function pembayaran_save() {
        $data = $this->m_billing->pembayaran_save();
        die(json_encode($data));
    }

    function cetak($id_pembayaran, $angsuran_ke, $no_daftar) {
        $data['title'] = 'Pembayaran Ke-' . $angsuran_ke;
        $data['apt'] = $this->m_billing->office_muat_data()->row();
        $data['attribute'] = $this->m_billing->data_kunjungan_muat_data($no_daftar);
        $data['list_jasa'] = $this->m_billing->get_tagihan_jasa($no_daftar)->result();
        $data['list_barang'] = $this->m_billing->get_tagihan_barang($no_daftar)->row();
        $data['pembayaran'] = $this->m_billing->load_data_pembayaran($id_pembayaran)->row();
        $data['rawat_inap'] = $this->m_billing->load_data_rawat_inap_tagihan($no_daftar)->result();
        $data['bayar_ke'] = $angsuran_ke;
        $this->load->view('billing/cetak_billing', $data);
    }

    function laporan() {
        $data['title'] = 'Rekap Pembayaran';
        $this->load->view('layout');
        $this->load->view('billing/laporan', $data);
    }

    function laporan_load_data() {
        $data['list_data'] = $this->m_billing->laporan_load_data($_GET['awal'], $_GET['akhir'], $_GET['pembayaran'])->result();
        $this->load->view('billing/laporan-table', $data);
    }
    
    function pp_uang() {
        $data['title'] = 'Transaksi Keuangan';
        $this->load->view('pp-uang', $data);
    }
    
    function pp_uang_save() {
        $data = $this->m_billing->pp_uang_save();
        die(json_encode($data));
    }
    
    function pp_uang_delete ($id) {
        $data = $this->m_billing->pp_uang_delete($id);
        die(json_encode($data));
    }

}

?>