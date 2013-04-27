<title><?= $title ?></title>
<script type="text/javascript">
$(function() {
    $('button').button();
    $('#deletion').click(function() {
        var ok=confirm('Anda yakin akan menghapus transaksi ini ?');
        if (ok) {
            var id = $('#stok_opname').html();
            $.get('<?= base_url('inventory/stok_opname_delete') ?>/'+id, function(data) {
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
<?php
    foreach ($list_data as $rows);
?>
<div>
    <h1 class="informasi"><?= $title ?></h1>
    <?= form_open('inventory/stok_opname', 'id=form_stok_opname') ?>
    <div class="data-input">
            <label>No.</label><span class="label" id="stok_opname"><?= $rows->transaksi_id ?></span>
            <label>Alasan</label><span class="label"><?= $rows->alasan ?></span>
    </div>
        <table class="tabel" width="100%">
            <thead>
            <tr>
                <th>Packing Barang</th>
                <th>ED</th>
                <th>HNA</th>
                <th>HPP</th>
                <th>HET</th>
                <th>Jumlah Sisa</th>
            </tr>
            </thead>
            <tbody>
                <?php
                
                foreach ($list_data as $key => $data) {
                ?>
                <tr class="<?= ($key%2==0)?'odd':'even' ?>">
                    <td><?= $data->barang ?> <?= ($data->kekuatan!='1')?$data->kekuatan:null ?>  <?= $data->satuan ?> <?= $data->sediaan ?> <?= ($data->pabrik == 'Non Generik')?'':$data->pabrik ?> @ <?= ($data->isi==1)?'':$data->isi ?> <?= $data->satuan_terbesar ?></td>
                    <td align="center"><?= datefmysql($data->ed) ?></td>
                    <td align="right"><?= inttocur($data->hna) ?></td>
                    <td align="right"><?= inttocur($data->hpp) ?></td>
                    <td align="right"><?= inttocur($data->het) ?></td>
                    <td align="center"><?= $data->sisa ?></td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
</div>
<?= form_button(null, 'Delete', 'id=deletion style="margin-left: 0;"') ?>