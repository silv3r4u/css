<link rel="stylesheet" href="<?= app_base_url('assets/css/workspace.css') ?>" />
<style>
    * { font-size: 12px; }
</style>
<script type="text/javascript">
function cetak() {
    SCETAK.innerHTML = '';
    window.print();
    if (confirm('Apakah menu print ini akan ditutup?')) {
            window.close();
    }
    SCETAK.innerHTML = '<br /><input onClick=\'cetak()\' type=\'submit\' name=\'Submit\' value=\'Cetak\' class=\'tombol\'>';
}
</script>
<script type="text/javascript">
$(function() {
    $('#cetakexcel').click(function() {
        location.href='<?= app_base_url('cetak/inventory/pembelian') ?>?id=<?= isset($_GET['id'])?$_GET['id']:NULL ?>';
    })
    $( ".tanggals" ).datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#retur').click(function() {
        var id = $('#nopemesanan').val();
        var id_pembelian = '<?= isset($_GET['id'])?$_GET['id']:NULL ?>';
        $.ajax({
            url: '<?= app_base_url('inventory/fillField') ?>',
            data: 'act=checkpembelian&id='+id,
            cache: false,
            success: function(msg) {
                if (msg) {
                    location.href='<?= app_base_url('inventory/retur-pembelian') ?>?id='+id_pembelian;
                } else {
                    alert('Nomor pembelian belum terdaftar');
                    return false;
                }
            }
        })
        //
    })
    $('#nopemesanan').autocomplete("<?= app_base_url('common/autocomplete?opsi=pemesanan') ?>",
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
            var str = '<div class=result>'+data.id+' - '+data.pabrik+'<br/>Sales: '+data.sales+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.id);
        $('#suplier').val(data.pabrik);
        $('#id_suplier').val(data.suplier_relasi_instansi_id);
        $('#sales').val(data.sales);
        $('#id_sales').val(data.salesman_penduduk_id);
        var id = data.id;
        $.ajax({
            url: '<?= app_base_url('inventory/pembelian-table') ?>',
            data: 'id='+id,
            cache: false,
            success: function(msg) {
                $('.form-inputan tbody').html(msg);
            }
        })
        //
    });
    $('#suplier').autocomplete("<?= app_base_url('common/autocomplete?opsi=supplier') ?>",
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
        $('#id_suplier').attr('value',data.id);
    });
    $('#sales').autocomplete("<?= app_base_url('common/autocomplete?opsi=sales') ?>",
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
            var str = '<div class=result>'+data.nama+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).attr('value',data.nama);
        $('#id_suplier').attr('value',data.id);
    });
})
function eliminate(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);
    var jumlah = $('.tr_row').length;
    hitungDetail()
    for (i = 1; i <= jumlah; i++) {
        $('.tr_row:eq('+i+')').children('td:eq(0)').children('.bc').attr('id','bc'+(i+1));
        $('.tr_row:eq('+i+')').children('td:eq(1)').children('.pb').attr('id','pb'+(i+1));
        $('.tr_row:eq('+i+')').children('td:eq(1)').children('.id_pb').attr('id','id_pb'+(i+1));
        $('.tr_row:eq('+i+')').children('td:eq(3)').children('.jml').attr('id','jml'+(i+1));
        $('.tr_row:eq('+i+')').children('td:eq(4)').children('.harga').attr('id','harga'+(i+1));
    }
}
function add(i) {
     str = '<tr class=tr_row>'+
        '<td><input type=text name=bc[] id=bc'+i+' size=15 class=bc /> </td>'+
        '<td><input type=text name=pb[] id=pb'+i+' size=40 class=pb />'+
        '<input type=hidden name=id_pb[] id=id_pb'+i+' class=pb /></td>'+
        '<td><input type=text name=ed[] id=ed'+i+' size=15 class=ed /></td>'+
        '<td><input type=text name=jml[] id=jml'+i+' size=10 class=jml onblur=jmlSubTotal('+i+') /></td>'+
        '<td><input type=text name=harga[] id=harga'+i+' size=10 onkeyup=FormNum(this) onblur=jmlSubTotal('+i+') class=harga /></td>'+
        '<td><input type=text name=diskon_pr[] id=diskon_pr'+i+' size=10 class=diskon_pr onkeyup=jmlSubTotal('+i+') class=diskon_pr /></td>'+
        '<td><input type=text name=diskon_rp[] id=diskon_rp'+i+' size=10 onkeyup=FormNum(this) onblur=jmlSubTotal('+i+') class=diskon_rp />'+
        '<input type=hidden name=subtotal[] id=subttl'+i+' class=subttl /></td>'+
        '<td id=subtotal'+i+' align=right></td>'+
        '<td><input type=text name=net[] id=net'+i+' size=10 class=net onKeyup=FormNum(this) /></td>'+
        '<td align=center><input type=checkbox name=bonus /></td>'+
        '<td class=aksi><a href=# class=delete onclick=eliminate(this)></a></td>'+
    '</tr>';

    $('.form-inputan tbody').append(str);
    $( ".ed" ).datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#pb'+i).autocomplete("<?= app_base_url('common/autocomplete?opsi=packing-barang') ?>",
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
            if (data.isi != '1') { var isi = '@ '+data.isi; }
            if (data.satuan != null) { var satuan = data.satuan; }
            if (data.kekuatan != null) { var kekuatan = data.kekuatan; }
            if (data.sediaan != null) { var sediaan = data.sediaan; }
            if (data.pabrik != null) { var pabrik = data.pabrik; }
            if (data.satuan_terbesar != null) { var satuan_terbesar = data.satuan_terbesar; }
            var str = '<div class=result>'+data.nama+' '+kekuatan+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terbesar = '';
        var sisa = data.sisa;
        if (data.isi != '1') { var isi = '@ '+data.isi; }
        if (data.satuan != null) { var satuan = data.satuan; }
        if (data.sediaan != null) { var sediaan = data.sediaan; }
        if (data.pabrik != null) { var pabrik = data.pabrik; }
        if (data.satuan_terbesar != null) { var satuan_terbesar = data.satuan_terbesar; }
        $(this).val(data.nama+' '+data.kekuatan+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terbesar);
        $('#id_pb'+i).val(data.id);
        $('#bc'+i).val(data.barcode);

        var id_packing = data.id;
        $.ajax({
            url: '<?= app_base_url('inventory/fillField') ?>',
            data: 'do=pemesanan&id='+id_packing,
            cache: false,
            dataType: 'json',
            success: function(msg) {
                sisa = '0';
                if (msg.row[1] != null) {
                    sisa = msg.row[1];
                }
                $('#sisa'+i).html(sisa);
            }
        })
    });
}
$(function() {
    i = 2;
    <?php if (!isset($_GET['id'])) { ?>
        for(x = 1; x <= i; x++) {
            add(x);
        }
    <?php } ?>
    $('#addnewrow').click(function() {
        row = $('.tr_row').length + 1;
        add(row);
        i++;
    });
    $('#save').click(function() {
        if ($('#nodoc').val() == '') {
            alert('Nomor dokumen tidak boleh kosong !');
            $('#nodoc').focus();
            return false;
        }
        if ($('#ppn').val() == '') {
            alert('PPN tidak boleh kosong !');
            $('#ppn').focus();
            return false;
        }
        if ($('#materai').val() == '') {
            alert('Materai tidak boleh kosong !');
            $('#materai').focus();
            return false;
        }
        if ($('#tempo').val() == '') {
            alert('Jatuh tempo tidak boleh kosong !');
            $('#tempo').focus();
            return false;
        }
    })
});
function jmlSubTotal(i) {
        
        var dis_pr= komaKeTitik($('#diskon_pr'+i).val());
        var dis_rp= parseInt(currencyToNumber($('#diskon_rp'+i).val()));
        
        var harga = parseInt(currencyToNumber($('#harga'+i).val()));
        var jumlah= parseInt($('#jml'+i).val());
        //$('#diskon_rp'+i).removeAttr('disabled');
        //$('#diskon_pr'+i).removeAttr('disabled');
        var subttl= (harga * jumlah);
        if (dis_pr != 0 || dis_rp != '') {
            var subttl = subttl - ((dis_pr/100)*harga)*jumlah;
            //$('#diskon_rp'+i).attr('disabled','disabled');
        }
        if (dis_rp != '' || dis_rp != 0) {
            var subttl = subttl - (dis_rp * jumlah);
            //$('#diskon_pr'+i).attr('disabled','disabled');
        }
        $('#subttl'+i).val(subttl);
        $('#subtotal'+i).html(numberToCurrency(subttl));
}
function hitungDetail() {
    $(function() {
        var jml_baris = $('.tr_row').length;
        var xtotal = 0;
        var xdiskon= 0;
        var ppn = $('#ppn').val();
        alert(jml_baris)
        for (i = 1; i <= jml_baris; i++) {
            var jml = parseInt($('#jml'+i).val());
            var hrg = parseInt(currencyToNumber($('#harga'+i).val()));
            var total = jml*hrg;
            var xtotal = xtotal + total;
            var subttl = parseInt(currencyToNumber($('#subtotal'+i).html()));
            var xdiskon = xdiskon + subttl;
        }
        var materai = currencyToNumber(($('#materai').val()));
        var ttl_ppn = xdiskon*(ppn/100);
        var tagihan = xdiskon+ttl_ppn+materai;
        //alert(tagihan);
        $('#total-harga').html(numberToCurrency(xtotal));
        $('#total-diskon').html(numberToCurrency(xtotal-xdiskon));
        $('#total-pembelian').html(numberToCurrency(xdiskon));
        $('#total-ppn').html(numberToCurrency(ttl_ppn));
        $('#materai2').html(numberToCurrency(materai));
        $('#total-tagihan').html(numberToCurrency(tagihan));
    })
}
</script>

    <?php
    require 'app/lib/common/master-data.php';
    require 'app/actions/common/messaging.php';
    echo isset($msg)?$msg:null;
    if (isset($_GET['id'])) {
        $array = pembelian_data_muat_data($_GET['id']);
        $data = info_pembelian_muat_data($_GET['id']);
        $tagihan = $data['subtotal']+$data['total_ppn']+$data['materai'];
        foreach ($array as $rows);
    }
    ?>
    <?= Form('controller/inventory/pembelian', 'post', null) ?>
    
    
    <table>
        <tr><td>No</td><td><?= isset($_GET['id'])?$_GET['id']:get_last_id('pembelian', 'id') ?></td> </tr>
        <tr><td>No. Dokumen</td><td><?= $rows['dokumen_no'] ?></td> </tr>
        <tr><td>Tanggal Dokumen</td><td><?= date("d/m/Y") ?></td> </tr>
        <tr><td>No. Pemesanan</td><td><?= $rows['pemesanan_id'] ?></td> </tr>
        <tr><td>Supplier</td><td><?= $rows['suplier'] ?></td> </tr>
        <tr><td>Salesman</td><td><?= $rows['salesman'] ?></td> </tr>
        <tr><td>Tandatangan Penerimaan</td><td><?= $rows['ada_penerima_ttd'] ?>
           </td> </tr>
        <tr><td>PPN (%)</td><td><?= $rows['ppn'] ?></td> </tr>
        <tr><td>Materai (Rp.)</td><td><?= $rows['materai'] ?></td> </tr>
        <tr><td>Tanggal Jatuh Tempo</td><td><?= $rows['tanggal_jatuh_tempo'] ?>
            </td> </tr>
        <tr><td>Keterangan</td><td><?= $rows['keterangan'] ?></td> </tr>
        
    </table>
    
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="10%">Barcode</th>
                <th width="25%">Packing Barang</th>
                <th width="11%">ED</th>
                <th width="5%">Jumlah</th>
                <th width="10%">Harga @</th>
                <th width="5%">Diskon (%)</th>
                <th width="10%">Diskon (Rp.)</th>
                <th width="10%">SubTotal</th>
                <th width="10%">HET</th>
                <th width="2%">Bonus</th>
                <th width="5%">#</th>
            </tr>
            </thead>
            <tbody>

            <?php
            if (isset($_GET['id'])) {
            foreach($array as $key => $data) { ?>
                <tr class="<?= ($key%2==0)?'odd':'even' ?>">
                    <td><?= $data['barcode'] ?></td>
                    <td style="white-space: nowrap"><?= $data['barang'] ?> <?= $data['kekuatan'] ?>  <?= $data['satuan'] ?> <?= $data['sediaan'] ?> <?= $data['pabrik'] ?> @ <?= ($data['isi']==1)?'':$data['isi'] ?> <?= $data['satuan_terkecil'] ?></td>
                    <td align="center"><?= datefmysql($data['ed']) ?></td>
                    <td><?= round($data['masuk']) ?></td>
                    <td align="right"><?= inttocur($data['harga']) ?></td>
                    <td align="center"><?= $data['beli_diskon_percentage'] ?></td>
                    <td align="right"><?= inttocur($data['harga']*($data['beli_diskon_percentage']/100)) ?></td>
                    <td align="right"><?= inttocur($data['subtotal']) ?></td>
                    <td align="right"><?= inttocur($data['het']) ?></td>
                    <td>-</td>
                    <td></td>
                </tr>
            <?php }
            }
            ?>
            </tbody>
        </table><br/>
        <table class="info" width="200px">
            <tr><td>Total Harga</td><td id="total-harga"><?= isset($data['total_harga'])?rupiah($data['total_harga']):null ?></td></tr>
            <tr><td>Total Diskon</td><td id="total-diskon"><?= isset($data['diskon'])?rupiah($data['diskon']):null ?></td></tr>
            <tr><td>Total Pembelian</td><td id="total-pembelian"><?= isset($data['subtotal'])?rupiah($data['subtotal']):null ?></td></tr>
            <tr><td>Total PPN</td><td id="total-ppn"><?= isset($data['total_ppn'])?rupiah($data['total_ppn']):null ?></td></tr>
            <tr><td>Materai</td><td id="materai2"><?= isset($data['materai'])?rupiah($data['materai']):null ?></td></tr>
            <tr><td>Total Tagihan</td><td id="total-tagihan"><?= rupiah(isset($tagihan)?$tagihan:null) ?></td></tr>
        </table>
        <p align="center">
<span id="SCETAK"><input type="button" class="tombol" value="Cetak" onClick="cetak()"/></span>
</p>
<?php die ?>