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
                    <a class="edit" onclick="edit_unit(<?= $prov->id ?>,'<?= $prov->nama ?>')"></a>
                    <a class="delete" onclick="delete_unit(<?= $prov->id ?>)"></a>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>
