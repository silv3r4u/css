<?php

class Pendaftaran extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('configuration');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('login');
        $this->load->model('pendaftaran_pasien');
        $this->load->model('unit_layanan');
        $this->load->model('user_app');
        is_logged_in();
    }

    function is_login() {
        $user = $this->session->userdata('id_user');
        if ($user != '') {
            
        } else {
            //redirect(base_url());
        }
    }

    function index() {

        $this->index_get();
    }

    function index_get() {
        //$this->load->view('layout', $menu);

        $data['title'] = "Pendaftaran";
        $this->load->view('pendaftaran/index', $data);
    }

    function list_pendaftar() {
        $this->load->view('layout');
        $this->load->helper('functions_helper');
        $data['title'] = 'Data Pendaftar';
        $this->load->view('pendaftaran/list', $data);
    }

    function list_data_pendaftar($from = null, $to = null, $page = null) {
        $data['sub_title'] = "<h2>Daftar Kunjungan Pasien<br/> ";

        if ($from != 'null') {
            $data['sub_title'] .= indo_tgl($from) . " s.d " . indo_tgl($to) . "<br/>";
        } else {
            $data['sub_title'] .= "Semua Kunjungan</h2>";
        }


        $limit = 10;
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $search = 'null';
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->pendaftaran_pasien->get_pendaftar($from, $to, $limit, $start);
        $data['jumlah'] = $query['jumlah'];
        $data['hasil'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        $this->load->view('pendaftaran/list_data', $data);
    }

    function pencarian() {
        $this->load->view('layout');

        $data['title'] = "Pencarian Pasien";
        $this->load->view('pencarian', $data);
    }

    function new_pasien($no_rm) {
        $this->load->view('layout');

        $this->load->model('demografi_pasien');
        $data['asuransi'] = $this->demografi_pasien->get_asuransi_kepesertaan($no_rm, null)->result();
        $data['total'] = $this->demografi_pasien->get_asuransi_kepesertaan($no_rm, null)->num_rows();
        $data['pasien'] = $this->demografi_pasien->get_by_no_rm($no_rm);
        $data['title'] = "Pendaftaran Pasien - Baru";
        $data['keb_rawat'] = $this->pendaftaran_pasien->keb_rawat();
        $data['jenis_rawat'] = $this->pendaftaran_pasien->jenis_rawat();
        $data['jenis_layan'] = $this->pendaftaran_pasien->jenis_layan();
        $data['krit_layan'] = $this->pendaftaran_pasien->krit_layan();
        $this->load->view('pendaftaran/new', $data);
    }

    function get_unit_layanan() {
        $q = $_GET['q'];
        $data = $this->unit_layanan->load_data_unit_layan($q)->result();
        die(json_encode($data));
    }

    function new_post() {
        $this->load->model('unit_layanan');
        $this->load->model('demografi_pasien');

        $nextAntri = array(
            'kd_unit' => $this->input->post('unit_layan'),
            'tgl_layan' => datetopg($this->input->post('tgl_layan'))
        );
        $this->demografi_pasien->next_kunjungan(array('no_rm' => $this->input->post('no_rm')));

        $id_penanggung_jawab = $this->input->post('id_pjawab');
        if ($id_penanggung_jawab == '') {
            if ($this->input->post('id_pjawab') != '') {
                $pjawab = array(
                    'nama' => $this->input->post('pjawab'),
                    'telp' => $this->input->post('telppjawab')
                );
                $this->db->insert('penduduk', $pjawab);
                $id_penanggung_jawab = $this->db->insert_id();

                $dinamis_penduduk_pjawab = array(
                    'alamat' => $this->input->post('alamatpjawab'),
                    'kelurahan_id' => $this->input->post('id_kelpjawab'),
                    'penduduk_id' => $id_penanggung_jawab
                );
                $this->db->insert('dinamis_penduduk', $dinamis_penduduk_pjawab);
            } else {
                $id_penanggung_jawab = $this->input->post('id_penduduk');
            }
        } else {
            // Checking Kelurahan Id
            $kel_id = $this->demografi_pasien->get_penduduk($id_penanggung_jawab)->kelurahan_id;

            if ($kel_id != $this->input->post('id_kelpjawab')) {
                $up_pj = array(
                    'penduduk_id' => $id_penanggung_jawab,
                    'kelurahan_id' => $this->input->post('id_kelpjawab')
                );
                $this->db->insert('dinamis_penduduk', $up_pj);
            }
        }
        $data = array(
            'pasien' => $this->input->post('no_rm'),
            'pjwb_penduduk_id' => $id_penanggung_jawab,
            'layanan' => $this->input->post('unit_layan'),
            'tgl_daftar' => date('Y-m-d H:i:s'),
            'tgl_layan' => datetopg($this->input->post('tgl_layan')),
            'no_antri' => $this->unit_layanan->get_next_antrian($nextAntri),
            'keb_rawat' => $this->input->post('keb_rawat'),
            'jenis_rawat' => $this->input->post('jenis_rawat'),
            'jenis_layan' => $this->input->post('jenis_layan'),
            'krit_layan' => $this->input->post('krit_layan'),
            'kd_ptgs_daft' => $this->session->userdata('id_user'),
            'kd_ptgs_confirm' => NULL,
            'arrive_time' => NULL,
            'dinamis_penduduk_id' => $this->input->post('dinamis'),
            'rujukan_instansi_id' => ($this->input->post('id_instansi') != '') ? $this->input->post('id_instansi') : NULL,
            'nakes_penduduk_id' => ($this->input->post('id_nakes') != '') ? $this->input->post('id_nakes') : NULL
        );
        $last = $this->pendaftaran_pasien->create_and_save($data);

        //insert biaya kunjungan
        $param['no_daftar'] = $last;
        $param['tarif_id'] = 2; // kunjungan pasien
        $this->pendaftaran_pasien->insert_biaya($param);
        redirect('pendaftaran/detail/' . $last . "?msg=1");
    }

    /* fungsi - fungsi untuk pencarian */

    function search() {
        $this->load->view('layout');
        $data['title'] = "Konfirmasi Pendaftaran";
        $data['today'] = date('l, d F Y');
        $this->load->view('pendaftaran/search', $data);
    }

    function search_by_nama_get() {
        $this->load->view('pendaftaran/search-tab1');
    }

    function search_by_nama_post($page) {
        $limit = 10;
        $start = ($page - 1) * $limit;
        $search = 'null';
        $data['page'] = $page;
        $data['limit'] = $limit;
        $param = array('nama' => $_GET['nama'], 'no_rm' => null, 'layanan' => null, 'no_antri' => null);
        $query = $this->pendaftaran_pasien->get($limit, $start, $param);
        $data['pasien'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        $this->load->view('pendaftaran/hasil_pencarian', $data);
    }

    function search_by_no_antri_get() {
        $data['layanan'] = $this->unit_layanan->get_unit_layanan();
        $this->load->view('pendaftaran/search-tab2', $data);
    }

    function search_by_no_antri_post($page) {
        $limit = 10;
        $start = ($page - 1) * $limit;
        $search = 'null';
        $data['page'] = $page;
        $data['limit'] = $limit;
        $param = array('nama' => null, 'no_rm' => null, 'layanan' => $_GET['unit'], 'no_antri' => $_GET['antri']);
        $query = $this->pendaftaran_pasien->get($limit, $start, $param);
        $data['pasien'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        $this->load->view('pendaftaran/hasil_pencarian', $data);
    }

    function search_by_no_rm_get() {
        $this->load->view('pendaftaran/search-tab3');
    }

    function search_by_no_rm_post() {
        $param = array('nama' => null, 'no_rm' => $this->input->post('no_rm'), 'layanan' => null, 'no_antri' => NULL);
        $query = $this->pendaftaran_pasien->get(1, 0, $param);
        $data['pasien'] = $query['data'];
        $data['jumlah'] = '';
        $data['paging'] = "";
        $this->load->view('pendaftaran/hasil_pencarian', $data);
    }

    function search_by_unit_get() {
        $data['layanan'] = $this->unit_layanan->get_unit_layanan("admission");
        $this->load->view('pendaftaran/search-tab4', $data);
    }

    function search_by_unit_post($page) {
        $limit = 10;
        $start = ($page - 1) * $limit;
        $search = 'null';
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['layanan'] = $_GET['unit'];
        $param = array('nama' => null, 'no_rm' => null, 'layanan' => $data['layanan'], 'no_antri' => null);
        $query = $this->pendaftaran_pasien->get($limit, $start, $param);
        $data['pasien'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        $this->load->view('pendaftaran/hasil_pencarian', $data);
    }

    /* fungsi - fungsi untuk pencarian */

    function detail($no_daftar) {
        $this->load->view('layout');
        $this->load->model('demografi_pasien');
        $data['pasien'] = $this->pendaftaran_pasien->get_by_no_daftar($no_daftar);
        $data['title'] = "Detail Pendaftar";
        if ($data['pasien'] != null) {
            $asu = $data['pasien'];
            $data['asuransi'] = $this->demografi_pasien->get_asuransi_kepesertaan($asu->no_rm, null)->result();
            $data['total'] = $this->demografi_pasien->get_asuransi_kepesertaan($asu->no_rm, null)->num_rows();
            $data['biaya_kartu'] = $this->pendaftaran_pasien->get_biaya_kartu($no_daftar);
            $data['biaya_kunjungan'] = $this->pendaftaran_pasien->get_biaya_kunjungan($no_daftar);
        }
        $this->load->view('pendaftaran/detail', $data);
    }

    function cetak_kartu_get($no_daftar) {
        $this->load->model('demografi_pasien');
        //ambil no rm berdasarkan no daftar
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['pasien'] = $this->pendaftaran_pasien->get_by_no_daftar($no_daftar);

        $this->load->view('demografi/card', $data);
    }

    function add_biaya_kartu($no_rm, $no_daftar) {
        $this->load->model('demografi_pasien');
        $param['no_daftar'] = $no_daftar;
        $param['tarif_id'] = 1; //cetak kartu
        $this->pendaftaran_pasien->insert_biaya($param);
        $this->demografi_pasien->add_is_cetak_kartu($no_rm);
    }

    function cetak_no_antri_get($no_daftar) {
        $data['title'] = 'Cetak nomor antrian';
        $data['pasien'] = $this->pendaftaran_pasien->get_by_no_daftar($no_daftar);
        $this->load->view('pendaftaran/cetak_no_antri', $data);
    }

    function set_arrive_time($no_daftar) {
        $this->pendaftaran_pasien->set_arrive_time($no_daftar);
    }

    function cetak_lembar_pertama($no_daftar, $perawatan) {
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['rows'] = $this->pendaftaran_pasien->get_by_no_daftar($no_daftar);
        if (urldecode($perawatan) == 'Rawat Jalan') {
            $data['title'] = 'LEMBAR POLIKLINIK';
            $this->load->view('pendaftaran/lembar-pertama-rm-poli', $data);
        } else {
            $data['title'] = 'LEMBAR GAWAT DARURAT';
            $this->load->view('pendaftaran/lembar-pertama-rm-igd', $data);
        }
    }

    public function load_data_instansi_relasi() {
        $q = $_GET['q'];
        $data = $this->pendaftaran_pasien->load_data_instansi_relasi($q)->result();
        die(json_encode($data));
    }

    function load_data_penduduk_profesi() {

        $q = $_GET['q'];
        $data = $this->pendaftaran_pasien->load_data_penduduk_profesi($q)->result();
        die(json_encode($data));
    }

}

?>
