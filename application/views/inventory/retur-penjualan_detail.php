<title><?= $title ?></title>
<script type="text/javascript">
$(function() {
    $('#delete_retur_penjualan').click(function() {
        var ok=confirm('Anda yakin akan menghapus transaksi ini ?');
        if (ok) {
            var id = $('#stok_opname').html();
            $.get('<?= base_url('inventory/retur_penjualan_delete') ?>/'+id, function(data) {
                if (data == true) {
                    alert_delete();
                }
            },'json');
            $(this).closest("#result_detail").dialog().remove();
            $('#loaddata').load('<?= base_url('laporan/stok?'.generate_get_parameter($_GET)) ?>');
        } else {
            return false;
        }
    })
})
</script>
<div>
    <h1><?= $title ?></h1>
    <?php
    foreach ($list_data as $rows); ?>
    
    <fieldset><legend>Summary</legend>
        <table width="100%">
            <tr><td width="15%">No.</td><td id="id_retur_penjualan"><?= $rows->transaksi_id ?></td> </tr>
            <tr><td>Waktu</td><td><?= datetime($rows->waktu) ?></td></tr>
            <tr><td>Pembeli</td><td><?= isset($rows->pembeli)?$rows->pembeli:'-' ?></td> </tr>
        </table>
    </fieldset> 
    
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th>Packing Barang</th>
                <th>Harga Jual</th>
                <th>Diskon</th>
                <th>Jumlah Retur</th>
            </tr>
            </thead>
            <tbody>
                <?php
                foreach ($list_data as $key => $rows) {
                    $harga_jual = $rows->hna+($rows->hna*($rows->margin/100)) - ($rows->hna*($rows->diskon/100)); ?>
                    <tr class="tr_row <?= ($key%2==1)?'even':'odd' ?>">
                        <td><?= $rows->barang." ".(($rows->kekuatan != '1')?$rows->kekuatan:null)." ". $rows->satuan." ".$rows->sediaan." ".$rows->pabrik." @ ".(($rows->isi==1)?'':$rows->isi)." ". $rows->satuan_terkecil ?> <?= form_hidden('id_pb[]', $rows->barang_packing_id) ?></td>
                        <td align="right" id="harga_jual<?= $key ?>"><?= rupiah($harga_jual) ?></td>
                        <td id="disc<?= $key ?>" align="right"><?= $rows->diskon ?></td>
                        <td align="center"><?= $rows->masuk ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?= form_button(null, 'Delete', 'id=delete_retur_penjualan') ?>
</div>