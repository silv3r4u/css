<?php
require_once 'app/lib/common/functions.php';
if (isset($_GET['do'])) {
    
    if ($_GET['do'] == 'pemesanan') {
        $sekarang = gmdate('Y-m-d' ,gmdate('U')+25200);
        $sql = "select id, 
            (select (sum(masuk) - sum(keluar)) as sisa from transaksi_detail where transaksi_jenis != 'Pemesanan' and barang_packing_id = '$_GET[id]' and unit_id = '$_SESSION[id_unit]') as sisa, 
            (select leadtime_hours from transaksi_detail where transaksi_jenis = 'Pembelian' and barang_packing_id = '$_GET[id]' and unit_id = '$_SESSION[id_unit]' order by id desc limit 1) as leadtime_hours,
            (select ss from transaksi_detail where transaksi_jenis = 'Pembelian' and barang_packing_id = '$_GET[id]' and unit_id = '$_SESSION[id_unit]' order by id desc limit 1) as ss,
            (select avg(keluar) from transaksi_detail where barang_packing_id = '$_GET[id]' and unit_id = '$_SESSION[id_unit]' and date(waktu) 
                between (select date(waktu) from transaksi_detail where transaksi_jenis = 'Pembelian' and barang_packing_id = '$_GET[id]' and unit_id = '$_SESSION[id_unit]' group by barang_packing_id) and '$sekarang') as average_usage
                from transaksi_detail t
            where id = (select max(id) from transaksi_detail where barang_packing_id = '$_GET[id]' and unit_id = '$_SESSION[id_unit]' and transaksi_jenis != 'Pemesanan')";
        $row = _select_unique_result($sql);
        die(json_encode(array('row' => $row)));
    }
    if ($_GET['do'] == 'getHarga') {
        $sql = "select d.hpp, b.margin, (d.hna*(b.margin/100)+d.hna) as harga, b.diskon from transaksi_detail d 
            join barang_packing b on (d.barang_packing_id = b.id) 
            where d.transaksi_jenis != 'Pemesanan' and d.barang_packing_id = '$_GET[id]' and d.unit_id = '$_SESSION[id_unit]' order by d.id desc limit 1";
        $row = _select_unique_result($sql);
        die(json_encode(array('row' => $row)));
    }
    if ($_GET['do'] == 'getPenjualanField') {
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
            where d.transaksi_jenis != 'Pemesanan' and d.barang_packing_id = (select id from barang_packing where barcode = '$_GET[barcode]') order by d.id desc limit 1";
        $row = _select_unique_result($sql);
        die(json_encode($row));
    }
    
}
if (isset($_GET['distribusiGetSisa'])) {
    $sql = "select * from transaksi_detail where barang_packing_id = '$_GET[id_pb]' and ed = '".  date2mysql($_GET['ed'])."' and unit_id = '$_SESSION[id_unit]' and sisa > 0 order by id desc limit 1";
    die(json_encode(_select_unique_result($sql)));
}
if (isset($_GET['id_pasien'])) {
    $sql = "select * from penduduk WHERE id = '$_GET[id]'";
    die(json_encode(_select_unique_result($sql)));
}
if (isset($_GET['pemusnahanGetSisa'])) {
    $sql = "select * from transaksi_detail where barang_packing_id = '$_GET[id_pb]' and ed = '".  date2mysql($_GET['ed'])."' and unit_id = '$_SESSION[id_unit]' and sisa > 0 order by id desc limit 1";
    die(json_encode(_select_unique_result($sql)));
}
if (isset($_GET['id_pembeli'])) {
    $sql = "select * from asuransi_kepesertaan a join asuransi_produk p on (a.asuransi_produk_id = p.id) where a.penduduk_id = '$_GET[id_pembeli]'";
    $row = _select_arr($sql);
    foreach ($row as $key =>  $rows) {
        echo ++$key.". $rows[nama]<br/>";
    }
    //die(json_encode());
}

if (isset($_GET['cekmember'])) {
    $sql = "select member from penduduk where id = '$_GET[penduduk]'";
    $row = _select_unique_result($sql);
    $hasil = 0;
    if ($row['member'] == 'Ya') {
        $get = _select_unique_result("select * from apotek");
        $hasil = abs($get['diskon_penjualan']);
    }
    die(json_encode($hasil));
}

if (isset($_GET['cekdiskonbank'])) {
    $sql = "select diskon_penjualan from relasi_instansi where id = '$_GET[id_bank]'";
    die(json_encode(_select_unique_result($sql)));
}

if (isset($_GET['act'])) {
    if ($_GET['act'] == 'checkpembelian') {
        $sql = "select count(*) as jumlah from pembelian where pemesanan_id = '$_GET[id]'";
        $row = _select_unique_result($sql);
        if ($row['jumlah'] > 0) {
            $result = true;
        } else {
            $result = false;
        }
        die(json_encode(array('status' => $result)));
    }
}

die;
?>
