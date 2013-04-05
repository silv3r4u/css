<div id="resume">
    <br/>
    <h3>
        Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit)==0)?1:ceil($jumlah / $limit)?> (Total <?= $jumlah ?> data )
    </h3>

</div>
<table cellpadding="0" cellspacing="0" class="tabel" width="60%">
    <tr>
        <th width="10%">No.</th>
        <th>Nama</th>
        <th width="15%">Kode</th>
        <th width="15%">Aksi</th>
    </tr>
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
</table>
<br/>
<div id="paging"><?= $paging ?></div>