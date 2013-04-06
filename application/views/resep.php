<title><?= $title ?></title>
<div id="data_cetak" style="display: none"></div>
<div id="result_cetak" style="display: none"></div>
<div class="kegiatan">
<?php $this->load->view('message'); ?>
<script type="text/javascript">
function cetakEtiket(val) {
    $(function() {
        $('.cetaketiket').click(function() {
            window.open('<?= base_url('cetak/transaksi/etiket') ?>?id=&no_r='+val,'mywindow','location=1,status=1,scrollbars=1,width=430px,height=300px');
        })
    })
}
$(function() {
    $('#copyresep, #print').hide();
    $('input[type=submit]').each(function(){
        $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
    });
    $('button[type=submit]').button({
        icons: {
            primary: 'ui-icon-circle-check'
        }
    });
    $('button[id=addnewrow]').button({
        icons: {
            secondary: 'ui-icon-circle-plus'
        }
    });
    $('button[id=reset]').button({
        icons: {
            primary: 'ui-icon-refresh'
        }
    });
    $('button[id=print], button[id=copyresep]').button({
        icons: {
            primary: 'ui-icon-print'
        }
    })
    $('#print').click(function() {
        var id = $('#id_receipt').html();
        $.get('<?= base_url('pelayanan/kitir_cetak_nota') ?>/'+id, function(data) {
            $('#result_cetak').html(data);
            $('#result_cetak').dialog({
                autoOpen: true,
                modal: true,
                width: 700,
                height: 400
            })
        });
    })
    $('#form_resep').submit(function() {
        if ($('input[name=id_dokter]').val() == '') {
            alert('Nama dokter tidak boleh kosong !');
            $('#dokter').focus();
            return false;
        }
        if ($('input[name=id_pasien]').val() == '') {
            alert('Nama pasien tidak boleh kosong !');
            $('#pasien').focus();
            return false;
        }
        var jumlah = $('.tr_row').length-1;
        for (i = 0; i <= jumlah; i++) {
            if ($('#jr'+i).val() == '') {
                alert('Jumlah R tidak boleh kosong !');
                $('#jr'+i).focus();
                return false;
            }
            if ($('#jt'+i).val() == '') {
                alert('Jumlah tebus tidak boleh kosong !');
                $('#jt'+i).focus();
                return false;
            }
            if ($('#ap'+i).val() == '') {
                alert('Aturan pakai tidak boleh kosong !');
                $('#ap'+i).focus();
                return false;
            }
            if ($('#it'+i).val() == '') {
                alert('Iter tidak boleh kosong !');
                $('#it'+i).focus();
                return false;
            }
            if ($('#ja'+i).val() == '0-0') {
                alert('Jasa apoteker tidak boleh kosong !');
                $('#ja'+i).focus();
                return false;
            }
            //var jumlahsub = $('.tr_rows').length-1;
        }
        var post = $(this).attr('action');
        $.ajax({
            type: 'POST',
            url: post,
            dataType: 'json',
            data: $(this).serialize(),
            success: function(data) {
                if (data.status == true) {
                    $('input[type=text], select, input[type=radio], textarea').attr('disabled','disabled');
                    $('button[type=submit], #addnewrow').hide();
                    $('#id_receipt').html(data.id_resep);
                    $('.etiket,#copyresep, #print').show();
                    alert_tambah();
                } else {

                }
            }
        })
        return false;
    })
    $('#cetakcsr').click(function() {
        var awal = $('#awal').val();
        var akhir= $('#akhir').val();
        var hambatan = $('#hambatan').val();
        window.open('<?= base_url('cetak/transaksi/statistika-resep') ?>?awal='+awal+'&akhir='+akhir+'&hambatan='+hambatan,'mywindow','location=1,status=1,scrollbars=1,width=730px,height=500px');
    })
    $('#copyresep').click(function() {
        var id = $('#id_receipt').html()
        location.href='<?= base_url('laporan/salin_resep') ?>/'+id;
    })
    
    $('#csr').click(function() {
        $('.csr').fadeIn('fast',function() {
            
            $('#tanggals').datepicker({
                changeYear: true,
                changeMonth: true
            })
        });
        $('#hambatan').focus();
    })
    $('#closehambatan').click(function() {
        $('.csr').fadeOut('fast');
    })
    
    $('#dokter').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_dokter') ?>",
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
        $('input[name=id_dokter]').val(data.penduduk_id);
    });
    $('#pasien, #id_penduduk').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_pasien') ?>",
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
            var kelurahan = '-';
            if (data.kelurahan != null) { var kelurahan = data.kelurahan; }
            var str = '<div class=result>'+data.nama+'<br/>'+data.alamat+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $('#pasien').val(data.nama);
        $('input[name=id_pasien]').val(data.penduduk_id);
        $('#id_penduduk').val(data.no_rm);
    });
})
$(function() {
    
    i = 0;
    <?php if (!isset($id_resep)) { ?>
    for(x = 0; x <= i; x++) {
        addnoresep(x);
        add(x);
    }
    <?php } ?>
    $('#addnewrow').click(function() {
        row = $('.masterresep').length;
        //alert(row)
        addnoresep(row);
        i++;
    });
});

