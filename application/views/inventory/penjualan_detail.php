<title><?= $title ?></title>
<script type="text/javascript" src="<?= base_url('assets/js/jquery-print.min.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $('#retur').click(function() {
            var id = $('#transaksi_id').html();
            $.get('<?= base_url('inventory/retur_penjualan') ?>/'+id+'?_'+Math.random(), function(data) {
                $('#loaddata').html(data);
            });
            $(this).closest("#result_detail").dialog().remove();
        });
        $('#deletion').click(function() {
            var ok=confirm('Anda yakin akan menghapus transaksi ini ?');
            if (ok) {
                var id = $('#transaksi_id').html();
                $.get('<?= base_url('inventory/penjualan_delete') ?>/'+id, function(data) {
                    if (data == true) {
                        alert_delete();
                    }
                },'json');
                $(this).closest("#result_detail").dialog().remove();
                $('#loaddata').load('<?= base_url('laporan/stok?'.generate_get_parameter($_GET)) ?>');
            } else {
                return false;
            }
        })
    })
    
    function PrintElem(elem) {
        $('#cetak').hide();
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
<div id="kegiatan">
    <h1 class="informasi"><?= $title ?></h1>
    <?php 
    foreach ($list_data as $key => $rows); 
    //$asuransi = $this->m_referensi->asuransi_kepersertaan_get_data($rows->pasien_penduduk_id);
    $biaya_apoteker = $this->m_inventory->biaya_apoteker_by_penjualan($rows->transaksi_id)->row();
    ?>
    <div class="data-input">
        <?= form_hidden('total', null, 'id=total_tagihan') ?>
        <?= form_hidden('jasa_apotek', null, 'id=jasa_total_apotek') ?>
        <div class="left_side_detail">
            <label>No.</label><span id="transaksi_id" class="label"><?= $rows->transaksi_id ?></span>
            <label>No. Resep </label><span class="label"><?= isset($rows->resep_id)?$rows->resep_id:'-' ?></span>
            <label>Waktu </label><span class="label"><?= datetime($rows->waktu) ?></span>
            <?php if (isset($rows->resep_id)) { ?>
            <label>Pasien</label><span id="pasien" class="label"><?= isset($rows->nama)?$rows->nama:'-' ?></span>
            <?php } else { ?>
            <label>Pembeli</label><span id="pasien" class="label"><?= isset($rows->pembeli)?$rows->pembeli:'-' ?></span>
            <?php } ?>
<!--            <label>Produk Asuransi</label><span id="asuransi" class="label">
                <?php
                foreach ($asuransi as $asu) {
                    echo $asu->nama." - ".$asu->polis_no."<br />";
                }?>
            </span>-->
            <label>PPN (%) </label><span id="ppn" class="label"><?= $rows->ppn ?></span>
        </div>
        <div class="right_side_detail">
            <label>Biaya Apoteker</label><span id="jasa-apt" class="label"><?= isset($biaya_apoteker->jasa)?rupiah($biaya_apoteker->jasa):'-' ?></span>
            <label>Sub Total</label><span id="total-tagihan" class="label"><?= rupiah($rows->total) ?></span>
            <label>PPN</label><span id="ppn-hasil" class="label"><?= $rows->ppn ?></span>
            <label>Total Diskon</label><span id="total-diskon" class="label"><?= $rows->total ?></span>
            <label>Total Tagihan</label><span id="total" class="label"></span>
        </div>
    </div>
        <table class="tabel" width="100%">
            <thead>
            <tr>
                <th>Barcode</th>
                <th width="40%">Kemasan Barang</th>
                <?php if (isset($_GET->id)) { ?>
                <th width="10%">ED</th>
                <?php } ?>
                <th width="15%">Harga Jual</th>
                <th width="7%">Diskon</th>
                <th width="10%">Jumlah</th>
                <th width="10%">Sub Total</th>
            </tr>
            </thead>
            <tbody>
                <?php
                $total_diskon = 0;
				$total = 0;
                foreach ($list_data as $key => $data) {
                    $hjual = ($data->hna*($data->margin/100))+$data->hna;
                    $subtotal = ($hjual - ($hjual*($data->diskon/100)))*$data->keluar;
                ?>
                <tr class="<?= ($key%2==0)?'odd':'even' ?> tr_row">
                    <td align="center"><?= $data->barcode ?></td>
                    <td><?= $data->barang." ".(($data->kekuatan != '1')?$data->kekuatan:null)." ". $data->satuan." ". $data->sediaan." ". $data->pabrik."@ ".(($data->isi==1)?'':$data->isi)." ".$data->satuan_terkecil ?></td>
                    <?php if (isset($_GET->id)) { ?>
                    <td align="center"><?= datefmysql($data->ed) ?></td>
                    <?php } ?>
                    <td align="right" id="hj<?= $key ?>"><?= inttocur($hjual) ?></td>
                    <td align="center" id="diskon<?= $key ?>"><?= $data->diskon ?></td>
                    <td align="center" id="jl<?= $key ?>"><?= $data->keluar ?></td>
                    <td align="right"><?= inttocur($subtotal) ?></td>
                </tr>
                <?php
                $total_diskon = $total_diskon+($data->diskon*$hjual*$data->keluar);
				$total = ceil($total + $subtotal);
                } ?>
            </tbody>
        </table><br/>
    <!--<?= form_button(null, 'Cetak Nota', 'id=cetak onClick=PrintElem("#kegiatan")') ?>-->
    <?= form_button(null, 'Delete', 'id=deletion') ?>
    <?= form_button(null, 'Retur', 'id=retur') ?>
    <?= form_close() ?>
</div>
<script>
    $('#total-diskon').html(numberToCurrency(parseInt(<?= $total_diskon ?>)));
    $('#total').html(numberToCurrency(parseInt(<?= $total_diskon+$total+$biaya_apoteker->jasa ?>)));
	$('#total-tagihan').html(numberToCurrency(parseInt(<?= $total ?>)));
    //subTotal();
</script>