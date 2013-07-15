<?php $this->load->view('message'); ?>
<script type="text/javascript">
function loading() {
    $('#loaddata').html('');
    var url = '<?= base_url('inventory/pembelian') ?>';
    $('#loaddata').load(url);
}
$(function() {
    $('#nopemesanan').focus();
    $('button[id=print], button[id=delete_pembelian]').hide();
    $('#delete_pembelian').click(function() {
        var ok=confirm('Anda yakin akan menghapus transaksi ini ?');
        if (ok) {
            var id = $('#id_pembelian').html();
            $.get('<?= base_url('inventory/pembelian_delete') ?>/'+id, function(data) {
                if (data.status === true) {
                    alert_delete();
                    loading();
                }
            },'json');
        } else {
            return false;
        }
    });
    $('#jenis').change(function() {
        var nilai = $('#jenis').val();
        if (nilai === 'konsinyasi') {
            $('#label_sp,#nopemesanan,#tgl_tempo,#tempo').hide();
        }
        if (nilai === 'cash') {
            $('#tgl_tempo,#tempo').show();
            $('#tempo').val('<?= date("d/m/Y") ?>');
            $('#label_sp,#nopemesanan').hide();
        }
        if (nilai === 'tempo') {
            $('#tempo').val('');
            $('#label_sp,#nopemesanan,#tgl_tempo,#tempo').show();
        }
    });
    $('#form_pembelian').submit(function() {
        var jumlah = $('.tr_row').length;
        for (i = 0; i <= jumlah; i++) {
            if ($('#id_pb'+i).val() === '') {
                alert('Data packing barang tidak boleh kosong !');
                $('#pb'+i).focus();
                return false;
            }
            if (($('#ed'+i).val() === '00/00/0000') || ($('#ed'+i).val() === '')) {
                alert('Expired date tidak boleh kosong !');
                $('#ed'+i).focus();
                return false;
            }
            if ($('#harga'+i).val() === '') {
                alert('Harga tidak boleh kosong !');
                $('#harga'+i).focus();
                return false;
            }
            if ($('#net'+i).val() === '') {
                alert('HET tidak boleh kosong !');
                $('#net'+i).focus();
                return false;
            }
            if ($('#kemasan'+i).val() === '') {
                alert('Pilih kemasan yang akan di konversikan !');
                $('#kemasan'+i).focus();
                return false;
            }
        }
        var url = $(this).attr('action');
        $.ajax({
            type: 'POST',
            url: url+'?_'+Math.random(),
            data: $(this).serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.status === true) {
                    $('input, textarea').attr('disabled','disabled');
                    $('button[type=submit]').hide();
                    $('button[id=delete_pembelian]').show();
                    $('#id_pembelian').html(data.id_pembelian);
                    //$('input[name=id]').val(data.id_pemesanan);
                    //$('button[id=delete_pembelian],button[id=print]').removeAttr('disabled');
                    alert_tambah();
                }
            }
        });
        return false;
    });
    $('button[id=reset]').click(function() {
        loading();
    });
    $('input[type=submit]').each(function(){
        $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
    });
    $('button[type=submit]').button({
        icons: {
            primary: 'ui-icon-circle-check'
        }
    });
    $('button[id=delete_pembelian]').button({
        icons: {
            primary: 'ui-icon-circle-close'
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
    $('button[id=print]').button({
        icons: {
            primary: 'ui-icon-print'
        }
    });
    $('#cetakexcel').click(function() {
        location.href='<?= base_url('cetak/inventory/pembelian') ?>?id=<?= isset($_GET['id'])?$_GET['id']:NULL ?>';
    })
    $('#printhasil').click(function() {
        window.open('<?= base_url('cetak/inventory/pembelian') ?>?id=<?= isset($_GET['id'])?$_GET['id']:NULL ?>','mywindow','location=1,status=1,scrollbars=1,width=900px,height=400px');
    })
    $('.ed, #tempo,.tanggals').datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#retur').click(function() {
        
        var id = $('#nopemesanan').val();
        var id_pembelian = '<?= isset($_GET['id'])?$_GET['id']:NULL ?>';
        if (id_pembelian == '') {
            alert('Untuk melakukan retur harus melalui menu informasi stok');
            return false;
        }
        $.ajax({
            url: '<?= base_url('inventory/fillField') ?>',
            data: 'act=checkpembelian&id='+id,
            cache: false,
            success: function(msg) {
                if (msg) {
                    location.href='<?= base_url('inventory/retur-pembelian') ?>?id='+id_pembelian;
                } else {
                    alert('Nomor pembelian belum terdaftar');
                    return false;
                }
            }
        })
        //
    })
    $('#nopemesanan').autocomplete("<?= base_url('inv_autocomplete/get_nomor_pemesanan') ?>",
    {
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].dokumen_no // nama field yang dicari
                };
            }
            return parsed;
        },
        formatItem: function(data,i,max){
            var str = '<div class=result>'+data.dokumen_no+'<br/>Suplier: '+data.pabrik+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.dokumen_no);
        $('input[name=no_pemesanan]').val(data.id);
        $('#suplier').val(data.pabrik);
        $('input[name=id_suplier]').val(data.suplier_relasi_instansi_id);
        $('#sales').val(data.sales);
        $('input[name=id_sales]').val(data.salesman_penduduk_id);
        var id = data.id;
        $('.form-inputan tbody').empty();
        $.ajax({
            url: '<?= base_url('inventory/get_data_pemesanan') ?>/'+id+'?_'+Math.random(),
            cache: false,
            success: function(msg) {
                $('.form-inputan tbody').html(msg);
            }
        });
    });
    
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
            var str = '<div class=result>'+data.nama+'<br/>'+data.alamat+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).attr('value',data.nama);
        $('input[name=id_sales').val(data.id);
    });
})
function eliminate(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);
    var jumlah = $('.tr_row').length-1;
    for (i = 0; i <= jumlah; i++) {
        $('.tr_row:eq('+i+')').children('td:eq(0)').html((i+1));
        $('.tr_row:eq('+i+')').children('td:eq(1)').children('.batch').attr('id','batch'+i);
        $('.tr_row:eq('+i+')').children('td:eq(2)').children('.pb').attr('id','pb'+i);
        $('.tr_row:eq('+i+')').children('td:eq(2)').children('.id_pb').attr('id','id_pb'+i);
        $('.tr_row:eq('+i+')').children('td:eq(3)').children('.ed').attr('id','ed'+i);
        $('.tr_row:eq('+i+')').children('td:eq(4)').children('.jml').attr('id','jml'+i);
        $('.tr_row:eq('+i+')').children('td:eq(5)').children('.harga').attr('id','harga'+i);
        $('.tr_row:eq('+i+')').children('td:eq(6)').attr('id','unit'+i);
        $('.tr_row:eq('+i+')').children('td:eq(6)').children('.kemasan').attr('id','kemasan'+i);
        $('.tr_row:eq('+i+')').children('td:eq(7)').children('.isi').attr('id','isi'+i);
        $('.tr_row:eq('+i+')').children('td:eq(8)').children('.diskon_pr').attr('id','diskon_pr'+i);
        $('.tr_row:eq('+i+')').children('td:eq(9)').children('.diskon_rp').attr('id','diskon_rp'+i);
        $('.tr_row:eq('+i+')').children('td:eq(10)').attr('id','subtotal'+i);
        $('.tr_row:eq('+i+')').children('td:eq(11)').children('input[name=bonus]').attr('id','check'+i);
        $('.tr_row:eq('+i+')').children('td:eq(12)').children('input[name=bonus]').attr('class',i);
    }
    hitungDetail();
}

