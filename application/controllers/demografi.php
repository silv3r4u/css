<?php

class Demografi extends CI_Controller {

    public $waktu = null;
    public $hari = null;

    function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->model('configuration');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('functions');
        $this->load->model('user_app');

        $this->waktu = gmdate('Y-m-d H:i:s', gmdate('U') + 25200);
        $this->hari = gmdate('Y-m-d', gmdate('U') + 25200);
    }

    function is_login() {
        $user = $this->session->userdata('user');
        if ($user != '') {
            
        } else {
            redirect(base_url());
        }
    }

    function edit($no_rm) {
        $this->edit_get($no_rm);
    }

    function edit_get($no_rm) {
        $this->load->view('layout');

        $data['pasien'] = $this->demografi_pasien->get_by_no_rm($no_rm);

        $data['kelamin'] = $this->demografi_pasien->kelamin();
        $data['darah'] = $this->demografi_pasien->gol_darah();
        $data['agama'] = $this->demografi_pasien->agama();
        $data['pendidikan'] = $this->demografi_pasien->pendidikan();
        $data['pekerjaan'] = $this->demografi_pasien->pekerjaan();
        $data['stat_nikah'] = $this->demografi_pasien->stat_nikah();
        $data['asuransi'] = $this->demografi_pasien->get_asuransi_kepesertaan($no_rm, null)->result();
        $data['total'] = $this->demografi_pasien->get_asuransi_kepesertaan($no_rm, null)->num_rows();
        $data['title'] = "Edit Data Pasien";

        $this->load->view('demografi/edit', $data);
    }

    function edit_put() {
        $edited = false;
        if ($this->input->post('no_rm')) {
            if ($this->input->post('identitas_no') != $this->input->post('bf_identitas_no'))
                $edited = true;
            if ($this->input->post('agama') != $this->input->post('bf_agama'))
                $edited = true;
            if ($this->input->post('alamat') != $this->input->post('bf_alamat'))
                $edited = true;
            if ($this->input->post('pendidikan') != $this->input->post('bf_pendidikan'))
                $edited = true;
            if ($this->input->post('pernikahan') != $this->input->post('bf_pernikahan'))
                $edited = true;
            if ($this->input->post('pekerjaan') != $this->input->post('bf_pekerjaan'))
                $edited = true;
            if ($this->input->post('hd_kelurahan') != $this->input->post('bf_hd_kelurahan'))
                $edited = true;

            $ayah_id = ($this->input->post('hd_bapak') == "") ? NULL : $this->input->post('hd_bapak');
            $ibu_id = ($this->input->post('hd_ibu') == "") ? NULL : $this->input->post('hd_ibu');
            $kelurahan_id = ($this->input->post('hd_kelurahan') == "") ? NULL : $this->input->post('hd_kelurahan');
            $tmp_id = ($this->input->post('hd_lahir_tempat') == "") ? NULL : $this->input->post('hd_lahir_tempat');

            $penduduk = array(
                'id' => $this->input->post('id'),
                'nama' => $this->input->post('nama'),
                'gender' => $this->input->post('gender'),
                'darah_gol' => $this->input->post('darah_gol'),
                'lahir_kabupaten_id' => $tmp_id, // link to kabupaten_id
                'lahir_tanggal' => datetopg($this->input->post('lahir_tanggal')),
                'telp' => $this->input->post('telp'),
                'ayah_penduduk_id' => $ayah_id,
                'ibu_penduduk_id' => $ibu_id
            );

            $this->demografi_pasien->save_penduduk($penduduk);


            if ($edited) {
                $dinamis = array(
                    'tanggal' => $this->hari,
                    'penduduk_id' => $this->input->post('id'),
                    'identitas_no' => $this->input->post('identitas_no'),
                    'agama' => $this->input->post('agama'),
                    'alamat' => $this->input->post('alamat'),
                    'kelurahan_id' => $kelurahan_id,
                    'pernikahan' => $this->input->post('pernikahan'),
                    'pendidikan_id' => ($this->input->post('pendidikan') != '') ? $this->input->post('pendidikan') : NULL,
                    'profesi_id' => 11,
                    'pekerjaan_id' => ($this->input->post('pekerjaan') != '') ? $this->input->post('pekerjaan') : NULL,
                );

                $this->demografi_pasien->create_dinamis_penduduk($dinamis);
            }
            // asuransi baru
            $asuArr = $this->input->post('id_asuransi');
            $polisArr = $this->input->post('no_polis');

            // asuransi lama
            $oldasuArr = $this->input->post('old_id_asuransi');
            $oldpolisArr = $this->input->post('old_no_polis');



            $asu_id = $this->input->post('id_asu');
            $asuAfter['penduduk_id'] = $this->input->post('id');
            if ($oldasuArr != null) {
                foreach ($oldasuArr as $key => $value) {
                    $asuBefore['id'][$key] = $asu_id[$key];
                    $asuBefore['asu_id'][$key] = $value;
                    $asuBefore['polis'][$key] = $oldpolisArr[$key];
                }
            }
            if ($asuArr != null) {
                foreach ($asuArr as $key => $value) {
                    $asuAfter['asu_id'][$key] = $value;
                    $asuAfter['polis'][$key] = $polisArr[$key];
                }
            }



            if (isset($asuBefore['asu_id'])) {
                $this->demografi_pasien->save_asuransi($asuBefore);
            }
            if (isset($asuAfter['asu_id'])) {
                $this->demografi_pasien->create_asuransi($asuAfter);
            }
            $this->detail($this->input->post('no_rm'));
        }
    }

    function hapus_asuransi($id_asu_peserta) {
        $this->db->delete('asuransi_kepesertaan', array('id' => $id_asu_peserta));
    }

    function new_pasien() {

        $data['title'] = "Data Demografi Pasien - Baru";
        $data['kelamin'] = $this->demografi_pasien->kelamin();
        $data['tgl_lahir'] = $this->demografi_pasien->usia();
        $this->load->view('demografi/new', $data);
    }

    // buat menyimpan data baru
    function find_similar_post() {

        $where = array(
            'nama' => $this->input->post('nama'),
            'tgl_lahir' => $this->input->post('tgl_lahir'),
            'kelamin' => $this->input->post('kelamin')
        );

        $query = $this->demografi_pasien->find_similiar($where);

        // bila data yang diinputkan ada???

        $data['pasien'] = $query;
        $this->load->view('demografi/find_new', $data);
    }

    function new_pelengkap() {
        // prosedure membuat nomor rekam medis



        $data['tgl_lahir'] = $this->input->post('tgl_lahir');
        $data['id_pdd'] = $this->input->post('id');
        if ($data['id_pdd'] != '') {
            $data['penduduk'] = $this->demografi_pasien->get_penduduk($data['id_pdd']);
            $data['asuransi'] = $this->demografi_pasien->get_asuransi_kepesertaan(null, $data['id_pdd'])->result();
            $data['total'] = $this->demografi_pasien->get_asuransi_kepesertaan(null, $data['id_pdd'])->num_rows();
        }



        $data['nama'] = $this->input->post('nama');
        $data['kelamin'] = $this->input->post('kelamin');
        $data['telp'] = $this->input->post('telp');
        $data['gol_darah'] = $this->demografi_pasien->gol_darah();
        $data['dob'] = $this->demografi_pasien->dob();
        $data['agama'] = $this->demografi_pasien->agama();
        $data['pendidikan'] = $this->demografi_pasien->pendidikan();
        $data['pernikahan'] = $this->demografi_pasien->stat_nikah();
        $data['pekerjaan'] = $this->demografi_pasien->pekerjaan();
        $data['title'] = "Data Demografi Pasien - Baru (Pelengkap)";

        $this->load->view('demografi/new_pelengkap', $data);
    }

    function new_post() {

        $ayah_id = ($this->input->post('hd_bapak') == "") ? NULL : $this->input->post('hd_bapak');
        $ibu_id = ($this->input->post('hd_ibu') == "") ? NULL : $this->input->post('hd_ibu');
        $kelurahan_id = ($this->input->post('hd_kelurahan') == "") ? NULL : $this->input->post('hd_kelurahan');
        $tmp_id = ($this->input->post('hd_lahir_tempat') == "") ? NULL : $this->input->post('hd_lahir_tempat');


        if ($ayah_id == NULL) {
            $ortu['nama'] = $this->input->post('nm_bapak');
            $ortu['gender'] = 'L';
            $this->db->insert('penduduk', $ortu);
            $ayah_id = $this->db->insert_id();
        }

        if ($ibu_id == NULL) {
            $ortu['nama'] = $this->input->post('nm_ibu');
            $ortu['gender'] = 'P';
            $this->db->insert('penduduk', $ortu);
            $ibu_id = $this->db->insert_id();
        }


        // entry penduduk dulu
        // baru entry pasien
        if ($this->input->post('id_pdd') == '') {


            $penduduk = array(
                'nama' => $this->input->post('nama'),
                'gender' => $this->input->post('gender'),
                'darah_gol' => $this->input->post('darah_gol'),
                'lahir_kabupaten_id' => $tmp_id, // link to kabupaten_id
                'lahir_tanggal' => datetopg($this->input->post('lahir_tanggal')),
                'telp' => $this->input->post('telp'),
                'ayah_penduduk_id' => $ayah_id,
                'ibu_penduduk_id' => $ibu_id,
                'unit_id' => null
            );


            $last_id = $this->demografi_pasien->create_penduduk($penduduk);

            $pasien = array(
                'registrasi_waktu' => $this->waktu,
                'kunjungan' => 0,
                'id' => $last_id,
                'is_cetak_kartu' => 0
            );

            $dinamis = array(
                'tanggal' => $this->hari,
                'penduduk_id' => $last_id,
                'identitas_no' => $this->input->post('identitas_no'),
                'agama' => $this->input->post('agama'),
                'alamat' => $this->input->post('alamat'),
                'kelurahan_id' => $kelurahan_id,
                'pernikahan' => $this->input->post('pernikahan'),
                'pendidikan_id' => ($this->input->post('pendidikan') != '') ? $this->input->post('pendidikan') : NULL,
                'profesi_id' => 11,
                'pekerjaan_id' => ($this->input->post('pekerjaan') != '') ? $this->input->post('pekerjaan') : NULL
            );

            $asuArr = $this->input->post('id_asuransi');
            $polisArr = $this->input->post('no_polis');

            $asuransi = array(
                'penduduk_id' => $last_id,
                'asu_id' => $asuArr,
                'polis' => $polisArr
            );
            $this->demografi_pasien->create_asuransi($asuransi);


            $no_rm = $this->demografi_pasien->create_pasien($pasien);
            $this->demografi_pasien->create_dinamis_penduduk($dinamis);
        } else {

            $pasien = array(
                'registrasi_waktu' => $this->waktu,
                'kunjungan' => 0,
                'id' => $this->input->post('id_pdd'),
                'is_cetak_kartu' => 0
            );
            $no_rm = $this->demografi_pasien->create_pasien($pasien);
        }

        $this->detail($no_rm);
    }

    function get_kelurahan() {
        $q = $_GET['q'];
        $rows = $this->demografi_pasien->get_kelurahan($q);
        die(json_encode($rows));
    }

    function get_kabupaten() {
        $q = $_GET['q'];
        $rows = $this->demografi_pasien->get_kabupaten($q);
        die(json_encode($rows));
    }

    function get_asuransi() {
        $q = $_GET['q'];
        $rows = $this->demografi_pasien->get_asuransi($q);
        die(json_encode($rows));
    }

    function get_penanggungjawab() {
        $q = $_GET['q'];
        $rows = $this->demografi_pasien->get_penanggungjawab($q);
        die(json_encode($rows));
    }

    function message($title, $pesan, $redirect) {
        $this->load->view('layout');
        $data['title'] = $title;
        $data['pesan'] = $pesan;
        $data['redirect'] = $redirect;
        $this->load->view('demografi/message', $data);
    }

    function list_pasien() {
        $this->load->view('layout');
        $data['pasien'] = $this->demografi_pasien->get();
        $data['title'] = "Data Demografi Pasien";
        $this->load->view('demografi/list', $data);
    }

    /* Fungsi - fungsi untuk mencari data pasien */

    function search() {
        $this->load->view('layout');
        $data['title'] = "Pencarian Demografi Pasien";
        $this->load->view('demografi/search', $data);
    }

    function search_by_no_rm_get() {
        $this->load->view('demografi/search-tab1');
    }

    function search_by_no_rm_post($no_rm) {
        // metode ajax
        if (!isset($no_rm)) {
            $no_rm = "";
        }
        $data['jumlah'] = '';
        $data['paging'] = '';
        $data['pasien'] = $this->demografi_pasien->get_by_no_rm($no_rm);
        $this->load->view('demografi/hasil_pencarian', $data);
    }

    function advance_search_get() {
        $data['kelamin'] = $this->demografi_pasien->kelamin();
        $this->load->view('demografi/search-tab2', $data);
    }

    function advance_search_post($page = null) {
        $limit = 10;
        $where = array(
            'nama' => $_GET['nama'],
            'addr_jln' => $_GET['alamat'],
            'nm_ibu' => $_GET['nm_ibu'],
            'kelamin' => $_GET['kelamin'],
            'umur' => $_GET['umur']
        );
        $start = ($page - 1) * $limit;
        $search = 'null';
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->demografi_pasien->advanced_search($limit, $start, $where);
        $data['pasien'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        $this->load->view('demografi/hasil_pencarian', $data);
    }

    /* Fungsi - fungsi untuk mencari data pasien */

    function detail($no_rm) {
        $this->load->view('layout');

        $data['pasien'] = $this->demografi_pasien->get_by_no_rm($no_rm);
        $data['asuransi'] = $this->demografi_pasien->get_asuransi_kepesertaan($no_rm, null)->result();
        $data['total'] = $this->demografi_pasien->get_asuransi_kepesertaan($no_rm, null)->num_rows();
        $data['title'] = "Detail Data Pasien";
        $this->load->view('demografi/detail', $data);
    }

}

?>
