<div class="kegiatan">
    <h1><?= $title ?></h1>
    <div class="data-list">
        <?= form_button(null, 'Pesan yang dipilih', 'id=pesancheck') ?>
        <table class="sortable" id="table" width="100%">
            <thead>
            <tr>
                <th class="nosort"><h3><?= form_checkbox('check', null, FALSE, 'id=checkall') ?></h3></th>
                <th class="nosort"><h3>No.</h3></th>
                <th><h3>Nama Barang</h3></th>
                <th><h3>Supplier</h3></th>
                <th><h3>Expired Date</h3></th>
                <th><h3>Sisa Stok</h3></th>
                <th><h3>Stok Minim</h3></th>
                <th><h3>Aksi</h3></th>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($list_data as $key => $data) { ?>
                <tr>
                    <td align="center"><?= form_checkbox('id_pb[]', $data->barang_packing_id, FALSE, 'class=check') ?></td>
                    <td align="center"><?= ++$key ?></td>
                    <td><?= $data->barang ?> <?= $data->kekuatan ?>  <?= $data->satuan ?> <?= $data->sediaan ?> <?= $data->pabrik ?> @ <?= ($data->isi==1)?'':$data->isi ?> <?= $data->satuan_terkecil ?></td>
                    <td></td>
                    <td><?= datefmysql($data->ed) ?></td>
                    <td><?= $data->sisa ?></td>
                    <td><?= $data->stok_minimal ?></td>
                    <td align="center">Pesan</td>
                </tr>
                <?php } ?>
            </tbody>
        </tabel>
    </div>
</div>