function eliminate(el) {
    ok=confirm('Anda yakin akan menghapus data ini ?');
    if (ok) {
        var parent = el.parentNode.parentNode;
        parent.parentNode.removeChild(parent);
        var jumlah = $('.tr_row').length-1;
        for (i = 0; i <= jumlah; i++) {
            
            $('.tr_row:eq('+i+')').children('.masterresep:eq(0)').children('.nr').attr('value',(i+1));
            $('.tr_row:eq('+i+')').children('.masterresep:eq(0)').children('.nr').attr('id','nr'+i);
            $('.tr_row:eq('+i+')').children('.psdg-right:eq(0)').children('.jr').attr('id','jr'+i);
            $('.tr_row:eq('+i+')').children('.psdg-right:eq(0)').children('.jt').attr('id','jt'+i);
            $('.tr_row:eq('+i+')').children('.psdg-right:eq(0)').children('.ap').attr('id','ap'+i);
            $('.tr_row:eq('+i+')').children('.psdg-right:eq(0)').children('.it').attr('id','it'+i);
            $('.tr_row:eq('+i+')').children('.psdg-right:eq(0)').children('.ja').attr('id','ja'+i);
            $('.tr_row:eq('+i+')').children('.psdg-right:eq(0)').children('.ad').attr('id','ad'+i);
            $('.tr_row:eq('+i+')').children('.psdg-right:eq(0)').children('.de').attr('id','de'+i);

                
        }
    } else {
        return false;
    }
    
}

function eliminatechild(el,x,y) {
    ok=confirm('Anda yakin akan menghapus data ini ?');
    if (ok) {

        var parent = el.parentNode.parentNode.parentNode.parentNode.parentNode;
        parent.parentNode.removeChild(parent);
        var jumlah = $('.tr_rows').length-1;
        //alert(jumlah)
        for (i = 0; i <= jumlah; i++) {
            /*$('.tr_rows:eq('+i+')').children('.psdg-right:eq(0)').children('.bc').attr('id','bc'+x+''+i);
            $('.tr_rows:eq('+i+')').children('.psdg-right:eq(0)').children('.pb').attr('id','pb'+i+''+x);
            $('.tr_rows:eq('+i+')').children('.psdg-right:eq(0)').children('.jt').attr('id','jt'+i);
            $('.tr_rows:eq('+i+')').children('.psdg-right:eq(0)').children('.ap').attr('id','ap'+i);
            $('.tr_rows:eq('+i+')').children('.psdg-right:eq(0)').children('.it').attr('id','it'+i);
            $('.tr_rows:eq('+i+')').children('.psdg-right:eq(0)').children('.ja').attr('id','ja'+i);
            $('.tr_rows:eq('+i+')').children('.psdg-right:eq(0)').children('.ad').attr('id','ad'+i);
            $('.tr_rows:eq('+i+')').children('.psdg-right:eq(0)').children('.de').attr('id','de'+i);*/
        }
    } else {
        return false;
    }
}

function cetak_etiket(i) {
    var no_resep = $('#id_receipt').html();
    var no_r = i;
    $.ajax({
        url: '<?= base_url('pelayanan/cetak_etiket') ?>',
        data: 'no_resep='+no_resep+'&no_r='+no_r,
        cache: false,
        success: function(data) {
            $('#data_cetak').html(data);
            $('#data_cetak').dialog({
                autoOpen: true,
                modal: true,
                width: 450,
                height: 400
            })
        }
    })
}

