<?php $this->load->view('message') ?>
<script type="text/javascript">
    $(function() {
        $('#reset').click(function() {
            $('#loaddata').empty();
            $('#loaddata').load('<?= base_url('klinis/hipertensi') ?>');
        });
        $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
        $('button[type=submit]').button({icons: {primary: 'ui-icon-circle-check'}});
        $('#tabs').tabs();
        $('#cari').button({icons: {primary: "ui-icon-newwin"}});
        $('#print').button({icons: {primary: "ui-icon-print"}});
        $('#reset').button({icons: {primary: "ui-icon-refresh"}});
        $('#print').click(function() {
            var id = $('input[name=id_penduduk]').val();
            if (id !== '') {
                location.href='<?= base_url('klinis/cetak_excel_diabetes') ?>/'+id;
            } else {
                alert('Isikan terlebih dahulu data pasien !');
                return false;
            }
        });
        $('#data').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_hipertensi') ?>",
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
                    var str = '<div class=result>'+data.nama+' <br/> '+data.alamat+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('input[name=id_penduduk]').val(data.id_penduduk);
                $('#norm').html(data.id_penduduk);
                $('#nama').html(data.nama);
                $('#ttl').html(data.kabupaten);
                $('#alamat').html(data.alamat);
                $('#usia').html(hitungUmur(datefmysql(data.lahir_tanggal)));
                $('#telp').html(data.telp);
                $('#pekerjaan').html(data.pekerjaan);
                $.ajax({
                    url: '<?= base_url('klinis/get_last_penyakit_diabetes') ?>/'+data.id_penduduk,
                    cache: false,
                    success: function(data) {
                        $('#last_penyakit').html(data);
                    }
                });
                $.ajax({
                    url: '<?= base_url('klinis/get_last_pemeriksaan_diabetes') ?>/'+data.id,
                    cache: false,
                    success: function(data) {
                        $('#last_pemeriksaan').html(data);
                    }
                });
            });
            $('#simpan_klinis').submit(function() {
                $.ajax({
                    url: '<?= base_url('klinis/save_diabetes') ?>',
                    type:'POST',
                    dataType:'json',
                    data: $(this).serialize(),
                    cache: false,
                    success: function(data) {
                        if (data === true) {
                            alert_tambah();
                        }
                    }
                }); 
                return false;
            });
    });
</script>
<div class="kegiatan">
    <h1 class="informasi"><?= $title ?></h1>
    <?= form_open('klinis/save_diabetes', 'id=simpan_klinis') ?>
    <?= form_input('data', NULL, 'id=data size=30 style="margin-left:0 padding: 4px;"') ?><?= form_hidden('id_penduduk') ?>
    <div id="tabs">
        <ul>
            <li><a class="demografi" href="#demografi">Demografi</a></li>
            <li><a class="history" href="#riwayat">Riwayat Penyakit</a></li>
            <li><a class="dinamis" href="#dinamis">Data Dinamis Pasien</a></li>
        </ul>
        <div id="demografi">
            <?php $this->load->view('klinis/demografi') ?>
        </div>
        <div id="riwayat">
            <?php $this->load->view('klinis/riwayat') ?>
        </div>
        <div id="dinamis">
            <?php $this->load->view('klinis/dinamis') ?>
        </div>
    </div>
    <?= form_submit('simpan', 'Simpan') ?>
    <?= form_button(NULL, 'Reset Data', 'id=reset') ?>
    <?= form_button(NULL, 'Cetak', 'id=print') ?>
    <?= form_close() ?>
</div>