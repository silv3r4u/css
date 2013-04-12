<script>
$(".sortable").tablesorter();
</script>
<div id="resume">
    <br/>
    <h3>
        Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit)==0)?1:ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
    </h3>

</div>
<table cellpadding="0" cellspacing="0" class="sortable" id="table" width="60%">
    <thead>
    <tr>
        <th width="10%" class="nosort"><h3>No.</h3></th>
        <th width="20%"><h3>Nama</h3></th>
        <th width="20%"><h3>Kabupaten</h3></th>
        <th width="20%"><h3>Kode</h3></th>
        <th width="10%" class="nosort"><h3>Aksi</h3></th>
    </tr>
    </thead>
    <tbody>
    <?php if ($kecamatan != null): ?>
        <?php foreach ($kecamatan as $key => $kab): ?>
            <tr class="<?php echo ($key % 2) ? "even" : "odd" ?>">
                <td align="center"><?= $kab->nomor ?></td>
                <td><?php echo $kab->nama ?></td>
                <td><?php echo $kab->kabupaten ?></td>
                <td><?php echo $kab->kode ?></td>
                <td class="aksi"> 
                    <span class="edit" onclick="edit_kecamatan('<?= $kab->id ?>','<?= $kab->nama ?>','<?= $kab->kabupaten_id ?>','<?= $kab->kabupaten ?>','<?= $kab->kode ?>')"><?= img('assets/images/icons/edit.png') ?></span>
                    <span class="delete" onclick="delete_kecamatan('<?= $kab->id ?>')"><?= img('assets/images/icons/delete.png') ?></span>
                </td> 
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<br/>
<div id="paging"><?= $paging ?></div>
