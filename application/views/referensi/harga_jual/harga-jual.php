<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        function selected_item() {
            var jumlah = $('.tr_row').length-1;
            for (i = 0; i <= jumlah; i++) {
                if ($('#check'+i).is(':checked') === true) {
                    $('#listdata'+i).addClass('selected');
                } else {
                    $('#listdata'+i).removeClass('selected');
                }
            }
        }
        $('.check').live('click', function() {
            selected_item();
        });
        $(function() {
            $('#pb').focus();
            $('#key').watermark('Search ...');
            $("#table").tablesorter();
            $('#checkall').live('click', function() {
                $('.ui-button-text').html('Uncheck all');
                $('#checkall').attr('id', 'uncheckall');
                $('.check').attr('checked', 'checked');
                selected_item();
            });
            $('#uncheckall').live('click', function() {
                $('.ui-button-text').html('Check all');
                $('#uncheckall').attr('id', 'checkall');
                $('.check').removeAttr('checked');
                selected_item();
            });
            $('#key').keyup(function(e) {
                if (e.keyCode === 13) {
                    var id_pb= $('#key').val();
                    $.ajax({
                        url: '<?= base_url('referensi/harga_jual_load') ?>',
                        data: 'pb='+id_pb,
                        cache: false,
                        success: function(msg) {
                            $('#loaddata').html(msg);  
                        }
                    });
                    return false;
                }
            });
            $('#form_harga_jual2').submit(function() {
                var status = ($('.check').is(':checked') === true);

                if (status === true) {
                    $.ajax({
                        type: 'POST',
                        url: $(this).attr('action'),
                        data: $(this).serialize(),
                        success: function(data) {
                            $('#result_load').html(data);
                            $('#result_load').dialog({
                                autoOpen: true,
                                modal: true,
                                width: 700,
                                title: 'Update Harga Jual',
                                height: 400,
                                close: function() {
                                    $("#result_load").dialog().remove();
                                    var id_pb= $('#pb').val();
                                    $.ajax({
                                        url: '<?= base_url('referensi/harga_jual_load') ?>',
                                        data: 'pb='+id_pb,
                                        cache: false,
                                        success: function(msg) {
                                            $('#loaddata').html(msg);
                                        }
                                    })
                                }
                            })
                        }
                    })
                } else {
                    alert('Barang belum ada yang dipilih !');
                }
                return false;
            });
            $('#checkall').button();
            $('button[id=reset]').button({
                icons: {
                    primary: 'ui-icon-refresh'
                }
            });
            $('#update').button({
                icons: {
                    primary: 'ui-icon-pencil'
                }
            });
            $('#akhir').datepicker({
                changeYear: true,
                changeMonth: true
            });
            $('#reset').click(function() {
                $('#loaddata').html('');
                var url = '<?= base_url('referensi/harga_jual') ?>';
                $('#loaddata').load(url);
            });
            $('#searchs').click(function() {
                $('#form-update').fadeIn('fast').draggable();
            });
        });
    </script>
    <h1><?= $title ?></h1>
    <div id="result_load"></div>
    <?= form_button(NULL, 'Check all', 'id=checkall') ?>
    <div style="margin-bottom: 2px; float: right;"><?= form_input('key', null, 'id=key size=30 style="padding: 4px 5px 5px 5px;"') ?></div>
    <div class="data-list">
        
        <?= form_open('referensi/harga_jual_update', 'id=form_harga_jual2') ?>
        <table class="sortable form-inputan" width="100%" id="table">
            <thead>
            <tr>
                <th class="nosort"><h3>#</h3></th>
                <th class="nosort"><h3>Tanggal</h3></th>
                <th><h3>Packing Barang</h3></th>
                <th class="sortright"><h3>HNA (Rp.)</h3></th>
                <th class="nosort"><h3>Margin (%)</h3></th>
                <th class="nosort"><h3>Diskon (%)</h3></th>
                <th class="sortright"><h3>Harga Jual (Rp.)</h3></th>
                <th class="nosort"><h3>Stok Minimal</h3></th>
            </tr>
            </thead>
            <tbody>
        <?php
        $jumlah = 0;
        foreach ($list_data as $key => $data) {
        $harga_jual = ($data->hna+($data->hna*$data->margin/100)) - (($data->hna+($data->hna*$data->margin/100))*($data->diskon/100));
        ?>
            <tr id="listdata<?= $key ?>" class="tr_row <?= ($key%2==0)?'odd':'even' ?>">
                <td align="center"><?= form_checkbox('pb[]', $data->barang_packing_id, FALSE, 'class=check id=check'.$key.'')  ?></td>
                <td align="center"><?= datefmysql($data->tanggal) ?></td>
                <td><?= $data->barang ?> <?= $data->kekuatan ?>  <?= $data->satuan ?> <?= $data->sediaan ?> <?= $data->pabrik ?> @ <?= ($data->isi==1)?'':$data->isi ?> <?= $data->satuan_terbesar ?></td>
                <td align="right"><?= inttocur($data->hna) ?></td>
                <td align="center"><?= $data->margin ?></td>
                <td align="center"><?= $data->diskon ?></td>
                <td align="right"><?= inttocur($harga_jual) ?></td>
                <td align="center"><?= $data->stok_minimal ?></td>
            </tr>
        <?php 
        $jumlah++;
        } 
        ?>
                </tbody>
        </table>

        <?= form_submit('submit', 'Pilih', 'id=update style="margin-left:0"') ?>
        <?= form_close() ?>
        </div>

</div>