<script>
$("#table").tablesorter({sortList:[[0,0]]});
</script>
<table cellpadding="0" cellspacing="0" class="sortable" id="table" width="100%">
    <thead>
    <tr>
        <th width="10%" class="nosort"><h3>No.</h3></th>
        <th width="40%"><h3>Perusahaan</h3></th>
        <th width="40%"><h3>nama produk</h3></th>
        <th width="10%" class="nosort"><h3>Aksi</h3></th>
    </tr>
    </thead>
    <tbody>
    <?php
    //$asuransi = asuransi_produk_muat_data();
    if (count($asuransi) == 0) {
        for ($key = 1; $key <= 2; $key++) {
            ?>
            <tr class="<?= ($key % 2) ? "even" : "odd" ?>">
                <td align="center">&nbsp;</td>
                <td></td>
                <td></td>
                <td class="aksi">

                </td>
            </tr>    
            <?php
        }
    } else {
        foreach ($asuransi as $key => $prov) {
            ?>
            <tr class="<?= ($key % 2) ? "even" : "odd" ?>">
                <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
                <td><?= $prov->prsh ?></td>
                <td><?= $prov->nama ?></td>
                <td class="aksi" align="center">
                    <a class="edit" onclick="edit_produk_asuransi('<?= $prov->id ?>','<?= $prov->nama ?>','<?= $prov->id_ap ?>','<?= $prov->prsh ?>','<?= $prov->reimbursement ?>')"></a>
                    <a class="delete" onclick="delete_produk_asuransi('<?= $prov->id ?>')"></a>
                </td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>
<br/>
<?= $this->load->view('paging') ?>
<!--<div id="paging"><?= $paging ?></div>-->