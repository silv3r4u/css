<?php

class M_inv_autocomplete extends CI_Model {

    function load_data_instansi_relasi($jenis, $q) {
        
        $sql = "select i.*, j.nama as jenis from relasi_instansi i
        join relasi_instansi_jenis j on (i.relasi_instansi_jenis_id = j.id)
        where i.nama like ('%$q%') and j.nama = '$jenis' order by locate('$q', i.nama)";
        return $this->db->query($sql);
    }

    function load_data_penduduk($jenis = null, $q) {
        $sort = null;
        if ($jenis != NULL) {
            $sort.=" and pf.nama = '$jenis'";
        }
        $sql = "select p.nama, dp.*, kl.nama as kelurahan from penduduk p
        join dinamis_penduduk dp on (p.id = dp.penduduk_id)
        left join kelurahan kl on (kl.id = dp.kelurahan_id)
        left join pekerjaan pf on (pf.id = dp.pekerjaan_id)
        where p.nama like ('%$q%') $sort and dp.id in (select max(id) from dinamis_penduduk group by penduduk_id) order by locate('$q', p.nama)";
        return $this->db->query($sql);
    }

    function load_penduduk($q) {
        $sql = "select p.*, d.alamat from penduduk p
            left join dinamis_penduduk d on (p.id = d.penduduk_id)
            where d.id in (select max(id) from dinamis_penduduk group by penduduk_id)
            and p.nama like ('%$q%') order by locate('$q', p.nama)";
        return $this->db->query($sql);
    }

    function load_data_penduduk_pasien($name = null, $id = null) {
        if ($id != NULL) {
            $q = " where p.id = '$id'";
        } else if ($name != null) {
            $q = "where p.nama like ('%$name%')";
        }
        $sql = "select p.id as id_penduduk, p.nama, p.lahir_tanggal, p.lahir_kabupaten_id, p.telp, p.member, pk.nama as pekerjaan, dp.*, kb.nama as kabupaten from penduduk p
        join dinamis_penduduk dp on (p.id = dp.penduduk_id)
        left join kabupaten kb on (p.lahir_kabupaten_id = kb.id)
        left join pekerjaan pk on (dp.pekerjaan_id = pk.id)
        inner join (
            select penduduk_id, max(id) as id_max
            from dinamis_penduduk group by penduduk_id
        ) idp on (idp.penduduk_id = dp.penduduk_id and idp.id_max = dp.id)
        $q ";
//echo $sql;
        return $this->db->query($sql);
    }

    function load_data_penduduk_asuransi($id_penduduk) {
        $query = $this->db->query("select * from asuransi_kepesertaan a join asuransi_produk p on (a.asuransi_produk_id = p.id) where a.penduduk_id = '$id_penduduk'")->result();
        foreach ($query as $key => $rows) {
            echo++$key . " " . $rows->nama . "<br/>";
        }
    }

    function load_data_penduduk_profesi($jenis = null, $q, $jns = null) {
        $sort = null;
        if ($jenis != NULL) {
            $sort.=" and pf.nama = '$jenis'";
        }
        if ($jns != NULL) {
            $sort.=" and pf.jenis = '$jns'";
        }
        $sql = "select p.nama, p.id as id_penduduk, dp.*, kb.nama as kabupaten, pf.nama as  profesi from penduduk p
        join dinamis_penduduk dp on (p.id = dp.penduduk_id)
        left join kabupaten kb on (kb.id = dp.kabupaten_id)
        left join profesi pf on (pf.id = dp.profesi_id)
        where p.nama like ('%$q%') $sort and dp.id in (select max(id) from dinamis_penduduk group by penduduk_id) order by locate('$q', p.nama)";
//echo $sql;
        return $this->db->query($sql);
    }
    
    function load_data_user_system($q) {
        $sql = "select p.*, dp.alamat from penduduk p left join users u on (p.id = u.id) 
            join dinamis_penduduk dp on (dp.penduduk_id = p.id)
            inner join (
                select penduduk_id, max(id) as id_max
                from dinamis_penduduk GROUP by penduduk_id
            ) tm on (tm.penduduk_id = dp.penduduk_id and tm.id_max = dp.id)
            where p.nama like ('%$q%') and p.id not in (select id from users) order by locate('$q', p.nama)";
        return $this->db->query($sql);
    }

