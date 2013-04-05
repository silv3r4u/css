<?php
header_excel("pmr-".$_GET['nama'].".xls");
$data = $this->db->query("select * from dinamis_penduduk dp join penduduk p on (p.id = dp.penduduk_id) where dp.kk_no = '".$rows->kk_no."' and dp.posisi = 'Ibu'")->row();
?>
<style>
    * { font-family: "Lucida Sans Unicode"; font-size: 16px; }
    table td {font-family: "Lucida Sans Unicode"; font-size: 16px;}
    .list-data-excel { border-top: 1px solid #000; border-left: 1px solid #000; border-spacing: 0; border-bottom: 1px solid #000; }
    .list-data-excel th { border-bottom: 1px solid #000; border-right: 1px solid #000;  }
    .list-data-excel td { border-bottom: 1px solid #f4f4f4; border-right: 1px solid #000;  }
</style>
<table border="1" bgcolor="#e6faff">
<tr>
<td>
<table width="100%" style="font-family: 'Lucida Sans Unicode'; color: #ffffff" bgcolor="#31849b">
    <tr><td colspan="12" align="center"><b><?= strtoupper($apt->nama) ?></b></td> </tr>
    <tr><td colspan="12" align="center"><b><?= strtoupper($apt->alamat) ?> <?= strtoupper($apt->kelurahan) ?></b></td> </tr>
    <tr><td colspan="12" align="center"><b>TELP. <?= $apt->telp ?>,  FAX. <?= $apt->fax ?>, EMAIL <?= $apt->email ?></b></td> </tr>
</table>
    <table width="100%">
        <tr valign="top">
            <td width="50%" colspan="3">
            <table width="100%">
                <tr><td width="40%">Nama Pasien:</td><td><?= $rows->nama ?></td></tr>
                <tr><td>Alamat Pasien:</td><td><?= $rows->alamat ?></td></tr>
                <tr><td>Nama Ibu Kandung:</td><td><?= isset($data->nama)?$data->nama:'-' ?></td></tr>
                <tr><td>No. Telepon:</td><td><?= $rows->telp ?></td></tr>
                <tr><td>Pekerjaan:</td><td><?= $rows->pekerjaan ?></td></tr>
            </table>
            </td>
            <td width="50%" colspan="9">
            <table width="100%">
                <tr><td width="50%">Jenis Kelamin:</td><td><?= $rows->gender ?></td></tr>
                <tr><td>Umur:</td><td><?= createUmur($rows->lahir_tanggal) ?></td></tr>
                <tr><td>TB/BB:</td><td></td></tr>
                <tr><td>Gol. Darah:</td><td><?= $rows->darah_gol ?></td></tr>
            </table>
            </td>
        </tr>
    </table>
    <table width="100%" class="list-data-excel" width="100%" border="1">
        <tr bgcolor="#bdb76b">
            <th>Tanggal</th>
            <th>Status Resep</th>
            <th>Packing Barang</th>
            <th>Kekuatan Obat</th>
            <th>Dosis</th>
            <th>Sediaan</th>
            <th>Cara Pemakaian</th>
            <th>Jumlah</th>
            <th>Dokter</th>
            <th>SIP</th>
            <th>Alamat</th>
            <th>Keterangan</th>
        </tr>
        <?php
        foreach ($list_data as $key => $data) { ?>
        <tr valign="top" bgcolor="<?= ($key%2==0)?'#ffffe0':'#ffffff' ?>">
            <td><?= datetime($data->waktu) ?></td>
            <td align="center"><?= (count($list_data) > 1)?'Lama':'Baru' ?></td>
            <td><?= $data->barang ?> <?= $data->kekuatan ?>  <?= $data->satuan ?> <?= $data->sediaan ?> @ <?= ($data->isi==1)?'':$data->isi ?> <?= $data->satuan_terkecil ?></td>
            <td align="center"><?= $data->kekuatan ?></td>
            <td align="center"><?= $data->dosis_racik ?></td>
            <td><?= $data->sediaan ?></td>
            <td align="center"><?= $data->pakai_aturan ?></td>
            <td align="center"><?= $data->resep_r_jumlah ?></td>
            <td><?= $data->dokter ?></td>
            <td><?= $data->sip_no ?></td>
            <td><?= $data->alamat ?></td>
            <td>...</td>
        </tr>
        <?php } ?>
    </table>
</td>
</tr>
</table>