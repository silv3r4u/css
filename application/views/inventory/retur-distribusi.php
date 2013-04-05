<title><?= $title ?></title>
<?= $this->load->view('message') ?>
<div class="kegiatan">
<script type="text/javascript">
$(function() {
    $('button[id=cancel]').click(function() {
        $('#loaddata').load('<?= base_url('laporan/stok') ?>');
    })
    $('button[id=delete]').hide();
    $('input[type=submit]').each(function(){
        $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
    });
    $('button[type=submit]').button({
        icons: {
            primary: 'ui-icon-circle-check'
        }
    });
    $('button[id=cancel]').button({
        icons: {
            primary: 'ui-icon-refresh'
        }
    });
    $('button[id=delete]').button({
        icons: {
            primary: 'ui-icon-circle-close'
        }
    });
    var jml_baris = $('.tr_row').length-1;
    
    for (i = 0; i <= jml_baris; i++) {
        autocomplete_date(i);
    }
    $('#tanggal').datetimepicker();
    $('#addnewrow').click(function() {
        row = $('.tr_row').length;
        add(row);
        i++;
    });
    $('#form_retur_distribusi').submit(function() {
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            dataType: 'json',
            cache: false,
            success: function(data) {
                if (data.status == true) {
                    alert_tambah();
                    $('#transaksi_id').html(data.id_retur_distribusi);
                    $('button[type=submit]').hide();
                }
            }
        })
        return false;
    })
})
function eliminate(el) {
    var ok = confirm('Anda yakin akan menghapus data ini ?');
    if (ok) {
        var parent = el.parentNode.parentNode;
        parent.parentNode.removeChild(parent);
        var jml_baris = $('.tr_row').length;
        if (jml_baris == 0) {
            $('button[type=submit]').hide();
        }
    } else {
        return false;
    }
}
function autocomplete_date(i) {
    $('#ed'+i).datepicker({
        changeYear: true,
        changeMonth: true
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
            var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terbesar = '';
            if (data.isi != '1') { var isi = '@ '+data.isi; }
            if (data.satuan != null) { var satuan = data.satuan; }
            if (data.sediaan != null) { var sediaan = data.sediaan; }
            if (data.pabrik != null) { var pabrik = data.pabrik; }
            if (data.satuan_terbesar != null) { var satuan_terbesar = data.satuan_terbesar; }
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
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        
        if (data.isi != '1') { var isi = '@ '+data.isi; }
        if (data.satuan != null) { var satuan = data.satuan; }
        if (data.sediaan != null) { var sediaan = data.sediaan; }
        if (data.pabrik != null) { var pabrik = data.pabrik; }
        if (data.satuan_terbesar != null) { var satuan_terbesar = data.satuan_terbesar; }
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
        
    });
}
function add(i) {
     str = '<tr class=tr_row>'+
                '<td><input type=text name=nodis[] id=nodis'+i+' size=15 /></td>'+
                '<td><input type=text name=pb[] id=pb'+i+' size=60 /><input type=hidden name=id_pb[] id=id_pb'+i+' /></td>'+
                '<td><input type=text name=ed[] id=ed'+i+' size=10 /></td>'+
                '<td id=jml_dist'+i+'></td>'+
                '<td><input type=text name=jp[] id=jp'+i+' size=10 /></td>'+
                '<td class=aksi><a class=delete onclick=eliminate(this)></a></td>'+
            '</tr>';

    $('.form-inputan tbody').append(str);
    autocomplete_date(i);
}

function cek_jumlah(i) {
    //var jml_baris = $('.tr_row').length-1;
    var dist = parseInt($('#jml_dist'+i).html());
    var jp   = parseInt($('#jp'+i).val());
    if (dist < jp) {
        alert('Jumlah yang diretur tidak boleh melebihi jumlah yg terdistribusi');
        $('#jp'+i).val('').focus();
    }
}
</script>
    <h1><?= $title ?></h1>
    <?php
    foreach ($list_data as $rows);
    ?>
    <?= form_open('inventory/retur_distribusi_save', 'id=form_retur_distribusi') ?>
    <?= form_hidden('id_distribusi_penerimaan', $rows->transaksi_id) ?>
    <?= form_hidden('id_unit', $rows->id_unit) ?>
    <div class="data-input">
        <fieldset><legend>Summary</legend>
            <label>No</label><span class="label" id="transaksi_id"><?= $rows->transaksi_id ?></span>
            <label>Waktu</label><?= form_input('tanggal', date("d/m/Y H:i"), 'id=tanggal') ?>
        </fieldset>
    </div>
    <!--<?= form_button(null, 'Tambah Baris', 'id=addnewrow') ?>-->
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="30%">Packing Barang</th>
                <th width="13%">ED</th>
                <th width="10%">Jumlah Distribusi</th>
                <th width="10%">Jumlah Retur</th>
                <th width="5%">#</th>
            </tr>
            </thead>
            <tbody>
                <?php 
                foreach($list_data as $key => $data) { 
                    if ($data->id_obat == null) {
                        $packing = $data->barang.' '.$data->pabrik.' @ '.(($data->isi==1)?'':$data->isi).' '.$data->satuan_terkecil;
                    } else {
                        $packing = $data->barang .' '.(($data->kekuatan != '1')?$data->kekuatan:null).' '. $data->satuan.' '. $data->sediaan .' '.(($data->generik == 'Non Generik')?'':$data->pabrik).' '.(($data->isi==1)?'':'@ '.$data->isi).' '. $data->satuan_terkecil;
                    }   
                ?>
                <tr class=tr_row>
                    <td align="center"><?= ($key+1) ?></td>
                    <td><?= $packing ?><input type=hidden name=id_pb[] value="<?= $data->barang_packing_id ?>" id=id_pb<?= $key ?> /></td>
                    <td align="center"><?= datefmysql($data->ed) ?><input type=hidden name=ed[] id=ed<?= $key ?> value="<?= $data->ed ?>" size=10 /></td>
                    <td align="center" id=jml_dist<?= $key ?>><?= $data->masuk ?></td>
                    <td align="center"><input type=text name=jp[] id=jp<?= $key ?> value="<?= $data->masuk ?>" onkeyup="cek_jumlah(<?= $key ?>)" size=10 /></td>
                    <td class=aksi><a href="#" class=delete onclick="eliminate(this)"></a></td>
                </tr>
                    <?php }
                ?>
            </tbody>
        </table>
        <?= form_submit('save', 'Simpan', null) ?>
        <?= form_button(null, 'Cancel', 'id=cancel') ?>
        <?= form_button(null, 'Delete', 'id=delete') ?>
    </div>
    <?= form_close() ?>

</div>