    function load_data_packing_barang($q, $extra_param = null) {
        $param = NULL;
        if ($extra_param != NULL) {
            $param.=" and b.id = '$extra_param'";
        }
        $sql = "select o.id as id_obat, b.hna, o.generik, bp.*, r.nama as pabrik, b.id as id_barang, sd.nama as sediaan, b.nama, s.nama as satuan, st.nama as satuan_terkecil, stb.nama as satuan_terbesar, o.kekuatan from barang_packing bp
        join barang b on (b.id = bp.barang_id)
        left join obat o on (b.id = o.id)
        left join satuan s on (s.id = o.satuan_id)
        left join satuan st on (st.id = bp.terkecil_satuan_id)
        left join satuan stb on (stb.id = bp.terbesar_satuan_id)
        left join sediaan sd on (sd.id = o.sediaan_id)
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        where b.nama like ('%$q%') $param order by locate ('$q', b.nama)";
        return $this->db->query($sql);
    }
    
    function load_data_packing_barang_obat($q) {
        $sql = "select o.id as id_obat, o.generik, bp.*, r.nama as pabrik, b.id as id_barang, sd.nama as sediaan, b.nama, s.nama as satuan, st.nama as satuan_terkecil, stb.nama as satuan_terbesar, o.kekuatan from barang_packing bp
        join barang b on (b.id = bp.barang_id)
        join obat o on (b.id = o.id)
        left join satuan s on (s.id = o.satuan_id)
        left join satuan st on (st.id = bp.terkecil_satuan_id)
        left join satuan stb on (stb.id = bp.terbesar_satuan_id)
        left join sediaan sd on (sd.id = o.sediaan_id)
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        where b.nama like ('%$q%') order by locate ('$q', b.nama)";
        return $this->db->query($sql);
    }
    
