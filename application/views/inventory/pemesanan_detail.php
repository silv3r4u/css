<?php $this->load->view('message'); ?>
<script type="text/javascript">
$(function() {
    $('#print').click(function() {
        var doc_no = $('#doc_no').html();
        var id_pemesanan = $('#id_auto').html();
        var perundangan = $('input[name=perundangan]').val();
        location.href='<?= base_url('inventory/pemesanan_cetak') ?>?no_doc='+doc_no+'&id='+id_pemesanan+'&perundangan='+perundangan;
    })
    $('#deletion').click(function() {
        var ok=confirm('Anda yakin akan menghapus transaksi ini ?');
        if (ok) {
            var id = $('#id_auto').html();
            $.get('<?= base_url('inventory/pemesanan_delete') ?>/'+id, function(data) {
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
if (count($list_data) > 0) { ?>
<h1 class="informasi"><?= $title ?></h1>
<?php    
    foreach($list_data as $rows);
   
?>
    <div class="data-input">
        <?= form_hidden('perundangan', $rows->perundangan); ?>
        <label>No</label><span id="id_auto" class="label"><?= $rows->id ?></span>
        <label>No. Dokumen</label><span id="no_doc" class="label"><?= $rows->dokumen_no ?></span>
        <label>Supplier</label><span class="label"> <?= isset($rows->suplier)?$rows->suplier:null ?></span>
    </div>
        
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="30%">Packing Barang</th>
                <th width="10%">Jumlah</th>
                <th width="10%">Jumlah Sisa</th>
                <th width="10%">ROP</th>
            </tr>
            </thead>
            <tbody>
                <?php
                foreach ($list_data as $key => $data) {
                    if ($data->id_obat == null) {
                        $packing = $data->barang.' '.$data->pabrik.' @ '.(($data->isi==1)?'':$data->isi).' '.$data->satuan_terkecil;
                    } else {
                        $packing = $data->barang .' '.(($data->kekuatan != '1')?$data->kekuatan:null).' '. $data->satuan.' '. $data->sediaan .' '.(($data->generik == 'Non Generik')?'':$data->pabrik).' '.(($data->isi==1)?'':'@ '.$data->isi).' '. $data->satuan_terkecil;
                    } ?>
                
                        <tr class=<?= ($key%2==0)?'odd':'even' ?>>
                            <td><?= $packing ?></td>
                            <td align=center><?= $data->masuk ?></td>
                            <td align=center><?= $data->sisa ?></td>
                            <td align=center><?= $data->leadtime_hours ?></td>
                        </tr>
                    
                <?php }
                ?>
            </tbody>
        </table><br/>
        <?= form_button('delete', 'Hapus', 'id=deletion') ?> 
        <?= form_button('cetakexcel', 'Cetak Excel', 'id=print'); ?> 
    <?= form_close() ?>
<?php } else { ?>
        <div class="information">Data tidak ditemukan ...</div>
<?php } ?>
