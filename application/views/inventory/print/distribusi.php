<link rel="stylesheet" media="print" href="<?= base_url('assets/css/workspace.css') ?>" />
<script type="text/javascript">
function printing() {
    window.print();
    window.close();
}
</script>
<body onload="printing()">
<?php 
if (count($list_data) > 0) {
foreach ($list_data as $rows); ?>
<div id="kegiatan">
    <h1 class="title"><?= $title ?></h1>
    <table width="100%">
        <tr><td>No:</td><td><?= $rows->transaksi_id ?></td></tr>
        <tr><td>Tanggal:</td><td><?= dateconvert(get_date_from_dt($rows->waktu)) ?></td></tr>
        <tr><td>Unit:</td><td><?= $rows->unit ?></td></tr>
    </table>
        <table class="table-cetak" width="100%">
            <thead>
                <tr>
                    <td width="70%">Packing Barang</td>
                    <td width="15%">ED</td>
                    <td width="10%">Jumlah</td>
                </tr>
            </thead>
            <tbody>
                <?php
                
                    foreach ($list_data as $key => $data) {
                        if ($data->id_obat == null) {
                            $packing = $data->barang." ".$data->pabrik."@".(($data->isi == 1) ? '' : $data->isi)." ". $data->satuan_terkecil;
                        } else {
                            $packing = $data->barang." ".(($data->kekuatan == '1')?'':$data->kekuatan) . " ".$data->satuan." ".$data->sediaan." ".(($data->generik == 'Non Generik') ? '' : $data->pabrik) . " @ " . (($data->isi == 1) ? '' : $data->isi) . " ". $data->satuan_terkecil;
                        }
                        ?>
                        <tr class="<?= ($key % 2 == 1) ? 'even' : 'odd' ?>">
                            <td><?= $packing ?></td>
                            <td align="center"><?= datefmysql($data->ed) ?></td>
                            <td align="center"><?= $data->keluar ?></td>
                        </tr>
                    <?php 
                    }
                ?>
            </tbody>
        </table>
</div>
<?php } else { ?>
<div class="information">
    Data tidak ditemukan ...
</div>
<?php } ?>
</body>