<table cellpadding="0" cellspacing="0" class="tabel" width="30%">
    <tr>
        <th width="10%">ID</th>
        <th>Unit</th>
        <th width="15%">Aksi</th>
    </tr>
    <?php if ($unit != null): ?>
        <?php foreach ($unit as $key => $prov) : ?>
            <tr class="<?= ($key % 2) ? "even" : "odd" ?>">
                <td align="center"><?= $prov->id ?></td>
                <td><?= $prov->nama ?></td>
                <td class="aksi">
                    <span class="edit" onclick="edit_unit(<?= $prov->id ?>,'<?= $prov->nama ?>')"><?= img('assets/images/icons/edit.png') ?></span>
                    <span class="delete" onclick="delete_unit(<?= $prov->id ?>)"><?= img('assets/images/icons/delete.png') ?></span>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>
