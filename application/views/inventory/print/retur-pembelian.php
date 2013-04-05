<?php
require_once 'app/lib/common/master-data.php';
require_once 'app/lib/common/functions.php';

header_excel("retur-pembelian-".date("d-m-Y").".xls");
$pembelian = pembelian_data_muat_data($_GET['id_pembelian']);
$sup = detail_retur_pembelian_muat_data($_GET['id']);
foreach ($sup as $rows);
foreach ($pembelian as $attr);

$apt = informasi_apotek();
/*if ($apt['logo_file_nama'] != '') {
    $img = "<img src='".app_base_url('assets/images/company/'.$apt['logo_file_nama'])."' width='100px' />";
} else {
    $img = "<img src='".app_base_url('assets/images/company')."/apotek.jpg' width='100px' />";
}*/
?>
<table border="1">
<tr>
<td>
<table style="border-bottom: 1px solid #000;">
    <tr><td colspan="4" align="center" style="text-transform: uppercase">Apotek <?= $apt['nama'] ?></td> </tr>
    <tr><td colspan="4" align="center"><?= $apt['alamat'] ?> <?= $apt['kelurahan'] ?></td> </tr>
    <tr><td colspan="4" align="center">Telp. <?= $apt['telp'] ?>,  Fax. <?= $apt['fax'] ?>, Email <?= $apt['email'] ?></td> </tr>
</table>
    <br/>
<?php

$user = data_user_muat_data($_SESSION['id_user']);
foreach ($user as $users);
$accout= data_account_muat_data($users['id']);
?>

<table>
    <tr><td>No. Bukti Retur</td><td><?= $_GET['id'] ?></td> </tr>
    <tr><td>Nama Suplier PBF</td><td><?= $attr['suplier'] ?></td> </tr>
    <tr><td>No. Faktur</td><td><?= $attr['dokumen_no'] ?></td> </tr>
    <tr><td>Tanggal Faktur</td><td><?= $attr['dokumen_tanggal'] ?></td> </tr>
    <tr><td>No. SP</td><td><?= $attr['pemesanan_id'] ?></td> </tr>
</table>

<div class="data-list">
    <table border="1">
        <tr>
            <th>No</th>
            <th>Packing Barang</th>
            <th>Jumlah</th>
            <th>Rencana Bentuk Pengembalian</th>
        </tr>
        <?php
        foreach ($sup as $key => $data) {
        ?>
        <tr>
            <td align="center"><?= ++$key ?></td>
            <td><?= $data['barang'] ?> <?= $data['kekuatan'] ?>  <?= $data['satuan'] ?> <?= $data['sediaan'] ?> <?= $data['pabrik'] ?> @ <?= ($data['isi']==1)?'':$data['isi'] ?> <?= $data['satuan_terbesar'] ?></td>
            <td align="center"><?= $data['keluar'] ?></td>
            <td align="center">...</td>
            
        </tr>
        <?php } ?>
    </table>
</div>

<table align="left">
    <tr><td align="center"><?= $apt['kabupaten'] ?>, <?= date("d F Y") ?></td> </tr>
    <tr><td align="center">Yang Menyerahkan</td> </tr>
    <tr><td>&nbsp;</td> </tr>
    <tr><td>&nbsp;</td> </tr>
    <tr><td align="center"><?= $users['nama'] ?></td> </tr>
    <tr><td align="center"><?= isset($accout[0]['sip_no'])?$accout[0]['sip_no']:null ?></td> </tr>
</table>
<table align="right">
    <tr><td align="center"><?= $apt['kabupaten'] ?>, <?= date("d F Y") ?></td> </tr>
    <tr><td align="center">Yang Menerima</td> </tr>
    <tr><td>&nbsp;</td> </tr>
    <tr><td>&nbsp;</td> </tr>
    <tr><td></td> </tr>
    <tr><td>(....................................)</td> </tr>
</table>
</td>
    </tr>
</table>
<?php die;
?>