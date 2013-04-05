<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        $(function() {
            $('input[id=search]').each(function(){
                $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
            });
            $('button[id=search]').button({
                icons: {
                    primary: 'ui-icon-circle-check'
                }
            });
            $('button[id=reset]').button({
                icons: {
                    primary: 'ui-icon-refresh'
                }
            })
            $('button[id=update]').button({
                icons: {
                    primary: 'ui-icon-pencil'
                }
            })
            $('#akhir').datepicker({
                changeYear: true,
                changeMonth: true
            })
        
            $('#update').hide();
            $('#reset').click(function() {
                $('#loaddata').html('');
                var url = '<?= base_url('referensi/harga_jual') ?>';
                $('#loaddata').load(url);
            })
            $('#searchs').click(function() {
                $('#form-update').fadeIn('fast').draggable();
            })
        
            $('#form_harga_jual').submit(function() {
                if ($('#id_pb').val() == '') {
                    alert('Packing barang tidak boleh kosong !');
                    $('#pb').focus();
                    return false;
                }
                if ($('#akhir').val() == '') {
                    alert('Tanggal batas akhir tidak boleh kosong !');
                    $('#akhir').focus();
                    return false;
                }
                $('#searchs').fadeIn('fast');
                var id_pb= $('#pb').val();
                $.ajax({
                    url: '<?= base_url('referensi/harga_jual_load') ?>',
                    data: 'pb='+id_pb,
                    cache: false,
                    success: function(msg) {
                        $('#result').html(msg);
                    }
                })
                return false;
            })
        
        })
    </script>
    <h1><?= $title ?></h1>
    <?php
    if (isset($_GET['id'])) {
        $bp = harga_jual_muat_data($_GET['id'], date2mysql($_GET['tgl']));
        foreach ($bp as $data)
            ;
        $packing = $data['barang'] . ' ' . $data['kekuatan'] . ' ' . $data['satuan'] . ' ' . $data['sediaan'] . ' ' . $data['pabrik'] . '@' . (($data['isi'] == 1 ) ? '' : $data['isi']) . ' ' . $data['satuan_terbesar'];
    }
    ?>
    <div class="data-input">
        <fieldset><legend>Parameter</legend>
            <?= form_open('referensi/harga_jual', 'id=form_harga_jual') ?>
            <label>Nama Barang</label> <?= form_input('pb', isset($_GET['id']) ? $packing : null, 'id=pb size=50') ?> <?= form_hidden('id_pb') ?>
            <label></label>
            <?= form_submit(null, 'Cari', 'id=search') ?>
            <?= form_button('Reset', 'Reset', 'id=reset') ?>
            <?= form_button('updatemargin', 'Update Margin', 'id=update') ?>
            <?= form_close() ?>
        </fieldset>
    </div>
    <div id="result">

    </div>

</div>