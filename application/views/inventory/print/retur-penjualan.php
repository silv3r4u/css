<?php
header_excel("retur-penjualan-".date("d-m-Y").".xls");
?>
<script type="text/javascript">
function hitungRetur() {
        var jumlah = $('.tr_row').length;
        var returning = 0;
        for (i = 1; i <= jumlah; i++) {
            var hpp = parseInt(currencyToNumber($('#hpp'+i).html()));
            <?php
            if (isset($_GET['trans'])) { ?>
                var retur = parseInt($('#jml_retur'+i).html());        
            <?php } else { ?>
                var retur = parseInt($('#jml_retur'+i).val());
            <?php } ?>
            var hsl = hpp * retur;
            returning = returning + hsl;
        }
        
        $('#retur').html(numberToCurrency(returning));
   
}
$(function() {
    $('#cetakexcel').click(function() {
        location.href='<?= app_base_url('cetak/inventory/retur-penjualan') ?>?id_penjualan=<?= isset($_GET['id_penjualan'])?$_GET['id_penjualan']:NULL ?>&id_retur_penjualan=<?= isset($_GET['iid_retur_penjualan'])?$_GET['id_retur_penjualan']:NULL ?>';
    })
    hitungRetur();
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
            var str = '<div class=result>'+data.nama+'<br/>'+data.alamat+' '+data.kelurahan+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).attr('value',data.nama);
        $('#id_sales').attr('value',data.penduduk_id);
    });
})
$(function() {
    i = 2;
    
    $('#addnewrow').click(function() {
        row = $('.tr_row').length + 1;
        add(row);
        i++;
    });
});
function eliminate(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);
    var jumlah = $('.tr_row').length;
    for (i = 1; i <= jumlah; i++) {
        $('.jml_retur').attr('id','jml_retur'+i);
        $('.hpp').attr('id','hpp'+i);
    }
    hitungRetur();
    //alert(jumlah);
}
function add(i) {

    str = '<tr class=tr_row>'+
                '<td><input type=text name=nopenjualan[] id=nopenjualan'+i+' size=10 class=nopenjualan /></td>'+
                '<td><input type=text name=pb[] id=pb'+i+' size=40 class=pb /><input type=hidden name=id_pb[] id=id_pb'+i+' class=id_pb /></td>'+
                '<td id=hj'+i+'></td>'+
                '<td id=diskon'+i+'></td>'+
                '<td id=jml_penjualan'+i+'></td>'+
                '<td><input type=text name=jml_retur[] id=jml_retur'+i+' size=10 class=jml_retur /></td>'+
                '<td class=aksi><a href=# class=delete onclick=eliminate(this)></a></td>'+
            '</tr>';

    $('.form-inputan tbody').append(str);
    $("#ed"+i).datepicker({
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
            var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terbesar = '';
            if (data.isi != '1') { var isi = '@ '+data.isi; }
            if (data.satuan != null) { var satuan = data.satuan; }
            if (data.sediaan != null) { var sediaan = data.sediaan; }
            if (data.pabrik != null) { var pabrik = data.pabrik; }
            if (data.satuan_terbesar != null) { var satuan_terbesar = data.satuan_terbesar; }
            var str = '<div class=result>'+data.nama+' '+data.kekuatan+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terbesar+'</div>';
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
        if (data.isi != '1') { var isi = '@ '+data.isi; }
        if (data.satuan != null) { var satuan = data.satuan; }
        if (data.sediaan != null) { var sediaan = data.sediaan; }
        if (data.pabrik != null) { var pabrik = data.pabrik; }
        if (data.satuan_terbesar != null) { var satuan_terbesar = data.satuan_terbesar; }
        $(this).val(data.nama+' '+data.kekuatan+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terbesar);
        $('#id_pb'+i).val(data.barang_packing_id);
        $('#bc'+i).val(data.barcode);
        $('#sisa'+i).html(sisa);
    });
}
</script>


<?php
    require_once 'app/lib/common/master-data.php';
    require_once 'app/lib/common/functions.php';
    $apt = informasi_apotek();
    $rows = _select_unique_result("select * from penjualan p left join penduduk pd on (p.pembeli_penduduk_id = pd.id) where p.id = '$_GET[id_penjualan]'");
    if (isset($_GET['id_retur_penjualan'])) {
        $rowA = _select_unique_result("select * from kas where transaksi_id = '$_GET[id_retur_penjualan]' and transaksi_jenis = 'Retur Penjualan'");
        //echo "select * from kas where transaksi_id = '$_GET[id_retur_penjualan]' and transaksi_jenis = 'Retur Penjualan'";
    }
?>
<table style="border-bottom: 1px solid #000;" width="100%">
    <tr><td colspan="4" align="center" style="text-transform: uppercase">Apotek <?= $apt['nama'] ?></td> </tr>
    <tr><td colspan="4" align="center"><?= $apt['alamat'] ?> <?= $apt['kelurahan'] ?></td> </tr>
    <tr><td colspan="4" align="center">Telp. <?= $apt['telp'] ?>,  Fax. <?= $apt['fax'] ?>, Email <?= $apt['email'] ?></td> </tr>
