<title><?= $title ?></title>
<div class="kegiatan">
<?php $this->load->view('message'); ?>
<script type="text/javascript">
$(function() {
    $('#reset').click(function() {
        $('#loaddata').html('');
        var url = '<?= base_url('inventory/reretur_pembelian') ?>';
        $('#loaddata').load(url);
    })
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
    $('#cetakexcel').click(function() {
        location.href='<?= base_url('cetak/inventory/pembelian') ?>?id=<?= isset($_GET['id'])?$_GET['id']:NULL ?>';
    })
    $('#printhasil').click(function() {
        window.open('<?= base_url('cetak/inventory/pembelian') ?>?id=<?= isset($_GET['id'])?$_GET['id']:NULL ?>','mywindow','location=1,status=1,scrollbars=1,width=900px,height=400px');
    })
    $( ".tanggals" ).datepicker({
        changeYear: true,
        changeMonth: true
    });
    
    $('#noretur').autocomplete("<?= base_url('inv_autocomplete/reretur_pembelian_load_id') ?>",
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
            var str = '<div class=result>'+data.id+' - '+data.suplier+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.id);
        $('#suplier').html(data.suplier);
        $('#salesman').html(data.salesman);
        var id = data.id;
        $.ajax({
            url: '<?= base_url('inv_autocomplete/reretur_pembelian_load_data') ?>',
            data: 'id='+id,
            cache: false,
            success: function(msg) {
                $('.form-inputan tbody').html(msg);
            }
        });
    });
});
function eliminate(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);
    var jumlah = $('.tr_row').length;
    hitungDetail();
    for (i = 0; i <= jumlah; i++) {
        $('.tr_row:eq('+i+')').children('td:eq(0)').children('.pb').attr('id','pb'+(i+1));
        $('.tr_row:eq('+i+')').children('td:eq(0)').children('.id_pb').attr('id','id_pb'+(i+1));
        $('.tr_row:eq('+i+')').children('td:eq(2)').children('.jml').attr('id','jml'+(i+1));
        $('.tr_row:eq('+i+')').children('td:eq(3)').children('.harga').attr('id','harga'+(i+1));
    }
}

function bonusthis(i) {
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
        '<td><input type=text name=pb[] id=pb'+i+' size=65 class=pb />'+
        '<input type=hidden name=id_pb[] id=id_pb'+i+' class=pb /></td>'+
        '<td><input type=text name=hpp[] id=hpp'+i+' size=10 class=hpp /></td>'+
        '<td><input type=text name=ed[] id=ed'+i+' size=10 class=ed /></td>'+
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
            if (data.isi !== '1') { var isi = '@ '+data.isi; }
            if (data.kekuatan != null && data.kekuatan != '0') { var kekuatan = data.kekuatan; }
            if (data.satuan != null) { var satuan = data.satuan; }
            if (data.sediaan != null) { var sediaan = data.sediaan; }
            if (data.pabrik != null) { var pabrik = data.pabrik; }
            if (data.satuan_terkecil != null) { var satuan_terkecil = data.satuan_terkecil; }
            if (data.id_obat === null) {
                var str = '<div class=result>'+data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
            } else {
                if (data.generik === 'Non Generik') {
                    var str = '<div class=result>'+data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil+'</div>';
                } else {
                    var str = '<div class=result>'+data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
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
        })
    });
}
$(function() {
    $('#tanggal').datetimepicker();
    for(x = 0; x <= 1; x++) {
        add(x);
    }
    $('#addnewrow').click(function() {
        row = $('.tr_row').length;
        add(row);
    });
    $('#save').click(function() {
        if ($('#barang').is(':checked') == false && $('#uang').is(':checked') == false) {
            alert('Pilih pengembalian, barang atau uang !');
            $('#barang').focus();
            return false;
        }
    })
    $('#form_reretur_pembelian').submit(function() {
        var url = $(this).attr('action');
        $.ajax({
            type: 'POST',
            url: url,
            data: $(this).serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.status == true) {
                    $('button[type=submit]').hide();
                    $('#id_auto').html(data.id_pemesanan);
                    $('input[name=id]').val(data.id_pemesanan);
                    $('input').attr('disabled','disabled');
                    //$('button[id=deletion],button[id=print]').removeAttr('disabled');
                    alert_tambah();
                } else {
                    
                }
            }
        })
        return false;
    })
});


