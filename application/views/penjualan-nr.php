<?php $this->load->view('message'); ?>
<title><?= $title ?></title>
<div id="result_cetak" style="display: none"></div>
<div class="kegiatan">
<script type="text/javascript" src="<?= base_url('assets/js/colResizable-1.3.min.js') ?>"></script>
<script type="text/javascript">
function eliminate(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);
    var jumlah = $('.tr_row').length-1;
    
    for (i = 0; i <= jumlah; i++) {
        $('.tr_row:eq('+i+')').children('td:eq(0)').children('.bc').attr('id','bc'+i);
        $('.tr_row:eq('+i+')').children('td:eq(1)').children('.pb').attr('id','pb'+i);
        $('.tr_row:eq('+i+')').children('td:eq(1)').children('.id_pb').attr('id','id_pb'+i);
        $('.tr_row:eq('+i+')').children('td:eq(1)').children('.id_barang').attr('id','id_barang'+i);
        $('.tr_row:eq('+i+')').children('td:eq(2)').children('.ed').attr('id','exp'+i);
        $('.tr_row:eq('+i+')').children('td:eq(2)').children('.jl').attr('id','jl'+i);
        $('.tr_row:eq('+i+')').children('td:eq(3)').attr('id','unit'+i);
        $('.tr_row:eq('+i+')').children('td:eq(4)').attr('id','ed'+i);
        $('.tr_row:eq('+i+')').children('td:eq(5)').attr('id','hj'+i);
        $('.tr_row:eq('+i+')').children('td:eq(6)').attr('id','diskon'+i);
        $('.tr_row:eq('+i+')').children('td:eq(7)').attr('id','subtotal'+i);
    }
    subTotal();
}
function add(i) {
     str = '<tr class=tr_row>'+
                '<td><input type=text name=nr[] id=bc'+i+' class=bc size=10 /></td>'+
                '<td><input type=text name=dr[] id=pb'+i+' class=pb size=60 /><input type=hidden name=id_pb[] id=id_pb'+i+' class=id_pb /><input id=id_barang'+i+' class=id_barang name=id_barang[] type=hidden /> </td>'+
                '<td><input type=hidden name=ed[] id=exp'+i+' class=ed /><input type=text name=jl[] id=jl'+i+' class=jl size=20 style="width: 100%;" onKeyup=subTotal() onblur=subTotal() /><input type=hidden name=subtotal[] id=subttl'+i+' class=subttl /></td>'+
                '<td id=unit'+i+' align=right><select style="width: 100%;"></select></td>'+
                '<td id=ed'+i+' align=center></td>'+
                '<td id=hj'+i+' align=right></td>'+
                '<td align=center><input type=text name=diskon[] id=diskon'+i+' class=diskon size=10 onkeyup=subTotal() /></td>'+
                '<td id=subtotal'+i+' align=right></td>'+
                //'<td class=aksi><span class=delete onclick=eliminate(this)><?= img('assets/images/icons/delete.png') ?></span><input type=hidden name=disc[] id=disc'+i+' /><input type=hidden name=harga_jual[] id=harga_jual'+i+' /></td>'+
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
    var lebar = $('#pb'+i).width();
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
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
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
        $('#jl'+i).focus();
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

function create_dialog() {
    var str = '<div id="penjualan_non_resep_bayar" class="data-input" style="display: none">'+
	'<table width=100%><tr><td width=30% style="font-size: 18px">Total:</td><td style="font-size: 18px;" id=totalopen></td></tr>'+
	'<tr><td style="font-size: 18px">Bayar (Rp):</td><td><input style="font-size: 18px;" type=text id=bayar size=5 /></td></tr>'+
        '<tr><td colspan=2>&nbsp;</td></tr>'+
        '<tr><td colspan=2>&nbsp;</td></tr>'+
        '<tr><td style="font-size: 18px">Kembalian (Rp):</td><td style="font-size: 18px;" id="kembalian_nr"></td></tr></table>'+
    '</div>';
    $('#form_penjualan_non_resep').append(str);
    $('#totalopen').html($('#bulat').val());
    $('#penjualan_non_resep_bayar').dialog({
        autoOpen: true,
        modal: true,
        width: 500,
        height: 320,
        title: 'Entri Pembayaran',
        close: function() {
            $(this).dialog().remove();
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

$(function() {
    $('#reset').click(function() {
        $('.kegiatan').remove();
        $('#loaddata').empty().load('<?= base_url('pelayanan/penjualan_nr') ?>');
    });
    var onSampleResized = function(e){
        var columns = $(e.currentTarget).find("th");
        var msg = "columns widths: ";
        columns.each(function(){ msg += $(this).width() + "px; "; });
    };
    $(".tabel").colResizable({
        liveDrag:true,
        gripInnerHtml:"<div class='grip'></div>", 
        draggingClass:"dragging", 
        onResize:onSampleResized
    });
    for (i = 0; i <= 5; i++) {
        add(i);
    }
    $(document).live('keydown', function(e) {
        if (e.keyCode === 120) {
            $('#penjualan_non_resep_bayar').remove();
            create_dialog();
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
    $('#bulat').focus(function() {
        var kembalian = $('#kembalian').html();
        $('#kembalian').html(numberToCurrency(kembalian));
        var jumlah = $('.tr_row').length;
        for (i = 1; i <= jumlah; i++) {
            subTotal(i);
        }
    });
    $('#bayar_nr').focus(function() {
        create_dialog();
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
});

</script>
<h1><?= $title ?></h1>
<?= form_open('pelayanan/penjualan_nr', 'id=form_penjualan_non_resep') ?>
    
    <div class="data-list">
        <!--<?= form_button(null, 'Tambah Baris', 'id=addnewrow') ?>-->
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="10%">Barcode</th>
                <th width="35%">Barang</th>
                <th width="10%">Jumlah</th>
                <th width="10%">Unit Kemasan</th>
                <th width="7%">ED</th>
                <th width="10%">Harga Jual</th>
                <th width="7%">Diskon</th>
                <th width="10%">Sub Total</th>
<!--                <th width="5%">Aksi</th>-->
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table> 
    </div>
    <div class="data-input">
        <fieldset>
        <?= form_hidden('total') ?>
            <div class="left_side" style="min-height: 150px; margin-bottom: 10px;">
                <table width="100%">
                    <tr><td width="25%">No.:</td> <td class="label" id="id_penjualan"><?= isset($_GET['id'])?$_GET['id']:get_last_id('penjualan', 'id') ?> </td></tr>
                    <tr><td>Waktu:</td><td><?= form_input('tanggal', date("d/m/Y H:i"), 'id=tanggal') ?></td></tr>
                    <tr><td>Pembayaran Bank:</td><td><?= form_dropdown('cara_bayar', $list_bank, NULL, 'id=pembayaran') ?></td></tr>
                    <tr><td>PPN (%):</td><td><?= form_input('ppn', '0', 'id=ppn size=10 onkeyup=subTotal()') ?></td></tr>
                    <tr><td>Pembeli:</td><td><?= form_input(null, null, 'id=pembeli') ?><?= form_hidden('id_pembeli') ?></td></tr>
                    <tr><td>Total Tagihan:</td><td id="total-tagihan"><?= isset($data['total'])?rupiah($data['total']):null ?> </td></tr>
                </table>
            </div>
            <div class="right_side" style="min-height: 150px; margin-bottom: 10px;">
                <table width="100%">
                    <tr><td width="25%">Total Diskon Barang:</td><td class="label" id="total-diskon"></td></tr>
                    <tr><td>Diskon Bank (%):</td><td><span id="disc_bank" class="label">0</span><?= form_hidden('diskon_bank', 0) ?></td></tr>
                    <tr><td>Diskon Penjualan (%):</td><td id="disk_penjualan" class="label"></td></tr>
                    <tr><td>Total:</td><td id="total" class="label"></td></tr>
                    <tr><td>Pembulatan Total:</td><td><?= form_input('bulat', NULL, 'id=bulat size=30 onkeyup=FormNum(this) ') ?></td></tr>
                    <tr><td>Bayar (Rp):</td><td><?= form_input('bayar', null, 'id=bayar_nr size=30 ') ?></td></tr>
                <!--<tr><td>Kembalian (Rp)</td><td><span id="kembalian" class="label"><?= rupiah(isset($kembali)?$kembali:null) ?></span>-->
                </table>
            </div>
            <?= form_button('Reset', 'Reset Form', 'id=reset') ?>
            <?= form_button(null, 'Cetak Nota', 'id=print') ?>
        </fieldset>
<!--<?= form_submit('save', 'Simpan', 'id=save') ?>-->
    
     <!--<?= form_button(null, 'Retur Penjualan', 'id=retur') ?>-->
        
    </div>
    
    <?= form_close() ?>
</div>