<title><?= $title ?></title>
<script type="text/javascript">
$(function() {
    $('#delete_pemakaian').click(function() {
        var ok=confirm('Anda yakin akan menghapus transaksi ini ?');
        if (ok) {
            var id = $('#id_pemakaian').html();
            $.get('<?= base_url('inventory/pemakaian_delete') ?>/'+id+'?_'+Math.random(), function(data) {
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
<?php foreach ($list_data as $rows); ?>
<div>
    <h1><?= $title ?></h1>
    <div class="data-input">
        <fieldset><legend>Summary</legend>
            <label>No.</label><span class="label" id="id_pemakaian"><?= $rows->transaksi_id ?></span>
            <label>Tanggal</label><span class="label"><?= datetime($rows->waktu) ?></span>
        </fieldset>
    </div>
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
                <tr>
                    <th width="80%">Packing Barang</th>
                    <th width="10%">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 0;
                foreach ($list_data as $key => $data) {
                    $hjual = ($data->hna*($data->margin/100))+$data->hna;
                    if ($data->id_obat == null) {
                        $packing = $data->barang.' '.$data->pabrik.' @ '.(($data->isi==1)?'':$data->isi).' '.$data->satuan_terkecil;
                    } else {
                        $packing = $data->barang .' '.(($data->kekuatan != '1')?$data->kekuatan:null).' '. $data->satuan.' '. $data->sediaan .' '.(($data->generik == 'Non Generik')?'':$data->pabrik).' '.(($data->isi==1)?'':'@ '.$data->isi).' '. $data->satuan_terkecil;
                    } ?>
                <tr class="<?= ($key%2==0)?'odd':'even' ?> tr_row">
                    <td><?= $packing ?></td>
                    <td align="center"><?= $data->keluar ?></td>
                </tr>
                <?php 
                $no++; 
                } ?>
            </tbody>
        </table>
    </div>
    <?= form_button(null, 'Delete', 'id=delete_pemakaian') ?>
</div>