function bonusthis() {
    var i = $(this).attr('class');
    if ($('#check'+i).is(':checked')) {
        $('#diskon_pr'+i).attr('value','100');
        jmlSubTotal(i);
        hitungDetail();
    } else {
        $('#diskon_pr'+i).attr('value','0');
        jmlSubTotal(i);
        hitungDetail();
    }
}

function add(i) {
     str = '<tr class=tr_row>'+
        '<td align=center>'+(i+1)+'</td>'+
        '<td><input type=text name=batch[] id=batch'+i+' size=10 class=batch />'+
        '<td><input type=text name=pb[] id=pb'+i+' size=50 class=pb />'+
        '<input type=hidden name=id_pb[] id=id_pb'+i+' class=id_pb /><input type=hidden name=barang_id[] id=barang_id'+i+' class=barang_id /></td>'+
        '<td><input type=text name=ed[] id=ed'+i+' size=8 class=ed /></td>'+
        '<td><input type=text name=jml[] id=jml'+i+' size=2 class=jml onblur=jmlSubTotal('+i+') /></td>'+
        '<td><input type=text name=harga[] id=harga'+i+' size=6 onkeyup=FormNum(this) onblur=jmlSubTotal('+i+') class=harga /></td>'+
        '<td align="center" id=unit'+i+'><select class=kemasan id=kemasan'+i+'></select></td>'+
        '<td><input type=text name=isi[] id=isi'+i+' size=2 class=isi /></td>'+
        '<td><input type=text name=diskon_pr[] id=diskon_pr'+i+' size=2 class=diskon_pr onkeyup=jmlSubTotal('+i+') onblur=hitungDetail() class=diskon_pr maxlength=3 min=0 max=100 /></td>'+
        '<td><input type=text name=diskon_rp[] id=diskon_rp'+i+' size=6 onkeyup=FormNum(this) onblur=jmlSubTotal('+i+') class=diskon_rp />'+
        '<input type=hidden name=subtotal[] id=subttl'+i+' class=subttl /></td>'+
        '<td id=subtotal'+i+' align=right></td>'+
        '<td align=center><input type=checkbox name=bonus id=check'+i+' class="'+i+'" onClick=bonusthis() /></td>'+
        '<td class=aksi><span class=delete onclick=eliminate(this)><?= img('assets/images/icons/delete.png') ?></span></td>'+
    '</tr>';

    $('.form-inputan tbody').append(str);
    $('#ed'+i).datepicker({
        changeMonth: true,
        changeYear: true
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
                    var str = '<div class=result>'+data.nama+' '+((data.kekuatan === '1')?'':data.kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil+'</div>';
                } else {
                    var str = '<div class=result>'+data.nama+' '+((data.kekuatan === '1')?'':data.kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
                }
            }
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
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
                $(this).val(data.nama+' '+((data.kekuatan === '1')?'':data.kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil);
            } else {
                $(this).val(data.nama+' '+((data.kekuatan === '1')?'':data.kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil);
            }
        }
        $('#id_pb'+i).val(data.id);
        $('#barang_id'+i).val(data.barang_id);
        var id_barang = data.barang_id;
        $.ajax({
            url: '<?= base_url('inv_autocomplete/get_kemasan_barang_pembelian') ?>/'+id_barang+'/'+i,
            cache: false,
            success: function(data) {
                $('#unit'+i).html(data);
            }
        });
    });
    
}

