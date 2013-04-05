<title><?= $title ?></title>
    <h1 class="informasi"><?= $title ?></h1>
    <?php
    $sisa = $rows->total-$rows->jumlah_bayar;
    ?>
    <div class="data-input">
            <label>No.</label><span class="label"> <?= $rows->id ?></span>
            <label>Waktu</label><span class="label"><?= indo_tgl($rows->dokumen_tanggal) ?></span>
            <label>No. Pembelian</label><span class="label"><?= $rows->pembelian_id ?></span>
            <label>Suplier</label> <span class="label" id="suplier"><?= $rows->instansi ?></span>
            <label>Total Tagihan (Rp.)</label><span class="label" id="total_tagihan"><?= rupiah($rows->total) ?></span>
            <label>Total Terbayar (Rp.)</label><span class="label" id="total_terbayar"><?= rupiah($rows->jumlah_bayar) ?></span>
            <label>Sisa Tagihan (Rp.)</label><span class="label" id="sisa_tagihan"><?= ($sisa <= 0)?'0':rupiah($sisa) ?></span>
    </div>