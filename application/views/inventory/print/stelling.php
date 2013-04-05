<script type="text/javascript" src="<?= base_url('assets/js/jquery-print.min.js') ?>"></script>
<script type="text/javascript">
    function PrintElem(elem) {
        $('#cetak').hide();
        Popup($(elem).printElement());
        $('#cetak').show();
    }

    function Popup(data) {
        //var mywindow = window.open('<?= $title ?>', 'Print', 'height=400,width=800');
        mywindow.document.write('<html><head><title> <?= $title ?> </title>');
        /*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');

        mywindow.print();
        mywindow.close();

        return true;
    }

</script>
<div class="layout-printer" id="mydiv">
<table class="header-printer" style="border-bottom: 1px solid #000;" width="100%">
    <tr><td colspan="4" align="center" style="text-transform: uppercase"><?= $apt->nama ?></td> </tr>
    <tr><td colspan="4" align="center"><?= $apt->alamat ?> <?= $apt->kelurahan ?> <?= $apt->kecamatan ?></td> </tr>
    <tr><td colspan="4" align="center">Telp. <?= $apt->telp ?>,  Fax. <?= $apt->fax ?>, Email <?= $apt->email ?></td> </tr>
</table>
<?php
foreach ($stelling as $data);
?>
<title><?= $data->barang ?> <?= $data->kekuatan ?>  <?= $data->satuan ?> <?= $_GET['awal'].'sd'.$_GET['akhir'] ?></title>
<table class="header-printer">
    <tr><td>Nama Obat</td><td>:</td><td><?= $data->barang ?> <?= ($data->kekuatan == '1')?'':$data->kekuatan ?> <?= $data->satuan ?> <?= $data->sediaan ?> <?= (($data->generik == 'Non Generik')?'':$data->pabrik) ?> @ <?= ($data->isi=='1')?'':$data->isi ?> <?= $data->satuan_terkecil ?></td></tr>
    <tr><td>Sediaan</td><td>:</td><td><?= $data->sediaan ?></td></tr>
</table>
<table class="table-cetak" width="100%">
    <tr>
        <th width="20%">No. Resep/Bukti</th>
        <th width="15%">Jenis Transaksi</th>
        <th width="15%">Tanggal</th>
        <th width="15%">Masuk</th>
        <th width="15%">Keluar</th>
        <th width="15%">Sisa</th>
    </tr>
    <?php
    foreach ($stelling as $key => $data) {?>
    <tr class="<?= ($key%2==0)?'even':'odd' ?>">
        <td align="center"><?= $data->transaksi_id ?></td>
        <td align="center"><?= $data->transaksi_jenis ?></td>
        <td align="center"><?= datetimefmysql($data->waktu) ?></td>
        <td align="center"><?= $data->masuk ?></td>
        <td align="center"><?= $data->keluar ?></td>
        <td align="center"><?= $data->sisa ?></td>
    </tr>
    <?php } ?>
</table>
<p align="center">
    <span id="SCETAK"><input type="button" class="tombol" value="Cetak" id="cetak" onClick="PrintElem('#stelling_cetak')"/></span>
</p>
</div>
<?php die; ?>