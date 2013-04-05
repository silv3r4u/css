<?php

class Laporan extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('configuration');
        $this->load->model('laporan_pendaftaran');
        $this->load->model('m_inventory');
        $this->load->model('m_referensi');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('functions');
        $this->load->model('user_app');
        $this->load->model('m_resep');
        $this->load->model('m_billing');
        is_logged_in();
    }

    function kunjungan_rs() {
        $data['title'] = "Laporan Kunjungan RS";
        $data['tipe'] = array('pilihtipe' => 'Pilih tipe Laporan ...', 'harian' => 'Harian', 'bulanan' => 'Bulanan');
        $data['jenis'] = array('pilihjenis' => 'Pilih Jenis Laporan ...', 'pasien' => 'Berdasarkan Pasien', 'unit' => 'Berdasarkan Unit');

        $data['bulan'] = $this->laporan_pendaftaran->get_bulan();
        $data['tahun'] = $this->laporan_pendaftaran->get_tahun();
        $data['bulan_now'] = array(date("m"));
        $data['tahun_now'] = array(date("Y"));
        $this->load->view('laporan/kunjungan_rs', $data);
    }

    function kunjungan_rs_harian($from = null, $to = null) {
        $data['from'] = $from;
        $data['to'] = $to;
        $data['title'] = "Laporan Kunjungan RS Harian";


        $this->load->view('laporan/kunjungan_rs_harian_tab', $data);
    }

    function kunjungan_rs_bulanan($from = null, $to = null) {
        $data['title'] = "Laporan Kunjungan RS Bulanan";
        $data['from'] = $from;
        $data['to'] = $to;
        $data['bulan_now'] = array(date("m"));
        $data['tahun_now'] = array(date("Y"));
        $data['bulan'] = $this->laporan_pendaftaran->get_bulan();
        $data['tahun'] = $this->laporan_pendaftaran->get_tahun();
        $this->load->view('laporan/kunjungan_rs_bulanan_tab', $data);
    }

    function kunjungan_rs_harian_pasien($from = null, $to = null) {

        $data['from'] = $from;
        $data['to'] = $to;
        if ($from != 'undefined-undefined-') {
            $data['pencarian'] = "<center><h1>Laporan Harian Kunjungan RS <br/> " . indo_tgl($from) . " s.d " . indo_tgl($to) . "</h1></center>";
        } else {
            $data['pencarian'] = "<center><h1>Laporan Harian Kunjungan RS </h1><br/></center>";
        }

        $data['hasil'] = $this->laporan_pendaftaran->get_kunjungan_harian(array('from' => $from, 'to' => $to));
        if ($data['hasil'] != null) {
            $data['pasienbaru'] = $this->laporan_pendaftaran->get_pasien_baru($data['hasil']);
            $data['pasienlama'] = $this->laporan_pendaftaran->get_pasien_lama($data['hasil']);
        }

        $this->load->view('laporan/kunjungan_rs_harian_pasien', $data);
    }

    function kunjungan_rs_harian_unit($from = null, $to = null) {
        $this->load->model('unit_layanan');
        $data['from'] = $from;
        $data['to'] = $to;

        if ($from != 'undefined-undefined-') {
            $data['pencarian'] = "<center><h1>Laporan kunjungan RS Harian per Unit<br/> " . indo_tgl($from) . " s.d " . indo_tgl($to) . "</h1></center>";
        } else {
            $data['pencarian'] = "<center><h1>Laporan kunjungan RS Harian per Unit<br/></h1></center>";
        }

        $data['semua_unit'] = $this->unit_layanan->get_unit_layanan_id();
        $data['hasil'] = $this->laporan_pendaftaran->get_kunjungan_harian(array('from' => $from, 'to' => $to));
        if ($data['hasil'] != null) {
            foreach ($data['semua_unit'] as $row) {
                $data['hasil_unit'][$row] = $this->laporan_pendaftaran->get_kunjungan_harian_unit($data['hasil'], $row);
            }
        }


        $this->load->view('laporan/kunjungan_rs_harian_unit', $data);
    }

    function kunjungan_rs_bulanan_pasien($bl_from = null, $th_from = null, $bl_to = null, $th_to = null) {
        $data['bl_from'] = $bl_from;
        $data['th_from'] = $th_from;
        $data['bl_to'] = $bl_to;
        $data['th_to'] = $th_to;
        $data['pencarian'] = "<center><h1>Laporan Bulanan Kunjungan RS <br/>" . tampil_bulan('' . '-' . $bl_from . '-' . '') . " " . $th_from . " s.d " . tampil_bulan('' . '-' . $bl_to . '-' . '') . " " . $th_to . "</h1>";
        $data['hasil'] = $this->laporan_pendaftaran->get_kunjungan_bulanan_pasien($data);
        if ($data['hasil'] != null) {
            $data['pasienbaru'] = $this->laporan_pendaftaran->get_pasien_bl_baru($data['hasil']);
            $data['pasienlama'] = $this->laporan_pendaftaran->get_pasien_bl_lama($data['hasil']);
        }
        $this->load->view('laporan/kunjungan_rs_bulanan_pasien', $data);
    }

    function kunjungan_rs_bulanan_unit($bl_from, $th_from, $bl_to, $th_to) {
        $this->load->model('unit_layanan');
        $data['bl_from'] = $bl_from;
        $data['th_from'] = $th_from;
        $data['bl_to'] = $bl_to;
        $data['th_to'] = $th_to;
        $data['pencarian'] = "<center><h1>Laporan Bulanan kunjungan RS per Unit <br/>" . tampil_bulan('' . '-' . $bl_from . '-' . '') . " " . $th_from . " s.d " . tampil_bulan('' . '-' . $bl_to . '-' . '') . " " . $th_to . "</h1></center>";
        $data['semua_unit'] = $this->unit_layanan->get_unit_layanan_id();
        $data['hasil'] = $this->laporan_pendaftaran->get_kunjungan_bulanan_pasien($data);
        if ($data['hasil'] != null) {
            foreach ($data['semua_unit'] as $row) {
                $data['hasil_unit'][$row] = $this->laporan_pendaftaran->get_kunjungan_bulanan_unit($data['hasil'], $row);
            }
        }
        $this->load->view('laporan/kunjungan_rs_bulanan_unit', $data);
    }

    function laporan_demografi() {
        $this->load->model('demografi_pasien');
        $this->load->view('layout');
        $data['title'] = "Demografi Pasien";
        $data['jenis'] = $this->demografi_pasien->jenis_demografi();
        $data['area'] = array('pilih' => 'Pilih ...... ', 'kelurahan' => 'Kelurahan', 'kecamatan' => 'Kecamatan', 'kabupaten' => 'Kabupaten', 'provinsi' => 'Provinsi');
        $this->load->view('laporan/laporan_demografi', $data);
    }

    function laporan_demografi_wilayah($from = null, $to = null) {
        $this->load->model('demografi_pasien');
        $data['sub_title'] = "Demografi Pasien Berdasarkan ";

        $tipe = $this->input->post('tipe');
        $prov = $this->input->post('prov');
        $kab = $this->input->post('kab');

        // array variable
        $kec = $this->input->post('kec');
        $kel = $this->input->post('kel');

        if ($tipe != null) {

            if ($tipe == "kelurahan") {
                $data['sub_title'] .="Kelurahan <br/>";
                $hasil = json_encode($this->laporan_pendaftaran->get_demografi_wilayah(array('from' => $from, 'to' => $to, 'tipe' => $tipe, 'area' => $kel)));
            } else if ($tipe == "kecamatan") {
                $data['sub_title'] .="Kecamatan <br/>";
                $hasil = json_encode($this->laporan_pendaftaran->get_demografi_wilayah(array('from' => $from, 'to' => $to, 'tipe' => $tipe, 'area' => $kec)));
            } else if ($tipe == "kabupaten") {
                $data['sub_title'] .="Kabupaten <br/>";
                $hasil = json_encode($this->laporan_pendaftaran->get_demografi_wilayah(array('from' => $from, 'to' => $to, 'tipe' => $tipe, 'area' => array($kab))));
            } else if ($tipe == "provinsi") {
                $data['sub_title'] .="Provinsi <br/>";
                $hasil = json_encode($this->laporan_pendaftaran->get_demografi_wilayah(array('from' => $from, 'to' => $to, 'tipe' => $tipe, 'area' => array($prov))));
            }

            if ($from != 'undefined-undefined-') {
                $data['sub_title'] .= indo_tgl($from) . " s.d " . indo_tgl($to);
            }

            $data['hasil'] = json_encode($hasil);
        } else {
            $data['hasil'] = null;
        }

        $this->load->view('laporan/laporan_demografi_wilayah', $data);
    }

    function laporan_demografi_agama($from = null, $to = null) {
        $this->load->model('demografi_pasien');
        $data['agama'] = $this->demografi_pasien->agama();
        $data['sub_title'] = "Demografi Pasien Berdasarkan Agama<br/> ";
        if ($from != 'undefined-undefined-') {
            $data['sub_title'] .= indo_tgl($from);
        }
        if ($to != 'undefined-undefined-') {
            $data['sub_title'] .= " s.d " . indo_tgl($to);
        }
        $data['hasil'] = json_encode($this->laporan_pendaftaran->get_demografi_agama(array('from' => $from, 'to' => $to, 'agama' => $data['agama'])));
        $data['jsagama'] = json_encode($data['agama']);
        $this->load->view('laporan/laporan_demografi_agama', $data);
    }

    function laporan_demografi_pekerjaan($from = null, $to = null) {
        $data['pekerjaan'] = $this->laporan_pendaftaran->kategori_demografi('pekerjaan');
        $data['sub_title'] = "Demografi Pasien Berdasarkan Pekerjaan<br/> ";
        if ($from != 'undefined-undefined-') {
            $data['sub_title'] .= indo_tgl($from);
        }
        if ($to != 'undefined-undefined-') {
            $data['sub_title'] .= " s.d " . indo_tgl($to);
        }
        $data['hasil'] = json_encode($this->laporan_pendaftaran->get_demografi_pekerjaan(array('from' => $from, 'to' => $to, 'pekerjaan' => $data['pekerjaan'])));
        $data['jspekerjaan'] = json_encode($data['pekerjaan']);
        $this->load->view('laporan/laporan_demografi_pekerjaan', $data);
    }

    function laporan_demografi_pendidikan($from = null, $to = null) {
        $data['pendidikan'] = $this->laporan_pendaftaran->kategori_demografi('pendidikan');
        $data['sub_title'] = "Demografi Pasien Berdasarkan Pendidikan<br/> ";
        if ($from != 'undefined-undefined-') {
            $data['sub_title'] .= indo_tgl($from);
        }
        if ($to != 'undefined-undefined-') {
            $data['sub_title'] .= " s.d " . indo_tgl($to);
        }
        $data['hasil'] = json_encode($this->laporan_pendaftaran->get_demografi_pendidikan(array('from' => $from, 'to' => $to, 'pendidikan' => $data['pendidikan'])));
        $data['jspendidikan'] = json_encode($data['pendidikan']);
        $this->load->view('laporan/laporan_demografi_pendidikan', $data);
    }

    function laporan_demografi_nikah($from = null, $to = null) {
        $this->load->model('demografi_pasien');
        $data['nikah'] = $this->demografi_pasien->stat_nikah();
        $data['sub_title'] = "Demografi Pasien Berdasarkan Status Pernikahan<br/> ";
        if ($from != 'undefined-undefined-') {
            $data['sub_title'] .= indo_tgl($from);
        }
        if ($to != 'undefined-undefined-') {
            $data['sub_title'] .= " s.d " . indo_tgl($to);
        }
        $data['hasil'] = json_encode($this->laporan_pendaftaran->get_demografi_nikah(array('from' => $from, 'to' => $to, 'nikah' => $data['nikah'])));
        $data['jsnikah'] = json_encode($data['nikah']);
        $this->load->view('laporan/laporan_demografi_nikah', $data);
    }

    function laporan_demografi_kelamin($from = null, $to = null) {
        $this->load->model('demografi_pasien');
        $data['kelamin'] = $this->demografi_pasien->kelamin();
        $data['sub_title'] = "Demografi Pasien Berdasarkan Jenis Kelamin<br/> ";
        if ($from != 'undefined-undefined-') {
            $data['sub_title'] .= indo_tgl($from);
        }
        if ($to != 'undefined-undefined-') {
            $data['sub_title'] .= " s.d " . indo_tgl($to);
        }
        $data['hasil'] = json_encode($this->laporan_pendaftaran->get_demografi_kelamin(array('from' => $from, 'to' => $to, 'kelamin' => $data['kelamin'])));
        $data['jskelamin'] = json_encode($data['kelamin']);
        $this->load->view('laporan/laporan_demografi_kelamin', $data);
    }

    function laporan_demografi_darah($from = null, $to = null) {
        $this->load->model('demografi_pasien');
        $data['darah'] = $this->demografi_pasien->gol_darah();
        $data['sub_title'] = "Demografi Pasien Berdasarkan Golongan Darah<br/> ";
        if ($from != 'undefined-undefined-') {
            $data['sub_title'] .= indo_tgl($from);
        }
        if ($to != 'undefined-undefined-') {
            $data['sub_title'] .= " s.d " . indo_tgl($to);
        }
        $data['hasil'] = json_encode($this->laporan_pendaftaran->get_demografi_darah(array('from' => $from, 'to' => $to, 'darah' => $data['darah'])));
        $data['jsdarah'] = json_encode($data['darah']);
        $this->load->view('laporan/laporan_demografi_darah', $data);
    }

    function laporan_demografi_usia($from = null, $to = null) {
        $this->load->model('demografi_pasien');
        $data['usia'] = $this->demografi_pasien->rentang_usia();
        $data['sub_title'] = "Demografi Pasien Berdasarkan Rentang Usia<br/> ";
        if ($from != 'undefined-undefined-') {
            $data['sub_title'] .= indo_tgl($from);
        }
        if ($to != 'undefined-undefined-') {
            $data['sub_title'] .= " s.d " . indo_tgl($to);
        }
        $data['hasil'] = json_encode($this->laporan_pendaftaran->get_demografi_usia(array('from' => $from, 'to' => $to, 'usia' => $data['usia'])));
        $data['jsusia'] = json_encode($this->demografi_pasien->format_usia());
        $this->load->view('laporan/laporan_demografi_usia', $data);
    }

    function get_kelurahan($kec_id) {
        $this->db->where('kecamatan_id', $kec_id);
        $rows = $this->db->get('kelurahan')->result();
        die(json_encode($rows));
    }

    function get_kecamatan($kab_id) {
        $this->db->where('kabupaten_id', $kab_id);
        $rows = $this->db->get('kecamatan')->result();
        die(json_encode($rows));
    }

    function get_kabupaten($prov_id) {
        $this->db->where('provinsi_id', $prov_id);
        $rows = $this->db->get('kabupaten')->result();
        die(json_encode($rows));
    }

    function get_provinsi() {
        $rows = $this->db->get('provinsi')->result();
        die(json_encode($rows));
    }

    function resep() {
        $data['title'] = 'Rekap Resep';
        if (isset($_GET['awal'])) {
            $noresep = isset($_GET['noresep']) ? $_GET['noresep'] : NULL;
            $awal = isset($_GET['awal']) ? $_GET['awal'] : NULL;
            $akhir = isset($_GET['awal']) ? $_GET['akhir'] : NULL;
            $pasien = isset($_GET['id_pasien']) ? $_GET['id_pasien'] : NULL;
            $dokter = isset($_GET['id_dokter']) ? $_GET['id_dokter'] : NULL;
            $apoteker=isset($_GET['id_apoteker']) ? $_GET['id_apoteker'] : NULL;
            $data['list_data'] = $this->m_resep->resep_report_muat_data(null, $awal, $akhir, $pasien, $dokter, null, $apoteker)->result();
        }
        $this->load->view('laporan/info-resep', $data);
    }
    
    function penjualan_jasa() {
        $data['title'] = 'Rekap Penjualan Jasa';
        if (isset($_GET['awal'])) {
            $param['awal'] = $this->input->get('awal');
            $param['akhir'] = $this->input->get('akhir');
            $param['nakes'] = $this->input->get('id_nakes');
            $data['list_data'] = $this->m_billing->penjualan_jasa_load_data($param)->result();
        }
        $this->load->view('laporan/penjualan-jasa', $data);
    }

    function resep_detail($id_resep) {
        $data['title'] = 'Resep';
        $data['list_data'] = $this->m_resep->data_resep_load_data($id_resep)->result();
        $this->load->view('laporan/resep_detail', $data);
    }

    function salin_resep($id_resep) {
        $data['resep'] = $this->m_resep->data_resep_load_data($id_resep)->result();
        $data['id_resep'] = $id_resep;
        $data['datas'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['apa'] = $this->configuration->penduduk_manager_farmasi()->row();
        $this->load->view('inventory/print/resep', $data);
    }

    function statistika_resep() {
        $data['title'] = 'Statistika Resep';
        $data['statistika_resep'] = $this->m_resep->statistika_resep($_GET['awal'], $_GET['akhir'])->result();
        $data['data'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $this->load->view('inventory/print/statistika-resep', $data);
    }

    function hutang() {
        $data['title'] = 'Laporan Hutang';
        if (isset($_GET['awal'])) {
            $data['list_data'] = $this->m_inventory->hutang_load_data($_GET['awal'], $_GET['akhir'])->result();
        }
        $this->load->view('laporan/laporan-utang', $data);
    }

    function laporan_utang_cetak() {
        $this->load->view('laporan/print/laporan-utang', $data);
    }

    function stok() {

        $data['title'] = 'Stok';
        $data['jenis_transaksi'] = $this->m_inventory->jenis_transaksi_load_data();
        $data['unit'] = $this->m_referensi->unit_get_data('inventori');
        $data['perundangan'] = $this->m_referensi->perundangan_load_data();
        $data['generik'] = $this->m_referensi->generik_load_data();
        $data['sediaan'] = $this->m_referensi->sediaan_get_data();
        if (isset($_GET['sort'])) {
            $param['awal'] = $this->input->get('awal');
            $param['akhir'] = $this->input->get('akhir');
            $param['id_pb'] = $this->input->get('id_pb');
            $param['atc'] = $this->input->get('atc');
            $param['ddd'] = $this->input->get('ddd');
            $param['perundangan'] = $this->input->get('perundangan');
            $param['generik'] = $this->input->get('generik');
            $param['jenis'] = $this->input->get('transaksi_jenis');
            $param['sort'] = $this->input->get('sort');
            $param['unit'] = $this->input->get('unit');
            $param['sediaan'] = $this->input->get('sediaan');
            $param['jns_barang'] = $this->input->get('jenis');
            //($awal = null, $akhir = null,$id_pb = null, $atc = null, $ddd = null, $perundangan = null, $generik = null, $jenis = null, $sort = null, $unit = null)
            $data['list_data'] = $this->m_inventory->informasi_stok_load_data($param)->result();
        }
        $this->load->view('laporan/stok', $data);
    }

    function print_stok() {
        if (isset($_GET['sort'])) {
            $param['awal'] = $this->input->get('awal');
            $param['akhir'] = $this->input->get('akhir');
            $param['id_pb'] = $this->input->get('id_pb');
            $param['atc'] = $this->input->get('atc');
            $param['ddd'] = $this->input->get('ddd');
            $param['perundangan'] = $this->input->get('perundangan');
            $param['generik'] = $this->input->get('generik');
            $param['jenis'] = $this->input->get('transaksi_jenis');
            $param['sort'] = $this->input->get('sort');
            $param['unit'] = $this->input->get('unit');
            $param['sediaan'] = $this->input->get('sediaan');
            $param['jns_barang'] = $this->input->get('jenis');
            //($awal = null, $akhir = null,$id_pb = null, $atc = null, $ddd = null, $perundangan = null, $generik = null, $jenis = null, $sort = null, $unit = null)
            $data['list_data'] = $this->m_inventory->informasi_stok_load_data($param)->result();
            $data['period'] = $param['awal'] . ' s.d ' . $param['akhir'];
            if ($param['awal'] == '' and $param['akhir'] == '') {
                $data['period'] = '';
            }
            $data['datas'] = $this->configuration->rumah_sakit_get_atribute()->row();
        }
        $this->load->view('inventory/print/stok', $data);
    }

    function psikotropika() {
        $param['awal'] = $this->input->get('awal');
        $param['akhir'] = $this->input->get('akhir');
        $param['id_pb'] = $this->input->get('id_pb');
        $param['atc'] = $this->input->get('atc');
        $param['ddd'] = $this->input->get('ddd');
        $param['perundangan'] = $this->input->get('perundangan');
        $param['generik'] = $this->input->get('generik');
        $param['jenis'] = $this->input->get('transaksi_jenis');
        $param['sort'] = $this->input->get('sort');
        $param['unit'] = $this->input->get('unit');
        $param['sediaan'] = $this->input->get('sediaan');
        $param['jns_barang'] = $this->input->get('jenis');
        $data['datas'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['list_data'] = $data['list_data'] = $this->m_inventory->informasi_stok_load_data($param)->result();
        $this->load->view('inventory/print/lap-psikotropika', $data);
    }

    function stelling() {
        $data['title'] = 'Kartu Stelling';
        $data['stelling'] = $this->m_inventory->stelling_load_data($_GET['id_pb'], $_GET['awal'], $_GET['akhir'])->result();
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $this->load->view('inventory/print/stelling', $data);
    }

    function kas() {
        $data['title'] = 'Laporan Kas';
        $data['jenis_transaksi'] = $this->configuration->jenis_transaksi();
        $this->load->view('laporan/kas', $data);
    }

    function kas_load_data() {
        $awal = $_GET['awal'];
        $akhir = $_GET['akhir'];
        $jenis = $_GET['jenis'];
        $nama = $_GET['nama'];
        $data['list_data'] = $this->m_inventory->kas_load_data($awal, $akhir, $jenis, $nama)->result();
        $this->load->view('laporan/kas-table', $data);
    }

    function reimbursement() {
        $data['title'] = 'Laporan Reimbursement';
        if (isset($_GET['awal'])) {
            $awal = isset($_GET['awal']) ? $_GET['awal'] : NULL;
            $akhir = isset($_GET['akhir']) ? $_GET['akhir'] : NULL;
            $id_asuransi = isset($_GET['id_asuransi']) ? $_GET['id_asuransi'] : NULL;
            $data['list_data'] = $this->m_inventory->reimbursement_load_data($awal, $akhir, $id_asuransi)->result();
        }
        $this->load->view('laporan/reimbursement', $data);
    }

    function rekap_laporan() {
        $data['title'] = 'Rekap Laporan';
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['og'] = $this->m_resep->data_item_obat('Generik')->num_rows();
        $data['ng_form'] = $this->m_resep->data_item_obat('Non Generik', 'Ya')->num_rows();
        $data['ng'] = $this->m_resep->data_item_obat('Non Generik')->num_rows();
        $data['og_2'] = $this->m_resep->data_item_obat('Generik', NULL, TRUE, $_GET['awal'], $_GET['akhir'])->num_rows();
        $data['ng_form_2'] = $this->m_resep->data_item_obat('Non Generik', 'Ya', TRUE, $_GET['awal'], $_GET['akhir'])->num_rows();
        $data['ng_2'] = $this->m_resep->data_item_obat('Non Generik', NULL, TRUE, $_GET['awal'], $_GET['akhir'])->num_rows();

        $data['og_3'] = $this->m_resep->data_item_obat('Generik', 'Ya', TRUE, $_GET['awal'], $_GET['akhir'])->num_rows();
        $data['ng_form_3'] = $this->m_resep->data_item_obat('Non Generik', 'Ya', TRUE, $_GET['awal'], $_GET['akhir'])->num_rows();
        $data['ng_3'] = $this->m_resep->data_item_obat('Non Generik', 'Ya', TRUE, $_GET['awal'], $_GET['akhir'])->num_rows();

        $data['g_rj'] = $this->m_resep->pelayanan_resep('Generik', 'Rawat Jalan')->num_rows();
        $data['g_igd'] = $this->m_resep->pelayanan_resep('Generik', 'IGD')->num_rows();
        $data['g_ri'] = $this->m_resep->pelayanan_resep('Generik', 'Rawat Inap')->num_rows();

        $data['ngf_rj'] = $this->m_resep->pelayanan_resep('Non Generik', 'Rawat Jalan', 'Ya')->num_rows();
        $data['ngf_igd'] = $this->m_resep->pelayanan_resep('Non Generik', 'IGD', 'Ya')->num_rows();
        $data['ngf_ri'] = $this->m_resep->pelayanan_resep('Non Generik', 'Rawat Inap', 'Ya')->num_rows();

        $data['ng_rj'] = $this->m_resep->pelayanan_resep('Non Generik', 'Rawat Jalan')->num_rows();
        $data['ng_igd'] = $this->m_resep->pelayanan_resep('Non Generik', 'IGD')->num_rows();
        $data['ng_ri'] = $this->m_resep->pelayanan_resep('Non Generik', 'Rawat Inap')->num_rows();


        $this->load->view('laporan/rekap-laporan', $data);
    }

    function laporan_abc() {
        $param['awal'] = $this->input->get('awal');
        $param['akhir'] = $this->input->get('akhir');
        $param['id_pb'] = $this->input->get('id_pb');
        $param['atc'] = $this->input->get('atc');
        $param['ddd'] = $this->input->get('ddd');
        $param['perundangan'] = $this->input->get('perundangan');
        $param['generik'] = $this->input->get('generik');
        $param['jenis'] = 'Penjualan';
        $param['sort'] = 'History';
        $param['unit'] = $this->input->get('unit');
        $param['laporan'] = 'abc';
        $param['sediaan'] = NULL;
        $param['jns_barang'] = $this->input->get('jenis');
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['list_data'] = $this->m_inventory->informasi_stok_load_data($param)->result();
        $this->load->view('laporan/laporan-abc', $data);
    }

    function rujukan() {
        $data['title'] = 'Laporan Rujukan';
        $this->load->view('laporan/rujukan', $data);
    }

    function rujukan_data() {
        $limit = 15;
        $param = array(
            'from' => date2mysql($this->input->post('fromdate')),
            'to' => date2mysql($this->input->post('todate')),
            'instansi' => $this->input->post('id_instansi'),
            'nakes' => $this->input->post('id_nakes')
        );
        $page = $this->input->post('p');

        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data = $this->laporan_pendaftaran->rujukan_get_data($limit, $start, $param);
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, 'null');

        if ($param['from'] != '') {
            $data['from'] = $param['from'];
        }
        
        if ($param['to'] != '') {
            $data['to'] = $param['to'];
        }

        $this->load->view('laporan/rujukan_list', $data);
    }

}

?>
