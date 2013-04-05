<?php

class M_inventory extends CI_Model {
    
    public $waktu = "";
    
    function __construct() {
        parent::__construct();
        $this->waktu = gmdate('Y-m-d H:i:s' ,gmdate('U')+25200);
    }
    
    function biaya_apoteker_by_penjualan($id) {
        $sql="select p.*, sum(t.nominal) as jasa from penjualan p
                left join resep r on (p.resep_id = r.id)
                left join resep_r rr on (r.id = rr.resep_id)
                left join tarif t on (t.id = rr.tarif_id) 
                where p.id = '$id'";
        
        return $this->db->query($sql);
    }
    
    function pp_uang_detail($id) {
        $sql = "select pp.dokumen_no, pp.tanggal, pp.jenis, k.penerimaan, k.pengeluaran, k.penerimaan_pengeluaran_nama from uang_penerimaan_pengeluaran pp
            join kas k on (pp.id = k.transaksi_id)
            where k.transaksi_jenis = 'Penerimaan dan Pengeluaran' and k.transaksi_id = '$id'";
        return $this->db->query($sql);
    }
    
    function save_pemesanan() {
        $this->db->trans_begin();
        $noDoc = get_last_id('pemesanan', 'id') . "/".date("dmY");
        $data1 = array(
            'dokumen_no' => $noDoc,
            'pegawai_penduduk_id' => $this->session->userdata('id_user'),
            'suplier_relasi_instansi_id' => $this->input->post('id_suplier'),
            'salesman_penduduk_id' => $this->input->post('id_sales')
        );
        $this->db->insert('pemesanan', $data1);
        $id_pemesanan = $this->db->insert_id();
        $bc     = $this->input->post('bc');
        $id_pb  = $this->input->post('id_pb');
        $jml    = $this->input->post('jml');
        
        foreach ($id_pb as $key => $data) {
            if ($data != '') {
                //$this->db->get_where('transaksi_detail', array('barang_packing_id' => $data, 'transaksi_jenis' => 'Pemesanan', 'unit_id' => $this->session->userdata('id_unit')), '1');
                $sql = $this->db->query("select * from transaksi_detail where barang_packing_id = '$data' and transaksi_jenis != 'Pemesanan' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1");
                $row = $sql->row();
                $data_trans = array(
                    'transaksi_id' => $id_pemesanan,
                    'transaksi_jenis' => 'Pemesanan',
                    'waktu' => datetime2mysql($this->input->post('tanggal')),
                    'ed' => isset($row->ed)?$row->ed:NULL,
                    'barang_packing_id' => $data,
                    'unit_id' => $this->session->userdata('id_unit'),
                    'awal' => isset($row->sisa)?$row->sisa:'0',
                    'masuk' => $jml[$key],
                    'keluar' => '0',
                    'sisa' => (isset($row->sisa)?$row->sisa:'0')
                );
                $this->db->insert('transaksi_detail', $data_trans);
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
        $result['id_pemesanan'] = $id_pemesanan;
        return $result;
    }
    
    function pemesanan_delete($id) {
        $this->db->trans_begin();
        $this->db->delete('pemesanan', array('id' => $id));
        $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Pemesanan'));
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status['status'] = FALSE;
        } else {
            $this->db->trans_commit();
            $status['status'] = TRUE;
        }
        return $status;
    }
    
    function pemusnahan_delete($id) {
        $this->db->trans_begin();
        $this->db->delete('pemusnahan', array('id' => $id));
        $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Pemusnahan'));
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
    
    function penjualan_delete($id) {
        $this->db->trans_begin();
        $this->db->delete('penjualan', array('id' => $id));
        $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Penjualan'));
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
    
    function pemesanan_muat_data($id = null, $id_user = NULL) {
        $q = null;
        if ($id != null) {
            $q.=" and p.id = '$id'";
        }
        if ($id_user != null) {
            $q.=" and t.unit_id = '".$this->session->userdata('id_unit')."'";
        }
        $sql = "select o.id as id_obat, o.generik, o.perundangan, p.*, b.nama as barang, bp.isi, r.nama as pabrik, o.kekuatan, bp.id as id_pb, b.id as id_barang, bp.barcode, 
        bp.isi, s.nama as satuan, pd.nama as salesman, pdd.nama as petugas, t.awal, t.masuk, sd.nama as sediaan, t.leadtime_hours, t.ss, ri.nama as suplier,
        t.sisa, t.awal, r.alamat, kl.nama as kelurahan, st.nama as satuan_terkecil, t.barang_packing_id, t.ed, t.beli_diskon_percentage, t.beli_diskon_rupiah from pemesanan p
            join transaksi_detail t on (p.id = t.transaksi_id)
            join barang_packing bp on (t.barang_packing_id = bp.id)
            join barang b on (b.id = bp.barang_id)
            left join obat o on (o.id = b.id)
            left join satuan s on (o.satuan_id = s.id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            left join relasi_instansi ri on (ri.id = p.suplier_relasi_instansi_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            join penduduk pd on (pd.id = p.salesman_penduduk_id)
            join penduduk pdd on (pdd.id = p.pegawai_penduduk_id)
            left join kelurahan kl on (kl.id = r.kelurahan_id) where t.transaksi_jenis = 'Pemesanan' $q";
        //echo "<pre>".$sql."</pre>";
            return $this->db->query($sql);
        }
        
    function pembelian_save() {
        $this->db->trans_begin();
        $data1 = array(
            'dokumen_no' => $this->input->post('nodoc'),
            'dokumen_tanggal' => date2mysql($this->input->post('tgldoc')),
            'pemesanan_id' => $this->input->post('no_pemesanan'),
            'pegawai_penduduk_id' => $this->session->userdata('id_user'),
            'suplier_relasi_instansi_id' => $this->input->post('id_suplier'),
            'ppn' => $this->input->post('ppn'),
            'materai' => currencyToNumber($this->input->post('materai')),
            'tanggal_jatuh_tempo' => datetopg($this->input->post('tempo')),
            'salesman_penduduk_id' => $this->input->post('id_sales'),
            'ada_penerima_ttd' => $this->input->post('ttd'),
            'keterangan' => $this->input->post('keterangan')
        );
        $this->db->insert('pembelian', $data1);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        }
        $id_pembelian = $this->db->insert_id();
        $pb = $this->input->post('pb');
        $id_pb = $this->input->post('id_pb');
        $ed = $this->input->post('ed');
        $harga = $this->input->post('harga');
        $disk_pr = str_replace(",", ".", $this->input->post('diskon_pr'));
        $disk_rp = $this->input->post('diskon_rp');
        $subtotal= $this->input->post('subtotal');
        $jumlah  = $this->input->post('jml');
        $het = $this->input->post('net');
        
        foreach ($id_pb as $key => $data) {
            if ($data != '') {
                $base_hna=$harga[$key];
                $hna_ppn= ($this->input->post('ppn')/100)*$base_hna;
                $hna = $base_hna+$hna_ppn;

                $base_hpp 	= ((currencyToNumber($harga[$key])*$jumlah[$key]) - ((currencyToNumber($harga[$key])*$jumlah[$key]) * ($disk_pr[$key]/100))) / ($jumlah[$key]);
                $hpp_ppn	= ($this->input->post('ppn')/100)*$base_hpp;
                $hpp 	= $base_hpp+$hpp_ppn;

                $hna = currencyToNumber($harga[$key])+(currencyToNumber($harga[$key])*($this->input->post('ppn')/100));
                //$hpp = $hna - (currencyToNumber($harga[$key]) - ($disk_pr[$key]/100)*currencyToNumber($harga[$key]));
                $hpp = $hna - ($disk_pr[$key]/100)*currencyToNumber($harga[$key]);

                if ($disk_rp[$key] == 0) {
                    $harga_terdiskon = currencyToNumber($harga[$key]) - (currencyToNumber($harga[$key])*($disk_pr[$key])/100);
                } else if ($disk_pr[$key] == 0){
                    $harga_terdiskon = currencyToNumber($harga[$key]) - $disk_rp[$key];
                }
                $jml = $this->db->query("select sisa, ed, date(waktu) as tanggal from transaksi_detail where transaksi_jenis != 'Pemesanan' and barang_packing_id = '$id_pb[$key]' and unit_id = '".$this->session->userdata('id_unit')."' and ed = '".  datetopg($ed[$key])."' order by waktu desc limit 1")->row();
                
                $cek = $this->db->query("select sisa, date(waktu) as tanggal from transaksi_detail where transaksi_jenis = 'Pemesanan' and transaksi_id = '".$this->input->post('no_pemesanan')."' order by waktu desc limit 1")->row();
                $beli= $this->db->query("select sisa, waktu from transaksi_detail where transaksi_jenis = 'Pembelian' and barang_packing_id = '$data' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1")->row();
                
                $sisa= (isset($jml->sisa)?$jml->sisa:0) + $jumlah[$key];
                $leadTime = $this->db->query("select datediff('".date("Y-m-d")."','".$cek->tanggal."') as selisih")->row();
                $sekarang = gmdate('Y-m-d' ,gmdate('U')+25200);
                $ss  = $this->db->query("select avg(sisa) as safetystock from transaksi_detail where transaksi_jenis != 'Pemesanan' and barang_packing_id = '$id_pb[$key]' and unit_id = '".$this->session->userdata('id_unit')."' and date(waktu) between '".$cek->tanggal."' and '$sekarang'")->row();
                
                $data_trans = array(
                    'transaksi_id' => $id_pembelian,
                    'transaksi_jenis' => 'Pembelian',
                    'waktu' => date2mysql($this->input->post('tgldoc')).' '.date("H:i:s"),
                    'ed' => datetopg($ed[$key]),
                    'barang_packing_id' => $data,
                    'unit_id' => $this->session->userdata('id_unit'),
                    'harga' => currencyToNumber($harga[$key]),
                    'beli_diskon_percentage' => $disk_pr[$key],
                    'beli_diskon_rupiah' => $disk_rp[$key],
                    'terdiskon_harga' => $harga_terdiskon,
                    'subtotal' => $subtotal[$key],
                    'ppn' => $this->input->post('ppn'),
                    'hna' => $hna,
                    'hpp' => $hpp,
                    'het' => currencyToNumber($het[$key]),
                    'awal' => (isset($jml->sisa)?$jml->sisa:'0'),
                    'masuk' => $jumlah[$key],
                    'sisa' => $sisa,
                    'leadtime_hours' => $leadTime->selisih,
                    'ss' => (isset($ss->safetystock)?$ss->safetystock:'0'),
                    'selisih_waktu_beli' => (isset($beli->waktu)?range_hours_between_two_dates($beli->waktu, date2mysql($this->input->post('tgldoc')).' '.date("H:i:s")):'0')
                );
                $this->db->insert('transaksi_detail', $data_trans);
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
        $result['id_pembelian'] = $id_pembelian;
        return $result;
    }
    
    function pembelian_load_data($id = null) {
        $q = null;
        if ($id != null) {
            $q.=" and p.id = '$id'";
        }
        $sql = "select td.*, o.generik, o.id as id_obat, b.nama as barang, p.id as id_pembelian, p.dokumen_no, p.dokumen_tanggal, p.id as id_pembelian, p.pemesanan_id,
            p.materai, p.ppn, p.tanggal_jatuh_tempo, p.ada_penerima_ttd, p.keterangan,
        bp.barcode, bp.isi, ri.nama as suplier, ri.id as id_suplier, st.nama as satuan_terkecil, o.kekuatan, pdd.id as id_sales, pdd.nama as salesman, s.nama as satuan, sd.nama as sediaan, r.nama as pabrik from transaksi_detail td
        join pembelian p on (td.transaksi_id = p.id)
        left join penduduk pdd on (pdd.id = p.salesman_penduduk_id)
        join barang_packing bp on (bp.id = td.barang_packing_id)
        join barang b on (b.id = bp.barang_id)
        left join obat o on (b.id = o.id)
        left join sediaan sd on (sd.id = o.sediaan_id)
        left join satuan s on (s.id = o.satuan_id)
        left join satuan st on (st.id = bp.terkecil_satuan_id)
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        left join relasi_instansi ri on (p.suplier_relasi_instansi_id = ri.id)
        where td.transaksi_jenis = 'Pembelian' $q";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function repackage_load_data($id) {
        $sql = "select td.*, o.generik, o.id as id_obat, b.nama as barang,
        bp.barcode, bp.isi, st.nama as satuan_terkecil, o.kekuatan, s.nama as satuan, sd.nama as sediaan, r.nama as pabrik from transaksi_detail td
        join barang_packing bp on (bp.id = td.barang_packing_id)
        join barang b on (b.id = bp.barang_id)
        left join obat o on (b.id = o.id)
        left join sediaan sd on (sd.id = o.sediaan_id)
        left join satuan s on (s.id = o.satuan_id)
        left join satuan st on (st.id = bp.terkecil_satuan_id)
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        where td.transaksi_jenis = 'Repackage' and td.transaksi_id = '$id'";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function repackage_save() {
        $this->db->trans_begin();
        $id_pb = $this->input->post('id_pb');
        $id_pb_hasil = $this->input->post('id_pb_hasil');
        $asal = $this->input->post('jml_asal');
        $data = $this->db->query("select transaksi_id from transaksi_detail where transaksi_jenis = 'Repackage' order by waktu desc limit 1")->row();
        if (!isset($data->transaksi_id)) {
            $transaksi_id = 1;
            $transaksi_id2= 2;
        } else {
            $transaksi_id = $data->transaksi_id + 1;
            $transaksi_id2= $data->transaksi_id + 2;
        }
        $awal= $this->db->query("select * from transaksi_detail where barang_packing_id = '$id_pb' and unit_id = '".$this->session->userdata('id_unit')."' and transaksi_jenis != 'Pemesanan' order by waktu desc limit 1")->row();
        $sisa = $awal->sisa - $this->input->post('jml_asal');
        $data_packing_awal = array(
            'transaksi_id' => $transaksi_id,
            'transaksi_jenis' => 'Repackage',
            'waktu' => $this->waktu,
            'ed' => $awal->ed,
            'barang_packing_id' => $id_pb,
            'unit_id' => $this->session->userdata('id_unit'),
            'hna' => isset($awal->hna)?$awal->hna:'0',
            'hpp' => isset($awal->hpp)?$awal->hpp:'0',
            'het' => isset($awal->het)?$awal->het:'0',
            'awal' => isset($awal->sisa)?$awal->sisa:'0',
            'masuk' => '0',
            'keluar' => $asal,
            'sisa' => $sisa,
        );
        $this->db->insert('transaksi_detail',$data_packing_awal);
        
        $awals= $this->db->query("select * from transaksi_detail where barang_packing_id = '$id_pb_hasil' and unit_id = '".$this->session->userdata('id_unit')."' and transaksi_jenis != 'Pemesanan' order by waktu desc limit 1")->row();
        $sisas = (isset($awals->sisa)?$awals->sisa:0) + $this->input->post('isi_hasil');
        $pb = $this->db->query("select * from barang_packing where id = '$id_pb'")->row();
        
            $data_packing_hasil = array(
            'transaksi_id' => $transaksi_id,
            'transaksi_jenis' => 'Repackage',
            'waktu' => $this->waktu,
            'ed' => $awal->ed,
            'barang_packing_id' => $id_pb_hasil,
            'unit_id' => $this->session->userdata('id_unit'),
            'hna' => isset($awals->hna)?$awals->hna:($awal->hna/$pb->isi),
            'hpp' => isset($awals->hpp)?$awals->hpp:($awal->hpp/$pb->isi),
            'het' => isset($awals->het)?$awals->het:($awal->hpp/$pb->isi),
            'awal' => isset($awals->sisa)?$awals->sisa:'0',
            'masuk' => $this->input->post('isi_hasil'),
            'keluar' => '0',
            'sisa' => $sisas,
        );
        $this->db->insert('transaksi_detail',$data_packing_hasil);
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        $result['id_repackage'] = $transaksi_id;
        return $result;
    }
    
    function jenis_transaksi_load_data() {
        return array(
            '' => 'Semua Transaksi ...',
            'Stok Opname' => 'Stok Opname',
            'Pemesanan' => 'Pemesanan',
            'Pembelian' => 'Pembelian',
            'Repackage' => 'Repackage',
            'Retur Pembelian' => 'Retur Pembelian',
            'Penerimaan Retur Pembelian' => 'Penerimaan Retur Pembelian',
            'Penerimaan Retur Distribusi' => 'Penerimaan Retur Distribusi',
            'Penjualan' => 'Penjualan',
            'Retur Penjualan' => 'Retur Penjualan'
        );
    }
    
    function informasi_stok_load_data($param) {
        $q = null; $last = null; $order = null; $where = null; $jml = null;
        if ($param['awal'] != null and $param['akhir'] != NULL) {
            $q.=" and date(td.waktu) between '". datetopg($param['awal'])."' and '". datetopg($param['akhir'])."'";
        }
        if ($param['id_pb'] != NULL) {
            $q.=" and td.barang_packing_id = '$param[id_pb]'";
        }
        if ($param['sediaan'] != NULL) {
            $q.=" and o.sediaan_id = '$param[sediaan]'";
        }
        if ($param['atc'] != NULL) {
            $q.=" and o.atc like ('%$param[atc]%')";
        }
        if ($param['ddd'] != NULL) {
            $q.=" and o.ddd like ('%$param[ddd]%')";
        }
        if ($param['perundangan'] != NULL) {
            $q.=" and o.perundangan = '$param[perundangan]'";
        }
        if ($param['generik'] != NULL) {
            $q.=" and o.generik = '$param[generik]'";
        }
        $unit = "and td.unit_id = '".$this->session->userdata('id_unit')."'";
        if ($param['unit'] != null) {
            $unit=" and td.unit_id = '$param[unit]'"; 
        }
        if ($param['jenis'] != NULL) {
            $q.=" and td.transaksi_jenis = '$param[jenis]'";
            $where = " where transaksi_jenis = '$param[jenis]'";
        }
        if ($param['jns_barang'] != NULL) {
            if ($param['jns_barang'] == 'Obat') {
                $q.=" and o.id is not NULL";
            }
            if ($param['jns_barang'] == 'Non Obat') {
                $q.=" and o.id is NULL";
            }
        }
        $cek = $this->db->query("select count(*) as jumlah from transaksi_detail where transaksi_jenis = 'Pembelian'")->row();
        if ($cek->jumlah > 0 and $param['sort'] == 'last') {
            $q.=" and td.transaksi_jenis != 'Pemesanan'";
        }
        if ($param['sort'] != NULL) {
            if ($param['sort'] == 'Terakhir') {
                $q.=" and td.sisa > 0";
                $last.="inner join (
                    select barang_packing_id, max(id) as id_max, max(ed) as ed_max
                    from transaksi_detail $where group by barang_packing_id, ed
                    ) tm on (td.barang_packing_id = tm.barang_packing_id and td.id = tm.id_max and td.ed = tm.ed_max)";
                $order = " group by bp.id, td.ed order by td.waktu asc";
            }
            if ($param['sort'] == 'History') {
                $lap = null; 
                if (isset($param['laporan']) and $param['laporan'] == 'abc') {
                    $jml = "sum(td.keluar) as jml_keluar, avg(td.hna*td.margin_percentage)+td.hna as harga_obat, ";
                    $lap = "group by td.barang_packing_id";
                }
                $q.=" $lap order by td.waktu asc";
            }
        }

        if ($param['sort'] == NULL) {
            $q.=" group by bp.id, td.ed order by td.id asc";
        }

        $sql = "select $jml td.*, bp.id as id_pb, o.generik, b.nama as barang, st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, 
            sd.nama as sediaan from transaksi_detail td
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join barang b on (bp.barang_id = b.id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            $last
            where td.id is not null $unit
            $q 
            $order";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function stelling_load_data($id, $awal = null, $akhir = null) {
        $q = null;
        if ($awal != null and $akhir != null) {
            $q = "and date(td.waktu) between '".date2mysql($awal)."' and '".date2mysql($akhir)."'";
        }
        $sql = "select td.*, bp.id as id_pb, o.generik, b.nama as barang, st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan from transaksi_detail td
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join barang b on (bp.barang_id = b.id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id) where td.id is NOT NULL
                and td.transaksi_jenis != 'Pemesanan' and bp.id = '$id' $q";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function stok_opname_save() {
        $this->db->trans_begin();
        $data_stok_opname = array(
            'pegawai_penduduk_id' => $this->session->userdata('id_user'),
            'alasan' => $this->input->post('alasan')
        );
        $this->db->insert('opname_stok', $data_stok_opname);
        $id_stok_opname = $this->db->insert_id();
        $pb = $this->input->post('pb');
        $id_pb = $this->input->post('id_pb');
        $ed = $this->input->post('ed');
        $hpp= currencyToNumber($this->input->post('hpp'));
        $hna= currencyToNumber($this->input->post('hna'));
        $het= currencyToNumber($this->input->post('het'));
        $sisa= $this->input->post('js');
        foreach ($id_pb as $key => $data) {
            if ($data != '') {
                $jml = $this->db->query("select * from transaksi_detail where transaksi_jenis != 'Pemesanan' and barang_packing_id = '$data' and ed = '".date2mysql($ed[$key])."' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1")->row();

                $data_transaksi_detail = array(
                    'transaksi_id' => $id_stok_opname,
                    'transaksi_jenis' => 'Stok Opname',
                    'waktu' => $this->waktu,
                    'barang_packing_id' => $data,
                    'unit_id' => $this->session->userdata('id_unit'),
                    'ed' => date2mysql($ed[$key]),
                    'ppn' => isset($jml->ppn)?$jml->sisa:'0',
                    'hna' => $hna[$key],
                    'hpp' => $hpp[$key],
                    'het' => $het[$key],
                    'awal' => '0',
                    'masuk' => '0',
                    'keluar' => '0',
                    'sisa' => $sisa[$key]
                );
                $this->db->insert('transaksi_detail', $data_transaksi_detail);
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
        $result['id_opname_stok'] = $id_stok_opname;
        return $result;
    }
    function hutang_load_data($awal = null, $akhir = null) {
        $q = null;
        if ($awal != null and $akhir != null) {
            $q.="and p.dokumen_tanggal between '".date2mysql($awal)."' and '".date2mysql($akhir)."'";
        }
        $sql = "
            select p.id, r.nama, r.alamat, sum(td.subtotal) as total, p.ppn, p.materai, p.dokumen_no, p.dokumen_tanggal from pembelian p
            join transaksi_detail td on (p.id = td.transaksi_id)
            join relasi_instansi r on (r.id = p.suplier_relasi_instansi_id)
            where td.transaksi_jenis = 'Pembelian' $q group by p.id
        ";
        //echo $sql;
        return $this->db->query($sql);
    }
    function get_data_inkaso($id = null) {
        $q = NULL;
        if ($id != null) {
            $q.=" and i.pembelian_id = '$id'";
        }
        $sql = "
            select sum(pengeluaran) as inkaso from kas k
            join inkaso i on (i.id = k.transaksi_id)
            where k.transaksi_jenis = 'Inkaso' $q
        ";
        return $this->db->query($sql);
    }
    
    function pemakaian_save() {
        $this->db->trans_begin();
        $data_pemakaian = array(
            'waktu' => datetime2mysql($this->input->post('tanggal')),
            'pegawai_penduduk_id' => $this->session->userdata('id_user')
        );
        $this->db->insert('pemakaian', $data_pemakaian);
        $id_pemakaian = $this->db->insert_id();
        $id_pb = $this->input->post('id_pb');
        $jumlah = $this->input->post('jl');
        $this->session->set_userdata(array('sisa_stok' => NULL));
        foreach ($id_pb as $key => $data) {
            if ($data != '') {
                $fefo = $this->db->query("select td.* from transaksi_detail td
                    inner join (
                        SELECT barang_packing_id, max(id) as id_max FROM `transaksi_detail` 
                        WHERE barang_packing_id = '$id_pb[$key]' and unit_id = '".$this->session->userdata('id_unit')."' group by barang_packing_id, ed
                    ) tm on (tm.barang_packing_id = td.barang_packing_id and td.id = tm.id_max) 
                    where td.sisa > 0 and td.ed > '".$this->waktu."' and td.transaksi_jenis != 'Pemesanan' order by td.ed asc")->result();
                $leadtime = $this->db->query("select leadtime_hours from transaksi_detail 
                    where unit_id = '".$this->session->userdata('id_unit')."' and barang_packing_id = '$data' and transaksi_jenis = 'Pembelian'
                        order by id desc limit 1")->row();
                    foreach ($fefo as $num => $jml) {
                        if ($this->session->userdata('sisa_stok') == NULL) {
                            $sisa = $jml->sisa - $jumlah[$key];
                            if ($sisa >= 0) {
                                $data_trans = array(
                                    'transaksi_id' => $id_pemakaian,
                                    'transaksi_jenis' => 'Pemakaian',
                                    'waktu' => $this->waktu,
                                    'barang_packing_id' => $id_pb[$key],
                                    'unit_id' => $this->session->userdata('id_unit'),
                                    'ed' => $jml->ed,
                                    'harga' => (isset($jml->harga)?$jml->harga:'0'),
                                    'ppn' => (isset($jml->ppn)?$jml->ppn:'0'),
                                    'hna' => (isset($jml->hna)?$jml->hna:'0'),
                                    'hpp' => (isset($jml->hna)?$jml->hna:'0'),
                                    'het' => (isset($jml->het)?$jml->het:'0'),
                                    'awal' => (isset($jml->sisa)?$jml->sisa:'0'),
                                    'masuk' => '0',
                                    'keluar' => $jumlah[$key],
                                    'sisa' => $sisa,
                                    'leadtime_hours' => (isset($leadtime->loadtime_hours)?$leadtime->loadtime_hours:'0')
                                );
                                $this->session->set_userdata(array('sisa_stok' => $sisa));
                                $this->db->insert('transaksi_detail', $data_trans);
                            } else if ($sisa < 0) {
                                $data_trans = array(
                                    'transaksi_id' => $id_pemakaian,
                                    'transaksi_jenis' => 'Pemakaian',
                                    'waktu' => $this->waktu,
                                    'barang_packing_id' => $id_pb[$key],
                                    'unit_id' => $this->session->userdata('id_unit'),
                                    'ed' => $jml->ed,
                                    'harga' => (isset($jml->harga)?$jml->harga:'0'),
                                    'ppn' => (isset($jml->ppn)?$jml->ppn:'0'),
                                    'hna' => (isset($jml->hna)?$jml->hna:'0'),
                                    'hpp' => (isset($jml->hna)?$jml->hna:'0'),
                                    'het' => (isset($jml->het)?$jml->het:'0'),
                                    'awal' => (isset($jml->sisa)?$jml->sisa:'0'),
                                    'masuk' => '0',
                                    'keluar' => $jml->sisa,
                                    'sisa' => '0',
                                    'leadtime_hours' => (isset($leadtime->loadtime_hours)?$leadtime->loadtime_hours:'0')
                                );
                                $this->session->set_userdata(array('sisa_stok' => $sisa));
                                $this->db->insert('transaksi_detail', $data_trans);
                            }
                        }
                        else if ($this->session->userdata('sisa_stok') < '0') {
                            $sisa = $jml->sisa - abs($this->session->userdata('sisa_stok'));
                            if ($sisa >= 0) {
                                $data_trans = array(
                                    'transaksi_id' => $id_pemakaian,
                                    'transaksi_jenis' => 'Pemakaian',
                                    'waktu' => $this->waktu,
                                    'barang_packing_id' => $id_pb[$key],
                                    'unit_id' => $this->session->userdata('id_unit'),
                                    'ed' => $jml->ed,
                                    'harga' => (isset($jml->harga)?$jml->harga:'0'),
                                    'ppn' => (isset($jml->ppn)?$jml->ppn:'0'),
                                    'hna' => (isset($jml->hna)?$jml->hna:'0'),
                                    'hpp' => (isset($jml->hna)?$jml->hna:'0'),
                                    'het' => (isset($jml->het)?$jml->het:'0'),
                                    'awal' => (isset($jml->sisa)?$jml->sisa:'0'),
                                    'masuk' => '0',
                                    'keluar' => abs($this->session->userdata('sisa_stok')),
                                    'sisa' => $sisa,
                                    'leadtime_hours' => (isset($leadtime->loadtime_hours)?$leadtime->loadtime_hours:'0')
                                );
                                $this->session->set_userdata(array('sisa_stok' => $sisa));
                                $this->db->insert('transaksi_detail', $data_trans);

                            } else if ($sisa < 0) {
                                $data_trans = array(
                                    'transaksi_id' => $id_pemakaian,
                                    'transaksi_jenis' => 'Pemakaian',
                                    'waktu' => $this->waktu,
                                    'barang_packing_id' => $id_pb[$key],
                                    'unit_id' => $this->session->userdata('id_unit'),
                                    'ed' => $jml->ed,
                                    'harga' => (isset($jml->harga)?$jml->harga:'0'),
                                    'ppn' => (isset($jml->ppn)?$jml->ppn:'0'),
                                    'hna' => (isset($jml->hna)?$jml->hna:'0'),
                                    'hpp' => (isset($jml->hna)?$jml->hna:'0'),
                                    'het' => (isset($jml->het)?$jml->het:'0'),
                                    'awal' => (isset($jml->sisa)?$jml->sisa:'0'),
                                    'masuk' => '0',
                                    'keluar' => $jml->sisa,
                                    'sisa' => '0',
                                    'leadtime_hours' => (isset($leadtime->loadtime_hours)?$leadtime->loadtime_hours:'0')
                                );
                                $this->session->set_userdata(array('sisa_stok' => $sisa));
                                $this->db->insert('transaksi_detail', $data_trans);
                            }
                        }
                        else {
                            $sisa = $jml->sisa - $jumlah[$key];
                                $data_trans = array(
                                    'transaksi_id' => $id_pemakaian,
                                    'transaksi_jenis' => 'Pemakaian',
                                    'waktu' => $this->waktu,
                                    'barang_packing_id' => $id_pb[$key],
                                    'unit_id' => $this->session->userdata('id_unit'),
                                    'ed' => $jml->ed,
                                    'harga' => (isset($jml->harga)?$jml->harga:'0'),
                                    'ppn' => (isset($jml->ppn)?$jml->ppn:'0'),
                                    'hna' => (isset($jml->hna)?$jml->hna:'0'),
                                    'hpp' => (isset($jml->hna)?$jml->hna:'0'),
                                    'het' => (isset($jml->het)?$jml->het:'0'),
                                    'awal' => (isset($jml->sisa)?$jml->sisa:'0'),
                                    'masuk' => '0',
                                    'keluar' => $jumlah[$key],
                                    'sisa' => $sisa,
                                    'leadtime_hours' => (isset($leadtime->loadtime_hours)?$leadtime->loadtime_hours:'0')
                                );
                                $this->session->set_userdata(array('sisa_stok' => $sisa));
                                $this->db->insert('transaksi_detail', $data_trans);
                        }
                    }
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
        $result['id_pemakaian'] = $id_pemakaian;
        return $result;
    }
    
    function pemakaian_delete($id) {
        $this->db->trans_begin();
        //$cek = $this->db->get_where('pemakaian', array('id' => $id))->num_rows();
        
        $this->db->delete('pemakaian', array('id' => $id));
        $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Pemakaian'));
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
    
    function stok_opname_delete($id) {
        $this->db->trans_begin();
        //$cek = $this->db->get_where('pemakaian', array('id' => $id))->num_rows();
        
        $this->db->delete('opname_stok', array('id' => $id));
        $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Stok Opname'));
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
    
    function reretur_pembelian_delete($id) {
        $this->db->trans_begin();
        //$cek = $this->db->get_where('pemakaian', array('id' => $id))->num_rows();
        
        $this->db->delete('pembelian_retur_pengeluaran', array('id' => $id));
        $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Penerimaan Retur Pembelian'));
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
    
    function retur_pembelian_delete($id) {
        $this->db->trans_begin();
        //$cek = $this->db->get_where('pemakaian', array('id' => $id))->num_rows();
        
        $this->db->delete('pembelian_retur', array('id' => $id));
        $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Retur Pembelian'));
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
    
    function pembelian_delete($id) {
        $this->db->trans_begin();
        //$cek = $this->db->get_where('pembelian', array('id' => $id))->num_rows();
        $row = $this->db->query("select id from pembelian_retur where pembelian_id = '$id'")->row();
        if (isset($row->id)) {
            $rows= $this->db->query("select id from pembelian_retur_penerimaan where retur_id = '".$row->id."'")->row();
        }
        $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Pembelian'));
        if (isset($row->id)) {
            $this->db->delete('transaksi_detail', array('transaksi_id' => $row->id, 'transaksi_jenis' => 'Retur Pembelian'));
        }
        if (isset($rows->id)) {
            $this->db->delete('transaksi_detail', array('transaksi_id' => $rows->id, 'transaksi_jenis' => 'Penerimaan Retur Pembelian'));
        }
        $this->db->delete('pembelian', array('id' => $id));
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
    
    function distribusi_delete($id) {
        $this->db->trans_begin();
        //$cek = $this->db->get_where('pembelian', array('id' => $id))->num_rows();
        $row = $this->db->query("select id from distribusi_penerimaan where distribusi_id = '$id'")->row();
        if (isset($row->id)) {
            $rows= $this->db->query("select id from distribusi_retur where penerimaan_distribusi_id = '".$row->id."'")->row();
        }
        if (isset($rows->id)) {
            $rowA= $this->db->query("select id from distribusi_retur_penerimaan where distribusi_retur_id = '".$rows->id."'")->row();
        }
        $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Distribusi'));
        if (isset($row->id)) {
            $this->db->delete('transaksi_detail', array('transaksi_id' => $row->id, 'transaksi_jenis' => 'Penerimaan Distribusi'));
        }
        if (isset($rows->id)) {
            $this->db->delete('transaksi_detail', array('transaksi_id' => $rows->id, 'transaksi_jenis' => 'Retur Distribusi'));
        }
        if (isset($rowA)) {
            $this->db->delete('transaksi_detail', array('transaksi_id' => $rowA->id, 'transaksi_jenis' => 'Penerimaan Retur Distribusi'));
        }
        $this->db->delete('distribusi', array('id' => $id));
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
    
    function distribusiGetSisa($id_pb, $ed) {
        $sql = "select * from transaksi_detail where barang_packing_id = '$id_pb' and ed = '".  date2mysql($ed)."' and unit_id = '".$this->session->userdata('id_unit')."' and sisa > 0 order by waktu desc limit 1";
        return $this->db->query($sql);
    }
    
    function distribusi_save() {
        $this->db->trans_begin();
        $data_distribusi = array(
            'unit_id' => $this->session->userdata('id_unit'),
            'tujuan_unit_id' => $this->input->post('unit'),
            'pegawai_penduduk_id' => $this->session->userdata('id_user')
        );
        $this->db->insert('distribusi', $data_distribusi);
        $id_distribusi = $this->db->insert_id();
        
        $pb = $this->input->post('pb');
        $id_pb = $this->input->post('id_pb');
        $ed = $this->input->post('ed');
        $jl = $this->input->post('jl');
        foreach ($pb as $key => $data) {
            if ($data != '') {
                $jml = $this->db->query("select * from transaksi_detail where transaksi_jenis != 'Pemesanan' 
                and barang_packing_id = '$id_pb[$key]' and ed = '".date2mysql($ed[$key])."' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1")->row();
                $sisa = (isset($jml->sisa)?$jml->sisa:'0') - $jl[$key];
                $data_trans = array(
                    'transaksi_id' => $id_distribusi,
                    'transaksi_jenis' => 'Distribusi',
                    'waktu' => datetime2mysql($this->input->post('tanggal')),
                    'barang_packing_id' => $id_pb[$key],
                    'unit_id' => $this->session->userdata('id_unit'),
                    'ed' => $jml->ed,
                    'harga' => (isset($jml->harga)?$jml->harga:'0'),
                    'ppn' => (isset($jml->ppn)?$jml->ppn:'0'),
                    'hna' => (isset($jml->hna)?$jml->hna:'0'),
                    'hpp' => (isset($jml->hna)?$jml->hna:'0'),
                    'het' => (isset($jml->het)?$jml->het:'0'),
                    'awal' => (isset($jml->sisa)?$jml->sisa:'0'),
                    'masuk' => '0',
                    'keluar' => $jl[$key],
                    'sisa' => $sisa
                );
                $this->db->insert('transaksi_detail', $data_trans);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                }
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
        $result['id_distribusi'] = $id_distribusi;
        return $result;
    }
    
    function inkaso_save() {
        $this->db->trans_begin();
        $data_inkaso = array(
            'waktu' => datetime2mysql($this->input->post('tanggal')),
            'pembelian_id' => $this->input->post('nopembelian'),
            'pegawai_penduduk_id' => $this->session->userdata('id_user'),
            'jumlah_bayar' => currencyToNumber($this->input->post('bayar'))
        );
        $this->db->insert('inkaso', $data_inkaso);
        $id_inkaso = $this->db->insert_id();
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        }
        
        $rows = $this->db->query("select * from kas order by waktu desc limit 1")->row();
        $data_kas = array(
            'waktu' => datetime2mysql($this->input->post('tanggal')),
            'transaksi_id' => $id_inkaso,
            'transaksi_jenis' => 'Inkaso',
            'awal_saldo' => $rows->akhir_saldo,
            'penerimaan' => '0',
            'pengeluaran' => currencyToNumber($this->input->post('bayar')),
            'akhir_saldo' => ($rows->akhir_saldo-currencyToNumber($this->input->post('bayar')))
        );
        $this->db->insert('kas', $data_kas);
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        $result['id_pembelian'] = $this->input->post('nopembelian');
        return $result;
    }
    
    function pemusnahan_save() {
        $this->db->trans_begin();
        
        $data_pemusnahan = array(
            'pegawai_penduduk_id' => $this->session->userdata('id_user'),
            'apotek_saksi_penduduk_id' => $this->input->post('id_sapt'),
            'bpom_saksi_penduduk_id' => $this->input->post('id_sbpom')
        );
        $this->db->insert('pemusnahan', $data_pemusnahan);
        $id_pemusnahan = $this->db->insert_id();
        
        $ed = $this->input->post('ed');
        $pb = $this->input->post('pb');
        $id_pb = $this->input->post('id_pb');
        $jl = $this->input->post('jl');
        
        foreach ($id_pb as $key => $data) {
            if ($data != '') {
                $jml = $this->db->query("select * from transaksi_detail where transaksi_jenis != 'Pemesanan' and barang_packing_id = '$id_pb[$key]' 
                and ed = '".  date2mysql($ed[$key])."' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1")->row();
                $sisa = $jml->sisa - $jl[$key];
                $data_trans = array(
                    'transaksi_id' => $id_pemusnahan,
                    'transaksi_jenis' => 'Pemusnahan',
                    'waktu' => datetime2mysql($this->input->post('tanggal')),
                    'barang_packing_id' => $data,
                    'unit_id' => $this->session->userdata('id_unit'),
                    'ed' => date2mysql($ed[$key]),
                    'harga' => (isset($jml->harga)?$jml->harga:'0'),
                    'ppn' => (isset($jml->ppn)?$jml->ppn:'0'),
                    'hna' => (isset($jml->hna)?$jml->hna:'0'),
                    'hpp' => (isset($jml->hna)?$jml->hna:'0'),
                    'het' => (isset($jml->het)?$jml->het:'0'),
                    'awal' => (isset($jml->sisa)?$jml->sisa:'0'),
                    'masuk' => '0',
                    'keluar' => $jl[$key],
                    'sisa' => $sisa
                );
                $this->db->insert('transaksi_detail', $data_trans);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $status = FALSE;
                }
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
        $result['id_pemusnahan'] = $id_pemusnahan;
        return $result;
    }
    
    function penerimaan_distribusi_save() {
        $this->db->trans_begin();
        $data_penerimaan_dist = array(
            'distribusi_id' => $this->input->post('nodistribusi'),
            'pegawai_penduduk_id' => $this->session->userdata('id_user')
        );
        $this->db->insert('distribusi_penerimaan', $data_penerimaan_dist);
        $id_dist_penerimaan = $this->db->insert_id();
        
        $pb = $this->input->post('pb');
        $id_pb = $this->input->post('id_pb');
        $ed = $this->input->post('ed');
        $jp = $this->input->post('jp');
        
        foreach ($id_pb as $key => $data) {
            if (($data != '') and ($ed[$key] != '') and ($jp[$key] != '')) {
                $rows = $this->db->query("select * from transaksi_detail where transaksi_jenis != 'Pemesanan' 
                    and barang_packing_id = '$data' and ed = '".date2mysql($ed[$key])."' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1")->row();
                /*if (!isset($rows->id)) {
                    $dist = $this->db->query("select * from distribusi where id = '".$this->input->post('nodistribusi')."'")->row();
                    $rows = $this->db->query("select * from transaksi_detail where transaksi_jenis != 'Pemesanan' 
                        and barang_packing_id = '$data' and ed = '".date2mysql($ed[$key])."' and unit_id = '".$dist->unit_id."' order by waktu desc limit 1")->row();
                }*/
                $new_sisa = (isset($rows->sisa)?$rows->sisa:'0') + $jp[$key];
                $new_ed  = isset($rows->barang_packing_id)?date2mysql($ed[$key]):isset($rows->ed)?$rows->ed:NULL;
                
                $data_trans = array(
                    'transaksi_id' => $id_dist_penerimaan,
                    'transaksi_jenis' => 'Penerimaan Distribusi',
                    'waktu' => datetime2mysql($this->input->post('tanggal')),
                    'barang_packing_id' => $data,
                    'unit_id' => $this->session->userdata('id_unit'),
                    'ed' => $new_ed,
                    'harga' => (isset($rows->harga)?$rows->harga:'0'),
                    'ppn' => (isset($rows->ppn)?$rows->ppn:'0'),
                    'hna' => (isset($rows->hna)?$rows->hna:'0'),
                    'hpp' => (isset($rows->hna)?$rows->hna:'0'),
                    'het' => (isset($rows->het)?$rows->het:'0'),
                    'awal' => (isset($rows->sisa)?$rows->sisa:'0'),
                    'masuk' => $jp[$key],
                    'keluar' => '0',
                    'sisa' => $new_sisa
                );
                $this->db->insert('transaksi_detail', $data_trans);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                }
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
        $result['id_penerimaan_distribusi'] = $id_dist_penerimaan;
        return $result;
    }
    
    function penjualan_non_resep_save() {
        $this->db->trans_begin();
        $data_penjualan = array(
            'pegawai_penduduk_id' => $this->session->userdata('id_user'),
            'pembeli_penduduk_id' => $this->input->post('id_pembeli'),
            'diskon_bank' => $this->input->post('cara_bayar'),
            'ppn' => $this->input->post('ppn'),
            'total' => currencyToNumber($this->input->post('total')),
            'bayar' => currencyToNumber($this->input->post('bayar')),
            'pembulatan' => currencyToNumber($this->input->post('bulat'))
        );
        $this->db->insert('penjualan', $data_penjualan);
        $id_penjualan = $this->db->insert_id();
        $id_pb = $this->input->post('id_pb');
        $jumlah = $this->input->post('jl');
        $this->session->set_userdata(array('sisa_stok' => NULL));
        foreach ($id_pb as $key => $data) {
            if ($data != '') {
                $fefo = $this->db->query("select td.* from transaksi_detail td
                    inner join (
                        SELECT barang_packing_id, max(id) as id_max FROM `transaksi_detail` 
                        WHERE barang_packing_id = '$id_pb[$key]' and transaksi_jenis != 'Pemesanan' and unit_id = '".$this->session->userdata('id_unit')."' group by barang_packing_id, ed
                    ) tm on (tm.barang_packing_id = td.barang_packing_id and td.id = tm.id_max) 
                    where td.sisa > 0 and td.ed > '".$this->waktu."' order by td.ed asc")->result();
                    foreach ($fefo as $num => $jml) {
                        if ($this->session->userdata('sisa_stok') == NULL) {
                            $sisa = $jml->sisa - $jumlah[$key];
                            if ($sisa >= 0) {
                                $data_trans = array(
                                    'transaksi_id' => $id_penjualan,
                                    'transaksi_jenis' => 'Penjualan',
                                    'waktu' => datetime2mysql($this->input->post('tanggal')),
                                    'barang_packing_id' => $id_pb[$key],
                                    'unit_id' => $this->session->userdata('id_unit'),
                                    'ed' => $jml->ed,
                                    'harga' => (isset($jml->harga)?$jml->harga:'0'),
                                    'ppn' => (isset($jml->ppn)?$jml->ppn:'0'),
                                    'hna' => (isset($jml->hna)?$jml->hna:'0'),
                                    'hpp' => (isset($jml->hna)?$jml->hna:'0'),
                                    'het' => (isset($jml->het)?$jml->het:'0'),
                                    'awal' => (isset($jml->sisa)?$jml->sisa:'0'),
                                    'masuk' => '0',
                                    'keluar' => $jumlah[$key],
                                    'sisa' => $sisa
                                );
                                $this->session->set_userdata(array('sisa_stok' => $sisa));
                                $this->db->insert('transaksi_detail', $data_trans);
                            } else if ($sisa < 0) {
                                $data_trans = array(
                                    'transaksi_id' => $id_penjualan,
                                    'transaksi_jenis' => 'Penjualan',
                                    'waktu' => datetime2mysql($this->input->post('tanggal')),
                                    'barang_packing_id' => $id_pb[$key],
                                    'unit_id' => $this->session->userdata('id_unit'),
                                    'ed' => $jml->ed,
                                    'harga' => (isset($jml->harga)?$jml->harga:'0'),
                                    'ppn' => (isset($jml->ppn)?$jml->ppn:'0'),
                                    'hna' => (isset($jml->hna)?$jml->hna:'0'),
                                    'hpp' => (isset($jml->hna)?$jml->hna:'0'),
                                    'het' => (isset($jml->het)?$jml->het:'0'),
                                    'awal' => (isset($jml->sisa)?$jml->sisa:'0'),
                                    'masuk' => '0',
                                    'keluar' => $jml->sisa,
                                    'sisa' => '0'
                                );
                                $this->session->set_userdata(array('sisa_stok' => $sisa));
                                $this->db->insert('transaksi_detail', $data_trans);
                            }
                        }
                        else if ($this->session->userdata('sisa_stok') < '0') {
                            $sisa = $jml->sisa - abs($this->session->userdata('sisa_stok'));
                            if ($sisa >= 0) {
                                $data_trans = array(
                                    'transaksi_id' => $id_penjualan,
                                    'transaksi_jenis' => 'Penjualan',
                                    'waktu' => datetime2mysql($this->input->post('tanggal')),
                                    'barang_packing_id' => $id_pb[$key],
                                    'unit_id' => $this->session->userdata('id_unit'),
                                    'ed' => $jml->ed,
                                    'harga' => (isset($jml->harga)?$jml->harga:'0'),
                                    'ppn' => (isset($jml->ppn)?$jml->ppn:'0'),
                                    'hna' => (isset($jml->hna)?$jml->hna:'0'),
                                    'hpp' => (isset($jml->hna)?$jml->hna:'0'),
                                    'het' => (isset($jml->het)?$jml->het:'0'),
                                    'awal' => (isset($jml->sisa)?$jml->sisa:'0'),
                                    'masuk' => '0',
                                    'keluar' => abs($this->session->userdata('sisa_stok')),
                                    'sisa' => $sisa
                                );
                                $this->session->set_userdata(array('sisa_stok' => $sisa));
                                $this->db->insert('transaksi_detail', $data_trans);

                            } else if ($sisa < 0) {
                                $data_trans = array(
                                    'transaksi_id' => $id_penjualan,
                                    'transaksi_jenis' => 'Penjualan',
                                    'waktu' => datetime2mysql($this->input->post('tanggal')),
                                    'barang_packing_id' => $id_pb[$key],
                                    'unit_id' => $this->session->userdata('id_unit'),
                                    'ed' => $jml->ed,
                                    'harga' => (isset($jml->harga)?$jml->harga:'0'),
                                    'ppn' => (isset($jml->ppn)?$jml->ppn:'0'),
                                    'hna' => (isset($jml->hna)?$jml->hna:'0'),
                                    'hpp' => (isset($jml->hna)?$jml->hna:'0'),
                                    'het' => (isset($jml->het)?$jml->het:'0'),
                                    'awal' => (isset($jml->sisa)?$jml->sisa:'0'),
                                    'masuk' => '0',
                                    'keluar' => $jml->sisa,
                                    'sisa' => '0'
                                );
                                $this->session->set_userdata(array('sisa_stok' => $sisa));
                                $this->db->insert('transaksi_detail', $data_trans);
                            }
                        }
                        else {
                            $sisa = $jml->sisa - $jumlah[$key];
                                $data_trans = array(
                                    'transaksi_id' => $id_penjualan,
                                    'transaksi_jenis' => 'Penjualan',
                                    'waktu' => datetime2mysql($this->input->post('tanggal')),
                                    'barang_packing_id' => $id_pb[$key],
                                    'unit_id' => $this->session->userdata('id_unit'),
                                    'ed' => $jml->ed,
                                    'harga' => (isset($jml->harga)?$jml->harga:'0'),
                                    'ppn' => (isset($jml->ppn)?$jml->ppn:'0'),
                                    'hna' => (isset($jml->hna)?$jml->hna:'0'),
                                    'hpp' => (isset($jml->hna)?$jml->hna:'0'),
                                    'het' => (isset($jml->het)?$jml->het:'0'),
                                    'awal' => (isset($jml->sisa)?$jml->sisa:'0'),
                                    'masuk' => '0',
                                    'keluar' => $jumlah[$key],
                                    'sisa' => $sisa
                                );
                                $this->session->set_userdata(array('sisa_stok' => $sisa));
                                $this->db->insert('transaksi_detail', $data_trans);
                        }
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                        }
                    }
                    
            }
            
        }
        $saldo = $this->db->query("select * from kas order by waktu desc limit 1")->row();
        $sisa  = (isset($saldo->akhir_saldo)?$saldo->akhir_saldo:'0')+currencyToNumber($this->input->post('bulat'));
        $data_kas = array(
            'waktu' => datetime2mysql($this->input->post('tanggal')),
            'transaksi_id' => $id_penjualan,
            'transaksi_jenis' => 'Penjualan Non Resep',
            'awal_saldo' => isset($saldo->akhir_saldo)?$saldo->akhir_saldo:'0',
            'penerimaan' => currencyToNumber($this->input->post('bulat')),
            'pengeluaran' => '0',
            'akhir_saldo' => $sisa
        );
        $this->db->insert('kas', $data_kas);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        $result['id_penjualan'] = $id_penjualan;
        return $result;
    }
    
    function penjualan_load_data($id_penjualan = NULL) {
        $q = null;
        if ($id_penjualan != null) {
            $q.="and p.id = '$id_penjualan'";
        }
        $sql = "select td.*, p.resep_id, p.bayar, p.total, p.pembulatan, p.id as id_penjualan, bp.id as id_pb, bp.barcode, bp.margin, bp.diskon, b.nama as barang, st.nama as satuan_terkecil, bp.isi, 
            o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan, pdk.nama as pegawai, p.pembeli_penduduk_id, pdd.nama as pasien, pd.nama as pembeli, pdd.nama, rs.pasien_penduduk_id, p.ppn, p.total, p.bayar, p.pembulatan
            from penjualan p
            left join transaksi_detail td on (p.id = td.transaksi_id)
            left join resep rs on (rs.id = p.resep_id)
            left join penduduk pd on (pd.id = p.pembeli_penduduk_id)
            left join penduduk pdd on (pdd.id = rs.pasien_penduduk_id)
            left join penduduk pdk on (pdk.id = p.pegawai_penduduk_id)
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join barang b on (b.id = bp.barang_id)
            left join obat o on (o.id = b.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id) where td.transaksi_jenis = 'Penjualan' $q";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function penjualan_jasa_save($no_rm) {
        $this->db->trans_begin();
        
        $last = $this->db->query("select no_daftar from pendaftaran where pasien = '$no_rm' order by no_daftar desc limit 1")->row();
        $data = $this->input->post('id_tarif');
        $tarif= $this->input->post('tarif');
        $freq = $this->input->post('freq');
        $jasa_klinis = $this->input->post('tindakan_jasa');
        foreach ($data as $key => $rows) {
            if ($rows != '') {
                $data_jasa_penjualan = array(
                    'waktu' => datetime2mysql($this->input->post('tanggal')),
                    'no_daftar' => $last->no_daftar,
                    'tarif_id' => $rows,
                    'tarif' => $tarif[$key],
                    'frekuensi' => $freq[$key],
                    'pegawai_penduduk_id' => $this->session->userdata('id_user'),
                    'nominal_jasa_klinis' => $jasa_klinis[$key]
                );
                $this->db->insert('jasa_penjualan_detail', $data_jasa_penjualan);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                }
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
        $result['id_penjualan'] = $no_rm;
        return $result;
    }
    
    function resep_save() {
        $this->db->trans_begin();
        $data_resep = array(
            'waktu' => datetime2mysql($this->input->post('tanggal')),
            'dokter_penduduk_id' => $this->input->post('id_dokter'),
            'pasien_penduduk_id' => $this->input->post('id_pasien'),
            'sah' => $this->input->post('absah'),
            'keterangan' => $this->input->post('ket')
        );
        $id_resep = $this->input->post('id_resep');
        if (isset($id_resep) and $id_resep == '') {
            $this->db->insert('resep', $data_resep);
            $id_resep = $this->db->insert_id();
        } else {
            $this->db->where('id', $id_resep);
            $this->db->update('resep', $data_resep);
            $this->db->delete('resep_r', array('resep_id' => $id_resep));
        }
        
        $nr  = $this->input->post('nr');
        $jr  = $this->input->post('jr');
        $jt  = $this->input->post('jt');
        $ap  = $this->input->post('ap');
        $it  = $this->input->post('it');
        $ja  = $this->input->post('ja');
        
        foreach ($nr as $key => $data) {
            if (($jr[$key] != '') and ($jt[$key] != '') and ($ap[$key] != '') and ($it[$key] != '') and ($ja[$key] != '0-0')) {
                $jasa = explode("-", $ja[$key]);
                $data_resep_r = array(
                    'resep_id' => $id_resep,
                    'r_no' => $data,
                    'resep_r_jumlah' => $jr[$key],
                    'tebus_r_jumlah' => $jt[$key],
                    'pakai_aturan' => $ap[$key],
                    'iter' => $it[$key],
                    'tarif_id' => $jasa[0],
                    'profesi_layanan_tindakan_jasa_total' => $jasa[1],
                    'pegawai_penduduk_id' => $this->session->userdata('id_user')
                );
                $this->db->insert('resep_r', $data_resep_r);
                $id_r= $this->db->insert_id();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                }
                $pb = $this->input->post('pb'.$key);
                $dr  = $this->input->post('dr'.$key);
                $id_pb = $this->input->post('id_pb'.$key);
                $jp  = $this->input->post('jp'.$key);
                foreach ($id_pb as $num => $rows) {
                    $form = $this->db->query("select o.formularium from obat o join barang_packing b on (o.id = b.id) where b.id = '$rows'")->row();
                    $harga= $this->db->query("select td.*, bp.margin, bp.diskon from transaksi_detail td 
                        join barang_packing bp on (td.barang_packing_id = bp.id) where td.barang_packing_id = '$rows' and 
                        td.transaksi_jenis != 'Pemesanan' order by td.waktu desc limit 1")->row();
                    //$hjual = (($harga->hna*($harga->margin/100))+$harga->hna) - (($harga->diskon/100)*$harga->hna);
                    $data_resep_r_racik = array(
                        'r_resep_id' => $id_r,
                        'barang_packing_id' => $rows,
                        'jual_harga' => '0',
                        'dosis_racik' => $dr[$num],
                        'pakai_jumlah' => $jp[$num],
                        'formularium' => isset($form->formularium)?$form->formularium:NULL
                    );
                    $this->db->insert('resep_racik_r_detail', $data_resep_r_racik);
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                    }
                }
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
        $result['id_resep'] = $id_resep;
        return $result;
    }
    
    function retur_pembelian_save() {
        $this->db->trans_begin();
        $data_retur = array(
            'pembelian_id' => $this->input->post('id_pembelian'),
            'pegawai_penduduk_id' => $this->session->userdata('id_user'),
            'salesman_penduduk_id' => $this->input->post('id_sales'),
            'suplier_relasi_instansi' => $this->input->post('id_suplier')
        );
        $this->db->insert('pembelian_retur', $data_retur);
        $id = $this->db->insert_id();
        $ed = $this->input->post('ed');
        $pb = $this->input->post('id_pb');
        $jml_retur = $this->input->post('jml_retur');
        foreach ($pb as $key => $data) {
            if ($data != '') {
                
                $jml = $this->db->query("select * from transaksi_detail where transaksi_jenis != 'Pemesanan' and barang_packing_id = '$pb[$key]' and ed = '$ed[$key]' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1")->row();
                $sisa = (isset($jml->sisa)?$jml->sisa:0) - $jml_retur[$key];
                $data_trans = array(
                    'transaksi_id' => $id,
                    'transaksi_jenis' => 'Retur Pembelian',
                    'waktu' => datetime2mysql($this->input->post('tanggal')),
                    'barang_packing_id' => $data,
                    'unit_id' => $this->session->userdata('id_unit'),
                    'ed' => $jml->ed,
                    'harga' => isset($jml->harga)?$jml->harga:'0',
                    'ppn' => isset($jml->ppn)?$jml->ppn:'0',
                    'hna' => isset($jml->hna)?$jml->hna:'0',
                    'hpp' => isset($jml->hpp)?$jml->hpp:'0',
                    'het' => isset($jml->het)?$jml->het:'0',
                    'awal' => isset($jml->sisa)?$jml->sisa:'0',
                    'masuk' => '0',
                    'keluar' => $jml_retur[$key],
                    'sisa' => $sisa
                );
                $this->db->insert('transaksi_detail', $data_trans);
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
        $result['id_retur_pembelian'] = $id;
        return $result;
    }
    
    function reretur_pembelian_save() {
        $this->db->trans_begin();
        $berupa = $this->input->post('berupa');
        
        if ($berupa == 'uang') {
            $data_retur_pembelian_penerimaan = array(
                'retur_id' => $this->input->post('noretur'),
                'pegawai_penduduk_id' => $this->session->userdata('id_user'),
                'uang' => '1'
            );
            $this->db->insert('pembelian_retur_penerimaan', $data_retur_pembelian_penerimaan);
            $id_retur = $this->db->insert_id();
            
            $total = $this->input->post('total');
            
            $data = $this->db->query("select * from kas order by waktu desc limit 1")->row();
            $sisa = $data->akhir_saldo+$total;
            $data_kas = array(
                'waktu' => datetime2mysql($this->input->post('tanggal')),
                'transaksi_id' => $id_retur,
                'transaksi_jenis' => 'Penerimaan Retur Pembelian',
                'awal_saldo' => $data->akhir_saldo,
                'penerimaan' => $total,
                'pengeluaran' => '0',
                'akhir_saldo' => $sisa
            );
            $this->db->insert('kas', $data_kas);
        }
        if ($berupa == 'barang') {

            $data_retur_pembelian_penerimaan = array(
                'retur_id' => $this->input->post('noretur'),
                'pegawai_penduduk_id' => $this->session->userdata('id_user'),
                'uang' => '0'
            );
            $this->db->insert('pembelian_retur_penerimaan', $data_retur_pembelian_penerimaan);
            $id_retur = $this->db->insert_id();
            
            $id_pb = $this->input->post('id_pb');
            $ed = $this->input->post('ed');
            $jumlah = $this->input->post('jml');
            $total = 0;
            foreach ($id_pb as $key => $data) {
                $rows = $this->db->query("select * from transaksi_detail where barang_packing_id = '$data' and ed = '".date2mysql($ed[$key])."' and unit_id = '".$this->session->userdata('id_unit')."' and transaksi_jenis != 'Pemesanan' order by waktu desc limit 1")->row();
                $sisa = $rows->sisa + $jumlah[$key];
                $data_trans = array(
                    'transaksi_id' => $id_retur,
                    'transaksi_jenis' => 'Penerimaan Retur Pembelian',
                    'waktu' => datetime2mysql($this->input->post('tanggal')),
                    'barang_packing_id' => $data,
                    'unit_id' => $this->session->userdata('id_unit'),
                    'ed' => $rows->ed,
                    'harga' => $rows->harga,
                    'ppn' => $rows->ppn,
                    'hna' => $rows->hna,
                    'hpp' => $rows->hpp,
                    'het' => $rows->het,
                    'awal' => $rows->sisa,
                    'masuk' => $jumlah[$key],
                    'keluar' => '0',
                    'sisa' => $sisa
                );
                $this->db->insert('transaksi_detail', $data_trans);
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
        $result['id_penerimaan_retur'] = $id_retur;
        return $result;
    }
    
    function retur_penjualan_save() {
        $this->db->trans_begin();
        $id_pembeli = $this->input->post('idpembeli');
        $data_retur = array(
            'penjualan_id' => $this->input->post('id_penjualan'),
            'pembeli_penduduk_id' => ($id_pembeli != null)?$id_pembeli:NULL,
            'pegawai_penduduk_id' => $this->session->userdata('id_user')
        );
        $this->db->insert('penjualan_retur', $data_retur);
        $id = $this->db->insert_id();
        $id_pb = $this->input->post('id_pb');
        $jml_retur = $this->input->post('jml_retur');
        $ed = $this->input->post('ed');
        foreach ($id_pb as $key => $data) {
            if ($data != '') {
            $jml = $this->db->query("select * from transaksi_detail where transaksi_jenis != 'Pemesanan' and barang_packing_id = '$data' and ed = '$ed[$key]' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1")->row();
            $sisa= (isset($jml->sisa)?$jml->sisa:'0') + $jml_retur[$key];
            $data_trans = array(
                    'transaksi_id' => $id,
                    'transaksi_jenis' => 'Retur Penjualan',
                    'waktu' => datetime2mysql($this->input->post('tanggal')),
                    'barang_packing_id' => $data,
                    'unit_id' => $this->session->userdata('id_unit'),
                    'ed' => $jml->ed,
                    'harga' => $jml->harga,
                    'ppn' => $jml->ppn,
                    'hna' => $jml->hna,
                    'hpp' => $jml->hpp,
                    'het' => $jml->het,
                    'awal' => $jml->sisa,
                    'masuk' => $jml_retur[$key],
                    'keluar' => '0',
                    'sisa' => $sisa
                );
                $this->db->insert('transaksi_detail', $data_trans);
            //echo "select * from transaksi_detail where transaksi_jenis != 'Pemesanan' and barang_packing_id = '$data' and ed = '$ed[$key]' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1 <br/>";
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
        $result['id_retur_penjualan'] = $id;
        return $result;
    }
    
    function reretur_penjualan_save() {
        $this->db->trans_begin();
        $berupa = $this->input->post('berupa');
        
        if ($berupa == 'uang') {
            $data_retur_penjualan_pengeluaran = array(
                'penjualan_retur_id' => $this->input->post('noretur'),
                'pegawai_penduduk_id' => $this->session->userdata('id_user'),
                'uang' => '1'
            );
            $this->db->insert('penjualan_retur_pengeluaran', $data_retur_penjualan_pengeluaran);
            $id_retur = $this->db->insert_id();
            
            $total = $this->input->post('totalreretur');
            
            $data = $this->db->query("select * from kas order by waktu desc limit 1")->row();
            $sisa = $data->akhir_saldo+$total;
            $data_kas = array(
                'waktu' => datetime2mysql($this->input->post('tanggal')),
                'transaksi_id' => $id_retur,
                'transaksi_jenis' => 'Penerimaan Retur Pembelian',
                'awal_saldo' => $data->akhir_saldo,
                'pengeluaran' => $total,
                'pengeluaran' => '0',
                'akhir_saldo' => $sisa
            );
            $this->db->insert('kas', $data_kas);
        }
        if ($berupa == 'barang') {

            $data_retur_penjualan_pengeluaran = array(
                'penjualan_retur_id' => $this->input->post('noretur'),
                'pegawai_penduduk_id' => $this->session->userdata('id_user'),
                'uang' => '0'
            );
            $this->db->insert('penjualan_retur_pengeluaran', $data_retur_penjualan_pengeluaran);
            $id_retur = $this->db->insert_id();
            
            $id_pb = $this->input->post('id_pb');
            $ed = $this->input->post('ed');
            $jumlah = $this->input->post('jml');
            $total = 0;
            foreach ($id_pb as $key => $data) {
                $rows = $this->db->query("select * from transaksi_detail where barang_packing_id = '$data' and ed = '".$ed[$key]."' and unit_id = '".$this->session->userdata('id_unit')."' and transaksi_jenis != 'Pemesanan' order by waktu desc limit 1")->row();
                $sisa = $rows->sisa - $jumlah[$key];
                $data_trans = array(
                    'transaksi_id' => $id_retur,
                    'transaksi_jenis' => 'Pengeluaran Retur Penjualan',
                    'waktu' => datetime2mysql($this->input->post('tanggal')),
                    'barang_packing_id' => $data,
                    'unit_id' => $this->session->userdata('id_unit'),
                    'ed' => $rows->ed,
                    'harga' => $rows->harga,
                    'ppn' => $rows->ppn,
                    'hna' => $rows->hna,
                    'hpp' => $rows->hpp,
                    'het' => $rows->het,
                    'awal' => $rows->sisa,
                    'masuk' => '0',
                    'keluar' => $jumlah[$key],
                    'sisa' => $sisa
                );
                $this->db->insert('transaksi_detail', $data_trans);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                }
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
        $result['id_pengeluaran_retur'] = $id_retur;
        return $result;
    }
    
    function penjualan_save() {
        $this->db->trans_begin();
        
        $resep = $this->input->post('id_resep');
        $data_penjualan = array(
            'pegawai_penduduk_id' => $this->session->userdata('id_user'),
            'resep_id' => $resep,
            'ppn' => $this->input->post('ppn'),
            'total' => currencyToNumber($this->input->post('total')),
            'bayar' => currencyToNumber($this->input->post('bayar')),
            'pembulatan' => currencyToNumber($this->input->post('bulat'))
        );
        $this->db->insert('penjualan', $data_penjualan);
        $id_penjualan = $this->db->insert_id();
        
        $id_pb = $this->input->post('id_pb');
        $subtotal = $this->input->post('subtotal');
        $jumlah = $this->input->post('jl');
        $disc = $this->input->post('disc');
        $harga_jual = $this->input->post('harga_jual');
        
        $this->session->set_userdata(array('sisa_stok' => NULL));
        foreach ($id_pb as $key => $data) {
            if ($data != '') {
                $terdiskon = $harga_jual[$key] - ($harga_jual[$key]*($disc[$key]/100));
                $fefo = $this->db->query("select td.* from transaksi_detail td
                    inner join (
                        SELECT barang_packing_id, max(id) as id_max FROM `transaksi_detail` 
                        WHERE barang_packing_id = '$id_pb[$key]' and transaksi_jenis != 'Pemesanan' and unit_id = '".$this->session->userdata('id_unit')."' group by barang_packing_id, ed
                    ) tm on (tm.barang_packing_id = td.barang_packing_id and td.id = tm.id_max) 
                    where td.sisa > 0 and td.ed > '".$this->waktu."' order by td.ed asc")->result();
                
                $leadtime = $this->db->query("select leadtime_hours from transaksi_detail 
                    where unit_id = '".$this->session->userdata('id_unit')."' and barang_packing_id = '$data' and transaksi_jenis = 'Pembelian'
                        order by id desc limit 1")->row();
                    foreach ($fefo as $num => $jml) {
                        if ($this->session->userdata('sisa_stok') == NULL) {
                            $sisa = $jml->sisa - $jumlah[$key];
                            if ($sisa >= 0) {
                                $marg= $this->db->query("select margin from barang_packing where id = '$id_pb[$key]]'")->row();
                                $data_trans = array(
                                    'transaksi_id' => $id_penjualan,
                                    'transaksi_jenis' => 'Penjualan',
                                    'waktu' => datetime2mysql($this->input->post('tanggal')),
                                    'barang_packing_id' => $id_pb[$key],
                                    'unit_id' => $this->session->userdata('id_unit'),
                                    'ed' => $jml->ed,
                                    'harga' => $jml->harga,
                                    'margin_percentage' => $marg->margin,
                                    'jual_diskon_percentage' => $disc[$key],
                                    'terdiskon_harga' => $terdiskon,
                                    'subtotal' => currencyToNumber($subtotal[$key]),
                                    'ppn' => $jml->ppn,
                                    'hna' => $jml->hna,
                                    'hpp' => $jml->hpp,
                                    'het' => $jml->het,
                                    'awal' => $jml->sisa,
                                    'masuk' => '0',
                                    'keluar' => $jumlah[$key],
                                    'sisa' => $sisa,
                                    'leadtime_hours' => (isset($leadtime->loadtime_hours)?$leadtime->loadtime_hours:'0')
                                );
                                $this->session->set_userdata(array('sisa_stok' => $sisa));
                                $this->db->insert('transaksi_detail', $data_trans);
                            } else if ($sisa < 0) {
                                $marg= $this->db->query("select margin from barang_packing where id = '$id_pb[$key]]'")->row();
                                $data_trans = array(
                                    'transaksi_id' => $id_penjualan,
                                    'transaksi_jenis' => 'Penjualan',
                                    'waktu' => datetime2mysql($this->input->post('tanggal')),
                                    'barang_packing_id' => $id_pb[$key],
                                    'unit_id' => $this->session->userdata('id_unit'),
                                    'ed' => $jml->ed,
                                    'harga' => $jml->harga,
                                    'margin_percentage' => $marg->margin,
                                    'jual_diskon_percentage' => $disc[$key],
                                    'terdiskon_harga' => $terdiskon,
                                    'subtotal' => currencyToNumber($subtotal[$key]),
                                    'ppn' => $jml->ppn,
                                    'hna' => $jml->hna,
                                    'hpp' => $jml->hpp,
                                    'het' => $jml->het,
                                    'awal' => $jml->sisa,
                                    'masuk' => '0',
                                    'keluar' => $jml->sisa,
                                    'sisa' => '0',
                                    'leadtime_hours' => (isset($leadtime->loadtime_hours)?$leadtime->loadtime_hours:'0')
                                );
                                $this->session->set_userdata(array('sisa_stok' => $sisa));
                                $this->db->insert('transaksi_detail', $data_trans);
                            }
                        }
                        else if ($this->session->userdata('sisa_stok') < '0') {
                            $sisa = $jml->sisa - abs($this->session->userdata('sisa_stok'));
                            if ($sisa >= 0) {
                                $marg= $this->db->query("select margin from barang_packing where id = '$id_pb[$key]]'")->row();
                                $data_trans = array(
                                    'transaksi_id' => $id_penjualan,
                                    'transaksi_jenis' => 'Penjualan',
                                    'waktu' => datetime2mysql($this->input->post('tanggal')),
                                    'barang_packing_id' => $id_pb[$key],
                                    'unit_id' => $this->session->userdata('id_unit'),
                                    'ed' => $jml->ed,
                                    'harga' => $jml->harga,
                                    'margin_percentage' => $marg->margin,
                                    'jual_diskon_percentage' => $disc[$key],
                                    'terdiskon_harga' => $terdiskon,
                                    'subtotal' => currencyToNumber($subtotal[$key]),
                                    'ppn' => $jml->ppn,
                                    'hna' => $jml->hna,
                                    'hpp' => $jml->hpp,
                                    'het' => $jml->het,
                                    'awal' => $jml->sisa,
                                    'masuk' => '0',
                                    'keluar' => abs($this->session->userdata('sisa_stok')),
                                    'sisa' => $sisa,
                                    'leadtime_hours' => (isset($leadtime->loadtime_hours)?$leadtime->loadtime_hours:'0')
                                );
                                $this->session->set_userdata(array('sisa_stok' => $sisa));
                                $this->db->insert('transaksi_detail', $data_trans);

                            } else if ($sisa < 0) {
                                $marg= $this->db->query("select margin from barang_packing where id = '$id_pb[$key]]'")->row();
                                $data_trans = array(
                                    'transaksi_id' => $id_penjualan,
                                    'transaksi_jenis' => 'Penjualan',
                                    'waktu' => datetime2mysql($this->input->post('tanggal')),
                                    'barang_packing_id' => $id_pb[$key],
                                    'unit_id' => $this->session->userdata('id_unit'),
                                    'ed' => $jml->ed,
                                    'harga' => $jml->harga,
                                    'margin_percentage' => $marg->margin,
                                    'jual_diskon_percentage' => $disc[$key],
                                    'terdiskon_harga' => $terdiskon,
                                    'subtotal' => currencyToNumber($subtotal[$key]),
                                    'ppn' => $jml->ppn,
                                    'hna' => $jml->hna,
                                    'hpp' => $jml->hpp,
                                    'het' => $jml->het,
                                    'awal' => $jml->sisa,
                                    'masuk' => '0',
                                    'keluar' => $jml->sisa,
                                    'sisa' => '0',
                                    'leadtime_hours' => (isset($leadtime->loadtime_hours)?$leadtime->loadtime_hours:'0')
                                );
                                $this->session->set_userdata(array('sisa_stok' => $sisa));
                                $this->db->insert('transaksi_detail', $data_trans);
                            }
                        }
                        else {
                            $sisa = $jml->sisa - $jumlah[$key];
                                $data_trans = array(
                                    'transaksi_id' => $id_penjualan,
                                    'transaksi_jenis' => 'Penjualan',
                                    'waktu' => datetime2mysql($this->input->post('tanggal')),
                                    'barang_packing_id' => $id_pb[$key],
                                    'unit_id' => $this->session->userdata('id_unit'),
                                    'ed' => $jml->ed,
                                    'harga' => $jml->harga,
                                    'margin_percentage' => $marg->margin,
                                    'jual_diskon_percentage' => $disc[$key],
                                    'terdiskon_harga' => $terdiskon,
                                    'subtotal' => currencyToNumber($subtotal[$key]),
                                    'ppn' => $jml->ppn,
                                    'hna' => $jml->hna,
                                    'hpp' => $jml->hpp,
                                    'het' => $jml->het,
                                    'awal' => $jml->sisa,
                                    'masuk' => '0',
                                    'keluar' => $jumlah[$key],
                                    'sisa' => $sisa,
                                    'leadtime_hours' => (isset($leadtime->loadtime_hours)?$leadtime->loadtime_hours:'0')
                                );
                                $this->session->set_userdata(array('sisa_stok' => $sisa));
                                $this->db->insert('transaksi_detail', $data_trans);
                        }
                    }
            }
        }
        
        $array_update_stok = $this->db->query("select r.resep_id, rrr.* from resep_r r join resep_racik_r_detail rrr on (r.id = rrr.r_resep_id) where resep_id = '$resep'")->result();
        foreach ($array_update_stok as $rows) {
            $harga= $this->db->query("select td.*, bp.margin, bp.diskon from transaksi_detail td 
                    join barang_packing bp on (td.barang_packing_id = bp.id) where td.barang_packing_id = '".$rows->barang_packing_id."' and 
                    td.transaksi_jenis != 'Pemesanan' order by td.waktu desc limit 1")->row();
            if (isset($harga->hna)) {
                $hjual = (($harga->hna*($harga->margin/100))+$harga->hna) - (($harga->diskon/100)*$harga->hna);
                $update_harga = array(
                    'jual_harga' => $hjual
                );
                $this->db->where(array('r_resep_id' => $rows->r_resep_id, 'barang_packing_id' => $rows->barang_packing_id));
                $this->db->update('resep_racik_r_detail', $update_harga);
            }
        }
        
        $rows = $this->db->query("select * from kas order by waktu desc limit 1")->row();
        $data_kas = array(
            'waktu' => datetime2mysql($this->input->post('tanggal')),
            'transaksi_id' => $id_penjualan,
            'transaksi_jenis' => 'Penjualan Resep',
            'awal_saldo' => (isset($rows->akhir_saldo)?$rows->akhir_saldo:'0'),
            'penerimaan' => currencyToNumber($this->input->post('bulat')),
            'pengeluaran' => '0',
            'akhir_saldo' => ((isset($rows->akhir_saldo)?$rows->akhir_saldo:'0')-currencyToNumber($this->input->post('bulat')))
        );
        $this->db->insert('kas', $data_kas);
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        $result['id_penjualan'] = $id_penjualan;
        return $result;
    }
    
    function kas_load_data($awal = null, $akhir = null, $jenis = null, $nama = null) {
        $q = null;
        if ($awal != null and $akhir != null) {
            $q.="where date(waktu) between '".date2mysql($awal)."' and '".date2mysql($akhir)."'";
        }
        if ($jenis != null) {
            $q.=" and transaksi_jenis = '$jenis'";
        }
        if ($nama != null) {
            $q.=" and penerimaan_pengeluaran_nama like ('%$nama%')";
        }
        $sql = "select * from kas $q";
        //echo $sql;
        return $this->db->query($sql);
    }
    
    function reimbursement_load_data($awal = null, $akhir = null, $instansi = null) {
        $q = null;
        if ($awal != null and $akhir != null) {
            $q.="and date(td.waktu) between '".date2mysql($awal)."' and '".date2mysql($akhir)."'";
        }
        if ($instansi != null) {
            $q.="and ap.id = '$instansi'";
        }
        $sql = "
            select td.waktu, td.keluar, td.hna, td.jual_diskon_percentage, (td.harga+(td.harga*(td.jual_diskon_percentage/100))) as harga, 
        bp.id as id_pb, b.nama as barang, st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, ak.polis_no, sd.nama as sediaan, p.id, pd.nama as pasien,
        ap.nama as perusahaan_asuransi
        from penjualan p
            join penduduk pd on (p.pembeli_penduduk_id = pd.id)
            join transaksi_detail td on (p.id = td.transaksi_id)
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join barang b on (bp.barang_id = b.id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            join asuransi_kepesertaan ak on (ak.penduduk_id = pd.id)
            join asuransi_produk ap on (ak.asuransi_produk_id = ap.id)
            join relasi_instansi ris on (ap.relasi_instansi_id = ris.id)
            where td.transaksi_jenis = 'Penjualan' $q group by bp.id
        ";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    function stok_opname_load_data($id) {
        $sql = "
            select os.alasan, td.*, bp.id as id_pb, b.nama as barang, o.kekuatan, bp.isi, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan, st.nama as satuan_terbesar, bp.barcode from opname_stok os
            join transaksi_detail td on (os.id = td.transaksi_id)
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join barang b on (bp.barang_id = b.id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terbesar_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            where os.id = '$id' and td.transaksi_jenis = 'Stok Opname'
        ";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function pemusnahan_load_data($id = null) {
        $q = null;
        if ($id != null) {
            $q.="and p.id = '$id'";
        }
        $sql = "select o.id as id_obat, pd.nama as saksi_apotek, o.generik, pdd.nama as saksi_bpom, td.*, bp.id as id_pb, bp.barcode, bp.margin, bp.diskon, b.nama as barang, 
        st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan from pemusnahan p 
        left join transaksi_detail td on (p.id = td.transaksi_id) 
        join barang_packing bp on (td.barang_packing_id = bp.id) 
        join barang b on (b.id = bp.barang_id) 
        left join obat o on (o.id = b.id) left join satuan s on (s.id = o.satuan_id) 
        left join satuan st on (st.id = bp.terkecil_satuan_id) 
        left join sediaan sd on (sd.id = o.sediaan_id) 
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        left join penduduk pd on (pd.id = p.apotek_saksi_penduduk_id)
        left join penduduk pdd on (pdd.id = p.bpom_saksi_penduduk_id) where td.transaksi_jenis = 'Pemusnahan' $q";
        ///echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function retur_pembelian_load_data($id) {
        $q = null;
        if ($id != null) {
            $q.="and p.id = '$id'";
        }
        $sql = "select o.id as id_obat, o.generik, td.*, bp.id as id_pb, ri.nama as suplier, bp.barcode, bp.margin, bp.diskon, b.nama as barang, 
        st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, pd.nama as salesman, s.nama as satuan, sd.nama as sediaan from pembelian_retur p 
        left join transaksi_detail td on (p.id = td.transaksi_id) 
        join barang_packing bp on (td.barang_packing_id = bp.id) 
        join barang b on (b.id = bp.barang_id) 
        left join obat o on (o.id = b.id) left join satuan s on (s.id = o.satuan_id) 
        left join satuan st on (st.id = bp.terkecil_satuan_id) 
        left join sediaan sd on (sd.id = o.sediaan_id) 
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        left join relasi_instansi ri on (p.suplier_relasi_instansi = ri.id)
        left join penduduk pd on (p.salesman_penduduk_id = pd.id)
        where td.transaksi_jenis = 'Retur Pembelian' $q";
        return $this->db->query($sql);
    }
    
    function reretur_pembelian_load_data($id) {
        $q = null;
        if ($id != null) {
            $q.="and p.id = '$id'";
        }
        $sql = "select p.retur_id, p.id as penerimaan_retur_id, p.uang, o.id as id_obat, o.generik, td.*, bp.id as id_pb, bp.barcode, bp.margin, bp.diskon, b.nama as barang, ri.nama as suplier,
        st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, pd.nama as salesman, s.nama as satuan, sd.nama as sediaan from pembelian_retur_penerimaan p 
        join pembelian_retur pr on (p.retur_id = pr.id)
        left join transaksi_detail td on (p.id = td.transaksi_id) 
        join barang_packing bp on (td.barang_packing_id = bp.id) 
        join barang b on (b.id = bp.barang_id) 
        left join obat o on (o.id = b.id) left join satuan s on (s.id = o.satuan_id) 
        left join satuan st on (st.id = bp.terkecil_satuan_id) 
        left join sediaan sd on (sd.id = o.sediaan_id) 
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        left join relasi_instansi ri on (pr.suplier_relasi_instansi = ri.id)
        left join penduduk pd on (pr.salesman_penduduk_id = pd.id)
        where td.transaksi_jenis = 'Penerimaan Retur Pembelian' $q";
        return $this->db->query($sql);
    }
    
    function pemakaian_load_data($id = null) {
        $q = null;
        if ($id != null) {
            $q.="and p.id = '$id'";
        }
        $sql = "select o.id as id_obat, pd.nama as pegawai, o.generik,  td.*, bp.id as id_pb, p.waktu, bp.barcode, bp.margin, bp.diskon, b.nama as barang, 
        st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan from pemakaian p 
        left join transaksi_detail td on (p.id = td.transaksi_id) 
        join barang_packing bp on (td.barang_packing_id = bp.id) 
        join barang b on (b.id = bp.barang_id) 
        left join obat o on (o.id = b.id) left join satuan s on (s.id = o.satuan_id) 
        left join satuan st on (st.id = bp.terkecil_satuan_id) 
        left join sediaan sd on (sd.id = o.sediaan_id) 
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        left join penduduk pd on (pd.id = p.pegawai_penduduk_id)
        where td.transaksi_jenis = 'Pemakaian' $q";
        ///echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function distribusi_load_data($id = null) {
        $q = null;
        if ($id != null) {
            $q.="and p.id = '$id'";
        }
        $sql = "select o.id as id_obat, pd.nama as pegawai, u.nama as unit, ut.nama as tujuan, o.generik,  td.*, bp.id as id_pb, p.distribusi_id, bp.barcode, bp.margin, bp.diskon, b.nama as barang, 
        st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan from distribusi d
        join unit u on (d.unit_id = u.id)
        join unit ut on (d.tujuan_unit_id = ut.id)
        left join transaksi_detail td on (p.id = td.transaksi_id) 
        join barang_packing bp on (td.barang_packing_id = bp.id) 
        join barang b on (b.id = bp.barang_id) 
        left join obat o on (o.id = b.id) left join satuan s on (s.id = o.satuan_id) 
        left join satuan st on (st.id = bp.terkecil_satuan_id) 
        left join sediaan sd on (sd.id = o.sediaan_id) 
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        left join penduduk pd on (pd.id = p.pegawai_penduduk_id)
        where td.transaksi_jenis = 'Distribusi' $q";
        ///echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function penerimaan_distribusi_load_data($id = null) {
        $q = null;
        if ($id != null) {
            $q.="and p.id = '$id'";
        }
        $sql = "select d.unit_id as id_unit, o.id as id_obat, pd.nama as pegawai, u.nama as unit, ut.nama as tujuan, o.generik,  td.*, bp.id as id_pb, p.distribusi_id, bp.barcode, bp.margin, bp.diskon, b.nama as barang, 
        st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan from distribusi_penerimaan p 
        join distribusi d on (p.distribusi_id = d.id)
        join unit u on (d.unit_id = u.id)
        join unit ut on (d.tujuan_unit_id = ut.id)
        left join transaksi_detail td on (p.id = td.transaksi_id) 
        join barang_packing bp on (td.barang_packing_id = bp.id) 
        join barang b on (b.id = bp.barang_id) 
        left join obat o on (o.id = b.id) left join satuan s on (s.id = o.satuan_id) 
        left join satuan st on (st.id = bp.terkecil_satuan_id) 
        left join sediaan sd on (sd.id = o.sediaan_id) 
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        left join penduduk pd on (pd.id = p.pegawai_penduduk_id)
        where td.transaksi_jenis = 'Penerimaan Distribusi' $q";
        ///echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function retur_penjualan_delete($id) {
        $this->db->trans_begin();
        $this->db->delete('penjualan_retur', array('id' => $id));
        $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Retur Penjualan'));
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status['status'] = FALSE;
        } else {
            $this->db->trans_commit();
            $status['status'] = TRUE;
        }
        return $status;
    }
    
    function retur_penjualan_load_data($id) {
        $q = null;
        if ($id != null) {
            $q.="and p.id = '$id'";
        }
        $sql = "select o.id as id_obat, pd.nama as pegawai, o.generik, pdd.nama as pembeli,  td.*, bp.id as id_pb, bp.barcode, bp.margin, bp.diskon, b.nama as barang, 
        st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan from penjualan_retur p 
        left join transaksi_detail td on (p.id = td.transaksi_id) 
        join barang_packing bp on (td.barang_packing_id = bp.id) 
        join barang b on (b.id = bp.barang_id) 
        left join obat o on (o.id = b.id) left join satuan s on (s.id = o.satuan_id) 
        left join satuan st on (st.id = bp.terkecil_satuan_id) 
        left join sediaan sd on (sd.id = o.sediaan_id) 
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        left join penduduk pd on (pd.id = p.pegawai_penduduk_id)
        left join penduduk pdd on (p.pembeli_penduduk_id = pdd.id)
        where td.transaksi_jenis = 'Retur Penjualan' $q";
        ///echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function retur_distribusi_save() {
        $this->db->trans_begin();
        
        $id_penerimaan_dist = $this->input->post('id_distribusi_penerimaan');
        $data_retur_dist = array(
            'waktu' => datetime2mysql($this->input->post('tanggal')),
            'penerimaan_distribusi_id' => $id_penerimaan_dist,
            'pegawai_penduduk_id' => $this->session->userdata('id_user')
        );
        $this->db->insert('distribusi_retur', $data_retur_dist);
        $id_retur = $this->db->insert_id();
        $id_pb = $this->input->post('id_pb');
        $ed    = $this->input->post('ed');
        $jumlah= $this->input->post('jp');
        foreach ($id_pb as $key => $data) {
            $jml = $this->db->query("select * from transaksi_detail where barang_packing_id = '$data' and ed = '".$ed[$key]."' and unit_id = '".$this->session->userdata('id_unit')."' and transaksi_jenis != 'Pemesanan' order by waktu desc limit 1")->row();
            
            $sisa= $jml->sisa - $jumlah[$key];
            $data_trans = array(
                'transaksi_id' => $id_retur,
                'transaksi_jenis' => 'Retur Distribusi',
                'waktu' => datetime2mysql($this->input->post('tanggal')),
                'barang_packing_id' => $data,
                'unit_id' => $this->session->userdata('id_unit'),
                'ed' => $jml->ed,
                'harga' => $jml->harga,
                'ppn' => $jml->ppn,
                'hna' => $jml->hna,
                'hpp' => $jml->hpp,
                'het' => $jml->het,
                'awal' => $jml->sisa,
                'masuk' => '0',
                'keluar' => $jumlah[$key],
                'sisa' => $sisa
            );
            $this->db->insert('transaksi_detail', $data_trans);
        }
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        $result['id_retur_distribusi'] = $id_retur;
        return $result;
    }
    
    function penerimaan_retur_distribusi_save() {
        $this->db->trans_begin();
        
        $data_penerimaan_retur_distribusi = array(
            'waktu' => datetime2mysql($this->input->post('tanggal')),
            'distribusi_retur_id' => $this->input->post('noretur'),
            'pegawai_penduduk_id' => $this->session->userdata('id_user')
        );
        $this->db->insert('distribusi_retur_penerimaan', $data_penerimaan_retur_distribusi);
        $id_penerimaan = $this->db->insert_id();
        $id_pb = $this->input->post('id_pb');
        $ed    = $this->input->post('ed');
        $jumlah= $this->input->post('jml');
        foreach ($id_pb as $key => $data) {
            $jml = $this->db->query("select * from transaksi_detail where barang_packing_id = '$data' and ed = '".$ed[$key]."' and unit_id = '".$this->session->userdata('id_unit')."' and transaksi_jenis != 'Pemesanan' order by waktu desc limit 1")->row();
            $sisa= (isset($jml->sisa)?$jml->sisa:0) + $jumlah[$key];
            $data_trans = array(
                'transaksi_id' => $id_penerimaan,
                'transaksi_jenis' => 'Penerimaan Retur Distribusi',
                'waktu' => datetime2mysql($this->input->post('tanggal')),
                'barang_packing_id' => $data,
                'unit_id' => $this->session->userdata('id_unit'),
                'ed' => $jml->ed,
                'harga' => isset($jml->sisa)?$jml->harga:'0',
                'ppn' => isset($jml->sisa)?$jml->ppn:'0',
                'hna' => isset($jml->sisa)?$jml->hna:'0',
                'hpp' => isset($jml->sisa)?$jml->hpp:'0',
                'het' => isset($jml->sisa)?$jml->het:'0',
                'awal' => isset($jml->sisa)?$jml->sisa:'0',
                'masuk' => $jumlah[$key],
                'keluar' => '0',
                'sisa' => $sisa
            );
            $this->db->insert('transaksi_detail', $data_trans);
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        $result['id_penerimaan'] = $id_penerimaan;
        return $result;
    }
    
    function repackage_delete($id) {
        $this->db->trans_begin();
        $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Repackage'));
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
    
    function penerimaan_distribusi_delete($id) {
        $this->db->trans_begin();
        //$cek = $this->db->get_where('pembelian', array('id' => $id))->num_rows();        
        $rows= $this->db->query("select id from distribusi_retur where penerimaan_distribusi_id = '$id'")->row();
      
        if (isset($rows->id)) {
            $rowA= $this->db->query("select id from distribusi_retur_penerimaan where distribusi_retur_id = '".$rows->id."'")->row();
        }
        if (isset($rows->id)) {
            $this->db->delete('transaksi_detail', array('transaksi_id' => $rows->id, 'transaksi_jenis' => 'Retur Distribusi'));
        }
        if (isset($rowA)) {
            $this->db->delete('transaksi_detail', array('transaksi_id' => $rowA->id, 'transaksi_jenis' => 'Penerimaan Retur Distribusi'));
        }
        $this->db->delete('distribusi_penerimaan', array('id' => $id));
        $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Penerimaan Distribusi'));
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
