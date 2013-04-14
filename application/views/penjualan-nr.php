<?php
header('Cache-Control: max-age=0');
?>
<title><?= $title ?></title>
<div id="result_cetak" style="display: none"></div>
<div class="kegiatan">
<?php $this->load->view('message'); ?>
<script type="text/javascript">
function create_dialog() {
    var str = '<div id=open_bayar class=data-input>'+
        '<label style="font-size: 18px">Total:</label><span style="font-size: 18px" class=label id=totalopen>'+$('#bulat').val()+'</span>'+
        '<label style="font-size: 18px">Bayar (Rp):</label><input style="font-size: 18px" type=text name=bayar id=bayar size=20 />'+
        '<label style="font-size: 18px">Kembalian (Rp):</label><span style="font-size: 18px" id="kembalian_nr" class="label"></span></div>';
    $('#loaddata').append(str);
    $('#open_bayar').dialog({
        autoOpen: true,
        modal: true,
        width: 350,
        height: 300,
        resizable: true,
        close: function() {
            $(this).dialog().remove();
            $('#bulat').focus();
        }, buttons: {
            "OK": function() {
                $('#')
            }
        }
    });
    $('#bayar').keyup(function() {
        FormNum(this);
        setKembali();  
    })
}
function form_open() {
    $('#form_pembayaran_nr').dialog({
        autoOpen: true,
        modal: true,
        width: 350,
        height: 300,
        buttons: {
            "Ok": function() {
                $('input[name=bulat]').val($('#bulat').val());
                $('input[name=bayar]').val($('#bayar').val());
                $(this).dialog("close");
                $('#noresep').focus();
            }
        }
    });
}
$(function() {
    $('input,select').live('keydown', function(e) {
        if (e.keyCode === 120) {
            create_dialog();
            $('#bayar').focus();
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
    $('#noresep').focus();
    $('#print').hide();
    $('#print').click(function() {
        var id = $('#id_penjualan').html();
        $.get('<?= base_url('pelayanan/penjualan_cetak_nota') ?>/'+id+'/nonresep', function(data) {
            $('#result_cetak').html(data);
            $('#result_cetak').dialog({
                autoOpen: true,
                modal: true,
                width: 350,
                height: 400
            });
        });
    });
    
    $('#pembayaran').change(function() {
        var id = $('#pembayaran').val();
        $.ajax({
            url: '<?= base_url('inv_autocomplete/get_diskon_instansi_relasi') ?>/'+id,
            cache: false,
            dataType: 'json',
            success: function(msg) {
                //alert(msg.diskon_penjualan)
                $('#disc_bank').html((msg.diskon_penjualan === null)?'0':msg.diskon_penjualan);
                $('#diskon_bank').val((msg.diskon_penjualan === null)?'0':msg.diskon_penjualan);
                subTotal();
            }
        });
    });
    $('#pembeli').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_pasien') ?>",
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
        $('#pembeli').val(data.nama);
        $('input[name=id_pembeli]').val(data.penduduk_id);
    });
});
$(function() {
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
        $('.tr_row:eq('+i+')').children('td:eq(0)').children('.bc').attr('id','bc'+i);
        $('.tr_row:eq('+i+')').children('td:eq(1)').children('.pb').attr('id','pb'+i);
        $('.tr_row:eq('+i+')').children('td:eq(1)').children('.id_pb').attr('id','id_pb'+i);
        $('.tr_row:eq('+i+')').children('td:eq(2)').attr('id','hj'+i);
        $('.tr_row:eq('+i+')').children('td:eq(3)').attr('id','diskon'+i);
        $('.tr_row:eq('+i+')').children('td:eq(4)').children('.jl').attr('id','jl'+i);
        $('.tr_row:eq('+i+')').children('td:eq(5)').attr('id','subtotal'+i);
    }
    subTotal();
}
function add(i) {
     str = '<tr class=tr_row>'+
                '<td><input type=text name=nr[] id=bc'+i+' class=bc size=10 /></td>'+
                '<td><input type=text name=dr[] id=pb'+i+' class=pb size=60 /><input type=hidden name=id_pb[] id=id_pb'+i+' class=id_pb /></td>'+
                '<td id=hj'+i+' align=right></td>'+
                '<td align=center id=diskon'+i+'></td>'+
                '<td><input type=text name=jl[] id=jl'+i+' class=jl size=20 style="width: 100px;" onKeyup=subTotal() /><input type=hidden name=subtotal[] id=subttl'+i+' class=subttl /></td>'+
                '<td id=subtotal'+i+' align=right></td>'+
                '<td class=aksi><a class=delete onclick=eliminate(this)></a><input type=hidden name=disc[] id=disc'+i+' /><input type=hidden name=harga_jual[] id=harga_jual'+i+' /></td>'+
            '</tr>';

    $('.form-inputan tbody').append(str);
    $('#bc'+i).live('keydown', function(e) {
        if (e.keyCode==13) {
            var bc = $('#bc'+i).val();
            $.ajax({
                url: '<?= base_url('inv_autocomplete/get_penjualan_field') ?>/'+bc,
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
    $('#pb'+i).autocomplete("<?= base_url('inv_autocomplete/load_data_packing_barang_where_stok_ready') ?>",
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
        width: 440, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
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
            url: '<?= base_url('inv_autocomplete/get_harga_barang_penjualan') ?>/'+id_packing,
            cache: false,
            dataType: 'json',
            success: function(msg) {
                $('#hj'+i).html(numberToCurrency(Math.ceil(msg.harga))); // text asli
                $('#harga_jual'+i).val(msg.harga);
                $('#disc'+i).val(msg.diskon);
                $('#diskon'+i).html(msg.diskon);
                subTotal(i);
            }
        })
        $('#jl'+i).val('1');
        subTotal(i);
        var jml = $('.tr_row').length;
        //alert(jml+' - '+i)
        if (jml - i == 1) {
            add(jml);
        }
        $('#bc'+(i+1)).focus();
    });
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
function subTotal() {
    
    $(function() {
        var jumlah = $('.tr_row').length-1;
        var tagihan = 0;
        var disc = 0;
        //alert(jumlah)
        var jasa_apt = 0;
        var ppn = $('#ppn').val()/100;
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
        $('#total-tagihan').html(numberToCurrency(tagihan));
        var totallica = (tagihan - disc)+jasa_apt;
        var diskon_bank   = (totallica * ($('#disc_bank').html()/100));
        var pajak = ppn*tagihan;
        var new_totallica = (totallica - diskon_bank)+pajak;
        $('#total, #total_tagihan_penjualan_nr').html(numberToCurrency(Math.ceil(new_totallica)));
        if (tagihan !== 0) {
            $('#bulat').val(numberToCurrency(pembulatan_seratus(Math.ceil(new_totallica))));
        }
    });
}
function setKembali() {
        //var apoteker = currencyToNumber($('#jasa-apt').html());
        var total = currencyToNumber($('#total').html());
        var bayar = currencyToNumber($('#bayar').val());
        var bulat = currencyToNumber($('#bulat').val());
        var kembali = bayar - bulat;
        if (isNaN(bayar)) {var kembali = 0;} else {var kembali = kembali;} 
        
        if (kembali < 0) {
            var kembali = kembali;
        } else {
            var kembali = numberToCurrency(kembali);
        }
        //$('#bulat').val(numberToCurrency(total));
        $('#kembalian, #kembalian_nr').html(kembali);
        $('input[name=total]').val(total);
}
$(function() {
    $('#bulat').focus(function() {
        var kembalian = $('#kembalian').html();
        $('#kembalian').html(numberToCurrency(kembalian));
        var jumlah = $('.tr_row').length;
        for (i = 1; i <= jumlah; i++) {
            subTotal(i);
        }
    });
    $('#bayar').focus(function() {
        var jumlah = $('.tr_row').length;
        for (i = 1; i <= jumlah; i++) {
            subTotal(i);
        }
    });
    $('#tanggal').datetimepicker();
    $('#reset').click(function() {
        $('#loaddata').empty();
        var url = '<?= base_url('pelayanan/penjualan_nr') ?>';
        $('#loaddata').load(url+'?_'+Math.random());
    });
    $("#form_penjualan_non_resep").submit(function() {
        
        if ($('#id_pembeli').val() == '') {
            alert('Nama pembeli tidak boleh kosong !');
            $('#pasien').focus();
            return false;
        }
        if ($('#bayar').val() == '') {
            alert('Jumlah bayar tidak boleh kosong !');
            $('#bayar').focus();
            return false;
        }
        if ($('#bulat').val() == '') {
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
            url: post+'?_'+Math.random(),
            dataType: 'json',
            data: $(this).serialize(),
            success: function(data) {
                if (data.status === true) {
                    $('input,select').attr('disabled','disabled');
                    $('#print').show();
                    $('#id_penjualan').html(data.id_penjualan);
                    $('button[type=submit]').hide();
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
    $('input,select').live('keydown', function(e) {
        if (e.keyCode === 120) {
            form_open();
        }
    });
});
</script>
    <h1><?= $title ?></h1>
    <?= form_open('pelayanan/penjualan_nr', 'id=form_penjualan_non_resep') ?>
    <div class="data-list">
        <?= form_button(null, 'Tambah Baris', 'id=addnewrow') ?>
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th>Barcode</th>
                <th width="40%">Packing Barang</th>
                <?php if (isset($_GET['id'])) { ?>
                <th width="10%">ED</th>
                <?php } ?>
                <th width="15%">Harga Jual</th>
                <th width="7%">Diskon</th>
                <th width="10%">Jumlah</th>
                <th width="10%">Sub Total</th>
                <th>#</th>
            </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_GET['id'])) { 
                $penjualan = penjualan_muat_data($_GET['id']);
                $no = 0;
                foreach ($penjualan as $key => $data) {
                    $hjual = ($data['hna']*($data['margin']/100))+$data['hna'];
                ?>
                <tr class="<?= ($key%2==0)?'odd':'even' ?> tr_row">
                    <td align="center"><?= $data['barcode'] ?></td>
                    <td><?= "$data[barang] ".(($data['kekuatan'] != '1')?$data['kekuatan']:null)." $data[satuan] $data[sediaan] $data[pabrik] @ ".(($data['isi']==1)?'':$data['isi'])." $data[satuan_terkecil]"; ?></td>
                    <?php if (isset($_GET['id'])) { ?>
                    <td align="center"><?= datefmysql($data['ed']) ?></td>
                    <?php } ?>
                    <td align="right" id="hj<?= $no ?>"><?= inttocur($hjual) ?></td>
                    <td align="center" id="diskon<?= $no ?>"><?= $data['diskon'] ?></td>
                    <td align="center" id="jl<?= $no ?>"><?= $data['keluar'] ?></td>
                    <td align="right"><?= inttocur(($hjual - ($hjual*($data['diskon']/100)))*$data['keluar']) ?></td>
                    <td></td>
                </tr>
                <?php $no++; } 
                } ?>
            </tbody>
        </table> 
    </div>
   <div class="data-input">
        <fieldset><legend>Summary</legend>
        <?= form_hidden('total') ?>
            <div class="left_side">
                <label>No.</label> <span class="label" id="id_penjualan"><?= isset($_GET['id'])?$_GET['id']:get_last_id('penjualan', 'id') ?> </span>
                <label>Waktu</label><?= form_input('tanggal', date("d/m/Y H:i"), 'id=tanggal') ?>
                <label>Pembayaran Bank</label><?= form_dropdown('cara_bayar', $list_bank, NULL, 'id=pembayaran') ?>
                <label>PPN (%)</label><?= form_input('ppn', '0', 'id=ppn size=10 onkeyup=subTotal()') ?>
                <label>Pembeli</label><?= form_input(null, null, 'id=pembeli') ?><?= form_hidden('id_pembeli') ?>
                <label>Total Tagihan</label><span class="label" id="total-tagihan"><?= isset($data['total'])?rupiah($data['total']):null ?> </span>
                
            </div><div class="right_side">
                <label>Total Diskon Barang</label><span class="label" id="total-diskon"><?= isset($data['subtotal'])?rupiah($data['subtotal']):null ?></span>
                <label>Diskon Bank (%)</label><span id="disc_bank" class="label"><?= isset($data['diskon_bank'])?$data['diskon_bank']:'0' ?></span><?= form_hidden('diskon_bank', isset($data['diskon_bank'])?$data['diskon_bank']:'0', 'id=diskon_bank size=10 ') ?>
                <label>Total</label><span id="total" class="label"><?= isset($data['total'])?rupiah($data['total']):null ?></span>
                <label>Pembulatan Total</label><?= form_input('bulat', isset($data['total'])?rupiah($data['total']):NULL, 'id=bulat size=30 onkeyup=FormNum(this) ') ?>
<!--                <label>Bayar (Rp)</label><?= form_input('bayar', isset($data['bayar'])?rupiah($data['bayar']):null, 'id=bayar size=30 ') ?>
                <label>Kembalian (Rp)</label><span id="kembalian" class="label"><?= rupiah(isset($kembali)?$kembali:null) ?></span>-->
                
            </div>
        </fieldset>
    </div>
    <?= form_submit('save', 'Simpan', 'id=save') ?>
    <?= form_button('Reset', 'Reset', 'id=reset') ?>
    <?= form_button(null, 'Cetak Nota', 'id=print') ?>
    <!--<?= form_button(null, 'Retur Penjualan', 'id=retur') ?>-->
    <?= form_close() ?>
</div>