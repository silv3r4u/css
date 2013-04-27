<?= $this->load->view('message') ?>
<script type="text/javascript">
function set_harga_jual(i) {
    var hna = currencyToNumber($('#hna'+i).html());
    var margin = parseInt($('#margin'+i).val())/100;
    var diskon = parseInt($('#diskon'+i).val())/100;
    //var harga_jual = (hna+(hna*margin)) - ((hna+(hna*margin))*diskon);
    var harga_jual = (hna*(margin+1))-((hna*(margin+1))*diskon);
    $('#harga_jual'+i).val(numberToCurrency(parseInt(harga_jual)));
    //($data->hna+($data->hna*$data->margin/100)) - (($data->hna+($data->hna*$data->margin/100))*($data->diskon/100));
}

function set_margin(i) {
    var hna = currencyToNumber($('#hna'+i).html());
    var harga_jual = currencyToNumber($('#harga_jual'+i).val());
    var diskon = parseInt($('#diskon'+i).val())/100;
    var margin = (harga_jual - (hna+(hna*diskon)))/(hna - (hna*diskon));
    var hsl = margin;
    if (isNaN(margin)) {
        var hsl = '';
    }
    $('#margin'+i).val(hsl*100);
}
$(function() {
    $('#update').button();
    $('#resethj').click(function() {
        $('#form-update').fadeOut('fast');
    });
    $('#margin,#diskons').keyup(function() {
        var hna = parseInt(currencyToNumber($('#hna').val()));
        var margin = parseInt($('#margin').val());
        var diskon = parseInt($('#diskons').val());
        var hasil  = hna + (hna*(margin/100) - (hna*(diskon/100)));
        $('#harga').html(numberToCurrency(parseInt(Math.ceil(hasil))));
    });
    $('#form_harga_jual_update_save').submit(function() {
        var jml = $('.tr_rows').length-1;
        for (i = 0; i <= jml; i++) {
            if ($('#margin'+i).val() === '') {
                alert('Margin tidak boleh kosong !');
                $('#margin'+i).focus();
                return false;
            }
            if ($('#diskon'+i).val() === '') {
                alert('Diskon tidak boleh kosong !');
                $('#diskon'+i).focus();
                return false;
            }
        }
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            dataType: 'json',
            cache: false,
            success: function(msg) {
                if (msg.status === true) {
                    $("#result_load").dialog('close');
                    alert_edit();
                }
            }
        });
        return false;
    });
});
</script>
<div id="result_load"></div>
<?= form_open('referensi/harga_jual_update_save', 'id=form_harga_jual_update_save') ?>
<table class="tabel form-inputan" width="100%">
        <thead>
        <tr>
            <th>Packing Barang</th>
            <th>HNA (Rp.)</th>
            <th>Margin (%)</th>
            <th>Diskon (%)</th>
            <th>Harga Jual (Rp.)</th>
            <th>Stok Minimal</th>
        </tr>
        </thead>
        <tbody>
<?php
$jumlah = 0;
foreach ($list_data as $key => $data) {
$harga_jual = ($data->hna+($data->hna*$data->margin/100)) - (($data->hna+($data->hna*$data->margin/100))*($data->diskon/100));
?>
    <tr class="tr_rows <?= ($key%2==0)?'odd':'even' ?>">
        <td><?= form_hidden('id_pb[]', $data->barang_packing_id) ?><?= $data->barang ?> <?= $data->kekuatan ?>  <?= $data->satuan ?> <?= $data->sediaan ?> <?= $data->pabrik ?> @ <?= ($data->isi==1)?'':$data->isi ?> <?= $data->satuan_terbesar ?></td>
        <td align="right" id="hna<?= $key ?>"><?= inttocur($data->hna*$data->isi) ?></td>
        <td align="center"><?= form_input('margin[]', $data->margin, 'size=5 onkeyup=set_harga_jual('.$key.') id=margin'.$key) ?></td>
        <td align="center"><?= form_input('diskon[]', $data->diskon, 'size=5 onkeyup=set_harga_jual('.$key.') id=diskon'.$key) ?></td>
        <td align="right" id="hj<?= $key ?>"><?= form_input('harga_jual[]', inttocur($harga_jual*$data->isi), 'id=harga_jual'.$key.' size=10 onblur=FormNum(this) onkeyup=set_margin('.$key.')') ?></td>
        <td align="center"><?= $data->stok_minimal ?></td>
    </tr>
<?php 
$jumlah++;
} 
?>
        </tbody>
</table>
<br/>
<?= form_submit('submit', 'Simpan', 'id=update style="margin-left: 0;"') ?>
<?= form_close() ?>