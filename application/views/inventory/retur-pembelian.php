<title><?= $title ?></title>
<div class="kegiatan">
    <?php $this->load->view('message'); ?>
<script type="text/javascript">
function hitungRetur() {
    
    var jumlah = $('.tr_row').length-1;
    var returning = 0;
    for (i = 0; i <= jumlah; i++) {
        var hpp = parseInt(currencyToNumber($('#hpp'+i).html()));
        var retur = parseInt($('#jml_retur'+i).val());
        var hsl = hpp * retur;
        returning = returning + hsl;
        //alert(hpp+' - '+retur+' - '+hsl);
    }
    
    $('#retur').html(numberToCurrency(returning));
}
$(function() {
    hitungRetur();
    $('#tanggal').datetimepicker();
    $('#reset').click(function() {
        $('#loaddata').html('');
        var url = '<?= base_url('laporan/stok') ?>';
        $('#loaddata').load(url);
    });
    $('#form_retur_pembelian').submit(function() {
        
        var url = $(this).attr('action');
        $.ajax({
            type: 'POST',
            url: url,
            data: $(this).serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.status == true) {
                    $('button[type=submit]').hide();
                    $('#id_retur_pembelian').html(data.id_retur_pembelian);
                    $('input[name=id]').val(data.id_retur_pembelian);
                    //$('button[id=deletion],button[id=print]').removeAttr('disabled');
                    alert_tambah();
                } else {
                    
                }
            }
        })
        return false;
    })
    $('button[id=reset]').click(function() {
        $('button[type=submit]').show();
    })
    $('button[id=reset]').button({
        icons: {
            primary: 'ui-icon-refresh'
        }
    });
    $('input[type=submit]').each(function() {
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
    $('#cetakexcel').click(function() {
        location.href='<?= base_url('cetak/inventory/retur-pembelian') ?>?id=<?= isset($_GET['id'])?$_GET['id']:NULL ?>&id_pembelian=<?= isset($_GET['id_pembelian'])?$_GET['id_pembelian']:NULL ?>';
    })
    
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
        $(this).val(data.nama);
        $('input[name=id_sales]').val(data.penduduk_id);
    });
})
function eliminate(el) {
    var confirmasi=confirm('Anda yakin akan menghapus data ini?');
    if (confirmasi) {
        var parent = el.parentNode.parentNode;
        parent.parentNode.removeChild(parent);
        var jml = $('.tr_row').length-1;
        for (j = 0; j <= jml; j++) {
            $('.tr_row:eq('+j+')').children('td:eq(3)').attr('id','hpp'+j);
            $('.tr_row:eq('+j+')').children('td:eq(5)').children('.jml_retur').attr('id','jml_retur'+j);
        }
        hitungRetur();
    } else {
        return false;
    }
}
</script>
<h1><?= $title ?></h1>
<?php
    
    foreach ($list_data as $data);
    //$retur = _select_unique_result("select sum(hpp) as total_retur from transaksi_detail where transaksi_jenis = 'Pembelian' and transaksi_id = '$_GET[id]'");
?>
    <?= form_open('inventory/retur_pembelian_save/'.$data->id_pembelian, 'id=form_retur_pembelian') ?>
    <?= form_hidden('id_pembelian', $data->id_pembelian) ?>
<div class="data-input">
    <fieldset><legend>Summary</legend>
        
        <label>No.:</label><span id="id_retur_pembelian" class="label"><?= get_last_id('pembelian_retur', 'id') ?></span>
        <label>Waktu:</label><?= form_input('tanggal', date("d/m/Y H:i"), 'id=tanggal') ?>
        <label>Suplier:</label><span class="label"><?= $data->suplier ?> <?= form_hidden('id_suplier', isset($data->id_suplier)?$data->id_suplier:NULL) ?></span>
<!--        <label>Salesman:</label><span class="label"><?= $data->salesman ?> <?= form_hidden('id_sales', $data->id_sales) ?></span>-->
        <label>Total Retur (Rp.):</label><span id="retur" class="label"></span>
    </fieldset>    
</div>
    <!--<?= form_button(null, 'Tambah Baris', 'id=addnewrow') ?>-->
    
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <?php if (!isset($_GET['id_pembelian)'])) { ?>
                <th>No. Faktur</th>
                <?php } ?>
                <th>Packing Barang</th>
                <th>ED</th>
                <th>HPP</th>
                <th>Jml Pembelian</th>
                <th>Jml Retur</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
                <?php
                foreach ($list_data as $key => $rows) { ?>
                
                <tr class="tr_row <?= ($key%2==1)?'even':'odd' ?>">
                    <td><?= form_input('nodoc', $rows->dokumen_no, 'size=15') ?></td>
                    <td><?= form_input('pb[]', $rows->barang." ".(($rows->kekuatan == 1)?'':$rows->kekuatan)." ". $rows->satuan." ". $rows->sediaan." ".(($rows->generik == 'Non Generik')?'':$rows->pabrik)." @ ".(($rows->isi==1)?'':$rows->isi)." ".$rows->satuan_terkecil, 'id=pb'.$key.' size=60') ?> 
                        <?= form_hidden('id_pb[]', $rows->barang_packing_id) ?></td>
                    <td align="center"><?= datefmysql($rows->ed) ?> <?= form_hidden('ed[]',$rows->ed) ?></td>
                    <td id="hpp<?= $key ?>" class="hpp" align="right"><?= rupiah($rows->hpp) ?></td>
                    <td align="center"><?= $rows->masuk ?></td>
                    <td><?= form_input('jml_retur[]', $rows->masuk, 'id=jml_retur'.$key.' class=jml_retur size=5 onkeyup=hitungRetur()') ?></td>
                    <td align="center" class="aksi"><span onclick="eliminate(this)" class="delete"><?= img('assets/images/icons/delete.png') ?></span></td>
                </tr>
            <script type="text/javascript">
                
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
                            var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = '';
                            if (data.isi != '1') { var isi = '@ '+data.isi; }
                            if (data.satuan != null) { var satuan = data.satuan; }
                            if (data.sediaan != null) { var sediaan = data.sediaan; }
                            if (data.pabrik != null) { var pabrik = data.pabrik; }
                            if (data.satuan_terkecil != null) { var satuan_terkecil = data.satuan_terkecil; }
                            var str = '<div class=result>'+data.nama+' '+data.kekuatan+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
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
                        if (data.satuan_terkecil != null) { var satuan_terkecil = data.satuan_terkecil; }
                        $(this).val(data.nama+' '+data.kekuatan+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil);
                        $('input[name=id_pb]').val(data.barang_packing_id);
                        $('#bc<?= $key ?>').val(data.barcode);
                        $('#sisa<?= $key ?>').html(sisa);
                    });
                
            </script>
               <?php } ?>
                
            </tbody>
        </table>
    </div>
    
    <?= form_submit('save', 'Simpan', null) ?>
    <?= form_button('Reset','Informasi Stok', 'id=reset') ?>
    <?= form_close() ?>
</div>