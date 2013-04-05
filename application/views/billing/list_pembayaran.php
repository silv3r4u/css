<script type="text/javascript">
$(function() {
    $('.print').click(function() {
        var id_nota = $(this).attr('title');
        var pembayaran_ke = $(this).attr('name');
        window.open('<?= base_url('billing/cetak') ?>/'+id_nota+'/'+pembayaran_ke, 'cetakbilling', 'location=1,status=1,scrollbars=1,width=820px,height=500px');
    })
})
</script>
<div class="data-list">
    <?php if ($total_data == 0) { ?>
        Belum ada data pembayaran 
    <?php } else { ?>
        <table class="tabel" width="100%">
            <tr>
                <th>No</th>
                <th>Waktu</th>
                <th>Tagihan</th>
                <th>Bayar</th>
                <th>Pembulatan Bayar</th>
                <th>Sisa Tagihan</th>
                <th>Cetak</th>
            </tr>
    <?php foreach ($list_data as $key => $data) { ?>
            <tr class="tr_rows <?= ($key % 2 == 0) ? 'even' : 'odd' ?>">
                <td align="center"><?= ++$key ?></td>
                <td align="center"><?= datetime($data->waktu) ?></td>
                <td align="right"><?= rupiah($data->total) ?></td>
                <td align="right"><?= rupiah($data->bayar) ?></td>
                <td align="right"><?= rupiah($data->pembulatan_bayar) ?></td>
                <td align="right"><?= rupiah($data->sisa) ?></td>
                <td align="center"><?= form_button($key . '/' . $rows->no_daftar . '/' . $rows->no_daftar, 'Cetak', 'title="' . $data->id_nota . '" class="print" id=cetak' . $key) ?></td>
            </tr>
            <script>
                $(function() {

                    $('button[id=cetak<?= $key ?>]').button({
                        icons: {
                            secondary: 'ui-icon-print'
                        }
                    })
                })
            </script>
    <?php } ?>
        </table>
    <?php } ?>
</div>

<?php die ?>