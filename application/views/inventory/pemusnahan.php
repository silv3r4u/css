<?php $this->load->view('message'); ?>
<script type="text/javascript">
function loading() {
    var url = '<?= base_url('inventory/pemusnahan') ?>';
    $('#loaddata').load(url);
}
$(function() {
    $('#deletion').hide();
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
    
    $('#cetak').click(function() {
        location.href='<?= base_url('cetak/inventory/pemusnahan') ?>?id=<?= isset($_GET['id'])?$_GET['id']:NULL ?>';
    })
    $('#sbpom').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_profesi/') ?>",
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
            var str = '<div class=result>'+data.nama+'<br/>'+data.alamat+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('input[name=id_sbpom]').val(data.id_penduduk);
    });
    $('#sapt').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_profesi/Apoteker') ?>",
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
            var str = '<div class=result>'+data.nama+'<br/>'+data.alamat+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('input[name=id_sapt]').val(data.id_penduduk);
    });
})
$(function() {
    $('#tanggal').datetimepicker();
    $('#reset').click(function() {
        var url = $('#form_pemusnahan').attr('action');
        $('#loaddata').load(url)
    })
    $('#deletion').click(function() {
        var ok=confirm('Anda yakin akan menghapus transaksi ini ?');
        if (ok) {
            var id = $('#id_pemusnahan').html();
            $.get('<?= base_url('inventory/pemusnahan_delete') ?>/'+id, function(data) {
                if (data.status == true) {
                    alert_delete();
                    loading();
                }
            },'json');
        } else {
            return false;
        }
    })
    $('#form_pemusnahan').submit(function() {
        if ($('input[name=id_sapt]').val() == '') {
            alert('Saksi apoteker tidak boleh kosong !');
            $('#sapt').focus();
            return false;
        }
        if ($('input[name=id_sbpom]').val() == '') {
            alert('Saksi BPOM tidak boleh kosong !');
            $('#sbpom').focus();
            return false;
        }
        var jumlah = $('.tr_row').length-1;
        for (i = 0; i <= jumlah; i++) {
            if (($('#id_pb'+i).val() != '') || ($('#ed'+i).val() != '')) {
                if ($('#jl'+i).val() == '') {
                    alert('Jumlah yang akan di musnahkan tidak boleh kosong !');
                    $('#jl'+i).focus();
                    return false;
                }
            }
        }
        var url = $(this).attr('action');
        $.ajax({
            type: 'POST',
            url: url,
            data: $(this).serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.status == true) {
                    $('#id_pemusnahan').html(data.id_pemusnahan);
                    $('button[type=submit]').hide();
                    $('#deletion').show();
                    alert_tambah();
                } else {

                }
            }
        })
        return false;
    })
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
    var jumlah = $('.tr_row').length;
    for (i = 1; i <= jumlah; i++) {

    }
}
function cek_jumlah(i) {
    
    var sisa = parseInt($('#jls'+i).html());
    var value= parseInt($('#jl'+i).val());
    if (sisa < value) {
        alert('Jumlah yang di musnahkan tidak boleh melebihi sisa yang tersedia !');
        $('#jl'+i).val('').focus();
        return false;
    }
}
function add(i) {
     str = '<tr class=tr_row>'+
                '<td><input type=text name=pb[] id=pb'+i+' size=70 /><input type=hidden name=id_pb[] id=id_pb'+i+' /></td>'+
                '<td><input type=text name=ed[] class=ed id=ed'+i+' size=10 /></td>'+
                '<td align=center id=hpp'+i+'></td>'+
                '<td align=center id=jls'+i+'></td>'+
                '<td><input type=text name=jl[] id=jl'+i+' size=10 onkeyup=cek_jumlah('+i+') /></td>'+
                '<td class=aksi><a class=delete onclick=eliminate(this)></a></td>'+
            '</tr>';
    $('.form-inputan tbody').append(str);
    $('#ed'+i).datepicker({
        onSelect: function(value, date) { 
            var id_pb = $('#id_pb'+i).val();
            $.ajax({
                url: '<?= base_url('inv_autocomplete/get_last_transaction') ?>',
                data: 'id_pb='+id_pb+'&ed='+value,
                dataType: 'json',
                success: function(msg) {
                    var nama = $('#pb'+i).val();
                    if (msg == false) {
                        alert('Packing barang '+nama+' dengan ED: '+value+' tidak ditemukan !');
                        $('#hpp'+i).html(''); $('#jls'+i).html(''); $('#jl'+i).val('');
                        $('#ed'+i).val('').focus();
                        return false;
                    }
                    $('#hpp'+i).html(numberToCurrency(msg['hpp']));
                    $('#jls'+i).html(msg['sisa']);
                    $('#jl'+i).removeAttr('disabled').focus();
                }
            })
        },
        changeYear: true,
        changeMonth: true
    })
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
            if (data.isi != '1') { var isi = '@ '+data.isi; }
            if (data.kekuatan != null && data.kekuatan != '0') { var kekuatan = data.kekuatan; }
            if (data.satuan != null) { var satuan = data.satuan; }
            if (data.sediaan != null) { var sediaan = data.sediaan; }
            if (data.pabrik != null) { var pabrik = data.pabrik; }
            if (data.satuan_terkecil != null) { var satuan_terkecil = data.satuan_terkecil; }
            if (data.id_obat == null) {
                var str = '<div class=result>'+data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
            } else {
                if (data.generik == 'Non Generik') {
                    var str = '<div class=result>'+data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil+'</div>';
                } else {
                    var str = '<div class=result>'+data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
                }
            }
            return str;
        },
        width: 400, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = '';
        if (data.isi != '1') { var isi = '@ '+data.isi; }
        if (data.kekuatan != null && data.kekuatan != '0') { var kekuatan = data.kekuatan; }
        if (data.satuan != null) { var satuan = data.satuan; }
        if (data.sediaan != null) { var sediaan = data.sediaan; }
        if (data.pabrik != null) { var pabrik = data.pabrik; }
        if (data.satuan_terkecil != null) { var satuan_terkecil = data.satuan_terkecil; }
        if (data.id_obat == null) {
            $(this).val(data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil);
        } else {
            if (data.generik == 'Non Generik') {
                $(this).val(data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil);
            } else {
                $(this).val(data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil);
            }
        }
        $('#id_pb'+i).val(data.id);
        //$('#ed'+i).html(datefmysql(data.ed));
        //$('#hpp'+i).html(numberToCurrency(data.hpp));
        //$('#jls'+i).html(data.sisa);
    });
}

