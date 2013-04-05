<script>
$("#table").tablesorter({sortList:[[0,0]]});
</script>
<?php if (isset($key)): ?>
    <div id="pencarian">
        <br/>
        <h3>
            Pencarian dengan kata kunci "<?= $key ?>" 
            <?php if ($pabrik != ""): ?>
                dan pabrik "<?= $pabrik ?>"   
            <?php endif; ?>
        </h3>
    </div>
<?php endif; ?>
<table class="sortable" id="table" width="100%">
    <thead>
    <tr>
        <th class="nosort" width="5%"><h3>No.</h3> </th>
        <th width="30%"><h3>Nama</h3> </th>
        <th width="10%"><h3>Kekuatan</h3></th>
        <th width="10%"><h3>Satuan</h3></th>
        <th width="15%"><h3>Bentuk Sediaan</h3></th>
        <th width="20%"><h3>Pabrik</h3></th>
        <th width="20%"><h3>Kandungan</h3></th>
        <th class="nosort" width="10%"><h3>Aksi</h3></th>
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
                <td></td>
                <td></td>
                <td></td>
                <td class="aksi">

                </td>
            </tr>
        <?php endfor; ?>
    <?php else: ?>
        <?php foreach ($barang as $key => $rows): ?>
            <tr class="<?= ($key % 2 == 1) ? 'even' : 'odd' ?>">
                <td align="center"><?= (++$key+$start) ?></td>
                <td><?= $rows->nama ?></td>
                <td><?= $rows->kekuatan ?></td>
                <td><?= $rows->satuan ?></td>
                <td><?= $rows->sediaan ?></td>
                <td><?= $rows->pabrik ?></td>
                <td><?= $rows->kandungan ?></td>
                <td class="aksi" align="center"> 
                    <?php
                    $str = $rows->id . "#" . $rows->nama
                            . "#" . $rows->id_pabrik . "#" . $rows->pabrik
                            . "#" . $rows->kekuatan . "#" . $rows->satuan_id
                            . "#" . $rows->sediaan_id . "#" . $rows->atc . "#" . $rows->ddd . "#" . $rows->adm_r
                            . "#" . $rows->perundangan . "#" . $rows->generik . "#" . $rows->formularium
                            . "#" . $rows->indikasi . "#" . $rows->dosis;
                    ?>
                    <a class="edit" onclick="edit_obat('<?= $str ?>')"></a>
                    <a class="delete" onclick="delete_obat('<?= $rows->id ?>')"></a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<br/>
<?= $this->load->view('paging') ?>
<div id="paging"><?= $paging ?></div>