<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        $(function() {
            $('button[id=reset]').button({
                icons: {
                    primary: 'ui-icon-refresh'
                }
            });
            $('button[id=search]').button({
                icons: {
                    primary: 'ui-icon-circle-check'
                }
            });
            $('button[id=cetak]').button({
                icons: {
                    primary: 'ui-icon-print'
                }
            });
            $('#reset').click(function() {
                var url = '<?= base_url('laporan/laba_rugi') ?>';
                $('#loaddata').load(url);
            });
            $('#cetak').hide();
            $('#cetak').click(function() {
                var awal = $('#awal').val();
                var akhir = $('#akhir').val();
                location.href='<?= base_url('laporan/laba_rugi_load_data_excel') ?>?awal='+awal+'&akhir='+akhir+'&do=cetak';
            });
            $('#awal,#akhir').datepicker({
                changeYear: true,
                changeMonth: true
            });
            $('#search').click(function() {
                var awal = $('#awal').val();
                var akhir= $('#akhir').val();
                var jenis= $('#jenis').val();
                var nama = $('#nama').val();
                $.ajax({
                    url: '<?= base_url('laporan/laba_rugi_load_data') ?>',
                    data: 'awal='+awal+'&akhir='+akhir,
                    cache: false,
                    success: function(data) {
                        $('#result').html(data);
                        $('#cetak').fadeIn();
                    }
                });
            });
        });
    </script>
    <h1><?= $title ?></h1>
    <div class="data-input">
        <fieldset><legend>Parameter Pencarian</legend>
            <label>Tanggal</label><?= form_input('awal', date("d/m/Y"), 'id=awal size=10') ?> <span class="label">s . d </span><?= form_input('akhir', date("d/m/Y"), 'id=akhir size=10') ?>
            <label></label><?= form_button(null, 'Cari', 'id=search') ?> 
            <?= form_button('Reset', 'Reset', 'id=reset') ?> 
            <?= form_button(null, 'Cetak Excel', 'id=cetak') ?>
        </fieldset>
    </div>
    <div id="result"></div>
        
    <!--<?= link_href('inventory/inkaso/', '<u>Inkaso</u>', null) ?> | 
    <?= link_href('inventory/retur-pembelian/', '<u>Retur Pembelian</u>', null) ?> |
    <?= link_href('transaksi/pp-uang/', '<u>Penerimaan dan Pengeluaran Uang</u>', null) ?>-->
</div>
