<?php

class Kepegawaian extends CI_Controller {

    public $limit = null;

    function __construct() {
        parent::__construct();
        //is_logged_in();
        $this->limit = 15;
        $this->load->helper('url');
        $this->load->model('m_kepegawaian');
    }

    function get_last_id($tabel, $id) {
        return die(json_encode(array('last_id' => get_last_id($tabel, $id))));
    }

    /* Jenis Jurusan Kualifikasi Pendidikan */

    function jenis_kualifikasi() {
        $data['title'] = "Jenis Jurusan Kualifikasi Pendidikan";
        $this->load->view('kepegawaian/jenis_jurusan', $data);
    }

    function get_jenis_list($page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $str = 'null';
        $start = ($page - 1) * $this->limit;
        $data['page'] = $page;
        $data['limit'] = $this->limit;

        $query = $this->m_kepegawaian->jenis_get_data($this->limit, $start, $search);
        $data['jenis'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];

        if (isset($search['nama'])) {
            $str = $search['nama'] . "-" . $search['nakes'];
        }

        $data['paging'] = paging_ajax($data['jumlah'], $this->limit, $page, 1, $str);
        return $data;
    }

    function manage_jenis($mode, $page) {
        $jenis = array(
            'id' => $this->input->post('id'),
            'nama' => $this->input->post('nama'),
            'nakes' => $this->input->post('nakes'),
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $cari = $_GET['search'];

                if ($cari != 'null') {
                    $param = explode('-', $cari);

                    $search['nama'] = $param[0];
                    $search['nakes'] = $param[1];
                    $data = $this->get_jenis_list($page, $search);
                    $data['key'] = $param[0];
                    $data['nakes'] = $param[1];
                } else {
                    $data = $this->get_jenis_list($page, $searchnull);
                }
                $this->load->view('kepegawaian/jenis_jurusan_list', $data);
                break;

            case 'post':
                // untuk add or edit
                if ($jenis['id'] == '') {
                    //add
                    $search['id'] = $this->m_kepegawaian->jenis_add_data($jenis);
                    $data = $this->get_jenis_list($page, $search);
                    $this->load->view('kepegawaian/jenis_jurusan_list', $data);
                } else {
                    $search['id'] = $jenis['id'];
                    $this->m_kepegawaian->jenis_edit_data($jenis);
                    $data = $this->get_jenis_list($page, $search);
                    $this->load->view('kepegawaian/jenis_jurusan_list', $data);
                }
                break;

            case 'delete':
                $id = $_GET['id'];
                $this->m_kepegawaian->jenis_delete_data($id);
                $data = $this->get_jenis_list($page, $searchnull);
                if ($data['jenis'] == null) {
                    $data = $this->get_jenis_list(1, $searchnull);
                }
                $this->load->view('kepegawaian/jenis_jurusan_list', $data);

                break;

            case 'search':
                $search = array(
                    'nama' => $this->input->post('nama'),
                    'nakes' => $this->input->post('nakes')
                );

                $data = $this->get_jenis_list($page, $search);
                $data['key'] = $search['nama'];
                $data['nakes'] = $search['nakes'];
                $this->load->view('kepegawaian/jenis_jurusan_list', $data);

                break;

            case 'cek':
                $cek = array(
                    'nama' => $this->input->post('nama'),
                    'nakes' => $this->input->post('nakes')
                );
                $data = $this->m_kepegawaian->jenis_cek_data($cek);
                die(json_encode(array('status' => $data)));

                break;

            default:
                break;
        }
    }

    function get_jenis() {
        $q = $_GET['q'];
        $data = $this->m_kepegawaian->load_data_jenis($q)->result();
        die(json_encode($data));
    }

    /* Jenis Jurusan Kualifikasi Pendidikan */

    /* Kualifikasi Pendidikan */

    function kualifikasi_pendidikan() {
        $data['title'] = "Kualifikasi Pendidikan";
        $this->load->view('kepegawaian/kualifikasi_pendidikan', $data);
    }

