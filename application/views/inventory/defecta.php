<?php $this->load->view('message') ?>
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
        $('#checkall').button();
        $('.pesan').button({ icons: { primary: 'ui-icon-suitcase' }});
        $('#pesancheck').button({ icons: { primary: 'ui-icon-suitcase' }});
        $('#showchart').button({ icons: { primary: 'ui-icon-cart' }});
        $('.sortable').tablesorter();
        $('.pesan').click(function() {
            var id = $(this).attr('id');
            $.get('<?= base_url('inventory/save_defecta') ?>/'+id, function() {
                $('#loaddata').load('<?= base_url('inventory/defecta') ?>');
            },'json');
        });
        $('#checkall').live('click', function() {
            $('#checkall .ui-button-text').html('Uncheck all');
            $('#checkall').attr('id', 'uncheckall');
            $('.check').attr('checked', 'checked');
            selected_item();
        });
        $('#pesancheck').click(function() {
            $('#form_defecta').submit();
        });
        $('#uncheckall').live('click', function() {
            $('#uncheckall .ui-button-text').html('Check all');
            $('#uncheckall').attr('id', 'checkall');
            $('.check').removeAttr('checked');
            selected_item();
        });
        $('#showchart').click(function() {
            $('#loaddata').load('<?= base_url('inventory/rencana_pemesanan') ?>');
        })
        $('#form_defecta').submit(function() {
            var data = $('.check').is(':checked');
            if (data === false) {
                alert('Pilih salah satu barang !');
            } else {
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    cache: false,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status === true) {
                            alert_tambah();
                        } else {
                            alert('Failed to process transaction !');
                        }
                    }
                });
            }
            return false;
        });
    </script>
    <h1><?= $title ?></h1>
    <?= form_button(NULL, 'Check all', 'id=checkall') ?><?= form_button(null, 'Masukkan ke Rencana', 'id=pesancheck') ?><?= form_button(NULL, 'Rencana Pemesanan', 'id=showchart') ?>
    <div class="data-list">
        <table class="sortable" id="table" width="100%">
            <thead>
            <tr>
                <th class="nosort"><h3>#</h3></th>
                <th class="nosort"><h3>No.</h3></th>
                <th><h3>Nama Barang</h3></th>
                <th><h3>Expired Date</h3></th>
                <th><h3>Sisa Stok</h3></th>
                <th><h3>Stok Minimum</h3></th>
                <th><h3>Aksi</h3></th>
            </tr>
            </thead>
            <tbody>
                <?php 
                echo form_open('inventory/save_defecta', 'id=form_defecta');
                foreach ($list_data as $key => $data) { ?>
                <tr class="tr_row" id="listdata<?= $key ?>">
                    <td align="center"><?= form_checkbox('id_pb[]', $data->barang_packing_id, FALSE, 'class=check id=check'.$key.'') ?></td>
                    <td align="center"><?= ++$key ?></td>
                    <td><?= $data->barang ?> <?= $data->kekuatan ?>  <?= $data->satuan ?> <?= $data->sediaan ?> <?= $data->pabrik ?> @ <?= ($data->isi==1)?'':$data->isi ?> <?= $data->satuan_terkecil ?></td>
                    <td align="center"><?= datefmysql($data->ed) ?></td>
                    <td><?= $data->sisa ?></td>
                    <td><?= $data->stok_minimal ?></td>
                    <td align="center"><?= form_button(NULL, 'Pesan', 'class=pesan id='.$data->barang_packing_id) ?></td>
                </tr>
                <?php } 
                echo form_close(); ?>
            </tbody>
        </tabel>
    </div>
</div>