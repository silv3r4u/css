<script type="text/javascript">
$(function() {
    $('#retur').click(function() {
        var id = $('#id_distribusi').html();
        $.get('<?= base_url('inventory/retur_distribusi') ?>/'+id, function(data) {
            $("#result_detail").dialog().remove();
            $('#loaddata').html(data);
        })
        $(this).closest("#result_detail").dialog('close');
    })
    $('#deletion').click(function() {
        var ok=confirm('Anda yakin akan menghapus transaksi ini ?');
        if (ok) {
            var id = $('#id_distribusi').html();
            $.get('<?= base_url('inventory/distribusi_delete') ?>/'+id, function(data) {
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
<div id="print_div" title="Cetak Distribusi" style="display: none"></div>
<div>
    <h1><?= $title ?></h1>
    <?php
    foreach ($list_data as $rows);
    ?>
    <div class="data-input">
        <fieldset><legend>Distribusi</legend>
            <label>No</label><span class="label" id="id_distribusi"><?= $rows->transaksi_id ?></span>
            <label>Tanggal</label> <span class="label"><?= dateconvert(get_date_from_dt($rows->waktu)) ?></span>
            <label>Unit</label><span class="label"><?= $rows->unit ?></span>
        </fieldset>
    </div>
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
                <tr>
                    <th width="30%">Packing Barang</th>
                    <th width="15%">ED</th>
                    <th width="15%">Sisa Stok</th>
                    <th width="10%">Jumlah</th>
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
                            <td align="center"><?= $data->sisa ?></td>
                            <td align="center"><?= $data->keluar ?></td>
                        </tr>
                    <?php }
                ?>
            </tbody>
        </table><br/>
        <!--<?= form_button(null, 'Retur', 'id=retur') ?>-->
        <?= form_button(null, 'Delete', 'id=deletion') ?>
    </div>

    <?= form_close() ?>
</div>