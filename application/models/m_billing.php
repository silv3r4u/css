<?php

class M_billing extends CI_Model {

    function modul_muat_data() {
        return array(
            array('0' => array('admission/', 'Admission')),
            array('1' => array('inventory/', 'Inventory')),
            array('2' => array('billing/', 'Billing')),
            array('3' => array('', 'Rekam Medik')),
            array('4' => array('referensi/', 'Referensi / Setting'))
        );
    }

    function previlege_muat_data($id_user = null, $limit = null, $status = null, $module) {
        $q = null;
        $sts = null;

        if ($status != null) {
            $sts = " and p.show_desktop = '1'";
        }
        if ($id_user != null) {
            $q.=" where pp.penduduk_id = '$id_user' $sts and modul = '$module' order by p.form_nama";
        }
        if ($limit != null) {
            $q.=" limit $limit";
        }

        $sql = "select p.*, pp.penduduk_id, pp.privileges_id from `privileges` p
            left join penduduk_privileges pp on (p.id = pp.privileges_id) $q";
        //echo $sql;
        $exe = $this->db->query($sql);
        return $exe->result();
    }

    function data_pasien_muat_data($q) {
        $sql = "select p.id as id_pasien,p.*, pd.nama, pdf.no_daftar from pasien p
            join penduduk pd on (p.id = pd.id)
            join pendaftaran pdf on (p.no_rm = pdf.pasien)
            inner join (
                select pasien, max(no_daftar) as max_no_daftar
                from pendaftaran group by pasien
            ) pdfi on (pdf.pasien = pdfi.pasien and pdf.no_daftar = pdfi.max_no_daftar)
            where p.no_rm like ('%$q%') or pd.nama like ('%$q%') order by locate ('$q',p.no_rm)";
        $exe = $this->db->query($sql);
        return $exe->result();
    }

    function get_data_pasien($no_daftar) {
        $sql = "select s.id, s.no_rm, p.no_daftar, pd.nama from pendaftaran p
                join pasien s on(p.pasien = s.no_rm)
                join penduduk pd on (s.id = pd.id)
                where p.no_daftar = '" . $no_daftar . "'";
        $exe = $this->db->query($sql);
        return $exe->row();
    }

    function data_kunjungan_muat_data($q) {

        $sql = "select p.*, pd.nama, pd.lahir_tanggal, ps.id as id_pasien, ps.no_rm, 
                (select sum(total) from penjualan where no_daftar = '$q') as total_barang,
                (select sum(tarif*frekuensi) from jasa_penjualan_detail where no_daftar = '$q') as total_jasa 
            from pendaftaran p 
            join pasien ps on (p.pasien = ps.no_rm)
            join penduduk pd on (ps.id = pd.id)
            where p.no_daftar = '$q'";
        //echo $sql;
        $exe = $this->db->query($sql);
        return $exe->result();
    }

    function total_pembayaran($no_daftar) {
        $sql = "select sum(pembulatan_bayar) as total_pembayaran from kunjungan_billing_pembayaran where no_daftar = '$no_daftar'";
        $exe = $this->db->query($sql);
        return $exe->row();
    }

    function asuransi_kepesertaan_get_data($id_pasien) {
        $sql = "select p.*, pd.nama, ap.nama as asuransi, ak.polis_no from pasien p
            join penduduk pd on (p.id = pd.id)
            join asuransi_kepesertaan ak on (ak.penduduk_id = pd.id)
            join asuransi_produk ap on (ap.id = ak.asuransi_produk_id)
            join relasi_instansi r on (ap.relasi_instansi_id = r.id)
            where p.id = '$id_pasien'";
        return $this->db->query($sql);
    }

