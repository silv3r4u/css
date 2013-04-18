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
        $('#pesancheck,#checkall').button();
        $('.sortable').tablesorter();
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
        
    </script>
    <h1><?= $title ?></h1>
    <?= form_button(NULL, 'Check all', 'id=checkall') ?><?= form_submit(null, 'Pesan yang dipilih', 'id=pesancheck') ?>
    <div class="data-list">
        <table class="sortable" id="table" width="100%">
            <thead>
            <tr>
                <th class="nosort"><h3>#</h3></th>
                <th class="nosort"><h3>No.</h3></th>
                <th><h3>Nama Barang</h3></th>
                <th><h3>Supplier</h3></th>
                <th><h3>Expired Date</h3></th>
                <th><h3>Sisa Stok</h3></th>
                <th><h3>Stok Minimum</h3></th>
                <th><h3>Aksi</h3></th>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($list_data as $key => $data) { ?>
                <tr class="tr_row" id="listdata<?= $key ?>">
                    <td align="center"><?= form_checkbox('id_pb[]', $data->barang_packing_id, FALSE, 'class=check id=check'.$key.'') ?></td>
                    <td align="center"><?= ++$key ?></td>
                    <td><?= $data->barang ?> <?= $data->kekuatan ?>  <?= $data->satuan ?> <?= $data->sediaan ?> <?= $data->pabrik ?> @ <?= ($data->isi==1)?'':$data->isi ?> <?= $data->satuan_terkecil ?></td>
                    <td></td>
                    <td><?= datefmysql($data->ed) ?></td>
                    <td><?= $data->sisa ?></td>
                    <td><?= $data->stok_minimal ?></td>
                    <td align="center"><?= form_button(NULL, 'Pesan', 'class=pesan id=pesan'.$key) ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </tabel>
    </div>
</div>