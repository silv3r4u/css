<?php

class Inv_autocomplete extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('m_inv_autocomplete');
        $this->load->model('m_billing');
        $this->load->model('configuration');
    }

    public function load_data_instansi_relasi($jenis) {
        $q = $_GET['q'];
        $data = $this->m_inv_autocomplete->load_data_instansi_relasi($jenis, $q)->result();
        die(json_encode($data));
    }

    function load_data_user_system() {
        $q = $_GET['q'];
        $data = $this->m_inv_autocomplete->load_data_user_system($q)->result();
        die(json_encode($data));
    }
    
    function load_data_penduduk_hipertensi() {
        $q = $_GET['q'];
        $data = $this->m_inv_autocomplete->load_data_penduduk_pasien($q)->result();
        die(json_encode($data));
    }

    function load_data_produk_asuransi() {
        $q = $_GET['q'];
        $data = $this->m_inv_autocomplete->load_data_produk_asuransi($q)->result();
        die(json_encode($data));
    }

    function load_data_penduduk($jenis = null) {
        $q = $_GET['q'];
        $data = $this->m_inv_autocomplete->load_data_penduduk($jenis, $q)->result();
        die(json_encode($data));
    }
    
    function load_data_penduduk_profesi() {
        $q = $_GET['q'];
        $data = $this->m_inv_autocomplete->load_data_penduduk_profesi('Nakes', $q)->result();
        die(json_encode($data));
    }
    
    function load_data_profesi_by_nakes() {
        $q = $_GET['q'];
        $data = $this->m_inv_autocomplete->load_data_penduduk_profesi(null, $q, 'Nakes')->result();
        die(json_encode($data));
    }

    function load_penduduk() {
        $q = $_GET['q'];
        $data = $this->m_inv_autocomplete->load_penduduk($q)->result();
        die(json_encode($data));
    }
    
    function load_data_penjualan_jasa($id_penduduk) {
        $data['list_data'] = $this->m_billing->penjualan_jasa_detail_load_data($id_penduduk)->result();
        $this->load->view('penjualan-jasa_table', $data);
    }
    
    function load_data_packing_barang_per_ed() {
        $q = $_GET['q'];
        $extra_param = isset($_GET['id_barang']) ? $_GET['id_barang'] : NULL;
        $data = $this->m_inv_autocomplete->load_data_packing_barang_per_ed($q, $extra_param)->result();
        die(json_encode($data));
    }
    
    function load_data_packing_barang_where_stok_ready() {
        $q = $_GET['q'];
        $extra_param = isset($_GET['id_barang']) ? $_GET['id_barang'] : NULL;
        $data = $this->m_inv_autocomplete->load_data_packing_barang_where_stok_ready($q, $extra_param)->result();
        die(json_encode($data));
    }

    function load_data_penduduk_pasien($id_penduduk = null) {
        $id = null;
        $q = null;
        if ($id_penduduk != null) {
            $id = $id_penduduk;
            $data = $this->m_inv_autocomplete->load_data_penduduk_pasien(null, $id)->row();
        }
        if ($id_penduduk == null) {
            $q = $_GET['q'];
            $data = $this->m_inv_autocomplete->load_data_penduduk_pasien($q)->result();
        }
        die(json_encode($data));
    }

    function load_data_penduduk_dokter() {
        $q = $_GET['q'];
        $data = $this->m_inv_autocomplete->load_data_penduduk_dokter($q)->result();
        die(json_encode($data));
    }

    function load_data_penduduk_asuransi($id_penduduk = null) {
        $id = null;
        if ($id_penduduk != null) {
            $id = $id_penduduk;
        }
        $data = $this->m_inv_autocomplete->load_data_penduduk_asuransi($id);
        die(json_encode($data));
    }

    function get_layanan_jasa() {
        $q = $_GET['q'];
        $data = $this->m_inv_autocomplete->get_layanan_jasa($q)->result();
        die(json_encode($data));
    }

    function load_data_packing_barang() {
        $q = $_GET['q'];
        $extra_param = isset($_GET['id_barang']) ? $_GET['id_barang'] : NULL;
        $data = $this->m_inv_autocomplete->load_data_packing_barang($q, $extra_param)->result();
        die(json_encode($data));
    }
    
    function load_data_packing_barang_obat() {
        $q = $_GET['q'];
        $data = $this->m_inv_autocomplete->load_data_packing_barang_obat($q)->result();
        die(json_encode($data));
    }

    function load_data_rop() {
        $id = $_GET['id'];
        $data = $this->m_inv_autocomplete->load_data_rop($id);
        die(json_encode($data));
    }

    function get_nomor_pemesanan() {
        $q = $_GET['q'];
        $data = $this->m_inv_autocomplete->get_nomor_pemesanan($q)->result();
        die(json_encode($data));
    }
    
    function hitung_detail_pemesanan($id_pb, $biaya) {
        $data = $this->m_inv_autocomplete->hitung_detail_pemesanan($id_pb, $biaya);
        die(json_encode($data));
    }
    
    function get_harga_jual() {
        $data = $this->m_inv_autocomplete->get_harga_jual($_GET['id'])->row();
        die(json_encode($data));
    }
    
    function get_harga_jual_barang_kemasan($id_barang, $id_kemasan) {
        $data = $this->m_inv_autocomplete->get_harga_jual_barang_kemasan($id_barang, $id_kemasan)->row();
        die(json_encode($data));
    }
    
    function load_data_penduduk_apoteker() {
        $data = $this->configuration->get_apoteker($_GET['q'])->result();
        die(json_encode($data));
    }

    function get_nomor_pembelian() {
        $q = $_GET['q'];
        $row = $this->m_inv_autocomplete->get_nomor_pembelian($q)->row();
        $data = null;
        if ($row->id != NULL) {
            $data = $this->m_inv_autocomplete->get_nomor_pembelian($q)->result();
        }
        die(json_encode($data));
    }
    
    function cek_inkaso($id_pembelian) {
        $data = $this->m_inv_autocomplete->cek_inkaso($id_pembelian)->row();
        die(json_encode($data));
    }

    function get_last_transaction() {
        $id_pb = $_GET['id_pb'];
        $ed = datetopg($_GET['ed']);
        $data = $this->m_inv_autocomplete->get_last_transaction($id_pb, $ed)->row();
        die(json_encode($data));
    }

    function get_nomor_distribusi() {
        $q = $_GET['q'];
        $data = $this->m_inv_autocomplete->get_nomor_distribusi($q)->result();
        die(json_encode($data));
    }

    function get_diskon_instansi_relasi($id_instansi_relasi = null) {
        $id = null;
        if ($id_instansi_relasi != null) {
            $id = $id_instansi_relasi;
        }
        if ($id_instansi_relasi == NULL) {
            $data = array('diskon_penjualan' => '0');
        } else {
            $data = $this->m_inv_autocomplete->get_diskon_instansi_relasi($id)->row();
        }
        die(json_encode($data));
    }

    function get_harga_barang_penjualan($id_packing) {
        $data = $this->m_inv_autocomplete->get_harga_barang_penjualan($id_packing)->row();
        die(json_encode($data));
    }

    function get_penjualan_field($barcode) {
        $data = $this->m_inv_autocomplete->get_penjualan_field($barcode)->row();
        die(json_encode($data));
    }

    function load_data_pabrik() {
        $q = $_GET['q'];
        $data = $this->m_inv_autocomplete->load_data_pabrik($q)->result();
        die(json_encode($data));
    }

    function load_data_no_resep() {
        $q = $_GET['q'];
        $data = $this->m_inv_autocomplete->load_data_no_resep($q)->result();
        die(json_encode($data));
    
    }
    
    function load_jasa_apoteker($id_resep) {
        $data = $this->m_inv_autocomplete->load_jasa_apoteker($id_resep)->row();
        die(json_encode($data));
    }

    function load_penjualan_by_no_resep($no_resep) {
        $data['list_data'] = $this->m_inv_autocomplete->load_penjualan_by_no_resep($no_resep)->result();
        $this->load->view('inventory/penjualan-table', $data);
    }

    function reretur_pembelian_load_id() {
        $q = $_GET['q'];
        $data = $this->m_inv_autocomplete->reretur_pembelian_load_id($q)->result();
        die(json_encode($data));
    }

    function reretur_pembelian_load_data() {
        $no_retur_pembelian = $_GET['id'];
        $data['list_data'] = $this->m_inv_autocomplete->reretur_pembelian_load_data($no_retur_pembelian)->result();
        $this->load->view('inventory/reretur-pembelian-table', $data);
    }

    function reretur_penjualan_get_nomor() {
        $id = $_GET['q'];
        $data = $this->m_inv_autocomplete->reretur_penjualan_get_nomor($id)->result();
        die(json_encode($data));
    }

    function reretur_penjualan_table() {
        $id = $_GET['id'];
        $data['list_data'] = $this->m_inv_autocomplete->reretur_penjualan_table($id)->result();
        $this->load->view('inventory/reretur-penjualan-table', $data);
    }

    function get_layanan() {
        $q = $_GET['q'];
        $data = $this->m_inv_autocomplete->get_layanan($q)->result();
        die(json_encode($data));
    }

    function get_tarif_kategori() {
        $q = $_GET['q'];
        $data = $this->m_inv_autocomplete->get_tarif_kategori($q)->result();
        die(json_encode($data));
    }

    function load_data_barang() {
        $q = $_GET['q'];
        $data = $this->m_inv_autocomplete->get_barang($q)->result();
        die(json_encode($data));
    }
    
    function load_data_layanan_profesi() {
        $q = $_GET['q'];
        $data = $this->m_inv_autocomplete->load_data_layanan_profesi($q)->result();
        die(json_encode($data));
    }
    
    function get_no_retur_distribusi() {
        $q =  $_GET['q'];
        $data = $this->m_inv_autocomplete->get_no_retur_distribusi($q)->result();
        die(json_encode($data));
    }
    
    function load_data_retur_unit($id) {
        $data['list_data'] = $this->m_inv_autocomplete->load_data_retur_unit($id)->result();
        $this->load->view('inventory/penerimaan-retur-distribusi-table', $data);
    }
    
    function get_kemasan_barang($id_barang, $i) {
        $array = $this->db->query("select s.*, bp.isi from barang_packing bp join satuan s on (bp.terbesar_satuan_id = s.id) where bp.barang_id = '$id_barang' order by bp.isi asc")->result();
        echo "<select style='border: 1px solid #ccc; width:100%;' name='kemasan[]' onchange='get_harga_jual(".$i.")' id='kemasan".$i."'>";
            foreach ($array as $rows) {
                echo "<option value='".$rows->id."-".$rows->isi."'>".$rows->nama." @ ".$rows->isi."</option>";
            }
        echo "</select>";
    }
    
    function get_kemasan_barang_pembelian($id_barang, $i) {
        $array = $this->db->query("select s.*, bp.isi from barang_packing bp join satuan s on (bp.terbesar_satuan_id = s.id) where bp.barang_id = '$id_barang' order by bp.isi asc")->result();
        echo "<select style='border: 1px solid #ccc; width:100%;' name='kemasan[]' onchange='get_isi_kemasan(".$i.")' id='kemasan".$i."'><option value=''>Pilih... </option>";
            foreach ($array as $rows) {
                echo "<option value='".$rows->isi."-".$rows->id."'>".$rows->nama." @ ".$rows->isi."</option>";
            }
        echo "</select>";
    }
}

?>