function get_isi_kemasan(i) {
    var isi = $('#kemasan'+i).val();
    var new_isi = isi.split("-");
    if (new_isi[0] !== '') {
        $('#isi'+i).val(new_isi[0]);
    } else {
        $('#isi'+i).val('');
    }
}
$(function() {
    <?php if (!isset($list_data)) { ?>
    for(x = 0; x <= 0; x++) {
        add(x);
    }
    <?php } ?>
    $('#addnewrow').click(function() {
        var row = $('.tr_row').length;
        add(row);
        i++;
    });
    $('#simpan').click(function() {
        if ($('#nodoc').val() === '') {
            alert('Nomor faktur tidak boleh kosong !');
            $('#nodoc').focus();
            return false;
        }
        if ($('#jenis').val() === 'tempo') {
            if ($('#nopemesanan').val() === '') {
                alert('Nomor pemesanan tidak boleh kosong !');
                $('#nopemesanan').focus();
                return false;
            }
        }
        if ($('#ppn').val() === '') {
            alert('PPN tidak boleh kosong !');
            $('#ppn').focus();
            return false;
        }
        
        if ($('#materai').val() === '') {
            alert('Materai tidak boleh kosong !');
            $('#materai').focus();
            return false;
        }
        if ($('#jenis').val() === 'tempo') {
            if ($('#tempo').val() === '') {
                alert('Jatuh tempo tidak boleh kosong !');
                $('#tempo').focus();
                return false;
            }
        }
    })
});
function jmlSubTotal(i) {
        var dis_pr= komaKeTitik($('#diskon_pr'+i).val());
        var dis_rp= parseInt(currencyToNumber($('#diskon_rp'+i).val()));
        
        var harga = parseInt(currencyToNumber($('#harga'+i).val()));
        var jumlah= parseInt($('#jml'+i).val());
        
        //alert(harga);
        var subttl= (harga * jumlah);
        if (dis_pr !== 0 || dis_rp !== '') {
            var subttl = subttl - ((dis_pr/100)*harga)*jumlah;
            //$('#diskon_rp'+i).attr('disabled','disabled');
        }
        if (dis_rp !== '' || dis_rp !== 0) {
            var subttl = subttl - (dis_rp);
            //$('#diskon_pr'+i).attr('disabled','disabled');
        }
        $('#subttl'+i).val(parseInt(subttl));
        $('#subtotal'+i).html(numberToCurrency(parseInt(subttl)));
        hitungDetail();
}
function hitungDetail() {
        
        var jml_baris = $('.tr_row').length-1;
        var xtotal = 0;
        var xdiskon= 0;
        
        var ppn = $('#ppn').val();
        //alert(materai+' - '+ttl_ppn+' '+tagihan);
        for (i = 0; i <= jml_baris; i++) {
            //if ($('#check'+i).not(':checked')) {
                
                var jml = parseInt($('#jml'+i).val());
                var hrg = parseInt(currencyToNumber($('#harga'+i).val()));
                var total = jml*hrg;
                var xtotal = xtotal + total;
                var subttl = parseInt(currencyToNumber($('#subtotal'+i).html()));
                var xdiskon = xdiskon + subttl;
            //}
        }
        var materai = currencyToNumber(($('#materai').val()));
        var ttl_ppn = Math.ceil(xdiskon*(ppn/100));
        var tagihan = xdiskon+ttl_ppn+materai;
        
        $('#total-harga').html(numberToCurrency(Math.ceil(xtotal)));
        $('#total-diskon').html(numberToCurrency(Math.ceil(xtotal-xdiskon)));
        $('#total-pembelian').html(numberToCurrency(Math.ceil(xdiskon)));
        $('#total-ppn').html(numberToCurrency(Math.ceil(ttl_ppn)));
        $('#materai2').html(numberToCurrency(Math.ceil(materai)));
        $('#total-tagihan').html(numberToCurrency(Math.ceil(tagihan)));
        $('input[name=total_tagihan]').val(Math.ceil(tagihan));
}