</table>
    <?= Form('controller/inventory/retur-penjualan', 'post', null) ?>
    <?= InputHidden('id_penjualan', isset($_GET['id_penjualan'])?$_GET['id_penjualan']:NULL, null) ?>
    
        
        <table width="100%">
            <tr><td width="10%">No.</td><td><?= isset($_GET['id'])?$_GET['id']:  get_last_id('penjualan_retur', 'id') ?></td> </tr>
            <tr><td>Pembeli</td><td><?= $rows['nama'] ?></td> </tr>
        </table>
    
    <?php
    if (!isset($_GET['id_retur_penjualan'])) { ?>
    <?= Button('Tambah Baris', 'id=addnewrow') ?>
    <?php } ?>
    
    <div class="data-list">
        <table class="tabel form-inputan" width="100%" border="1">
            <thead>
            <tr>
                <th>No. Penjualan</th>
                <th>Packing Barang</th>
                <th>Harga Jual</th>
                <th>Diskon</th>
                <th>Jumlah Penjualan</th>
                <th>Jumlah Retur</th>
            </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $penjualan = retur_penjualan_muat_data($_GET['id_retur_penjualan']);
                foreach ($penjualan as $key => $rows) {
                if (isset($_GET['id_retur_penjualan'])) { 
                    $penjualan = _select_unique_result("select * from transaksi_detail where barang_packing_id = '$rows[barang_packing_id]' and transaksi_id = '$_GET[id_penjualan]' and transaksi_jenis = 'Penjualan' order by id desc limit 1");
                    //echo "select masuk from transaksi_detail where barang_packing_id = '$rows[barang_packing_id]' and transaksi_id = '$_GET[id_penjualan]' and transaksi_jenis = 'Penjualan' order by id desc limit 1";
                    ?>
                <tr class="tr_row <?= ($key%2==1)?'even':'odd' ?>">
                    <td align="center"><?= $rows['transaksi_id'] ?></td>
                    <td><?= "$rows[barang] $rows[kekuatan] $rows[satuan] $rows[sediaan] $rows[pabrik] @ ".(($rows['isi']==1)?'':$rows['isi'])." $rows[satuan_terbesar]" ?></td>
                    <td align="right"><?= rupiah($rows['hna']) ?></td>
                    <td id="hpp<?= $no ?>" class="hpp"><?= inttocur($rows['diskon']) ?></td>
                    <td align="center"><?= round($penjualan['keluar'],1) ?></td>
                    <td align="center" id="jml_retur<?= $no ?>"><?= round($rows['masuk'],1) ?></td>
                </tr>    
                <?php } else { 
                ?>
                <tr class="tr_row <?= ($key%2==1)?'even':'odd' ?>">
                    <td><?= InputText('nodoc', $rows['transaksi_id'], 'size=10 id=nopenjualan') ?></td>
                    <td><?= InputText('pb[]', "$rows[barang] $rows[kekuatan] $rows[satuan] $rows[sediaan] $rows[pabrik] @ ".(($rows['isi']==1)?'':$rows['isi'])." $rows[satuan_terbesar]", 'id=pb'.$no.' size=40') ?> <?= InputHidden('id_pb[]', $rows['barang_packing_id'], 'id=id_pb'.$no.'') ?></td>
                    <td align="right"><?= rupiah($rows['hna']) ?> <?= InputHidden('harga[]', $rows['hna'], null) ?></td>
                    <td id="hpp<?= $no ?>" align="right"><?= $rows['diskon'] ?></td>
                    <td align="center"><?= round($rows['keluar'],1) ?></td>
                    <td><?= InputText('jml_retur[]', round($rows['keluar'],1), 'size=10 id=jml_retur'.$no.' class=jml_retur onkeyup=hitungRetur()') ?></td>
                    <td align="center"><a href=# onclick="eliminate(this)"><img src="<?= app_base_url('assets/images/icons/delete.gif') ?>" /></a></td>
                </tr>
                <?php 
                
                } ?>
            <script type="text/javascript">
                $(function() {
                    $('#pb<?= $no ?>').autocomplete("<?= app_base_url('common/autocomplete?opsi=packing-barang') ?>",
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
                            var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terbesar = '';
                            if (data.isi != '1') { var isi = '@ '+data.isi; }
                            if (data.satuan != null) { var satuan = data.satuan; }
                            if (data.sediaan != null) { var sediaan = data.sediaan; }
                            if (data.pabrik != null) { var pabrik = data.pabrik; }
                            if (data.satuan_terbesar != null) { var satuan_terbesar = data.satuan_terbesar; }
                            var str = '<div class=result>'+data.nama+' '+data.kekuatan+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terbesar+'</div>';
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
                        if (data.isi != '1') { var isi = '@ '+data.isi; }
                        if (data.satuan != null) { var satuan = data.satuan; }
                        if (data.sediaan != null) { var sediaan = data.sediaan; }
                        if (data.pabrik != null) { var pabrik = data.pabrik; }
                        if (data.satuan_terbesar != null) { var satuan_terbesar = data.satuan_terbesar; }
                        $(this).val(data.nama+' '+data.kekuatan+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terbesar);
                        $('#id_pb'+i).val(data.barang_packing_id);
                        $('#bc'+i).val(data.barcode);
                        $('#sisa'+i).html(sisa);
                    });
                })
            </script>
               <?php $no++; } ?>
                
            </tbody>
        </table>
    </div>
    
</div>
<?php die; ?>