<head>
<script type="text/javascript" src="<?= base_url('assets/js/jquery-print.min.js') ?>"></script>
<link rel="stylesheet" href="<?= base_url('assets/css/base.css') ?> "/>
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
</head>
<div class="layout-printer">
<center style="font-weight: bold; font-size: 16px;">LAPORAN STATISTIKA RESEP DAN PELAYANAN OBAT GENERIK BERLOGO<br/>Tanggal <?= $_GET['awal'] ?> s.d <?= $_GET['akhir'] ?></center>

<table class="content-printer" width="100%">
    <tr><td style="font-size: 16px;">Nama:</td><td style="font-size: 16px;"><?= $data->nama ?></td> </tr>
    <tr><td style="font-size: 16px;">Nomor S.I.A:</td><td style="font-size: 16px;"><?= $data->sia ?></td> </tr>
    <tr valign="top"><td style="font-size: 16px;">Alamat:</td><td style="font-size: 16px;"><?= $data->alamat ?><br/><?= $data->kabupaten ?></td> </tr>
</table><br/>
I. STATISTIKA RESEP
<?php
$row = $this->db->query("select count(id) as total_resep from resep where date(waktu) between '".  date2mysql($_GET['awal'])."' and '".date2mysql($_GET['akhir'])."'")->row();
$row2= $this->db->query("select count(rr.id) as total_no_r from resep r join resep_r rr on (r.id = rr.resep_id) where date(r.waktu) between '".  date2mysql($_GET['awal'])."' and '".date2mysql($_GET['akhir'])."'")->row();
$row3= $this->db->query("select (sum(rrr.jual_harga)/".$row2->total_no_r.") as harga_rata2 from resep r join resep_r rr on (r.id = rr.resep_id) join resep_racik_r_detail rrr on (rr.id = rrr.r_resep_id) where date(r.waktu) between '".  date2mysql($_GET['awal'])."' and '".date2mysql($_GET['akhir'])."'")->row();
$row4= $this->db->query("select br.*
    from resep r 
    join resep_r rr on (r.id = rr.resep_id) 
    join resep_racik_r_detail rrr on (rr.id = rrr.r_resep_id) 
    join barang_packing b on (b.id = rrr.barang_packing_id)
    join barang br on (br.id = b.barang_id)
    join obat o on (o.id = b.id)
    where o.generik = 'Generik' and date(r.waktu) between '".date2mysql($_GET['awal'])."' and '".date2mysql($_GET['akhir'])."' group by rrr.r_resep_id")->num_rows();
$row5= $this->db->query("select (sum(rrr.jual_harga)/".$row4.") as harga_rata2
    from resep r 
    join resep_r rr on (r.id = rr.resep_id) 
    join resep_racik_r_detail rrr on (rr.id = rrr.r_resep_id) 
    join barang_packing b on (b.id = rrr.barang_packing_id)
    join barang br on (br.id = b.barang_id)
    join obat o on (o.id = b.id)
    where o.generik = 'Generik' and date(r.waktu) between '".date2mysql($_GET['awal'])."' and '".date2mysql($_GET['akhir'])."' group by rrr.r_resep_id")->row();

?>
<table width="100%" class="list-data-printer">
  <tr>
    <th colspan="3">NAMA RESEP SELURUHNYA <br/> (NAMA DAGANG DAN GENERIK BERLOOGO)</th>
    <th colspan="2">RESEP GENERIK BERLOGO</th>
    <th>PROSENTASE R/ GENERIK BERLOGO DIBANDING R/ SELURUNYA</th>
  </tr>
  <tr>
    <th>JUMLAH LEMBAR</th>
    <th>JUMLAH RESEP * (R/)</th>
    <th>HARGA RATA-RATA PER R/</th>
    <th>JUMLAH RESEP ** (R/)</th>
    <th>HARGA RATA-RATA PER R/</th>
    <th>* - ** x 100%</th>
  </tr>
  <tr>
    <td align="center"><?= $row->total_resep ?></td>
    <td align="center"><?= $row2->total_no_r ?></td>
    <td align="center"><?= isset($row3->harga_rata2)?rupiah($row3->harga_rata2):NULL ?></td>
    <td align="center"><?= isset($row4)?$row4:NULL ?></td>
    <td align="center"><?= (isset($row2->total_no_r) and isset($row5->harga_rata2))?rupiah(($row2->total_no_r==0)?'0':$row5->harga_rata2):'0' ?></td>
    <td align="center"><?= ($row2->total_no_r==0)?'0':($row4/$row2->total_no_r)*100 ?> %</td>
  </tr>
</table><br/>
II. OBAT GENERIK YANG MENGALAMI KEKOSONGAN
<table width="100%" class="list-data-printer">
  <tr>
    <th rowspan="2">NO.</th>
    <th colspan="2">TANGGAL KEKOSONGAN SAMPAI TERSEDIA</th>
    <th rowspan="2">NAMA SEDIAAN</th>
    <th rowspan="2">KEMASAN</th>
  </tr>
  <tr>
    <th>AWAL</th>
    <th>AKHIR</th>
  </tr>
  <?php
  
  foreach ($statistika_resep as $key => $data) {
      $akhir = $this->db->query("select * from transaksi_detail 
          where barang_packing_id = '".$data->barang_packing_id."' and id > '".$data->id."'
              and sisa > 0 and date(waktu) between '".date2mysql($_GET['awal'])."' and '".date2mysql($_GET['akhir'])."'");
  ?>
  <tr>
    <td align="center"><?= ++$key ?></td>
    <td align="center"><?= datefmysql($data->awal) ?></td>
    <td align="center"><?= datetimefmysql($akhir->waktu) ?></td>
    <td><?= $data->sediaan ?></td>
    <td><?= $data->satuan ?></td>
  </tr>
  <?php } ?>
</table><br/>
III. HAMBATAN
<div id="hambatan"><textarea name="hambatan" id="hambatan" cols="44" rows="3" style="font-size: 16px;"></textarea></div>
IV. LAIN-LAIN
<ol type="1">
    <li>POLA PERHTUNGAN OBAT GENERIK
        <ol type="a">
            <li>OBAT RACIKAN</li>
            <li>OBAT NON RACIKAN</li>
        </ol>
    </li>
    <li>PERGANTIAN OBAT GENERIK BERLOGO
        <ol>
            <li>ALASAN</li>
        </ol>
    </li>
    <li>OBAT GENERIK BERLOGO YANG TERSEDIA</li>
</ol>
<?= form_button('Cetak', 'Cetak', 'id=cetak onClick=PrintElem(".layout-printer")') ?>
</div>
<?php die ?>