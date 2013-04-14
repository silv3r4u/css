<script>
$("#table").tablesorter({sortList:[[0,0]]});
</script>
<?php if (isset($key)): ?>
    <div id="pencarian">
        <h3>
            Pencarian dengan kata kunci "<?= $key ?>" 
            <?php if (isset($alamat) && $alamat != ''): ?>
                , alamat "<?= $alamat ?>"   
            <?php endif; ?>

            <?php if (isset($telp) && $telp != ''): ?>
                ,  telepon "<?= $telp ?>"   
            <?php endif; ?>

            <?php if (isset($kabupaten) && $kabupaten != ''): ?>
                , kabupaten "<?= $kabupaten ?>"   
            <?php endif; ?>

            <?php if (isset($gender) && $gender != ''): ?>
                , jenis kelamin "<?= ($gender == 'L') ? 'Laki - laki' : 'Perempuan' ?>"   
            <?php endif; ?>

            <?php if (isset($gol_darah) && $gol_darah != ''): ?>
                , golongan darah "<?= $gol_darah ?>"   
            <?php endif; ?>

            <?php if (isset($tgl_lahir) && $tgl_lahir != ''): ?>
                , tanggal lahir "<?= $tgl_lahir ?>"   
            <?php endif; ?>

        </h3>
    </div>
<?php endif; ?>
<div id="list" class="data-list">
<table class="sortable" id="table" width="100%">
    <thead>
    <tr>
        <th class="nosort" width="5%"><h3>No.</h3></th>
        <th width="10%"><h3>No. Identitas</h3></th>
        <th width="30%"><h3>Nama</h3></th>
        <th width="40%"><h3>Alamat</h3></th>
        <th width="10%"><h3>No. Telp</h3></th>
        <th class="nosort" width="5%"><h3>Aksi</h3></th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($penduduk) == 0) : ?>

        <?php
        for ($i = 1; $i <= 2; $i++) :
            ?>
            <tr class="<?= ($i % 2 == 1) ? 'even' : 'odd' ?>">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="aksi">

                </td>
            </tr>
        <?php endfor; ?>
    <?php else: ?>
        <?php foreach ($penduduk as $key => $rows): ?>

            <tr class="tr_row <?= ($key % 2 == 1) ? 'even' : 'odd' ?>">
                <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
                <td align="center"><?= ($rows->identitas_no=='')?'-':$rows->identitas_no ?></td>
                <td><?= $rows->nama ?></td>
                <td><?= !empty($rows->alamat) ? $rows->alamat : '-' ?></td>
                <td><?= $rows->telp ?></td>
                <td class="aksi" align="center">
                    <?php
                    $str = $rows->penduduk_id
                            . "#" . $rows->nama
                            . "#" . $rows->alamat
                            . "#" . $rows->telp
                            . "#" . $rows->lahir_kabupaten_id
                            . "#" . $rows->kabupaten
                            . "#" . $rows->gender
                            . "#" . $rows->darah_gol
                            . "#" . $rows->lahir_tanggal
                            . "#" . $rows->id_dp;
                    ?>
                    <span class="edit" onclick="edit_penduduk('<?= $str ?>')"><?= img('assets/images/icons/edit.png') ?></span>
                    <span class="delete" onclick="delete_penduduk('<?= $rows->penduduk_id ?>')"><?= img('assets/images/icons/delete.png') ?></span>
                </td>   
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
</div><br/>
<?= $this->load->view('paging') ?>
<!--<div id="paging"><?= $paging ?></div>-->