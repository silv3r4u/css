<script type="text/javascript">
    $(function() {
        $('#retur').click(function() {
            var id = $('#transaksi_id').html();
            $.get('<?= base_url('inventory/retur_distribusi') ?>/'+id, function(data) {
                $("#result_detail").dialog().remove();
                $('#loaddata').html(data);
            })
            $("#result_detail").dialog().remove();
        })  
        $('#deletion').click(function() {
            var ok=confirm('Anda yakin akan menghapus transaksi ini ?');
            if (ok) {
                var id = $('#penerimaan_distribusi_id').html();
                $.get('<?= base_url('inventory/penerimaan_distribusi_delete') ?>/'+id, function(data) {
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
<title><?= $title ?></title>
<?php foreach ($list_data as $rows); ?>
<div>
    <h1><?= $title ?></h1>
    <div class="data-input">
        <fieldset><legend>Summary</legend>
            <label>No</label><span class="label" id="penerimaan_distribusi_id"><?= $rows->transaksi_id ?></span>
            <label>Waktu</label><span class="label"><?= datetime($rows->waktu) ?></span>
            <label>No. Distribusi</label><span class="label"><?= $rows->distribusi_id ?></span>
            <label>Dari</label><span class="label"><?= $rows->unit ?></span>
            <label>Ke Unit</label><span class="label"><?= $rows->tujuan ?></span>
            <label>Petugas</label><span class="label"><?= $rows->pegawai ?></span>

        </table>
        </fieldset>
    </div>
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="30%">Packing Barang</th>
                <th width="13%">ED</th>
                <th width="10%">Penerimaan</th>
            </tr>
            </thead>
            <tbody>
                <?php 
                    foreach ($list_data as $key => $data) { ?>
                    <tr class="<?= ($key%2==0)?'odd':'even' ?>">
                        <td><?= $data->barang ?> <?= $data->kekuatan ?> <?= $data->satuan ?> <?= $data->sediaan ?> <?= ($data->generik == 'Non Generik')?'':$data->pabrik ?> @ <?= ($data->isi=='1')?'':$data->isi ?> <?= $data->satuan_terkecil ?></td>
                        <td align="center"><?= datefmysql($data->ed) ?></td>
                        <td align="center"><?= $data->masuk ?></td>
                    </tr>
                <?php 
                } ?>
            </tbody>
        </table>
    </div>
    <?= form_close() ?>
    <?= form_button(null, 'Delete', 'id=deletion') ?>
    <?= form_button(NULL, 'Retur', 'id=retur') ?>
</div>