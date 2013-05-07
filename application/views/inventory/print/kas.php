<?php
$border=null;
if (isset($_GET['do'])) {
    echo "<table width='100%' style='font-family: 'Lucida Sans Unicode'; color: #ffffff' bgcolor='#31849b'><tr><td colspan=8 align=center><b>LAPORAN KAS <br/>TANGGAL $_GET[awal] s/d $_GET[akhir] <br/>$_GET[jenis]</b></td></tr></table>";
    header_excel("kas-".$_GET['awal']." sd".$_GET['akhir']."-".$_GET['jenis'].".xls");
    $border = "border=1";
}
?>
<script>
    $(function() {
        $('.view_transaction').click(function() {
            var url = $(this).attr('href');
            $.get(url, function(data) {
                $('#result_detail').html(data);
                $('#result_detail').dialog({
                    autoOpen: true,
                    height: 500,
                    width: 900,
                    modal: true,
                    close: function() {
                        $(this).dialog('close');
                    }
                });
            });
            return false;
        })
    })
</script>
<div id="result_detail" style="display: none"></div>
    <table class="tabel" width="100%" <?= $border ?>>
        <thead>
        <tr>
            <th>Waktu</th>
            <th>ID Transaksi</th>
            <th>Jenis Transaksi</th>
            <th>Nama</th>
            <?php if (isset($_GET['jenis']) and $_GET['jenis'] == '') { ?>
            <th>Awal</th>
            <?php } ?>
            <th>Penerimaan</th>
            <th>Pengeluaran</th>
            <?php if (isset($_GET['jenis']) and $_GET['jenis'] == '') { ?>
            <th>Akhir</th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <?php
        $penerimaan = 0;
        $pengeluaran= 0;
        $awal = 0;
        foreach ($list_data as $key => $data) { 
            $link = null;
            if ($data->transaksi_jenis == 'Inkaso') { $link = base_url('inventory/inkaso_detail/'.$data->transaksi_id); }
            if ($data->transaksi_jenis == 'Retur Pembelian') { $link = base_url('inventory/retur-pembelian/'.$data->transaksi_id); }
            if ($data->transaksi_jenis == 'Penjualan Resep') { $link = base_url('inventory/penjualan_detail/'.$data->transaksi_id); }
            if ($data->transaksi_jenis == 'Retur Penjualan') { $link = base_url('inventory/retur-penjualan/'.$data->transaksi_id); }
            if ($data->transaksi_jenis == 'Penerimaan dan Pengeluaran') { $link = base_url('inventory/pp_uang_detail/'.$data->transaksi_id); }
            if ($data->transaksi_jenis == 'Penerimaan Retur Pembelian') { $link = base_url('inventory/reretur_pembelian_detail/'.$data->transaksi_id); }
            if ($data->transaksi_jenis == 'Pengeluaran Retur Penjualan') { $link = base_url('inventory/reretur_penjualan_detail/'.$data->transaksi_id); }
            if ($data->transaksi_jenis == 'Penjualan Non Resep') { $link = base_url('inventory/penjualan_detail/'.$data->transaksi_id); }
            if ($data->transaksi_jenis == 'Pembayaran Billing Pasien') { $link = base_url('laporan/pembayaran_billing_pasien_detail/'.$data->transaksi_id); }
        ?>
        <tr class="<?= ($key%2==1)?'even':'odd' ?>">
            <td align="center"><?= datetime($data->waktu) ?></td>
            <td align="center"><?= isset($_GET['do'])?$data->transaksi_id:'<a class="view_transaction" href='.$link.'>'.$data->transaksi_id.'</a>' ?></td>
<!--            <td align="center"><?= isset($_GET['do'])?$data->transaksi_id:$data->transaksi_id ?></td>-->
            <td><?= $data->transaksi_jenis ?></td>
            <td><?= $data->penerimaan_pengeluaran_nama ?></td>
            <?php if (isset($_GET['jenis']) and $_GET['jenis'] == '') { ?>
            <td align="right"><?= $awal ?></td>
            <?php } ?>
            <td align="right"><?= $data->penerimaan ?></td>
            <td align="right"><?= $data->pengeluaran ?></td>
            <?php if (isset($_GET['jenis']) and $_GET['jenis'] == '') { ?>
            <td align="right"><?= $awal+$data->penerimaan-$data->pengeluaran ?></td>
            <?php } ?>
        </tr>
        <?php 
        $awal = $awal+$data->penerimaan-$data->pengeluaran;
        $penerimaan = $penerimaan+$data->penerimaan;
        $pengeluaran= $pengeluaran+$data->pengeluaran;
        }
        $colspan = 5;
        if (isset($_GET['jenis']) and $_GET['jenis'] != '') {
            $colspan = 4;
        }
        ?>
        </tbody>
        <tfoot>
            <tr style="height: 25px; background: #f3f3f3; font-weight: bold;">
                <td align="center" colspan="<?= $colspan ?>">Total</td>
                <td align="right"><?= $penerimaan ?></td>
                <td align="right"><?= $pengeluaran ?></td>
                <?php if (isset($_GET['jenis']) and $_GET['jenis'] == '') { ?>
                <td align="right"><?= ($penerimaan-$pengeluaran) ?></td>
                <?php } ?>
            </tr>
            <?php
            if ($_GET['jenis'] == 'Penjualan') {
                $q = null;
                if ($_GET['awal'] != '' and $_GET['akhir'] != '') {
                    $q = " and date(td.waktu) between '".  date2mysql($_GET['awal'])."' and '".  date2mysql($_GET['akhir'])."'";
                }
                $array = $this->db->query("SELECT p.total FROM transaksi_detail td 
                    join penjualan p on (p.id = td.transaksi_id) 
                    WHERE td.transaksi_jenis = 'Penjualan' $q group by td.transaksi_id
                ")->result();
                $tagihan = 0;
                foreach ($array as $total) {
                    $tagihan = $tagihan + $total->total;
                }
                
            ?>
            <tr>
                <td colspan="5" align="right"> Total Tagihan Penjualan</td>
                <td align="right"><?= $tagihan ?></td>
            </tr>
            <?php } ?>
        </tfoot>
    </table>
    <?php
    if (!isset($_GET['do'])) { ?>
    <?php } ?>
<?php die; ?>