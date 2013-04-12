<script>
$(".sortable").tablesorter();
</script>
<div id="resume">
    <br/>
    <h3>
        Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit)==0)?1:ceil($jumlah / $limit)?> (Total <?= $jumlah ?> data )
    </h3>

</div>
<table cellpadding="0" cellspacing="0" class="sortable" id="table" width="60%">
    <thead>
    <tr>
        <th width="10%" class="nosort"><h3>No.</h3></th>
        <th width="20%"><h3>Nama</h3></th>
        <th width="20%"><h3>Provinsi</h3></th>
        <th width="20%"><h3>Kode</h3></th>
        <th width="10%" class="nosort"><h3>Aksi</h3></th>
    </tr>
    </thead>
    <tbody>
    <?php if ($kabupaten != null): ?>
        <?php foreach ($kabupaten as $key => $kab) : ?>
            <tr class="<?= ($key % 2) ? "even" : "odd" ?>">
                <td align="center"><?= $kab->nomor ?></td>
                <td><?= $kab->nama ?></td>
                <td><?= $kab->provinsi ?></td>
                <td><?= $kab->kode ?></td>
                <td class="aksi"> 
                    <span class="edit" onclick="edit_kabupaten('<?= $kab->id ?>','<?= $kab->nama ?>','<?= $kab->provinsi_id ?>','<?= $kab->provinsi ?>','<?= $kab->kode ?>')"><?= img('assets/images/icons/edit.png') ?></span>
                    <span class="delete" onclick="delete_kabupaten('<?= $kab->id ?>')"><?= img('assets/images/icons/delete.png') ?></span>
                </td>   
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<br/>
<div id="paging"><?= $paging ?></div>