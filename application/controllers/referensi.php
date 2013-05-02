<?php

class Referensi extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('html');
        $this->load->model('m_referensi');
        $this->load->model('m_inv_autocomplete');
    }

    function ganti_password() {
        $data['title'] = "Ganti Password";
        $data['user'] = $this->session->userdata('user');
        $this->load->view('ganti_password', $data);
    }

    function cek_password() {
        $pwd = md5($this->input->post('password'));

        $id = $this->session->userdata('id_user');

        $pwd_cek = $this->m_referensi->get_user_detail($id)->password;
        $status = false;
        if ($pwd == $pwd_cek) {
            $status = true;
        } else {
            $status = false;
        }
        die(json_encode(array('status' => $status)));
    }

    function simpan_password() {
        $pwd = md5($this->input->post('password'));

        $id = $this->session->userdata('id_user');

        $data = array(
            'password' => $pwd
        );
        $this->m_referensi->ubah_password($id, $data);
    }

    /* Referensi Tempat Tidur */

    function tempat_tidur() {
        $data['title'] = "Tempat Tidur";
        $data['bangsal'] = $this->m_referensi->unit_get_data();
        $data['bed'] = $this->m_referensi->bed_get_data();
        $data['kelas'] = $this->m_referensi->kelas_layanan_get_data();
        $this->load->view('referensi/tempat_tidur/bed', $data);
    }

    function get_tempat_tidur_list($page, $search) {
        $limit = 15;
        $q = '';
        if ($page == 'undefined') {
            $page = 1;
        }
        if ($search != 'null') {
            $q = " where t.id = '" . $search['id'] . "'";
        }

        $sql = "SELECT t.id,t.unit_id,t.kelas, t.no, u.nama, t.tarif FROM tt t
            join unit u on(t.unit_id = u.id) $q";
        $start = ($page - 1) * $limit;
        $lm = " limit $start , $limit";

        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['hasil'] = $this->db->query($sql . $lm)->result();
        $data['jumlah'] = $this->db->query($sql)->num_rows();
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        return $data;
    }

    function tempat_tidur_manage() {
        $act = $_GET['act'];
        $searchnull = 'null';

        if ($act == 'list_bed') {
            $page = $_GET['page'];
            $data = $this->get_tempat_tidur_list($page, $searchnull);
            $this->load->view('referensi/tempat_tidur/list_bed', $data);
        } else if ($act == 'delete_bed') {
            $page = $_GET['page'];
            $sql = "delete from tt where id = '" . $_GET['id'] . "'";
            $this->db->query($sql);

            $data = $this->get_tempat_tidur_list($page, $searchnull);
            if ($data['hasil'] == null) {
                $data = $this->get_tempat_tidur_list(1, $searchnull);
            }
            $this->load->view('referensi/tempat_tidur/list_bed', $data);
        } else if ($act == 'last_bed') {

            $sql = "SELECT max(no) as last FROM tt WHERE unit_id = '" . $_GET['unit'] . "' and kelas = '" . $_GET['kelas'] . "'";
            $row = $this->db->query($sql)->row();
            $last = $row->last;

            die(json_encode(array('last_no' => ++$last)));
        } else if ($act == 'add_bed') {
            $page = $_GET['page'];
            $unit = $this->input->post('bangsal');
            $kelas = $this->input->post('kelas');
            $no = $this->input->post('no_tt');
            $tarif = currencyToNumber($this->input->post('tarif'));
            $sql = "insert into tt values('','$unit','$kelas','$no','$tarif','0')";
            $this->db->query($sql);
            $search['id'] = $this->db->insert_id();
            $data = $this->get_tempat_tidur_list($page, $search);
            $this->load->view('referensi/tempat_tidur/list_bed', $data);
        } else if ($act == 'get_bed') {
            die($this->m_referensi->get_bed_data($_GET['id']));
        } else if ($act == 'edit_bed') {
            $page = $_GET['page'];
            $id = $this->input->post('hd_id');
            $unit = $this->input->post('bangsal');
            $kelas = $this->input->post('kelas');
            $no = $this->input->post('no_tt');
            $tarif = currencyToNumber($this->input->post('tarif'));
            $sql = "update tt set unit_id='" . $unit . "', kelas='" . $kelas . "', tarif='" . $tarif . "', no ='" . $no . "' where id = '" . $id . "' ";
            $search['id'] = $id;
            $this->db->query($sql);
            $data = $this->get_tempat_tidur_list($page, $search);
            $this->load->view('referensi/tempat_tidur/list_bed', $data);
        }
    }

    /* Referensi Tempat Tidur */

    /* Masterdata Unit */

    function master_unit() {
        $data['title'] = "Unit";
        $this->load->view('referensi/unit/unit', $data);
    }

    function master_unit_list() {
        $data['unit'] = $this->m_referensi->get_unit_data();
        $this->load->view('referensi/unit/list_unit', $data);
    }

    function master_unit_search() {
        $unit = $_GET['unit'];
        $count = $this->m_referensi->cek_unit($unit);

        if ($count->jumlah == 0) {
            $status = true;
        } else {
            $status = false;
        }
        die(json_encode(array('status' => $status)));
    }

    function master_unit_save() {
        $unit = $this->input->post('unit');
        $this->m_referensi->add_unit($unit);
        $this->master_unit_list();
    }

    function master_unit_delete() {
        $id = $_GET['id'];
        $this->m_referensi->delete_unit($id);
        $this->master_unit_list();
    }

    function master_unit_edit() {
        $id = $this->input->post('id_edit');
        $param = array(
            'id' => $this->input->post('id_edit'),
            'nama' => $this->input->post('unit_edit')
        );
        $this->m_referensi->edit_unit($param);
        $this->master_unit_list();
    }

    /* Masterdata Unit */

    /* Produk Asuransi */

    function produk_asuransi() {
        $data['title'] = "Produk Asuransi";
        $this->load->view('referensi/asuransi/produk-asuransi', $data);
    }

    function produk_asuransi_list($page) {
        $searchnull = 'null';
        $data = $this->produk_asuransi_data($page, $searchnull);
        $this->load->view('referensi/asuransi/list_produk_asuransi', $data);
    }

    function produk_asuransi_data($page, $search) {
        $limit = 15;

        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->get_produk_asuransi_data($limit, $start, $search);
        $data['jumlah'] = $query['jumlah'];
        $data['asuransi'] = $query['data'];

        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        return $data;
    }

    function produk_asuransi_add($page) {

        $dat = array(
            'id' => $this->input->post('id_produk'),
            'nama' => $this->input->post('nama'),
            'reimbursement' => $this->input->post('reimbursement'),
        );
        if ($this->input->post('id_ap') == '') {
            // Perusahaan belum ada
            $relasi = array(
                'nama' => $this->input->post('perusahaan'),
                'relasi_instansi_jenis_id' => 4
            );
            $dat['relasi_instansi_id'] = $this->m_referensi->add_relasi_instansi_data($relasi);
        } else {
            $dat['relasi_instansi_id'] = $this->input->post('id_ap');
        }
        $search['id'] = $this->m_referensi->add_produk_asuransi_data($dat);
        $data = $this->produk_asuransi_data($page, $search);
        $this->load->view('referensi/asuransi/list_produk_asuransi', $data);
    }

    function get_relasi_instansi() {
        $q = $_GET['q'];
        $rows = $this->m_referensi->relasi_instansi_data($q);
        die(json_encode($rows));
    }

    function get_produk_asuransi_last_no() {
        $no = $this->m_referensi->produk_asuransi_last_no();
        die(json_encode(array('no' => $no)));
    }

    function produk_asuransi_delete($page) {
        $id = $_GET['id'];
        $this->m_referensi->delete_produk_asuransi($id);
        $data = $this->produk_asuransi_data($page);
        if ($data['asuransi'] == null) {
            $data = $this->produk_asuransi_data(1);
        }
        $this->load->view('referensi/asuransi/list_produk_asuransi', $data);
    }

    function produk_asuransi_edit($page) {
        $dat = array(
            'id' => $this->input->post('id_produk'),
            'nama' => $this->input->post('nama'),
            'relasi_instansi_id' => $this->input->post('id_ap'),
            'reimbursement' => $this->input->post('reimbursement'),
        );
        $search['id'] = $dat['id'];
        $this->m_referensi->edit_produk_asuransi_data($dat);
        $data = $this->produk_asuransi_data($page, $search);
        $this->load->view('referensi/asuransi/list_produk_asuransi', $data);
    }

    function produk_asuransi_cek() {

        $prov = array(
            'nama' => $_GET['nama'],
            'relasi' => $_GET['relasi']
        );
        $cek = $this->m_referensi->produk_cek_data($prov);
        die(json_encode(array('status' => $cek)));
    }

    /* Produk Asuransi */


    /* Data Wilayah */

    /* Data Wilayah */

    function data_wilayah() {
        $data['title'] = "Data Wilayah";
        $data['provinsi'] = $this->m_referensi->provinsi_get_data(0, 15, 'null');
        $this->load->view('referensi/data_wilayah/data-wilayah', $data);
    }

    function data_provinsi() {
        $this->load->view('referensi/data_wilayah/data-provinsi');
    }

    function get_pro_list($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->provinsi_get_data($limit, $start, $search);
        $data['jumlah'] = $query['jumlah'];
        $data['provinsi'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        return $data;
    }

    function manage_provinsi($mode, $page = null) {
        $limit = 10;
        $add = array(
            'nama' => $this->input->post('provinsi'),
            'kode' => $this->input->post('kode')
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':

                $data = $this->get_pro_list($limit, $page, $searchnull);
                $this->load->view('referensi/data_wilayah/list_pro', $data);
                break;
            case 'add':
                $search = $this->m_referensi->provinsi_add_data($add);
                $data = $this->get_pro_list($limit, $page, $search);
                $this->load->view('referensi/data_wilayah/list_pro', $data);
                break;

            case 'edit':
                $add['id'] = $this->input->post('id');
                $this->m_referensi->provinsi_edit_data($add);
                $data = $this->get_pro_list($limit, $page, $add['id']);
                $this->load->view('referensi/data_wilayah/list_pro', $data);
                break;

            case 'delete':
                $id = $_GET['id'];
                $this->m_referensi->provinsi_delete_data($id);
                $data = $this->get_pro_list($limit, $page, $searchnull);
                if ($data['provinsi'] == null) {
                    $data = $this->get_pro_list($limit, $page - 1);
                }
                $this->load->view('referensi/data_wilayah/list_pro', $data);
                break;
            case 'cek':
                $prov = array(
                    'nama' => $_GET['provinsi']
                );
                $cek = $this->m_referensi->provinsi_cek_data($prov);
                die(json_encode(array('status' => $cek)));

                break;

            default:
                break;
        }
    }

    function get_provinsi() {
        $q = $_GET['q'];
        $rows = $this->m_referensi->provinsi_data($q);
        die(json_encode($rows));
    }

    function data_kabupaten() {
        $this->load->view('referensi/data_wilayah/data-kabupaten');
    }

    function get_kab_list($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->kabupaten_get_data($limit, $start, $search);
        $data['jumlah'] = $query['jumlah'];
        $data['kabupaten'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 2, $search);
        return $data;
    }

    function manage_kabupaten($mode, $page = null) {
        $limit = 15;
        $add = array(
            'nama' => $this->input->post('kabupaten'),
            'provinsi_id' => $this->input->post('idprovinsikab'),
            'kode' => $this->input->post('kodekab')
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $data = $this->get_kab_list($limit, $page, $searchnull);
                $this->load->view('referensi/data_wilayah/list_kab', $data);
                break;
            case 'add':
                $search = $this->m_referensi->kabupaten_add_data($add);
                $data = $this->get_kab_list($limit, $page, $search);
                $this->load->view('referensi/data_wilayah/list_kab', $data);
                break;

            case 'edit':
                $add['id'] = $this->input->post('id');
                $this->m_referensi->kabupaten_edit_data($add);
                $data = $this->get_kab_list($limit, $page, $add['id']);
                $this->load->view('referensi/data_wilayah/list_kab', $data);
                break;

            case 'delete':
                $id = $_GET['id'];
                $this->m_referensi->kabupaten_delete_data($id);
                $data = $this->get_kab_list($limit, $page, $searchnull);
                if ($data['kabupaten'] == null) {
                    $data = $this->get_kab_list($limit, 1, $searchnull);
                }
                $this->load->view('referensi/data_wilayah/list_kab', $data);
                break;
            case 'cek':
                $kab = array(
                    'nama' => $_GET['kabupaten'],
                    'provinsi_id' => $_GET['provid']
                );
                $cek = $this->m_referensi->kabupaten_cek_data($kab);
                die(json_encode(array('status' => $cek)));

                break;

            default:
                break;
        }
    }

    function get_kabupaten() {
        $q = $_GET['q'];
        $rows = $this->m_referensi->kabupaten_data($q);
        die(json_encode($rows));
    }

    function data_kecamatan() {
        $this->load->view('referensi/data_wilayah/data-kecamatan');
    }

    function get_kec_list($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->kecamatan_get_data($limit, $start, $search);
        $data['jumlah'] = $query['jumlah'];
        $data['kecamatan'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 3, $search);
        return $data;
    }

    function manage_kecamatan($mode, $page = null) {
        $limit = 15;
        $add = array(
            'nama' => $this->input->post('kecamatan'),
            'kabupaten_id' => $this->input->post('idkabupatenkec'),
            'kode' => $this->input->post('kodekec')
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $data = $this->get_kec_list($limit, $page, $searchnull);
                $this->load->view('referensi/data_wilayah/list_kec', $data);
                break;
            case 'add':
                $search = $this->m_referensi->kecamatan_add_data($add);
                $data = $this->get_kec_list($limit, $page, $search);
                $this->load->view('referensi/data_wilayah/list_kec', $data);
                break;

            case 'edit':
                $add['id'] = $this->input->post('id');
                $this->m_referensi->kecamatan_edit_data($add);
                $data = $this->get_kec_list($limit, $page, $add['id']);
                $this->load->view('referensi/data_wilayah/list_kec', $data);
                break;

            case 'delete':
                $id = $_GET['id'];
                $this->m_referensi->kecamatan_delete_data($id);
                $data = $this->get_kec_list($limit, $page, $searchnull);
                if ($data['kecamatan'] == null) {
                    $data = $this->get_kec_list($limit, 1, $searchnull);
                }
                $this->load->view('referensi/data_wilayah/list_kec', $data);
                break;
            case 'cek':
                $kab = array(
                    'nama' => $_GET['kecamatan'],
                    'kabupaten_id' => $_GET['kabid']
                );
                $cek = $this->m_referensi->kecamatan_cek_data($kab);
                die(json_encode(array('status' => $cek)));

                break;

            default:
                break;
        }
    }

    function get_kecamatan() {
        $q = $_GET['q'];
        $rows = $this->m_referensi->kecamatan_data($q);
        die(json_encode($rows));
    }

    function get_kelurahan() {
        $q = $_GET['q'];
        $rows = $this->m_referensi->kelurahan_data($q);
        die(json_encode($rows));
    }

    function data_kelurahan() {
        $this->load->view('referensi/data_wilayah/data-kelurahan');
    }

    function get_kel_list($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->kelurahan_get_data($limit, $start, $search);
        $data['jumlah'] = $query['jumlah'];
        $data['kelurahan'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 4, $search);
        return $data;
    }

    function manage_kelurahan($mode, $page = null) {
        $limit = 15;
        $add = array(
            'nama' => $this->input->post('kelurahan'),
            'kecamatan_id' => ($this->input->post('idkecamatankel') == '') ? NULL : $this->input->post('idkecamatankel'),
            'kode' => $this->input->post('kodekel')
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $data = $this->get_kel_list($limit, $page, $searchnull);
                $this->load->view('referensi/data_wilayah/list_kel', $data);
                break;
            case 'add':
                $search = $this->m_referensi->kelurahan_add_data($add);
                $data = $this->get_kel_list($limit, $page, $search);
                $this->load->view('referensi/data_wilayah/list_kel', $data);
                break;

            case 'edit':
                $add['id'] = $this->input->post('id');
                $this->m_referensi->kelurahan_edit_data($add);
                $data = $this->get_kel_list($limit, $page, $add['id']);
                $this->load->view('referensi/data_wilayah/list_kel', $data);
                break;

            case 'delete':
                $id = $_GET['id'];
                $this->m_referensi->kelurahan_delete_data($id);
                $data = $this->get_kel_list($limit, $page, $searchnull);
                if ($data['kelurahan'] == null) {
                    $data = $this->get_kel_list($limit, 1, $searchnull);
                }
                $this->load->view('referensi/data_wilayah/list_kel', $data);
                break;
            case 'cek':
                $kab = array(
                    'nama' => $_GET['kelurahan'],
                    'kecamatan_id' => $_GET['kecid']
                );
                $cek = $this->m_referensi->kelurahan_cek_data($kab);
                die(json_encode(array('status' => $cek)));

                break;

            default:
                break;
        }
    }

    /* Data Wilayah */
    /* Relasi Instansi */

    function instansi_relasi() {
        $data['title'] = 'Data Perusahaan';
        $data['jenis'] = null;
        $data['jenis'] = $this->m_referensi->relasi_instansi_jenis_get_data()->result();
        $jns = $this->m_referensi->relasi_instansi_jenis_get_data();
        $ddmenu = array();
        $ddmenu[''] = 'Pilih Jenis Perusahaan ..';
        foreach ($jns->result_array() as $rows) {
            $ddmenu[$rows['nama']] = $rows['nama'];
        }
        $data['jns_prsh'] = $ddmenu;
        $this->load->view('referensi/instansi_relasi/instansi', $data);
    }

    function get_instansi_list($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->instansi_get_data($limit, $start, $search);
        $data['jumlah'] = $query['jumlah'];
        $data['instansi'] = $query['data'];
        $str = 'null';
        if (($search != 'null') & isset($search['nama'])) {
            $str = $search['nama'];
        }
        $data['paging'] = '';
        if ($query['jumlah'] > 0) {
            $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $str);
        }
        return $data;
    }

    function manage_instansi($mode, $page = null) {
        $limit = 15;
        $add = array(
            'nama' => $this->input->post('nama'),
            'alamat' => preg_replace('/^\s+|\n|\r|\s+$/m', ' ',$this->input->post('alamat')),
            'kabupaten_id' => ($this->input->post('id_kelurahan') == '') ? NULL : $this->input->post('id_kelurahan'),
            'telp' => $this->input->post('telp'),
            'fax' => $this->input->post('fax'),
            'email' => $this->input->post('email'),
            'website' => $this->input->post('website'),
            'relasi_instansi_jenis_id' => ($this->input->post('jenis') == '') ? NULL : $this->input->post('jenis'),
            'diskon_penjualan' => $this->input->post('disk_penjualan')
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $search['nama'] = '';

                $param = $_GET['search'];
                if ($param != 'null') {
                    $search['nama'] = $param;
                    $data = $this->get_instansi_list($limit, $page, $search);
                } else {
                    $data = $this->get_instansi_list($limit, $page, $searchnull);
                }
                if ($param != 'null') {
                    $data['key'] = $param;
                }
                $this->load->view('referensi/instansi_relasi/list_instansi', $data);
                break;
            case 'add':
                $search['id'] = $this->m_referensi->instansi_add_data($add);
                $data = $this->get_instansi_list($limit, $page, $search);
                $this->load->view('referensi/instansi_relasi/list_instansi', $data);
                break;

            case 'edit':
                $add['id'] = $this->input->post('id');
                $search['id'] = $this->input->post('id');
                $this->m_referensi->instansi_edit_data($add);
                $data = $this->get_instansi_list($limit, $page, $search);
                $this->load->view('referensi/instansi_relasi/list_instansi', $data);
                break;

            case 'delete':
                $id = $_GET['id'];
                $this->m_referensi->instansi_delete_data($id);
                $data = $this->get_instansi_list($limit, $page, $searchnull);
                if ($data['instansi'] == null) {
                    $data = $this->get_kel_list($limit, 1, $searchnull);
                }
                $this->load->view('referensi/instansi_relasi/list_instansi', $data);
                break;
            case 'cek':
                $ins = array(
                    'instansi' => $_GET['instansi']
                );
                $cek = $this->m_referensi->instansi_cek_data($ins);
                die(json_encode(array('status' => $cek)));

                break;
            case 'search':
                $data['key'] = '';
                $search['nama'] = $this->input->post('nama');
                $data = $this->get_instansi_list($limit, $page, $search);
                $data['key'] = $search['nama'];
                $this->load->view('referensi/instansi_relasi/list_instansi', $data);
                break;

            default:
                break;
        }
    }

    /* Relasi Instansi */

    /* User Account */

    function user_account() {
        $data['title'] = 'User Account';
        $this->load->view('referensi/user_account/account', $data);
    }

    function get_user_list($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['jumlah'] = $this->m_referensi->count_user_data();
        $data['user'] = $this->m_referensi->user_get_data($limit, $start, $search);
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        return $data;
    }

    function manage_user($mode, $page = null) {
        $limit = 15;
        $add = array(
            'id' => $this->input->post('id_penduduk'),
            'username' => $this->input->post('username')
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $data = $this->get_user_list($limit, $page, $searchnull);
                $this->load->view('referensi/user_account/list_account', $data);
                break;
            case 'add':
                $add['password'] = md5($this->input->post('password'));
                $this->m_referensi->user_add_data($add);
                $search['id'] = $add['id'];
                $data = $this->get_user_list($limit, $page, $search);
                $this->load->view('referensi/user_account/list_account', $data);
                break;

            case 'edit':
                /*
                 * Butuh data unit, user
                 */
                $data['title'] = "User Account Privileges";
                $add['id'] = $_GET['id'];
                $data['unit'] = $this->m_referensi->unit_get_data();
                $data['user'] = $this->m_referensi->detail_user_data($add['id']);
                $this->load->view('referensi/user_account/privilege', $data);
                break;

            case 'delete':
                $id = $_GET['id'];
                $this->m_referensi->user_delete_data($id);
                $data = $this->get_user_list($limit, $page, $searchnull);
                if ($data['user'] == null) {
                    $data = $this->get_user_list($limit, 1, $searchnulls);
                }
                $this->load->view('referensi/user_account/list_account', $data);
                break;
            case 'cek':
                $kab = array(
                    'nama' => $_GET['user'],
                    'kecamatan_id' => $_GET['kecid'],
                    'kode' => $_GET['kode']
                );
                $cek = $this->m_referensi->user_cek_data($kab);
                die(json_encode(array('status' => $cek)));

                break;

            default:
                break;
        }
    }

    function get_user_privileges($id) {
        $data['user_priv'] = $this->m_referensi->user_privileges_data($id);
        $data['privilege'] = $this->m_referensi->privileges_get_data();
        return $data;
    }

    function manage_privileges($mode) {

        switch ($mode) {
            case 'list':
                $id = $_GET['id'];
                $data = $this->get_user_privileges($id);
                $this->load->view('referensi/user_account/list_privileges', $data);

                break;

            case 'add':
                $add = array(
                    'privileges' => $this->input->post('data'),
                    'penduduk_id' => ($this->input->post('id_user') == '') ? NULL : $this->input->post('id_user'),
                    'unit' => '1'
                );
                $this->m_referensi->privileges_edit_data($add);
                $data = $this->get_user_privileges($this->input->post('id_user'));
                $this->load->view('referensi/user_account/list_privileges', $data);

                break;

            default:
                break;
        }
    }

    /* User Account */

    /* Asuransi Kepesertaan */

    function asuransi() {
        $data['title'] = 'Asuransi Kepesertaan';
        $this->load->view('referensi/asuransi/asuransi', $data);
    }

    function manage_asuransi_kepesertaan($mode) {

        switch ($mode) {
            case 'list':
                $id = $_GET['id'];
                $data['asuransi'] = $this->m_referensi->asuransi_kepersertaan_get_data($id);
                $this->load->view('referensi/asuransi/list_asuransi', $data);

                break;

            case 'add':
                $add = array(
                    'id_produk' => $this->input->post('id_produk'), //array
                    'no' => $this->input->post('np'), //array
                    'id_penduduk' => $this->input->post('id_penduduk')
                );

                $this->m_referensi->asuransi_kepesertaan_add_data($add);
                break;

            case 'delete':
                $id = $_GET['id'];
                $this->m_referensi->asuransi_kepesertaan_delete_data($id);
                break;

            default:
                break;
        }
    }

    /* Asuransi Kepesertaan */

    /* Barang */

    function barang() {
        $data['title'] = 'Produk';
        $this->load->view('referensi/barang/barang', $data);
    }

    function barang_non_obat() {
        $query = $this->m_referensi->kategori_barang_get_data(null);
        $kat[''] = "Pilih Kategori";
        foreach ($query as $value) {
            if ($value->nama != 'Obat') {
                $kat[$value->id] = $value->nama;
            }
        }
        $data['kategori'] = $kat;
        $this->load->view('referensi/barang/non_obat', $data);
    }

    function barang_obat() {
        $data['satuan'] = $this->m_referensi->satuans_get_data(null);
        $data['sediaan'] = $this->m_referensi->sediaan_get_data(null);
        $data['admr'] = $this->m_referensi->adm_r_get_data(null);
        $data['perundangan'] = $this->m_referensi->perundangan_get_data(null);
        $this->load->view('referensi/barang/obat', $data);
    }

    function get_barang_list($limit, $page, $tab, $tipe, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['start'] = $start;
        if (isset($search['nama'])) {
            $query = $this->m_referensi->barang_get_data($limit, $start, $tipe, null, $search['nama'], $search['id_pabrik'], null, $search['indikasi'], $search['dosis'], $search['kandungan']);

            if (($search['nama'] != '') & ($search['id_pabrik'] != '')) {
                $str = $search['nama'] . "-" . $search['id_pabrik'] . "-" . $search['pabrik'];
            }
        } else if (isset($search['id'])) {
            $query = $this->m_referensi->barang_get_data($limit, $start, $tipe, $search['id'], null, null, null);
        } else {
            $query = $this->m_referensi->barang_get_data($limit, $start, $tipe, null, null, null, null);
        }

        $data['barang'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];

        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, $tab, $str);
        return $data;
    }

    function manage_barang_non($mode, $page = null) {
        $limit = 15;
        $add = array(
            'nama' => $this->input->post('nama'),
            'barang_kategori_id' => ($this->input->post('kategori') == '') ? NULL : $this->input->post('kategori'),
            'pabrik_relasi_instansi_id' => ($this->input->post('id_pabrik') == '') ? NULL : $this->input->post('id_pabrik'),
            'hna' => currencyToNumber($this->input->post('hna_nb'))
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $search['nama'] = '';
                $search['id_pabrik'] = '';
                $search['indikasi'] = '';
                $search['dosis'] = '';
                $search['kandungan'] = '';
                $param = $_GET['search'];
                if ($param != 'null') {
                    $a = explode("-", $param);
                    $search['nama'] = $a[0];
                    $search['id_pabrik'] = $a[1];
                    $search['pabrik'] = $a[2];
                }
                $cek = $this->db->query("select count(*) as jumlah from barang")->row();
                if ($cek->jumlah == 50) {
                    echo "<script>alert('Maximal barang yang dapat dimasukkan adalah 50')</script>";
                }
                $data = $this->get_barang_list($limit, $page, 1, 'Non Obat', $search);
                if ($param != 'null') {
                    $data['key'] = $search['nama'];
                    $data['pabrik'] = $search['pabrik'];
                }

                $this->load->view('referensi/barang/list_non_obat', $data);
                break;
            case 'add':
                $insert['barang'] = $add;
                $search['id'] = $this->m_referensi->barang_add_data($insert, 'non obat');
                $data = $this->get_barang_list($limit, $page, 1, 'Non Obat', $search);
                $cek = $this->db->query("select count(*) as jumlah from barang")->row();
                if ($cek->jumlah == 15) {
                    $data['max_limit'] = TRUE;
                }
                $this->load->view('referensi/barang/list_non_obat', $data);
                break;

            case 'edit':
                /*
                 * Butuh data unit, user
                 */

                $add['id'] = $this->input->post('id_barang');
                $update['barang'] = $add;
                $search['id'] = $this->input->post('id_barang');
                $this->m_referensi->barang_edit_data($update, 'non obat');
                $data = $this->get_barang_list($limit, $page, 1, 'Non Obat', $search);
                $this->load->view('referensi/barang/list_non_obat', $data);
                break;

            case 'delete':
                $id = $_GET['id'];
                $this->m_referensi->barang_delete_data($id, 'non obat');
                $data = $this->get_barang_list($limit, $page, 1, 'Non Obat', $searchnull);
                if ($data['barang'] == null) {
                    $data = $this->get_barang_list($limit, 1, 1, 'Non Obat', $searchnull);
                }
                $this->load->view('referensi/barang/list_non_obat', $data);
                break;
            case 'cek':
                $kab = array(
                    'nama' => $_GET['nama']
                );
                $cek = $this->m_referensi->barang_non_cek_data($kab);
                die(json_encode(array('status' => $cek)));

                break;

            case 'search':
                $data['key'] = '';
                $search['nama'] = $this->input->post('nama');
                $search['id_pabrik'] = $this->input->post('id_pabriks');
                $search['pabrik'] = $this->input->post('pabrik');
                $data = $this->get_barang_list($limit, $page, 1, 'Non Obat', $search);
                $data['key'] = $search['nama'];
                $data['pabrik'] = $search['pabrik'];
                $this->load->view('referensi/barang/list_non_obat', $data);
                break;

            default:
                break;
        }
    }

    function manage_barang_obat($mode, $page = null) {
        $limit = 15;
        $is_konsi = $this->input->post('konsinyasi');
        $add = array(
            'nama' => $this->input->post('nama'),
            'barang_kategori_id' => '1',
            'pabrik_relasi_instansi_id' => ($this->input->post('id_pabrik_obat') == '') ? NULL : $this->input->post('id_pabrik_obat'),
            'hna' => currencyToNumber($this->input->post('hna')),
            'stok_minimal' => $this->input->post('stokmin'),
            'is_konsinyasi' => (isset($is_konsi)?'1':'0')
        );
        $obat = array(
            'kekuatan' => ($this->input->post('kekuatan') != '') ? $this->input->post('kekuatan') : '1',
            'perundangan' => $this->input->post('perundangan'),
            'satuan_id' => ($this->input->post('satuan') == '') ? NULL : $this->input->post('satuan'),
            'adm_r' => $this->input->post('admr'),
            'sediaan_id' => ($this->input->post('sediaan') == '') ? NULL : $this->input->post('sediaan'),
            'generik' => $this->input->post('generik'),
            'indikasi' => $this->input->post('indikasi'),
            'dosis' => $this->input->post('dosis'),
            'kandungan' => $this->input->post('kandungan')
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $search['nama'] = '';
                $search['id_pabrik'] = '';
                $search['indikasi'] = '';
                $search['dosis'] = '';
                $search['kandungan'] = '';
                $param = $_GET['search'];
                if ($param != 'null') {
                    $a = explode("-", $param);
                    $search['nama'] = $a[0];
                    $search['id_pabrik'] = $a[1];
                    $search['pabrik'] = $a[2];
                }
                $data = $this->get_barang_list($limit, $page, 2, 'Obat', $search);
                if ($param != 'null') {
                    $data['key'] = $search['nama'];
                    $data['pabrik'] = $search['pabrik'];
                }

                $this->load->view('referensi/barang/list_obat', $data);
                break;
            case 'add':
                $insert['barang'] = $add;
                $insert['obat'] = $obat;
                $search['id'] = $this->m_referensi->barang_add_data($insert, 'Obat');
                
                $data = $this->get_barang_list($limit, $page, 2, 'Obat', $search);
                $this->load->view('referensi/barang/list_obat', $data);
                break;

            case 'edit':
                $add['id'] = $this->input->post('id_obat');
                $obat['id'] = $this->input->post('id_obat');
                $search['id'] = $this->input->post('id_obat');
                $update['barang'] = $add;
                $update['obat'] = $obat;
                $this->m_referensi->barang_edit_data($update, 'Obat');
                $data = $this->get_barang_list($limit, $page, 2, 'Obat', $search);
                $this->load->view('referensi/barang/list_obat', $data);
                break;

            case 'delete':
                $id = $_GET['id'];
                $this->m_referensi->barang_delete_data($id, 'obat');
                $data = $this->get_barang_list($limit, $page, 2, 'Obat', $searchnull);
                if ($data['barang'] == null) {
                    $data = $this->get_barang_list($limit, 1, 2, 'Obat', $searchnull);
                }
                $this->load->view('referensi/barang/list_obat', $data);
                break;
            case 'cek':
                $get = array(
                    'nama' => $_GET['nama']
                );
                $cek = $this->m_referensi->obat_cek_data($get);
                die(json_encode(array('status' => $cek)));
                break;

            case 'search':
                $data['key'] = '';
                $search['nama'] = $this->input->post('nama');
                $search['id_pabrik'] = $this->input->post('id_pabriks_obat');
                $search['pabrik'] = $this->input->post('pabrik');
                $search['indikasi'] = $this->input->post('indikasi_obat');
                $search['dosis'] = $this->input->post('dosis_obat');
                $search['kandungan'] = $this->input->post('kandungan');
                $data = $this->get_barang_list($limit, $page, 2, 'Obat', $search);
                $data['key'] = $search['nama'] .' '. $search['pabrik'] .' '. $search['indikasi'] .' '. $search['dosis'].' '.$search['kandungan'];
                $data['pabrik'] = $search['pabrik'];
                $this->load->view('referensi/barang/list_obat', $data);
                break;

            default:
                break;
        }
    }

    /* Barang */


    /* Tarif Jasa */

    function tarif_jasa() {
        $data['title'] = 'Tarif Jasa';
        $this->load->view('referensi/tarif_jasa/tarif', $data);
    }

    function get_tarif_list($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $str = '';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->tarif_get_data($limit, $start, $search);
        $data['jumlah'] = $query['jumlah'];
        $data['tarif'] = $query['data'];
        if (isset($search['nama'])) {
            $str = $search['nama'];
        }
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $str);
        return $data;
    }

    function get_last_id($tabel, $id) {
        return die(json_encode(array('last_id' => get_last_id($tabel, $id))));
    }

    function manage_tarif($mode, $page = null) {
        $limit = 15;
        $add = array(
            'id' => $this->input->post('id_tarif'),
            'layanan_id' => ($this->input->post('id_layanan') == '') ? NULL : $this->input->post('id_layanan'),
            'tarif_kategori_id' => ($this->input->post('id_kategori') == '') ? NULL : $this->input->post('id_kategori'),
            'profesi_layanan_tindakan_jasa_total' => $this->input->post('jp'),
            'uc' => $this->input->post('unit_cost'),
            'nominal' => currencyToNumber($this->input->post('nominals')),
            'js' => currencyToNumber($this->input->post('js')),
            'rs_tindakan_jasa' => currencyToNumber($this->input->post('js_rs')),
            'bhp' => currencyToNumber($this->input->post('bhp')),
            'profit_margin' => $this->input->post('margin')
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $q = $_GET['search'];
                if ($q != 'null') {
                    $search['nama'] = $q;
                    $data = $this->get_tarif_list($limit, $page, $search);
                    $data['key'] = $search['nama'];
                } else {
                    $data = $this->get_tarif_list($limit, $page, $searchnull);
                }

                $this->load->view('referensi/tarif_jasa/list_tarif', $data);
                break;
            case 'add':
                $search['id'] = $this->m_referensi->tarif_add_data($add);
                $data = $this->get_tarif_list($limit, $page, $search);
                $this->load->view('referensi/tarif_jasa/list_tarif', $data);
                break;
            case 'edit':
                $this->m_referensi->tarif_edit_data($add);
                $search['id'] = $add['id'];
                $data = $this->get_tarif_list($limit, $page, $search);
                $this->load->view('referensi/tarif_jasa/list_tarif', $data);
                break;

            case 'delete':
                $id = $_GET['id'];
                $this->m_referensi->tarif_delete_data($id);
                $data = $this->get_tarif_list($limit, $page, $searchnull);
                if ($data['tarif'] == null) {
                    $data = $this->get_packing_list($limit, 1, $searchnull);
                }
                $this->load->view('referensi/tarif_jasa/list_tarif', $data);

                break;
            case 'cek':
                $ins = array(
                    'layanan' => $_GET['layanan'],
                    'kategori' => $_GET['kategori'],
                    'js' => $_GET['js'],
                    'js_rs' => $_GET['js_rs'],
                    'jp' => $_GET['jp'],
                    'bhp' => $_GET['bhp'],
                    'uc' => $_GET['uc'],
                    'margin' => $_GET['margin'],
                    'nominal' => $_GET['nominal']
                );
                $cek = $this->m_referensi->tarif_cek_data($ins);
                die(json_encode(array('status' => $cek)));
                break;
            case 'search':
                $search['nama'] = $this->input->post('nama_layanan');
                $data = $this->get_tarif_list($limit, 1, $search);
                $data['key'] = '';
                $data['key'] = $search['nama'];
                $this->load->view('referensi/tarif_jasa/list_tarif', $data);
                break;

            default:
                break;
        }
    }

    function get_jasa_profesi() {
        $id = $_GET['id_layanan'];
        $jp = $this->m_referensi->get_jasa_profesi($id);
        die(json_encode(array('total_jp' => $jp->total)));
    }

    /* Tarif Jasa */



    /* Packing Barang */

    function packing_barang() {
        $data['satuan'] = $this->m_referensi->satuan_get_data(null);
        $data['kemasan'] = $this->m_referensi->satuan_get_data(null);
        $data['title'] = 'Kemasan Barang';
        $this->load->view('referensi/barang/packing', $data);
    }

    function get_packing_list($limit, $page, $id, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['start'] = $start;
        
        $query = $this->m_referensi->packing_get_data($limit, $start, $id, $search);
        $data['jumlah'] = $query['jumlah'];
        $data['packing'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        return $data;
    }

    function manage_packing($mode, $page = null) {
        $limit = 15;
        $add = array(
            'barcode' => $this->input->post('barcode'),
            'barang_id' => ($this->input->post('id_barang') == '') ? NULL : $this->input->post('id_barang'),
            'terbesar_satuan_id' => ($this->input->post('kemasan') == '') ? NULL : $this->input->post('kemasan'),
            'isi' => $this->input->post('isi'),
            'terkecil_satuan_id' => ($this->input->post('satuan') == '') ? NULL : $this->input->post('satuan')
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $searchnull = $_GET['search'];
                $data = $this->get_packing_list($limit, $page, 'null', $searchnull);
                $this->load->view('referensi/barang/list_packing', $data);
                break;
            case 'add':
                $search = $this->m_referensi->packing_add_data($add);
                $data = $this->get_packing_list($limit, $page, $search, $searchnull);
                $this->load->view('referensi/barang/list_packing', $data);
                break;

            case 'edit':
                $add['id'] = $this->input->post('id');
                $this->m_referensi->packing_edit_data($add);
                $data = $this->get_packing_list($limit, $page, $add['id'], $searchnull);
                $this->load->view('referensi/barang/list_packing', $data);
                break;

            case 'delete':
                $id = $_GET['id'];
                $this->m_referensi->packing_delete_data($id);
                $data = $this->get_packing_list($limit, $page, 'null', $searchnull);
                if ($data['packing'] == null) {
                    $data = $this->get_packing_list($limit, 1, 'null', $searchnull);
                }
                $this->load->view('referensi/barang/list_packing', $data);

                break;
            case 'cek':
                $ins = array(
                    'barcode' => $_GET['barcode'],
                    'id_barang' => $_GET['id_barang'],
                    'kemasan' => $_GET['kemasan'],
                    'isi' => $_GET['isi'],
                    'satuan' => $_GET['satuan']
                );
                $cek = $this->m_referensi->packing_cek_data($ins);
                die(json_encode(array('status' => $cek)));
                break;

            case 'search':

                $search = $this->input->post('barang_cari');
                $data = $this->get_packing_list($limit, 1, 'null', $search);
                $data['key'] = '';
                $data['key'] = $search;
                $this->load->view('referensi/barang/list_packing', $data);
                break;

            default:
                break;
        }
    }

    function cetak_barcode() {
        $data['jml'] = $_GET['jumlah'];
        $data['barcode'] = $_GET['barcode'];
        $this->load->view('barcode', $data);
    }

    /* Packing Barang */

    /* Layanan */

    function layanan() {

        $data['title'] = 'Layanan';
        $data['kelas'] = $this->m_referensi->kelas_layanan_get_data();
        $data['bobot'] = $this->m_referensi->bobot_layanan_get_data();
        $this->load->view('referensi/layanan/layanan', $data);
    }

    function get_layanan_list($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->layanan_get_data($limit, $start, $search);
        $data['jumlah'] = $query['jumlah'];
        $data['layanan'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        return $data;
    }

    function manage_layanan($mode, $page = null) {
        $limit = 15;
        $bobot = $this->input->post('bobot');
        $add = array(
            'nama' => $this->input->post('nama'),
            'bobot' => ($bobot == '')?NULL:$bobot,
            'nominal' => currencyToNumber($this->input->post('nominal'))
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $data = $this->get_layanan_list($limit, $page, $searchnull);
                $this->load->view('referensi/layanan/list_layanan', $data);
                break;
            case 'add':
                $search = $this->m_referensi->layanan_add_data($add);
                $data = $this->get_layanan_list($limit, $page, $search);
                $this->load->view('referensi/layanan/list_layanan', $data);
                break;

            case 'edit':
                $add['id'] = $this->input->post('id_layanan');
                $this->m_referensi->layanan_edit_data($add);
                $data = $this->get_layanan_list($limit, $page, $add['id']);
                $this->load->view('referensi/layanan/list_layanan', $data);
                break;

            case 'delete':
                $id = $_GET['id'];
                $this->m_referensi->layanan_delete_data($id);
                $data = $this->get_layanan_list($limit, $page, $searchnull);
                if ($data['layanan'] == null) {
                    $data = $this->get_layanan_list($limit, 1, $searchnull);
                }
                $this->load->view('referensi/layanan/list_layanan', $data);

                break;
            case 'cek':
                $ins = array(
                    'layanan' => $_GET['layanan']
                );
                $cek = $this->m_referensi->layanan_cek_data($ins);
                die(json_encode(array('status' => $cek)));

                break;

            default:
                break;
        }
    }

    /* Layanan */


    /* Penduduk */

    function penduduk() {
        $this->load->model('demografi_pasien');
        $data['title'] = 'Penduduk';
        $data['gol_darah'] = $this->demografi_pasien->gol_darah();
        $data['pendidikan'] = $this->demografi_pasien->pendidikan();
        $data['pernikahan'] = $this->demografi_pasien->stat_nikah();
        $data['pekerjaan'] = $this->demografi_pasien->pekerjaan();
        $data['profesi'] = $this->m_referensi->profesi_get_data();
        $data['posisi'] = $this->m_referensi->posisi_keluarga_get_data();
        $data['jabatan'] = $this->m_referensi->jabatan_get_data();
        $this->load->view('referensi/penduduk/penduduk', $data);
    }

    function get_penduduk_list($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;

        if ($search != 'null') {
            $query = $this->m_referensi->penduduk_get_data($limit, $start, $search);
        } else {
            $query = $this->m_referensi->penduduk_get_data($limit, $start, null);
        }
        $data['penduduk'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];

        if (($search != 'null') & isset($search['nama'])) {
            $str = $search['nama']
                    . "-" . $search['alamat']
                    . "-" . $search['telp']
                    . "-" . $search['kabupaten']
                    . "-" . $search['gender']
                    . "-" . $search['gol_darah']
                    . "-" . $search['tgl_lahir'];
        }
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $str);
        return $data;
    }

    function manage_penduduk($mode, $page = null) {
        $limit = 15;
        $pdd = array(
            'id' => $this->input->post('id_penduduk'),
            'nama' => $this->input->post('nama'),
            'lahir_kabupaten_id' => ($this->input->post('id_kabupaten') == "") ? NULL : $this->input->post('id_kabupaten'),
            'gender' => $this->input->post('kelamin'),
            'telp' => $this->input->post('telp'),
            'darah_gol' => $this->input->post('gol_darah'),
            'lahir_tanggal' => date2mysql($this->input->post('tgl_lahir')),
            'member' => $this->input->post('disc')
        );

        $dinamis = array(
            'penduduk_id' => $this->input->post('id_penduduk'),
            'tanggal' => date('Y-m-d'),
            'alamat' => $this->input->post('alamat'),
            'kabupaten_id' => $this->input->post('id_kabupaten_alamat'),
            'identitas_no' => $this->input->post('noid'),
            'pernikahan' => $this->input->post('pernikahan'),
            'pendidikan_id' => ($this->input->post('pendidikan') == '') ? NULL : $this->input->post('pendidikan'),
            'profesi_id' => ($this->input->post('profesi') == '') ? NULL : $this->input->post('profesi'),
            'str_no' => $this->input->post('nostr'),
            'sip_no' => $this->input->post('nosip'),
            'pekerjaan_id' => ($this->input->post('pekerjaan') == '') ? NULL : $this->input->post('pekerjaan'),
            'kerja_izin_surat_no' => $this->input->post('nosik'),
            'jabatan' => $this->input->post('jabatan')
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':

                $search = array(
                    'nama' => '',
                    'alamat' => '',
                    'telp' => '',
                    'kabupaten' => '',
                    'gender' => '',
                    'gol_darah' => '',
                    'tgl_lahir' => ''
                );
                $param = $_GET['search'];
                if ($param != 'null') {
                    $a = explode("-", $param);
                    $search['nama'] = $a[0];
                    $search['alamat'] = $a[1];
                    $search['telp'] = $a[2];
                    $search['kabupaten'] = $a[3];
                    $search['gender'] = $a[4];
                    $search['gol_darah'] = $a[5];
                    $search['tgl_lahir'] = $a[6];
                    $data = $this->get_penduduk_list($limit, $page, $search);
                } else {
                    $data = $this->get_penduduk_list($limit, $page, $searchnull);
                }

                if ($param != 'null') {
                    $data['key'] = $search['nama'];
                }
                $this->load->view('referensi/penduduk/list_penduduk', $data);
                break;
            case 'add':
                $add['penduduk'] = $pdd;
                $add['dinamis'] = $dinamis;
                $search['id'] = $this->m_referensi->penduduk_add_data($add);
                $data = $this->get_penduduk_list($limit, $page, $search);
                $this->load->view('referensi/penduduk/list_penduduk', $data);
                break;

            case 'edit':
                $dinamis['alamat_lama'] = $this->input->post('alamat_lama');
                $edit['penduduk'] = $pdd;
                $edit['dinamis'] = $dinamis;
                $search['id'] = $pdd['id'];
                $this->m_referensi->penduduk_edit_data($edit);
                $data = $this->get_penduduk_list($limit, $page, $search);
                $this->load->view('referensi/penduduk/list_penduduk', $data);
                break;

            case 'delete':
                $id = $_GET['id'];
                $this->m_referensi->penduduk_delete_data($id);
                $data = $this->get_penduduk_list($limit, $page, $searchnull);
                if ($data['penduduk'] == null) {
                    $data = $this->get_penduduk_list($limit, 1, $searchnull);
                }
                $this->load->view('referensi/penduduk/list_penduduk', $data);

                break;
            case 'cek':
                $ins = array(
                    'penduduk' => $_GET['nama'],
                );
                $cek = $this->m_referensi->penduduk_cek_data($ins);
                die(json_encode(array('status' => $cek)));

                break;

            case 'search':

                $search = array(
                    'nama' => $this->input->post('nama_cari'),
                    'alamat' => $this->input->post('alamat_cari'),
                    'telp' => $this->input->post('telp_cari'),
                    'kabupaten' => $this->input->post('id_kabupaten_cari'),
                    'gender' => $this->input->post('kelamin_cari'),
                    'gol_darah' => $this->input->post('gol_darah_cari'),
                    'tgl_lahir' => $this->input->post('tgl_lahir_cari'),
                    'kategori' => $this->input->post('kategori')
                );


                $data = $this->get_penduduk_list($limit, $page, $search);
                $data['key'] = '';
                $data['key'] = $search['nama'];
                $data['alamat'] = $search['alamat'];
                $data['telp'] = $search['telp'];
                $data['kabupaten'] = $this->input->post('kabupaten_cari');
                $data['gender'] = $search['gender'];
                $data['gol_darah'] = $search['gol_darah'];
                $data['tgl_lahir'] = $search['tgl_lahir'];
                $data['kategori'] = $search['kategori'];
                
                $this->load->view('referensi/penduduk/list_penduduk', $data);
                break;

            case 'edit_dinamis':
                $din = array(
                    'penduduk_id' => $this->input->post('id_dinamis'),
                    'tanggal' => date('Y-m-d'),
                    'identitas_no' => $this->input->post('noid'),
                    'alamat' => $this->input->post('alamat'),
                    'kelurahan_id' => ($this->input->post('id_kelurahan') == '') ? NULL : $this->input->post('id_kelurahan'),
                    'pernikahan' => $this->input->post('pernikahan'),
                    'kk_no' => $this->input->post('nokk'),
                    'posisi' => $this->input->post('posisi'),
                    'pendidikan_id' => ($this->input->post('pendidikan') == '') ? NULL : $this->input->post('pendidikan'),
                    'profesi_id' => ($this->input->post('profesi') == '') ? NULL : $this->input->post('profesi'),
                    'str_no' => $this->input->post('nostr'),
                    'sip_no' => $this->input->post('nosip'),
                    'pekerjaan_id' => ($this->input->post('pekerjaan') == '') ? NULL : $this->input->post('pekerjaan'),
                    'kerja_izin_surat_no' => $this->input->post('nosik'),
                    'jabatan' => $this->input->post('jabatan')
                );

                $ret = $this->m_referensi->dinamis_penduduk_edit_data($din);
                die(json_encode(array('id' => $ret['id'])));
                break;

            default:
                break;
        }
    }

    function dinamis_penduduk_get_data() {
        /*
         * untuk mengambil data dinamis penduduk yg terakhir
         */
        $id = $_GET['id'];
        $id_dp = $_GET['id_dp'];
        $query = $this->m_referensi->penduduk_dinamis_get_data($id, $id_dp);
        die(json_encode($query));
    }

    function dinamis_penduduk_get_list() {
        /*
         * unutk mengambil histori data dinamis penduduk 
         */
        $id = $_GET['id'];
        $query['dinamis'] = $this->m_referensi->penduduk_dinamis_get_data($id, null);
        $this->load->view('referensi/penduduk/list_dinamis', $query);
    }

    function harga_jual() {
        $data['title'] = 'Administrasi Produk';
        $data['list_data'] = $this->m_referensi->harga_jual_load_data()->result();
        $this->load->view('referensi/harga_jual/harga-jual', $data);
    }

    function harga_jual_load() {
        $data['title'] = 'Administrasi Produk';
        $pb = ($_GET['pb'] != 'undefined')?$_GET['pb']:'';
        $data['list_data'] = $this->m_referensi->harga_jual_load_data($pb)->result();
        $this->load->view('referensi/harga_jual/harga-jual', $data);
    }

    function harga_jual_update() {
        $data['title'] = 'Update Harga Jual';
        $id = implode(',', $this->input->post('pb'));
        $data['list_data'] = $this->m_referensi->harga_jual_load_data_update($id)->result();
        $this->load->view('referensi/harga_jual/harga-jual-update-table', $data);
    }

    function harga_jual_update_save() {
        $data = $this->m_referensi->harga_jual_update_save();
        die(json_encode($data));
    }

    function setting_kas() {
        $data['title'] = 'Posisi Kas Awal';
        $this->load->view('referensi/setting-kas', $data);
    }

    function setting_kas_save() {
        $data = $this->m_referensi->setting_kas_save();
        die(json_encode($data));
    }

    function layanan_profesi() {
        $data['title'] = 'Jasa Tindakan Layanan Profesi';
        $this->load->view('referensi/layanan/adm-layanan-profesi', $data);
    }

    function layanan_profesi_save() {
        $data = $this->m_referensi->layanan_profesi_save();
        die(json_encode($data));
    }

    function layanan_profesi_delete($id_tindakan, $id_layanan) {
        $this->m_referensi->layanan_profesi_delete($id_tindakan);
        $this->layanan_profesi_load_table($id_layanan);
    }

    function layanan_profesi_load_table($id_layanan) {
        $data['title'] = 'Jasa Tindakan Layanan Profesi';
        $data['list_data'] = $this->m_inv_autocomplete->adm_layanan_profesi($id_layanan)->result();
        $this->load->view('referensi/layanan/adm-layanan-profesi-table', $data);
    }

    function load_data_profesi() {
        $q = $_GET['q'];
        $data = $this->m_inv_autocomplete->load_data_profesi($q)->result();
        die(json_encode($data));
    }

}

?>
