<script type="text/javascript" src="<?= base_url('assets/js/jquery-print.min.js') ?>"></script>
<script type="text/javascript">
    function PrintElem(elem) {
        $('#cetak').hide();
        $('textarea').css('border', 'none');
        Popup($(elem).printElement());
        $('#cetak').show();
    }

    function Popup(data) {
        //var mywindow = window.open('<?= $title ?>', 'Print', 'height=400,width=800');
        mywindow.document.write('<html><head><title> <?= $title ?> </title>');
        /*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');

        mywindow.print();
        mywindow.close();

        return true;
    }

</script>
<title><?= $title ?></title>
<div id="mydiv">
<center><b><?= $title ?></b></center>
<?php
foreach ($list_data as $rows);
?>
<div style="padding: 3px;">
    No. R/: <?= $rows->r_no ?>&nbsp; &nbsp; &nbsp; &nbsp; <?= (count($list_data) == 1)?null:$rows->tebus_r_jumlah.' Bungkus' ?><br/>
    <?php
        foreach ($list_data as $data) { ?>
            <?= $data->barang ?> <?= $data->kekuatan ?>  <?= $data->satuan ?> <?= $data->sediaan ?> <?= ($data->isi==1)?'':'@'.$data->isi ?> <?= $data->satuan_terkecil ?><br/>
    <?php }
    ?>
             
</div>
<table>
    <tr valign="top"><td> Aturan Pakai:</td><td><textarea id=""></textarea></td></tr>
</table>

<p align="center">
<span id="SCETAK"><input type="button" class="tombol" value="Cetak" id="cetak" onClick="PrintElem('#mydiv')"/></span>
</p>
</div>
<?php die; ?>