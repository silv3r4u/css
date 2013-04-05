<?php $this->load->view('message'); ?>
<script type="text/javascript">
    $(function() {
        $('input[type=reset]').each(function(){
            $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
        });
        $('button[type=reset]').button({
            icons: {
                primary: 'ui-icon-circle-check'
            }
        });
        $('button[type=reset]').click(function() {
            $('#loaddata').html('');
            var url = '<?= base_url('inventory/repackage') ?>';
            $('#loaddata').load(url+'?_'+Math.random());
        })
        $('input[type=submit]').each(function(){
            $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
        });
        $('button[type=submit]').button({
            icons: {
                primary: 'ui-icon-circle-check'
            }
        });
        $('#form_repackage').submit(function() {
            if ($('input[name=id_pb]').val() == '') {
                alert('Packing barang asal tidak boleh kosong!');
                $('#pb').focus();
                return false;
            }
            if (($('#jml_asal').val() == '') || $('#jml_asal').val() == '0') {
                alert('Jumlah asal tidak boleh kosong!');
                $('#jml_asal').val('').focus();
                return false;
            }
            if ($('input[name=id_pb_hasil]').val() == '') {
                alert('Packing barang hasil tidak boleh kosong!');
                $('#pb_hasil').focus();
                return false;
            }
            var url = $(this).attr('action');
            $.ajax({
                type: 'POST',
                url: url,
                data: $(this).serialize(),
                dataType: 'json',
                success: function(data) {
                    if (data.status == true) {
                        $('input').attr('disabled','disabled');
                        $('button[type=submit]').hide();
                        alert_tambah();
                    }
                }
            })
            return false;
        })
        $('#pb').autocomplete("<?= base_url('inv_autocomplete/load_data_packing_barang_per_ed') ?>",
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
                        var str = '<div class=result>'+data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil+'<br/>Expired: '+datefmysql(data.ed)+'</div>';
                    } else {
                        if (data.generik == 'Non Generik') {
                            var str = '<div class=result>'+data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil+'<br/>Expired: '+datefmysql(data.ed)+'</div>';
                        } else {
                            var str = '<div class=result>'+data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil+'<br/>Expired: '+datefmysql(data.ed)+'</div>';
                        }
                    }
                    return str;
                },
                width: 400, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
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
                    $(this).val(data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil+' '+datefmysql(data.ed));
                } else {
                    if (data.generik == 'Non Generik') {
                        $(this).val(data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil+' '+datefmysql(data.ed));
                    } else {
                        $(this).val(data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil+' '+datefmysql(data.ed));
                    }
                }
                $('input[name=id_pb]').val(data.id);
                $('input[name=id_brg]').val(data.id_barang);
                $('input[name=isi]').val(data.isi);
                $('#jml_asal').focus();
                $('input[name=sisa_stok]').val(data.sisa);
                //hitungHasil();
            });
            $('#pb_hasil').autocomplete("<?= base_url('inv_autocomplete/load_data_packing_barang') ?>",
            {
                extraParams: {id_barang: function() { return $("input[name=id_brg]").val(); } },
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
                            var str = '<div class=result>'+data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil+'</div>';
                        } else {
                            var str = '<div class=result>'+data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
                        }
                    }
                    return str;
                },
                width: 400, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
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
                $('input[name=hasil],input[name=isi_hasil_asli]').val(data.isi);
                $('input[name=id_pb_hasil]').val(data.id);
                $('input[name=id_brg2]').val(data.id_barang);
                hitungHasil($('input[name=isi]').val(), $('#jml_asal').val(), data.isi);
                $('#jml_hasil').focus();
            });
            
            $('#jml_asal').blur(function() {
                var isi_asal = $('input[name=isi]').val();
                var jml_asal = $('#jml_asal').val();
                var isi_hsil = $('input[name=isi_hasil_asli]').val();
                var sisa_stok= $('input[name=sisa_stok]').val();
                if (jml_asal <= 0) {
                    alert('Jumlah asal yang di masukkan minimal 1 !');
                    $('#jml_asal').val('').focus();
                    return false;
                }
                if (jml_asal > sisa_stok) {
                    alert('Jumlah stok tersisa '+sisa_stok+' !');
                    $('#jml_asal').val('').focus();
                    return false;
                }
                var hasil = (isi_asal*jml_asal) / isi_hsil;
                if (hasil != 'Infinity') {
                    $('#hasil').html(hasil);
                    $('input[name=isi_hasil]').val(hasil);
                }
                
            })
        })
        function hitungHasil(isi, asal, isi_hasil) {
            var hasil = (isi/isi_hasil) * asal;
            if (isNaN(hasil)) {
                hasil = null;
            }
            
            if ($('input[name=id_pb_hasil]').val() != '') {
                $('#hasil').html(hasil);
                $('input[name=isi_hasil]').val(hasil);
            }
        }
    </script>
<title><?= $title ?></title>
<div class="kegiatan">
    <h1><?= $title ?></h1>
    <div class="data-input">
        <?= form_open('inventory/repackage_save', 'id=form_repackage') ?>
        <fieldset><legend>Summary</legend>
            <label>No.:</label><span class="label"><?= get_last_repackage_id('transaksi_detail', 'transaksi_id', 'Repackage') ?></span>
		<label>Packing Barang Asal:</label><?= form_input('pb',NULL,'id=pb size=55') ?> <?= form_hidden('id_pb') ?> <?= form_hidden('id_brg') ?> <?= form_hidden('isi') ?>
                <label>Jumlah Asal:</label><?= form_input('jml_asal',NULL,'size=10 id=jml_asal onkeyup="Angka(this)"') ?> <?= form_hidden('sisa_stok') ?>
		<label>Packing Barang Hasil:</label><?= form_input('pb_hasil',NULL,'id=pb_hasil size=55') ?> <?= form_hidden('id_pb_hasil') ?> <?= form_hidden('id_brg2') ?> <?= form_hidden('hasil') ?>
                <label>Jumlah Hasil:</label><span class="label" id="hasil"></span>
                <label></label><?= form_hidden('isi_hasil', null) ?> <?= form_hidden('isi_hasil_asli') ?>
                <?= form_submit('submit','Simpan','id=simpan') ?> <?= form_reset('Reset','Reset','id=reset') ?>
        </fieldset>
        <?= form_close() ?>
    </div>
</div>