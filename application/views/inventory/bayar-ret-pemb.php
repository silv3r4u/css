<script type="text/javascript">
$(function() {
    $('button[id=reset]').button({
        icons: {
            primary: 'ui-icon-refresh'
        }
    });
    $('button[id=cetak]').button({
        icons: {
            primary: 'ui-icon-print'
        }
    });
    $('button[id=addnewrow]').button({
        icons: {
            secondary: 'ui-icon-circle-plus'
        }
    });
    $('input[type=submit]').each(function(){
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
    i = 1;
    for(x = 0; x <= i; x++) {
        add(x);
    }
    $('#addnewrow').click(function() {
        row = $('.tr_row').length + 1;
        add(row);
        i++;
    });
});
function eliminate(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);
    var jumlah = $('.tr_row').length+1;
    //alert(jumlah)
    for (i = 0; i <= jumlah; i++) {
        $('.tr_row:eq('+i+')').children('td:eq(0)').children('.id_retur').attr('id','retur'+(i+1));
        $('.tr_row:eq('+i+')').children('td:eq(2)').children('.subtotal').attr('id','subtotal'+(i+1));
    }
    sumTotal();
    hitungKembali();
}
function add(i) {
     str = '<tr class=tr_row>'+
                '<td><input type=text name=retur[] id=retur'+i+' class=retur size=50 /><input type=hidden name=id_retur[] id=id_retur'+i+' class=id_retur /></td>'+
                '<td id=supplier'+i+'></td>'+
                '<td id=subtotal'+i+' class=subtotal align=right></td>'+
                '<td class=aksi><a class=delete onclick=eliminate(this)></a></td>'+
            '</tr>';

    $('.form-inputan tbody').append(str);
    $('#ed'+i).datepicker({
        changeYear: true,
        changeMonth: true
    })
    $('#retur'+i).autocomplete("<?= base_url('common/autocomplete?opsi=retur_pembelian') ?>",
    {
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].id // nama field yang dicari
                };
            }
            return parsed;
        },
        formatItem: function(data,i,max){
            if (data.id != null) {
                var str = '<div class=result>'+data.id+'</div>';
            }
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        
        $(this).val(data.id);
        $('#id_retur'+i).val(data.id);
        $('#supplier'+i).html(data.suplier);
        $('#subtotal'+i).html(numberToCurrency(Math.ceil(data.subtotal)));
        sumTotal();
    });
    $('#bayar').keyup(function() {
        FormNum(this);
        hitungKembali();
    })
}
function hitungKembali() {
    var bayar = currencyToNumber($('#bayar').val());
    var total = currencyToNumber($('#total').html());
    var kembali = bayar - total;
    $('#kembalian').html(parseInt(kembali));
}
function sumTotal() {
    var jumlah = $('.tr_row').length-1;
    var total = 0;
    for (i = 0; i <= jumlah; i++) {
        if ($('#subtotal'+i).html() != '') {
            var subtotal = parseInt(currencyToNumber($('#subtotal'+i).html()));
            var total = total + subtotal;
        }
    }
    $('#total').html(numberToCurrency(total));
    $('#send_total').val(total);
}
</script>
<title><?= $title ?></title>
<div class="kegiatan" title="Pengembalian Uang Pembelian">
    <h1><?= $title ?></h1>
    <div class="data-input">
    <?= form_open('transaksi/bayar-ret-pemb', 'id=form_pembayaran_retur_pembelian') ?>
        <fieldset><legend>Pembayaran Retur Pembelian</legend>
            <label>No.</label> <span><?= isset($_GET['id'])?$_GET['id']:null ?></span>
            <label></label><?= form_button(null, 'Tambah Baris', 'id=addnewrow') ?>
        </fieldset>
    </div>
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="40%">ID Retur Pembelian</th>
                <th width="30%">Suplier</th>
                <th width="15%">Total @ (Rp.)</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
                <?php 
                if (isset($_GET['msg'])) {
                $rows = pembayaran_retur_pembelian($_GET['id']);
                $total = 0;
                foreach ($rows as $key => $data) { ?>
                <tr class="tr_row <?= ($key%2==1)?'even':'odd' ?>" >
                    <td width="40%" align="center"><?= $data['pembelian_retur_id'] ?></td>
                    <td id=supplier'+i+'><?= $data['suplier'] ?></td>
                    <td id=subtotal'+i+' class=subtotal align=right><?= rupiah($data['subtotal']) ?></td>
                    <td class=aksi><a class=delete onclick=eliminate(this)></a></td>
                </tr>
                <?php 
                $total = $total + $data['subtotal'];
                } } ?>
            </tbody>
        </table><br/>
        <?php
        $ttl = null;
        if (isset($_GET['id'])) {
            $data = _select_unique_result("select * from kas where transaksi_id = '$_GET[id]' and transaksi_jenis = 'Retur Pembelian'");
            $ttl = rupiah($total);
        }
        ?>
        <table align="left">
            <tr><td>Total (Rp.)</td><td>:</td><td align="right" id="total"><?= $ttl ?></td></tr>
            <?php
            if (!isset($_GET['id'])) {
            ?>
            <tr><td>Jumlah Pengembalian (Rp.)</td><td>:</td><td><input type="text" name="total" size="10" id="bayar" /> <input type="hidden" name="send_total" size="10" id="send_total" /> </td></tr>
            <tr><td>Kembalian (Rp.)</td><td>:</td><td align="right" id="kembalian"></td></tr>
            <?php } ?>
            <tr><td colspan="2"></td><td><?php if (!isset($_GET['id'])) { echo form_submit('save', 'Simpan', null); } ?> <?= form_button(null, 'Reset', 'id=reset') ?></td></tr>
            
        </table>
        
    </div>
    <?= form_close() ?>
</div>