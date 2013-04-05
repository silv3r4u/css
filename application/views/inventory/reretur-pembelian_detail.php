<title><?= $title ?></title>
<script type="text/javascript">
$(function() {
    $('#deletion').click(function() {
        var ok=confirm('Anda yakin akan menghapus transaksi ini ?');
        if (ok) {
            var id = $('#reretur_pembelian_id').html();
            $.get('<?= base_url('inventory/reretur_pembelian_delete') ?>/'+id, function(data) {
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
        <div class="total-retur">Total: Rp. <b id="returan"></b></div>
        <label>No. Retur:</label><span class="label" id="reretur_pembelian_id"><?= $rows->penerimaan_retur_id ?></span>
        <label>Waktu:</label><span class="label"><?= datetime($rows->waktu) ?></span>
        <label>Suplier:</label><span class="label"><?= $rows->suplier ?></span>
        <label>Salesman:</label><span class="label"><?= $rows->salesman ?></span>
        <label>Penerimaan Berupa:</label><span class="label"><?= ($rows->uang == '0')?'Barang':'Uang' ?></span>
        </table>
        </fieldset>
    </div>
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="34%">Packing Barang</th>
                <th width="11%">HPP</th>
                <th width="11%">ED</th>
                <th width="5%">Jumlah Retur</th>
                <th width="11%">Jumlah</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $total = 0;
            foreach($list_data as $key => $data) { 
                $rowA = $this->db->query("select keluar from transaksi_detail where transaksi_id = '".$data->retur_id."' and transaksi_jenis = 'Retur Pembelian' and barang_packing_id = '".$data->barang_packing_id."'")->row();
                if ($data->id_obat == null) {
                    $packing = $data->barang.' '.$data->pabrik.' @ '.(($data->isi==1)?'':$data->isi).' '.$data->satuan_terkecil;
                } else {
                    $packing = $data->barang .' '.(($data->kekuatan != '1')?$data->kekuatan:null).' '. $data->satuan.' '. $data->sediaan .' '.(($data->generik == 'Non Generik')?'':$data->pabrik).' '.(($data->isi==1)?'':'@ '.$data->isi).' '. $data->satuan_terkecil;
                } ?>
                <tr class="<?= ($key%2==0)?'odd':'even' ?>">
                    <td style="white-space: nowrap"><?= $packing ?></td>
                    <td align="right"><?= rupiah($data->hpp) ?></td>
                    <td align="center"><?= datefmysql($data->ed) ?></td>
                    <td align="center"><?= $rowA->keluar ?></td>
                    <td align="center"><?= $data->masuk ?></td>
                </tr>
                <?php 
                $total = $total + ($data->masuk*$data->hpp); 
                } ?>
            <script type="text/javascript">
                $(function() {
                    //alert(<?= $total ?>);
                    $('#returan').html(numberToCurrency(<?= $total ?>));
                    $('input[name=total]').val(<?= $total ?>);
                })
            </script>
            </tbody>
        </table>
    </div>
</div>