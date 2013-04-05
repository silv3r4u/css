<?php if (isset($key)): ?>
    <div id="pencarian">
        <br/>
        <h3>
            Pencarian dengan kata kunci "<?= $key ?>" 
        </h3>
    </div>
<?php endif; ?>
<div id="resume">
    <br/>
    <h3>
        Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
    </h3>
</div>
<table class="tabel" width="100%">
    <tr>
        <th width="10%">No</th>
        <th>Nama</th>
        <th>Kategori</th>
        <th>J.S</th>
        <th>J.S Tindakan RS</th>
        <th>Jasa Profesi</th>
        <th>BHP</th>
        <th>U.C</th>
        <th>Margin (%)</th>
        <th>Total</th>
        <th width="10%">Aksi</th>
    </tr>
    <?php foreach ($tarif as $key => $data): ?>
        <tr class="<?= ($key % 2 == 0) ? 'odd' : 'even' ?>">
            <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
            <td><?= $data->nama ?> <?= $data->bobot ?> <?= $data->kelas ?></td>
            <td><?= $data->kategori ?></td>
            <td><?= rupiah($data->js) ?></td>
            <td><?= rupiah($data->rs_tindakan_jasa) ?></td>
            <td><?= rupiah($data->profesi_layanan_tindakan_jasa_total) ?></td>
            <td><?= rupiah($data->bhp) ?></td>
            <td><?= rupiah($data->uc) ?></td>
            <td><?= $data->profit_margin ?></td>
            <td><?= rupiah($data->nominal) ?></td>
            <td class="aksi">
                <?php
                $str = $data->id
                        . "#" . $data->layanan_id
                        . "#" . $data->nama
                        . "#" . $data->tarif_kategori_id
                        . "#" . $data->kategori
                        . "#" . $data->js
                        . "#" . $data->rs_tindakan_jasa
                        . "#" . $data->profesi_layanan_tindakan_jasa_total
                        . "#" . $data->bhp
                        . "#" . $data->uc
                        . "#" . $data->profit_margin
                        . "#" . $data->nominal
                ?>
                <a class="edit" onclick="edit_tarif('<?= $str ?>')"></a>
                <a class="delete" onclick="delete_tarif('<?= $data->id ?>')"></a>
            </td>   
        </tr>
    <?php endforeach; ?>
</table>
<br/>
<div id="paging"><?= $paging ?></div>