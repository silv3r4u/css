<link rel="stylesheet" href="<?= base_url('/assets/css/workspace.css') ?>" />
<script type="text/javascript">
    function cetak() {
        window.print();
        window.close();
    }
</script>
<title><?= $title ?></title>
<body onload="cetak()">
<div class="layout-printer">
<table class="header-printer" style="border-bottom: 1px solid #000;" width="100%">
    <tr><td colspan="4" align="center" style="text-transform: uppercase">Rumah Sakit <?= $apt->nama ?></td> </tr>
    <tr><td colspan="4" align="center"><?= $apt->alamat ?> <?= $apt->kelurahan ?></td> </tr>
    <tr><td colspan="4" align="center">Telp. <?= $apt->telp ?>,  Fax. <?= $apt->fax ?>, Email <?= $apt->email ?></td> </tr>
</table>
<?php
foreach ($attribute as $rows);
?>
<table width="100%">
    <tr><td width="20%">Tanggal</td><td><?= datefrompg($rows->tgl_layan) ?></td></tr>
    <tr><td>No. Kunjungan</td><td><?= $rows->no_daftar ?></td></tr>
    <tr><td>Nama / No Pasien</td><td><?= $rows->nama ?> / <?= $rows->no_rm ?></td></tr>
    <tr><td>Umur</td><td><?= hitungUmur($rows->lahir_tanggal) ?></td></tr>
</table>
    <h2>Rincian</h2>
<table width="100%" class="list-data-printer">
    <tr>
        <th align="left">Jenis Layanan</th>
        <th>Subtotal</th>
    </tr>
    <?php 
    $total_jasa = 0;
    foreach ($list_jasa as $rowA) { ?>
        <tr><td><?= $rowA->nama ?></td><td align="center"><?= rupiah($rowA->tarif_layanan) ?></td></tr>
    <?php 
    $total_jasa = $total_jasa + $rowA->tarif_layanan;
    } 
    ?>
    <tr><td>Penjualan Barang</td><td align="center"><?= rupiah($list_barang->total_barang) ?></td></tr>
    <?php
    $total_ri = 0;
    foreach ($rawat_inap as $rowB) { ?>
        <tr><td>Pemakaian <?= $rowB->nama ?> <?= $rowB->kelas ?></td><td align="center"><?= rupiah($rowB->tarif) ?></td></tr>
    <?php 
    $total_ri = $total_ri + $rowB->tarif;
    }
    ?>
    <tr><td>Total </td><td align="center"><?= rupiah($list_barang->total_barang+$total_jasa+$total_ri) ?></td></tr>
</table>
    <p>
        Untuk Pembayaran: Pembayaran ke <?= $bayar_ke ?> <br/>
    
        Jumlah Pembayaran: <?= rupiah($pembayaran->bayar) ?>, Sisa Tagihan: <?= rupiah($pembayaran->sisa) ?>
    </p>
</div>
</body>