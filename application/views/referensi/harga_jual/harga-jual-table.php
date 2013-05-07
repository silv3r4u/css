<script>
$("#table").tablesorter({sortList:[[0,0]]});
</script>    
        <?= form_open('referensi/harga_jual_update', 'id=form_harga_jual2') ?>
        <table class="sortable form-inputan" width="100%" id="table">
            <thead>
            <tr>
                <th class="nosort" width="10%"><h3>#</h3></th>
                <th width="80%"><h3>Nama Barang</h3></th>
                <!--<th class="sortright"><h3>HNA (Rp.)</h3></th>
                <th class="nosort"><h3>Margin (%)</h3></th>
                <th class="nosort"><h3>Diskon (%)</h3></th>
                <th class="sortright"><h3>Harga Jual (Rp.)</h3></th>-->
                <th class="nosort" width="10%"><h3>Stok Minimal</h3></th>
            </tr>
            </thead>
            <tbody>
        <?php
        $jumlah = 0;
        foreach ($barang as $key => $data) {
        //$harga_jual = ($data->hna+($data->hna*$data->margin/100)) - (($data->hna+($data->hna*$data->margin/100))*($data->diskon/100));
        ?>
            <tr id="listdata<?= $key ?>" class="tr_row <?= ($key%2==0)?'odd':'even' ?>">
                <td align="center"><?= form_checkbox('pb[]', $data->id, FALSE, 'class=check id=check'.$key.'')  ?></td>
                <td><?= $data->barang ?> <?= $data->kekuatan ?>  <?= $data->pabrik ?></td>
                <!--<td align="right"><?= inttocur($data->hna) ?></td>
                <td align="center"><?= $data->margin ?></td>
                <td align="center"><?= $data->diskon ?></td>
                <td align="right"><?= inttocur($harga_jual) ?></td>-->
                <td align="center"><?= $data->stok_minimal ?></td>
            </tr>
        <?php 
        $jumlah++;
        } 
        ?>
                </tbody>
        </table>

        <?= form_close() ?>
<?= $this->load->view('paging') ?>
<div id="paging"><?= $paging ?></div>