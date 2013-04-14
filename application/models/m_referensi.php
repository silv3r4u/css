<?php

class M_referensi extends CI_Model {

    function ubah_password($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('users', $data);
    }

    function kelas_layanan_get_data() {
        return array(
            'pilih' => 'Pilih kelas', 'VIP' => 'VIP', 'III' => 'III'
        );
    }

    function get_user_detail($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('users');
        return $query->row();
    }

    function bobot_layanan_get_data() {
        return array(
            'Tanpa Bobot' => 'Tanpa Bobot',
            'Ringan' => 'Ringan',
            'Sedang' => 'Sedang',
            'Berat' => 'Berat'
        );
    }

    function unit_get_data($jenis = NULL) {
        $sort = NULL;
        if ($jenis != NULL) {
            $sort = "where jenis = '$jenis'";
        }


        $sql = "select id, nama from unit $sort order by nama";

        $query = $this->db->query($sql)->result();
        $data[''] = "Pilih Unit";
        foreach ($query as $value) {
            $data[$value->id] = $value->nama;
        }
        return $data;
    }

    function perundangan_load_data() {
        $array = array(
            '' => 'Semua perundangan ...',
            'Bebas' => 'Bebas',
            'Bebas Terbatas' => 'Bebas Terbatas',
            'OWA' => 'OWA',
            'Keras' => 'Keras',
            'Psikotropika' => 'Psikotropika',
            'Narkotika' => 'Narkotika'
        );
        return $array;
    }

    function generik_load_data() {
        $array = array(
            '' => 'Semua ..',
            'Generik' => 'Generik',
            'Non Generik' => 'Non Generik'
        );
        return $array;
    }

    function kolom_multiselect() {
        $array = array(
            'HPP',
            'HNA',
            'HET',
            'Alasan',
            'Awal',
            'Masuk',
            'Keluar',
            'No. Transaksi',
            'Jenis Transaksi',
            'Tanggal',
            'Packing Barang',
            'ED',
            'Harga',
            'Sisa'
        );
        return $array;
    }

    function adm_r_get_data() {
        $array = array(
            'Oral', 'Rektal', 'Infus', 'Topikal', 'Sublingual', 'Intrakutan', 'Subkutan', 'Intravena', 'Intramuskuler', 'Vagina', 'Injeksi', 'Intranasal', 'Intraokuler', 'Intraaurikuler', 'Intrapulmonal', 'Implantasi', 'Intralumbal', 'Intrarteri'
        );
        sort($array);
        $rows[] = 'Pilih';
        foreach ($array as $val) {
            $rows[$val] = $val;
        }
        return $rows;
    }

    function bed_get_data() {
        $sql = "SELECT t.id, t.kelas, t.no, u.nama, t.tarif FROM tt t
            join unit u on(t.unit_id = u.id)";
        $query = $this->db->query($sql)->result();
        $data = null;
        foreach ($query as $value) {
            $data[$value->id] = $value->nama;
        }

        return $data;
    }

    function get_bed_data($id) {
        $sql = "SELECT t.id, t.kelas, t.no,u.id as unit, u.nama, t.tarif FROM tt t
            join unit u on(t.unit_id = u.id) where t.id = '" . $id . "'";
        $arr = $this->db->query($sql)->row();

        $json = array(
            'id' => $arr->id,
            'nama' => $arr->nama,
            'unit' => $arr->unit,
            'kelas' => $arr->kelas,
            'no' => $arr->no,
            'tarif' => $arr->tarif
        );
        return json_encode($json);
    }

    /* Masterdata Unit */

    function get_unit_data() {
        $query = $this->db->get('unit');
        return $query->result();
    }

    function cek_unit($unit) {
        $db = "select count(*) as jumlah from unit where nama = '$unit'";
        $query = $this->db->query($db);
        return $query->row();
    }

    function add_unit($unit) {
        $db = "insert into unit (id, nama) values ('','$unit')";
        $this->db->query($db);
    }

    function delete_unit($id) {
        $db = "delete from unit where id = '$id'";
        $this->db->query($db);
    }

    function edit_unit($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('unit', $data);
    }

    /* Masterdata Unit */

    /* Produk Asuransi */