    function get_pendidikan_list($page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $str = 'null';
        $start = ($page - 1) * $this->limit;
        $data['page'] = $page;
        $data['limit'] = $this->limit;

        $query = $this->m_kepegawaian->pendidikan_get_data($this->limit, $start, $search);
        $data['pendidikan'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];

        if (isset($search['nama'])) {
            $str = $search['nama'];
        }

        $data['paging'] = paging_ajax($data['jumlah'], $this->limit, $page, 1, $str);
        return $data;
    }

    function manage_pendidikan($mode, $page) {
        $pendidikan = array(
            'id' => $this->input->post('id'),
            'nama' => $this->input->post('nama'),
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $cari = $_GET['search'];

                if ($cari != 'null') {
                    $search['nama'] = $cari;
                    $data = $this->get_pendidikan_list($page, $search);
                    $data['key'] = $cari;
                } else {
                    $data = $this->get_pendidikan_list($page, $searchnull);
                }

                $this->load->view('kepegawaian/kualifikasi_pendidikan_list', $data);
                break;

            case 'post':
                // untuk add or edit
                if ($pendidikan['id'] == '') {
                    //add
                    $search['id'] = $this->m_kepegawaian->pendidikan_add_data($pendidikan);
                    $data = $this->get_pendidikan_list($page, $search);
                    $this->load->view('kepegawaian/kualifikasi_pendidikan_list', $data);
                } else {
                    $search['id'] = $pendidikan['id'];
                    $this->m_kepegawaian->pendidikan_edit_data($pendidikan);
                    $data = $this->get_pendidikan_list($page, $search);
                    $this->load->view('kepegawaian/kualifikasi_pendidikan_list', $data);
                }
                break;

            case 'delete':
                $id = $_GET['id'];
                $this->m_kepegawaian->pendidikan_delete_data($id);
                $data = $this->get_pendidikan_list($page, $searchnull);
                if ($data['pendidikan'] == null) {
                    $data = $this->get_pendidikan_list(1, $searchnull);
                }
                $this->load->view('kepegawaian/kualifikasi_pendidikan_list', $data);

                break;

            case 'search':
                $search = array(
                    'nama' => $this->input->post('nama')
                );

                $data = $this->get_pendidikan_list($page, $search);
                $data['key'] = $search['nama'];
                $this->load->view('kepegawaian/kualifikasi_pendidikan_list', $data);

                break;

            case 'cek':
                $cek = array(
                    'nama' => $this->input->post('nama')
                );
                $data = $this->m_kepegawaian->pendidikan_cek_data($cek);
                die(json_encode(array('status' => $data)));

                break;

            default:
                break;
        }
    }

    /* Kualifikasi Pendidikan */


    /* Jurusan Kualifikasi Pendidikan */

    function jurusan() {
        $data['title'] = "Jurusan Kualifikasi Pendidikan";
        $this->load->view('kepegawaian/jurusan', $data);
    }

