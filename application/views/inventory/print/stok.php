<?php
header_excel("stok-".date("d-m-Y").".xls");
?>
<table width="100%" style="font-family: 'Lucida Sans Unicode'; color: #ffffff" bgcolor="#31849b">
    <tr><td colspan="9" align="center" style="text-transform: uppercase"><?= $datas->nama ?></td> </tr>
    <tr><td colspan="9" align="center"><?= $datas->alamat ?> <?= $datas->kelurahan ?></td> </tr>
    <tr><td colspan="9" align="center">Telp. <?= $datas->telp ?>,  Fax. <?= $datas->fax ?>, Email <?= $datas->email ?></td> </tr>
</table>
<table width="100%" style="font-family: 'Lucida Sans Unicode';">
    <tr><td colspan="9" align="center"><b>Laporan Stok <br/><?= $period ?></b></td></tr>
</table>
<div class="data-list">
<table border="1" width="100%" class="tabel">
    <tr>
        <th>Tanggal</th>
        <th>No. Transaksi</th>
        <th>Jenis Transaksi</th>
        <th>Packing Barang</th>
        <th>HPP</th>
        <th>Awal</th>
        <th>Masuk</th>
        <th>Keluar</th>
        <th>Sisa</th>
    </tr>
    <?php
    foreach ($list_data as $key => $data) { ?>

    <tr class="<?= ($key%2==0)?'odd':'even' ?>">
        <td><?= datetimefmysql($data->waktu) ?></td>
        <td align="center"><?= $data->transaksi_id ?></td>
        <td><?= $data->transaksi_jenis ?></td>
        <td><?= $data->barang ?> <?= $data->kekuatan ?>  <?= $data->satuan ?> <?= $data->sediaan ?> <?= $data->pabrik ?> @ <?= ($data->isi==1)?'':$data->isi ?> <?= $data->satuan_terkecil ?></td>
        <td align="right"><?= inttocur($data->hpp) ?></td>
        <td><?= $data->awal ?></td>
        <td><?= $data->masuk ?></td>
        <td><?= $data->keluar ?></td>
        <td><?= $data->sisa ?></td>
    </tr>
    <?php }
    ?>
</table>
    </div>