    function get_produk_asuransi_data($limit, $start, $search) {
        $q = '';
        if (($search != 'null') & isset($search['id'])) {
            $q = " where ap.id = '" . $search['id'] . "'";
        }
        $limit = " limit $start, $limit";;
        $sort = " order by ap.nama , r.nama asc";
        $sql = "select ap.*, r.nama as prsh, r.id as id_ap from asuransi_produk ap
        join relasi_instansi r on (ap.relasi_instansi_id = r.id)";
        $query = $this->db->query($sql . $q . $sort . $limit);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql . $q)->num_rows();
        return $ret;
    }

    function add_produk_asuransi_data($data) {
        $this->db->insert('asuransi_produk', $data);
        return $this->db->insert_id();
    }

    function relasi_instansi_data($q) {
        $sql = "select i.*, j.nama as jenis from relasi_instansi i
        join relasi_instansi_jenis j on (i.relasi_instansi_jenis_id = j.id)
        where i.nama like ('%$q%') and j.nama = 'Asuransi' order by locate('$q', i.nama)";
        $query = $this->db->query($sql);
        return $query->result();
    }

    function produk_cek_data($data) {
        $sql = "select count(*) as jumlah from asuransi_produk 
            where nama = '" . $data['nama'] . "' and relasi_instansi_id = '" . $data['relasi'] . "' ";
        $query = $this->db->query($sql);
        $jml = $query->row()->jumlah;
        if ($jml == 0) {
            return true;
        } else {
            return false;
        }
    }

    function produk_asuransi_last_no() {
        $sql = "select max(id) as last from asuransi_produk";
        $row = $this->db->query($sql)->row();
        if ($row != null) {
            $last = $row->last;
            $last++;
        } else {
            $last = 1;
        }

        return $last;
    }

    function delete_produk_asuransi($id) {
        $db = "delete from asuransi_produk where id = '$id'";
        $this->db->query($db);
    }

    function edit_produk_asuransi_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('asuransi_produk', $data);
    }

    function add_relasi_instansi_data($data) {
        $this->db->insert('relasi_instansi', $data);
        return $this->db->insert_id();
    }

    /* Produk Asuransi */

    /* Data Wilayah */

    function provinsi_data($q) {
        $sql = "select * from provinsi where nama like ('%$q%') order by locate ('$q', nama)";
        $query = $this->db->query($sql);
        return $query->result();
    }

    function provinsi_get_data($limit = null, $start = null, $id = null) {
        $w = "";
        $page = "  limit $start ,$limit";

        if ($id != 'null') {
            $w = " where p.id = $id ";
        }
        $sql = "select @row := @row + 1 as nomor,p.* from 
            provinsi p, (SELECT @row := $start) rr $w order by nama asc";
        $query = $this->db->query($sql . $page);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function provinsi_add_data($data) {
        $this->db->insert('provinsi', $data);
        return $this->db->insert_id();
    }

    function provinsi_delete_data($id) {
        $db = "delete from provinsi where id = '$id'";
        $this->db->query($db);
    }

    function provinsi_edit_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('provinsi', $data);
    }

    function provinsi_cek_data($data) {

        $sql = "select count(*) as jumlah from provinsi where nama = '" . $data['nama'] . "'";
        $query = $this->db->query($sql);
        $jml = $query->row()->jumlah;
        if ($jml == 0) {
            return true;
        } else {
            return false;
        }
    }

    function kabupaten_data($q) {
        $sql = "select k.*, p.nama as provinsi, p.id as id_provinsi FROM kabupaten k
        join provinsi p on (k.provinsi_id = p.id)
        where k.nama like ('%$q%') order by locate ('$q', k.nama)";
        $query = $this->db->query($sql);
        return $query->result();
    }

    function kabupaten_get_data($limit, $start, $id) {
        $w = "";
        $page = "  limit $start ,$limit";

        if ($id != 'null') {
            $w = " where k.id = $id ";
        }
        $sql = "select @row := @row + 1 as nomor, k.*, p.nama as provinsi, p.id as id_provinsi FROM kabupaten k
        join provinsi p on (k.provinsi_id = p.id), (SELECT @row := $start) rr $w
            order by nama asc";
        $query = $this->db->query($sql . $page);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function kabupaten_add_data($data) {
        $this->db->insert('kabupaten', $data);
        return $this->db->insert_id();
    }

    function kabupaten_delete_data($id) {
        $db = "delete from kabupaten where id = '$id'";
        $this->db->query($db);
    }

    function kabupaten_edit_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('kabupaten', $data);
    }

    function kabupaten_cek_data($data) {

        $sql = "select count(*) as jumlah from kabupaten 
            where provinsi_id = '" . $data['provinsi_id'] . "' and nama = '" . $data['nama'] . "' ";
        $query = $this->db->query($sql);
        $jml = $query->row()->jumlah;
        if ($jml == 0) {
            return true;
        } else {
            return false;
        }
    }

    function kecamatan_data($q) {
        $sql = "select kc.*, k.nama as kabupaten, k.id as id_kabupaten, p.nama as provinsi FROM kecamatan kc
        join kabupaten k on (kc.kabupaten_id = k.id)
        join provinsi p on (k.provinsi_id = p.id)
        where kc.nama like ('%$q%') order by locate ('$q', kc.nama)";
        $query = $this->db->query($sql);
        return $query->result();
    }

    function kecamatan_get_data($limit, $start, $id) {
        $w = "";
        $page = "  limit $start ,$limit";

        if ($id != 'null') {
            $w = " where kc.id = $id ";
        }
        $sql = "select @row := @row + 1 as nomor, kc.*, k.nama as kabupaten, k.id as kabupaten_id FROM kecamatan kc
        left join kabupaten k on (kc.kabupaten_id = k.id)
        left join provinsi p on (k.provinsi_id = p.id), (SELECT @row := $start) rr $w
        order by nama asc ";
        $query = $this->db->query($sql . $page);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function kecamatan_add_data($data) {
        $this->db->insert('kecamatan', $data);
        return $this->db->insert_id();
    }

    function kecamatan_delete_data($id) {
        $db = "delete from kecamatan where id = '$id'";
        $this->db->query($db);
    }

    function kecamatan_edit_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('kecamatan', $data);
    }

    function kecamatan_cek_data($data) {

        $sql = "select count(*) as jumlah from kecamatan where kabupaten_id = '" . $data['kabupaten_id'] . "' 
            and nama = '" . $data['nama'] . "'";
        $query = $this->db->query($sql);
        $jml = $query->row()->jumlah;
        if ($jml == 0) {
            return true;
        } else {
            return false;
        }
    }

    function kelurahan_get_data($limit, $start, $id) {
        $w = "";
        $page = "  limit $start ,$limit";

        if ($id != 'null') {
            $w = " where kl.id = $id ";
        }
        $sql = "select @row := @row + 1 as nomor, kl.*, kc.nama as kecamatan, kc.id as id_kecamatan FROM kelurahan kl
        left join kecamatan kc on (kl.kecamatan_id = kc.id)
        left join kabupaten k on (kc.kabupaten_id = k.id)
        left join provinsi p on (k.provinsi_id = p.id) , (SELECT @row := $start) rr $w
        order by nama asc";
        $query = $this->db->query($sql . $page);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function kelurahan_data($q) {
        $sql = "select  kl.*, kc.nama as kecamatan, kb.id as id_kabupaten, kb.nama as kabupaten, p.nama as provinsi from kelurahan kl
            join kecamatan kc on (kl.kecamatan_id = kc.id)
            join kabupaten kb on (kc.kabupaten_id = kb.id)
            join provinsi p on (kb.provinsi_id = p.id)
            where kl.nama like ('%$q%') order by locate('$q', kl.nama)";
        $query = $this->db->query($sql);
        return $query->result();
    }

    function kelurahan_add_data($data) {
        $this->db->insert('kelurahan', $data);
        return $this->db->insert_id();
    }

    function kelurahan_delete_data($id) {
        $db = "delete from kelurahan where id = '$id'";
        $this->db->query($db);
    }

    function kelurahan_edit_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('kelurahan', $data);
    }

    function kelurahan_cek_data($data) {

        $sql = "select count(*) as jumlah from kelurahan where kecamatan_id = '" . $data['kecamatan_id'] . "' 
            and nama = '" . $data['nama'] . "'";
        $query = $this->db->query($sql);
        $jml = $query->row()->jumlah;
        if ($jml == 0) {
            return true;
        } else {
            return false;
        }
    }

    /* Data Wilayah */

    /* Instansi Relasi */

    function relasi_instansi_jenis_get_data() {
        $query = $this->db->get('relasi_instansi_jenis');
        return $query->result();
    }

    function instansi_get_data($limit, $start, $search) {
        $q = '';
        $limit = "limit $start, $limit";
        if (($search != 'null') & isset($search['nama'])) {
            $q = " where r.nama like '%" . $search['nama'] . "%' or kb.nama like '%" . $search['nama'] . "%' or kc.nama like '%" . $search['nama'] . "%' or rj.nama like '%" . $search['nama'] . "%'";
        }
        if (($search != 'null') & isset($search['id'])) {
            $q = " where r.id = '" . $search['id'] . "' ";
        }
        $sql = "select @row := @row + 1 as nomor, r.*, rj.id as jenis_id, rj.nama as jenis, kb.nama as kabupaten
                from relasi_instansi r
                left join relasi_instansi_jenis rj on(rj.id = r.relasi_instansi_jenis_id)
                left join kabupaten kb on (kb.id = r.kabupaten_id), (SELECT @row := $start) rr 
                $q order by r.nama asc ";
        $query = $this->db->query($sql . $limit);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function count_instansi_data() {
        $sql = "select count(id) as jumlah from relasi_instansi";
        $query = $this->db->query($sql);
        return $query->row()->jumlah;
    }

    function instansi_add_data($data) {
        $this->db->insert('relasi_instansi', $data);
        return $this->db->insert_id();
    }

    function instansi_delete_data($id) {
        $db = "delete from relasi_instansi where id = '$id'";
        $this->db->query($db);
    }

    function instansi_edit_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('relasi_instansi', $data);
    }

    function instansi_cek_data($data) {

        $instansi = "";

        if ($data['instansi'] != '') {
            $instansi = "where nama = '" . $data['instansi'] . "'";
        }

        $sql = "select count(*) as jumlah from relasi_instansi
          $instansi";
        $exe = $this->db->query($sql)->row();

        if ($exe->jumlah == 0) {
            $status = true;
        } else {
            $status = false;
        }
        die(json_encode(array('status' => $status)));
    }

    /* Instansi Relasi */

    /* User Account */

    function user_get_data($limit, $start, $search) {
        $q = '';
        if (($search != 'null') & isset($search['id'])) {
            $q = " where u.id = '" . $search['id'] . "'";
        }
        $sql = "select p.*, u.username from penduduk p 
        join users u on (p.id = u.id) $q
        order by username asc limit $start, $limit ";

        $query = $this->db->query($sql);
        return $query->result();
    }

    function count_user_data() {

        $sql = "select count(id) as jumlah from users";
        $query = $this->db->query($sql);
        return $query->row()->jumlah;
    }

    function biaya_apoteker_load_data() {
        $sql = "select t.id, l.nama as layanan, t.bobot, t.nominal, l.kelas from tarif t
        join layanan l on (t.layanan_id = l.id)
        where l.nama = 'Pelayanan Resep'";
        return $this->db->query($sql);
    }

    function user_add_data($data) {
        $this->db->insert('users', $data);
    }

    function user_delete_data($id) {
        $db = "delete from users where id = '$id'";
        $this->db->query($db);
    }

    function detail_user_data($id) {
        $sql = "select * from dinamis_penduduk d left join penduduk p on (p.id = d.penduduk_id)
             where p.id = '$id'";
        // 	echo $sql;
        $query = $this->db->query($sql);
        return $query->row();
    }

    function user_privileges_data($id) {
        $sql = "select * from penduduk_privileges where 
             penduduk_id = '" . $id . "'";
        //echo $sql;
        $query = $this->db->query($sql)->result();
        $data = array();
        foreach ($query as $value) {
            $data[] = $value->privileges_id;
        }
        return $data;
    }

    function privileges_get_data() {
        $sql = "select p.*, m.nama as modul from `privileges`p 
            join module m on(p.module_id = m.id)
            order by form_nama";
        $query = $this->db->query($sql);
        return $query->result();
    }

    function privileges_edit_data($data) {

        //delete privileges
        $this->db->where('penduduk_id', $data['penduduk_id']);
        $this->db->delete('penduduk_privileges');

        // add privileges
        foreach ($data['privileges'] as $value) {
            $insert = array(
                'penduduk_id' => $data['penduduk_id'],
                'privileges_id' => $value
            );
            $this->db->insert('penduduk_privileges', $insert);
        }

        // edit unit
        $this->db->where('id', $data['penduduk_id']);
        $this->db->update('penduduk', array('unit_id' => $data['unit']));
    }

    /* User Account */

    /* Asuransi Kepesertaan */

    function asuransi_kepersertaan_get_data($id) {
        $sql = "select p.nama as pasien, p.id as id_pasien, ap.*, r.nama as prsh, ak.id as id_ak, ak.polis_no 
        from asuransi_kepesertaan ak
        join asuransi_produk ap on (ak.asuransi_produk_id = ap.id)
        join relasi_instansi r on (ap.relasi_instansi_id = r.id)
        join penduduk p on (ak.penduduk_id = p.id) 
        where p.id = '$id'";
        $query = $this->db->query($sql);
        return $query->result();
    }

    function asuransi_kepesertaan_delete_data($id) {
        $this->db->where('id', $id);
        $this->db->delete('asuransi_kepesertaan');
    }

    function asuransi_kepesertaan_add_data($data) {
        foreach ($data['id_produk'] as $k => $val) {
            if ($val != '') {
                $add = array(
                    'penduduk_id' => $data['id_penduduk'],
                    'asuransi_produk_id' => $val,
                    'polis_no' => $data['no'][$k]
                );
                $this->db->insert('asuransi_kepesertaan', $add);
            }
        }
    }

    /* Asuransi Kepesertaan */

    /* barang */

    function kategori_barang_get_data($id = null) {
        $q = null;
        if ($id != null) {
            $q = "where id = '$id'";
        }
        $sql = "select * from barang_kategori $q";
        $query = $this->db->query($sql);

        return $query->result();
    }

    function satuan_get_data($id = null) {
        $q = null;
        if ($id != null) {
            $q.=" where id = '$id'";
        }
        $sql = "select * from satuan $q order by nama asc";
        $query = $this->db->query($sql)->result();
        $data[''] = 'Pilih Satuan';
        foreach ($query as $value) {
            $data[$value->id] = $value->nama;
        }
        return $data;
    }

    function sediaan_get_data($id = null) {
        $q = null;
        if ($id != null) {
            $q.=" where id = '$id'";
        }
        $sql = "select * from sediaan $q order by nama asc";
        $query = $this->db->query($sql)->result();
        $data[''] = 'Pilih Sediaan';
        foreach ($query as $value) {
            $data[$value->id] = $value->nama;
        }
        return $data;
    }

    function perundangan_get_data() {
        return array(
            '' => 'Pilih',
            'Bebas' => 'Bebas',
            'Bebas Terbatas' => 'Bebas Terbatas',
            'OWA' => 'OWA',
            'Keras' => 'Keras',
            'Psikotropika' => 'Psikotropika',
            'Narkotika' => 'Narkotika'
        );
    }

    function barang_get_data($limit = null, $start = null, $status = null, $id = null, $nama = null, $pabrik = null, $sort = null, $indikasi = null, $dosis = null, $kandungan = null) {
        $q = null;
        if ($status != null) {
            if ($status == 'Obat') {
                $q.=" where bk.nama = 'Obat'";
            } else {
                $q.=" where (bk.nama != 'Obat' or bk.nama IS NULL)";
            }
        }
        if ($id != null and $status != null) {
            $q.=" and b.id = '$id'";
        } else if ($id != null and $status == null) {
            $q.=" where b.id = '$id'";
        }
        if (($nama != null) & ($nama != '')) {
            $q.=" and b.nama like ('%$nama%')";
        }
        if ($pabrik != null) {
            $q.=" and b.pabrik_relasi_instansi_id = '$pabrik'";
        }
        if ($indikasi != null) {
            $q.=" and o.indikasi like ('%$indikasi%')";
        }
        if ($dosis != null) {
            $q.=" and o.dosis like ('%$dosis%')";
        }
        if ($kandungan != null) {
            $q.=" and o.kandungan like ('%$kandungan%')";
        }
        if ($sort != null) {
            if ($sort == 'asc') {
                $q.=" order by b.nama asc";
            } else {
                $q.=" order by b.nama desc";
            }
        }
        if ($sort == null) {
            $q.=" order by b.nama asc";
        }
        $limitation = null;
        $limitation.=" limit $start , $limit";



        $sql = "select o.*, b.*, bk.nama as kategori, r.id as id_pabrik, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan from barang b
        left join barang_kategori bk on (b.barang_kategori_id = bk.id)
        left join relasi_instansi r on (b.pabrik_relasi_instansi_id = r.id)
        left join obat o on (b.id = o.id)
        left join satuan s on (s.id = o.satuan_id)
        left join sediaan sd on (sd.id = o.sediaan_id)";
        $query = $this->db->query($sql . $q . $limitation);
        $queryAll = $this->db->query($sql . $q);
        $data['data'] = $query->result();
        $data['jumlah'] = $queryAll->num_rows();
        return $data;
    }

    function barang_delete_data($id, $tipe) {
        // tipe = obat, non_obat
        $db = "delete from barang where id = '$id'";
        $this->db->query($db);

        // delete obat
        if ($tipe == 'obat') {
            $db = "delete from obat where id = '$id'";
            $this->db->query($db);
        }
    }

    function barang_non_cek_data($data) {
        $sql = "select count(*) as jumlah from barang 
            where nama = '" . $data['nama'] . "' ";
        $query = $this->db->query($sql);
        $jml = $query->row()->jumlah;
        if ($jml == 0) {
            return true;
        } else {
            return false;
        }
    }

    function barang_add_data($data, $tipe) {
        $cek = $this->db->query("select count(*) as jumlah from barang")->row();
        $this->db->insert('barang', $data['barang']);
        if ($tipe == 'Obat') {
            $data['obat']['id'] = $this->db->insert_id();
            $this->db->insert('obat', $data['obat']);
        }
        return $this->db->insert_id();
    }

    function barang_edit_data($data, $tipe) {
        $this->db->where('id', $data['barang']['id']);
        $this->db->update('barang', $data['barang']);
        if ($tipe == 'Obat') {
            $this->db->where('id', $data['obat']['id']);
            $this->db->update('obat', $data['obat']);
        }
    }

    function obat_cek_data($data) {
        $q = null;

        $exe = $this->db->query("select count(*) as jumlah from barang b
        join obat o on (b.id = o.id) where 
        b.nama  = '" . $data['nama'] . "' ")->row();

        if ($exe->jumlah == 0) {
            $status = true;
        } else {
            $status = false;
        }
        return $status;
    }

    /* Tarif Jasa */

    function tarif_get_data($limit, $start, $search) {
        $q = null;
        $q.=" limit " . $start . ", $limit";
        $w = '';

        if (($search != 'null') & isset($search['nama'])) {
            $w = " where l.nama like '%" . $search['nama'] . "%'";
        }
        if (($search != 'null') & isset($search['id'])) {
            $w = " where t.id = '" . $search['id'] . "'";
        }

        $sql = "select t.*, l.nama, l.bobot, l.kelas, tk.nama as kategori from tarif t
        join layanan l on (t.layanan_id = l.id)
        left join tarif_kategori tk on (t.tarif_kategori_id = tk.id) $w order by l.nama";

        $query = $this->db->query($sql . $q);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function tarif_add_data($data) {
        $this->db->insert('tarif', $data);
        return $this->db->insert_id();
    }

    function tarif_delete_data($id) {
        $db = "delete from tarif where id = '$id'";
        $this->db->query($db);
    }

    function tarif_edit_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('tarif', $data);
    }

    function tarif_cek_data($data) {
        $exe = $this->db->query("select count(*) as jumlah from tarif 
            where layanan_id = '" . $data['layanan'] . "' and 
            tarif_kategori_id = '" . $data['kategori'] . "' and 
            js = '" . $data['js'] . "' and 
            rs_tindakan_jasa = '" . $data['js_rs'] . "' and 
            profesi_layanan_tindakan_jasa_total = '" . $data['jp'] . "' and 
            bhp = '" . $data['bhp'] . "' and
            uc = '" . $data['uc'] . "' and
            profit_margin = '" . $data['margin'] . "' and
            nominal = '" . currencyToNumber($data['nominal']) . "'")->row();

        if ($exe->jumlah == 0) {
            $status = true;
        } else {
            $status = false;
        }
        return $status;
    }

    function get_jasa_profesi($id_layanan) {
        $sql = "select sum(nominal) as total from tindakan_layanan_profesi_jasa where layanan_id = '$id_layanan'";
        $query = $this->db->query($sql)->row();

        return $query;
    }

    /* Tarif Jasa */

    /* Packing Barang */

    function packing_get_data($limit, $start, $id, $cari) {
        $where = '';
        $search = '';
        $q = null;
        $q.=" limit " . $start . ", $limit";
        $order = ' order by b.nama asc';
        $sql = "select o.id as id_obat, o.generik, bp.*, s.nama as s_besar, st.nama as s_kecil, b.nama, o.kekuatan, stn.nama as satuan_obat, sd.nama as sediaan, r.nama as pabrik from barang_packing bp
        join barang b on (b.id = bp.barang_id)
        left join relasi_instansi r on (b.pabrik_relasi_instansi_id = r.id)
        left join satuan s on (s.id = bp.terbesar_satuan_id)
        left join satuan st on (st.id = bp.terkecil_satuan_id)
        left join obat o on (b.id = o.id)
        left join satuan stn on (o.satuan_id = stn.id)
        left join sediaan sd on (o.sediaan_id = sd.id) where bp.id is not NULL ";

        if ($id != 'null') {
            $where = " and bp.id = '" . $id . "' ";
        }
        if ($cari != 'null') {
            $search = " and b.nama like '%" . $cari . "%' ";
        }

        $query = $this->db->query($sql . $where . $search . $order . $q);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql . $where . $search . $order)->num_rows();
        return $ret;
    }

    function packing_add_data($data) {
        $this->db->insert('barang_packing', $data);
        $id = $this->db->insert_id();
        if ($data['barcode'] == '') {
            $edit = array(
                'barcode' => $id
            );
            $this->db->where('id', $id);
            $this->db->update('barang_packing', $edit);
        }
        return $this->db->insert_id();
    }

    function packing_delete_data($id) {
        $db = "delete from barang_packing where id = '$id'";
        $this->db->query($db);
    }

    function packing_edit_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('barang_packing', $data);
    }

    function packing_cek_data($data) {
        $sql = "select count(*) as jumlah from barang_packing
        where barcode = '" . $data['barcode'] . "' 
        and barang_id = '" . $data['id_barang'] . "' 
        and terbesar_satuan_id = '" . $data['kemasan'] . "' 
        and isi = '" . $data['isi'] . "' 
        and terkecil_satuan_id = '" . $data['satuan'] . "'";
        $exe = $this->db->query($sql)->row();

        if ($exe->jumlah == 0) {
            $status = true;
        } else {
            $status = false;
        }
        return $status;
    }

    /* Packing Barang */

    /* Layanan */

    function layanan_get_data($limit, $start, $id) {
        $q = null;
        $q.=" limit " . $start . ", $limit";
        $w = '';
        if ($id != 'null') {
            $w = " where id = '" . $id . "'";
        }

        $sql = "select @row := @row + 1 as nomor , l.* from layanan l,
            (SELECT @row := $start) rr $w order by l.nama, l.bobot, l.kelas asc";
        $query = $this->db->query($sql . $q);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function count_layanan_data() {
        $sql = "select count(id) as jumlah from layanan";
        $query = $this->db->query($sql);
        return $query->row()->jumlah;
    }

    function layanan_add_data($data) {
        $this->db->insert('layanan', $data);
        return $this->db->insert_id();
    }

    function layanan_delete_data($id) {
        $db = "delete from layanan where id = '$id'";
        $this->db->query($db);
    }

    function layanan_edit_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('layanan', $data);
    }

    function layanan_cek_data($data) {
        $sql = "select count(*) as jumlah from layanan 
            where nama = '" . $data['layanan'] . "' ";
        $exe = $this->db->query($sql)->row();
        // echo $sql;
        if ($exe->jumlah == 0) {
            $status = true;
        } else {
            $status = false;
        }
        return $status;
    }

    /* Layanan */


    /* Penduduk */

    function penduduk_get_data($limit = null, $start = null, $search = null) {
        $q = null;

        if (isset($search['nama']) && ($search['nama'] != '')) {
            $q.=" and p.nama like ('%" . $search['nama'] . "%')";
        }
        if (isset($search['alamat']) && ($search['alamat'] != '')) {
            $q.=" and d.alamat like ('%" . $search['alamat'] . "%')";
        }
        if (isset($search['telp']) && ($search['telp'] != '')) {
            $q.=" and p.telp like ('%" . $search['telp'] . "%')";
        }
        if (isset($search['kabupaten']) && ($search['kabupaten'] != '')) {
            $q.=" and p.lahir_kabupaten_id = '" . $search['kabupaten'] . "'";
        }
        if (isset($search['gender']) && ($search['gender'] != '')) {
            $q.=" and p.gender = '" . $search['gender'] . "'";
        }
        if (isset($search['gol_darah']) && ($search['gol_darah'] != '')) {
            $q.=" and p.darah_gol = '" . $search['gol_darah'] . "'";
        }
        if (isset($search['tgl_lahir']) && ($search['tgl_lahir'] != '')) {
            $q.=" and p.lahir_tanggal = '" . $search['tgl_lahir'] . "'";
        }

        if (isset($search['id'])) {
            $q.=" and p.id = '" . $search['id'] . "'";
        }

        $limitation = null;
        $limitation.="";

        $sql = "select p.*, d.*, d.identitas_no, d.id as id_dp, p.id as penduduk_id, kl.nama as kelurahan,kb.nama as kabupaten, pd.nama as pendidikan, pr.nama profesi 
            , dp.no_id from penduduk p
        left join dinamis_penduduk d on (p.id = d.penduduk_id)
        left join kabupaten kb on (p.lahir_kabupaten_id = kb.id)
        left join kelurahan kl on (d.kelurahan_id = kl.id)
        left join pendidikan pd on (d.pendidikan_id = pd.id)
        left join profesi pr on (d.profesi_id = pr.id)
        inner join (
            select identitas_no as no_id,penduduk_id, max(id) as id_max
            from dinamis_penduduk GROUP by penduduk_id
        ) dp on (dp.penduduk_id = d.penduduk_id and dp.id_max = d.id)
        where d.id is not null";
        $order = ' order by p.nama asc';


        $query = $this->db->query($sql . $q . $order . $limitation);
        $data['data'] = $query->result();
        $data['jumlah'] = $this->db->query($sql . $q)->num_rows();
        return $data;
    }

    function count_penduduk_data() {
        $sql = "select count(id) as jumlah from penduduk";
        $query = $this->db->query($sql);
        return $query->row()->jumlah;
    }

    function penduduk_add_data($data) {
        $this->db->insert('penduduk', $data['penduduk']);
        $id = $this->db->insert_id();
        $this->db->insert('dinamis_penduduk', $data['dinamis']);
        return $id;
    }

    function penduduk_delete_data($id) {
        $db_pdd = "delete from penduduk where id = '$id'";
        $this->db->query($db_pdd);
        $db_dinamis = "delete from dinamis_penduduk where penduduk_id = '$id'";
        $this->db->query($db_dinamis);
    }

    function penduduk_edit_data($data) {
        $this->db->where('id', $data['penduduk']['id']);
        $this->db->update('penduduk', $data['penduduk']);

        if ($data['dinamis']['alamat'] != $data['dinamis']['alamat_lama']) {
            unset($data['dinamis']['alamat_lama']);
            $this->db->insert('dinamis_penduduk', $data['dinamis']);
        }
    }

    function penduduk_cek_data($data) {
        $sql = "select count(*) as jumlah from penduduk 
            where nama = '" . $data['penduduk'] . "' ";
        $exe = $this->db->query($sql)->row();

        if ($exe->jumlah == 0) {
            $status = true;
        } else {
            $status = false;
        }
        return $status;
    }

    function profesi_get_data() {
        $sql = "select * from profesi order by nama";
        $data = $this->db->query($sql)->result();
        return $data;
    }

    function posisi_keluarga_get_data() {
        return array(
            '' => 'Pilih',
            'Ayah' => 'Ayah',
            'Ibu' => 'Ibu',
            'Anak' => 'Anak',
        );
    }

    function jabatan_get_data() {
        return array(
            '' => 'Pilih',
            'PSA' => 'Pemegang Saham Apotek',
            'APA' => 'Apoteker Pengelola Apotek',
            'Akuntan' => 'Akuntan',
            'Staff' => 'Staff'
        );
    }

    function penduduk_dinamis_get_data($id = null, $id_dp = null) {
        $q = null;
        $q1 = " and (d.id in (select max(id) from dinamis_penduduk group by penduduk_id) or d.id is null)";
        if ($id != NULL) {
            $q.=" and p.id = '$id'";
        }
        if ($id_dp != NULL) {
            $q.=" and d.id = '$id_dp'";
            $q1 = NULL;
        }
        $sort = ' order by d.tanggal desc';

        $sql = "select p.*, d.*, p.id as penduduk_id,pk.nama as pekerjaan, kl.nama as kelurahan, pd.nama as pendidikan, pr.nama profesi from penduduk p
        left join dinamis_penduduk d on (p.id = d.penduduk_id)
        left join kelurahan kl on (d.kelurahan_id = kl.id)
        left join pendidikan pd on (d.pendidikan_id = pd.id)
        left join profesi pr on (d.profesi_id = pr.id)
        left join pekerjaan pk on (d.pekerjaan_id = pk.id)
        where d.id is not null ";
        //echo $sql.$q;
        $query = $this->db->query($sql . $q . $sort);
        return $query->result();
    }

    function dinamis_penduduk_edit_data($data) {
        $this->db->insert('dinamis_penduduk', $data);
        $ret['id_dp'] = $this->db->insert_id();
        $ret['id'] = $data['penduduk_id'];
        return $ret;
    }

    function harga_jual_load_data($pb = null) {
        $q = null;
        if ($pb != null) {
            $q.="and br.nama like ('%$pb%') or t.hna like ('%$pb%')";
        }
        $sql = "select t.*, b.stok_minimal, date(t.waktu) as tanggal, br.nama as barang, b.margin, b.id as id_pb, br.nama as barang, b.diskon, o.kekuatan, b.isi, r.nama as pabrik, 
            s.nama as satuan, sd.nama as sediaan, st.nama as satuan_terbesar from transaksi_detail t 
            join barang_packing b on (t.barang_packing_id = b.id)
            join barang br on (br.id = b.barang_id)
            left join relasi_instansi r on (r.id = br.pabrik_relasi_instansi_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = b.terbesar_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            inner join (
                select barang_packing_id, max(id) as id_max
                from transaksi_detail where unit_id = '" . $this->session->userdata('id_unit') . "' group by barang_packing_id
            ) td on (t.barang_packing_id = td.barang_packing_id and t.id = td.id_max)
            where t.transaksi_jenis != 'Pemesanan' $q and t.unit_id = '" . $this->session->userdata('id_unit') . "' order by br.nama";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }

    function harga_jual_load_data_update($pb) {
        $q = null;
        if ($pb != null) {
            $q.="and t.barang_packing_id in ($pb)";
        }
        $sql = "select t.*, b.stok_minimal, date(t.waktu) as tanggal, br.nama as barang, b.margin, b.id as id_pb, br.nama as barang, b.diskon, o.kekuatan, b.isi, r.nama as pabrik, 
            s.nama as satuan, sd.nama as sediaan, st.nama as satuan_terbesar from transaksi_detail t 
            join barang_packing b on (t.barang_packing_id = b.id)
            join barang br on (br.id = b.barang_id)
            left join relasi_instansi r on (r.id = br.pabrik_relasi_instansi_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = b.terbesar_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            inner join (
                select barang_packing_id, max(id) as id_max
                from transaksi_detail where unit_id = '" . $this->session->userdata('id_unit') . "' group by barang_packing_id
            ) td on (t.barang_packing_id = td.barang_packing_id and t.id = td.id_max)
            where t.transaksi_jenis != 'Pemesanan' $q and t.unit_id = '" . $this->session->userdata('id_unit') . "' order by br.nama";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }

    function harga_jual_update_save() {
        $this->db->trans_begin();
        $id_pb = $this->input->post('id_pb');
        $margin = $this->input->post('margin');
        $diskon = $this->input->post('diskon');
        $stokmin= $this->input->post('stokmin');
        foreach ($id_pb as $key => $data) {
            $data_update = array(
                'margin' => $margin[$key],
                'diskon' => $diskon[$key],
                'stok_minimal' => $stokmin[$key]
            );
            $this->db->where('id', $data);
            $this->db->update('barang_packing', $data_update);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            }
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        return $result;
    }

    function setting_kas_save() {
        $data_kas = array(
            'waktu' => datetime2mysql($this->input->post('tanggal')),
            'penerimaan_pengeluaran_nama' => $this->input->post('transaksi'),
            'akhir_saldo' => currencyToNumber($this->input->post('akhir_saldo'))
        );
        $this->db->insert('kas', $data_kas);
        $id_kas = $this->db->insert_id();
        $result['id_kas'] = $id_kas;
        return $result;
    }

    function layanan_profesi_save() {
        $id_profesi = $this->input->post('id_profesi');
        $posisi = $this->input->post('posisi');
        $nominal = $this->input->post('nominal');
        foreach ($id_profesi as $key => $data) {
            if ($data != '') {
                $data_adm_pro = array(
                    'layanan_id' => $this->input->post('id_layanan'),
                    'profesi_id' => $data,
                    'posisi' => $posisi[$key],
                    'nominal' => currencyToNumber($nominal[$key])
                );
                $this->db->insert('tindakan_layanan_profesi_jasa', $data_adm_pro);
            }
        }
        $result['id_layanan'] = $this->input->post('id_layanan');
        return $result;
    }

    function layanan_profesi_delete($id_tindakan) {
        $this->db->delete('tindakan_layanan_profesi_jasa', array('id' => $id_tindakan));
    }

}
?>

