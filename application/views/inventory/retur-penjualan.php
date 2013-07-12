<?php $this->load->view('message'); ?>
<script type="text/javascript">
    $(function() {
        $('#deletion').hide();
        $('button[id=reset]').click(function() {
            $('button[type=submit]').show();
        })
        $('button[id=reset]').button({
            icons: {
                primary: 'ui-icon-refresh'
            }
        });
        $('input[type=submit]').each(function() {
            $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
        });
        $('button[type=submit]').button({
            icons: {
                primary: 'ui-icon-circle-check'
            }
        });
        $('button[id=deletion]').button({
            icons: {
                primary: 'ui-icon-circle-close'
            }
        });
    })
function eliminate(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);
    var jumlah = $('.tr_row').length;
    for (i = 1; i <= jumlah; i++) {
        $('.jml_retur').attr('id','jml_retur'+i);
        $('.hpp').attr('id','hpp'+i);
    }
}
function hitungRetur() {
        var jumlah = $('.tr_row').length-1;
        var returning = 0;
        for (i = 0; i <= jumlah; i++) {
            var hj = parseInt(currencyToNumber($('#harga_jual'+i).html()));
            var disc = parseInt($('#disc'+i).html())/100;
            var jml  = parseInt($('#jml_retur'+i).val());
            var hasil= (hj - (hj*disc))*jml;
            var returning = returning + hasil;
            //$('#subtotal'+i).html(numberToCurrency(returning));
            $('#subtotal'+i).html(numberToCurrency(hasil));
        }
}
$(function() {
    $('#tanggal').datetimepicker();
    $('#reset').click(function() {
        var url = '<?= base_url('laporan/stok') ?>';
        $('#loaddata').load(url);
    })
    $('#deletion').click(function() {
        var ok=confirm('Anda yakin akan menghapus transaksi ini ?');
        if (ok) {
            var id = $('#id_retur_penjualan').html();
            $.get('<?= base_url('inventory/retur_penjualan_delete') ?>/'+id, function(data) {
                if (data == true) {
                    alert_delete();
                    $('#loaddata').load('<?= base_url('laporan/stok') ?>');
                }
            },'json');
            $(this).closest("#result_detail").dialog('close');
        } else {
            return false;
        }
    })
    $('#form_retur_penjualan').submit(function() {
        var post = $(this).attr('action');
        $.ajax({
            type: 'POST',
            url: post,
            dataType: 'json',
            data: $(this).serialize(),
            success: function(data) {
                if (data.status == true) {
                    $('#id_retur_penjualan').html(data.id_retur_penjualan);
                    $('button[type=submit]').hide();
                    $('#deletion').show();
                    $('#tanggal,.jml_retur').attr('disabled','disabled');
                    alert_tambah();
                }
            }
        })
        return false;
    })
})
</script>
<title><?= $title ?></title>
<div class="kegiatan">
    <h1><?= $title ?></h1>
    <?php
    foreach ($list_data as $rows);
    ?>
    <?= form_open('inventory/retur_penjualan_save', 'id=form_retur_penjualan') ?>
    <?= form_hidden('id_penjualan', $rows->transaksi_id) ?>
    <fieldset><legend>Summary</legend>
        <table width="100%">
            <tr><td width="15%">No.:</td><td id="id_retur_penjualan"><?= get_last_id('penjualan_retur', 'id') ?></td> </tr>
            <tr><td>Waktu:</td><td><?= form_input('tanggal', date("d/m/Y H:i"), 'id=tanggal') ?></td></tr>
            <tr><td>Pembeli:</td><td><?= $rows->nama ?> <?= form_hidden('idpembeli', $rows->pasien_penduduk_id) ?></td> </tr>
        </table>
    </fieldset> 
    
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th>Packing Barang</th>
                <th>ED</th>
                <th>Harga Jual</th>
                <th>Diskon</th>
                <th>Jumlah Penjualan</th>
                <th>Jumlah Retur</th>
                <th>#</th>
            </tr>
            </thead>
            <tbody>
                <?php
                foreach ($list_data as $key => $rows) {
                    $harga_jual = $rows->hna+($rows->hna*($rows->margin/100)) - ($rows->hna*($rows->diskon/100)); ?>
                    <tr class="tr_row <?= ($key%2==1)?'even':'odd' ?>">
                        <td><?= $rows->barang." ".(($rows->kekuatan != '1')?$rows->kekuatan:null)." ". $rows->satuan." ".$rows->sediaan." ".$rows->pabrik." @ ".(($rows->isi==1)?'':$rows->isi)." ". $rows->satuan_terkecil ?> <?= form_hidden('id_pb[]', $rows->barang_packing_id) ?></td>
                        <td align="center"><?= datefmysql($rows->ed) ?></td>
                        <td align="right" id="harga_jual<?= $key ?>"><?= rupiah($harga_jual) ?></td>
                        <td id="disc<?= $key ?>" align="right"><?= $rows->diskon ?></td>
                        <td align="center"><?= round($rows->keluar,1) ?> <?= form_hidden('harga[]', $harga_jual) ?></td>
                        <td><?= form_input('jml_retur[]', round($rows->keluar,1), 'size=10 id=jml_retur'.$key.' class=jml_retur onkeyup=hitungRetur() ') ?> <?= form_hidden('ed[]', $rows->ed) ?></td>
                        <td align="center" class="aksi"><span class="delete" onclick="eliminate(this)"><?= img('assets/images/icons/delete.png') ?></span></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
    <?= form_submit('save', 'Simpan', 'id=simpan') ?>
    <?= form_button('delete', 'Delete', 'id=deletion') ?>
    <?= form_button(null, 'Reset', 'id=reset') ?>
    <?= form_close() ?>
</div>