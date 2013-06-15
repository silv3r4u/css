<script type="text/javascript">
    $(".sortable").tablesorter();
</script>
<?php 
if (isset($key)): ?>
    <div id="pencarian">
        <h3>
            Pencarian dengan kata kunci "<?= $key ?>" 
        </h3>
    </div>
<?php endif; ?>
<table class="sortable" id="table" width="100%">
    <thead>
    <tr>
        <th class="nosort" width="5%"><h3>No.</h3></th>
        <th width="20%"><h3>Nama</h3></th>
        <th width="40%"><h3>Alamat</h3></th>
        <th width="10%"><h3>Jenis</h3></th>
        <?php if (isset($key) and ($key == 'Supplier')) { ?>
        <th width="10%"><h3>Diskon Supplier (%)</h3></th>
        <?php } ?>
        <th width="5%" class="nosort"><h3>Aksi</h3></th>
    </tr>
    </thead>
    <tbody>
    <?php if ($instansi != null): ?>
        <?php foreach ($instansi as $no => $rows): ?>
        <?php $str = $rows->id . "#" . $rows->nama . "#" . preg_replace('/^\s+|\n|\r|\s+$/m', '',$rows->alamat) . "#" . $rows->kabupaten_id . "#" . $rows->kabupaten . "#" . $rows->telp . "#" . $rows->fax . "#" . $rows->email . "#" . $rows->website . "#" . $rows->relasi_instansi_jenis_id . "#" . $rows->diskon_penjualan."#".$rows->diskon_supplier; ?>
            <tr class="<?= ($no % 2 == 1) ? 'even' : 'odd' ?>">
                <td align="center"><?= $rows->nomor ?></td>
                <td><?= $rows->nama ?></td>
                <td><?= $rows->alamat.' '.$rows->kabupaten ?></td>
                <td><?= $rows->jenis ?></td>
                <?php if (isset($key) and ($key == 'Supplier')) { ?>
                <td align="center"><?= $rows->diskon_supplier ?></td>
                <?php } ?>
                <td class="aksi"> 
                    <span class="edit" onclick="edit_instansi('<?= $str ?>')"><?= img('assets/images/icons/edit.png') ?></span>
                    <span class="delete" onclick="delete_instansi('<?= $rows->id ?>')"><?= img('assets/images/icons/delete.png') ?></span>
                </td>   
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<br/>
<div id="paging"><?= $paging ?></div>