function addnoresep(i) {
    
    str = ' <div style="display: inline-block; width: 100%" class=tr_row>'+
                '<div class="masterresep" style="display: inline-block; width: 100%; border-bottom: 1px solid #f1f1f1; padding: 10px 0; ">'+
                    '<label>No. R/:</label><input style="border: none;" type=text name=nr[] id=nr'+i+' value='+(i+1)+' class=nr size=20 onkeyup=Angka(this) readonly maxlength=2 />'+
                    '<label>Jumlah Permintaan:</label><input type=text name=jr[] id=jr'+i+' class=jr size=20 onkeyup=Angka(this) />'+
                    '<label>Jumlah Tebus:</label><input type=text name=jt[] id=jt'+i+' class=jt onkeyup=Angka(this) size=20 />'+
                    '<label>Aturan Pakai:</label><input type=text name=ap[] id=ap'+i+' class=ap size=20 />'+
                    '<label>Iterasi:</label><input type=text name=it[] id=it'+i+' class=it size=10 value="0" onkeyup=Angka(this) />'+
                    '<label>Biaya Apoteker:</label><select onchange="subTotal()" name=ja[] id=ja'+i+'><option value="0-0">Pilih biaya ..</option><?php foreach ($biaya_apoteker as $value) { echo '<option value="'.$value->id.'-'.$value->nominal.'">'.$value->layanan.' '.$value->bobot.'</option>'; } ?></select>'+
                    '<label></label><input type=button value="Tambah Packing Barang" onclick=add('+i+') id="addition'+i+'" />'+
                    '<input type=button value="Hapus R/" id="deletion'+i+'" onclick=eliminate(this) /> <input type=button value="Etiket" id="etiket'+i+'" style="display: none" class="etiket" onclick=cetak_etiket('+(i+1)+') />'+
                '</div>'+
                '<div id=resepno'+i+' style="display: inline-block;width: 100%"></div>'+
            '</div>';
    
    $('#psdg-middle').append(str);
    
}

function add(i) {
    var j = $('.detailobat'+i).length;
    str = ' <div class=tr_rows style="width: 100%; display: block;">'+
                '<table align=right width=95% style="border-bottom: 1px solid #f1f1f1" class="detailobat'+i+'">'+
                '<tr><td width=15%>Barcode:</td><td> <input type=text value="<?= isset($val->barcode)?$val->barcode:NULL ?>" name=bc'+i+'[] id=bc'+i+''+j+' class=bc size=30 readonly /></td></tr>'+
                '<tr><td>Packing Barang</td><td>  <input type=text name=pb'+i+'[] id=pb'+i+''+j+' class=pb size=60 />'+
                    '<input type=hidden name=id_pb'+i+'[] id=id_pb'+i+''+j+' class=id_pb />'+
                    '<input type=hidden name=kr'+i+'[] id=kr'+i+''+j+' class=kr />'+
                    '<input type=hidden name=jp'+i+'[] id=jp'+i+''+j+' class=jp /></td></tr>'+
                '<tr><td>Kekuatan:</td><td><span class=label id=kekuatan'+i+''+j+'>-</span></td></tr>'+
                '<tr><td>Dosis Racik:</td><td> <input type=text name=dr'+i+'[] id=dr'+i+''+j+' class=dr onkeyup=jmlPakai('+i+','+j+') size=10 value="" /></td></tr>'+
                '<tr><td>Jumlah Pakai:</td><td><span class=label id=jmlpakai'+i+''+j+'>-</span></td></tr>'+
                '<tr><td></td><td><span class=label><input type=button value="Hapus" id="deleting'+i+''+j+'" onclick=eliminatechild(this,'+i+','+j+') /></span></td></tr></table>'+
            '</div>';

        
    $('#resepno'+i).append(str);
    $('.pb').watermark('Nama packing barang');
    $('#pb'+i+''+j).autocomplete("<?= base_url('inv_autocomplete/load_data_packing_barang') ?>",
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
        if (data.kekuatan == null) {
            alert('Kekuatan untuk packing barang yang dipilih tidak boleh null, silahkan ubah pada bagian master data obat');
            $(this).val('');
            $('#id_pb'+i+''+j).val('');
            $('#bc'+i+''+j).val('');
            $('#kekuatan'+i+''+j).html('');
            $('#dr'+i+''+j).val('');
            return false;
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
        $('#id_pb'+i+''+j).val(data.id);
        $('#bc'+i+''+j).val(data.barcode);
        $('#kekuatan'+i+''+j).html(data.kekuatan);
        $('#dr'+i+''+j).val(data.kekuatan);
        
        jmlPakai(i, j);
        
    });
}
function jmlPakai(i,j) {
        var dosis_racik = parseInt($('#dr'+i+''+j).val());
        var jumlah_tbs  = parseInt($('#jt'+i).val());
        var kekuatan    = parseInt($('#kekuatan'+i+''+j).html());
        var jumlah_pakai= (dosis_racik*jumlah_tbs)/kekuatan;
        if (isNaN(kekuatan) || kekuatan == 0) {
            alert('Kekuatan obat tidak boleh bernilai nol, silahkan diubah pada master data obat !');
            $('#pb'+i+''+j).val('');
            $('#id_pb'+i+''+j).val('');
            $('#bc'+i+''+j).val('');
            $('#kekuatan'+i+''+j).html('');
            $('#dr'+i+''+j).val('');
            return false;
        }
        $('#jmlpakai'+i+''+j).html(jumlah_pakai);
        $('#kr'+i+''+j).val(kekuatan);
        $('#jp'+i+''+j).val(jumlah_pakai);
}

