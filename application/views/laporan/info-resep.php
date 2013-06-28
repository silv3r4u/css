<script type="text/javascript">
    $(function() {
        $('#reset').click(function() {
            var url = '<?= base_url('laporan/resep') ?>';
            $('#loaddata').load(url);
        })
        $('button[type=pmr_open], #csr').each(function(){
            $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'" onclick="'+$(this).attr('onclick')+'">' + $(this).val() + '</button>');
        });
        $('button[type=pmr_open], #csr').button({
            icons: {
                secondary: 'ui-icon-print'
            }
        });
        $('button[id=reset]').button({
            icons: {
                primary: 'ui-icon-refresh'
            }
        });
        $('button[id=pmr_open]').button({
            icons: {
                primary: 'ui-icon-print'
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
        $('.tanggal').datepicker({
            changeYear: true,
            changeMonth: true
        })
        
        $('#csr').click(function() {
            var awal = $('#awal').val();
            var akhir= $('#akhir').val();
            var hambatan = $('#hambatan').val();
            var url = '<?= base_url('laporan/statistika_resep') ?>?awal='+awal+'&akhir='+akhir;
            $.get(url, function(data) {
                $('#result_detail').html(data);
                $('#result_detail').dialog({
                    autoOpen: true,
                    height: 500,
                    width: 900,
                    modal: true
                })
            });
            return false;
            //window.open('<?= base_url('laporan/statistika_resep') ?>?awal='+awal+'&akhir='+akhir+'&hambatan='+hambatan,'mywindow','location=1,status=1,scrollbars=1,width=840.48px,height=500px');
        })
        $('#closehambatan').click(function() {
            $('.csr').fadeOut('fast');
        })
        $('#pmr_open').click(function() {
            var pasien = $('input[name=id_pasien]').val();
            var nama   = $('input[name=pasien]').val();
            if (pasien == '') {
                alert('Silahkan isikan data pasien terlebih dahulu!');
                $('#pasien').focus();
            } else {
                location.href='<?= base_url('pelayanan/cetak_pmr') ?>?id_pasien='+pasien+'&nama='+nama;
            }
        })
        $('.noresep').click(function() {
            var url = $(this).attr('href');
            $.get(url, function(data) {
                $('#result_detail').html(data);
                $('#result_detail').dialog({
                    autoOpen: true,
                    height: 500,
                    width: 400,
                    modal: true
                })
            });
            return false;
        })
        $('.salinresep').click(function() {
            var url = $(this).attr('href');
            $('#loaddata').load(url)
            return false;
        })
        $('#apoteker').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_apoteker') ?>",
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
                var str = '<div class=result>'+data.nama+' - '+data.sip_no+'</div>';
                return str;
            },
            width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $(this).val(data.nama+' - '+data.sip_no);
            $('input[name=id_apoteker]').val(data.id);
            
        });
        $('#pasien').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_pasien') ?>",
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
                var str = '<div class=result>'+data.nama+' <br/> '+data.alamat+'</div>';
                return str;
            },
            width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $(this).val(data.nama);
            $('input[name=id_pasien]').val(data.penduduk_id);
            
        });
        $('#dokter').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_dokter') ?>",
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
                var str = '<div class=result>'+data.nama+' - '+data.kerja_izin_surat_no+'</div>';
                return str;
            },
            width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $(this).val(data.nama+' - '+data.kerja_izin_surat_no);
            $('input[name=id_dokter]').val(data.penduduk_id);
        });
        
        $('#forminforesep').submit(function(){
            var url = $(this).attr('action');
            $.ajax({
                type: 'GET',
                url: url,
                data: $(this).serialize(),
                success: function(data) {
                    $('#loaddata').html(data);
                }
            })
            return false;
        })
        $('#awal, #akhir').datepicker({
            changeMonth: true,
            changeYear: true
        })
    })