    function load_data_packing_barang_where_stok_ready($q, $extra_param = null) {
        $sql = "select o.id as id_obat, o.generik, bp.*, r.nama as pabrik, td.ed, td.sisa, td.hpp, td.hna, td.ppn, b.id as id_barang, sd.nama as sediaan, b.nama, s.nama as satuan, st.nama as satuan_terkecil,
            o.kekuatan from transaksi_detail td
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join barang b on (bp.barang_id = b.id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            inner join (
                    select barang_packing_id, max(id) as id_max, max(ed) as ed_max
                    from transaksi_detail  group by barang_packing_id, ed
                    ) tm on (td.barang_packing_id = tm.barang_packing_id and td.id = tm.id_max and td.ed = tm.ed_max)
            where td.id is not null and b.nama like ('%$q%') and td.unit_id = '".$this->session->userdata('id_unit')."'
             and td.sisa > 0 
             group by bp.id, td.ed order by locate('$q', b.nama)";
        return $this->db->query($sql);
    }
    
    function load_data_packing_barang_per_ed($q, $extra_param = null) {
        $param = NULL;
        if ($extra_param != NULL) {
            $param.=" and b.id = '$extra_param'";
        }
        $sql = "select o.id as id_obat, o.generik, bp.*, r.nama as pabrik, td.ed, td.sisa, td.hpp, td.hna, td.ppn, b.id as id_barang, sd.nama as sediaan, b.nama, s.nama as satuan, st.nama as satuan_terkecil, 
            stb.nama as satuan_terbesar, o.kekuatan from barang_packing bp
            join barang b on (b.id = bp.barang_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join satuan stb on (stb.id = bp.terbesar_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            join transaksi_detail td on (td.barang_packing_id = bp.id)
            inner join (
                select barang_packing_id, max(id) as id_max from transaksi_detail group by barang_packing_id, ed
            ) tm on (td.barang_packing_id = tm.barang_packing_id and td.id = tm.id_max)
            where b.nama like ('%$q%') and td.sisa > 0 and td.ed > '".date("Y-m-d")."' and td.unit_id = '".$this->session->userdata('id_unit')."' $param group by bp.id, td.ed order by locate ('$q', b.nama)";
        return $this->db->query($sql);
    }
    
    function distribusi_load_data($id, $id_pb = NULL) {
        $q = null;
        if ($id_pb != null) {
            $q.=" and barang_packing_id = '$id_pb'";
        }
        $sql = "select o.id as id_obat, o.generik, td.*, bp.id as id_pb, b.nama as barang, st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, 
            s.nama as satuan, sd.nama as sediaan, u.nama as unit, pd.nama as pegawai from transaksi_detail td
            join distribusi d on (d.id = td.transaksi_id)
            join penduduk pd on (pd.id = d.pegawai_penduduk_id)
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join barang b on (bp.barang_id = b.id)
            left join obat o on (b.id = o.id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join unit u on (d.tujuan_unit_id = u.id)
            where td.transaksi_id = '$id' $q and td.transaksi_jenis = 'Distribusi'";
        return $this->db->query($sql);
    }

    function load_data_rop($id) {
        $start = $this->db->query("select date(waktu) as tanggal from transaksi_detail where transaksi_jenis = 'Pemesanan' and barang_packing_id = '$id' order by waktu desc limit 1")->row();
        $end   = $this->db->query("select date(waktu) as tanggal from transaksi_detail where transaksi_jenis = 'Pembelian' and barang_packing_id = '$id' order by waktu desc limit 1")->row();
        
        $sql = "select id, 
            (select avg(selisih_waktu_beli) from transaksi_detail where transaksi_jenis = 'Pembelian') as selisih_waktu_beli,
            (select (sum(masuk) - sum(keluar)) as sisa from transaksi_detail where transaksi_jenis != 'Pemesanan' and barang_packing_id = '$id') as sisa, 
            (select datediff('".(isset($end->tanggal)?$end->tanggal:'0')."','".(isset($start->tanggal)?$start->tanggal:'0')."')) as leadtime_hours,
            (select ss from transaksi_detail where transaksi_jenis = 'Pembelian' and barang_packing_id = '$id' order by id desc limit 1) as ss,
            (select avg(keluar) from transaksi_detail where barang_packing_id = '$id' and transaksi_jenis in ('Pemakaian','Penjualan') and date(waktu) 
                between '".(isset($start->tanggal)?$start->tanggal:'')."' and '".(isset($end->tanggal)?$end->tanggal:'')."') as average_usage
                from transaksi_detail t
            where id = (select max(id) from transaksi_detail where barang_packing_id = '$id' and transaksi_jenis != 'Pemesanan')";
        return $this->db->query($sql)->row();
    }
    
    function get_harga_jual($id) {
        $sql = "select d.hpp, b.margin, (d.hna*(b.margin/100)+d.hna) as harga, b.diskon from transaksi_detail d 
            join barang_packing b on (d.barang_packing_id = b.id) 
            where d.transaksi_jenis != 'Pemesanan' and d.barang_packing_id = '$id' and d.unit_id = '".$this->session->userdata('id_unit')."' order by d.id desc limit 1";
        return $this->db->query($sql);
    }
    
    function get_harga_jual_barang_kemasan($id_barang, $id_kemasan) {
        $row = $this->db->query("select hna from barang where id = '$id_barang'")->row();
        $hna = $row->hna;
        $sql = "select ($hna+($hna*(margin/100)) - ($hna*(diskon/100)))*isi as harga_jual from barang_packing where barang_id = '$id_barang' and terbesar_satuan_id = '$id_kemasan'";
        return $this->db->query($sql);
    }

    function get_nomor_pemesanan($q) {
        $sql = "select p.*, r.nama as pabrik from pemesanan p
            join relasi_instansi r on (p.suplier_relasi_instansi_id = r.id)
            where p.id not in (select pemesanan_id from pembelian where pemesanan_id is not NULL) and p.dokumen_no like ('%$q%') order by locate ('$q', p.dokumen_no)";
        return $this->db->query($sql);
    }

    function get_nomor_pembelian($q) {
        $sql = "select p.total_pembelian as total, p.*, r.nama as instansi, (select sum(jumlah_bayar) from inkaso where pembelian_id = '$q') as jumlah_terbayar
            from pembelian p
            join relasi_instansi r on (p.suplier_relasi_instansi_id = r.id)
            where (p.id = '$q' or p.dokumen_no like '%$q%')";
        return $this->db->query($sql);
    }
    
    function cek_inkaso($id_pembelian) {
        $sql = "select sum(jumlah_bayar) as jumlah_terbayar, p.*, p.total_pembelian as total, r.nama as instansi 
            from inkaso i 
            join pembelian p on (i.pembelian_id = p.id) 
            join relasi_instansi r on (r.id = p.suplier_relasi_instansi_id)
            where i.pembelian_id = '$id_pembelian'";
        return $this->db->query($sql);
    }

    function get_last_transaction($id_pb, $ed) {
        $sql = "select * from transaksi_detail 
             where barang_packing_id = '$id_pb' and ed = '$ed' and unit_id = '" . $this->session->userdata('id_unit') . "' and sisa > 0 order by id desc limit 1";
        return $this->db->query($sql);
    }
    
    function get_detail_inkaso($id_inkaso) {
        $sql = "select distinct sum(td.subtotal)+(sum(td.subtotal)*(p.ppn/100))+p.materai as total, p.*, r.nama as instansi, i.*
            from inkaso i
            join pembelian p on (i.pembelian_id = p.id)
            join transaksi_detail td on (td.transaksi_id = p.id)
            join relasi_instansi r on (p.suplier_relasi_instansi_id = r.id)
            where i.id = '$id_inkaso' and td.transaksi_jenis = 'Pembelian' group by p.id";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }

    function get_nomor_distribusi($q) {
        $sql = "select distinct d.*, p.nama as pegawai, date(td.waktu) as waktu 
            from distribusi d 
            join penduduk p on (d.pegawai_penduduk_id = p.id) 
            join transaksi_detail td on (d.id = td.transaksi_id) 
            where d.id = '$q' and td.transaksi_jenis = 'Distribusi' and d.id not in (select distribusi_id from distribusi_penerimaan)";
        return $this->db->query($sql);
    }

    function get_diskon_instansi_relasi($id_instansi_relasi) {
        $sql = "select * from relasi_instansi where id = '$id_instansi_relasi'";
        return $this->db->query($sql);
    }

    function get_harga_barang_penjualan($id) {
        $sql = "select d.hpp, b.margin, (d.hna*(b.margin/100)+d.hna) as harga, b.diskon from transaksi_detail d 
            join barang_packing b on (d.barang_packing_id = b.id) 
            where d.transaksi_jenis != 'Pemesanan' and d.barang_packing_id = '$id' and d.unit_id = '" . $this->session->userdata('id_unit') . "' order by d.id desc limit 1";
        return $this->db->query($sql);
    }

    function get_penjualan_field($barcode) {
        $sql = "select d.hpp, bp.margin, (d.hna*(bp.margin/100)+d.hna) as harga, bp.diskon,
        o.id as id_obat, bp.*, r.nama as pabrik, b.id as id_barang, sd.nama as sediaan, b.nama, s.nama as satuan, st.nama as satuan_terkecil, 
        stb.nama as satuan_terbesar, o.kekuatan from transaksi_detail d 
            join barang_packing bp on (d.barang_packing_id = bp.id) 
            join barang b on (b.id = bp.barang_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join satuan stb on (stb.id = bp.terbesar_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            where d.transaksi_jenis != 'Pemesanan' and d.barang_packing_id = (select id from barang_packing where barcode = '$barcode') order by d.id desc limit 1";
        return $this->db->query($sql);
    }

    function get_layanan_jasa($q) {
        $sql = "select l.*, t.nominal, t.id as id_tarif, t.profesi_layanan_tindakan_jasa_total from layanan l
            join tarif t on (l.id = t.layanan_id)
        where l.nama like ('%$q%') or l.kelas like ('%$q%') or l.bobot like ('%$q%') order by locate ('$q',l.nama)";
        return $this->db->query($sql);
    }

    function load_data_produk_asuransi($q) {
        $sql = "select a.*, r.nama as instansi from asuransi_produk a
        join relasi_instansi r on (r.id = a.relasi_instansi_id)
        where a.nama like ('%$q%') order by locate('$q', a.nama)";
        return $this->db->query($sql);
    }

    function load_data_pabrik($q) {
        $sql = "select i.*, j.nama as jenis from relasi_instansi i
        join relasi_instansi_jenis j on (i.relasi_instansi_jenis_id = j.id)
        where i.nama like ('%$q%') and j.nama = 'Pabrik' order by locate('$q', i.nama)";
        return $this->db->query($sql);
    }

    function load_data_penduduk_dokter($q) {

        $sql = "select p.*, dp.*, p.id as penduduk_id from penduduk p
           join dinamis_penduduk dp on (p.id = dp.penduduk_id)
           join profesi pr on (pr.id = dp.profesi_id)
           where dp.id in (select max(id) from dinamis_penduduk group by penduduk_id) and pr.nama = 'Dokter' 
           and p.nama like ('%$q%') order by locate ('$q',p.nama)
        ";
        return $this->db->query($sql);
    }

    function load_data_no_resep($q) {
        $sql = "select r.*, p.nama as dokter, pd.nama as pasien from resep r
            join penduduk p on (r.dokter_penduduk_id = p.id)
            join penduduk pd on (r.pasien_penduduk_id = pd.id)
            where r.id like '%$q%' order by locate ('$q', r.id)";
        return $this->db->query($sql);
    }
    
    function load_jasa_apoteker($id_resep) {
        $sql = "select sum(t.nominal) as jasa_apoteker from resep_r rr join tarif t on (t.id = rr.tarif_id) where rr.resep_id = '$id_resep'";
        return $this->db->query($sql);
    }

    function load_penjualan_by_no_resep($noresep) {
        $sql = "select rr.*, o.generik, td.sisa, td.ed, rd.jual_harga, rd.pakai_jumlah, b.nama as barang, bp.margin, bp.diskon, rd.barang_packing_id, bp.barcode, r.nama as pabrik, 
            o.kekuatan, st.nama as satuan_terkecil, sd.nama as sediaan, bp.isi, td.keluar, s.nama as satuan, td.harga, bp.diskon as percent,
            td.hna
            from resep_r rr
                join resep_racik_r_detail rd on (rr.id = rd.r_resep_id)
                join barang_packing bp on (rd.barang_packing_id = bp.id)
                join barang b on (b.id = bp.barang_id)
                left join obat o on (o.id = b.id)
                left join satuan s on (s.id = o.satuan_id)
                left join satuan st on (st.id = bp.terkecil_satuan_id)
                left join sediaan sd on (sd.id = o.sediaan_id)
                left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
                join transaksi_detail td on (bp.id = td.barang_packing_id)
                inner join (
                    select barang_packing_id, max(id) as id_max
                    from transaksi_detail group by barang_packing_id
                    ) tm on (td.barang_packing_id = tm.barang_packing_id and td.id = tm.id_max)
                where td.transaksi_jenis != 'Pemesanan' and rr.resep_id = '$noresep'";
        return $this->db->query($sql);
    }
    
    function load_attribute_penjualan_by_resep($id_resep) {
        $sql = "select r.*, r.id as resep_id, p.nama as pasien from resep r
            join penduduk p on (r.pasien_penduduk_id = p.id)
            where r.id = '$id_resep'";
        return $this->db->query($sql);
    }

    function reretur_pembelian_load_id($id) {
        $sql = "select p.*, pdd.nama as pegawai, pdk.nama as salesman, r.nama as suplier from pembelian_retur p 
            join penduduk pdd on (p.pegawai_penduduk_id = pdd.id)
            join penduduk pdk on (p.salesman_penduduk_id = pdk.id)
            join relasi_instansi r on (r.id = p.suplier_relasi_instansi)
            where p.id like ('%$id%') order by locate ('$id', p.id)";
        return $this->db->query($sql);
    }

    function reretur_pembelian_load_data($id_retur_pembelian) {
        $sql = "select td.barang_packing_id, td.ed, td.hpp, td.masuk, td.sisa, o.id as id_obat, bp.*, r.nama as pabrik, b.id as id_barang, sd.nama as sediaan, 
            b.nama as barang, td.keluar, s.nama as satuan, st.nama as satuan_terkecil, stb.nama as satuan_terbesar, o.kekuatan from pembelian_retur p
            join transaksi_detail td on (p.id = td.transaksi_id)
            join barang_packing bp on (bp.id = td.barang_packing_id)
            join barang b on (b.id = bp.barang_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join satuan stb on (stb.id = bp.terbesar_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            where td.transaksi_jenis = 'Retur Pembelian' and p.id = '$id_retur_pembelian'";

//echo $sql;
        return $this->db->query($sql);
    }

    function get_layanan($q) {
        $sql = "select * from layanan
        where nama like ('%$q%') order by locate ('$q',nama)";
        return $this->db->query($sql);
    }

    function get_tarif_kategori($q) {
        $sql = "select * from tarif_kategori
        where nama like ('%$q%') order by locate ('$q',nama)";
        return $this->db->query($sql);
    }

//echo $sql;

    function reretur_penjualan_get_nomor($q) {
        $sql = "select p.*, pdd.nama as pegawai, pdk.nama as pembeli from penjualan_retur p 
            join penjualan pj on (p.penjualan_id = pj.id)
            join penduduk pdd on (p.pegawai_penduduk_id = pdd.id)
            left join penduduk pdk on (pj.pembeli_penduduk_id = pdk.id)
            where p.id like ('%$q%') order by locate ('$q', p.id)";
        return $this->db->query($sql);
    }

    function reretur_penjualan_table($id) {
        $sql = "
            select td.*, o.id as id_obat, bp.barcode, bp.margin, bp.isi, bp.diskon, r.nama as pabrik, b.id as id_barang, sd.nama as sediaan, 
            b.nama as barang, td.keluar, s.nama as satuan, st.nama as satuan_terkecil, stb.nama as satuan_terbesar, o.kekuatan from penjualan_retur p
            join transaksi_detail td on (p.id = td.transaksi_id)
            join barang_packing bp on (bp.id = td.barang_packing_id)
            join barang b on (b.id = bp.barang_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join satuan stb on (stb.id = bp.terbesar_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            where td.transaksi_jenis = 'Retur Penjualan' and p.id = '$id'";
        return $this->db->query($sql);
    }
    
    function get_barang($q) {
        $sql = "select b.id as id_barang, b.nama, r.nama as pabrik, s.nama as satuan2, sd.nama as sediaan, o.kekuatan, o.id as id_obat, st.nama as satuan from barang b
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (o.satuan_id = st.id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (b.pabrik_relasi_instansi_id = r.id)
            where b.nama like ('%$q%') order by locate('$q', b.nama)";
        return $this->db->query($sql);
    }
    
    function load_data_layanan_profesi($q) {
        $sql = "select * from layanan where nama like ('%$q%') order by locate('$q',nama)";
        return $this->db->query($sql);
    }
    
    function adm_layanan_profesi($id) {
        $q = null;
        if ($id != NULL) {
            $q.="where t.layanan_id = '$id'";
        }
        $sql = "
            select p.nama, t.* from tindakan_layanan_profesi_jasa t
            join profesi p on (t.profesi_id = p.id) $q
            ";
        
        return $this->db->query($sql);
    }
    
    function load_data_profesi($q) {
        $sql = "select * from profesi where jenis = 'Nakes' and nama like ('%$q%') order by locate('$q',nama)";
        return $this->db->query($sql);
    }
    
    function get_no_retur_distribusi($q) {
        $sql = "select p.*, date(p.waktu) as waktu, pdd.nama as pegawai, u.nama as unit from distribusi_retur p 
            join penduduk pdd on (p.pegawai_penduduk_id = pdd.id)
            join transaksi_detail td on (td.transaksi_id = p.id)
            join unit u on (td.unit_id = u.id)
            where p.id like ('%$q%') group by td.transaksi_id order by locate ('$q', p.id)";
        return $this->db->query($sql);
    }
    
    function load_data_retur_unit($id) {
        $sql = "select pdd.nama as pegawai,o.generik, td.barang_packing_id, td.ed, td.hpp, td.hna, td.masuk, td.sisa, o.id as id_obat, bp.*, r.nama as pabrik, b.id as id_barang, sd.nama as sediaan, 
        b.nama as barang, td.keluar, s.nama as satuan, st.nama as satuan_terkecil, stb.nama as satuan_terbesar, o.kekuatan from distribusi_retur p
        join transaksi_detail td on (p.id = td.transaksi_id)
        join penduduk pdd on (p.pegawai_penduduk_id = pdd.id)
        join barang_packing bp on (bp.id = td.barang_packing_id)
        join barang b on (b.id = bp.barang_id)
        left join obat o on (b.id = o.id)
        left join satuan s on (s.id = o.satuan_id)
        left join satuan st on (st.id = bp.terkecil_satuan_id)
        left join satuan stb on (stb.id = bp.terbesar_satuan_id)
        left join sediaan sd on (sd.id = o.sediaan_id)
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        where td.transaksi_jenis = 'Retur Distribusi' and p.id = '$id'";
        return $this->db->query($sql);
    }
    
    function hitung_detail_pemesanan($id_pb, $biaya) {
        $start = $this->db->query("select date(waktu) as tanggal from transaksi_detail where barang_packing_id = '$id_pb' and transaksi_jenis = 'Pemesanan' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1")->row();
        $end   = $this->db->query("select date(waktu) as tanggal from transaksi_detail where barang_packing_id = '$id_pb' and transaksi_jenis = 'Pembelian' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1")->row();
        
        $result['eoq'] = 0;
        if (isset($start->tanggal) and isset($end->tanggal)) {
            $var1 = $this->db->query("select avg(keluar) as keluar, avg(leadtime_hours) as leadtime_hours from transaksi_detail where barang_packing_id = '$id_pb' and transaksi_jenis = 'Pemakaian' and date(waktu) between '".$start->tanggal."' and '".$end->tanggal."'")->row();
            $var2 = $this->db->query("select avg(keluar) as keluar, avg(leadtime_hours) as leadtime_hours from transaksi_detail where barang_packing_id = '$id_pb' and transaksi_jenis = 'Penjualan' and date(waktu) between '".$start->tanggal."' and '".$end->tanggal."'")->row();
            $hpp  = $this->db->query("select avg(hpp) as hpp FROM transaksi_detail where barang_packing_id = '$id_pb' and transaksi_jenis = 'Pembelian' and date(waktu) between '".$start->tanggal."' and '".$end->tanggal."'")->row();
            if ($var1->keluar != NULL and $var2->keluar != NULL) {
                $result['eoq'] = round(sqrt((2*($var1->keluar)+($var2->keluar*$biaya))/(0.25*$hpp->hpp)),2);
            }
        }
        
        $result['eoi'] = 0;
        if (isset($start->tanggal) and isset($end->tanggal)) {
            $var1 = $this->db->query("select avg(keluar) as keluar, avg(leadtime_hours) as leadtime_hours from transaksi_detail where barang_packing_id = '$id_pb' and transaksi_jenis = 'Pemakaian' and date(waktu) between '".$start->tanggal."' and '".$end->tanggal."'")->row();
            $var2 = $this->db->query("select avg(keluar) as keluar, avg(leadtime_hours) as leadtime_hours from transaksi_detail where barang_packing_id = '$id_pb' and transaksi_jenis = 'Penjualan' and date(waktu) between '".$start->tanggal."' and '".$end->tanggal."'")->row();
            $hpp  = $this->db->query("select avg(hpp) as hpp FROM transaksi_detail where barang_packing_id = '$id_pb' and transaksi_jenis = 'Pembelian' and date(waktu) between '".$start->tanggal."' and '".$end->tanggal."'")->row();
            if ($var1->keluar != NULL and $var2->keluar != NULL) {
                $result['eoi'] = round(sqrt((2*$biaya)/(($var1->keluar+$var2->keluar)*(0.25*$hpp->hpp))),2);
            }
        }
        return $result;
    }
    
    function load_data_penjualan_jasa($id_penduduk) {
        
    }
}

?>