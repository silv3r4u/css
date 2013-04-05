
<table class="tabel" width="100%">
    <tr>
        <th>Tanggal</th>
        <th>Alamat</th>
        <th>Kelurahan</th>
        <th>Pekerjaan</th>
    </tr>
    <?php foreach ($dinamis as $key => $rows) : ?>
        <tr class="<?= ($key % 2 == 1) ? 'even' : 'odd' ?>">
            <td align="center"><?= datefmysql($rows->tanggal) ?></td>
            <td><?= $rows->alamat ?></td>
            <td><?= $rows->kelurahan ?></td>
            <td><?= $rows->pekerjaan ?></td>
        </tr>
    <?php endforeach; ?>
</table>