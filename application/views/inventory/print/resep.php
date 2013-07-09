<?php
//header_excel("salin-resep-".date("d-m-Y").".xls");
?>
<head>
<style>
    * { font-family: Calibri; font-size: 11px; font-weight: bold; -webkit-print-color-adjust:exact; }
</style>

<script type="text/javascript">
    function cetak() {  		
        window.print();
        window.close();
    }
</script> 
</head>
<body onload="cetak()">
<?php
foreach ($resep as $rows);

?>
<table class="content-printer" style="width: 100%">
<tr>
<td>

<table width="100%" style="border-bottom: 1px solid #000; font-size: 12px;" width="100%">
    <tr><td colspan="4" align="center" style="text-transform: uppercase"><?= $datas->nama ?></td> </tr>
    <tr><td colspan="4" align="center"><?= $datas->alamat ?> <?= $datas->kabupaten ?></td> </tr>
    <tr><td colspan="4" align="center">Telp. <?= $datas->telp ?>,  Fax. <?= $datas->fax ?>, Email <?= $datas->email ?></td> </tr>
    <tr><td colspan="4" align="center">APA: <?= isset($apa->nama)?$apa->nama:NULL ?>, SIPA: <?= isset($apa->sip_no)?$apa->sip_no:NULL ?></td></tr>
</table>
<h2 style="text-align: center">SALINAN RESEP</h2>
<table width="100%">
    <tr><td>No. Resep: </td><td colspan="3" align="left"><?= $rows->id ?></td> </tr>
    <tr><td>Dari Dokter: </td><td colspan="3"><?= $rows->dokter ?></td> </tr>
    <tr><td>Tanggal: </td><td colspan="3" align="left"><?= datetime($rows->waktu) ?></td> </tr>
    <tr><td>Pro: </td><td colspan="3"><?= $rows->pasien ?></td> </tr>
    <tr><td>Usia:</td><td colspan="3"><?= ($rows->lahir_tanggal=='0000-00-00')?'':hitungUmur($rows->lahir_tanggal) ?></td> </tr>
</table>

<!--Iter: <?= $rows->iter ?>-->
<?php
    foreach ($resep as $key => $rows) {
    ?>
    <table width="100%"><tr><td colspan="4">Iter: <?= ($rows->iter) ?> x R/</td></tr></table>
    <table width="100%">
    <?php
    $detail = $this->m_resep->detail_data_resep_load_data($rows->id_rr)->result();
    foreach ($detail as $key => $data) { ?>

        <tr><td colspan="4">&nbsp;&nbsp;&nbsp;&nbsp;<?= $data->barang ?> <?= ($data->kekuatan != '1')?$data->kekuatan:null ?>  <?= $data->satuan ?> <?= $data->sediaan ?> <?= ($data->isi==1)?'':'@'.$data->isi ?> <?= $data->satuan_terkecil ?></td></tr>
    <?php } ?>
        <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<?= $rows->pakai_aturan ?></td></tr>
    </table>
<?php
    
    $data = $this->db->query("select rr.*, sum(rr.resep_r_jumlah) as data1, sum(rr.tebus_r_jumlah) data2 from resep r join resep_r rr on (r.id = rr.resep_id) where rr.id = '".$rows->id_rr."'")->row();
        if (($data->data1 - $data->data2) == 0) {
            $then = "Detur Originale";
        }
        if (($data->data1 - $data->data2) == $data->data1) {
            $then = "Nedet";
        }
        if (($data->data1 - $data->data2) > 0) {
            $then = "Det ".($data->data2);
        }
        ?>
    <table width="100%"><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;No. <?= $data->resep_r_jumlah ?> <?= $then ?></td></tr></table>
<?php    }
//}

?>
<?php
$sip = $this->db->query("select * from dinamis_penduduk where penduduk_id = '".$this->session->userdata('id_user')."'")->row();
?>
<table width="100%" style="border-top: 1px solid #000; padding-top: 10px; margin-top: 10px;">
    <tr><td align="left" colspan="2">Cap Apotek</td><td colspan="2" align="right"><?= $datas->kabupaten ?>, <?= date("d F Y") ?></td> </tr>
    <tr><td colspan="4" align="right">PCC</td> </tr>
    <tr><td colspan="4" align="right">APA</td> </tr>
    <tr><td colspan="4" align="right">&nbsp;</td> </tr>
    <tr><td colspan="4" align="right">&nbsp;</td> </tr>
    <tr><td colspan="4" align="right"><?= $this->session->userdata('nama') ?></td> </tr>
    <tr><td colspan="4" align="right"><?= $sip->sip_no ?></td> </tr>
</table>
</td>
    </tr>
</table>
</body>