    function penjualan_barang_load_data($id_pasien = null, $status = null) {
        $q = null;
        if ($status != null) {
            $q.=" group by p.id";
        }
        $exe = $this->db->query("select * from pendaftaran p join pasien ps on (p.pasien = ps.no_rm) where ps.id = '$id_pasien' order by p.no_daftar desc limit 1");
        $row = $exe->row();
        $sql = "select p.id as no_penjualan, o.generik, p.resep_id as status, td.*, bp.id as id_pb, bp.barcode, bp.margin, bp.diskon, b.nama as barang, st.nama as satuan_terkecil, 
            bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan from penjualan p
            join pendaftaran pdf on (pdf.no_daftar = p.no_daftar)
            join pasien ps on (ps.no_rm = pdf.pasien)
            join transaksi_detail td on (p.id = td.transaksi_id)
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join barang b on (b.id = bp.barang_id)
            left join obat o on (o.id = b.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            where p.no_daftar = '" . $row->no_daftar . "' $q";
        //echo "<pre>$sql</pre>";
        return $this->db->query($sql);
    }

    function penjualan_barang_detail_load_data($id_penjualan) {
        $sql = "select p.id as no_penjualan, o.generik, p.resep_id as status, td.*, bp.id as id_pb, bp.barcode, bp.margin, bp.diskon, b.nama as barang, st.nama as satuan_terkecil, 
            bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan from penjualan p
            join pendaftaran pdf on (pdf.no_daftar = p.no_daftar)
            join pasien ps on (ps.no_rm = pdf.pasien)
            join transaksi_detail td on (p.id = td.transaksi_id)
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join barang b on (b.id = bp.barang_id)
            left join obat o on (o.id = b.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            where p.id = '$id_penjualan' and td.transaksi_jenis = 'Penjualan'";
        //echo "<pre>$sql</pre>";
        return $this->db->query($sql);
    }

    function penjualan_jasa_detail_load_data($id_pasien) {
        $exe = $this->db->query("select * from pendaftaran p join pasien ps on (p.pasien = ps.no_rm) where ps.id = '$id_pasien' order by p.no_daftar desc limit 1");
        $row = $exe->row();
        $sql = "select jpd.*, jpd.id as id_jasa, jpd.tarif, jpd.frekuensi, (jpd.frekuensi*jpd.tarif) as subtotal, (jpd.tarif*jpd.frekuensi) as total, tk.nama as kategori, l.nama as layanan
            from pendaftaran pdf
            join jasa_penjualan_detail jpd on (pdf.no_daftar = jpd.no_daftar)
            join pasien ps on (ps.no_rm = pdf.pasien)
            join tarif t on (jpd.tarif_id = t.id)
            join tarif_kategori tk on (t.tarif_kategori_id = tk.id)
            join layanan l on (t.layanan_id = l.id)
            where jpd.no_daftar = '" . $row->no_daftar . "' order by jpd.id";
        //echo "<pre>$sql</pre>";
        return $this->db->query($sql);
    }
    
    function penjualan_jasa_load_data($param) {
        $q = null;
        if ($param['awal'] != null and $param['akhir'] != null) {
            $q.=" and date(jpd.waktu) between '".date2mysql($param['awal'])."' and '".date2mysql($param['akhir'])."'";
        }
        if ($param['nakes'] != null) {
            $q.=" and jpd.pegawai_penduduk_id = '$param[nakes]'";
        }
        $sql = "select jpd.*, jpd.id as id_jasa, jpd.tarif, jpd.frekuensi, ps.no_rm, pd.nama as pasien, (jpd.frekuensi*jpd.tarif) as subtotal, (jpd.tarif*jpd.frekuensi) as total, tk.nama as kategori, l.nama as layanan, l.bobot, l.kelas
            from pendaftaran pdf
            join jasa_penjualan_detail jpd on (pdf.no_daftar = jpd.no_daftar)
            join pasien ps on (ps.no_rm = pdf.pasien)
            join penduduk pd on (pd.id = ps.id)
            join tarif t on (jpd.tarif_id = t.id)
            join tarif_kategori tk on (t.tarif_kategori_id = tk.id)
            join layanan l on (t.layanan_id = l.id) where pdf.no_daftar is not null $q";
        //echo "<pre>$sql</pre>";
        return $this->db->query($sql);
    }

    function rawat_inap_detail_load_data($id_pasien) {
        $exe = $this->db->query("select * from pendaftaran p join pasien ps on (p.pasien = ps.no_rm) where ps.id = '$id_pasien' order by p.no_daftar desc limit 1");
        $row = $exe->row();
        $sql = "select i.*, u.nama as unit, t.kelas, t.no, t.tarif from inap_rawat_kunjungan i
            join tt t on (i.tt_id = t.id)
            join unit u on (t.unit_id = u.id)
            where i.no_daftar = '" . $row->no_daftar . "'";
        return $this->db->query($sql);
    }

    function load_data_tagihan($id_kunjungan) {
        $sql = "select id as id_nota, waktu, total, bayar, pembulatan_bayar, 
            uang_diserahkan, sisa, no_daftar 
            from kunjungan_billing_pembayaran
            where no_daftar = '$id_kunjungan'
            ";
        return $this->db->query($sql);
    }

    function load_last_data_tagihan($id_kunjungan) {
        $sql = "select * from kunjungan_billing kb
            join kunjungan_billing_pembayaran kbp on (kb.id = kbp.kunjungan_billing_id)
            where kb.no_daftar = '$id_kunjungan'  order by kbp.id desc limit 1
            ";
        return $this->db->query($sql);
    }

    function data_kunjungan_muat_data_total_jasa($no_daftar) {
        $sql = "select sum(jpd.tarif*jpd.frekuensi) as total_jasa 
            from pendaftaran p
            join jasa_penjualan_detail jpd on (jpd.no_daftar = p.no_daftar)
            join tarif t on (t.id = jpd.tarif_id)
            join layanan l on (l.id = t.layanan_id)
            where p.no_daftar = '$no_daftar'";
        $exe = $this->db->query($sql);
        return $exe->row();
    }
    
    function data_rawat_inap_tagihan($no_daftar) {
        $sql = "select sum(subtotal) as total_rawat_inap from inap_rawat_kunjungan where no_daftar = '$no_daftar'";
        return $this->db->query($sql);
    }

    function data_kunjungan_muat_data_total_barang($no_daftar) {
        $sql = "select sum(pj.total) as total_barang 
            from pendaftaran p
            join penjualan pj on (pj.no_daftar = p.no_daftar)
            where p.no_daftar = '$no_daftar'";
        $exe = $this->db->query($sql);
        return $exe->row();
    }

    function office_muat_data() {
        $sql = "select a.*, k.nama as kelurahan from rumah_sakit a
            left join kelurahan k on (k.id = a.kelurahan_id)";
        return $this->db->query($sql);
    }

    function get_tagihan_jasa($no_daftar) {
        $sql = "SELECT sum(jpd.tarif*jpd.frekuensi) as tarif_layanan, l.nama FROM jasa_penjualan_detail jpd
            join tarif t on (jpd.tarif_id = t.id)
            join layanan l on (t.layanan_id = l.id)
            where jpd.no_daftar = '$no_daftar' group by l.id";
        return $this->db->query($sql);
    }

    function get_tagihan_barang($no_daftar) {
        $sql = "select sum(pj.total) as total_barang from pendaftaran p
            join penjualan pj on (pj.no_daftar = p.no_daftar)
            where pj.no_daftar = '$no_daftar'";
        return $this->db->query($sql);
    }

    function load_data_pembayaran($id_billing_pembayaran) {
        $sql = "select id as id_nota, waktu, total, bayar, pembulatan_bayar, uang_diserahkan, sisa 
            from kunjungan_billing_pembayaran
            where id = '$id_billing_pembayaran'
            ";
        return $this->db->query($sql);
    }
    
    function load_data_rawat_inap_tagihan($no_daftar) {
        $sql = "select i.*, t.kelas, t.no, u.nama 
            from inap_rawat_kunjungan i 
            join tt t on (i.tt_id = t.id) 
            join unit u on (t.unit_id = u.id)
            where i.no_daftar = '$no_daftar'";
        return $this->db->query($sql);
    }

    function laporan_load_data($awal, $akhir, $pembayaran) {
        $q = null;
        if ($pembayaran == 'lunas') {
            $q = "and kbp.sisa = '0'";
        }
        if ($pembayaran == 'tidak') {
            $q = "and p.no_daftar not in (select no_daftar from kunjungan_billing_pembayaran)";
        }
        if ($pembayaran == 'belum') {
            $q = "and kbp.sisa > 0";
        }
        if ($pembayaran == 'belum' or $pembayaran == 'lunas') {
            $sql = "select p.*, kbp.*, ps.no_rm, pd.nama as pasien, pd.lahir_tanggal
            from pendaftaran p 
            join kunjungan_billing_pembayaran kbp on (p.no_daftar = kbp.no_daftar)
            join pasien ps on (p.pasien = ps.no_rm)
            join penduduk pd on (pd.id = ps.id)
            inner join (
                select no_daftar, max(id) as id_max from kunjungan_billing_pembayaran
                group by no_daftar
            ) kbi on (kbi.no_daftar = kbp.no_daftar and kbi.id_max = kbp.id)
            where date(kbp.waktu) between '" . datetopg($awal) . "' and '" . datetopg($akhir) . "' $q";
        }
        if ($pembayaran == 'tidak') {
            $sql = "select p.*, kbp.sisa, ps.no_rm, pd.nama as pasien, pd.lahir_tanggal
            from pendaftaran p 
            left join kunjungan_billing_pembayaran kbp on (p.no_daftar = kbp.no_daftar)
            join pasien ps on (p.pasien = ps.no_rm)
            join penduduk pd on (pd.id = ps.id)
            where p.no_daftar not in (select no_daftar from kunjungan_billing_pembayaran)";
        }
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }

    function pp_uang_save() {
        $this->db->trans_begin();
        $nama = $this->input->post('nama');
        $tanggal = datetime2mysql($this->input->post('tanggal'));

        $data_pp_uang = array(
            'dokumen_no' => $this->input->post('nodoc'),
            'tanggal' => $tanggal,
            'pegawai_penduduk_id' => $this->session->userdata('id_user'),
            'jenis' => $this->input->post('jenis')
        );
        $this->db->insert('uang_penerimaan_pengeluaran', $data_pp_uang);
        $id_transaksi = $this->db->insert_id();
        $jml = $this->input->post('jml');
        $jenis = $this->input->post('jenis');
        foreach ($nama as $key => $data) {
            if ($data != '' and $jml[$key] != '') {
                $row = $this->db->query("select akhir_saldo from kas order by id desc limit 1")->row();
                $awal = $row->akhir_saldo;
                if ($jenis == 'Penerimaan') {
                    $terima = currencyToNumber($jml[$key]);
                    $keluar = 0;
                    $akhir = $terima + $awal;
                } else {
                    $terima = 0;
                    $keluar = currencyToNumber($jml[$key]);
                    $akhir = $awal - $keluar;
                }
                //$qry = _exec("insert into kas values ('','$tanggal','$id_transaksi','Penerimaan dan Pengeluaran','$data','$awal','$terima','$keluar','$akhir')");
                $data_kas = array(
                    'waktu' => datetime2mysql($this->input->post('tanggal')),
                    'transaksi_id' => $id_transaksi,
                    'transaksi_jenis' => 'Penerimaan dan Pengeluaran',
                    'penerimaan_pengeluaran_nama' => $data,
                    'awal_saldo' => $awal,
                    'penerimaan' => $terima,
                    'pengeluaran' => $keluar,
                    'akhir_saldo' => $akhir
                );
                $this->db->insert('kas', $data_kas);
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
        $result['id_pp_uang'] = $id_transaksi;
        return $result;
    }

    function pp_uang_delete($id) {
        $this->db->trans_begin();
        $this->db->delete('uang_penerimaan_pengeluaran', array('id' => $id));
        $this->db->delete('kas', array('transaksi_id' => $id, 'transaksi_jenis' => 'Penerimaan dan Pengeluaran'));

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

    function pembayaran_save() {
        $this->db->trans_begin();
        $this->load->helper('url');
        $id_kunjungan = $this->input->post('id_kunjungan');
        if (isset($id_kunjungan) and $id_kunjungan != '') {
            $waktu = gmdate('Y-m-d H:i:s', gmdate('U') + 25200);
            $cek = $this->m_billing->load_data_tagihan($this->input->post('id_kunjungan'))->num_rows();
            if ($cek > 0) {
                $last = $this->m_billing->load_last_data_tagihan($this->input->post('id_kunjungan'))->row();
                $bayar = currencyToNumber($this->input->post('bayar'));
                $sisa_hasil = $bayar - $last->sisa;
                if ($sisa_hasil < 0) {
                    $sisa = abs($sisa_hasil);
                } else {
                    $sisa = 0;
                }
                $data = array(
                    'waktu' => $waktu,
                    'no_daftar' => $this->input->post('id_kunjungan'),
                    'total' => $last->sisa,
                    'bayar' => currencyToNumber($this->input->post('bayar')),
                    'pembulatan_bayar' => currencyToNumber($this->input->post('bulat')),
                    'uang_diserahkan' => currencyToNumber($this->input->post('serahuang')),
                    'sisa' => $sisa
                );
            } else {

                $sisa = currencyToNumber($this->input->post('bayar')) - $this->input->post('totallica');
                $data = array(
                    'waktu' => $waktu,
                    'no_daftar' => $this->input->post('id_kunjungan'),
                    'total' => $this->input->post('totallica'),
                    'bayar' => currencyToNumber($this->input->post('bayar')),
                    'pembulatan_bayar' => currencyToNumber($this->input->post('bulat')),
                    'uang_diserahkan' => currencyToNumber($this->input->post('serahuang')),
                    'sisa' => abs($sisa)
                );
            }
            $this->db->insert('kunjungan_billing_pembayaran', $data);
            $id_bayar = $this->db->insert_id();
            $sql = $this->db->query("select * from kas order by id desc limit 1");
            $get_last = $sql->row();
            $awal_saldo = isset($get_last->akhir_saldo) ? $get_last->akhir_saldo : '0';
            $kas = array(
                'waktu' => $waktu,
                'transaksi_id' => $id_bayar,
                'transaksi_jenis' => 'Pembayaran Billing Pasien',
                'awal_saldo' => $awal_saldo,
                'penerimaan' => currencyToNumber($this->input->post('bayar')),
                'akhir_saldo' => $awal_saldo + currencyToNumber($this->input->post('bayar'))
            );
            $this->db->insert('kas', $kas);
            //$this->db-
            $id = $this->input->post('id_kunjungan');
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        $result['id_kunjungan'] = $id;
        return $result;
    }
    
    function pendapatan_penjualan_load_data($awal, $akhir) {
        $sql = "select sum(penerimaan) as penjualan_barang from kas where transaksi_jenis like ('Penjualan%') and date(waktu) between '".  date2mysql($awal)."' and '".  date2mysql($akhir)."'";
        return $this->db->query($sql);
    }
    
    function pendapatan_jasa_load_data($awal, $akhir) {
        $sql = "select sum(rr.profesi_layanan_tindakan_jasa_total) as jasa from resep_r rr join resep r on (r.id = rr.resep_id) where date(waktu) between '".  date2mysql($awal)."' and '".  date2mysql($akhir)."'";
        return $this->db->query($sql);
    }
    
    function penerimaan_kas_load_data($awal, $akhir) {
        $sql = "select penerimaan, penerimaan_pengeluaran_nama from kas where transaksi_jenis = 'Penerimaan dan Pengeluaran' and penerimaan != '0' and date(waktu) between '".  date2mysql($awal)."' and '".  date2mysql($akhir)."'";
        return $this->db->query($sql);
    }
    
    function total_penerimaan_kas_load_data($awal, $akhir) {
        $sql = "select sum(penerimaan) as penerimaan_total from kas where transaksi_jenis = 'Penerimaan dan Pengeluaran' and date(waktu) between '".  date2mysql($awal)."' and '".  date2mysql($akhir)."'";
        return $this->db->query($sql);
    }
    
    function hna_load_data($awal, $akhir) {
        $sql = "select sum(hna*keluar) as total_hna from transaksi_detail where transaksi_jenis = 'Penjualan' and date(waktu) between '".  date2mysql($awal)."' and '".  date2mysql($akhir)."'";
        return $this->db->query($sql);
    }
    
    function pengeluaran_kas_load_data($awal, $akhir) {
        $sql = "select pengeluaran, penerimaan_pengeluaran_nama from kas where transaksi_jenis = 'Penerimaan dan Pengeluaran' and pengeluaran != '0' and date(waktu) between '".  date2mysql($awal)."' and '".  date2mysql($akhir)."'";
        return $this->db->query($sql);
    }
    function total_pengeluaran_kas_load_data($awal, $akhir) {
        $sql = "select sum(pengeluaran) as pengeluaran_total from kas where transaksi_jenis = 'Penerimaan dan Pengeluaran' and date(waktu) between '".  date2mysql($awal)."' and '".  date2mysql($akhir)."'";
        return $this->db->query($sql);
    }
    

}

?>