function subTotal() {
    
    var jumlah = $('.tr_row').length-1;
    var total_jasa = 0;
    for(i = 0; i<= jumlah; i++) {
        var valjasa  = $('#ja'+i).val();
        var n=valjasa.split("-");
        var jasa = parseInt(n[1]);
        var total_jasa = total_jasa + jasa;
    }
    $('#totalbiaya').html(numberToCurrency(total_jasa));
}
</script>
<script type="text/javascript">
$(function() {
    $('#tanggal').datetimepicker();
    $('#reset').click(function() {
        $('#loaddata').html('');
        var url = '<?= base_url('pelayanan/resep') ?>';
        $('#loaddata').load(url);
    });
    var jml = $('.masterresep').length-1;
    for(i = 0; i <= jml; i++) {
        $('#cetak'+i).each(function(){
            $(this).replaceWith('<button class="'+$(this).attr('class')+'" title="'+$(this).attr('title')+'" type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'" onclick="'+$(this).attr('onclick')+'">' + $(this).val() + '</button>');
        });
        $('#cetak'+i).button({
            icons: {
                secondary: 'ui-icon-print'
            }
        });
    }
    $('#pmr_open').click(function() {
        var pasien = $('#id_pasien').val();
        var nama   = $('#pasien').val();
        if (pasien == '') {
            alert('Silahkan isikan data pasien terlebih dahulu!');
            $('#pasien').focus();
        } else {
            location.href='<?= base_url('cetak/transaksi/pmr') ?>?id_pasien='+pasien+'&nama='+nama;
        }
    })
    $('#id_penduduk').blur(function() {
        if ($('#id_penduduk').val() != '') {
            var id = $('#id_penduduk').val();
            $.ajax({
                url: '<?= base_url('inv_autocomplete/load_data_penduduk_pasien') ?>/'+id,
                dataType: 'json',
                success: function(val) {
                    if (val.id == null) {
                        $('#id_penduduk, #pasien, #id_pasien').val('');
                        $('#id_penduduk').focus();
                    } else {
                        $('#pasien').val(val.nama);
                        $('#id_pasien').val(val.id);
                    }

                }
            })
        }
    })
})
</script>
    <h1><?= $title ?></h1>
    <?= form_open('pelayanan/resep', 'id=form_resep') ?>
    
    <div class="data-input">
    <?php if (isset($list_data)) { foreach ($list_data as $key => $rows); } ?>
    <?= form_hidden('id_resep', isset($id_resep)?$id_resep:NULL) ?>
    <fieldset><legend>Summary</legend>
        <label>No.:</label><span class="label" id="id_receipt"><?= isset($id_resep)?$id_resep:get_last_id('resep', 'id') ?></span>
        <label>Waktu:</label><?= form_input('tanggal', isset($id_resep)?datetimefmysql($rows->waktu,'true'):date("d/m/Y H:i:s"), 'id=tanggal') ?>
        <label>Dokter *:</label><?= form_input('', isset($id_resep)?$rows->dokter:NULL, 'id=dokter size=40') ?> <?= form_hidden('id_dokter', isset($id_resep)?$rows->dokter_penduduk_id:NULL) ?>
        <label>Pasien: </label><?= form_input('', isset($id_resep)?$rows->pasien:NULL, 'id=pasien size=40') ?> <?= form_hidden('id_pasien', isset($id_resep)?$rows->pasien_penduduk_id:NULL) ?>
        <label></label><span class="label"><?= form_radio('absah', 'Sah', (isset($id_resep) and $rows->sah == 'Sah')?TRUE:FALSE) ?>  Sah </span> <span class="label"><?= form_radio('absah', 'Tidak Sah', (isset($id_resep) and $rows->sah == 'Tidak Sah')?TRUE:FALSE) ?>  Tidak Sah</span>
        <label>Keterangan:</label><?= form_textarea('ket', isset($id_resep)?$rows->keterangan:NULL) ?> </td> </tr>
        
    </fieldset>
    </div>
    <div class="data-list">
        <div id="psdgraphics-com-table">
            
            <div id="psdg-middle" class="data-input">
                <?php if (isset($id_resep)) { 
                    $noo = 1;
                    $nom = 0;
                    foreach ($list_data as $key => $data) { ?>
                    <div style="display: inline-block; width: 100%" class=tr_row>
                        <div class="masterresep" style="display: inline-block; width: 100%; border-bottom: 1px solid #f1f1f1; padding: 10px 0; ">
                                <label>No. R/: </label><input style="border: none;" type=text name=nr[] id=nr<?= $key ?> value='<?= $noo ?>' class=nr size=20 onkeyup=Angka(this) readonly maxlength=2 />
                                <label>Jumlah Permintaan:</label><input type=text name=jr[] value="<?= $data->resep_r_jumlah ?>" id=jr<?= $key ?> class=jr size=20 onkeyup=Angka(this) />
                                <label>Jumlah Tebus:</label><input type=text name=jt[] value="<?= $data->tebus_r_jumlah ?>" id=jt<?= $key ?> class=jt onkeyup=Angka(this) size=20 />
                                <label>Aturan Pakai:</label><input type=text name=ap[] value="<?= $data->pakai_aturan ?>" id=ap<?= $key ?> class=ap size=20 />
                                <label>Iterasi:</label><input type=text name=it[] value="<?= $data->iter ?>" id=it<?= $key ?> class=it size=10 value="0" onkeyup=Angka(this) />
                                <label>Biaya Apoteker</label><select onchange="subTotal()" name=ja[] id=ja<?= $key ?>><option value="0-0">Pilih biaya ..</option><?php foreach ($biaya_apoteker as $value) { echo '<option '; if ($value->id == $data->tarif_id) echo 'selected'; echo ' value="'.$value->id.'-'.$value->nominal.'">'.$value->layanan.' '.$value->bobot.' '.$value->kelas.'</option>'; } ?></select>
                                <label></label><input type=button value="Tambah Packing Barang" onclick=add(<?= $key ?>) id="addition<?= $key ?>" />
                                <input type=button value="Hapus R/" id="deletion<?= $key ?>" onclick=eliminate(this) /> <input type=button value="Etiket" id="etiket<?= $noo ?>" style="display: none" class="etiket" onclick="cetak_etiket(<?= $noo ?>)" />
                        </div>
                        <div id=resepno<?= $key ?> style="display: inline-block;width: 100%"></div>
                    </div>
                <?php 
                    $detail = $this->m_resep->detail_data_resep_muat_data($data->id_rr)->result();
                    foreach($detail as $no => $val) { ?>
                         <div class=tr_rows style="width: 100%; display: block;">
                                <table align=right width=95% style="border-bottom: 1px solid #f4f4f4" class="detailobat<?= $key ?>">
                                <tr><td width=15%>Barcode:</td><td> <input type=text value="<?= isset($val->barcode)?$val->barcode:NULL ?>" name=bc<?= $key ?>[] id=bc<?= $key ?><?= $no ?> class=bc size=30 readonly /></td></tr>
                                <tr><td>Packing Barang:</td><td>  <input type=text name=pb<?= $key ?>[] value="<?= $val->barang ?> <?= $val->barang ?> <?= ($val->kekuatan == '1')?'':$val->kekuatan ?>  <?= $val->satuan ?> <?= $val->sediaan ?> <?= $val->pabrik ?> <?= ($val->isi==1)?'':'@'.$val->isi ?> <?= $val->satuan_terkecil ?>" id=pb<?= $key ?><?= $no ?> class=pb size=60 />
                                        <input type=hidden name=id_pb<?= $key ?>[] value="<?= $val->id_packing ?>" id=id_pb<?= $key ?><?= $no ?> class=id_pb />
                                        <input type=hidden name=kr<?= $key ?>[] value="<?= $val->kekuatan ?>" id=kr<?= $key ?><?= $no ?> class=kr />
                                        <input type=hidden name=jp<?= $key ?>[] value="<?= $val->pakai_jumlah ?>" id=jp<?= $key ?><?= $no ?> class=jp /></td></tr>
                                <tr><td>Kekuatan:</td><td><span class=label id=kekuatan<?= $key ?><?= $no ?>><?= $val->kekuatan ?></span></td></tr>
                                <tr><td>Dosis Racik:</td><td> <input type=text name=dr<?= $key ?>[] value="<?= $val->dosis_racik ?>" id=dr<?= $key ?><?= $no ?> class=dr onkeyup=jmlPakai(<?= $key ?>,<?= $no ?>) size=10 value="" /></td></tr>
                                <tr><td>Jumlah Pakai:</td><td><span class=label id=jmlpakai<?= $key ?><?= $no ?>><?= $val->pakai_jumlah ?></span></td></tr>
                                <tr><td></td><td><span class=label><input type=button value="Hapus" id="deleting<?= $key ?><?= $no ?>" onclick=eliminatechild(this,<?= $key ?>,<?= $no ?>) /></span></td></tr></table>
                        </div>
                        <script>
                            $('#pb<?= $key ?><?= $no ?>').autocomplete("<?= base_url('inv_autocomplete/load_data_packing_barang') ?>",
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
                                    if (data.kekuatan == null) {
                                        alert('Kekuatan untuk packing barang yang dipilih tidak boleh null, silahkan ubah pada bagian master data obat');
                                        $(this).val('');
                                        $('#id_pb<?= $key ?><?= $no ?>').val('');
                                        $('#bc<?= $key ?><?= $no ?>').val('');
                                        $('#kekuatan<?= $key ?><?= $no ?>').html('');
                                        $('#dr<?= $key ?><?= $no ?>').val('');
                                        return false;
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
                                    $('#id_pb<?= $key ?><?= $no ?>').val(data.id);
                                    $('#bc<?= $key ?><?= $no ?>').val(data.barcode);
                                    $('#kekuatan<?= $key ?><?= $no ?>').html(data.kekuatan);
                                    $('#dr<?= $key ?><?= $no ?>').val(data.kekuatan);

                                    jmlPakai(<?= $key ?>, <?= $no ?>);

                                });
                        </script>
                    <?php }
                    $noo++;
                    $nom = $nom + $data->nominal;
                    }
                } ?>
            </div>
            <?= form_button(null,'Tambah R', 'id=addnewrow') ?>
<!--            <table width="100%">
                <tr><td>
                * Total Biaya Apoteker: <b id="totalbiaya"><?= isset($id_resep)?rupiah($nom):NULL ?></b>
                    </td></tr>
            </table>-->
            </div>
    </div>
        <?= form_submit('save', 'Simpan', 'id=submit') ?>
        <?= form_button('Reset', 'Reset', 'id=reset') ?>
        <?= form_button('copyresep', 'Cetak', 'id=copyresep') ?>
        <!--<?= form_button(null, 'Cetak Kitir', 'id=print') ?>-->
    <?= form_close() ?>
</div>