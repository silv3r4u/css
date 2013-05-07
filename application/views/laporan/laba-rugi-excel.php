<div class="data-list">
<?php
$total_pend = 0;
$percentage_pendapatan_barang = 0;
$percentage_jasa_apt = 0;
$percentage_pend_lain = 0;
$border = 0;
if (isset($_GET['do']) and $_GET['do'] == 'cetak') {
    $border = 1;
    header_excel("lap-labarugi.xls"); ?>
    <table width="100%" style="color: #ffffff" bgcolor="#31849b">
        <tr>
        <td colspan="5" align="center"><b><?= strtoupper($apt->nama) ?></b></td> </tr>
        <tr><td colspan="5" align="center"><b><?= strtoupper($apt->alamat) ?> <?= strtoupper($apt->kabupaten) ?></b></td> </tr>
        <tr><td colspan="5" align="center" style="padding-right: 70px"><b>TELP. <?= $apt->telp ?>,  FAX. <?= $apt->fax ?>, EMAIL <?= $apt->email ?></b></td> </tr>
    </table>
    <center>LAPORAN LABA-RUGI <br/><?= indo_tgl(date2mysql($_GET['awal'])) ?> s.d <?= indo_tgl(date2mysql($_GET['akhir'])) ?></center>
<?php }
$total_pendapatan = $pendapatan_penjualan->penjualan_barang+$pendapatan_jasa->jasa+$kas->penerimaan_total;
if ($total_pendapatan != 0) {
    $total_pend = $pendapatan_penjualan->penjualan_barang+$pendapatan_jasa->jasa+$kas->penerimaan_total;
    $percentage_pendapatan_barang = ($pendapatan_penjualan->penjualan_barang/($total_pend))*100;
    $percentage_jasa_apt = ($pendapatan_jasa->jasa/($total_pend))*100;
    $percentage_pend_lain= ($kas->penerimaan_total/$total_pend)*100;
}
$total_pengeluaran = $hpp->total_hna+$total_keluar->pengeluaran_total;
?>
<table class="tabel" width="100%" border="<?= $border ?>">
        <thead>
            <tr>
                <th width="45%">Diskripsi</th>
                <th width="15%">Rincian</th>
                <th width="15%">Jumlah</th>
                <th width="15%">Total</th>
                <th width="10%">( % )</th>
            </tr>
        </thead>
        <tbody>
            <!-- Pendapatan Begin -->
            <tr class="even">
                <td><b>&nbsp;PENDAPATAN APOTEK</b></td>
                <td colspan="4"></td>
            </tr>
            <tr class="even">
                <td>&nbsp;&nbsp;PENDAPATAN PENJUALAN</td>
                <td colspan="4"></td>
            </tr>
                    <tr class="even">
                        <td style="padding-left: 20px;">Penjualan Barang Dagangan</td>
                        <td align="right"><?= ($pendapatan_penjualan->penjualan_barang) ?></td>
                        <td></td>
                        <td></td>
                        <td align="right"><?= round($percentage_pendapatan_barang,2) ?></td>
                    </tr>
                    <tr class="even">
                        <td style="padding-left: 20px;">Jasa Apoteker</td>
                        <td align="right"><?= ($pendapatan_jasa->jasa) ?></td>
                        <td></td>
                        <td></td>
                        <td align="right"><?= round($percentage_jasa_apt,2) ?></td>
                    </tr>
            <tr class="even">
                <td>&nbsp;&nbsp;PENDAPATAN LAIN-LAIN</td>
                <td colspan="4"></td>
            </tr>
                    <?php foreach ($penerimaan as $rows) { ?>
                    <tr class="even">
                        <td style="padding-left: 20px;"><?= $rows->penerimaan_pengeluaran_nama ?></td>
                        <td align="right"><?= ($rows->penerimaan) ?></td>
                        <td></td>
                        <td></td>
                        <td align="right"><?= round(($rows->penerimaan/$total_pend)*100,2) ?></td>
                    </tr>
                    <?php } ?>
            <tr class="odd" style="font-weight: bold;">
                <td>&nbsp;TOTAL PENDAPATAN</td>
                <td align="right"></td>
                <td></td>
                <td align="right"><?= ($total_pend) ?></td>
                <td align="right"><?= round($percentage_pendapatan_barang+$percentage_jasa_apt+$percentage_pend_lain,2) ?></td>
            </tr>
        <!-- Pendapatan End -->
        
        <!-- Pengeluaran Start -->
        
        <tr class="even">
            <td><b>&nbsp;BEBAN APOTEK</b></td>
            <td colspan="4"></td>
        </tr>
        <tr class="even">
            <td>&nbsp;&nbsp;BEBAN POKOK PENJUALAN</td>
            <td align="right"></td>
            <td></td>
            <td></td>
            <td align="right"></td>
        </tr>
                <tr class="even">
                    <td style="padding-left: 20px;">Harga Pokok Penjualan</td>
                    <td align="right"><?= ($hpp->total_hna) ?></td>
                    <td></td>
                    <td></td>
                    <td align="right">
                        <?php 
                            $hpps = 0;
                            if ($total_pend > 0) {
                                $hpps = round(($hpp->total_hna/$total_pend)*100,2); echo $hpps;  
                            }
                        ?>
                    </td>
                </tr>
        <tr class="even">
            <td>&nbsp;&nbsp;BEBAN USAHA</td>
            <td align="right"></td>
            <td></td>
            <td></td>
            <td align="right"></td>
        </tr>
                <?php 
                $ttl_beban_usaha = 0;
                foreach ($pengeluaran as $rowx) { ?>
                    <tr class="even">
                    <td style="padding-left: 20px;"><?= $rowx->penerimaan_pengeluaran_nama ?></td>
                    <td align="right"><?= ($rowx->pengeluaran) ?></td>
                    <td></td>
                    <td></td>
                    <td align="right"><?= round(($rowx->pengeluaran/$total_pend)*100,2) ?></td>
                </tr>
                <?php 
                $ttl_beban_usaha=$ttl_beban_usaha+round(($rowx->pengeluaran/$total_pend)*100,2);
                } ?>
        <tr class="odd" style="font-weight: bold;">
            <td>&nbsp;TOTAL BEBAN</td>
            <td align="right"></td>
            <td></td>
            <td align="right"><?= ($total_pengeluaran) ?></td>
            <td align="right"><?= ($hpps+$ttl_beban_usaha) ?></td>
        </tr>
        <?php
        $result_nominal = $total_pend-$total_pengeluaran;
        if ($result_nominal < 0) {
            $hasil = "(".(abs($result_nominal)).")";
        } else {
            $hasil = ($result_nominal);
        }
        
        $result_percent = (round($percentage_pendapatan_barang+$percentage_jasa_apt+$percentage_pend_lain,2)-($hpps+$ttl_beban_usaha));
        if ($result_percent < 0) {
            $hasil_percent = "(".abs($result_percent).")";
        } else {
            $hasil_percent = $result_percent;
        }
        ?>
        <tr class="odd" style="font-weight: bold;">
            <td>&nbsp;LABA (RUGI)</td>
            <td align="right"></td>
            <td></td>
            <td align="right"><?= $hasil ?></td>
            <td align="right"><?= $hasil_percent ?></td>
        </tr>
        </tbody>
    </table>
</div>