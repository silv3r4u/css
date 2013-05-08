<title><?= $title ?></title>
<div id="result_cetak" style="display: none"></div>
<?php $this->load->view('message'); ?>
<script type="text/javascript">
    $('#reset').click(function() {
        $('#penjualan_non_resep_bayar').dialog().remove();
        $('#loaddata').empty();
        var url = '<?= base_url('pelayanan/penjualan_nr') ?>';
        $('#loaddata').load(url+'?_='+Math.random());
    });
$(function() {
    add(0);
    $('input').live('keyup', function(e) {
        if (e.keyCode === 120) {
            create_dialog();
            //$('#penjualan_non_resep_bayar').dialog().remove();
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
        window.open('<?= base_url('pelayanan/penjualan_cetak_nota') ?>/'+id+'/nonresep', 'cetak nota', 'width=600px, height=400px, resizable=1, scrollbars=1');
        /*$.get('<?= base_url('pelayanan/penjualan_cetak_nota') ?>/'+id+'/nonresep', function(data) {
            $('#result_cetak').html(data);
            $('#result_cetak').dialog({
                autoOpen: true,
                modal: true,
                width: 350,
                height: 400
            });
        });*/
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
            if (data.kelurahan !== null) { var kelurahan = data.kelurahan; }
            var str = '<div class=result>'+data.nama+'<br/>'+data.alamat+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $('#pembeli').val(data.nama);
        $('input[name=id_pembeli]').val(data.penduduk_id);
        $('#disk_penjualan').html(data.member);
        subTotal();
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
        $('.tr_row:eq('+i+')').children('td:eq(1)').children('.id_barang').attr('id','id_barang'+i);
        $('.tr_row:eq('+i+')').children('td:eq(2)').attr('id','unit'+i);
        $('.tr_row:eq('+i+')').children('td:eq(3)').attr('id','ed'+i);
        $('.tr_row:eq('+i+')').children('td:eq(4)').attr('id','hj'+i);
        $('.tr_row:eq('+i+')').children('td:eq(5)').attr('id','diskon'+i);
        $('.tr_row:eq('+i+')').children('td:eq(6)').children('.ed').attr('id','exp'+i);
        $('.tr_row:eq('+i+')').children('td:eq(6)').children('.jl').attr('id','jl'+i);
        $('.tr_row:eq('+i+')').children('td:eq(7)').attr('id','subtotal'+i);
    }
    subTotal();
}
function add(i) {
     str = '<tr class=tr_row>'+
                '<td><input type=text name=nr[] id=bc'+i+' class=bc size=10 /></td>'+
                '<td><input type=text name=dr[] id=pb'+i+' class=pb size=60 /><input type=hidden name=id_pb[] id=id_pb'+i+' class=id_pb /><input id=id_barang'+i+' class=id_barang name=id_barang[] type=hidden /> </td>'+
                '<td id=unit'+i+' align=center><select style="border: 1px solid #ccc; width: 100%;"></select></td>'+
                '<td id=ed'+i+' align=center></td>'+
                '<td id=hj'+i+' align=right></td>'+
                '<td align=center><input type=text name=diskon[] id=diskon'+i+' class=diskon size=10 onkeyup=subTotal() /></td>'+
                '<td><input type=hidden name=ed[] id=exp'+i+' class=ed /><input type=text name=jl[] id=jl'+i+' class=jl size=20 style="width: 100%;" onKeyup=subTotal() /><input type=hidden name=subtotal[] id=subttl'+i+' class=subttl /></td>'+
                '<td id=subtotal'+i+' align=right></td>'+
                '<td class=aksi><span class=delete onclick=eliminate(this)><?= img('assets/images/icons/delete.png') ?></span><input type=hidden name=disc[] id=disc'+i+' /><input type=hidden name=harga_jual[] id=harga_jual'+i+' /></td>'+
            '</tr>';

    $('.form-inputan tbody').append(str);
    $('#bc'+i).live('keydown', function(e) {
        if (e.keyCode===13) {
            var bc = $('#bc'+i).val();
            $.ajax({
                url: '<?= base_url('inv_autocomplete/get_penjualan_field') ?>/'+bc,
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
                            $('#pb'+i).val(data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil);
                        } else {
                            if (data.generik === 'Non Generik') {
                                $('#pb'+i).val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil);
                            } else {
                                $('#pb'+i).val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil);
                            }
                        }
                        $('#id_pb'+i).val(data.id);
                        $('#kekuatan'+i).html(data.kekuatan);
                        $('#hj'+i).html(numberToCurrency(Math.ceil(data.harga))); // text asli
                        $('#harga_jual'+i).val(data.harga);
                        $('#disc'+i).val(data.diskon);
                        $('#diskon'+i).val(data.diskon);
                        $('#jl'+i).val('1');
                        subTotal(i);
                        var jml = $('.tr_row').length;
                        //alert(jml+' - '+i)
                        if (jml - i === 1) {
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
                        $('#diskon'+i).val('');
                    }
                }
            });
            return false;
        }
    });
    $('#pb'+i).autocomplete("<?= base_url('inv_autocomplete/load_data_packing_barang_per_ed') ?>",
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
                var str = '<div class=result>'+data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil+'<br/>Expired: '+datefmysql(data.ed)+'</div>';
            } else {
                if (data.generik === 'Non Generik') {
                    var str = '<div class=result>'+data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil+'<br/>Expired: '+datefmysql(data.ed)+'</div>';
                } else {
                    var str = '<div class=result>'+data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil+'<br/>Expired: '+datefmysql(data.ed)+'</div>';
                }
            }
            return str;
        },
        width: 440, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
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
        $('#id_pb'+i).val(data.id);
        $('#bc'+i).val(data.barcode);
        $('#ed'+i).html(datefmysql(data.ed));
        $('#exp'+i).val(data.ed);
        $('#id_barang'+i).val(data.id_barang);
        $('#kekuatan'+i).html(data.kekuatan);
        var id_barang = data.barang_id;
        $.ajax({
            url: '<?= base_url('inv_autocomplete/get_kemasan_barang') ?>/'+id_barang+'/'+i,
            cache: false,
            success: function(data) {
                $('#unit'+i).html(data);
            }
        }); 
        var id_packing = data.id;
        $.ajax({
            url: '<?= base_url('inv_autocomplete/get_harga_barang_penjualan') ?>/'+id_packing,
            cache: false,
            dataType: 'json',
            success: function(msg) {
                $('#hj'+i).html(numberToCurrency(Math.ceil(msg.harga))); // text asli
                $('#harga_jual'+i).val(msg.harga);
                $('#disc'+i).val(msg.diskon);
                $('#diskon'+i).val(msg.diskon);
                subTotal(i);
            }
        });
        $('#jl'+i).val('1');
        var jml = $('.tr_row').length;
        
        if (jml - i === 1) {
            add(jml);
        }
        $('#bc'+(i+1)).focus();
        //subTotal();
    });
}

