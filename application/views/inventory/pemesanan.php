<?php $this->load->view('message'); ?>
<script type="text/javascript">
function reset_form() {
    $('#loaddata').html('');
    var url = '<?= base_url('inventory/pemesanan') ?>';
    $('#loaddata').load(url+'?_'+Math.random());
}
$(function() {
    //$('#deletion').hide();
    //$('button[id=deletion],button[id=print]').attr('disabled','disabled');
    $('button[id=reset]').click(function() {
        reset_form();
        $('button[type=submit]').show();
    })
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
    $('button[id=tambahrow]').button({
        icons: {
            secondary: 'ui-icon-circle-plus'
        }
    });
    $('button[id=reset]').button({
        icons: {
            primary: 'ui-icon-refresh'
        }
    });
    $('button[id=print]').button({
        icons: {
            primary: 'ui-icon-print'
        }
    });
    $('#tanggal').datetimepicker();
    $('button[id=print], button[id=deletion]').hide();
    $('#deletion').click(function() {
        var ok=confirm('Anda yakin akan menghapus transaksi ini ?');
        if (ok) {
            var id = $('#id_auto').html();
            $.get('<?= base_url('inventory/pemesanan_delete') ?>/'+id, function(data) {
                if (data.status == true) {
                    alert_delete();
                    reset_form();
                }
            },'json');
        } else {
            return false;
        }
    })
    $('#myform').submit(function() {
        if ($('input[name=id_suplier]').val() == '') {
            alert('Data suplier tidak boleh kosong !');
            $('#suplier').focus();
            return false;
        }
        if ($('input[name=id_sales]').val() == '') {
            alert('Data sales tidak boleh kosong !');
            $('#sales').focus();
            return false;
        }
        var jumlah = $('.tr_row').length-1;
        for (i = 0; i <= jumlah; i++) {
            if ($('#id_pb'+i).val() == '') {
                alert('Data packing barang tidak boleh kosong !');
                $('#pb'+i).focus();
                return false;
            }
            if (($('#jml'+i).val() == '') || ($('#jml'+i).val() == '0')) {
                alert('Jumlah pemesanan tidak boleh kosong !');
                $('#jml'+i).val('').focus();
                return false;
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
                    $('input').attr('disabled','disabled');
                    $('button[type=submit]').hide();
                    $('button[id=print], button[id=deletion]').show();
                    $('#id_auto').html(data.id_pemesanan);
                    $('input[name=id]').val(data.id_pemesanan);
                    //$('button[id=deletion],button[id=print]').removeAttr('disabled');
                    alert_tambah();
                }
            }
        })
        return false;
    })
    
    $('#print').click(function() {
        var doc_no = $('#doc_no').html();
        var id_pemesanan = $('input[name=id]').val();
        location.href='<?= base_url('inventory/pemesanan_cetak') ?>?no_doc='+doc_no+'&id='+id_pemesanan+'&perundangan=<?= isset($_GET['perundangan'])?$_GET['perundangan']:NULL ?>';
    })
    $('#suplier').autocomplete("<?= base_url('inv_autocomplete/load_data_instansi_relasi/supplier') ?>",
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
        $(this).attr('value',data.nama);
        $('input[name=id_suplier]').val(data.id);
    });
    $('#sales').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk/salesman') ?>",
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
            var kelurahan = data.kelurahan;
            if (data.kelurahan != 'null') {
                var kelurahan = '-';
            }
            var str = '<div class=result>'+data.nama+'<br/>'+data.alamat+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).attr('value',data.nama);
        $('input[name=id_sales]').val(data.penduduk_id);
    });
})
$(function() {
    i = 1;
    <?php
    if (!isset($_GET['id']) or (isset($_GET['id']) and !isset($_GET['do']))) { ?>
    for(x = 0; x <= i; x++) {
        add(x);
    }
    <?php } ?>
    $('#tambahrow').click(function() {
        row = $('.tr_row').length;
        //alert(row)
        add(row);
        i++;
    });
});


function hitung(i) {
    var biaya = currencyToNumber($('#biapesan'+i).val());
    var id_pb = $('#id_pb'+i).val();
    $.ajax({
        url: '<?= base_url('inv_autocomplete/hitung_detail_pemesanan') ?>/'+id_pb+'/'+biaya,
        dataType: 'json',
        cache: false,
        success: function(data) {
            $('#eoq'+i).html(data.eoq);
            $('#eoi'+i).html(data.eoi);
            
        }
    })
}

