<?php $this->load->view('message'); ?>
<script type="text/javascript">
$(function() {
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
    $('#reset').click(function() {
        $('#loaddata').html('');
        $('#loaddata').load('<?= base_url('inventory/reretur_penjualan') ?>');
    });
    $('#cetakexcel').click(function() {
        location.href='<?= base_url('cetak/inventory/pembelian') ?>?id=<?= isset($_GET['id'])?$_GET['id']:NULL ?>';
    });
    $('#printhasil').click(function() {
        window.open('<?= base_url('cetak/inventory/pembelian') ?>?id=<?= isset($_GET['id'])?$_GET['id']:NULL ?>','mywindow','location=1,status=1,scrollbars=1,width=900px,height=400px');
    });
    $( ".tanggals" ).datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#returan').click(function() {
        
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
    $('#noretur').autocomplete("<?= base_url('inv_autocomplete/reretur_penjualan_get_nomor') ?>",
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
            var str = '<div class=result>'+data.id+' - '+((data.pembeli == null)?'Penjualan Bebas':data.pembeli)+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.id);
        $('#pembeli').html(data.pembeli);
        $('#pegawai').html(data.pegawai);
        var id = data.id;
        $.ajax({
            url: '<?= base_url('inv_autocomplete/reretur_penjualan_table') ?>',
            data: 'id='+id,
            cache: false,
            success: function(msg) {
                $('.form-inputan tbody').html(msg);
                totalreretur();
            }
        })
         
   });
    
})
function eliminate(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);
    var jumlah = $('.tr_row').length;
    hitungDetail()
    for (i = 0; i <= jumlah; i++) {
        $('.tr_row:eq('+i+')').children('td:eq(0)').children('.bc').attr('id','bc'+(i+1));
        $('.tr_row:eq('+i+')').children('td:eq(1)').children('.pb').attr('id','pb'+(i+1));
        $('.tr_row:eq('+i+')').children('td:eq(1)').children('.id_pb').attr('id','id_pb'+(i+1));
        $('.tr_row:eq('+i+')').children('td:eq(3)').children('.jml').attr('id','jml'+(i+1));
        $('.tr_row:eq('+i+')').children('td:eq(4)').children('.harga').attr('id','harga'+(i+1));
    }
}

function bonusthis(i) {
    if ($('#check'+i).is(':checked')) {
        $('#diskon_pr'+i).attr('value','100');
        jmlSubTotal(i)
        hitungDetail();
    } else {
        $('#diskon_pr'+i).attr('value','0');
        jmlSubTotal(i)
        hitungDetail();
    }
}

function add(i) {
     str = '<tr class=tr_row>'+
        '<td><input type=text name=pb[] id=pb'+i+' size=65 class=pb />'+
        '<input type=hidden name=id_pb[] id=id_pb'+i+' class=pb /></td>'+
        '<td id=harga_jual'+i+'></td>'+
        '<td id=jml_retur'+i+'></td>'+
        '<td><input type=text name=jml[] id=jml'+i+' size=15 class=jml /></td>'+
        '<td class=aksi><span class=delete onclick=eliminate(this)><?= img('assets/images/icons/delete.png') ?></span></td>'+
    '</tr>';

    $('.form-inputan tbody').append(str);
    $( ".ed" ).datepicker({
        changeYear: true,
        changeMonth: true
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
                    var str = '<div class=result>'+data.nama+' '+kekuatan+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil+'</div>';
                } else {
                    if (data.generik == 'Non Generik') {
                        var str = '<div class=result>'+data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil+'</div>';
                    } else {
                        var str = '<div class=result>'+data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
                    }
                }
                
            }
            return str;
        },
        width: 370, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
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
                $('#sisa'+i).html(sisa);
            }
        });
    });
}
$(function() {
    $('#tanggal').datetimepicker();
    $('#form_reretur_penjualan').submit(function() {
        var post = $(this).attr('action');
        $.ajax({
            type: 'POST',
            url: post,
            data: $(this).serialize(),
            dataType:'json',
            success: function(data) {
                if (data.status === true) {
                    $('button[type=submit]').hide();
                    $('input').attr('disabled','disabled');
                    //$('button[id=deletion],button[id=print]').removeAttr('disabled');
                    alert_tambah();
                }
            }
        });
        return false;
    });
    for(x = 0; x <= 1; x++) {
        add(x);
    }
    $('#addnewrow').click(function() {
        row = $('.tr_row').length;
        //alert(row)
        add(row);
        i++;
    });
});


