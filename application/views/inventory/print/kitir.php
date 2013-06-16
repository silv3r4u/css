<title><?= $title ?></title>
<style>
    * { font-family: tahoma; font-size: 10px; }
</style>
<script type="text/javascript" src="<?= base_url('assets/js/jquery-print.min.js') ?>"></script>
<script type="text/javascript">
    function PrintElem() {
        window.print();
        window.close();
    }

</script>
<body onload="PrintElem()">
<?php
    foreach ($penjualan as $rows);
?>
<table style="border-bottom: 1px solid #000; font-size: 16px;" width="100%">
    <tr><td colspan="4" align="center" style="text-transform: uppercase; font-size: 16px;"><?= $apt->nama ?></td> </tr>
    <tr><td colspan="4" align="center" style="font-size: 16px;"><?= $apt->alamat ?> <?= $apt->kabupaten ?></td> </tr>
    <tr><td colspan="4" align="center" style="font-size: 16px;">Telp. <?= $apt->telp ?>,  Fax. <?= $apt->fax ?>, Email <?= $apt->email ?></td> </tr>
</table>
    <center><h1><?= $title ?></h1></center>
<table class="content-printer" style="border-bottom: 1px solid #000; width: 100%">
    <tr><td>No.:</td><td><?= $rows->transaksi_id ?></td></tr>
    <?php if ($jenis == '') {?>
    <tr><td>Nama:</td><td><?= $rows->pasien ?></td></tr>
    <?php } ?>
    <tr><td>Petugas:</td><td><?= $rows->pegawai ?></td></tr>
</table>

<table width="100%" style="border-bottom: 1px solid #000">
    <tr>
        <th align="left" width="20%">Barang</th>
        <th width="15%">Harga</th>
        <th width="15%">Disc(%)</th>
        <th width="15%">Qty</th>
        <th width="20%">Subtotal</th>
    </tr>
<?php

$no = 1;
$tagihan = 0;
//$diskon = 0;
$set = $this->m_referensi->get_setting()->row();
$f_kali = $set->h_resep;
if (isset($jenis) and $jenis != '') {
    $f_kali = 0;
}
foreach ($penjualan as $key => $data) {
    $hjual = ($data->hna*($data->margin/100))+$data->hna;
    $harga = $hjual+($hjual*($f_kali/100));
    ?>
    <tr valign="top" class="<?= ($key%2==0)?'even':'odd' ?> tr_row">
        <td width="60%"><?= $data->barang." ".(($data->kekuatan == '1')?'':$data->kekuatan)." ". $data->satuan." ".$data->sediaan." @ ".(($data->isi==1)?'':$data->isi)." ".$data->satuan_terkecil ?></td>
        <td align="right" id=hj<?= $no ?>><?= rupiah($harga) ?></td>
        <td align="center" id=diskon<?= $no ?>><?= $data->jual_diskon_percentage ?></td>
        <!--<td><?= rupiah(($data->jual_harga - (($data->jual_harga*($data->percent/100))))*$data->pakai_jumlah) ?></td>-->
        <td align="center"><?= $data->keluar ?></td>
        <td align="right"><?= rupiah($data->h_jual) ?></td>
    </tr>
<?php 
$tagihan = $tagihan + $data->h_jual;
$no++;
}

foreach ($penjualan as $rows);
$ppn = (isset($rows->ppn)?$rows->ppn:'0')*$tagihan;
$jml_ppn = isset($rows->ppn)?$rows->ppn:'0';
?>
</table>
<table width="100%">
    <tr><td>Total Tagihan:</td><td align="right"><?= inttocur($tagihan) ?></td></tr>
    <?php 
    $byapotek = 0;
    if ($rows->resep_id != NULL) { 
    $biaya = $this->db->query("select sum(t.nominal) as jasa_apoteker from resep_r rr join tarif t on (t.id = rr.tarif_id) where rr.resep_id = '".$rows->resep_id."'")->row();
    $byapotek = $biaya->jasa_apoteker;
    ?>
    <tr><td>Biaya Apoteker:</td><td align="right"><?= rupiah($biaya->jasa_apoteker) ?></td></tr>
    <?php } 
    $diskon_member = $tagihan*($rows->diskon_member/100);
    $totals = $tagihan-$diskon_member+$byapotek+($jml_ppn/100*$tagihan);
    ?>
    <tr><td>Diskon:</td><td align="right"><?= ($diskon_member) ?></td></tr>
    <tr><td>PPN (%):</td><td align="right"><?= $jml_ppn ?></td></tr>
    <tr><td>Total:</td><td align="right"><?= inttocur($totals) ?></td></tr>
    <?php if ($rows->bayar != '0') { 
    $money = $this->db->query("select total, bayar, pembulatan from penjualan where id = '".$rows->id_penjualan."'")->row();
    ?>
    <tr><td>Bayar:</td><td align="right"><?= inttocur($money->bayar) ?></td></tr>
    <tr><td>Kembali:</td><td align="right"><?= inttocur($money->bayar-$totals) ?></td></tr>
    <?php } ?>
</table>
</body>