<div id="resume">
    <br/>
    <h3>
        Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
    </h3>
</div>
<table class="tabel" width="100%">
    <tr>
        <th width="10%">No.</th>
        <th>Nama</th>
        <th>Bobot</th>
        <th>Nominal</th>
        <th width="10%">Aksi</th>
    </tr>
    <?php if (count($layanan) == 0) : ?>

        <?php for ($i = 1; $i <= 2; $i++) : ?>
            <tr class="<?= ($i % 2 == 1) ? 'even' : 'odd' ?>">
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="aksi"></td>
            </tr>
        <?php endfor; ?>
    <?php else: ?>
        <?php foreach ($layanan as $key => $data): ?>
            <tr class="<?= ($key % 2 == 0) ? 'odd' : 'even' ?>">
                <td align="center"><?= $data->nomor ?></td>
                <td><?= $data->nama ?></td>
                <td align="center"><?= $data->bobot ?></td>
                <td align="center"><?= rupiah($data->nominal) ?></td>
                <td class="aksi">
                    <?php
                    $str = $data->id
                            . "#" . $data->nama
                            . "#" . $data->bobot
                            . "#" . $data->nominal;
                    ?>
                    <span class="edit" onclick="edit_layanan('<?= $str ?>')"><?= img('assets/images/icons/edit.png') ?></span>
                    <span class="delete" onclick="delete_layanan('<?= $data->id ?>')"><?= img('assets/images/icons/delete.png') ?></span>
                </td>   
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>
<br/>
<div id="paging"><?= $paging ?></div>