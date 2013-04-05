<?php
if (!isset($_GET['do'])) {
    echo $this->load->view('message');
}
?>
<title><?= $title ?></title>
<div class="kegiatan">
<div id="result_detail" style="display: none"></div>
    <script>
        $(function() {
            $(".sortable").tablesorter();
            $('.view_transaction').click(function() {
                var url = $(this).attr('href');
                $.get(url, function(data) {
                    $('#result_detail').html(data);
                    $('#result_detail').dialog({
                        autoOpen: true,
                        height: 500,
                        width: 900,
                        modal: true
                    });
                });
                return false;
            })
            $('#reset').click(function() {
                var url = $('#formlaporanhutang').attr('action');
                $('#loaddata').load(url);
            })
            $('#formlaporanhutang').submit(function() {
                var url = $(this).attr('action');
                $.ajax({
                    type: 'GET',
                    url: url,
                    data: $(this).serialize(),
                    success: function(data) {
                        $('#loaddata').html(data);
                    }
                })
                return false;
            })
            $('#excel').click(function() {
                $('#loaddata').load(url);
            })
            $('button[id=reset]').button({
                icons: {
                    primary: 'ui-icon-refresh'
                }
            });
            $('button[id=cetak]').button({
                icons: {
                    primary: 'ui-icon-print'
                }
            });
            $('input[type=submit]').each(function(){
                $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
            });
            $('button[type=submit]').button({
                icons: {
                    primary: 'ui-icon-circle-check'
                }
            });
            $('#cetak').click(function() {
                var awal = '<?= isset($_GET['awal']) ? $_GET['awal'] : null ?>';
                var akhir = '<?= isset($_GET['awal']) ? $_GET['akhir'] : null ?>';
                location.href='<?= base_url('laporan/hutang') ?>?awal='+awal+'&akhir='+akhir+'&do=cetak';
            })
            $('#awal,#akhir').datepicker({
                changeYear: true,
                changeMonth: true
            })
        })
    </script>
    <h1><?= $title ?></h1>
    <?php
    $border = 0;
    if (isset($_GET['do'])) {
        header_excel("laporan-hutang_" . date("d-m-Y") . ".xls");
        $border = 1;
    }
    if (!isset($_GET['do'])) {
        ?>
        <div class="data-input">
            <fieldset><legend>Parameter Pencarian</legend>
    <?= form_open('laporan/hutang', 'id=formlaporanhutang') ?>
                <label>Tanggal:</label> <?= form_input('awal', isset($_GET['awal']) ? $_GET['awal'] : date("d/m/Y"), 'id=awal size=10') ?> <span class="label"> s . d </span> <?= form_input('akhir', isset($_GET['awal']) ? $_GET['akhir'] : date("d/m/Y"), 'id=akhir size=10') ?>
                <label></label><?= form_submit(null, 'Cari', 'id=search') ?> <?= form_button('Reset', 'Reset', 'id=reset') ?> <?= form_button(null, 'Cetak Excel', 'id=cetak') ?>
    <?= form_close() ?>
            </fieldset>
        </div>
<?php } ?>
    <div class="data-list">
        <table class="sortable" border="<?= $border ?>" width="100%">
            <thead>
                <tr>
                    <th width="10%"><h3>Waktu Pembelian</h3></th>
                    <th width="30%"><h3>Supplier</h3></th>
                    <th width="10%"><h3>No. Doc</h3></th>
                    <th width="10%"><h3>Total/Faktur (Rp.)</h3></th>
                    <th width="10%"><h3>Inkaso (Rp.)</h3></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_GET['awal'])) {
                    $t_faktur = 0;
                    $t_inkaso = 0;
                    //$utang = utang_get_data($_GET['awal'], $_GET['akhir']);
                    foreach ($list_data as $key => $data) {
                        $inkaso = $this->m_inventory->get_data_inkaso($data->id)->row();
                        $total_faktur = ($data->total + $data->materai) + ($data->total * ($data->ppn / 100));
                        ?>
                        <tr class="<?= ($key % 2 == 1) ? 'even' : 'odd' ?>">
                            <td align="center"><?= datefmysql($data->dokumen_tanggal) ?></td>
                            <td><?= $data->nama ?></td>
                            <td align="center"><?= anchor('inventory/pembelian_detail/'.$data->id.'?awal='.$_GET['awal'].'&akhir='.$_GET['akhir'], $data->dokumen_no, 'class=view_transaction') ?></td>
                            <td align="right"><?= rupiah($total_faktur) ?></td>
                            <td align="right"><?= rupiah($inkaso->inkaso) ?></td>
                        </tr>
                        <?php
                        $t_faktur = $t_faktur + $total_faktur;
                        $t_inkaso = $t_inkaso + $inkaso->inkaso;
                    }
                }
                ?>
            </tbody>
            <tfoot>
                <tr class="odd">
                    <td colspan="3" align="center">Total</td>
                    <td align="right" style="font-weight: bold"><?= (isset($_GET['awal']) ? rupiah($t_faktur) : null) ?></td>
                    <td align="right" style="font-weight: bold"><?= (isset($_GET['awal']) ? rupiah($t_inkaso) : null) ?></td>
                </tr>
            </tfoot>
        </table>
        <div style="text-align: right; font-weight: bold; font-size: 13px;">
            <table>
                <tr><td>HUTANG (Rp.) </td><td>:</td> <td><?= isset($_GET['awal']) ? rupiah($t_faktur - $t_inkaso) : null ?></td></tr>
            </table>

        </div>
    </div>
</div>