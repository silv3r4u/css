<script type="text/javascript">
$(function() {
    $("#table").tablesorter();
    $('#checkall').live('click', function() {
        $('#checkall').html('Uncheck all');
        $('#checkall').attr('id', 'uncheckall');
        $('.check').attr('checked', 'checked');
    });
    $('#uncheckall').live('click', function() {
        $('#uncheckall').html('Check all');
        $('#uncheckall').attr('id', 'checkall');
        $('.check').removeAttr('checked');
    });
    $('#form_harga_jual2').submit(function() {
        var status = ($('.check').is(':checked') === true);
        
        if (status === true) {
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                success: function(data) {
                    $('#result_load').html(data);
                    $('#result_load').dialog({
                        autoOpen: true,
                        modal: true,
                        width: 700,
                        height: 400,
                        close: function() {
                            $("#result_load").dialog().remove();
                            var id_pb= $('#pb').val();
                            $.ajax({
                                url: '<?= base_url('referensi/harga_jual_load') ?>',
                                data: 'pb='+id_pb,
                                cache: false,
                                success: function(msg) {
                                    $('#result').html(msg);
                                }
                            })
                        }
                    })
                }
            })
        } else {
            alert('Barang belum ada yang dipilih !');
        }
        return false;
    })
    $('#simpan').each(function(){
        $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
    });
    $('#simpan').button({
        icons: {
            secondary: 'ui-icon-circle-check'
        }
    });
    $('#resethj').each(function(){
        $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'" onclick="'+$(this).attr('onclick')+'">' + $(this).val() + '</button>');
    });
    $('#resethj').button({
        icons: {
            secondary: 'ui-icon-refresh'
        }
    });
    $('input[type=submit]').each(function(){
        $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
    });
    $('button[type=submit]').button({
        icons: {
            primary: 'ui-icon-pencil'
        }
    });
})
</script>
<div id="result_load"></div>
<div class="data-list">
<?= form_open('referensi/harga_jual_update', 'id=form_harga_jual2') ?>
<?= form_button(NULL, 'Check all', 'id=checkall') ?>
<table class="sortable form-inputan" width="100%" id="table">
    <thead>
    <tr>
        <th class="nosort"><h3>#</h3></th>
        <th class="nosort"><h3>Tanggal</h3></th>
        <th><h3>Packing Barang</h3></th>
        <th class="sortright"><h3>HNA (Rp.)</h3></th>
        <th class="nosort"><h3>Margin (%)</h3></th>
        <th class="nosort"><h3>Diskon (%)</h3></th>
        <th class="sortright"><h3>Harga Jual (Rp.)</h3></th>
    </tr>
    </thead>
    <tbody>
<?php
$jumlah = 0;
foreach ($list_data as $key => $data) {
$harga_jual = ($data->hna+($data->hna*$data->margin/100)) - (($data->hna+($data->hna*$data->margin/100))*($data->diskon/100));
?>
    <tr class="<?= ($key%2==0)?'odd':'even' ?>">
        <td align="center"><?= form_checkbox('pb[]', $data->barang_packing_id, FALSE, 'class=check')  ?></td>
        <td align="center"><?= datefmysql($data->tanggal) ?></td>
        <td><?= $data->barang ?> <?= $data->kekuatan ?>  <?= $data->satuan ?> <?= $data->sediaan ?> <?= $data->pabrik ?> @ <?= ($data->isi==1)?'':$data->isi ?> <?= $data->satuan_terbesar ?></td>
        <td align="right"><?= inttocur($data->hna) ?></td>
        <td align="center"><?= $data->margin ?></td>
        <td align="center"><?= $data->diskon ?></td>
        <td align="right"><?= inttocur($harga_jual) ?></td>
    </tr>
<?php 
$jumlah++;
} 
?>
        </tbody>
</table>

<?= form_submit('submit', 'Pilih', 'id=update') ?>
<?= form_close() ?>
</div>