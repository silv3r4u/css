<div id="resume">
    <br/>
    <h3>
        Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit)==0)?1:ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
    </h3>

</div>
<table cellpadding="0" cellspacing="0" class="tabel" width="60%">
    <tr>
        <th width="10%">No.</th>
        <th width="20%">Nama</th>
        <th width="20%">Kecamatan</th>
        <th width="20%">Kode Wilayah</th>
        <th width="10%">Aksi</th>
    </tr>
    <?php if ($kelurahan != null): ?>
        <?php foreach ($kelurahan as $key => $data): ?>
            <tr class="<?php echo ($key % 2) ? "even" : "odd" ?>">
                <td align="center"><?= $data->nomor ?></td>
                <td><?php echo $data->nama ?></td>
                <td><?php echo $data->kecamatan ?></td>
                <td><?= $data->kode ?></td>
                <td class="aksi"> 
                    <a class="edit" onclick="edit_kelurahan('<?= $data->id ?>','<?= $data->nama ?>','<?= $data->kecamatan_id ?>','<?= $data->kecamatan ?>','<?= $data->kode ?>')"></a>
                    <a class="delete" onclick="delete_kelurahan('<?= $data->id ?>')"></a>
                </td> 
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>
<br/>
<div id="paging"><?= $paging ?></div>