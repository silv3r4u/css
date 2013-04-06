<?php $this->load->view('message'); ?>
<script>
$(function() {
    $('button').button();
    $('#deletion_ppuang').click(function() {
        var ok=confirm('Anda yakin akan menghapus transaksi ini ?');
        if (ok) {
            var id = $('#id_pp_uang').html();
            $.get('<?= base_url('inventory/pp_uang_delete') ?>/'+id, function(data) {
                if (data.status === true) {
                    alert_delete();
                }
            },'json');
            $(this).closest("#result_detail").dialog().remove();
            $('#loaddata').load('<?= base_url('laporan/kas?'.generate_get_parameter($_GET)) ?>');
        } else {
            return false;
        }
    });
});
</script>
<title><?= $title ?></title>
    <h1 class="informasi"><?= $title ?></h1>
    <?php foreach ($list_data as $rows); ?>
    <div class="data-input">
            <label>ID</label><span class="label" id="id_pp_uang"><?= $rows->id ?></span>
            <label>No. Dokumen</label><span class="label" id="no_document"><?= $rows->dokumen_no ?></span>
            <label>Tanggal</label><span class="label"><?= indo_tgl($rows->tanggal) ?></span>
            <label>Jenis Transaksi</label><span class="label"><?= $rows->jenis ?></span>
    </div>
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="70%">Nama Transaksi</th>
                <th width="20%">Jumlah (Rp.)</th>
            </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                    foreach ($list_data as $key => $data) { ?>
                        <tr class="tr_row <?= ($key%2==1)?'even':'odd' ?>">
                            <td><?= $data->penerimaan_pengeluaran_nama ?></td>
                            <td align="right"><?= ($data->jenis == 'Penerimaan')?rupiah($data->penerimaan):rupiah($data->pengeluaran) ?></td>
                        </tr>
                    <?php 
                    if ($data->jenis == 'Penerimaan') {
                        $jml = $data->penerimaan;
                    } else {
                        $jml = $data->pengeluaran;
                    }
                    $total = $total+$jml;
                    }
                ?>
            </tbody>
            <tfoot>
                <tr class="odd">
                    <td align="right">Total</td>
                    <td id="total" align="right"><?= $total ?></td>
                </tr>
            </tfoot>
        </table><br/>
        <?= form_button('delete', 'Delete', 'id=deletion_ppuang') ?>