</script>
<title><?= $title ?></title>
<div class="kegiatan">
    <h1><?= $title ?></h1>
    <?= form_open('inventory/reretur_penjualan_save', 'id=form_reretur_penjualan') ?>
    <div class="data-input">
        <fieldset><legend>Summary</legend>
            <div class="left_side">
        <?= form_hidden('totalreretur') ?>
        <label>Waktu:</label><?= form_input('tanggal', date("d/m/Y H:i"), 'id=tanggal') ?>
        <label>No. Retur:</label><?= form_input('noretur', isset($_GET['id'])?$_GET['id']:null, 'id=noretur size=40') ?>
        <label>Pembeli:</label><span id="pembeli" class="label"><?= isset($_GET['id'])?$data[0]['pembeli']:null ?></span>
        <label>Pegawai:</label><span id="pegawai" class="label"><?= isset($_GET['id'])?$data[0]['pegawai']:null ?></span>
        <label>Total:</label><span class="label" id="returan"></span>
        <label></label><span class="label"><?= form_radio('berupa', 'barang', TRUE) ?> Barang</span> <span class="label"><?= form_radio('berupa', 'uang', FALSE) ?> Uang</span>
            <label></label><?= form_button(null, 'Tambah Baris', 'id=addnewrow') ?>
        </table>
        </div>
        </fieldset>
    </div>
    <div class="data-list">
        <?php if ((isset($cek) and $cek[0]['uang'] == 0) or (!isset($_GET['id']))) { ?>
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="25%">Packing Barang</th>
                <th width="11%">Harga Jual</th>
                <th width="5%">Jumlah Retur</th>
                <th width="11%">Jumlah</th>
                <th width="5%">#</th>
            </tr>
            </thead>
            <tbody>

            <?php
            if (isset($_GET['id'])) {
            $total = 0;
            foreach($array as $key => $data) { 
                $harga_jual = $data['hna']+($data['hna']*($data['margin']/100) - ($data['hna']*($data['diskon']/100)));
                $rowA = _select_unique_result("select masuk from transaksi_detail where transaksi_id = '$data[penjualan_retur_id]' and transaksi_jenis = 'Retur Penjualan' and barang_packing_id = '$data[barang_packing_id]'");
                if ($data['id_obat'] == null) {
                    $packing = "$data[barang] $data[suplier] @  ".(($data['isi']==1)?'':$data['isi'])." $data[satuan_terkecil]";
                } else {
                    $packing = "$data[barang] ".(($data['kekuatan'] == '1')?'':$data['kekuatan'])." $data[satuan] $data[sediaan] $data[suplier] @ ".(($data['isi']==1)?'':$data['isi'])." $data[satuan_terkecil]";
                }
                ?>
                <tr class="<?= ($key%2==0)?'odd':'even' ?>">
                    <td><?= $data['barcode'] ?></td>
                    <td style="white-space: nowrap"><?= $packing ?></td>
                    <td align="right"><?= rupiah($harga_jual) ?></td>
                    <td align="center"><?= $rowA['masuk'] ?></td>
                    <td align="center"><?= $data['keluar'] ?></td>
                    <td>-</td>
                </tr>
            <?php 
            $total = $total + ($data['keluar']*$harga_jual);
                } ?>
            <script type="text/javascript">
                $(function() {
                    $('#returan').html(numberToCurrency(<?= $total ?>));
                })
            </script>
            <?php }
            ?>
            </tbody>
        </table><br/>
        <?php
        } ?>
        <?= form_submit('save', 'Simpan', 'id=save'); ?>
        <?= form_button(null, 'Reset', 'id=reset') ?>
    </div>
    <?= form_close(); ?>
</div>