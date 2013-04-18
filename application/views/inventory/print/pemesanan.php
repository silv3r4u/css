<?php
header_excel("surat-pemesanan-".date("d-m-Y").".xls");
foreach ($pemesanan as $rows);
/*if ($apt['logo_file_nama'] != '') {
    $img = "<img src='".app_base_url('assets/images/company/'.$apt['logo_file_nama'])."' width='100px' />";
} else {
    $img = "<img src='".app_base_url('assets/images/company')."/apotek.jpg' width='100px' />";
}*/
?>
<style>
    * { font-size: 16px; }
    table td {font-size: 16px;}
    .list-data-excel { border-top: 1px solid #000; border-left: 1px solid #000; border-spacing: 0; border-bottom: 1px solid #000; }
    .list-data-excel th { border-bottom: 1px solid #000; border-right: 1px solid #000;  }
    .list-data-excel td { border-bottom: 1px solid #f4f4f4; border-right: 1px solid #000;  }
</style>
<table border="1" bgcolor="#e6faff">
<tr>
<td>

<table width="100%" style="color: #ffffff" bgcolor="#31849b">
    <tr>
    <td rowspan="3" style="width: 70px"><img src="<?= base_url('assets/images/company/'.$apt->logo_file_nama) ?>" width="70px" height="70px" /></td>    
    <td colspan="3" align="center"><b><?= strtoupper($apt->nama) ?></b></td> </tr>
    <tr><td colspan="3" align="center"><b><?= strtoupper($apt->alamat) ?> <?= strtoupper($apt->kabupaten) ?></b></td> </tr>
    <tr><td colspan="3" align="center" style="padding-right: 70px"><b>TELP. <?= $apt->telp ?>,  FAX. <?= $apt->fax ?>, EMAIL <?= $apt->email ?></b></td> </tr>
</table>
<?php
if ($rows->perundangan == 'Psikotropika') {

//$detail = $this->db->query("select * from dinamis_penduduk where penduduk_id = '".$users->id."' order by id desc limit 1")->row();
?>
<table width="100%">
    <tr><td colspan="4" align="center">SURAT PESANAN PSIKOTROPIKA</td></tr>
</table>
<table><tr><td colspan="4">Yang bertandatangan di bawah ini:</td></tr></table>
<table width="100%">
    <tr><td>Nama:</td><td><?= $detail->nama ?></td><td colspan="2">Kepada: </td></tr>
    <tr><td>Alamat:</td><td><?= isset($detail->alamat)?$detail->alamat:null ?></td><td colspan="2">Yth. <?= $rows->suplier ?></td></tr>
    <tr><td>Jabatan:</td><td><?= isset($detail->jabatan)?$detail->jabatan:null ?></td><td colspan="2">Di <?= $rows->alamat ?> <?= $rows->kabupaten ?></td></tr>
</table>

<table>
    <tr> </tr>
    <tr> </tr>
    <tr> </tr>
</table>
<table><tr><td colspan="4">Mohon dikirim obat-obatan untuk keperluan Rumah Sakit kami sebagai berikut:</td></tr></table>
<div class="data-list">
    <table width="100%" class="list-data-excel">
        <tr bgcolor="#bdb76b">
            <th>No</th>
            <th colspan="2">Packing Barang</th>
            <th>Jumlah</th>
            
        </tr>
        <?php
        foreach ($pemesanan as $key => $data) {
        if ($data->id_obat == null) {
            $packing = $data->barang." ".$data->pabrik." @".(($data->isi==1)?'':$data->isi)." ". $data->satuan_terkecil;
        } else {
            $packing = $data->barang." ".(($data->kekuatan == '1')?'':$data->kekuatan)." ".$data->satuan." ".$data->sediaan." ".(($data->generik == 'Non Generik')?'':$data->pabrik)." @ ".(($data->isi==1)?'':$data->isi)." ".$data->satuan_terkecil;
        }
        
        ?>
        <tr bgcolor="<?= ($key%2==0)?'#ffffe0':'#ffffff' ?>">
            <td align="center"><?= ++$key ?></td>
            <td colspan="2"><?= $packing ?></td>
            <td align="center"><?= $data->masuk ?></td>
            
        </tr>
        <?php } ?>
    </table>
</div>
<?php
} else { ?>
<table style="margin-bottom: 5px;"><tr><td colspan="4">NO. SP: <?= $_GET['id'] ?></td></tr></table>
<table width="100%">
    <tr><td colspan="4">Kepada: </td></tr>
    <tr><td colspan="4">Yth. <?= $rows->suplier ?></td></tr>
    <tr><td colspan="4">Di <?= $rows->alamat ?> <?= $rows->kabupaten ?></td></tr>
</table>

<table><tr><td colspan="4">Mohon dikirim obat-obatan untuk keperluan Apotek kami sebagai berikut:<br/></td></tr></table>

    <table class="list-data-excel" width="100%">
        <tr bgcolor="#bdb76b">
            <th>No</th>
            <th>Packing Barang</th>
            <th>Jumlah</th>
            <th>Keterangan</th>
        </tr>
        <?php
        foreach ($pemesanan as $key => $data) {
        ?>
        <tr bgcolor="<?= ($key%2==0)?'#ffffe0':'#ffffff' ?>">
            <td align="center"><?= ++$key ?></td>
            <td><?= $data->barang." ".(($data->kekuatan == '1')?'':$data->kekuatan)." ". $data->satuan." ". $data->sediaan."".(($data->generik == 'Non Generik')?'':$data->pabrik)." @ ".(($data->isi==1)?'':$data->isi)."". $data->satuan_terkecil.""; ?></td>
            <td align="center"><?= $data->masuk ?></td>
            <td></td>
        </tr>
        <?php } ?>
    </table>
<?php } ?>
<table align="right" width="100%">
    <tr><td align="right" colspan="4"><?= $apt->kabupaten ?>, <?= date("d F Y") ?></td> </tr>
    <tr><td align="right" colspan="4"></td> </tr>
    <tr><td colspan="4">&nbsp;</td> </tr>
    <tr><td colspan="4">&nbsp;</td> </tr>
    <tr><td align="right" colspan="4"><?= isset($manager->nama)?$manager->nama:NULL ?></td> </tr>
    <tr><td align="right" colspan="4"><?= isset($manager->sip_no)?$manager->sip_no:NULL ?></td> </tr>
</table>
</td>
    </tr>
</table>