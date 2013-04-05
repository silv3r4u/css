<div id="resume">
    <br/>
    <h3>
        Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit)==0)?1:ceil($jumlah / $limit)?> (Total <?= $jumlah ?> data )
    </h3>

</div>
<table cellpadding="0" cellspacing="0" class="tabel" width="80%">
    <tr>
        <th width="10%">No.</th>
        <th width="30%">Nama</th>
        <th width="30%">Provinsi</th>
        <th width="10%">Kode</th>
        <th width="10%">Aksi</th>
    </tr>
    <?php if ($kabupaten != null): ?>
        <?php foreach ($kabupaten as $key => $kab) : ?>
            <tr class="<?= ($key % 2) ? "even" : "odd" ?>">
                <td align="center"><?= $kab->nomor ?></td>
                <td><?= $kab->nama ?></td>
                <td><?= $kab->provinsi ?></td>
                <td><?= $kab->kode ?></td>
                <td class="aksi"> 
                    <a class="edit" onclick="edit_kabupaten('<?= $kab->id ?>','<?= $kab->nama ?>','<?= $kab->provinsi_id ?>','<?= $kab->provinsi ?>','<?= $kab->kode ?>')"></a>
                    <a class="delete" onclick="delete_kabupaten('<?= $kab->id ?>')"></a>
                </td>   
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>
<br/>
<div id="paging"><?= $paging ?></div>