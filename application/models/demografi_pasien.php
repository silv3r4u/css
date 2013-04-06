<?php

class Demografi_pasien extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get_by_no_rm($no_rm) {
        $sql = "select p.id, p.no_rm, p.is_cetak_kartu ,pd.nama as nama, pd.gender, kab.nama as tempat_lahir, kab.id as tempat_lahir_id, pd.lahir_tanggal,
            pd.telp, pd.darah_gol, dp.agama,pdi.nama as pendidikan, pdi.id as pendidikan_id , pk.nama as pekerjaan, 
            pk.id as pekerjaan_id , dp.pernikahan, dp.id as dinamis_penduduk_id,
            dp.alamat, dp.identitas_no, kel.nama as kelurahan, kel.id as kelurahan_id, kabb.nama as kabupaten, kec.nama as kecamatan, 
            pro.nama as provinsi, pd_bp.nama as nama_ayah,pd_bp.id as ayah_id, pd_ib.nama as nama_ibu, pd_ib.id as ibu_id
            from pasien p 
            join penduduk pd on (p.id = pd.id)
            left join kabupaten kab on (kab.id = pd.lahir_kabupaten_id)
            left join dinamis_penduduk dp on (pd.id = dp.penduduk_id)
            left join pendidikan pdi on (pdi.id = dp.pendidikan_id)
            left join pekerjaan pk on (pk.id = dp.pekerjaan_id)
            left join asuransi_kepesertaan a on (a.id = pd.id)
            left join kelurahan kel on (kel.id = dp.kelurahan_id)
            left join kecamatan kec on (kel.kecamatan_id = kec.id)
            left join kabupaten kabb on (kec.kabupaten_id = kabb.id)
            left join provinsi pro on (kabb.provinsi_id = pro.id)
            left join penduduk pd_bp on (pd.ayah_penduduk_id = pd_bp.id)
            left join penduduk pd_ib on (pd.ibu_penduduk_id = pd_ib.id)
            inner join (
                select penduduk_id, max(id) as id_max
                from dinamis_penduduk GROUP BY penduduk_id
            ) dpi on (dp.penduduk_id = dpi.penduduk_id and dp.id = dpi.id_max)
            where p.no_rm = '" . $no_rm . "'";

