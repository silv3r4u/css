<?php

class Laporan_pendaftaran extends CI_Model {

    function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }

    function get_bulan() {
        return array(
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        );
    }

    function get_tahun() {
        $start = 2013;
        $arrYear = '';
        while ($start == date('Y')) {
            $arrYear[$start] = $start;
            $start++;
        }

        return $arrYear;
    }

    function get_kunjungan_harian($param) {
        /*
         * Param
         * 1. from
         * 2. to
         */

        $q = "WHERE tgl_layan BETWEEN '" . $param['from'] . "' AND '" . $param['to'] . "' and p.arrive_time is not NULL ";
        $db = "select tgl_layan, nama, count(*) as jumlah FROM pendaftaran p join layanan u on(p.layanan = u.id)";
        if ($param['from'] != 'undefined-undefined-') {
            $db.=$q;
        }

        $db .="GROUP BY tgl_layan";
        $data = $this->db->query($db);
        return $data->result();
    }

    function get_kunjungan_harian_unit($param, $kd_unit) {
        /*
         * Param
         * 1. layanan =  semua layanan
         * 2. tgl = range tanggal
         */

        foreach ($param as $tgl) {
            $db = "SELECT  count(*) as jumlah FROM pendaftaran p JOIN layanan u ON ( p.layanan = u.id )  ";
            if ($tgl->tgl_layan != null) {
                $db.="WHERE tgl_layan ='" . $tgl->tgl_layan . "'";
            }

            $db.="AND u.nama = '" . $kd_unit . "'";

            $query = $this->db->query($db);
            $data[] = $query->row()->jumlah;
        }

        return $data;
    }

    function get_kunjungan_bulanan_unit($param, $kd_unit) {
        /*
         * Param
         * 1. layanan =  semua layanan
         * 2. tgl = range tanggal
         */


        foreach ($param as $row) {
            $db = "SELECT  count(*) as jumlah FROM pendaftaran p JOIN layanan u ON ( p.layanan = u.id )
                WHERE month(tgl_layan) = '" . $row->bulan . "' AND year(tgl_layan) = '" . $row->tahun . "'
                    AND u.nama = '" . $kd_unit . "'";

            $query = $this->db->query($db);
            $data[] = $query->row()->jumlah;
        }

        return $data;
    }

    function get_kunjungan_bulanan_pasien($param) {
        $db = "select month(tgl_layan) as bulan, year(tgl_layan) as tahun,  count(*) as jumlah FROM pendaftaran WHERE
            tgl_layan BETWEEN '" . $param['th_from'] . "-" . $param['bl_from'] . "-01" . "' 
                AND '" . $param['th_to'] . "-" . $param['bl_to'] . "-31" . "' group by month(tgl_layan)";
        //echo $db;
        $data = $this->db->query($db);
        return $data->result();
    }

    function get_pasien_baru($param) {
        /*
         * Param =  result dari get_kunjungan_harian
         * 
         */
        foreach ($param as $row) {
            $db = "select  count(*) as baru FROM pendaftaran p join pasien d on(p.pasien = d.no_rm) 
                WHERE tgl_layan = '" . $row->tgl_layan . "' AND kunjungan = 1";
            $query = $this->db->query($db);


            $data[] = $query->row()->baru;
        }

        return $data;
    }

    function get_pasien_bl_baru($param) {
        /*
         * Param =  result dari get_kunjungan_harian
         * 
         */
        foreach ($param as $row) {
            $db = "select  count(*) as baru FROM pendaftaran p join pasien d on(p.pasien = d.no_rm) 
                WHERE month(tgl_layan) = '" . $row->bulan . "' AND year(tgl_layan) = '" . $row->tahun . "' AND kunjungan = 1";
            $query = $this->db->query($db);


            $data[] = $query->row()->baru;
        }

        return $data;
    }

    function get_pasien_lama($param) {
        /*
         * Param =  result dari get_kunjungan_harian
         * 
         */
        foreach ($param as $row) {
            $db = "select  count(*) as lama FROM pendaftaran p join pasien d on(p.pasien= d.no_rm)
                WHERE tgl_layan = '" . $row->tgl_layan . "' AND kunjungan > 1";
            $query = $this->db->query($db);

            $data[] = $query->row()->lama;
        }

        return $data;
    }

    function get_pasien_bl_lama($param) {
        /*
         * Param =  result dari get_kunjungan_harian
         * 
         */
        foreach ($param as $row) {
            $db = "select  count(*) as lama FROM pendaftaran p join pasien d on(p.pasien = d.no_rm)
               WHERE month(tgl_layan) = '" . $row->bulan . "' AND year(tgl_layan) = '" . $row->tahun . "' AND kunjungan > 1";
            $query = $this->db->query($db);

            $data[] = $query->row()->lama;
        }

        return $data;
    }

    function get_demografi_agama($param) {
        /*
         * Param
         * 1. from
         * 2. to
         */
        foreach ($param['agama'] as $key => $row) {
            $db = "SELECT agama, count(*) AS jumlah FROM pendaftaran pr
                    JOIN pasien p on(pr.pasien = p.no_rm)
                    JOIN penduduk d ON(p.id = d.id) 
                    JOIN dinamis_penduduk dp on(pr.dinamis_penduduk_id = dp.id)
                    WHERE dp.agama = '" . $row . "' ";
            if ($param['from'] != 'undefined-undefined-') {
                $db .= "AND date(pr.tgl_layan) BETWEEN '" . $param['from'] . "' AND '" . $param['to'] . "' ";
            }


            $query = $this->db->query($db);

            $data[] = $query->row()->jumlah;
        }


        return $data;
    }

    function get_demografi_pekerjaan($param) {
        foreach ($param['pekerjaan'] as $row) {
            $db = "SELECT pk.nama as pekerjaan , count(*) AS jumlah FROM pendaftaran pr
                JOIN pasien p on(pr.pasien = p.no_rm)
                JOIN penduduk d ON(p.id = d.id) 
                JOIN dinamis_penduduk dp on(pr.dinamis_penduduk_id = dp.id)
                JOIN pekerjaan pk on (dp.pekerjaan_id = pk.id)
               WHERE  pk.nama = '" . $row . "' ";

            if ($param['from'] != 'undefined-undefined-') {
                $db .= "AND date(pr.tgl_layan) BETWEEN '" . $param['from'] . "' AND '" . $param['to'] . "' ";
            }
            $query = $this->db->query($db);
            $data[] = $query->row()->jumlah;
        }

        return $data;
    }

    function get_demografi_pendidikan($param) {
        foreach ($param['pendidikan'] as $row) {
            $db = "SELECT pend.nama as pendidikan , count(*) AS jumlah FROM pendaftaran pr
                JOIN pasien p on(pr.pasien = p.no_rm)
                JOIN penduduk d ON(p.id = d.id) 
                JOIN dinamis_penduduk dp on(pr.dinamis_penduduk_id = dp.id)
                JOIN pendidikan pend on (dp.pendidikan_id = pend.id)
                WHERE pend.nama = '" . $row . "' ";

            if ($param['from'] != 'undefined-undefined-') {
                $db .= "AND date(pr.tgl_layan) BETWEEN '" . $param['from'] . "' AND '" . $param['to'] . "' ";
            }

            $query = $this->db->query($db);

            $data[] = $query->row()->jumlah;
        }
        return $data;
    }

    function get_demografi_nikah($param) {
        foreach ($param['nikah'] as $row) {
            $db = "SELECT pernikahan, count(*) AS jumlah FROM pendaftaran pr
                JOIN pasien p on(pr.pasien = p.no_rm)
                JOIN penduduk d ON(p.id = d.id) 
            JOIN dinamis_penduduk dp on(pr.dinamis_penduduk_id = dp.id)
            WHERE dp.pernikahan = '" . $row . "'";

            if ($param['from'] != 'undefined-undefined-') {
                $db .= "AND date(pr.tgl_layan) BETWEEN '" . $param['from'] . "' AND '" . $param['to'] . "' ";
            }
            $query = $this->db->query($db);

            $data[] = $query->row()->jumlah;
        }
        return $data;
    }

    function get_demografi_kelamin($param) {

        foreach ($param['kelamin'] as $key => $row) {
            $db = "select gender, count(*) as jumlah FROM pendaftaran pr
                JOIN pasien p on(pr.pasien = p.no_rm) 
                JOIN penduduk d on(p.id = d.id)
            WHERE d.gender = '" . $key . "' ";

            if ($param['from'] != 'undefined-undefined-') {
                $db .= "AND date(pr.tgl_layan) BETWEEN '" . $param['from'] . "' AND '" . $param['to'] . "' ";
            }

            $query = $this->db->query($db);
            $data[] = $query->row()->jumlah;
        }
        return $data;
    }

    function get_demografi_darah($param) {
        foreach ($param['darah'] as $row) {
            $db = "select darah_gol, count(*) as jumlah FROM pendaftaran pr
                JOIN pasien p on(pr.pasien = p.no_rm)
                JOIN penduduk d on(p.id = d.id)
            WHERE d.darah_gol = '" . $row . "' ";

            if ($param['from'] != 'undefined-undefined-') {
                $db .= "AND date(pr.tgl_layan) BETWEEN '" . $param['from'] . "' AND '" . $param['to'] . "' ";
            }

            $query = $this->db->query($db);

            $data[] = $query->row()->jumlah;
        }
        return $data;
    }

    function get_demografi_usia($param) {

        foreach ($param['usia'] as $row) {
            $usia = explode("-", $row);
            $db = "select  count(*) as jumlah FROM pendaftaran pr
                JOIN pasien p on(pr.pasien = p.no_rm)
                JOIN penduduk d on(p.id = d.id)
                WHERE datediff('" . date('Y-m-d') . "',d.lahir_tanggal ) >  '" . $usia[0] . "' ";
            if (isset($usia[1])) {
                $db .= " AND datediff('" . date('Y-m-d') . "',d.lahir_tanggal ) < '" . $usia[1] . "' ";
            }

            if ($param['from'] != 'undefined-undefined-') {
                $db .= "AND date(pr.tgl_layan) BETWEEN '" . $param['from'] . "' AND '" . $param['to'] . "' ";
            }
            //echo "<pre>".$db."</pre>";
            $query = $this->db->query($db);
            $data[] = $query->row()->jumlah;
        }

        return $data;
    }

    function get_demografi_wilayah($param) {
        $area[] = null;
        $data[] = null;
        foreach ($param['area'] as $row) {
            $db = "count(*) as jumlah 
                    FROM pendaftaran pr
                    JOIN pasien p on(pr.pasien = p.no_rm)
                    JOIN penduduk d on(p.id = d.id)
                    JOIN dinamis_penduduk dp on(pr.dinamis_penduduk_id = dp.id)
                    left JOIN kelurahan kel on (kel.id = dp.kelurahan_id)
                    left JOIN kecamatan kec on (kel.kecamatan_id = kec.id)
                    left JOIN kabupaten kabb on (kec.kabupaten_id = kabb.id)
                    left JOIN provinsi pro on (kabb.provinsi_id = pro.id)";

            if ($param['tipe'] == "kelurahan") {
                // array
                $db = "select kel.nama as area ," . $db . " WHERE kel.id = '" . $row . "'";
            } else if ($param['tipe'] == "kecamatan") {
                //array
                $db = "select kec.nama as area, " . $db . "WHERE kec.id = '" . $row . "'";
            } else if ($param['tipe'] == "kabupaten") {
                $db = "select  kabb.nama as area," . $db . "WHERE kabb.id = '" . $row . "'";
            } else if ($param['tipe'] == "provinsi") {
                $db = "select  pro.nama as area," . $db . "WHERE pro.id = '" . $row . "'";
            }

            if ($param['from'] != 'undefined-undefined-') {
                $db .= "AND date(pr.tgl_layan) BETWEEN '" . $param['from'] . "' AND '" . $param['to'] . "' ";
            }

            $query = $this->db->query($db);
            if ($query->row()->jumlah != 0) {

                $area[] = $query->row()->area;
                $data[] = $query->row()->jumlah;
            }
        }

        return array('area' => $area, 'data' => $data);
    }

    function kategori_demografi($tabel) {
        $db = $this->db->get($tabel);
        foreach ($db->result() as $row) {
            $data[$row->id] = $row->nama;
        }
        return $data;
    }

    function rujukan_get_data($limit, $start, $data) {
        $range = '';
        $nakes = '';
        $relasi = '';
        $q = '';

        $q.=" limit " . $start . ", $limit";

        if (($data['from'] != '') & ($data['to'] != '')) {
            $range = " and p.tgl_daftar  BETWEEN '" . $data['from'] . "' AND '" . $data['to'] . "'";
        }

        if ($data['instansi'] != '') {
            $relasi = " and r.id = '" . $data['instansi'] . "'";
        }
        if ($data['nakes'] != '') {
            $nakes = " and nk.id = '" . $data['nakes'] . "'";
        }

        $sql = "SELECT p.tgl_daftar, pd.nama as nama_pasien,r.nama as nama_instansi, nk.nama as nama_nakes  FROM pendaftaran p
            left join pasien d on (p.pasien = d.no_rm) 
            left join penduduk pd on (pd.id = d.id)
            left join relasi_instansi r on(p.rujukan_instansi_id = r.id)
            left join penduduk nk on(p.nakes_penduduk_id = nk.id)
            WHERE rujukan_instansi_id is not null and nakes_penduduk_id is not null $range $relasi $nakes";
   
        $data = $this->db->query($sql . $q);
        $ret['hasil'] = $data->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

}

?>