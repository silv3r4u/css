<title><?= $title ?></title>
<script type="text/javascript">
$(function() {
    $('#deletion').click(function() {
        var ok=confirm('Anda yakin akan menghapus transaksi ini ?');
        if (ok) {
            var id = $('#id_pemusnahan').html();
            $.get('<?= base_url('inventory/pemusnahan_delete') ?>/'+id, function(data) {
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
    foreach ($list_data as $rows);
?>
<div>
    <h1><?= $title ?></h1>
    <div class="data-input">
        <fieldset><legend>Summary</legend>
            <label>No</label><span class="label" id="id_pemusnahan"><?= $rows->transaksi_id ?></span>
            <label>Waktu</label><span class="label"><?= datetime($rows->waktu) ?></span>
            <label>Saksi Apotek</label><span class="label"><?= $rows->saksi_apotek ?></span>
            <label>Saksi BPOM</label><span class="label"><?= $rows->saksi_bpom ?></span>

        </fieldset>
    </div>
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="30%">Packing Barang</th>
                <th width="10%">ED</th>
                <th width="10%">HPP</th>
                <th width="10%">Jumlah Sisa</th>
                <th width="10%">Jumlah</th>
                <th width="5%">#</th>
            </tr>
            </thead>
            <tbody>
            <?php
            
                foreach ($list_data as $key => $data) { 
                if ($data->id_obat == null) {
                    $packing = $data->barang.' '.$data->pabrik.' @ '.(($data->isi==1)?'':$data->isi).' '.$data->satuan_terkecil;
                } else {
                    $packing = $data->barang .' '.(($data->kekuatan != '1')?$data->kekuatan:null).' '. $data->satuan.' '. $data->sediaan .' '.(($data->generik == 'Non Generik')?'':$data->pabrik).' '.(($data->isi==1)?'':'@ '.$data->isi).' '. $data->satuan_terkecil;
                }    
                ?>
                <tr>
                    <td><?= $packing ?></td>
                    <td align="center"><?= datefmysql($data->ed) ?></td>
                    <td align="right"><?= rupiah($data->hpp) ?></td>
                    <td align="center"><?= $data->sisa ?></td>
                    <td align="center"><?= $data->keluar ?></td>
                    <td>-</td>
                </tr>
                <?php 
                }   
            ?>
            </tbody>
        </table>
    </div>
<?= form_button(null, 'Delete', 'id=deletion') ?>
</div>