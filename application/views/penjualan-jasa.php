<?php $this->load->view('message'); ?>
<script type="text/javascript">
$(function() {
    $('button[id=reset]').button({
        icons: {
            primary: 'ui-icon-refresh'
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
    $('#bayar').keyup(function() {
        FormNum(this);
        setKembali();
        
    })
    $('#noresep').autocomplete("<?= base_url('common/autocomplete?opsi=noresep') ?>",
    {
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].id // nama field yang dicari
                };
            }
            $('#id_resep').val('');
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
        $('#id_resep').val(data.id);
        $('#pasien').html(data.pasien);
        $('#id_pasien').val(data.pasien_penduduk_id);
        $('#jasa-apt').html(numberToCurrency(data.jasa_apoteker));
        var id = data.id;
        $.ajax({
            url: '<?= base_url('inventory/penjualan-table') ?>',
            data: 'id='+id,
            cache: false,
            success: function(msg) {
                $('.form-inputan tbody').html(msg);
                $('#total-tagihan').html($('#tagihan').val());
            }
        })
    });
    
    $('#pembeli, #id_penduduk').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_pasien') ?>",
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
            var kelurahan = '';
            if (data.kelurahan!=null) { var kelurahan = data.kelurahan; }
            var str = '<div class=result>'+data.nama+' - '+data.no_rm+'<br/>'+data.alamat+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $('#pembeli').val(data.nama);
        $('input[name=id_pembeli]').val(data.no_rm);
        $('#id_penduduk').val(data.no_rm);
        var id = data.penduduk_id;
        $.ajax({
            url: '<?= base_url('inv_autocomplete/load_data_penduduk_asuransi') ?>',
            data: 'id_pembeli='+id,
            cache: false,
            success: function(msg) {
                $('#asuransi').html((msg != 'null')?msg:'-');
            }
        })
        $.ajax({
            url: '<?= base_url('inv_autocomplete/load_data_penjualan_jasa') ?>/'+data.penduduk_id,
            cache: false,
            success: function(data) {
                $('.form-inputan tbody').html(data);
            }
        })
    });
})
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
        $('.tr_row:eq('+i+')').children('td:eq(0)').children('.layanan').attr('id','layanan'+i);
        $('.tr_row:eq('+i+')').children('td:eq(0)').children('.id_layanan').attr('id','id_layanan'+i);
        $('.tr_row:eq('+i+')').children('td:eq(1)').attr('id','tarif'+i);
        $('.tr_row:eq('+i+')').children('td:eq(2)').children('.freq').attr('id','freq'+i);
        $('.tr_row:eq('+i+')').children('td:eq(3)').attr('id','subtotal'+i);
    }
    subtotal();
}

function subtotal(i) {
    var tarif = currencyToNumber($('#tarif'+i).html());
    var freq  = $('#freq'+i).val();
    var subtotal = tarif * freq;
    $('#subtotal'+i).html(numberToCurrency(subtotal));
    total_jual_jasa();
}

function total_jual_jasa() {
    var jumlah = $('.tr_row').length-1;
    var total = 0;
    for (i = 0; i <= jumlah; i++) {
        var subtotal = currencyToNumber($('#subtotal'+i).html());
        if (!isNaN(subtotal)) {
            var total = total + subtotal;
        }
    }
    $('#total, #totals').html(numberToCurrency(total));
}

function add(i) {
     str = '<tr class=tr_row>'+
                '<td><input type=text name=layanan[] id=layanan'+i+' class=layanan size=60 /><input type=hidden name=id_tarif[] id=id_tarif'+i+' class=id_tarif /></td>'+
                '<td align=right id="tarif'+i+'"></td>'+
                '<td><input type=text name=freq[] id=freq'+i+' class=freq size=10 onkeyup="subtotal('+i+')" /></td>'+
                '<td align=right id=subtotal'+i+'></td>'+
                '<td class=aksi><a class=delete onclick=eliminate(this)></a><input type=hidden name=disc[] id=disc'+i+' />'+
                '<input type=hidden name=tarif[] id=tarifs'+i+' /> <input type=hidden name=tindakan_jasa[] id=tindakan_jasa'+i+' /></td>'+
            '</tr>';

    $('.form-inputan tbody').append(str);
    $('#layanan'+i).autocomplete("<?= base_url('inv_autocomplete/get_layanan_jasa') ?>",
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
            var str = '<div class=result>'+data.nama+' - '+((data.bobot == null)?'':data.bobot)+' - '+((data.kelas == null)?'':data.bobot)+'</div>';
            return str;
        },
        width: 350, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.nama+' '+((data.bobot == null)?'':' - '+data.bobot)+' '+((data.kelas == null)?'':' - '+data.bobot));
        $('#id_tarif'+i).val(data.id_tarif);
        $('#tarif'+i).html(numberToCurrency(data.nominal));
        $('#tarifs'+i).val(data.nominal);
        $('#freq'+i).val('1');
        $('#tindakan_jasa'+i).val(data.profesi_layanan_tindakan_jasa_total);
        subtotal(i);
        var jml = $('.tr_row').length;
        //alert(jml+' - '+i)
        if (jml - i == 1) {
            add(jml);
        }
        $('#layanan'+(i+1)).focus();
    });
}