        $data = $this->db->query($sql);
        return $data->result();
    }

    function get_asuransi_kepesertaan($norm, $id_pdd) {

        $sql = "select ap.nama,a.id, a.polis_no , a.asuransi_produk_id
            from penduduk p
            left join pasien ps on (p.id = ps.id)
            join asuransi_kepesertaan a on (a.penduduk_id = p.id)
            join asuransi_produk ap on (ap.id = a.asuransi_produk_id)";

        if ($norm != null) {
            $sql .= " where ps.no_rm = '$norm'";
        } else if ($id_pdd != null) {
            $sql .= " where p.id= '$id_pdd'";
        }
        return $this->db->query($sql);
    }

    function get() {
        $this->db->from('pasien');
        $this->db->order_by('no_rm');
        $data = $this->db->get();
        return $data->result();
    }

    function get_where($data) {
        $data = $this->db->get_where('pasien', $data);
        return $data->row();
    }

    function next_kunjungan($param) {
        // $param = nomor id pasien pasien 
        $this->db->trans_begin();

        $this->db->from('pasien');
        $this->db->where('no_rm', $param['no_rm']);
        $data = $this->db->get();
        $demo = $data->row();

        $next_kunjungan = $demo->kunjungan + 1;
        $this->db->where('no_rm', $param['no_rm']);
        $this->db->update('pasien', array('kunjungan' => $next_kunjungan));

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
    }

    function create_penduduk($data) {
        $this->db->insert('penduduk', $data);
        return $this->db->insert_id();
    }

    function save_penduduk($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('penduduk', $data);
    }

    function create_dinamis_penduduk($data) {
        $this->db->insert('dinamis_penduduk', $data);
    }

    function create_pasien($data) {
        $this->db->insert('pasien', $data);
        return $this->db->insert_id();
    }

    function create_asuransi($data) {
        $this->db->trans_begin();
        foreach ($data['asu_id'] as $key => $row) {
            if ($row != "") {
                $this->db->insert('asuransi_kepesertaan', array(
                    'penduduk_id' => $data['penduduk_id'],
                    'asuransi_produk_id' => $row,
                    'polis_no' => $data['polis'][$key]));
            }
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
    }

    function save_asuransi($data) {
        $this->db->trans_begin();
        foreach ($data['asu_id'] as $key => $row) {
            $update = array(
                'asuransi_produk_id' => $row,
                'polis_no' => $data['polis'][$key]
            );

            $this->db->where('id', $data['id'][$key]);
            $this->db->update('asuransi_kepesertaan', $update);
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
    }

    function save($data) {
        $this->db->where('no_rm', $data['no_rm']);
        $this->db->update('pasien', $data);
    }

    function add_is_cetak_kartu($no_rm) {
        //get jumlah is_cetak_kartu , then plus 1
        // update       
        $data = $this->get_by_no_rm($no_rm);
        $update = array(
            'is_cetak_kartu' => '1'
        );


        $this->db->where('no_rm', $no_rm);
        $this->db->update('pasien', $update);
    }

    function get_kelurahan($q) {
        $sql = "select kel.*, kec.nama as kecamatan, kab.id as kabupaten_id ,kab.nama as kabupaten, pro.nama as provinsi from kelurahan kel
            left join kecamatan kec on (kel.kecamatan_id = kec.id)
            left join kabupaten kab on (kec.kabupaten_id = kab.id)
            left join provinsi pro on (kab.provinsi_id = pro.id)
        where kel.nama like ('%$q%') order by locate ('$q', kel.nama)";
        $exe = $this->db->query($sql);
        return $exe->result();
    }

    function get_kabupaten($q) {
        $sql = "select  kab.id as kabupaten_id ,kab.nama as kabupaten, pro.nama as provinsi from
            kabupaten kab left join provinsi pro on (kab.provinsi_id = pro.id)
        where kab.nama like ('%$q%') order by locate ('$q', kab.nama)";
        $exe = $this->db->query($sql);
        return $exe->result();
    }

    function get_asuransi($q) {
        $sql = "select * from asuransi_produk where nama like ('%$q%') order by locate ('$q', nama)";
        $exe = $this->db->query($sql);
        return $exe->result();
    }

    function get_penanggungjawab($q) {
        $sql = "select pd.*, dp.alamat, dp.kelurahan_id, k.nama as kelurahan, kec.nama as kecamatan, kab.nama as kabupaten, pro.nama as provinsi from penduduk pd
            left join dinamis_penduduk dp on (pd.id = dp.penduduk_id)
            left join kelurahan k on (dp.kelurahan_id = k.id)
            left join kecamatan kec on (k.kecamatan_id = kec.id)
            left join kabupaten kab on (kec.kabupaten_id = kab.id)
            left join provinsi pro on (kab.provinsi_id = pro.id)
           inner join (
                select penduduk_id, max(id) as id_max
                from dinamis_penduduk GROUP BY penduduk_id
            ) dpi on (dp.penduduk_id = dpi.penduduk_id and dp.id = dpi.id_max)
            where pd.nama like ('%$q%') order by locate ('$q',pd.nama)";
        $exe = $this->db->query($sql);
        return $exe->result();
    }

    function find_similiar($data) {
        $q = null;
        if ($data['nama'] != '') {
            $q.=" and pd.nama like ('%$data[nama]%')";
        }
        if ($data['tgl_lahir'] != '') {
            $q.=" and pd.lahir_tanggal = '" . datetopg($data['tgl_lahir']) . "'";
        }
        if ($data['kelamin'] != '') {
            $q.=" and pd.gender = '$data[kelamin]'";
        }
        $sql = "select * from pasien p 
            join penduduk pd on (p.id = pd.id)
            left join dinamis_penduduk dp on (dp.id = pd.id)
            inner join (
                select id, max(id) as id_max from dinamis_penduduk
                group by id
            ) tm on (dp.id = tm.id and dp.id = tm.id_max)
            where pd.id is not NULL $q";
        //echo "<pre>".$sql."</pre>";
        $data = $this->db->query($sql);
        return $data->result();
    }

    function get_umur($tgl_lahir) {
        $tglawal = date('Y');  // Format: Tanggal/Bulan/Tahun -> 12 Desember 2010
        $year1 = explode('-', $tgl_lahir);
        $selisih = $tglawal - $year1;
//Tampilkan hasil
        return $selisih;
    }

    function calc_tgl_lahir($umur) {
        $tgl = (date('Y') - $umur) . "-" . date('m-d');
        return $tgl;
    }

    function advanced_search($limit, $start, $data) {
        $umur = $this->calc_tgl_lahir($data['umur']);
        $q = null;
        /* $this->db->from('pasien p');
          $this->db->join('penduduk pd','p.id = pd.id');
          $this->db->join('');
          $this->db->like('nama', $data['nama']);
          $this->db->where('kelamin', $data['kelamin']);
          //   $this->db->where('tgl_lahir', substr($umur, 0, 4));
          $this->db->like('nm_ibu', $data['nm_ibu']);
          $this->db->like('addr_jln', $data['addr_jln']); */
        if ($data['nama'] != '') {
            $q.=" and pd.nama like ('%$data[nama]%')";
        }
        if ($data['kelamin'] != '') {
            $q.=" and pd.gender = '$data[kelamin]'";
        }
        if ($data['umur'] != '') {
            $year_now = date("Y");
            $selisih = $year_now - $data['umur'];
            $new_param = $selisih . "-" . date("m") . "-" . date("d");
            $last_param = ($selisih - 1) . "-" . date("m") . "-" . date("d");
            $q.=" and pd.lahir_tanggal between '$last_param' and '$new_param'";
        }
        if ($data['addr_jln'] != '') {
            $q.=" and dp.alamat like ('%$data[addr_jln]%')";
        }
        if ($data['nm_ibu'] != '') {
            //$q.=" and dp.";
        }
        $sql = "select * from pasien p 
            join penduduk pd on (p.id = pd.id)
            left join dinamis_penduduk dp on (dp.penduduk_id = pd.id)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk
                group by penduduk_id
            ) tm on (dp.penduduk_id = tm.penduduk_id and dp.id = tm.id_max)
            where pd.id is not NULL $q";
        //echo $sql;
        $paging = " limit " . $start . "," . $limit . " ";
        $data = $this->db->query($sql . $paging);
        $ret['data'] = $data->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows;
        return $ret;
    }

    function dob() {
        return array('tgl_lahir' => 'Tanggal lahir', 'umur' => 'Umur');
    }

    function jenis_demografi() {
        return array('0' => 'Pilih Jenis Demografi', '1' => 'Jenis Kelamin', '2' => 'Usia', '3' => 'Agama', '4' => 'Pendidikan', '5' => 'Pekerjaan', '6' => 'Status Pernikahan', '7' => 'Golongan Darah', '8' => 'Wilayah');
    }

    function identitas() {
        return array('noktp' => 'No. KTP', 'sim' => 'SIM', 'passport' => 'Passport');
    }

    function usia() {
        return array('tanggal lahir' => 'Tanggal Lahir', 'umur' => 'Umur');
    }

    function agama() {
        return array('' => 'Pilih', 'Islam' => 'Islam', 'Kristen' => 'Kristen', 'Katholik' => 'Katholik', 'Hindu' => 'Hindu', 'Budha' => 'Budha', 'kepercayaan' => 'Kepercayaan', 'tidak beragama' => 'Tidak Beragama');
    }

    function pendidikan() {
        $db = $this->db->get('pendidikan');
        $data[''] = 'Pilih';
        foreach ($db->result() as $row) {
            $data[$row->id] = $row->nama;
        }
        return $data;
    }

    function stat_nikah() {
        return array('' => 'Pilih', 'Lajang' => 'Lajang', 'Menikah' => 'Menikah', 'Duda' => 'Duda', 'Janda' => 'Janda');
    }

    function pekerjaan() {
        $this->db->order_by('nama', 'asc');
        $db = $this->db->get('pekerjaan');
        $data[''] = 'Pilih';
        foreach ($db->result() as $row) {
            $data[$row->id] = $row->nama;
        }
        return $data;
    }

    function kelamin() {
        return array('' => 'Pilih', 'L' => 'Laki-laki', 'P' => 'Perempuan');
    }

    function gol_darah() {
        return array('' => 'Pilih', 'A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O');
    }

    function rentang_usia() {
        return array('0-6', '7-28', '29-364', '365-1824', '1825-5474', '5475-9124', '9125-16424', '16425-23139', '23140');
    }

    function format_usia() {
        return array('0 - 6 Hari',
            '7 - 28 Hari',
            '28 - 364 Hari',
            '1 -4 Tahun',
            '5 -14 Tahun',
            '15 - 24 Tahun',
            '25 - 44 Tahun',
            '45 - 64 Tahun',
            '> 65 Tahun',);
        ;
    }

    function get_penduduk($id) {
        $sql = "select pd.nama as nama, pd.gender, kab.nama as tempat_lahir, kab.id as tempat_lahir_id, pd.lahir_tanggal,
            pd.telp, pd.darah_gol, dp.agama,pdi.nama as pendidikan, pdi.id as pendidikan_id , pk.nama as pekerjaan, 
            pk.id as pekerjaan_id , dp.pernikahan, dp.id as dinamis_penduduk_id,
            dp.alamat, dp.identitas_no, kel.nama as kelurahan, kel.id as kelurahan_id, kabb.nama as kabupaten, kec.nama as kecamatan, 
            pro.nama as provinsi, pd_bp.nama as nama_ayah,pd_bp.id as ayah_id, pd_ib.nama as nama_ibu, pd_ib.id as ibu_id
            from penduduk pd
            left join kabupaten kab on (kab.id = pd.lahir_kabupaten_id)
            left join dinamis_penduduk dp on (pd.id = dp.penduduk_id)
            left join pendidikan pdi on (pdi.id = dp.pendidikan_id)
            left join pekerjaan pk on (pk.id = dp.pekerjaan_id)
            left join asuransi_kepesertaan a on (a.id = pd.id)
            left join kelurahan kel on (kel.id = dp.kelurahan_id)
            left join kecamatan kec on (kel.kecamatan_id = kec.id)
            left join kabupaten kabb on (kec.kabupaten_id = kabb.id)
            left join provinsi pro on (kabb.provinsi_id = pro.id)
            left join penduduk pd_bp on (pd.ayah_penduduk_id = pd_bp.id)
            left join penduduk pd_ib on (pd.ibu_penduduk_id = pd_ib.id)
             inner join (
                select penduduk_id, max(id) as id_max
                from dinamis_penduduk GROUP BY penduduk_id
            ) dpi on (dp.penduduk_id = dpi.penduduk_id and dp.id = dpi.id_max)
            where pd.id= '" . $id . "'";
        $exe = $this->db->query($sql);
        return $exe->row();
    }

}

?>