    function get_jurusan_list($page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $str = 'null';
        $start = ($page - 1) * $this->limit;
        $data['page'] = $page;
        $data['limit'] = $this->limit;

        $query = $this->m_kepegawaian->jurusan_get_data($this->limit, $start, $search);
        $data['jurusan'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        if (isset($search['nama']) | isset($search['jenis'])) {
            $str = $search['nama'] . '-' . $search['jenis'] . "-" . $search['nm_jenis'];
        }

        $data['paging'] = paging_ajax($data['jumlah'], $this->limit, $page, 1, $str);
        return $data;
    }

    function manage_jurusan($mode, $page) {
        $jurusan = array(
            'id' => $this->input->post('id'),
            'nama' => $this->input->post('nama'),
            'id_jenis_jurusan_kualifikasi_pendidikan' => ($this->input->post('id_jenis') != '') ? $this->input->post('id_jenis') : 'NULL'
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $cari = $_GET['search'];

                if ($cari != 'null') {
                    $param = explode("-", $cari);
                    $search['nama'] = $param[0];
                    $search['jenis'] = $param[1];
                    $search['nm_jenis'] = $param[2];
                    $data = $this->get_jurusan_list($page, $search);
                    $data['key'] = $param[0];
                    $data['jenis'] = $param[2];
                } else {
                    $data = $this->get_jurusan_list($page, $searchnull);
                }
                $this->load->view('kepegawaian/jurusan_list', $data);
                break;

            case 'post':
                // untuk add or edit
                if ($jurusan['id'] == '') {
                    //add
                    $search['id'] = $this->m_kepegawaian->jurusan_add_data($jurusan);
                    $data = $this->get_jurusan_list($page, $search);
                    $this->load->view('kepegawaian/jurusan_list', $data);
                } else {
                    $search['id'] = $jurusan['id'];
                    $this->m_kepegawaian->jurusan_edit_data($jurusan);
                    $data = $this->get_jurusan_list($page, $search);
                    $this->load->view('kepegawaian/jurusan_list', $data);
                }
                break;

            case 'delete':
                $id = $_GET['id'];
                $this->m_kepegawaian->jurusan_delete_data($id);
                $data = $this->get_jurusan_list($page, $searchnull);
                if ($data['jurusan'] == null) {
                    $data = $this->get_jurusan_list(1, $searchnull);
                }
                $this->load->view('kepegawaian/jurusan_list', $data);

                break;

            case 'search':
                $search = array(
                    'nama' => $this->input->post('nama'),
                    'jenis' => $this->input->post('id_jenis'),
                    'nm_jenis' => $this->input->post('jenis')
                );

                $data = $this->get_jurusan_list($page, $search);
                $data['key'] = $search['nama'];
                $data['jenis'] = $this->input->post('jenis');
                $this->load->view('kepegawaian/jurusan_list', $data);

                break;

            case 'cek':
                $cek = array(
                    'nama' => $this->input->post('nama'),
                    'jenis' => $this->input->post('id_jenis')
                );
                $data = $this->m_kepegawaian->jurusan_cek_data($cek);
                die(json_encode(array('status' => $data)));

                break;

            default:
                break;
        }
    }

    /* Jurusan Kualifikasi Pendidikan */


    /* Kepegawaian */

    function pegawai() {
        $data['title'] = "Kepegawaian";
        $data['jabatan'] = $this->m_kepegawaian->get_jabatan();
        $data['pendidikan'] = $this->m_kepegawaian->get_jenjang_pendidikan();
        $this->load->view('kepegawaian/pegawai', $data);
    }

    function get_jurusan() {
        $q = $_GET['q'];
        $data = $this->m_kepegawaian->load_jurusan($q)->result();
        die(json_encode($data));
    }

    function get_pegawai_list($page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $str = 'null';
        $start = ($page - 1) * $this->limit;
        $data['page'] = $page;
        $data['limit'] = $this->limit;

        $query = $this->m_kepegawaian->pegawai_get_data($this->limit, $start, $search);
        $data['pegawai'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];

        if ($search != 'null') {
            if (isset($search['gender'])) {
                $str = $search['gender'];
            } else {
                $str .= '@' . '';
            }
            if (isset($search['fromdate']) && ($search['fromdate'] != '')) {
                $str .= '@' . $search['fromdate'];
            } else {
                $str .= '@' . '';
            }
            if (isset($search['todate']) && ($search['todate'] != '')) {
                $str .= '@' . $search['todate'];
            } else {
                $str .= '@' . '';
            }
            if (isset($search['jenjang']) && ($search['jenjang'] != '')) {
                $str .= '@' . $search['jenjang'];
            } else {
                $str .= '@' . '';
            }
            if (isset($search['jurusan']) && ($search['jurusan'] != '')) {
                $str .= '@' . $search['jurusan'];
            } else {
                $str .= '@' . '';
            }

            if (isset($search['nm_jurusan']) && ($search['nm_jurusan'] != '')) {
                $str .= '@' . $search['nm_jurusan'];
            } else {
                $str .= '@' . '';
            }
        }

        $data['paging'] = paging_ajax($data['jumlah'], $this->limit, $page, 1, $str);
        return $data;
    }

    function manage_pegawai($mode, $page) {
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $cari = $_GET['search'];
                $c = array();
                if ($cari != 'null') {
                    $param = explode("@", $cari);

                    if (isset($param[0])) {
                        $search['gender'] = $param[0];
                        $c['gender'] = $param[0];
                    }

                    if (isset($param[1]) & isset($param[2])) {
                        $search['fromdate'] = $param[1];
                        $search['todate'] = $param[2];
                        $c['fromdate'] = $param[1];
                        $c['todate'] = $param[2];
                    }

                    if (isset($param[3]) & isset($param[4])) {
                        $search['jenjang'] = $param[3];
                        $search['jurusan'] = $param[4];
                        $search['nm_jurusan'] = $param[5];
                        $c['jenjang'] = $param[3];
                        $c['nm_jurusan'] = $param[5];
                    }
                    $data = $this->get_pegawai_list($page, $search);
                } else {
                    $data = $this->get_pegawai_list($page, $searchnull);
                }
                $data['pendidikan'] = $this->m_kepegawaian->get_jenjang_pendidikan();
                $this->load->view('kepegawaian/pegawai_list', array_merge($data, $c));
                break;

            case 'post':
                $pegawai = array(
                    'id' => $this->input->post('id_baru'),
                    'waktu' => datetime2mysql($this->input->post('waktu')),
                    'nip' => $this->input->post('nip'),
                    'nama' => $this->input->post('nama'),
                    'gender' => $this->input->post('kelamin_baru'),
                    'id_kualifikasi_pendidikan' => $this->input->post('jenjang_baru'),
                    'id_jurusan_kualifikasi_pendidikan' => $this->input->post('id_jurusan_baru'),
                    'jabatan' => $this->input->post('jabatan')
                );


                // untuk add or edit
                if ($pegawai['id'] == '') {
                    //add

                    if ($pegawai['gender'] == 'L') {
                        $pegawai['jumlah_kebutuhan_per_jenjang_pendidikan_pria'] = $this->input->post('jumlah');
                    } else {
                        $pegawai['jumlah_kebutuhan_per_jenjang_pendidikan_wanita'] = $this->input->post('jumlah');
                    }
                    $search['id'] = $this->m_kepegawaian->pegawai_add_data($pegawai);
                    $data = $this->get_pegawai_list($page, $search);
                    $this->load->view('kepegawaian/pegawai_list', $data);
                } else {
                    $search['id'] = $pegawai['id'];
                    $this->m_kepegawaian->pegawai_edit_data($pegawai);
                    $data = $this->get_pegawai_list($page, $search);
                    $this->load->view('kepegawaian/pegawai_list', $data);
                }
                break;

            case 'delete':
                $id = $_GET['id'];
                $this->m_kepegawaian->pegawai_delete_data($id);
                $data = $this->get_pegawai_list($page, $searchnull);
                if ($data['pegawai'] == null) {
                    $data = $this->get_pegawai_list(1, $searchnull);
                }
                $this->load->view('kepegawaian/pegawai_list', $data);

                break;

            case 'search':
                $c = array();
                $search = array(
                    'gender' => $this->input->post('gender'),
                    'fromdate' => date2mysql($this->input->post('fromdate')),
                    'todate' => date2mysql($this->input->post('todate')),
                    'jenjang' => $this->input->post('jenjang'),
                    'jurusan' => $this->input->post('id_jurusan'),
                    'nm_jurusan' => $this->input->post('jurusan')
                );
                $data = $this->get_pegawai_list($page, $search);

              
                $data['pendidikan'] = $this->m_kepegawaian->get_jenjang_pendidikan();
                $this->load->view('kepegawaian/pegawai_list', array_merge($data, $search));
                break;

            case 'cek':
                $cek = array(
                    'nama' => $this->input->post('nama'),
                );
                $data = $this->m_kepegawaian->pegawai_cek_data($cek);
                die(json_encode(array('status' => $data)));

                break;

            default:
                break;
        }
    }

    /* Kepegawaian */
}

?>