<?php

class Configuration extends CI_Model {

    function get_biaya_kartu() {
        $db = $this->db->get('konfigurasi');
        $biaya = $db->row();
        return $biaya->biaya_kartu;
    }

    function get_biaya_daftar() {
        $db = $this->db->get('konfigurasi');
        $biaya = $db->row();
        return $biaya->biaya_daftar;
    }

    function set_biaya_kartu($biaya) {
        $data = array('biaya_kartu', $biaya);
        $this->db->where('id', 1);
        $this->db->update('konfigurasi',$data);
    }

    function set_biaya_daftar($biaya) {
        $data = array('biaya_daftar', $biaya);
        $this->db->where('id', 1);
        $this->db->update('konfigurasi',$data);
    }
    
    function get_manager_farmasi() {
        $sql = "select * from penduduk p
            join dinamis_penduduk dp on (p.id = dp.penduduk_id)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk
                group by penduduk_id
            ) idp on (idp.penduduk_id = dp.penduduk_id and idp.id_max = dp.id)
            where dp.jabatan = 'APA' and p.unit_id = (select id from unit where nama = 'Pelayanan Farmasi')";
        return $this->db->query($sql)->row();
    }
    
    function get_apoteker($q) {
        $sql = "select p.id, p.nama, dp.sip_no from penduduk p
            join dinamis_penduduk dp on (p.id = dp.penduduk_id)
            left join profesi pf on (pf.id = dp.profesi_id)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk
                group by penduduk_id
            ) idp on (idp.penduduk_id = dp.penduduk_id and idp.id_max = dp.id)
            where pf.nama = 'Apoteker' and p.nama like ('%$q%') order by locate ('$q',p.nama)";
        return $this->db->query($sql);
    }
    
    function jenis_transaksi() {
        return array (
            '' => 'Semua Transaksi ...',
            'Inkaso' => 'Inkaso',
            'Penerimaan Retur Pembelian' => 'Penerimaan Retur Pembelian',
            'Penerimaan dan Pengeluaran' => 'Penerimaan dan Pengeluaran',
            'Pengeluaran Retur Penjualan' => 'Pengeluaran Retur Penjualan',
            'Penjualan Resep' => 'Penjualan Resep',
            'Penjualan Non Resep' => 'Penjualan Non Resep',
            'Penjualan' => 'Penjualan Total',
            'Retur Penjualan' => 'Retur Penjualan',
            'Retur Pembelian' => 'Retur Pembelian'
            
        );
    }
    
    function rumah_sakit_get_atribute() {
        $sql = "select a.*, kb.nama as kabupaten
        from apotek a
        left join kabupaten kb on (kb.id = a.kabupaten_id)";
        return $this->db->query($sql);
    }
    
    function penduduk_manager_farmasi() {
        $sql = "select p.nama, p.lahir_tanggal, dp.* from penduduk p
            join dinamis_penduduk dp on (p.id = dp.penduduk_id)
            join unit u on (p.unit_id = u.id)
            inner join (
                select penduduk_id, max(id) as id_max
                from dinamis_penduduk group by penduduk_id
            ) dm on (dp.penduduk_id = dm.penduduk_id and dp.id = dm.id_max)
            where dp.jabatan = 'Manajer' and u.nama = 'Pelayanan Farmasi'";
        return $this->db->query($sql);
    }
    
    function unit_load_data($id = null, $jenis) {
        $q = null;
        if ($id != null) {
            $q.="and id = '$id'";
        }
        $sql = "select * from unit where jenis = '$jenis' $q order by nama";
        //echo $sql;
        return $this->db->query($sql);
        
    }
    
    function instansi_relasi_load_data($id = null, $jenis = null) {
        $q = null;
        if ($id != null) {
            $q.=" and r.id = '$id'";
        }
        if ($jenis != null) {
            $q.=" and j.nama = '$jenis'";
        }
        $sql = "select r.*, j.nama as jenis from relasi_instansi r
            join relasi_instansi_jenis j on (r.relasi_instansi_jenis_id = j.id)
            where r.id is not NULL $q";
        return $this->db->query($sql);
    }
    
    function reset_data() {
        $this->db->trans_begin();
        $this->db->query("delete from pemesanan");
        $this->db->query("delete from pembelian");
        $this->db->query("delete from distribusi");
        $this->db->query("delete from penjualan");
        $this->db->query("delete from resep");
        $this->db->query("delete from transaksi_detail");
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

}

?>
