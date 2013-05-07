<?php $this->load->view('message'); ?>
<title><?= $title ?></title>
<script type="text/javascript">
    var ok;
    $('button').button();
    $('#done').button({
        icons: {
            primary: 'ui-icon-arrowthickstop-1-s'
        }
    });
    $('#reset').button({
        icons: {
            primary: 'ui-icon-refresh'
        }
    });
    $('#reset').click(function() {
        $('#loaddata').empty();
        $('#loaddata').load('<?= base_url('inventory/rencana_pemesanan') ?>');
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
    $('#form_rencana').submit(function() {
        if ($('input[name=id_suplier]').val() === '') {
            alert('Data suplier tidak boleh kosong !');
            $('#suplier').focus();
            return false;
        }
        var jumlah = $('.tr_row').length-1;
        for (i = 0; i <= jumlah; i++) {
            if (($('#jml'+i).val() === '') || ($('#jml'+i).val() === '0')) {
                alert('Jumlah pemesanan tidak boleh kosong !');
                $('#jml'+i).val('').focus();
                return false;
            }
        }
        var url = $(this).attr('action');
        $.ajax({
            type: 'POST',
            url: url,
            data: $(this).serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.status === true) {
                    $('input').attr('disabled','disabled');
                    $('button[type=submit]').hide();
                    $('button[id=print], button[id=deletion]').show();
                    $('#id_auto').html(data.id_pemesanan);
                    $('input[name=id]').val(data.id_pemesanan);
                    //$('button[id=deletion],button[id=print]').removeAttr('disabled');
                    alert_tambah();
                }
            }
        });
        return false;
    });
    $('#done').click(function() {
        $('#form_rencana').submit();
    });
    function delete_rencana(id) {
        var ok = confirm('Apakah anda yakin akan menghapus data rencana ini ?');
        if (ok) {
            $.ajax({
                url: '<?= base_url('inventory/rencana_pemesanan_delete') ?>/'+id,
                cache: false,
                dataType: 'json',
                success: function() {
                    $('#loaddata').load('<?= base_url('inventory/rencana_pemesanan') ?>');
                }
            });
        }
    }
    
    function hitungSubtotal(i) {
        //var jumlah = $('.tr_row').length-1;
        var harga_beli = currencyToNumber($('#hpp'+i).html());
        var jumlah     = $('#jml'+i).val();
        var hasil      = harga_beli*jumlah;
        $('#subtotal'+i).html(numberToCurrency(Math.ceil(hasil)));
    }
</script>
<div class="kegiatan">
    <h1><?= $title ?></h1>
    <?= form_open('inventory/save_pemesanan_defecta', 'id=form_rencana'); ?>
    <?= form_hidden('defecta','yeaah') ?>
    <div class="data-input">
        <fieldset><legend>Summary</legend>
            <div class="one_side">
            <label>No. SP:</label><span id="no_doc" class="label"><?= get_last_id('pemesanan', 'id').'/'.date("dmY") ?></span>
            <label>Waktu:</label><?= form_input('tanggal', date("d/m/Y H:i"), 'id=tanggal') ?>
            <label>Supplier:</label><?= form_input(null, null, 'id=suplier') ?>
            <?= form_hidden('id_suplier') ?>
            </div>
        </fieldset>
    </div>
    <div class="data-list">
        <table class="sortable" id="table" width="100%">
            <thead>
            <tr>
                <th width="5%" class="nosort"><h3>No.</h3></th>
                <th width="50%"><h3>Nama Barang</h3></th>
                <th width="10%">Kemasan</th>
                <th width="5%"><h3>Sisa Stok</h3></th>
                <th width="5%"><h3>Stok Min</h3></th>
                <th width="5%"><h3>Jumlah</h3></th>
                <th width="5%"><h3>Aksi</h3></th>
            </tr>
            </thead>
            <tbody>
                <?php 
                foreach ($list_data as $key => $data) { ?>
                <tr class="tr_row" id="listdata<?= $key ?>">
                    <td align="center"><?= ++$key ?></td>
                    <td><?= form_hidden('id_pb[]',$data->barang_packing_id) ?><?= $data->barang ?> <?= $data->kekuatan ?>  <?= $data->satuan ?> <?= $data->sediaan ?> <?= $data->pabrik ?><!-- @ <?= ($data->isi==1)?'':$data->isi ?> <?= $data->satuan_terkecil ?>--></td>
                    <td><?= form_hidden('barang_id[]', $data->barang_id) ?>
                    <select style="border: 1px solid #ccc;" name="kemasan[]" id="kemasan<?= $key ?>"><option value="">Pilih kemasan ...</option>
                        <?php $array_kemasan = $this->m_inventory->get_kemasan_by_barang($data->barang_id); 
                        foreach ($array_kemasan as $rowA) { ?>
                            <option value="<?= $rowA->isi ?>-<?= $rowA->id ?>"><?= $rowA->nama ?></option>
                        <?php } ?>
                    </select></td>
                    <td align="center"><?= $data->sisa ?></td>
                    <td align="center"><?= $data->stok_minimal ?></td>
                    <td><?= form_input('jml[]', $data->jumlah, 'id=jml'.$key.' size=5 onkeyup=hitungSubtotal('.$key.')') ?></td>
                    <td align="center"><span onclick="delete_rencana(<?= $data->barang_packing_id ?>);"><?= img('assets/images/icons/delete.png') ?></span></td>
                </tr>
                <?php }  ?>
            </tbody></table>
    </div>
    <?= form_close() ?>
    <?= form_button(NULL, 'Pesan Barang', 'id=done') ?>
    <?= form_button(null, 'Reset', 'id=reset') ?>
</div>