<script>
    $("#table").tablesorter({sortList:[[0,0]]});
</script>
<div class="data-list">  
<table class="sortable" id="table" width="100%">
    <thead>
        <th width="20%"><h3>Username</h3></th>
        <th width="70%"><h3>Nama</h3></th>
        <th width="10%" class="nosort"><h3>Aksi</h3></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($user as $key => $rows) : ?>
        <tr class="<?= ($key % 2 == 1) ? 'even' : 'odd' ?>">
            <td><?= $rows->username ?></td>
            <td><?= $rows->nama ?></td>
            <td class="aksi" align="center"> 
                <span class="edit" onclick="edit_user('<?= $rows->id ?>')"><?= img('assets/images/icons/edit.png') ?></span>
                <span class="delete" onclick="delete_user('<?= $rows->id ?>')"><?= img('assets/images/icons/delete.png') ?></span>
            </td>  
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<br/>
<?= $this->load->view('paging') ?>
<!--<div id="paging"><?= $paging ?></div>-->
</div>