</script>
<title><?= $title ?></title>
<div class="kegiatan">
    <h1><?= $title ?></h1>
    <?php if (isset($list_data)) { foreach ($list_data as $rows); } ?>
    <?= form_open('inventory/pembelian_save', 'id=form_pembelian') ?>
    <?= form_hidden('total_tagihan') ?>
    <div class="data-input">
        <fieldset><legend>Summary</legend>
            
            <div class="left_side" style="min-height: 300px;">
                <table width="100%" class="tabel-new">
                    <tr><td>No.:</td><td> <span class="label" id="id_pembelian"><?= get_last_id('pembelian', 'id') ?></span></td></tr>
                    <tr><td>Jenis Pembelian</td><td><?= form_dropdown('jenis', array('tempo' => 'Tempo', 'cash' => 'Cash', 'konsinyasi' => 'Konsinyasi'), NULL, 'id=jenis') ?></td></tr>
                    <tr id="label_sp"><td>No. SP:</td><td><?= form_input('no_dokumen', isset($rows->dokumen_no)?$rows->dokumen_no:null, 'id=nopemesanan size=20') ?></td></tr>
                    <?= form_hidden('no_pemesanan',isset($rows->id)?$rows->id:NULL) ?></td></tr>
                    <tr><td>No. Faktur:</td><td><?= form_input('nodoc', null, 'size=20 id=nodoc') ?></td></tr>
                    <tr><td>Tanggal:</td><td><?= form_input('tgldoc', date("d/m/Y"), 'size=10 class=tanggals') ?></td></tr>
                    <tr><td>Supplier:</td><td><?= form_input(null, isset($rows->id)?$rows->suplier:null, 'id=suplier') ?> <?= form_hidden('id_suplier', isset($rows->id)?$rows->suplier_relasi_instansi_id:null) ?></td></tr>
                    <tr><td>Ttd Penerimaan:</td><td>
                        <span class="label"><?= form_radio('ttd', 'Ada', TRUE, null) ?>  Ada</span>
                        <span class="label"><?= form_radio('ttd', 'Tidak', FALSE, null) ?> Tidak</span></td></tr>
                    <tr><td>PPN (%):</td><td><?= form_input('ppn', '0', 'id=ppn min=0 max=100 onblur=hitungDetail() ') ?></td></tr>
                    <tr><td>Materai (Rp.):</td><td><?= form_input('materai', '0', 'id=materai size=10 onkeyup=FormNum(this) onblur=hitungDetail() ') ?></td></tr>
                    <tr id="tgl_tempo"><td>Tgl Jatuh Tempo:</td><td><?= form_input('tempo', null, 'id=tempo class=tanggals size=10') ?></td></tr>

                    <tr><td valign="top">Keterangan:</td><td><?= form_textarea('keterangan',null, null) ?></td></tr>
                    <tr><td></td><td><?= form_button('add','Tambah Baris', 'id=addnewrow') ?></td></tr>
                </table>
            </div>
            <div class="right_side" style="min-height: 300px;">
                <table width="100%">
                    <tr><td>Total Harga:</td><td><span class="label" id="total-harga"></span></td></tr>
                    <tr><td>Total Diskon:</td><td><span class="label" id="total-diskon"></span></td></tr>
                    <tr><td>Total Pembelian:</td><td><span class="label" id="total-pembelian"></span></td></tr>
                    <tr><td>Total PPN:</td><td><span class="label" id="total-ppn"></span></td></tr>
                    <tr><td>Materai:</td><td><span class="label" id="materai2"></span></td></tr>
                    <tr><td>Total Tagihan:</td><td><span class="label" id="total-tagihan"></span></td></tr>
                </table>
            </div>
        </fieldset>
    </div>
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="5%">No</th>
                <th width="8%">No. Batch</th>
                <th width="35%">Packing Barang</th>
                <th width="8%">ED</th>
                <th width="5%">Jumlah</th>
                <th width="8%">Harga @</th>
                <th width="5%">Convert To</th>
                <th width="5%">Isi</th>
                <th width="5%">Disc(%)</th>
                <th width="7%">Disc(Rp.)</th>
                <th width="8%">SubTotal</th>
                <th width="2%">Bonus</th>
                <th width="5%">#</th>
            </tr>
            </thead>
            <tbody>
            <?php if (isset($list_data)) { 
                $no = 1;
                foreach ($list_data as $key => $data) { ?>
                <tr class="tr_row">
                    <td align="center"><?= $no ?> <input type="hidden" id="cur_hna<?= $key ?>" name="cur_hna[]" value="<?= $rows->hna ?>" /></td>
                    <td align="center"><input type="text" name="batch[]" id="batch<?= $key ?>" class="batch" /></td>
                    <td><input type=text name=pb[] id="pb<?= $key ?>" style="width: 100%" value="<?= $data->barang ?> <?= ($data->kekuatan == '1')?'':$data->kekuatan ?> <?= $data->satuan ?> <?= $data->sediaan ?> <?= (($data->generik == 'Non Generik')?'':$data->pabrik) ?> @ <?= ($data->isi=='1')?'':$data->isi ?> <?= $data->satuan_terkecil ?>" class=pb />
                    <input type=hidden name=id_pb[] id="id_pb<?= $key ?>" value="<?= $data->barang_packing_id ?>" class=id_pb />
                    <input type="hidden" name="barang_id[]" id="barang_id<?= $key ?>" value="<?= $data->barang_id ?>" class="barang_id" />
                    </td>
                    <td><input type=text name=ed[] id="ed<?= $key ?>" size=8 value="<?= datefrompg($data->ed) ?>" class=ed /></td>
                    <td><input type=text name=jml[] id="jml<?= $key ?>" size=2 value="<?= $data->masuk ?>" class=jml onblur="jmlSubTotal(<?= $key ?>);" /></td>
                    <td><input type=text name=harga[] id="harga<?= $key ?>" size=6 value="" onkeyup="FormNum(this);" onblur="jmlSubTotal(<?= $key ?>);" class=harga /></td>
                    <td>
                    <select style="border: 1px solid #ccc;" class="kemasan" name="kemasan[]" id="kemasan<?= $key ?>"><option value="">Pilih kemasan ...</option>
                        <?php $array_kemasan = $this->m_inventory->get_kemasan_by_barang($data->id_barang); 
                        foreach ($array_kemasan as $rowA) { ?>
                            <option value="<?= $rowA->isi ?>-<?= $rowA->id ?>"><?= $rowA->nama ?></option>
                        <?php } ?>
                    </select></td>
                    <td><input type=text name=isi[] readonly="readonly" id="isi<?= $key ?>" size=2 value="" class="isi" /></td>
                    <td><input type=text name=diskon_pr[] id="diskon_pr<?= $key ?>" size=2 value="<?= $data->beli_diskon_percentage ?>" class=diskon_pr onkeyup="jmlSubTotal(<?= $key ?>);" /></td>
                    <td><input type=text name=diskon_rp[] id="diskon_rp<?= $key ?>" size=6 value="<?= $data->beli_diskon_rupiah ?>" onkeyup="FormNum(this);" onblur="jmlSubTotal(<?= $key ?>);" class=diskon_rp />
                    <input type=hidden name=subtotal[] id="subttl<?= $key ?>" class="subttl" />
                    </td>
                    <td id="subtotal<?= $key ?>" align="right"></td>
                    <td align=center>-</td>
                    <td class=aksi>
                        <input type="hidden" name="status[]" value="Ya" id="status<?= $key ?>" />
                        <span class=delete onclick=eliminate(this);><?= img('assets/images/icons/delete.png') ?></span></td>
                </tr>
                <script type="text/javascript">
                    $('#ed<?= $key ?>').datepicker({
                        changeYear: true,
                        changeMonth: true
                    });
                    $('#harga<?= $key ?>').blur(function() {
                        var ppn = ($('#ppn').val()/100);
                        var nama = $('#pb<?= $key ?>').val();
                        var h_lama = parseInt($('#cur_hna<?= $key ?>').val());
                        var h_b    = parseInt(currencyToNumber($('#harga<?= $key ?>').val()));
                        var h_baru = h_b + (h_b*ppn);
                        if (h_lama > h_baru) {
                            var ok = confirm('Harga lama untuk '+nama+' = Rp. '+h_lama+' !, apakah anda akan mengubah HNA ?');
                            if (!ok) {
                                $('#status<?= $key ?>').val('Tidak');
                            }
                            $(this).focus();
                            return false;
                        }
                    });
                    $('#kemasan<?= $key ?>').change(function() {
                        var isi = $(this).attr('value');
                        var new_isi = isi.split("-");
                        if (new_isi[0] !== '') {
                            $('#isi<?= $key ?>').val(<?= $rows->isi ?>/new_isi[0]);
                        } else {
                            $('#isi<?= $key ?>').val('');
                        }
                    });
                    $('#pb<?= $key ?>').autocomplete("<?= base_url('inv_autocomplete/load_data_packing_barang') ?>",
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
                                var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terbesar = ''; var kekuatan = '';
                                if (data.isi !== '1') { var isi = '@ '+data.isi; }
                                if (data.satuan !== null) { var satuan = data.satuan; }
                                if (data.kekuatan !== null && data.kekuatan !== '0') { var kekuatan = data.kekuatan; }
                                if (data.sediaan !== null) { var sediaan = data.sediaan; }
                                if (data.pabrik !== null) { var pabrik = data.pabrik; }
                                if (data.satuan_terbesar !== null) { var satuan_terbesar = data.satuan_terbesar; }
                                var str = '<div class=result>'+data.nama+' '+kekuatan+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan+'</div>';
                                return str;
                            },
                            width: 370, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
                        }).result(
                        function(event,data,formated){
                            var sisa = data.sisa;
                            if (data.sisa == null) {
                                var sisa = 0;
                            }
                            var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terbesar = ''; var kekuatan = '';
                            if (data.isi !== '1') { var isi = '@ '+data.isi; }
                            if (data.kekuatan !== null && data.kekuatan !== '0') { var kekuatan = data.kekuatan; }
                            if (data.satuan !== null) { var satuan = data.satuan; }
                            if (data.sediaan !== null) { var sediaan = data.sediaan; }
                            if (data.pabrik !== null) { var pabrik = data.pabrik; }
                            if (data.satuan_terbesar !== null) { var satuan_terbesar = data.satuan_terbesar; }
                            $(this).val(data.nama+' '+kekuatan+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terbesar);
                            $('#id_pb<?= $key ?>').val(data.id);
                            $('#sisa<?= $key ?>').html(sisa);
                        });
                </script>
                <?php 
                $no++;
                }
            } ?>
            </tbody>
        </table><br/>
        <?= form_submit('Simpan', 'Simpan', 'id=simpan'); ?>
        <?= form_button('Reset', 'Reset', 'id=reset'); ?>
        <?= form_button('delete', 'Hapus', 'id=delete_pembelian') ?>
    </div>
    <?= form_close(); ?>
</div>
<br/>
<br/>
<br/>
<br/>