</script>
<title><?= $title ?></title>
<div class="kegiatan">
    <h1><?= $title ?></h1>
    
    <?= form_open('inventory/pemusnahan', 'id=form_pemusnahan') ?>
    <div class="data-input">
        <fieldset><legend>Summary</legend>
            <label>No.:</label><span class="label" id="id_pemusnahan"><?= isset($_GET['id'])?$_GET['id']:get_last_id('pemusnahan', 'id') ?></span>
            <label>Waktu:</label><?= form_input('tanggal', date("d/m/Y H:i"), 'id=tanggal') ?>
            <label>Saksi Apotek:</label><?= form_input(null, isset($_GET['id'])?$attr[0]['saksi_apotek']:null, 'id=sapt size=40') ?> <?= form_hidden('id_sapt') ?>
            <label>Saksi BPOM:</label><?= form_input(null, isset($_GET['id'])?$attr[0]['saksi_bpom']:null, 'id=sbpom size=40') ?> <?= form_hidden('id_sbpom') ?>
            <label></label><?= isset($_GET['msg'])?'':  form_button(null, 'Tambah Baris', 'id=addnewrow') ?>

        </fieldset>
    </div>
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="30%">Packing Barang</th>
                <th width="10%">ED</th>
                <th width="10%">HPP</th>
                <th width="10%">Jumlah Sisa</th>
                <th width="10%">Jumlah</th>
                <th width="5%">#</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(isset($_GET['id'])) {
            $pemusnahan = pemusnahan_muat_data($_GET['id']);
                foreach ($pemusnahan as $key => $rows) { ?>
                <tr>
                    <td><?= "$rows[barang] $rows[kekuatan] $rows[satuan] $rows[sediaan] ".(($rows['generik'] == 'Non Generik')?'':$rows['pabrik'])." @ ".(($rows['isi']==1)?'':$rows['isi'])." $rows[satuan_terkecil]"; ?></td>
                    <td align="center"><?= datefmysql($rows['ed']) ?></td>
                    <td align="right"><?= rupiah($rows['hpp']) ?></td>
                    <td align="center"><?= $rows['sisa'] ?></td>
                    <td align="center"><?= $rows['keluar'] ?></td>
                    <td>-</td>
                </tr>
                <?php 
                }    
            }
            ?>
            </tbody>
        </table>
        
        <?= form_submit('save', 'Simpan', 'id=save') ?>
        <?= form_button(null, 'Reset', 'id=reset') ?>
        <?= form_button(null, 'delete', 'id=deletion') ?>
    </div>
    <?= form_close() ?>

</div>