<?php $this->load->view('message'); ?>
<script type="text/javascript">
function loading() {
    var url = '<?= base_url('inventory/penjualan') ?>';
    $('#loaddata').load(url);
}

function form_open() {
    $('#form_pembayaran').dialog({
        autoOpen: true,
        modal: true,
        width: 550,
        height: 250,
        buttons: {
            "Simpan Pembayaran": function() {
                $('input[name=bulat]').val($('#bulat').val());
                $('input[name=bayar]').val($('#bayar').val());
                $('#form_penjualan').submit();
                $(this).dialog("close");
                $('#noresep').focus();
            }
        }
    });
}
$(function() {
    $('#print').hide();
    $('input,select').live('keydown', function(e) {
        if (e.keyCode === 120) {
            form_open();
            $('#bayar').focus();
        }
    });
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
    $('button[id=print]').button({
        icons: {
            primary: 'ui-icon-print'
        }
    });
    $('#noresep').focus();
    $('#bayar').keyup(function() {
        FormNum(this);
        setKembali();
        
    })
    $('#deletion').hide();
    $('#cetak').click(function() {
        var id = $('#id_resep').val();
        if (id == '') {
            var id = '<?= isset($_GET['id'])?$_GET['id']:NULL ?>';
            window.open('<?= base_url('cetak/inventory/kitir') ?>?penjualan=bebas&id='+id,'mywindow','location=1,status=1,scrollbars=1,width=820px,height=570px');
        } else {
            var id = '<?= isset($_GET['id'])?$_GET['id']:NULL ?>';
            window.open('<?= base_url('cetak/inventory/kitir') ?>?id='+id,'mywindow','location=1,status=1,scrollbars=1,width=820px,height=500px');
        }
    })
    $('#deletion').click(function() {
        var ok=confirm('Anda yakin akan menghapus transaksi ini ?');
        if (ok) {
            var id = $('#id_penjualan').html();
            $.get('<?= base_url('inventory/penjualan_delete') ?>/'+id, function(data) {
                if (data.status == true) {
                    $('#deletion').show();
                    alert_delete();
                    loading();
                }
            },'json');
        } else {
            return false;
        }
    })
    $('#noresep').autocomplete("<?= base_url('inv_autocomplete/load_data_no_resep') ?>",
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
                var str = '<div class=result>'+data.id+' - '+data.pasien+'<br/>Dokter: '+data.dokter+'</div>';
            }
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.id);
        $('#display-apt').show();
        $('input[name=id_resep]').val(data.id);
        $('#pasien').html(data.pasien);
        $('input[name=id_pasien]').val(data.no_rm);
        //$('#jasa-apt').html(numberToCurrency(data.jasa_apoteker));
        var id_resep = data.id;
        $.ajax({
            url: '<?= base_url('inv_autocomplete/load_jasa_apoteker') ?>/'+id_resep,
            cache: false,
            dataType: 'json',
            success: function(data) {
                $('#jasa-apt').html(numberToCurrency(data.jasa_apoteker));
            }
        })
        var id = data.id;
        $.ajax({
            url: '<?= base_url('inv_autocomplete/load_penjualan_by_no_resep') ?>/'+id,
            cache: false,
            success: function(msg) {
                $('.form-inputan tbody').html(msg);
            }
        });
//        var id = data.pasien_penduduk_id;
//        $.ajax({
//            url: '<?= base_url('inv_autocomplete/load_data_penduduk_asuransi') ?>',
//            data: 'id_pembeli='+id,
//            cache: false,
//            success: function(msg) {
//                $('#asuransi').html((msg != 'null')?msg:'-');
//            }
//        });
        $('#ppn').focus();
    });
});
$(function() {
    <?php if (!isset($list_data)) { ?>
    for(x = 0; x <= 1; x++) {
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
        $('.tr_row:eq('+i+')').children('td:eq(2)').attr('id','hj'+i);
        $('.tr_row:eq('+i+')').children('td:eq(3)').attr('id','diskon'+i);
        $('.tr_row:eq('+i+')').children('td:eq(4)').children('.jl').attr('id','jl'+i);
    }
    subTotal();
}
function add(i) {
     str = '<tr class=tr_row>'+
        '<td><input type=text name=nr[] id=bc'+i+' class=bc size=20 /></td>'+
        '<td><input type=text name=dr[] id=pb'+i+' class=pb size=60 /><input type=hidden name=id_pb[] id=id_pb'+i+' class=id_pb /></td>'+
        '<td id=ed'+i+' align=center></td>'+
        '<td id=hj'+i+' align=right></td>'+
        '<td align=center id=diskon'+i+'></td>'+
        '<td id=sisa'+i+' align=center></td>'+
        '<td><input type=text name=jl[] id=jl'+i+' class=jl size=20 style="width: 100%;" onKeyup=subTotal() /><input type=hidden name=subtotal[] id=subttl'+i+' class=subttl /></td>'+
        '<td id=subtotal'+i+' align="right"></td>'+
        '<td class=aksi><a class=delete onclick=eliminate(this)></a><input type=hidden name=disc[] id=disc'+i+' /><input type=hidden name=harga_jual[] id=harga_jual'+i+' /></td>'+
    '</tr>';

    $('.form-inputan tbody').append(str);
    $('#bc'+i).live('keydown', function(e) {
        if (e.keyCode==13) {
            var bc = $('#bc'+i).val();
            $.ajax({
                url: '<?= base_url('inventory/fillField') ?>',
                data: 'do=getPenjualanField&barcode='+bc,
                cache: false,
                dataType: 'json',
                success: function(data) {
                    if (data.nama != null) {
                        var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = '';
                        if (data.isi != '1') { var isi = '@ '+data.isi; }
                        if (data.kekuatan != null && data.kekuatan != '0') { var kekuatan = data.kekuatan; }
                        if (data.satuan != null) { var satuan = data.satuan; }
                        if (data.sediaan != null) { var sediaan = data.sediaan; }
                        if (data.pabrik != null) { var pabrik = data.pabrik; }
                        if (data.satuan_terkecil != null) { var satuan_terkecil = data.satuan_terkecil; }
                        if (data.id_obat == null) {
                            $('#pb'+i).val(data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil);
                        } else {
                            if (data.generik == 'Non Generik') {
                                $('#pb'+i).val(data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil);
                            } else {
                                $('#pb'+i).val(data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil);
                            }
                        }
                        $('#id_pb'+i).val(data.id);
                        $('#kekuatan'+i).html(data.kekuatan);
                        $('#hj'+i).html(numberToCurrency(Math.ceil(data.harga))); // text asli
                        $('#harga_jual'+i).val(data.harga);
                        $('#disc'+i).val(data.diskon);
                        $('#diskon'+i).html(data.diskon);
                        $('#jl'+i).val('1');
                        subTotal(i);
                        var jml = $('.tr_row').length;
                        //alert(jml+' - '+i)
                        if (jml - i == 1) {
                            add(jml);
                        }
                        $('#bc'+(i+1)).focus();
                    } else {
                        alert('Barang yang diinputkan tidak ada !');
                        $('#bc'+i).val('');
                        $('#pb'+i).val('');
                        $('#id_pb'+i).val('');
                        $('#kekuatan'+i).html('');
                        $('#hj'+i).html(''); // text asli
                        $('#harga_jual'+i).val('');
                        $('#disc'+i).val('');
                        $('#diskon'+i).html('');
                    }
                }
            })
            return false;
        }
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
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
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
        $('#bc'+i).val(data.barcode);
        $('#kekuatan'+i).html(data.kekuatan);
        var id_packing = data.id;
        $.ajax({
            url: '<?= base_url('inv_autocomplete/get_harga_jual') ?>',
            data: 'id='+id_packing,
            cache: false,
            dataType: 'json',
            success: function(msg) {
                $('#hj'+i).html(numberToCurrency(Math.ceil(msg.harga))); // text asli
                $('#harga_jual'+i).val(msg.harga);
                $('#disc'+i).val(msg.diskon);
                $('#diskon'+i).html(msg.diskon);
                //subTotal(i);
            }
        });
    });
}

function subTotal() {
        var jumlah = $('.tr_row').length-1;
        var tagihan = 0;
        var disc = 0;
        var ppn = $('#ppn').val()/100;
        var jasa_apt = currencyToNumber($('#jasa-apt').html());
        if (isNaN(jasa_apt)) { jasa_apt = 0; } else { jasa_apt = jasa_apt; }
        for (i = 0; i <= jumlah; i++) {
            var harga = currencyToNumber($('#hj'+i).html());
            var diskon= parseInt($('#diskon'+i).html())/100;
            <?php 
            if (isset($_GET['id'])) { ?>
            var jml= parseInt($('#jl'+i).html());                
            <?php } else { ?>
            var jml= parseInt($('#jl'+i).val());
            <?php } ?>
            var subtotal = numberToCurrency(Math.ceil((harga - (harga*diskon))*jml));
            //alert(subtotal);
            $('#subtotal'+i).html(subtotal);
            $('#subttl'+i).val(subtotal);
            //alert(harga)
            //alert(disc)
            
            var harga = parseInt(currencyToNumber($('#hj'+i).html()));
            var diskon= parseInt($('#diskon'+i).html())/100;
            //var jumlah= parseInt($('#jl'+i).val());
            var subtotall = 0;
            //alert(harga); alert(diskon); alert(jumlah);
            if (!isNaN(harga) && !isNaN(diskon) && !isNaN(jml)) {
                if (parseInt($('#subttl'+i).val()) !== '') {
                    //var subtotall = parseInt($('#subttl'+i).val());
                    var subtotall = harga*jml;
                }
                var disc = disc + ((diskon*harga)*jml);
                var tagihan = tagihan + subtotall;
            }
            
        }
        //$('#jasa_total_apotek').val(ja);
        
        $('#total-diskon').html(numberToCurrency(Math.ceil(disc)));
        //$('#total-tagihan').html(numberToCurrency(tagihan));
        //alert(jasa_apt)
        var totalllica = Math.ceil((tagihan - disc)+jasa_apt);
        var total = totalllica+(tagihan*ppn);
        $('#ppn-hasil').html(numberToCurrency(Math.ceil(tagihan*ppn)));
        $('#total').html(numberToCurrency(Math.ceil(total)));
        $('input[name=total]').val(Math.ceil(tagihan - disc)+Math.ceil(tagihan*ppn));
        
        $('#total_tagihan_penjualan').html($('#total').html());
        var nominal = currencyToNumber($('#total').html());
        $('#bulat').val(numberToCurrency(pembulatan_seratus(nominal)));
}
function setKembali() {
        //var apoteker = currencyToNumber($('#jasa-apt').html());
        var bayar = currencyToNumber($('#bayar').val());
        var bulat = currencyToNumber($('#bulat').val());
        var kembali = bayar - bulat;
        if (isNaN(bayar)) {var kembali = 0;} else {var kembali = kembali;}
        if (kembali < 0) {
            var kembali = kembali;
        } else {
            var kembali = numberToCurrency(Math.ceil(kembali));
        }
        //$('#bulat').val(numberToCurrency(total));
        $('#kembalian').html(kembali);
        //$('#total_tagihan').val(total);
}
function pembulatan_seratus(angka) {
    var kelipatan = 100;
    var sisa = angka % kelipatan;
    if (sisa != 0) {
        var kekurangan = kelipatan - sisa;
        var hasilBulat = angka + kekurangan;
        return Math.ceil(hasilBulat);
    } else {
        return Math.ceil(angka);
    }
    
    
}
$(function() {
    $('#tanggal').datetimepicker();
    $('#reset').click(function() {
        $('#form_pembayaran').dialog().remove();
        $('#loaddata').empty();
        var url = '<?= base_url('inventory/penjualan') ?>';
        $('#loaddata').load(url);
    });
    $('#print').click(function() {
        var id = $('#noresep').val();
        var id_penjualan = $('#id_penjualan').html();
        $.get('<?= base_url('pelayanan/kitir_cetak_nota') ?>/'+id+'/'+id_penjualan, function(data) {
            $('#result_cetak').html(data);
            $('#result_cetak').dialog({
                autoOpen: true,
                modal: true,
                width: 350,
                height: 400
            });
        });
    });
    $("#form_penjualan").submit(function() {
        if ($('#id_pembeli').val() === '') {
            alert('Nama pembeli tidak boleh kosong !');
            $('#pasien').focus();
            return false;
        }
        if ($('#bayar').val() === '') {
            alert('Jumlah bayar tidak boleh kosong !');
            $('#bayar').focus();
            return false;
        }
        if ($('#bulat').val() === '') {
            alert('Jumlah pembulatan tidak boleh kosong !');
            $('#bulat').focus();
            return false;
        }
        var jumlah = $('.tr_row').length;
        for(i = 1; i <= jumlah; i++) {
            if ($('#jl'+i).val() === '') {
                if ($('#id_pb'+i).val() !== '') {
                    alert('Jumlah tidak boleh kosong !');
                    $('#jl'+i).focus();
                    return false;
                }
            }
        }
        var post = $(this).attr('action');
        $.ajax({
            type: 'POST',
            url: post,
            data: $(this).serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.status === true) {
                    $('#deletion,#print').show();
                    $('#print').show();
                    $('#id_penjualan').html(data.id_penjualan);
                    $('input[name=bulat]').val('');
                    $('input[name=bayar]').val('');
                    alert_tambah();
                }
            }
        });
        return false;
        
    });
    $('#id_penduduk').blur(function() {
        if ($('#id_penduduk').val() !== '') {
            var id = $('#id_penduduk').val();
            $.ajax({
                url: '<?= base_url('inventory/fillField') ?>',
                data: 'id_pasien=true&id='+id,
                dataType: 'json',
                success: function(val) {
                    if (val.id === null) {
                        alert('Data pasien tidak ditemukan !');
                        $('#id_penduduk, #pembeli, #id_pembeli').val('');
                        $('#id_penduduk').focus();
                    } else {
                        $('#pembeli').val(val.nama);
                        $('#id_pembeli').val(val.id);
                    }
                }
            });
        }
    });
});
</script>
<title><?= $title ?></title>
<div id="result_cetak" style="display: none;"></div>
<div class="kegiatan">
    <h1><?= $title ?></h1>
    <?= form_open('inventory/penjualan', 'id=form_penjualan') ?>
    <div class="data-input" id="form_pembayaran" title="Pembayaran Penjualan Resep" style="display: none;">
        <label style="font-size: 18px;">Total (Rp.):</label><b><span style="font-size: 18px;" id="total_tagihan_penjualan" class="label"></span></b>
        <label style="font-size: 18px;">Pembulatan(Rp.):</label><?= form_input('', null, 'id=bulat style="font-size: 18px;" onkeyup=FormNum(this) ') ?>
        <label style="font-size: 18px;">Bayar (Rp):</label><?= form_input('', null, 'id=bayar  style="font-size: 18px;"') ?>
        <label style="font-size: 18px;">Kembalian (Rp):</label><span id="kembalian" class="label" style="font-size: 18px;"></span>
    </div>
    <?php if (isset($list_data)) { 
        foreach ($atribute as $rows);
    } ?>
    <div class="data-input">
    <?= form_hidden('bulat') ?>
    <?= form_hidden('bayar') ?>
    <?= form_hidden('id_pasien', null, 'id=id_pasien') ?>
    <fieldset><legend>Summary</legend>
        <?= form_hidden('total', null, 'id=total_tagihan') ?>
        <?= form_hidden('jasa_apotek') ?>
        <div class="left_side" style="min-height: 160px;">
            <label>No.</label><span class="label" id="id_penjualan"><?= get_last_id('penjualan', 'id') ?></span>
            <label>Waktu</label><?= form_input('tanggal', date("d/m/Y H:i"), 'id=tanggal') ?>
            <label>No. Resep </label>
                    <?= form_input('', isset($rows->resep_id)?$rows->resep_id:NULL, 'id=noresep size=30') ?> 
                    <?= form_hidden('id_resep', isset($rows->resep_id)?$rows->resep_id:NULL) ?>
            <label>Pasien</label><span id="pasien" class="label"><?= isset($rows->pasien)?$rows->pasien:NULL ?></span>
