<?php $this->load->view('message'); ?>
<script type="text/javascript">
$(function() {
    $('#tanggal').datetimepicker();
    $('#reset').click(function() {
        var url = $('#form_penerimaan_distribusi').attr('action');
        $('#loaddata').load(url)
    })
    $('button[id=reset]').button({
        icons: {
            primary: 'ui-icon-refresh'
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
    $('button[id=deletion]').button({
        icons: {
            primary: 'ui-icon-circle-close'
        }
    });
    $('input[name=save]').click(function() {
        if ($('#nodistribusi').val() == '') {
            alert('Nomor distribusi tidak boleh kosong !');
            $('#nodistribusi').focus();
            return false;
        }
    })
    $('#nodistribusi').autocomplete("<?= base_url('inv_autocomplete/get_nomor_distribusi') ?>",
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
            var str = '<div class=result>'+data.id+' - '+datefmysql(data.waktu)+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.id);
        $('#petugas').html(data.pegawai);
        var id = data.id;
        $.ajax({
            url:'<?= base_url('inventory/distribusi_load_data') ?>/'+id,
            cache: false,
            success: function(msg) {
                $('.form-inputan tbody').html(msg);
            }
        })
    });
    
})
$(function() {
    $('#form_penerimaan_distribusi').submit(function() {
        if ($('#nodistribusi').val() == '') {
            alert('Nomor distribusi tidak boleh kosong !');
            $('#nodistribusi').focus();
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
                    $('button[type=submit]').hide();
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
function add(i) {
     str = '<tr class=tr_row>'+
                
                '<td><input type=text name=pb[] id=pb'+i+' size=80 /><input type=hidden name=id_pb[] id=id_pb'+i+' /></td>'+
                '<td><input type=text name=ed[] id=ed'+i+' size=15 /></td>'+
                '<td id=jml_dist'+i+'></td>'+
                '<td><input type=text name=jp[] id=jp'+i+' size=10 /></td>'+
                '<td class=aksi><a class=delete onclick=eliminate(this)></a></td>'+
            '</tr>';

    $('.form-inputan tbody').append(str);
    $('#ed'+i).datepicker({
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
            if (data.kekuatan != null) { var kekuatan = data.kekuatan; }
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
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = '';
        if (data.isi != '1') { var isi = '@ '+data.isi; }
        if (data.kekuatan != null) { var kekuatan = data.kekuatan; }
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
        $('#bc'+i).val(data.barcode);
        
    });
}
function cek_jumlah(i) {
    //var jumlah = $('.tr_row').length-1;
    if ($('#jp'+i).val() == '') {
        alert('Jumlah penerimaan tidak boleh kosong !');
        $('#jp'+i).val('').focus();
    }
    var dist = parseInt($('#jml_dist'+i).html());
    var terima = parseInt($('#jp'+i).val());
    var selisih= dist - terima;
    if (selisih < 0) {
        alert('Jumlah penerimaan tidak boleh melebihi jumlah yang di distribusikan !');
        $('#jp'+i).val('').focus();
    }
}
</script>
<title><?= $title ?></title>
<div class="kegiatan">
    <h1><?= $title ?></h1>
    <?= form_open('inventory/penerimaan_distribusi', 'id=form_penerimaan_distribusi') ?>
    <div class="data-input">
        <fieldset><legend>Summary</legend>
            <label>No.:</label><span class="label"><?= isset($_GET['id'])?$_GET['id']:get_last_id('distribusi_penerimaan', 'id') ?></span>
            <label>Waktu:</label><?= form_input('tanggal', date("d/m/Y H:i"), 'id=tanggal') ?>
            <label>No. Distribusi:</label><?= form_input('nodistribusi', isset($_GET['id'])?$_GET['id_distribusi']:null, 'id=nodistribusi size=30') ?>
            <label>Petugas:</label><span class="label" id="petugas"><?= isset($rows['pegawai'])?$rows['pegawai']:null ?></span>

        </table>
        </fieldset>
    </div>
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="30%">Packing Barang</th>
                <th width="13%">ED</th>
                <?php if (!isset($_GET['id'])) { ?>
                <th width="10%">Distribusi</th>
                <?php } ?>
                <th width="10%">Penerimaan</th>
                <th width="5%">#</th>
            </tr>
            </thead>
            <tbody>
                <?php if (isset($_GET['id'])) { 
                    foreach ($array as $key => $data) { ?>
                    <tr class="<?= ($key%2==0)?'odd':'even' ?>">
                        <td><?= $data['barang'] ?> <?= $data['kekuatan'] ?> <?= $data['satuan'] ?> <?= $data['sediaan'] ?> <?= ($data['generik'] == 'Non Generik')?'':$data['pabrik'] ?> @ <?= ($data['isi']=='1')?'':$data['isi'] ?> <?= $data['satuan_terkecil'] ?></td>
                        <td align="center"><?= datefmysql($data['ed']) ?></td>
                        <?php if (!isset($_GET['id'])) { ?>
                        <td align="center" id="jml_dist<?= $no ?>"><?= $data['masuk'] ?></td>
                        <?php } ?>
                        <td align="center"><?= $data['masuk'] ?></td>
                        <td class=aksi>-</a></td>
                    </tr>
                <?php } 
                } ?>
            </tbody>
        </table>
        <?= form_submit('save', 'Simpan', null) ?>
        <?= form_button('Reset', 'Reset','id=reset') ?>
    </div>
    <?= form_close() ?>

</div>