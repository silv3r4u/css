<script type="text/javascript">
    $(function() {
        $("#table").tablesorter({sortList:[[0,0]]});
    })
</script>
<?php if (isset($key)): ?>
    <div id="pencarian">
        <br/>
        <h3>
            Pencarian dengan kata kunci "<?= $key ?>" 
        </h3>
    </div>
<?php endif; ?>
<table class="sortable" width="100%" id="table">
    <thead>
    <tr>
        <th width="10%" class="nosort"><h3>No.</h3></th>
        <th width="30%"><h3>Nama</h3></th>
        <th width="40%"><h3>Alamat</h3></th>
        <th width="10%"><h3>Jenis</h3></th>
        <th width="10%" class="nosort"><h3>Aksi</h3></th>
    </tr>
    </thead>
    <tbody>
    <?php if ($instansi != null): ?>
        <?php foreach ($instansi as $key => $rows): ?>
        <?php $str = $rows->id . "#" . $rows->nama . "#" . $rows->alamat . "#" . $rows->kelurahan_id . "#" . $rows->kelurahan . "#" . $rows->telp . "#" . $rows->fax . "#" . $rows->email . "#" . $rows->website . "#" . $rows->relasi_instansi_jenis_id . "#" . $rows->diskon_penjualan; ?>
            <tr class="<?= ($key % 2 == 1) ? 'even' : 'odd' ?>">
                <td align="center"><?= $rows->nomor ?></td>
                <td><?= $rows->nama ?></td>
                <td><?= $rows->alamat.' '.$rows->kabupaten ?></td>
                <td><?= $rows->jenis ?></td>
                <td align="center"> 
                    <a class="edit" onclick="edit_instansi('<?= $str ?>')">&nbsp;</a>
                    <a class="delete" onclick="delete_instansi('<?= $rows->id ?>')">&nbsp;</a>
                </td>   
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<div id="paging"><?= $paging ?></div>