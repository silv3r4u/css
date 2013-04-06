<?php

class M_resep extends CI_Model {
    
    function resep_report_muat_data($id = null, $awal = null, $akhir = null, $pasien = null, $dokter = null, $detail = null, $apoteker = NULL) {
        $group = "group by rr.id";
        $q = null;
        if ($id != null) {
            $q.=" and r.id = '$id'";
        } else {
            if ($awal != null and $akhir != null) {
                $q.= " and date(r.waktu) between '".datetopg($awal)."' and '".datetopg($akhir)."'";
            }
            if ($dokter != null) {
                $q.=" and r.dokter_penduduk_id = '$dokter'";
            }
            if ($apoteker != null) {
                $q.=" and rr.pegawai_penduduk_id = '$apoteker'";
            }
            if ($pasien != null) {
                $q.=" and r.pasien_penduduk_id = '$pasien'";
            }
        }
        if ($detail != null) {
            $q.="";
        }
        $sql = "select pd.id as id_pasien, r.*, pdk.nama as apoteker, l.nama as layanan, l.bobot, l.kelas, rr.profesi_layanan_tindakan_jasa_total, rr.r_no, rr.resep_id, pd.lahir_tanggal, bp.isi, s.nama as satuan, sd.nama as sediaan, st.nama as satuan_terbesar, ri.nama as pabrik, o.kekuatan,  rr.resep_r_jumlah, rr.tebus_r_jumlah,
            rr.pakai_aturan, rr.iter, p.nama dokter, pd.nama as pasien, b.nama as barang, bp.barcode, rrr.dosis_racik, rrr.pakai_jumlah
            from resep r
            left join penduduk p on (r.dokter_penduduk_id = p.id)
            left join penduduk pd on (r.pasien_penduduk_id = pd.id)
            left join resep_r rr on (rr.resep_id = r.id)
            left join tarif t on (rr.tarif_id = t.id)
            left join layanan l on (t.layanan_id = l.id)
            left join penduduk pdk on (pdk.id = rr.pegawai_penduduk_id)
            left join resep_racik_r_detail rrr on (rr.id = rrr.r_resep_id)
            left join barang_packing bp on (bp.id = rrr.barang_packing_id)
            left join barang b on (b.id = bp.barang_id)
            left join obat o on (o.id = b.id)
            left join sediaan sd on (o.sediaan_id = sd.id)
            left join satuan st on (bp.terbesar_satuan_id = st.id)
            left join relasi_instansi ri on (ri.id = b.pabrik_relasi_instansi_id)
            left join satuan s on (s.id = bp.terkecil_satuan_id)
            where r.id IS NOT NULL $q $group";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function cetak_etiket($id, $no_r = null) {
        $q = null;
        if ($no_r != null) {
            $q.="and rr.r_no = '$no_r'";
        }
        $sql = "select bp.id as id_packing, r.*, rr.r_no, pd.lahir_tanggal, bp.isi, s.nama as satuan, sd.nama as sediaan, st.nama as satuan_terkecil, stb.nama as satuan_terbesar, ri.nama as pabrik, o.kekuatan,  rr.resep_r_jumlah, rr.tebus_r_jumlah,
            rr.pakai_aturan, rr.iter, p.nama dokter, pd.nama as pasien, b.nama as barang, bp.barcode, rrr.dosis_racik, rrr.pakai_jumlah
            from resep r
            left join penduduk p on (r.dokter_penduduk_id = p.id)
            left join penduduk pd on (r.pasien_penduduk_id = pd.id)
            left join resep_r rr on (rr.resep_id = r.id)
            left join resep_racik_r_detail rrr on (rr.id = rrr.r_resep_id)
            left join barang_packing bp on (bp.id = rrr.barang_packing_id)
            left join barang b on (b.id = bp.barang_id)
            left join obat o on (o.id = b.id)
            left join sediaan sd on (o.sediaan_id = sd.id)
            left join satuan st on (bp.terkecil_satuan_id = st.id)
            left join satuan stb on (stb.id = bp.terbesar_satuan_id)
            left join relasi_instansi ri on (ri.id = b.pabrik_relasi_instansi_id)
            left join satuan s on (s.id = o.satuan_id)
            where r.id = '$id' $q";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function kitir_load_data($id_resep = NULL) {
        $q = null;
        if ($id_resep != null) {
            $q.="where rs.id = '$id_resep'";
        }
        $sql = "select rr.resep_id, td.transaksi_id, td.hna, td.hpp, pj.bayar, pj.id as id_penjualan, rrr.pakai_jumlah as keluar, td.ed, bp.id as id_pb, bp.barcode, bp.margin, bp.diskon, b.nama as barang, st.nama as satuan_terkecil, bp.isi, 
            o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan, pdk.nama as pegawai, pdd.nama as pasien, pdd.nama, rs.pasien_penduduk_id
            from resep rs
            join resep_r rr on (rs.id = rr.resep_id)
            join resep_racik_r_detail rrr on (rr.id = rrr.r_resep_id)
            left join penjualan pj on (pj.resep_id = rs.id)
            left join transaksi_detail td on (rrr.barang_packing_id = td.barang_packing_id)
            left join penduduk pdd on (pdd.id = rs.pasien_penduduk_id)
            left join penduduk pdk on (pdk.id = rs.dokter_penduduk_id)
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join barang b on (b.id = bp.barang_id)
            left join obat o on (o.id = b.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            inner join (
                select barang_packing_id, max(id) as id_max from transaksi_detail group by barang_packing_id
            ) tm on (tm.barang_packing_id = td.barang_packing_id and tm.id_max = td.id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id) $q";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function data_resep_load_data($id) {
        $sql = "select r.*, rr.id as id_rr, rr.tarif_id, rr.r_no, pd.nama as pasien, pd.lahir_tanggal, rr.resep_r_jumlah, rr.tebus_r_jumlah,
            rr.pakai_aturan, rr.iter, p.nama dokter, t.nominal, pd.id as pasien_id
            from resep r
            left join penduduk p on (r.dokter_penduduk_id = p.id)
            left join penduduk pd on (r.pasien_penduduk_id = pd.id)
            left join resep_r rr on (rr.resep_id = r.id)
            left join tarif t on (t.id = rr.tarif_id)
            where r.id = '$id'";
        return $this->db->query($sql);
    }
    function detail_data_resep_load_data($id_resep_r) {
        $sql = "select bp.id as id_packing, o.kekuatan, r.*, rr.r_no, pd.lahir_tanggal, bp.isi, s.nama as satuan, sd.nama as sediaan, st.nama as satuan_terkecil, 
        stb.nama as satuan_terbesar, ri.nama as pabrik, o.kekuatan,  rr.resep_r_jumlah, rr.tebus_r_jumlah,
            rr.pakai_aturan, rr.iter, p.nama dokter, pd.nama as pasien, b.nama as barang, bp.barcode, rrr.dosis_racik, rrr.pakai_jumlah
            from resep r
            left join penduduk p on (r.dokter_penduduk_id = p.id)
            left join penduduk pd on (r.pasien_penduduk_id = pd.id)
            left join resep_r rr on (rr.resep_id = r.id)
            left join resep_racik_r_detail rrr on (rr.id = rrr.r_resep_id)
            left join barang_packing bp on (bp.id = rrr.barang_packing_id)
            left join barang b on (b.id = bp.barang_id)
            left join obat o on (o.id = b.id)
            left join sediaan sd on (o.sediaan_id = sd.id)
            left join satuan st on (bp.terkecil_satuan_id = st.id)
            left join satuan stb on (stb.id = bp.terbesar_satuan_id)
            left join relasi_instansi ri on (ri.id = b.pabrik_relasi_instansi_id)
            left join satuan s on (s.id = o.satuan_id)
            where rrr.r_resep_id = '$id_resep_r'";
        //echo $sql;
        return $this->db->query($sql);
    }
    
    function statistika_resep($awal, $akhir) {
    //
        $sql = "select td.*, o.generik, date(td.waktu) as awal, s.nama as sediaan, st.nama as satuan from transaksi_detail td
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join obat o on (bp.id = o.id)
            join sediaan s on (s.id = o.sediaan_id)
            join satuan st on (st.id = bp.terbesar_satuan_id)
            where date(td.waktu) between '$awal' and '$akhir'
            and o.generik = 'Generik' and td.sisa = '0'";
        return $this->db->query($sql);
    }
    
    function get_data_pmr_penduduk($pasien) {
        $sql = "select p.*, d.*, p.id as penduduk_id, pk.nama as pekerjaan, kl.nama as kelurahan, pd.nama as pendidikan, pr.nama profesi from penduduk p
            left join dinamis_penduduk d on (p.id = d.penduduk_id)
            left join kelurahan kl on (d.kelurahan_id = kl.id)
            left join pendidikan pd on (d.pendidikan_id = pd.id)
            left join profesi pr on (d.profesi_id = pr.id)
            left join pekerjaan pk on (pk.id = d.pekerjaan_id)
            inner join (
                select penduduk_id, max(id) as id_max
                from dinamis_penduduk group by penduduk_id
                ) tm on (d.penduduk_id = tm.penduduk_id and d.id = tm.id_max)
            where d.id = (select max(id) from dinamis_penduduk where penduduk_id = '$pasien')";

        return $this->db->query($sql);
    }
    
    function get_data_pmr_penduduk_detail($pasien) {
        $sql = "select r.waktu,rr.*, rrr.dosis_racik, dp.sip_no, dp.alamat, pdd.nama as dokter, rrr.jual_harga, rrr.pakai_jumlah, b.nama as barang, bp.margin, rrr.barang_packing_id, bp.barcode, ri.nama as pabrik, 
        o.kekuatan, st.nama as satuan_terkecil, sd.nama as sediaan, bp.isi, s.nama as satuan from resep r
            join resep_r rr on (r.id = rr.resep_id)
            join resep_racik_r_detail rrr on (rr.id = rrr.r_resep_id)
            join barang_packing bp on (rrr.barang_packing_id = bp.id)
            join barang b on (b.id = bp.barang_id)
            left join obat o on (o.id = b.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi ri on (ri.id = b.pabrik_relasi_instansi_id)
            left join penduduk pdd on (pdd.id = r.dokter_penduduk_id)
            left join dinamis_penduduk dp on (pdd.id = dp.penduduk_id)
            inner join (
                select penduduk_id, max(id) as id_max
                from dinamis_penduduk group by penduduk_id
                ) tm on (dp.penduduk_id = tm.penduduk_id and dp.id = tm.id_max)
            where r.pasien_penduduk_id = '$pasien'";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function get_jenis_rawat_by_pasien($id_pasien, $no_rm) {
        $cek = $this->db->query("select no_daftar, jenis_rawat from pendaftaran where pasien = '$no_rm' order by no_daftar desc limit 1")->row(); // mengecek jenis_rawat
        $cek2= $this->db->query("select * from inap_rawat_kunjungan where no_daftar = '".$cek->no_daftar."' order by id desc limit 1")->row(); // mengecek rawat inap atau tidak
        if (isset($cek2->id)) {
            if ($cek2->keluar_waktu == NULL) {
                $result = 'Rawat Inap';
            }
            if ($cek2->keluar_waktu != NULL) {
                $result = NULL;
            }
        }
        else if (!isset($cek2->id)) {
            if ($cek->jenis_rawat == 'IGD') {
                $result = 'IGD';
            }
            if ($cek->jenis_rawat == 'Rawat Jalan') {
                $result = 'Rawat Jalan';
            }
        }
        else {
            $result = NULL;
        }
        return $result;
    }
    
    function data_item_obat($generik, $formularium = null, $stok = null, $awal = null, $akhir = null) {
        $q=null; $stk=null; $sisa=null;
        if ($formularium != null) {
            $q.="and o.formularium = '$formularium'";
        }
        if ($stok != null) {
            $stk="left join transaksi_detail td on (td.barang_packing_id = b.id)
            inner join (
                select barang_packing_id, max(id) as id_max
                from transaksi_detail group by barang_packing_id
            ) tm on (td.barang_packing_id = tm.barang_packing_id and td.id = tm.id_max)";
            $sisa=" and td.sisa > 0 and date(td.waktu) between '".date2mysql($awal)."' and '".date2mysql($akhir)."'";
        }
        $sql = "select distinct(o.id) 
            from obat o 
            left join barang_packing b on (o.id = b.barang_id) $stk
            where o.generik = '$generik' $q $sisa";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function pelayanan_resep($generik, $jenis, $formularium = null) {
        $q = null;
        if ($formularium != null) {
            $q.="and rrr.formularium = '$formularium'";
        }
        $sql = "select distinct(r.id) from resep r
            join resep_r rr on (r.id = rr.resep_id)
            join resep_racik_r_detail rrr on (rr.id = rrr.r_resep_id)
            join barang_packing b on (rrr.barang_packing_id = b.id) 
            join obat o on (b.id = o.id)
            where o.generik = '$generik' and r.jenis = '$jenis' $q";
        return $this->db->query($sql);
    }
    
    function data_resep_muat_data($id) {
        $sql = "select r.*, rr.id as id_rr, rr.tarif_id, t.nominal, rr.r_no, pd.nama as pasien, pd.lahir_tanggal, rr.resep_r_jumlah, rr.tebus_r_jumlah,
            rr.pakai_aturan, rr.iter, p.nama as dokter, t.nominal, pd.id as pasien_id
            from resep r
            left join penduduk p on (r.dokter_penduduk_id = p.id)
            left join penduduk pd on (r.pasien_penduduk_id = pd.id)
            left join resep_r rr on (rr.resep_id = r.id)
            left join tarif t on (t.id = rr.tarif_id)
            where r.id = '$id'";
        return $this->db->query($sql);
    }
    
    function detail_data_resep_muat_data($id_resep_r) {
        $sql = "select bp.id as id_packing, o.kekuatan, r.*, rr.r_no, pd.lahir_tanggal, bp.isi, s.nama as satuan, sd.nama as sediaan, st.nama as satuan_terkecil, 
        stb.nama as satuan_terbesar, ri.nama as pabrik, o.kekuatan,  rr.resep_r_jumlah, rr.tebus_r_jumlah,
            rr.pakai_aturan, rr.iter, p.nama dokter, pd.nama as pasien, b.nama as barang, bp.barcode, rrr.dosis_racik, rrr.pakai_jumlah
            from resep r
            left join penduduk p on (r.dokter_penduduk_id = p.id)
            left join penduduk pd on (r.pasien_penduduk_id = pd.id)
            left join resep_r rr on (rr.resep_id = r.id)
            left join resep_racik_r_detail rrr on (rr.id = rrr.r_resep_id)
            left join barang_packing bp on (bp.id = rrr.barang_packing_id)
            left join barang b on (b.id = bp.barang_id)
            left join obat o on (o.id = b.id)
            left join sediaan sd on (o.sediaan_id = sd.id)
            left join satuan st on (bp.terkecil_satuan_id = st.id)
            left join satuan stb on (stb.id = bp.terbesar_satuan_id)
            left join relasi_instansi ri on (ri.id = b.pabrik_relasi_instansi_id)
            left join satuan s on (s.id = o.satuan_id)
            where rrr.r_resep_id = '$id_resep_r'";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
}
?>
