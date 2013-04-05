<div class="data-list">
    <table class="tabel" width="100%">
        <tr>
            <th width="10%">No. Kunjungan</th>
            <th width="15%">Tgl Kunjungan</th>
            <th width="10%">No. Pasien</th>
            <th width="20%">Nama Pasien</th>
            <th width="15%">Umur</th>
            <th width="10%">Total</th>
            <th width="10%">Sisa</th>
        </tr>
        <?php
        foreach ($list_data as $key => $data) { 
            $tb = $this->m_billing->data_kunjungan_muat_data_total_barang($data->no_daftar);
            $tj = $this->m_billing->data_kunjungan_muat_data_total_jasa($data->no_daftar);
            ?>
        <tr class="<?= ($key%2==0)?'even':'odd' ?>">
            <td align="center"><?= $data->no_daftar ?></td>
            <td align="center"><?= datetime($data->arrive_time) ?></td>
            <td align="center"><?= $data->no_rm ?></td>
            <td><?= $data->pasien ?></td>
            <td align="center"><?= hitungUmur($data->lahir_tanggal) ?></td>
            <td align="right"><?= rupiah($tj->total_jasa+$tb->total_barang) ?></td>
            <td align="right"><?= ($data->sisa == NULL)?rupiah($tj->total_jasa+$tb->total_barang):rupiah($data->sisa) ?></td>
        </tr>
        <?php } ?>
    </table>
</div>
<?php die; ?>