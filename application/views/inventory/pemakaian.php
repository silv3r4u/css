<?php $this->load->view('message'); ?>
<script type="text/javascript">
function loading() {
    var url = $("#form_pemakaian").attr('action');
    $('#loaddata').load(url);
}
$(function() {
    $('button[id=deletion]').hide();
    $('#reset').click(function() {
        loading();
    })
    $('#deletion').click(function() {
        var ok=confirm('Anda yakin akan menghapus transaksi ini ?');
        if (ok) {
            var id = $('#id_pemakaian').html();
            $.get('<?= base_url('inventory/pemakaian_delete') ?>/'+id, function(data) {
                if (data.status == true) {
                    alert_delete();
                    loading();
                }
            },'json');
        } else {
            return false;
        }
    })
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
    $('#bayar').keyup(function() {
        FormNum(this);
        setKembali();
        
    })
})
$(function() {
    i = 1;
    <?php
    if (!isset($_GET['id'])) {
    ?>
    for(x = 0; x <= i; x++) {
        add(x);
    }
    <?php } ?>
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
        $('.tr_row:eq('+i+')').children('td:eq(0)').children('.bc').attr('id','bc'+i);
        $('.tr_row:eq('+i+')').children('td:eq(1)').children('.pb').attr('id','pb'+i);
        $('.tr_row:eq('+i+')').children('td:eq(1)').children('.id_pb').attr('id','id_pb'+i);
        $('.tr_row:eq('+i+')').children('td:eq(4)').children('.jl').attr('id','jl'+i);
    }
    subTotal();
}
function add(i) {
    
     str = '<tr class=tr_row>'+
                '<td><input type=text name=dr[] id=pb'+i+' class=pb size=75 /><input type=hidden name=id_pb[] id=id_pb'+i+' class=id_pb /></td>'+
                '<td><input type=text name=jl[] id=jl'+i+' class=jl size=20 style="width: 100px;" onKeyup=subTotal() /></td>'+
                '<td class=aksi><a class=delete onclick=eliminate(this)></a><input type=hidden name=disc[] id=disc'+i+' /></td>'+
            '</tr>';

    $('.form-inputan tbody').append(str);
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
        width: 380, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
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
        $('#jl'+i).focus();
    });
}

$(function() {
    $('#tanggal').datetimepicker({
        changeYear: true,
        changeMonth: true
    });
    $("#form_pemakaian").submit(function() {
        var jumlah = $('.tr_row').length-1;
        for(i = 0; i <= jumlah; i++) {
            if ($('#jl'+i).val() == '') {
                if ($('#id_pb'+i).val() != '') {
                    alert('Jumlah tidak boleh kosong !');
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
                $('#id_pemakaian').html(data.id_pemakaian);
                $('button[type=submit]').hide();
                $('button[id=deletion]').show();
                alert_tambah();
            }
        })
        return false;
        
    });
})
</script>
<title><?= $title ?></title>
<div class="kegiatan">
    <h1><?= $title ?></h1>
    <?= form_open('inventory/pemakaian', 'id=form_pemakaian') ?>
    <?= form_hidden('id_pasien', null, 'id=id_pasien') ?>
    <div class="data-input">
        <fieldset><legend>Summary</legend>
        <label>No.</label><span class="label" id="id_pemakaian"><?= isset($_GET['id'])?$_GET['id']:get_last_id('pemakaian', 'id') ?></span>
        <label>Tanggal</label> <?= form_input('tanggal', date('d/m/Y H:i'), 'id=tanggal') ?>
        <label></label><?= form_button(null, 'Tambah Baris', 'id=addnewrow') ?>

        </fieldset>
    </div>
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
                <tr>
                    <th width="80%">Packing Barang</th>
                    <th width="10%">Jumlah</th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_GET['id'])) { 
                $penjualan = pemakaian_muat_data($_GET['id']);
                $no = 0;
                foreach ($penjualan as $key => $data) {
                    $hjual = ($data['hna']*($data['margin']/100))+$data['hna'];
                ?>
                <tr class="<?= ($key%2==0)?'odd':'even' ?> tr_row">
                    <td><?= "$data[barang] ".(($data['kekuatan'] != '1')?$data['kekuatan']:null)." $data[satuan] $data[sediaan] ".(($data['generik'] == 'Non Generik')?'':$data['pabrik'])." @ ".(($data['isi']==1)?'':$data['isi'])." $data[satuan_terkecil]"; ?></td>
                    <td align="center" id="jl<?= $no ?>"><?= $data['keluar'] ?></td>
                    <td align="center">-</td>
                </tr>
                <?php $no++; } 
                } ?>
            </tbody>
        </table>
    </div>
    
    <?= form_submit('save', 'Simpan', 'id=save') ?>
    <?= form_button('Reset', 'Reset', 'id=reset') ?>
    <?= form_button('delete', 'Hapus', 'id=deletion') ?> 
    <?= form_close() ?>
</div>