<script>
$("#table").tablesorter({sortList:[[0,0]]});
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
        <th><h3>Nama</h3></th>
        <th width="15%"><h3>Kode</h3></th>
        <th width="15%" class="nosort"><h3>Aksi</h3></th>
    </tr>
    </thead>
    <tbody>
    <?php if ($provinsi != null): ?>
        <?php foreach ($provinsi as $key => $prov): ?>
            <tr class="<?= ($key % 2) ? "even" : "odd" ?>">
                <td align="center"><?= $prov->nomor ?></td>
                <td><?= $prov->nama ?></td>
                <td align="center"><?= $prov->kode ?></td>
                <td class="aksi"> 
                    <a class="edit" onclick="edit_provinsi('<?= $prov->id ?>','<?= $prov->nama ?>','<?= $prov->kode ?>')"></a>
                    <a class="delete" onclick="delete_provinsi('<?= $prov->id ?>')"></a>
                </td>        
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<br/>
<div id="paging"><?= $paging ?></div>