$(function() {
    $('#tanggal').datetimepicker();
    $('#reset').click(function() {
        var url = $('#form_penjualan_jasa').attr('action');
        $('#loaddata').load(url)
    })
    $('#form_penjualan_jasa').submit(function() {
        if ($('#pembeli').val() == '') {
            alert('Pasien tidak boleh kosong !');
            $('#pembeli').focus();
            return false;
        }
        var jumlah = $('.tr_row').length-1;
        for (i = 0; i <= jumlah; i++) {
            if ($('#id_tarif'+i).val() != '') {
                if ($('#freq'+i).val() == '') {
                    alert('Jumlah frequensi tidak boleh kosong !');
                    $('#freq'+i).focus();
                    return false;
                }
            }
        }
        var post = $(this).attr('action');
        $.ajax({
            type: 'POST',
            url: post,
            dataType: 'json',
            data: $(this).serialize(),
            success: function(data) {
                if (data.status == true) {
                    //$('input').attr('disabled','disabled');
                    $('button[type=submit]').hide();
                    alert_tambah();
                } else {

                }
            }
        })
        return false;
    })
    /*$('#id_penduduk').blur(function() {
        if ($('#id_penduduk').val() != '') {
            var id = $('#id_penduduk').val();
            $.ajax({
                url: '<?= base_url('inv_autocomplete/load_data_penduduk_pasien') ?>/'+id,
                dataType: 'json',
                success: function(val) {
                    if (val.id == null) {
                        alert('Data pasien tidak ditemukan !');
                        $('#id_penduduk, #pembeli, #id_pembeli').val('');
                        $('#id_penduduk').focus();
                    } else {
                        $('#pembeli').val(val.nama);
                        $('input[name=id_pembeli]').val(val.no_rm);
                    }

                }
            })
            $.ajax({
                url: '<?= base_url('inv_autocomplete/load_data_penduduk_asuransi') ?>',
                data: 'id_pembeli='+id,
                cache: false,
                success: function(msg) {
                    $('#asuransi').html((msg != 'null')?msg:'-');
                }
            })
        }
    })*/
})
</script>
<title><?= $title ?></title>
<div class="kegiatan">
    <h1><?= $title ?></h1>
    <?= form_open('pelayanan/penjualan_jasa', 'id=form_penjualan_jasa') ?>
    <div class="data-input">
        <?= form_hidden('id_pasien', null, 'id=id_pasien') ?>
        <fieldset><legend>Summary</legend>
        <?= form_hidden('total', null, 'id=total_tagihan') ?>
            <label>Waktu</label><?= form_input('tanggal', date("d/m/Y H:i"), 'id=tanggal') ?>
            <label>No. RM *</label><?= form_input('id_penduduk', isset($_GET['id'])?$rows['id']:null, 'id=id_penduduk size=10') ?></td> </tr>
            <label>Pasien</label><?= form_input('', isset($rows['nama'])?$rows['nama']:NULL, 'id=pembeli size=40') ?> <?= form_hidden('id_pembeli') ?> </td> </tr>
            <label>Produk Asuransi</label> <span id="asuransi" class="label"></span>
            <label>Total </label> <span id="total" class="label"><?= isset($rows['pasien'])?$rows['pasien']:null ?></span>
            <label></label><?= form_button(null, 'Tambah Baris', 'id=addnewrow') ?>
        </table>
        </fieldset>
    </div>
    <div class="data-list">
        <?php if (isset($_GET['id'])) { ?>
        <h3>Jasa yang pernah di terima pasien :</h3>
        <?php } ?>
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="50%">Layanan</th>
                <th width="25%">Tarif</th>
                <th width="10%">Frekuensi</th>
                <th width="10%">Sub Total</th>
                <th>#</th>
            </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_GET['msg'])) { 
                $penjualan = penjualan_jasa_muat_data($_GET['id']);
                $no = 0;
                $total = 0;
                foreach ($penjualan as $key => $data) {
                    $layanan = _select_unique_result("select * from tarif t join layanan l on (t.layanan_id = l.id) where t.id = '$data[tarif_id]'");
                    //$hjual = ($data['hna']*($data['margin']/100))+$data['hna'];
                ?>
                <tr class="<?= ($key%2==0)?'odd':'even' ?> tr_row">
                    <td><?= $layanan['nama'] ?></td>
                    <td align="right"><?= rupiah($data['tarif']) ?></td>
                    <td align="center"><?= $data['frekuensi'] ?></td>
                    <td align="right"><?= rupiah($data['tarif']*$data['frekuensi']) ?></td>
                    <td align="center">-</td>
                </tr>
                <?php $no++; 
                    $total = $total + ($data['tarif']*$data['frekuensi']);
                    } 
                } ?>
            </tbody>
            <tfoot>
                <tr class="odd">
                    <td align="right" colspan="3">Total</td>
                    <td align="right" id="totals"></td>
                    <td></td>
                </tr>
            </tfoot>
        </table> 
    </div>
    
    <?= form_submit('save', 'Simpan', 'id=save') ?>
    <?= form_button('Reset', 'Reset', 'id=reset') ?>
    
    <?= form_close() ?>
</div>