<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        $(function() {
            $('button[id=reset]').click(function() {
                var url = '<?= base_url('billing/laporan') ?>';
                $('#loaddata').load(url);
            })
            $('#show').each(function(){
                $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
            });
            $('#show').button({
                icons: {
                    secondary: 'ui-icon-circle-check'
                }
            });
            $('button[id=reset]').button({
                icons: {
                    secondary: 'ui-icon-refresh'
                }
            })
        
            $('#show').focus();
            $('#awal').datepicker({
                changeYear : true,
                changeMonth : true 
            });
            $('#akhir').datepicker({
                changeYear : true,
                changeMonth : true,
                minDate : $('#awal').val()
            });
            $('#tidak').click(function() {
                $('#awal,#akhir').val('').attr('disabled','disabled');
            })
            $('#lunas,#belum').click(function() {
                $('#awal,#akhir').removeAttr('disabled');
            })
            $('#show').click(function() {
                var awal = $('#awal').val();
                var akhir= $('#akhir').val();
                if ($('#tidak').is(':checked')) { var pembayaran = 'tidak'; }
                if ($('#lunas').is(':checked')) { var pembayaran = 'lunas'; }
                if ($('#belum').is(':checked')) { var pembayaran = 'belum'; }
                $.ajax({
                    url: '<?= base_url('billing/laporan_load_data/') ?>?awal='+awal+'&akhir='+akhir+'&pembayaran='+pembayaran,
                    cache: false,
                    success: function(data) {
                        $('#results').html(data);
                    }
                })
            })
        })
    </script>
    <h1><?= $title ?></h1>
    <div class="data-input">
        <fieldset><legend>Summary</legend>
            <label>Tgl Pembayaran</label><?= form_input('awal', isset($_GET['awal']) ? $_GET['awal'] : date("d/m/Y"), 'id=awal') ?> <span class="label"> s.d </span> <?= form_input('akhir', isset($_GET['awal']) ? $_GET['awal'] : date("d/m/Y"), 'id=akhir') ?>
            <label>Pembayaran</label><?= form_radio('bayar', 'tidak', '', 'id=tidak') ?> <span class="label" id="tidak">Belum Bayar</span>
            <?= form_radio('bayar', 'lunas', '', 'id=lunas') ?> <span class="label" id="lunas">Lunas</span>
            <?= form_radio('bayar', 'belum', true, 'id=belum') ?> <span class="label" id="belum">Belum Lunas</span>


            <label></label><?= form_submit('tampilkan', 'Tampilkan', 'id=show') ?> <?= form_button(NULL, 'Reset', 'id=reset') ?>
        </fieldset>
        <div id="results"></div>
    </div>

</div>
<?php die; ?>