</script>
<title><?= $title ?></title>
<div id="result_detail" style="display: none"></div>
<div class="kegiatan">
    <h1><?= $title ?></h1>
    <div class="data-input">
        <fieldset><legend>Parameter</legend>

            <?= form_open('laporan/resep', 'id=forminforesep') ?>
            <label>Range Resep:</label> <?= form_input('awal',isset($_GET['awal'])?$_GET['awal']:date("d/m/Y"),'size=10 id=awal') ?> <span class="label">&nbsp; s.d &nbsp;</span><?= form_input('akhir',isset($_GET['akhir'])?$_GET['akhir']:date("d/m/Y"),'size=10 id=akhir') ?>
                <label>Nama Apoteker:</label><?= form_input('apoteker',isset($_GET['awal'])?$_GET['apoteker']:NULL,'size=40 id=apoteker') ?> <?= form_hidden('id_apoteker', isset($_GET['awal'])?$_GET['id_apoteker']:NULL) ?>
                <label>Nama Dokter:</label><?= form_input('dokter',isset($_GET['awal'])?$_GET['dokter']:NULL,'size=40 id=dokter') ?> <?= form_hidden('id_dokter', isset($_GET['awal'])?$_GET['id_dokter']:NULL) ?>
                <label>Nama Pasien:</label><?= form_input('pasien',isset($_GET['awal'])?$_GET['pasien']:NULL,'size=40 id=pasien') ?> <?= form_hidden('id_pasien', isset($_GET['awal'])?$_GET['id_pasien']:NULL) ?>
                <label></label>
                <?= form_submit(null, 'Cari', 'id=search') ?> 
                <?= form_button('Reset', 'Reset','id=reset') ?> 
                <?= form_button('', 'Cetak PMR', 'id=pmr_open') ?> 
                <input type="button" value="Cetak Statistika Resep" id="csr" />
            <?= form_close() ?>
        </fieldset>
    </div>
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
        <tr>
            <th>No Resep</th>
            <th>Tanggal</th>
            <th>ID</th>
            <th>Pasien</th>
            <th>Dokter</th>
            <th>No. R/</th>
            <th>Apoteker</th>
            <th>Bia. Apoteker</th>
            <th>Nominal</th>
            <th>#</th>
        </tr>
        <?php
        if (isset($_GET['awal'])) {
        $total = 0;
        foreach ($list_data as $key => $data) { 
        $total = $total + $data->profesi_layanan_tindakan_jasa_total;
        ?>
        <tr class="<?= ($key%2==1)?'even':'odd' ?>">
            <td align="center"><?= anchor('laporan/resep_detail/'.$data->id, $data->id, 'class=noresep') ?></td>
            <td align="center"><?= datetimefmysql($data->waktu) ?></td>
            <td align="center"><?= $data->id_pasien ?></td>
            <td><?= $data->pasien ?></td>
            <td><?= $data->dokter ?></td>
            <td><?= $data->r_no ?></td>
            <td><?= $data->apoteker ?></td>
            <td><?= $data->tarif.' '.(($data->bobot == 'Tanpa Bobot')?'':$data->bobot) ?></td>
            <td align="right"><?= $data->profesi_layanan_tindakan_jasa_total ?></td>
            <td align="center"><!--<?= anchor('laporan/salin_resep/'.$data->id, 'Cetak Salin Resep', 'class=salinresep') ?>--> 
            <?= anchor('pelayanan/resep/'.$data->id, 'Edit', 'class=salinresep') ?></td>
        </tr>
        <?php } ?>
        <tr>
            <td colspan="8" align="right"><b>Total</b></td>
            <td align="right"><b><?= rupiah($total) ?></b></td>
            <td></td>
        </tr>
        <?php } else { 
        for ($i = 1; $i <= 2; $i++) {
        ?>
        <tr class="<?= ($i%2==1)?'even':'odd' ?>">
            <td align="center">&nbsp;</td>
            <td align="center"></td>
            <td align="center"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td align="center"></td>
        </tr>
        <?php } }?>
    </table><br/>
    </div>
</div>