<?php

class Inventory extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->helper('login');
        $this->load->helper('functions');
        $this->load->model('m_inventory');
        $this->load->model('m_inv_autocomplete');
        $this->load->model('m_referensi');
        $this->load->model('m_resep');
        $this->load->model('configuration');
        $this->load->helper('html');
        date_default_timezone_set('Asia/Jakarta');
        is_logged_in();
    }
    
    function pemesanan() {
        $data['title'] = 'Pemesanan';
        $this->load->view('inventory/pemesanan', $data);
        //echo $this->waktu;
    }
    
    function save_pemesanan() {
        $id = $this->input->post('id');
        if (isset($id)) {
            $data = $this->m_inventory->save_pemesanan();
            die(json_encode($data));
        } else {
            die(json_encode(array('status' => false)));
        }
    }
    
    function pembelian($id = null) {
        $data['title'] = 'Pembelian';
        if ($id != null) {
            $data['list_data'] = $this->m_inventory->pemesanan_muat_data($id)->result();
        }
        $this->load->view('inventory/pembelian', $data);
    }
    
    function pembelian_save() {
        $id = $this->input->post('id');
        if (isset($id)) {
            $data = $this->m_inventory->pembelian_save();
            die(json_encode($data));
        } else {
            die(json_encode(array('status' => false)));
        }
    }
    
    function pemesanan_delete($id) {
        if (isset($id)) {
            $data = $this->m_inventory->pemesanan_delete($id);
            die(json_encode($data));
        }
    }
    
    function get_data_pemesanan($id) {
        $data['list_data'] = $this->m_inventory->pemesanan_muat_data($id)->result();
        $this->load->view('inventory/pembelian-table', $data);
    }
    
    function pemesanan_cetak() {
        $id = $this->input->get('id');
        $id_user = $this->session->userdata('id_user');
        $data['pemesanan'] = $this->m_inventory->pemesanan_muat_data($id, $id_user)->result();
        $data['detail'] = $this->configuration->get_manager_farmasi();
        
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['manager'] = $this->configuration->get_manager_farmasi();
        $this->load->view('inventory/print/pemesanan', $data);
    }
    
    function distribusi($id = NULL) {
        $data['title'] = 'Distribusi';
        $unit = $this->configuration->unit_load_data(null, 'inventori');
        $ddmenu = array();
        $ddmenu[''] = 'Pilih Unit ..';
        foreach ($unit->result_array() as $rows) {
            $ddmenu[$rows['id']] = $rows['nama'];
        }
        $data['list_unit'] = $ddmenu;
        $tanggal = $this->input->post('tanggal');
        if (isset($tanggal) and $tanggal != '') {
            $data = $this->m_inventory->distribusi_save();
            die(json_encode($data));
        }
        
        $this->load->view('inventory/distribusi', $data);
    }
    
    function distribusi_cetak($id) {
        $data['title'] = 'SURAT DISTRIBUSI';
        $data['list_data'] = $this->m_inv_autocomplete->distribusi_load_data($id)->result();
        $this->load->view('inventory/print/distribusi', $data);
    }
    
    function distribusiGetSisa() {
        $id_pb = $_GET['id_pb'];
        $ed = $_GET['ed'];
        $data = $this->m_inventory->distribusiGetSisa($id_pb, $ed)->row();
        die(json_encode($data));
    }
    
    function inkaso() {
        $data['title'] = 'Pembayaran Pembelian (Inkaso)';
        $id_pembelian = $this->input->post('nopembelian');
        if (isset($id_pembelian) and $id_pembelian != '') {
            $data['result'] = $this->m_inventory->inkaso_save();
            $data['detail'] = $this->m_inv_autocomplete->cek_inkaso($id_pembelian)->row();
            die(json_encode($data));
        }
        $this->load->view('inventory/inkaso', $data);
    }
    
    function inkaso_detail($id_inkaso) {
        $data['title'] = 'Inkaso (Pembayaran Pembelian)';
        $data['rows'] = $this->m_inv_autocomplete->get_detail_inkaso($id_inkaso)->row();
        $this->load->view('inventory/inkaso_detail', $data);
    }
    
    function pemakaian() {
        $data['title'] = 'Pemakaian Unit';
        $id_barang = $this->input->post('id_pb');
        if (isset($id_barang) and $id_barang != '') {
            $data = $this->m_inventory->pemakaian_save();
            die(json_encode($data));
        }
        $this->load->view('inventory/pemakaian', $data);
    }
     
    function pemakaian_delete($id) {
        $data = $this->m_inventory->pemakaian_delete($id);
        die(json_encode($data));
    }
    
    function pembelian_delete($id) {
        $data = $this->m_inventory->pembelian_delete($id);
        die(json_encode($data));
    }
    
    function retur_penjualan_delete($id) {
        $data = $this->m_inventory->retur_penjualan_delete($id);
        die(json_encode($data));
    }
    
    function bayar_ret_pemb() {
        $data['title'] = 'Pembayaran Retur Pembelian';
        $this->load->view('inventory/bayar-ret-pemb', $data);
    }
    
    function pemusnahan() {
        $data['title'] = 'Pemusnahan';
        $id_sapt = $this->input->post('id_sapt');
        if (isset($id_sapt) and $id_sapt != '') {
            $data = $this->m_inventory->pemusnahan_save();
            die(json_encode($data));
        }
        $this->load->view('inventory/pemusnahan', $data);
    }
    
    function pemusnahan_delete($id) {
        $data = $this->m_inventory->pemusnahan_delete($id);
        die(json_encode($data));
    }
    
    function penerimaan_distribusi () {
        $data['title'] = 'Penerimaan Distribusi';
        $id = $this->input->post('nodistribusi');
        if (isset($id) and $id != '') {
            $data = $this->m_inventory->penerimaan_distribusi_save();
            die(json_encode($data));
        }
        $this->load->view('inventory/penerimaan-distribusi', $data);
    }
    
    function penjualan($id = null) {
        $data['title'] = 'Penjualan (Resep)';
        $noresep = $this->input->post('id_resep');
        if (isset($noresep) and $noresep != '') {
            $data = $this->m_inventory->penjualan_save();
            die(json_encode($data));
        }
        if ($id != null) {
            $data['atribute'] = $this->m_inv_autocomplete->load_attribute_penjualan_by_resep($id)->result();
            $data['list_data'] = $this->m_inv_autocomplete->load_penjualan_by_no_resep($id)->result();
        }
        $this->load->view('inventory/penjualan', $data);
    }
    
    function penjualan_delete($id) {
        $data = $this->m_inventory->penjualan_delete($id);
        die(json_encode($data));
    }
    
    function repackage_delete($id) {
        $data = $this->m_inventory->repackage_delete($id);
        die(json_encode($data));
    }
    
    function stok_opname_delete($id) {
        $data = $this->m_inventory->stok_opname_delete($id);
        die(json_encode($data));
    }
    
    function reretur_pembelian_delete($id) {
        $data = $this->m_inventory->reretur_pembelian_delete($id);
        die(json_encode($data));
    }
    
    function retur_pembelian_delete($id) {
        $data = $this->m_inventory->retur_pembelian_delete($id);
        die(json_encode($data));
    }
    
    function distribusi_delete($id) {
        $data = $this->m_inventory->distribusi_delete($id);
        die(json_encode($data));
    }
    
    function penerimaan_distribusi_delete($id) {
        $data = $this->m_inventory->penerimaan_distribusi_delete($id);
        die(json_encode($data));
    }
    
    function repackage() {
        $data['title'] = 'Repackage';
        $this->load->view('inventory/repackage', $data);
    }
    
    function repackage_save() {
        $packing_barang = $this->input->post('pb');
        if (isset($packing_barang)) {
            $data = $this->m_inventory->repackage_save();
            die(json_encode($data));
        } else {
            die(json_encode(array('status' => false)));
        }
    }
    
    function reretur_pembelian() {
        $data['title'] = 'Penerimaan Retur Pembelian';
        $this->load->view('inventory/reretur-pembelian', $data);
    }
    
    function reretur_penjualan() {
        $data['title'] = 'Pengeluaran Retur Penjualan';
        $this->load->view('inventory/reretur-penjualan', $data);
    }
    
    function reretur_penjualan_save() {
        $noretur = $this->input->post('noretur');
        if (isset($noretur) and $noretur != '') {
            $data = $this->m_inventory->reretur_penjualan_save();
            die(json_encode($data));
        }
    }
    
    function stok_opname() {
        $data['title'] = 'Stok Opname';
        $alasan = $this->input->post('alasan');
        if (isset($alasan) and $alasan != '') {
            $param = $this->m_inventory->stok_opname_save();
            die(json_encode($param));
        }
        $this->load->view('inventory/stok-opname', $data);
    }
    
    function stok_opname_detail($id) {
        $data['title'] = 'Stok Opname';
        $data['list_data'] = $this->m_inventory->stok_opname_load_data($id)->result();
        $this->load->view('inventory/stok-opname_detail', $data);
    }
    
    function pemesanan_detail($id) {
        $data['title'] = 'Pemesanan';
        $data['list_data'] = $this->m_inventory->pemesanan_muat_data($id)->result();
        $this->load->view('inventory/pemesanan_detail', $data);
    }
    
    function pembelian_detail($id) {
        $data['title'] = 'Pembelian';
        $data['list_data'] = $this->m_inventory->pembelian_load_data($id)->result();
        $this->load->view('inventory/pembelian_detail', $data);
    }
    
    function distribusi_load_data($id) {
        $data['list_data'] = $this->m_inv_autocomplete->distribusi_load_data($id)->result();
        $this->load->view('inventory/pd-table', $data);
    }
    
    function repackage_detail($id) {
        $data['title'] = 'Repackage Detail';
        $data['label'] = array('Asal', 'Hasil');
        $data['jumlah']= array('keluar', 'masuk');
        $data['list_data'] = $this->m_inventory->repackage_load_data($id)->result();
        $this->load->view('inventory/repackage_detail', $data);
    }
    
    function retur_pembelian($id_pembelian = NULL) {
        $id = NULL;
        if ($id_pembelian != NULL) {
            $id = $id_pembelian;
        }
        $data['title'] = 'Retur Pembelian';
        $data['list_data'] = $this->m_inventory->pembelian_load_data($id)->result();
        $this->load->view('inventory/retur-pembelian', $data);
        
    }
    
    function retur_pembelian_detail($id_retur_pembelian = NULL) {
        $data['title'] = 'Retur Pembelian';
        $data['list_data'] = $this->m_inventory->retur_pembelian_load_data($id_retur_pembelian)->result();
        $this->load->view('inventory/retur-pembelian_detail', $data);
    }
    
    function reretur_pembelian_detail($id_retur_pembelian = NULL) {
        $data['title'] = 'Penerimaan Retur Pembelian';
        $data['list_data'] = $this->m_inventory->reretur_pembelian_load_data($id_retur_pembelian)->result();
        $this->load->view('inventory/reretur-pembelian_detail', $data);
    }
    
    function retur_pembelian_save() {
        $data['title'] = 'Retur Pembelian';
        $tanggal = $this->input->post('tanggal');
        if (isset($tanggal) and $tanggal != '') {
            $data = $this->m_inventory->retur_pembelian_save();
            die(json_encode($data));
        }
        //$this->load->view('inventory/retur-pembelian', $data);
    }
    
    function retur_penjualan($id_penjualan = NULL) {
        $id = NULL;
        if ($id_penjualan != NULL) {
            $id = $id_penjualan;
        }
        $data['title'] = 'Retur Penjualan';
        $data['list_data'] = $this->m_inventory->penjualan_load_data($id)->result();
        $this->load->view('inventory/retur-penjualan', $data);
        
    }
    
    function retur_penjualan_save() {
        $id_penjualan = $this->input->post('id_penjualan');
        if (isset($id_penjualan) and $id_penjualan != '') {
            $data = $this->m_inventory->retur_penjualan_save();
            die(json_encode($data));
        }
    }
    
    function reretur_pembelian_save() {
        $noretur = $this->input->post('noretur');
        if (isset($noretur) and $noretur != '') {
            $data = $this->m_inventory->reretur_pembelian_save();
            die(json_encode($data));
        }
    }
    
    function penjualan_detail($id) {
        $data['title'] = 'Penjualan';
        $data['list_data'] = $this->m_inventory->penjualan_load_data($id)->result();
        
        $this->load->view('inventory/penjualan_detail', $data);
    }
    
    function pemusnahan_detail($id) {
        $data['title'] = 'Pemusnahan';
        $data['list_data'] = $this->m_inventory->pemusnahan_load_data($id)->result();
        
        $this->load->view('inventory/pemusnahan_detail', $data);
    }
    
    function distribusi_detail($id) {
        $data['title'] = 'Distribusi';
        $data['list_data'] = $this->m_inv_autocomplete->distribusi_load_data($id)->result();
        
        $this->load->view('inventory/distribusi_detail', $data);
    }
    
    function pemakaian_detail($id) {
        $data['title'] = 'Pemakaian';
        $data['list_data'] = $this->m_inventory->pemakaian_load_data($id)->result();
        
        $this->load->view('inventory/pemakaian_detail', $data);
    }
    
    function penerimaan_distribusi_detail($id) {
        $data['title'] = 'Penerimaan Distribusi';
        $data['list_data'] = $this->m_inventory->penerimaan_distribusi_load_data($id)->result();
        $this->load->view('inventory/penerimaan-distribusi_detail', $data);
    }
    
    function retur_penjualan_detail($id) {
        $data['title'] = 'Retur Penjualan';
        $data['list_data'] = $this->m_inventory->retur_penjualan_load_data($id)->result();
        $this->load->view('inventory/retur-penjualan_detail', $data);
    }
    
    function retur_distribusi($id_distribusi) {
        $data['title'] = 'Retur Distribusi';
        $data['list_data'] = $this->m_inventory->penerimaan_distribusi_load_data($id_distribusi)->result();
        $this->load->view('inventory/retur-distribusi', $data);
    }
    
    function retur_distribusi_save() {
        $data = $this->m_inventory->retur_distribusi_save();
        die(json_encode($data));
    }
    
    function penerimaan_retur_unit() {
        $data['title'] = 'Penerimaan Retur Unit';
        $this->load->view('inventory/penerimaan-retur-distribusi', $data);
    }
    
    function penerimaan_retur_distribusi_save() {
        $data = $this->m_inventory->penerimaan_retur_distribusi_save();
        die(json_encode($data));
    }
    
    function pp_uang_detail($id) {
        $data['title'] = 'Pemasukan & Pengeluaran Kas';
        $data['list_data'] = $this->m_inventory->pp_uang_detail($id)->result();
        $this->load->view('pp-uang_detail', $data);
    }
    
    function pp_uang_delete($id) {
        $data = $this->m_inventory->pp_uang_delete($id);
        die(json_encode($data));
    }
    
    function defecta() {
        $data['title'] = 'Buku Defecta';
        $data['list_data'] = $this->m_inventory->defecta_load_data()->result();
        $this->load->view('inventory/defecta', $data);
    }
    
    function save_defecta($id = null) {
        $data = $this->m_inventory->save_defecta($id);
        die(json_encode($data));
    }
    
    function rencana_pemesanan() {
        $data['title'] = 'Rencana Pemesanan';
        $data['list_data'] = $this->m_inventory->rencana_pemesanan_load_data()->result();
        $this->load->view('inventory/rencana-pemesanan', $data);
    }
    
    function save_pemesanan_defecta() {
        $data = $this->m_inventory->save_pemesanan_defecta();
        die(json_encode($data));
    }
    
    function rencana_pemesanan_delete($id) {
        $data = $this->db->delete('defecta', array('barang_packing_id' => $id));
        die(json_encode($data));
    }
}
?>
