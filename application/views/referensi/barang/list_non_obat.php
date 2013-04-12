<script>
$("#table").tablesorter({sortList:[[0,0]]});
</script>
<?php if (isset($key)): ?>
    <div id="pencarian">
        <br/>
        <h3>
            Pencarian dengan kata kunci "<?= $key ?>" 
            <?php if ($pabrik!=""): ?>
                dan pabrik "<?= $pabrik ?>"   
            <?php endif; ?>
        </h3>
    </div>
<?php endif; ?>
<table class="sortable" id="table" width="100%">
    <thead>
    <tr>
        <th class="nosort" width="5%"><h3>No.</h3></th>
        <th width="20%"><h3>Nama</h3></th>
        <th width="20%"><h3>Kategori</h3></th>
        <th width="20%"><h3>Pabrik</h3></th>
        <th width="5%" class="nosort"><h3>Aksi</h3></th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($barang) == 0) : ?>

        <?php for ($i = 1; $i <= 2; $i++) : ?>
            <tr class="<?= ($i % 2 == 1) ? 'even' : 'odd' ?>">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td class="aksi">

                </td>
            </tr>
        <?php endfor; ?>
    <?php else: ?>
        <?php foreach ($barang as $key => $rowA): ?>
            <tr class="<?= ($key % 2 == 1) ? 'even' : 'odd' ?>">
                <td align="center"><?= (++$key+$start) ?></td>
                <td><?= $rowA->nama ?></td>
                <td><?= isset($rowA->kategori) ? $rowA->kategori : '-' ?></td>
                <td><?= $rowA->pabrik ?></td>
                <?php $str = $rowA->id . "#" . $rowA->nama . "#" . $rowA->barang_kategori_id . "#" . $rowA->id_pabrik . "#" . $rowA->pabrik; ?>
                <td class="aksi" align="center"> 
                    <span class="edit" onclick="edit_non('<?= $str ?>')"><?= img('assets/images/icons/edit.png') ?></span>
                    <span class="delete" onclick="delete_non('<?= $rowA->id ?>')"></span>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<br/>
<?= $this->load->view('paging') ?>
<div id="paging"><?= $paging ?></div>