function pembulatan_seratus(angka) {
    var kelipatan = 100;
    var sisa = angka % kelipatan;
    if (sisa !== 0) {
        var kekurangan = kelipatan - sisa;
        var hasilBulat = angka + kekurangan;
        return Math.ceil(hasilBulat);
    } else {
        return Math.ceil(angka);
    }
    
    
}

function get_harga_jual(i) {
    var id_barang = $('#id_barang'+i).val();
    var id_kemasan= $('#kemasan'+i).val();
    var value = id_kemasan.split('-');
    var kemasan = value[0];
    $.ajax({
        url: '<?= base_url('inv_autocomplete/get_harga_jual_barang_kemasan') ?>/'+id_barang+'/'+kemasan,
        cache: false,
        dataType: 'json',
        success: function(data) {
            $('#hj'+i).html(numberToCurrency(Math.ceil(data.harga_jual)));
            var h_jual = currencyToNumber($('#hj'+i).html());
            var jumlah = $('#jl'+i).val();
            var subtotal = h_jual*jumlah;
            $('#subtotal'+i).html(numberToCurrency(Math.ceil(subtotal)));
            subTotal();
        }
    });
}

function subTotal() {
        var jumlah = $('.tr_row').length-1;
        var tagihan = 0;
        var disc = 0;
        
        var jasa_apt = 0;
        var ppn = $('#ppn').val()/100;
        for (i = 0; i <= jumlah; i++) {
            if ($('#id_pb'+i).val() !== '') {
                var harga  = currencyToNumber($('#hj'+i).html());
                var diskon = $('#diskon'+i).val()/100;
                var jml    = $('#jl'+i).val();
                
                var subtotal = numberToCurrency(Math.ceil((harga - (harga*diskon))*jml));
                $('#subtotal'+i).html(subtotal);
                $('#subttl'+i).val(subtotal);
                //var jumlah= parseInt($('#jl'+i).val());
                subtotall = 0;
            //alert(harga); alert(diskon); alert(jumlah);
            
                if (parseInt($('#subttl'+i).val()) !== '') {
                    //var subtotall = parseInt($('#subttl'+i).val());
                    var subtotall = harga*jml;
                }
                var disc = disc + ((diskon*harga)*jml);
                var tagihan = tagihan + subtotall;
            }
        }
        $('#total-diskon').html(numberToCurrency(Math.ceil(disc)));
        $('#total-tagihan').html(numberToCurrency(tagihan));
        var totallica = (tagihan - disc)+jasa_apt;
        var diskon_bank   = (totallica * ($('#disc_bank').html()/100));
        var diskon_jual   = (totallica * ($('#disk_penjualan').html()/100));
        var pajak = ppn*tagihan;
        var new_totallica = (totallica - (diskon_bank+diskon_jual))+pajak;
        $('#total, #total_tagihan_penjualan_nr').html(numberToCurrency(Math.ceil(new_totallica)));
        if (tagihan !== 0) {
            $('#bulat').val(numberToCurrency(pembulatan_seratus(Math.ceil(new_totallica))));
        } else { $('#bulat').val(''); }
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
    $("#form_penjualan_non_resep").submit(function() {
        
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
});
function create_dialog() {
    $('#totalopen').html($('#bulat').val());
    $('#penjualan_non_resep_bayar').dialog({
        autoOpen: true,
        modal: true,
        width: 330,
        height: 320,
        title: 'Entri Pembayaran',
        close: function() {
            $(this).dialog('close');
        }, buttons: {
            "Simpan Pembayaran": function() {
                $('#bayar_nr').val($('#bayar').val());
                $('#form_penjualan_non_resep').submit();
                $(this).dialog().remove();
            }
        }
    });
    $('#bayar').keyup(function() {
        FormNum(this);
        setKembali();  
    });
}
</script>
<div class="kegiatan">
    <h1><?= $title ?></h1>
    <?= form_open('pelayanan/penjualan_nr', 'id=form_penjualan_non_resep') ?>
    <div id="penjualan_non_resep_bayar" class="data-input" style="display: none">
	<label style="font-size: 18px">Total:</label><span style="font-size: 18px; padding-left: 3px;" class=label id=totalopen></span>
	<label style="font-size: 18px">Bayar (Rp):</label><input style="font-size: 18px;" type=text id=bayar size=5 />
        <label style="font-size: 18px">Kembalian (Rp):</label><span style="font-size: 18px; padding-left: 3px; padding-top: 7px;" id="kembalian_nr" class="label"></span>
    </div>
    <div class="data-list">
        <!--<?= form_button(null, 'Tambah Baris', 'id=addnewrow') ?>-->
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="10%">Barcode</th>
                <th width="30%">Packing Barang</th>
                <th width="10%">Unit Kemasan</th>
                <th width="10%">ED</th>
                <th width="15%">Harga Jual</th>
                <th width="7%">Diskon</th>
                <th width="10%">Jumlah</th>
                <th width="10%">Sub Total</th>
                <th>#</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table> 
    </div>
   <div class="data-input">
        <fieldset><legend>Summary</legend>
        <?= form_hidden('total') ?>
            <div class="left_side" style="min-height: 160px">
                <label>No.</label> <span class="label" id="id_penjualan"><?= isset($_GET['id'])?$_GET['id']:get_last_id('penjualan', 'id') ?> </span>
                <label>Waktu</label><?= form_input('tanggal', date("d/m/Y H:i"), 'id=tanggal') ?>
                <label>Pembayaran Bank</label><?= form_dropdown('cara_bayar', $list_bank, NULL, 'id=pembayaran') ?>
                <label>PPN (%)</label><?= form_input('ppn', '0', 'id=ppn size=10 onkeyup=subTotal()') ?>
                <label>Pembeli</label><?= form_input(null, null, 'id=pembeli') ?><?= form_hidden('id_pembeli') ?>
                <label>Total Tagihan</label><span class="label" id="total-tagihan"><?= isset($data['total'])?rupiah($data['total']):null ?> </span>
                
            </div><div class="right_side" style="min-height: 160px">
                <label>Total Diskon Barang</label><span class="label" id="total-diskon"></span>
                <label>Diskon Bank (%)</label><span id="disc_bank" class="label">0</span><?= form_hidden('diskon_bank', 0) ?>
                <label>Diskon Penjualan (%)</label><span id="disk_penjualan" class="label"></span>
                <label>Total</label><span id="total" class="label"></span>
                <label>Pembulatan Total</label><?= form_input('bulat', NULL, 'id=bulat size=30 onkeyup=FormNum(this) ') ?>
                <label>Bayar (Rp)</label><?= form_input('bayar', null, 'id=bayar_nr size=30 ') ?>
                <!--<label>Kembalian (Rp)</label><span id="kembalian" class="label"><?= rupiah(isset($kembali)?$kembali:null) ?></span>-->
                
            </div>
        </fieldset>
    </div>
    <!--<?= form_submit('save', 'Simpan', 'id=save') ?>-->
    <?= form_button('Reset', 'Reset', 'id=reset') ?>
    <?= form_button(null, 'Cetak Nota', 'id=print') ?>
    <!--<?= form_button(null, 'Retur Penjualan', 'id=retur') ?>-->
    <?= form_close() ?>
</div>