<script type="text/javascript">
    $(function() {
        $('#retur_pembelian').click(function() {
            var id = $('#id_pembelian').html();
            $.get('<?= base_url('inventory/retur_pembelian') ?>/'+id+'?_'+Math.random(), function(data) {
                $("#result_detail").dialog().remove();
                $('#loaddata').html(data);
            });
        });
        $('#delete_pembelian').click(function() {
            var ok=confirm('Anda yakin akan menghapus transaksi ini ?');
            if (ok) {
                var id = $('#id_pembelian').html();
                $.get('<?= base_url('inventory/pembelian_delete') ?>/'+id+'?_'+Math.random(), function(data) {
                    if (data === true) {
                        alert_delete();
                    }
                },'json');
                $(this).closest("#result_detail").dialog().remove();
                var url = '<?= base_url('laporan/hutang?awal='.$_GET['awal'].'&akhir='.$_GET['akhir']) ?>';
                $('#loaddata').load(url);
            } else {
                return false;
            }
        });
    });
</script>
<title><?= $title ?></title>
<h1 class="informasi"><?= $title ?></h1>
    <?php
    foreach($list_data as $key => $rows);
    ?>
    <div class="data-input">
            <div class="left_side_detail">
                <label>No.:</label> <span class="label" id="id_pembelian"><?= $rows->id_pembelian ?></span>
                <label>No. Faktur:</label> <span class="label"> <?= $rows->dokumen_no ?> </span>
                <label>Tanggal Faktur:</label><span class="label"><?= datefrompg($rows->dokumen_tanggal) ?></span>
<!--                <label>No. Pemesanan:</label><span class="label"><?= $rows->dokumen_no ?></span>-->
                <label>Supplier:</label><span class="label"><?= $rows->pabrik ?></span>
<!--                <label>Salesman:</label><span class="label"><?= $rows->salesman ?></span>-->
                <label>Ttd Penerimaan:</label><span class="label"><?= $rows->ada_penerima_ttd ?></span>
                <label>PPN (%):</label><span class="label"><?= $rows->ppn ?></span>
                <label>Materai (Rp.):</label><span class="label"><?= rupiah($rows->materai) ?></span>
                <label>Tanggal Jatuh Tempo:</label><span class="label"><?= datefrompg($rows->tanggal_jatuh_tempo) ?></span>
                <label>Keterangan:</label><span class="label"><?= $rows->keterangan ?></span>
            </div>
            <div class="right_side_detail">
                <label>Total Harga:</label><span class="label" id="total-harga"></span>
                <label>Total Diskon:</label><span class="label" id="total-diskon"></span>
                <label>Total Pembelian:</label><span class="label" id="total-pembelian"></span>
                <label>Total PPN:</label><span class="label" id="total-ppn"></span>
                <label>Materai:</label><span class="label" id="materai2"><?= rupiah($rows->materai) ?></span>
                <label>Total Tagihan</label><span class="label" id="total-tagihan"></span>
                </table>
            </div>
    </div>
    
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="35%">Kemasan Barang</th>
                <th width="8%">ED</th>
                <th width="5%">Jumlah</th>
                <th width="8%">Harga @</th>
                <th width="5%">Disc(%)</th>
                <th width="7%">Disc(Rp.)</th>
                <th width="8%">SubTotal</th>
                <th width="2%">Bonus</th>
            </tr>
            </thead>
            <tbody>

            <?php
            $total = 0;
            $diskon= 0;
            foreach($list_data as $key => $data) { 
                if ($data->id_obat == null) {
                    $packing = $data->barang.' '.$data->pabrik.' @ '.(($data->isi==1)?'':$data->isi).' '.$data->satuan_terkecil;
                } else {
                    $packing = $data->barang .' '.(($data->kekuatan != '1')?$data->kekuatan:null).' '. $data->satuan.' '. $data->sediaan .' '.(($data->generik == 'Non Generik')?'':$data->pabrik).' '.(($data->isi==1)?'':'@ '.$data->isi).' '. $data->satuan_terkecil;
                }
                ?>
                <tr class="<?= ($key%2==0)?'odd':'even' ?>">
                    <td style="white-space: nowrap"><?= $packing ?></td>
                    <td align="center"><?= datefmysql($data->ed) ?></td>
                    <td><?= round($data->masuk) ?></td>
                    <td align="right"><?= inttocur($data->hna) ?></td>
                    <td align="center"><?= $data->beli_diskon_percentage ?></td>
                    <td align="right"><?= inttocur(($data->hna*($data->beli_diskon_percentage/100))*$data->masuk) ?></td>
                    <td align="right"><?= inttocur($data->subtotal) ?></td>
                    <td align="center"><?= ($data->beli_diskon_percentage == '100')?'Bonus':'-' ?></td>
                </tr>
            <?php 
            $total = $total + ($data->masuk*$data->hna);
            $diskon= $diskon+ (($data->hna*($data->beli_diskon_percentage/100))*$data->masuk);
            
            }?>
            </tbody>
        </table><br/>
        <?= form_button('delete', 'Hapus', 'id=delete_pembelian') ?>
        <?= form_button('retur_pembelian', 'Retur', 'id=retur_pembelian') ?>
        
        <script>
            $(function() {
                $('#total-harga').html(numberToCurrency(Math.ceil(<?= $total ?>)));
                $('#total-diskon').html(numberToCurrency(Math.ceil(<?= $diskon ?>)));
                $('#total-pembelian').html(numberToCurrency(Math.ceil(<?= $total-$diskon ?>)));
                $('#total-ppn').html(numberToCurrency(Math.ceil(<?= ($rows->ppn/100)*$total ?>)));
                $('#total-tagihan').html(numberToCurrency(Math.ceil(<?= $rows->materai+($total-$diskon)+(($rows->ppn/100)*$total) ?>)));
            })
        </script>
