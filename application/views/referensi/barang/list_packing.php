<script>
$("#table").tablesorter({sortList:[[0,0]]});
</script>
<?php if (isset($key)): ?>
    <div id="pencarian">
        <br/>
        <h3>
            Pencarian dengan kata kunci "<?= $key ?>" 
        </h3>
    </div>
<?php endif; ?>
<table class="sortable" id="table" width="100%">
    <thead>
    <tr>
        <th class="nosort"><h3>No</h3></th>
        <th width="15%"><h3>Barcode</h3></th>
        <th width="40%"><h3>Barang</h3></th>
        <th width="10%"><h3>Kemasan</h3></th>
        <th width="10%"><h3>Isi @</h3></th>
        <th width="10%"><h3>Satuan</h3></th>
        <th class="nosort" width="10%"><h3>Aksi</h3></th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($packing) == 0) : ?>

        <?php
        for ($i = 1; $i <= 2; $i++) :
            ?>
            <tr class="<?= ($i % 2 == 1) ? 'even' : 'odd' ?>">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="aksi"></td>
            </tr>
        <?php endfor; ?>
    <?php else: ?>
        <?php foreach ($packing as $key => $rows): ?>
            <tr class="<?= ($key % 2 == 0) ? 'odd' : 'even' ?>">
                <td align="center" align="center"><?= (++$key+$start) ?></td>
                <td><a id="<?= $rows->barcode ?>" class="barcode" style="cursor: pointer;" onclick="cetak_barcode('<?= $rows->barcode ?>')"><?= $rows->barcode ?></a></td>
                <td><?= $rows->nama ?> <?= ($rows->kekuatan == '1') ? '' : $rows->kekuatan ?> <?= $rows->satuan_obat ?> <?= $rows->sediaan ?> <?= ($rows->generik != 'Non Generik')?$rows->pabrik:NULL ?></a></td>
                <td><?= $rows->s_besar ?></td>
                <td><?= $rows->isi ?></td>
                <td><?= $rows->s_kecil ?></td>
                <td class="aksi" align="center">
                    <?php
                    $str = $rows->id
                            . "#" . $rows->barcode
                            . "#" . $rows->barang_id
                            . "#" . $rows->nama
                            . "#" . $rows->terbesar_satuan_id
                            . "#" . $rows->isi
                            . "#" . $rows->terkecil_satuan_id;
                    ?>
                    <span class="edit" onclick="edit_packing('<?= $str ?>')"><?= img('assets/images/icons/edit.png') ?></span>
                    <span class="delete" onclick="delete_packing('<?= $rows->id ?>')"><?= img('assets/images/icons/delete.png') ?></span>
                </td>   

            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<br/>
<?= $this->load->view('paging') ?>
<div id="paging"><?= $paging ?></div>