function eliminate(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);
    var jumlah = $('.tr_row').length-1;
    for (i = 0; i <= jumlah; i++) {
        $('.tr_row:eq('+i+')').children('td:eq(0)').children('.pb').attr('id','pb'+i);
        $('.tr_row:eq('+i+')').children('td:eq(0)').children('.id_pb').attr('id','id_pb'+i);
        $('.tr_row:eq('+i+')').children('td:eq(1)').attr('id','sisa'+i);
        $('.tr_row:eq('+i+')').children('td:eq(2)').attr('id','rop'+i);
        $('.tr_row:eq('+i+')').children('td:eq(3)').children('.biapesan').attr('id','biapesan'+i);
        $('.tr_row:eq('+i+')').children('td:eq(3)').children('.biapesan').attr('onblur','hitung('+i+')');
        $('.tr_row:eq('+i+')').children('td:eq(4)').attr('id','eoq'+i);
        $('.tr_row:eq('+i+')').children('td:eq(5)').attr('id','eoi'+i);
        $('.tr_row:eq('+i+')').children('td:eq(6)').attr('id','smin'+i);
        $('.tr_row:eq('+i+')').children('td:eq(7)').attr('id','smax'+i);
        $('.tr_row:eq('+i+')').children('td:eq(8)').children('.jml').attr('id','jml'+i);
    }
}

function add(i) {
     str = '<tr class=tr_row>'+
                '<td><input type=text name=pb[] id=pb'+i+' class=pb size=90 /><input type=hidden name=id_pb[] id=id_pb'+i+' class=id_pb size=10 /></td>'+
                '<td align=center class=sisa id=sisa'+i+'></td>'+
                '<td align=center class=rop id=rop'+i+'></td>'+
                '<td><input type=text name=biapesan[] id=biapesan'+i+' onblur="hitung('+i+')" class=biapesan size=10 onkeyup=FormNum(this) /></td>'+
                '<td align=center class=eoq id=eoq'+i+'></td>'+
                '<td align=center class=eoi id=eoi'+i+'></td>'+
                '<td align=center class=smin id=smin'+i+'></td>'+
                '<td align=center class=smax id=smax'+i+'></td>'+
                '<td><input type=text name=jml[] id=jml'+i+' class=jml size=10 onkeyup=Angka(this) /></td>'+
                '<td class=aksi><a class="delete" onclick=eliminate(this)></a></td>'+
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
        width: 430, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        var sisa = data.sisa;
        if (data.sisa == null) {
            var sisa = 0;
        }
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
        $('#bc'+i).val(data.barcode);
        var id_packing = data.id;
        $.ajax({
            url: '<?= base_url('inv_autocomplete/load_data_rop') ?>',
            data: 'id='+id_packing,
            cache: false,
            dataType: 'json',
            success: function(msg) {
                sisa = '0';
                if (msg.sisa != null) {
                    sisa = msg.sisa;
                }
                var rop = '0';
                //Reorder Point = (LD x AU )
                if (msg.leadtime_hours != null || msg.ss != null) {
                    var rop = Math.ceil(msg.leadtime_hours*msg.average_usage);
                    //alert(msg.row[2]+' - '+msg.row[4]+' - '+msg.row[3]);
                }
                var panjang = (sisa.length) - 3;
                var checking= sisa.substr(panjang, 3);
                if (checking == '.00') {
                    sisa = sisa.substr(0, panjang);
                }
                var smin = 2*rop;
                var smax = Math.ceil(smin+(msg.selisih_waktu_beli*msg.average_usage));
                $('#sisa'+i).html(sisa);
                $('#rop'+i).html(rop);
                $('#smin'+i).html(smin);
                $('#smax'+i).html(smax);
            }
        });
        var jml = $('.tr_row').length;
        if (jml - i === 1) {
            add(jml);
        }
        $('#jml'+i).focus();
    });
}
</script>
<title><?= $title ?></title>
<div class="kegiatan">
<h1><?= $title ?></h1>
<?php    
    $isi = null;
    if (isset($_GET['id']) and !isset($_GET['do'])) {?>
        <script type="text/javascript">
            $(function() {
                $('.tr_row').html('');
            })
        </script>
        <?php
        $psn = pemesanan_muat_data($_GET['id']);
        $pesan = pemesanan_muat_data($_GET['id']);
        foreach($psn as $rows);
        foreach ($pesan as $key => $data) {
            if ($data['id_obat'] == null) {
                $packing = "$data[barang] $data[pabrik] @ ".(($data['isi']==1)?'':$data['isi'])." $data[satuan_terkecil]";
            } else {
                $packing = "$data[barang] ".(($data['kekuatan'] != '1')?$data['kekuatan']:null)." $data[satuan] $data[sediaan] ".(($data['generik'] == 'Non Generik')?'':$data['pabrik'])." ".(($data['isi']==1)?'':'@ '.$data['isi'])." $data[satuan_terkecil]";
            }
            $isi.="
                <tr class='".(($key%2==0)?'odd':'even')."'>
                    <td>$packing</td>
                    <td align=center>$data[masuk]</td>
                    <td align=center>$data[sisa]</td>
                    <td align=center>$data[leadtime_hours]</td>
                    <td align=center>-</td>
                </tr>
            ";
        }
    }
    $row = null;
    if (isset($_GET['id']) and isset($_GET['do'])) { 
        $psn = pemesanan_muat_data($_GET['id']);
        $pesan = pemesanan_muat_data($_GET['id']);
        foreach($psn as $rows);
        
        foreach ($pesan as $key => $data) {
            if ($data['id_obat'] == null) {
                $packing = "$data[barang] $data[pabrik] @ ".(($data['isi']==1)?'':$data['isi'])." $data[satuan_terkecil]";
            } else {
                $packing = "$data[barang] ".(($data['kekuatan'] != '1')?$data['kekuatan']:null)." $data[satuan] $data[sediaan] ".(($data['generik'] == 'Non Generik')?'':$data['pabrik'])." ".(($data['isi']==1)?'':'@ '.$data['isi'])." $data[satuan_terkecil]";
            }
            
            $row.="
                <tr class='tr_row'>
                    <td><input type=text name=pb[] id='pb".$key."' value='$packing' class=pb size=40 /><input type=hidden name=id_pb[] id='id_pb".$key."' value='$data[id_pb]' class=id_pb size=10 /></td>
                    <td><input type=text name=jml[] id='jml".$key."' value='$data[masuk]' class=jml size=10 /></td>
                    <td align=center id='sisa".$key."'>$data[sisa]</td>
                    <td align=center id='rop".$key."'>$data[leadtime_hours] </td>
                    <td align=center>-</td>
            </tr>
            ";
            ?>
        <script type="text/javascript">
            $(function() {
            $('#pb<?= $key ?>').autocomplete("<?= base_url('common/autocomplete?opsi=packing-barang') ?>",
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
                        var str = '<div class=result>'+data.nama+' '+kekuatan+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
                    }
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                var sisa = data.sisa;
                if (data.sisa == null) {
                    var sisa = 0;
                }
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
                    $(this).val(data.nama+' '+kekuatan+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil);
                }
                $('#id_pb<?= $key ?>').val(data.id);
                $('#bc<?= $key ?>').val(data.barcode);
                var id_packing = data.id;
                $.ajax({
                    url: '<?= base_url('inventory/fillField') ?>',
                    data: 'do=pemesanan&id='+id_packing,
                    cache: false,
                    dataType: 'json',
                    success: function(msg) {
                        sisa = '0';
                        if (msg.row[1] != null) {
                            sisa = msg.row[1];
                        }
                        var rop = '0';
                        if (msg.row[2] != null || msg.row[3] != null) {
                            var rop = parseInt(msg.row[2]*msg.row[3]);
                        }
                        var panjang = (sisa.length) - 3;
                        var checking= sisa.substr(panjang, 3);
                        if (checking == '.00') {
                            sisa = sisa.substr(0, panjang);
                        }
                        $('#sisa<?= $key ?>').html(sisa);
                        $('#rop<?= $key ?>').html(rop);
                    }
                })
            });
            });
            </script>
        <?php
        }
    }
