<title><?= $title ?></title>
<script type="text/javascript">
$(function() {
    $('#deletion').click(function() {
        var ok=confirm('Anda yakin akan menghapus transaksi ini ?');
        if (ok) {
            var id = $('#repackage_id').html();
            $.get('<?= base_url('inventory/repackage_delete') ?>/'+id, function(data) {
                if (data == true) {
                    alert_delete();
                }
            },'json');
            $(this).closest("#result_detail").dialog('close');
            $('#loaddata').load('<?= base_url('laporan/stok?'.generate_get_parameter($_GET)) ?>');
        } else {
            return false;
        }
    })
})
</script>
<?php
    foreach ($list_data as $data);
?>
<div>
    <h1><?= $title ?></h1>
    <div class="data-input">
        <?= form_open('inventory/repackage_save', 'id=form_repackage') ?>
        <fieldset><legend>Summary</legend>
                <label>No.</label><span class="label" id="repackage_id"><?= $data->transaksi_id ?></span>
                <?php foreach ($list_data as $key => $data) { 
                    if ($data->id_obat == null) {
                        $packing = $data->barang.' '.$data->pabrik.' @ '.(($data->isi==1)?'':$data->isi).' '.$data->satuan_terkecil;
                    } else {
                        $packing = $data->barang .' '.(($data->kekuatan != '1')?$data->kekuatan:null).' '. $data->satuan.' '. $data->sediaan .' '.(($data->generik == 'Non Generik')?'':$data->pabrik).' '.(($data->isi==1)?'':'@ '.$data->isi).' '. $data->satuan_terkecil;
                    } ?>
                    <label>Packing Barang <?= $label[$key] ?>: </label><span class="label"><?= $packing ?></span>
                    <label>Jumlah <?= $label[$key] ?>: </label><span class="label"><?= $data->$jumlah[$key] ?></span>
                <?php } ?>
                <label></label><?= form_hidden('isi_hasil', null) ?>
        </fieldset>
        <?= form_close() ?>
        <?= form_button(null, 'Delete', 'id=deletion') ?>
    </div>
</div>