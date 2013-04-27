<?php
    header_excel('pmr-'.$rows->nama.'.xls');
?>
<table width="100%" style="border: 1px solid #000; background: #f4f4f4;">
    <tr><td>Nomor PMR:</td><td align="left" colspan="2"><?= $rows->id_penduduk ?></td><td>Usia:</td><td colspan="2"><?= hitungUmur($rows->lahir_tanggal) ?></td></tr>
    <tr><td>Nama Pasien:</td><td colspan="2"><?= $rows->nama ?></td><td>No. Telepon:</td><td align="left colspan="2""><?= $rows->telp ?></td></tr>
    <tr><td>Tempat tanggal lahir:</td><td colspan="2"><?= $rows->kabupaten ?></td><td>Pekerjaan / Aktivitas:</td><td colspan="2"><?= $rows->pekerjaan ?></td></tr>
    <tr><td>Alamat:</td><td colspan="2"><?= $rows->alamat ?></td><td></td><td colspan="2"></td></tr>
</table>
<br/>
<table border="1">
    <tr>
        <th>Tanggal</th>
        <th>RPD</th>
        <th>RPK</th>
        <th>Pengobatan Sekarang</th>
        <th>Obat Herbal</th>
        <th>Alergi Obat</th>
        <th>Alergi Lain</th>
        <th>Dokter Langganan</th>
        <th>Merokok</th>
        <th>Konsumsi Alkohol</th>
    </tr>
    <?php foreach ($last as $key => $data) { ?>
    <tr>
        <td><?= indo_tgl($data->tanggal) ?></td>
        <td><?= $data->rpd ?></td>
        <td><?= $data->rpk ?></td>
        <td><?= $data->ps ?></td>
        <td><?= $data->oh ?></td>
        <td><?= $data->ao ?></td>
        <td><?= $data->dl ?></td>
        <td><?= $data->al ?></td>
        <td><?= $data->mk ?></td>
        <td><?= $data->ka ?></td>
    </tr>
    <?php } ?>
</table>
<br/>
<table border="1">
    <tr>
         <th>Tanggal</th>
        <th>Subjektif</th>
        <th>Suhu Badan</th>
        <th>Tekanan Darah</th>
        <th>Respiration Rate</th>
        <th>Nadi</th>
        <th>GDS</th>
        <th>Angka Kolesterol</th>
        <th>Asam Urat</th>
        <th>Assessment</th>
        <th>Saran Pengobatan</th>
        <th>Saran Non Farmakoterapi</th>
    </tr>
    <?php foreach ($lastp as $key => $val) { ?>
    <tr>
        <td><?= indo_tgl($val->tanggal) ?></td>
        <td><?= $val->subjektif ?></td>
        <td><?= $val->suhu_badan ?></td>
        <td><?= $val->tek_darah ?></td>
        <td><?= $val->res_rate ?></td>
        <td><?= $val->nadi ?></td>
        <td><?= $val->gds ?></td>
        <td><?= $val->angka_kolesterol ?></td>
        <td><?= $val->asam_urat ?></td>
        <td><?= $val->assessment ?></td>
        <td><?= $val->saran_pengobatan ?></td>
        <td><?= $val->saran_non_farmakoterapi ?></td>
    </tr>
    <?php } ?>
</table>