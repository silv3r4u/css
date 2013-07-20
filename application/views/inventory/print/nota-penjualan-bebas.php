<title><?= $title ?></title>
<style>
    * { font-family: Arial; font-size: 10px; line-height: 9px; }
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
<table style="border-bottom: 1px solid #000; font-size: 12px;" width="100%">
    <tr><td colspan="4" align="center" style="text-transform: uppercase; font-size: 12px;"><?= $apt->nama ?></td> </tr>
    <tr><td colspan="4" align="center" style="font-size: 12px;"><?= $apt->alamat ?> <?= $apt->kabupaten ?></td> </tr>
    <tr><td colspan="4" align="center" style="font-size: 12px;">Telp. <?= $apt->telp ?>,  Fax. <?= $apt->fax ?>, Email <?= $apt->email ?></td> </tr>
</table>
    <center><h1><?= $title ?></h1></center>
<table class="content-printer" style="border-bottom: 1px solid #000; width: 100%">
    <tr><td>No.:</td><td><?= $rows->transaksi_id ?></td></tr>
    <tr><td>Tanggal:</td><td><?= indo_tgl(date("Y-m-d")).' '.date("H:i") ?></td></tr>
    <?php if ($jenis == '') {?>
    <tr><td>Nama:</td><td><?= $rows->pasien ?></td></tr>
    <?php } ?>
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
        <td width="60%"><?= $data->barang." ". $data->satuan ?></td>
        <td align="right" id=hj<?= $no ?>><?= rupiah($harga) ?></td>
        <td align="center" id=diskon<?= $no ?>><?= $data->jual_diskon_percentage ?></td>
        <!--<td><?= rupiah(($data->jual_harga - (($data->jual_harga*($data->percent/100))))*$data->pakai_jumlah) ?></td>-->
        <td align="center"><?= $data->keluar ?></td>
        <td align="right"><?= rupiah($data->subtotal) ?></td>
    </tr>
<?php 
$tagihan = $tagihan + $data->subtotal;
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
    <tr><td>Biaya Embalage:</td><td align="right"><?= rupiah($rows->tuslah) ?></td></tr>
    <?php } 
    $money = $this->db->query("select total, bayar, pembulatan from penjualan where id = '".$rows->id_penjualan."'")->row();
    $disc_penjualan= ($tagihan+$rows->tuslah)-$money->total;
    $diskon_member = $tagihan*($rows->diskon_member/100)+$disc_penjualan;
    $totals = (($tagihan+$byapotek+$rows->tuslah)-$diskon_member)+($jml_ppn/100*($tagihan+$byapotek-$diskon_member));
    ?>
    <tr><td>Diskon:</td><td align="right"><?= rupiah($diskon_member) ?></td></tr>
    <!--<tr><td>PPN (%):</td><td align="right"><?= rupiah(round($jml_ppn/100*(($tagihan+$byapotek+$rows->tuslah)-$diskon_member))) ?></td></tr>-->
    <tr><td>Total:</td><td align="right"><?= inttocur($totals) ?></td></tr>
    <?php if ($rows->bayar != '0') { 
    $money = $this->db->query("select total, bayar, pembulatan from penjualan where id = '".$rows->id_penjualan."'")->row();
    ?>
    <tr><td>Bayar:</td><td align="right"><?= inttocur($money->bayar) ?></td></tr>
    <tr><td>Kembali:</td><td align="right"><?= inttocur($money->bayar-$totals) ?></td></tr>
    <?php } ?>
</table>
    <br/>
    <center style="border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;">
        TERIMA KASIH, SEMOGA LEKAS SEMBUH
    </center>
    <?php
    if (($money->bayar-$totals) >= 0) { ?>
    <table width="100%">
        <tr>
            <td width="50%">LUNAS</td><td width="50%" align="right"><?= $this->session->userdata('nama') ?></td>
        </tr>
    </table>
    <?php }
    ?>
</body>