?>
    <?= form_open('inventory/save_pemesanan', 'id=myform') ?>
    <?= form_hidden('id', NULL) ?>
    <div class="data-input">
    <fieldset><legend>Summary</legend>
    
        <label>No.:</label><span id="id_auto" class="label"><?= get_last_id('pemesanan', 'id') ?></span>
        <label>No. Dokumen:</label><span id="no_doc" class="label"><?= !isset($_GET['id'])?get_last_id('pemesanan', 'id').'/'.date("dmY"):$rows['dokumen_no'] ?></span>
        <label>Waktu:</label><?= form_input('tanggal', date("d/m/Y H:i"), 'id=tanggal') ?>
        <label>Supplier:</label><?= form_input(null, isset($rows['suplier'])?$rows['suplier']:null, 'id=suplier size=40') ?>
        <?= form_hidden('id_suplier', isset($rows['suplier_relasi_instansi_id'])?$rows['suplier_relasi_instansi_id']:null) ?></td> </tr>
        <label>Salesman:</label><?= form_input(null, isset($rows['salesman'])?$rows['salesman']:null, 'id=sales size=40') ?>
        <?= form_hidden('id_sales', isset($rows['salesman_penduduk_id'])?$rows['salesman_penduduk_id']:null) ?></td> </tr>
        
        <label></label><?= form_button('Tambah Baris', 'Tambah Baris', 'id=tambahrow') ?>
        
    </table>
    </fieldset>
    </div>
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="30%">Packing Barang</th>
                <th width="10%">Jumlah Sisa</th>
                <th width="10%">ROP</th>
                <th width="10%">Bia. Pemesanan</th>
                <th width="5%">EOQ</th>
                <th width="5%">EOI</th>
                <th width="5%">Smin</th>
                <th width="5%">Smax</th>
                <th width="10%">Jumlah</th>
                <th width="5%">#</th>
            </tr>
            </thead>
            <tbody>
                <?= isset($isi)?$isi:$row ?>
            </tbody>
        </table><br/>
        <?= form_submit('Simpan', 'Simpan', 'id=simpan'); ?> 
        <?= form_button('Reset', 'Reset', 'id=reset'); ?> 
        <?= form_button('delete', 'Hapus', 'id=deletion') ?> 
        <?= form_button('cetakexcel', 'Cetak Excel', 'id=print'); ?> 
        
    </div>
    <?= form_close() ?>
        
</div>