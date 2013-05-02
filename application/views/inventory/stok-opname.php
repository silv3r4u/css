<?php $this->load->view('message'); ?>
<script type="text/javascript">
$(function() {
    $("#tanggal").datetimepicker();
    $('#form_stok_opname').submit(function() {
        var url = $(this).attr('action');
        $.ajax({
            type: 'POST',
            url: url,
            data: $(this).serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.status === true) {
                    $('button[type=submit]').hide();
                    $('#stok_opname').html(data.id_opname_stok);
                    alert_tambah();
                } else {
                    
                }
            }
        });
        return false;
    });
    $('#reset').click(function() {
        $('#loaddata').html('');
        var url = '<?= base_url('inventory/stok_opname') ?>';
        $('#loaddata').load(url);
    });
    $('button[id=reset]').button({
        icons: {
            primary: 'ui-icon-refresh'
        }
    });
    $('button[id=deletion]').button({
        icons: {
            primary: 'ui-icon-circle-close'
        }
    });
    $('button[id=retur]').button({
        icons: {
            primary: 'ui-icon-transferthick-e-w'
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
    for(x = 0; x <= 1; x++) {
        add(x);
    }
    $('#addnewrow').click(function() {
        row = $('.tr_row').length;
        add(row);
        i++;
    });
});
function eliminate(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);
    var jumlah = $('.tr_row').length-1;
    
    for (i = 0; i <= jumlah; i++) {
        $('.tr_row:eq('+i+')').children('td:eq(0)').children('.batch').attr('id','batch'+i);
        $('.tr_row:eq('+i+')').children('td:eq(1)').children('.pb').attr('id','pb'+i);
        $('.tr_row:eq('+i+')').children('td:eq(1)').children('.id_pb').attr('id','id_pb'+i);
        $('.tr_row:eq('+i+')').children('td:eq(1)').children('.id_barang').attr('id','id_barang'+i);
        $('.tr_row:eq('+i+')').children('td:eq(2)').children('.ed').attr('id','ed'+i);
        $('.tr_row:eq('+i+')').children('td:eq(3)').children('.hna').attr('id','hna'+i);
        $('.tr_row:eq('+i+')').children('td:eq(4)').children('.js').attr('id','js'+i);
    }
}
function add(i) {
     str = '<tr class=tr_row>'+
                '<td><input type=text name=batch[] id=batch'+i+' class=batch /></td>'+
                '<td><input type=text name=pb[] id=pb'+i+' class=pb size=45 />'+
                    '<input type=hidden name=id_pb[] id=id_pb'+i+' class=id_pb />'+
                    '<input type=hidden name=id_barang[] id=id_barang'+i+' class=id_barang /></td>'+
                '<td><input type=text name=ed[] id=ed'+i+' class=ed size=8 /></td>'+
                '<td><input type=text name=hna[] id=hna'+i+' class=hna size=5 onKeyup=FormNum(this) /></td>'+
                '<td><input type=text name=js[] id=js'+i+' class=js size=5 /></td>'+
                '<td class=aksi><span class=delete onclick=eliminate(this)><?= img('assets/images/icons/delete.png') ?></span></td>'+
            '</tr>';

    $('.form-inputan tbody').append(str);
    $('#ed'+i).datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#pb'+i).autocomplete("<?= base_url('inv_autocomplete/load_data_packing_barang') ?>",
    {
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].nama // nama field yang dicari
                };
            }
            return parsed;
        },
        formatItem: function(data,i,max){
            var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = '';
            if (data.isi !== '1') { var isi = '@ '+data.isi; }
            if (data.kekuatan !== null && data.kekuatan !== '0') { var kekuatan = data.kekuatan; }
            if (data.satuan !== null) { var satuan = data.satuan; }
            if (data.sediaan !== null) { var sediaan = data.sediaan; }
            if (data.pabrik !== null) { var pabrik = data.pabrik; }
            if (data.satuan_terkecil !== null) { var satuan_terkecil = data.satuan_terkecil; }
            if (data.id_obat === null) {
                var str = '<div class=result>'+data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
            } else {
                if (data.generik === 'Non Generik') {
                    var str = '<div class=result>'+data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil+'</div>';
                } else {
                    var str = '<div class=result>'+data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
                }
            }
            return str;
        },
        width: 350, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = '';
        if (data.isi !== '1') { var isi = '@ '+data.isi; }
        if (data.kekuatan !== null && data.kekuatan !== '0') { var kekuatan = data.kekuatan; }
        if (data.satuan !== null) { var satuan = data.satuan; }
        if (data.sediaan !== null) { var sediaan = data.sediaan; }
        if (data.pabrik !== null) { var pabrik = data.pabrik; }
        if (data.satuan_terkecil !== null) { var satuan_terkecil = data.satuan_terkecil; }
        if (data.id_obat === null) {
            $(this).val(data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil);
        } else {
            if (data.generik === 'Non Generik') {
                $(this).val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil);
            } else {
                $(this).val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil);
            }
        }
        
        $('#id_barang'+i).val(data.id_barang);
        $('#id_pb'+i).val(data.id);
        $('#bc'+i).val(data.barcode);
        $('#ed'+i).val(data.ed);
        $('#hna'+i).val(numberToCurrency(data.hna));
        $('#hpp'+i).val(data.hpp);
        $('#het'+i).val(data.het);
    });
}

</script>
<title><?= $title ?></title>
<div class="kegiatan">
    <h1><?= $title ?></h1>
    <?= form_open('inventory/stok_opname', 'id=form_stok_opname') ?>
    <div class="data-input">
    <fieldset><legend>Summary</legend>
        <div class="one_side">
            <label>No.:</label><span class="label" id="stok_opname"><?= isset($_GET['id'])?$_GET['id']:get_last_id('opname_stok', 'id') ?></span>
            <label>Waktu:</label><?= form_input('tanggal', date("d/m/Y H:i"), 'id=tanggal') ?>
            <label>Alasan:</label><?= form_input('alasan', NULL, 'id=alasan size=40') ?>
            <label></label><?= form_button(null, 'Tambah Baris', 'id=addnewrow') ?>
            </div>
    </fieldset>
    </div>
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th>No. Batch</th>
                <th>Packing Barang</th>
                <th>ED</th>
                <th>HNA</th>
                <th>Jumlah Sisa</th>
                <th>#</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table><br/>
        <?= form_submit('save', 'Simpan', 'id=submit') ?>
    <?= form_button('delete','Delete','id=deletion') ?>
    <?= form_button('Reset', 'Reset', 'id=reset') ?>
    </div>
    
    <?= form_close() ?>
    
    
</div>