<script>
$("#table").tablesorter({sortList:[[0,0]]});
</script>
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
                    /*1*/   . "#" . $rows->nama
                            . "#" . $rows->alamat
                            . "#" . $rows->telp
                            . "#" . $rows->lahir_kabupaten_id
                            . "#" . $rows->kabupaten
                    /*6*/   . "#" . $rows->gender
                            . "#" . $rows->darah_gol
                            . "#" . $rows->lahir_tanggal
                            . "#" . $rows->id_dp
                            . "#" . $rows->lahir_kabupaten_id
                    /*11*/  . "#" . $rows->kabupaten
                            . "#" . $rows->kabupaten_id
                            . "#" . $rows->kabupaten_alamat
                            . "#" . $rows->identitas_no
                            . "#" . $rows->pernikahan
                            . "#" . $rows->kabupaten_alamat
                    /*17*/  . "#" . $rows->pendidikan_id
                            . "#" . $rows->profesi_id
                            . "#" . $rows->str_no
                            . "#" . $rows->sip_no
                            . "#" . $rows->kerja_izin_surat_no
                    /*22*/  . "#" . $rows->jabatan
                            . '#' . $rows->member;
                            
                    ?>
                    <span class="edit" onclick="edit_penduduk('<?= $str ?>')"><?= img('assets/images/icons/edit.png') ?></span>
                    <span class="delete" onclick="delete_penduduk('<?= $rows->penduduk_id ?>')"><?= img('assets/images/icons/delete.png') ?></span>
                </td>   
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<div id="paging"><?= $paging ?></div>
</div><br/>