<!--            <label>Produk Asuransi</label><span id="asuransi" class="label"></span>-->
            <label>PPN (%) </label><?= form_input('ppn', '0', 'id=ppn size=10 onkeyup=subTotal()') ?>
            <label></label><?= isset($_GET['msg'])?'':form_button(null, 'Tambah Baris', 'id=addnewrow') ?>
        </div>
        <div class="right_side" style="min-height: 160px;">
            <!--Jasa Apoteker</td><td id="jasa-apt"><?= isset($biaya['nominal'])?rupiah($biaya['nominal']):null ?>-->
            <label>Biaya Apoteker</label><span id="jasa-apt" class="label"></span>
            <label>Total Tagihan</label><span id="total-tagihan" class="label"></span>
            <label>PPN</label><span id="ppn-hasil" class="label"></span>
            <label>Total Diskon</label><span id="total-diskon" class="label"></span>
            <label>Total</label><span id="total" class="label"></span>
        </div>
        </fieldset>
    </div>
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="10%">Barcode</th>
                <th width="45%">Kemasan Barang</th>
                <th width="15%">ED</th>
                <th width="10%">Harga Jual</th>
                <th width="7%">Diskon</th>
                <th width="7%">Sisa Stok</th>
                <th width="5%">Jumlah</th>
                <th width="10%">SubTotal</th>
                <th>#</th>
            </tr>
            </thead>
            <tbody>
                <?php if (isset($list_data)) {
                    $no = 0;
                    $total = 0; $disc = 0;
                    $biaya_apoteker = 0;
                    foreach ($list_data as $key => $data) {
                        $harga_jual = $data->hna+($data->hna*$data->margin/100) - ($data->hna*($data->diskon/100));
                        $subtotal = ($harga_jual - (($harga_jual*($data->percent/100))))*$data->pakai_jumlah;
                        $total = $total + $subtotal;
                        $disc = $disc + (($data->percent/100)*$harga_jual);
                        $biaya_apoteker = $biaya_apoteker + $data->profesi_layanan_tindakan_jasa_total;
                        $alert=NULL;
                        if ($data->sisa <= 0) {
                            $alert = "style=background:red";
                        }
                        ?>
                        <tr <?= $alert ?> class="tr_row <?= ($key%2==0)?'odd':'even' ?>">
                            <td><input type=text name=nr[] id=bc<?= $no ?> class=bc size=20 value="<?= $data->barcode ?>" /></td>
                            <td><input type=text name=dr[] id=pb<?= $no ?> class=pb size=60 value="<?= $data->barang ?> <?= ($data->kekuatan == '1')?'':$data->kekuatan ?>  <?= $data->satuan ?> <?= $data->sediaan ?> <?= ($data->generik == '1')?'':$data->pabrik ?> <?= ($data->isi==1)?'':'@'.$data->isi ?> <?= $data->satuan_terkecil ?>" />
                                <input type=hidden name=id_pb[] id=id_pb<?= $no ?> class=id_pb value="<?= $data->barang_packing_id ?>" /></td>
                                <input type="hidden" name="ed[]" value="<?= $data->ed ?>" id="exp<?= $key ?>" />
                            <td align="center" id=ed<?= $no ?>><?= datefmysql($data->ed) ?></td>
                            <td align="right" id=hj<?= $no ?>><?= rupiah($harga_jual) ?></td>
                            <td align="center" id=diskon<?= $no ?>><?= $data->percent ?></td>
                            <td align="center" id=sisa<?= $no ?>><?= $data->sisa ?></td>
                            <td><input type=text name=jl[] id=jl<?= $no ?> class=jl size=10 value="<?= $data->pakai_jumlah ?>" onkeyup="subTotal(<?= $no ?>)" />
                            <input type=hidden name=subtotal[] id="subttl<?= $no ?>" class=subttl /></td>

                            <td id=subtotal<?= $no ?> align="right"><?= rupiah($subtotal) ?></td>
                            <td class=aksi><a class=delete onclick="eliminate(this)"></a> 
                                <input type=hidden name="disc[]" id="disc<?= $no ?>" value="<?= $data->percent ?>" />
                                <input type=hidden name="harga_jual[]" id="harga_jual<?= $no ?>" value="<?= $harga_jual ?>" /></td>
                        </tr>
                        <script type="text/javascript">
                            $(function() {
                                <?php if ($data->sisa <= 0) { ?>
                                    alert('Stok barang untuk <?= $data->barang ?> <?= ($data->kekuatan == '1')?'':$data->kekuatan ?>  <?= $data->satuan ?> <?= $data->sediaan ?> <?= ($data->generik == '1')?'':$data->pabrik ?> <?= ($data->isi==1)?'':'@'.$data->isi ?> <?= $data->satuan_terkecil ?> = 0 !');
                                <?php } ?>
                                $('#bc<?= $no ?>').live('keydown', function(e) {
                                    if (e.keyCode==13) {
                                        var bc = $('#bc<?= $no ?>').val();
                                        $.ajax({
                                            url: '<?= base_url('inventory/fillField') ?>',
                                            data: 'do=getPenjualanField&barcode='+bc,
                                            cache: false,
                                            dataType: 'json',
                                            success: function(data) {
                                                if (data.nama !== null) {
                                                    var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = '';
                                                    if (data.isi !== '1') { var isi = '@ '+data.isi; }
                                                    if (data.kekuatan !== null && data.kekuatan !== '0') { var kekuatan = data.kekuatan; }
                                                    if (data.satuan !== null) { var satuan = data.satuan; }
                                                    if (data.sediaan !== null) { var sediaan = data.sediaan; }
                                                    if (data.pabrik !== null) { var pabrik = data.pabrik; }
                                                    if (data.satuan_terkecil !== null) { var satuan_terkecil = data.satuan_terkecil; }
                                                    if (data.id_obat === null) {
                                                        $('#pb<?= $no ?>').val(data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil);
                                                    } else {
                                                        if (data.generik === 'Non Generik') {
                                                            $('#pb<?= $no ?>').val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil);
                                                        } else {
                                                            $('#pb<?= $no ?>').val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil);
                                                        }

                                                    }
                                                    $('#id_pb<?= $no ?>').val(data.id);
                                                    $('#kekuatan<?= $no ?>').html(data.kekuatan);
                                                    $('#hj<?= $no ?>').html(numberToCurrency(Math.ceil(data.harga))); // text asli
                                                    $('#harga_jual<?= $no ?>').val(data.harga);
                                                    $('#disc<?= $no ?>').val(data.diskon);
                                                    $('#diskon<?= $no ?>').html(data.diskon);
                                                    subTotal(i);
                                                    var jml = $('.tr_row').length;
                                                    //alert(jml+' - '+i)
                                                    if (jml - i === 1) {
                                                        add(jml);
                                                    }
                                                    $('#jl<?= $no ?>').focus();
                                                } else {
                                                    alert('Barang yang diinputkan tidak ada !');
                                                    $('#bc<?= $no ?>').val('');
                                                    $('#pb<?= $no ?>').val('');
                                                    $('#id_pb<?= $no ?>').val('');
                                                    $('#kekuatan<?= $no ?>').html('');
                                                    $('#hj<?= $no ?>').html(''); // text asli
                                                    $('#harga_jual<?= $no ?>').val('');
                                                    $('#disc<?= $no ?>').val('');
                                                    $('#diskon<?= $no ?>').html('');
                                                }
                                            }
                                        })
                                        return false;
                                    }
                                });
                                $('#pb<?= $no ?>').autocomplete("<?= base_url('inv_autocomplete/load_data_packing_barang') ?>",
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
                                        var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = '';
                                        if (data.isi != '1') { var isi = '@ '+data.isi; }
                                        if (data.satuan != null) { var satuan = data.satuan; }
                                        if (data.kekuatan != null && data.kekuatan != '0') { var kekuatan = data.kekuatan; }
                                        if (data.sediaan != null) { var sediaan = data.sediaan; }
                                        if (data.pabrik != null) { var pabrik = data.pabrik; }
                                        if (data.satuan_terkecil != null) { var satuan_terkecil = data.satuan_terkecil; }
                                        if (data.id_obat == null) {
                                            var str = '<div class=result>'+data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
                                        } else {
                                            if (data.generik == 'Non Generik') {
                                                var str = '<div class=result>'+data.nama+' '+kekuatan+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil+'</div>';
                                            } else {
                                                var str = '<div class=result>'+data.nama+' '+kekuatan+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
                                            }

                                        }
                                        return str;
                                    },
                                    width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                                    dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
                                }).result(
                                function(event,data,formated){
                                    var sisa = data.sisa;
                                    if (data.isi != '1') { var isi = '@ '+data.isi; }
                                    if (data.satuan != null) { var satuan = data.satuan; }
                                    if (data.kekuatan != null && data.kekuatan != '0') { var kekuatan = data.kekuatan; }
                                    if (data.sediaan != null) { var sediaan = data.sediaan; }
                                    if (data.pabrik != null) { var pabrik = data.pabrik; }
                                    if (data.satuan_terkecil != null) { var satuan_terkecil = data.satuan_terkecil; }
                                    if (data.id_obat == null) {
                                        $(this).val(data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil);
                                    } else {
                                        if (data.generik == 'Non Generik') {
                                            $(this).val(data.nama+' '+kekuatan+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil);
                                        } else {
                                            $(this).val(data.nama+' '+kekuatan+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil);
                                        }

                                    }
                                    $('#id_pb<?= $no ?>').val(data.id);
                                    $('#bc<?= $no ?>').val(data.barcode);
                                });
                            })
                        </script>
                    <?php 

                    $no++;
                }
                } ?>
            </tbody>
        </table> 
    </div>
    <!--<?= form_submit('save', 'Simpan', 'id=save') ?>-->
    <?= form_button(null, 'Delete', 'id=deletion') ?>
    <?= form_button('Reset', 'Reset', 'id=reset') ?>
    <?= form_button(null, 'Cetak Nota', 'id=print') ?>
    <?= form_close() ?>
</div>
<?php
if (isset($list_data)) { ?>
    <script>
        $(function() {
            $('#jasa-apt').html(numberToCurrency(<?= $biaya_apoteker ?>));
            $('#total-tagihan').html(numberToCurrency(<?= $total ?>));
            $('#total-diskon').html(<?= ceil($disc) ?>);
            $('#total').html(numberToCurrency(<?= ($total-ceil($disc)) ?>));
            var jumlah = $('.tr_row').length;
            for (i = 1; i <= jumlah; i++) {
                subTotal(i);
            }
        });
    </script>
<?php }
?>