</script>
    <h1><?= $title ?></h1>
    <?= form_open('inventory/reretur_pembelian_save', 'id=form_reretur_pembelian') ?>
    <div class="data-input">
        
    <?= form_hidden('total') ?>
        <fieldset><legend>Summary</legend>
            <div class="one_side">
        <label>Waktu:</label><?= form_input('tanggal', date("d/m/Y H:i"), 'id=tanggal') ?>
        <label>No. Retur:</label></label><?= form_input('noretur', isset($_GET['id'])?$_GET['id']:null, 'id=noretur size=40') ?>
        <label>Suplier:</label><span id="suplier" class="label"><?= isset($_GET['id'])?$data[0]['suplier']:null ?></span>
        <label>Total:</label><span class="label" id="returan"><?= isset($kas)?rupiah($kas['penerimaan']):null ?></span>
        <label></label>
            <span class="label"><?= form_radio('berupa', 'barang', TRUE, 'id=barang') ?> Barang</span> 
            <span class="label"><?= form_radio('berupa', 'uang', FALSE, 'id=uang') ?> Uang</span>
            <label></label><?= form_button(null, 'Tambah Baris', 'id=addnewrow') ?>
        </div>
        </fieldset>
    </div>
    <div class="data-list">
        <?php if ((isset($cek) and $cek[0]['uang'] == 0) or (!isset($_GET['id']))) { ?>
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="34%">Packing Barang</th>
                <th width="11%">HPP</th>
                <th width="11%">ED</th>
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
                $rowA = _select_unique_result("select keluar from transaksi_detail where transaksi_id = '$data[id_retur_pembelian]' and transaksi_jenis = 'Retur Pembelian' and barang_packing_id = '$data[barang_packing_id]'");
                if ($data['id_obat'] == null) {
                    $packing = "$data[barang] $data[suplier] @  ".(($data['isi']==1)?'':$data['isi'])." $data[satuan_terkecil]";
                } else {
                    $packing = "$data[barang] ".(($data['kekuatan'] == '1')?'':$data['kekuatan'])." $data[satuan] $data[sediaan] ".(($data['generik'] == 'Non Generik')?'':$data['suplier'])." @ ".(($data['isi']==1)?'':$data['isi'])." $data[satuan_terkecil]";
                }
                ?>
                <tr class="<?= ($key%2==0)?'odd':'even' ?>">
                    <td style="white-space: nowrap"><?= $packing ?></td>
                    <td align="right"><?= rupiah($data['hpp']) ?></td>
                    <td align="center"><?= datefmysql($data['ed']) ?></td>
                    <td align="center"><?= $rowA['keluar'] ?></td>
                    <td align="center"><?= $data['masuk'] ?></td>
                    <td>-</td>
                </tr>
            <?php $total = $total + ($data['masuk']*$data['hpp']); 
                } ?>
            <script type="text/javascript">
                $(function() {
                    //alert(<?= $total ?>);
                    $('#returan').html(numberToCurrency(<?= $total ?>));
                    $('input[name=total]').val(<?= $total ?>);
                })
            </script>
            <?php } 
            ?>
            </tbody>
        </table><br/>
        
        <?php
        } else { 
            
            ?>
            <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="25%">Packing Barang</th>
                <th width="11%">HPP</th>
                <th width="11%">ED</th>
                <th width="5%">Jumlah Retur</th>
                <th width="11%">Jumlah</th>
                <th width="5%">#</th>
            </tr>
            </thead>
            <tbody>

            <?php
            if (isset($_GET['id'])) {
            $array = retur_pembelian_muat_data($data[0]['retur_id']);
            $total = 0;
            foreach($array as $key => $data) {
                //$rowA = _select_unique_result("select keluar from transaksi_detail where transaksi_id = '$data[id_retur_pembelian]' and transaksi_jenis = 'Retur Pembelian' and barang_packing_id = '$data[barang_packing_id]'");
                if ($data['id_obat'] == null) {
                    $packing = "$data[barang] $data[suplier] @  ".(($data['isi']==1)?'':$data['isi'])." $data[satuan_terkecil]";
                } else {
                    
                    $packing = "$data[barang] $data[kekuatan] $data[satuan] $data[sediaan] $data[suplier] @ ".(($data['isi']==1)?'':$data['isi'])." $data[satuan_terkecil]";
                }
                ?>
                <tr class="<?= ($key%2==0)?'odd':'even' ?>">
                    <td style="white-space: nowrap"><?= $packing ?></td>
                    <td align="right"><?= rupiah($data['hpp']) ?></td>
                    <td align="center"><?= datefmysql($data['ed']) ?></td>
                    <td align="center"><?= $data['masuk'] ?></td>
                    <td align="center"><?= $data['masuk'] ?></td>
                    <td>-</td>
                </tr>
            <?php $total = $total + ($data['masuk']*$data['hpp']); 
                } ?>
            <script type="text/javascript">
                $(function() {
                    $('#returan').html(numberToCurrency(<?= $total ?>));
                    $('input[name=total]').val(<?= $total ?>);
                })
            </script>
            <?php } 
            ?>
            </tbody>
        </table><br/> <?php
        } ?>
        <?= form_submit('save', 'Simpan', 'id=save') ?>
        <?= form_button(null, 'Reset', 'id=reset') ?>
    </div>
    <?= form_close(); ?>
</div>