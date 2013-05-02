<script type="text/javascript">
$(function() {
    $('#delete_retur_pembelian').click(function() {
        var ok=confirm('Anda yakin akan menghapus transaksi ini ?');
        if (ok) {
            var id = $('#retur_pembelian_id').html();
            $.get('<?= base_url('inventory/retur_pembelian_delete') ?>/'+id+'?_'+Math.random(), function(data) {
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
<h1 class="informasi"><?= $title ?></h1>
<?php
    
    foreach ($list_data as $data);
    //$retur = _select_unique_result("select sum(hpp) as total_retur from transaksi_detail where transaksi_jenis = 'Pembelian' and transaksi_id = '$_GET[id]'");
    ?><div class="data-input">
        <table width="100%">
            <tr><td width="10%">No.:</td><td id="retur_pembelian_id"><?= $data->transaksi_id ?></td> </tr>
            <tr><td>Waktu:</td><td><?= datetime($data->waktu) ?></td></tr>
            <tr><td>Suplier:</td><td><?= $data->suplier ?></td> </tr>
            <tr><td>Total:</td><td id="retur"></td></tr>
        </table>
        </div>
    
    
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th>Packing Barang</th>
                <th>ED</th>
                <th>HPP</th>
                <th>Jml Retur</th>
            </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($list_data as $key => $rows) { ?>
                
                <tr class="tr_row <?= ($key%2==1)?'even':'odd' ?>">
                    <td><?= $rows->barang." ".(($rows->kekuatan == 1)?'':$rows->kekuatan)." ". $rows->satuan." ". $rows->sediaan." ".(($rows->generik == 'Non Generik')?'':$rows->pabrik)." @ ".(($rows->isi==1)?'':$rows->isi)." ".$rows->satuan_terkecil ?></td>
                    <td align="center"><?= datefmysql($rows->ed) ?></td>
                    <td id="hpp<?= $key ?>" class="hpp" align="right"><?= rupiah($rows->hpp) ?></td>
                    <td align="center"><?= $rows->keluar ?></td>
                </tr>
               <?php 
               $total = $total + ($rows->hpp*$rows->keluar);
               } ?>
                
            </tbody>
        </table>
        <script>
            $('#retur').html(numberToCurrency(<?= $total ?>));
        </script>
<?= form_button(null, 'Delete', 'id=delete_retur_pembelian') ?>
</div>