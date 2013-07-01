<script type="text/javascript" src="<?= base_url('assets/js/colResizable-1.3.min.js') ?>"></script>
<script type="text/javascript">
$("#table").tablesorter({sortList:[[0,0]]});
$(function() {
    var onSampleResized = function(e){
            var columns = $(e.currentTarget).find("th");
            var msg = "columns widths: ";
            columns.each(function(){ msg += $(this).width() + "px; "; });

    };
    $("#table").colResizable({
        liveDrag:true,
        gripInnerHtml:"<div class='grip'></div>", 
        draggingClass:"dragging", 
        onResize:onSampleResized
    });
});
</script>
NB: Double click pada data yang dipilih, untuk melakukan penjualan.
<table width="100%" class="tabel" id="table">
    <tr>
        <th width="5%">No.</th>
        <th width="20%">Waktu</th>
        <th width="25%">Pasien</th>
        <th width="25%">Dokter</th>
        <th width="25%">Keterangan</th>
    </tr>
    <?php foreach ($list_data as $key => $data) { ?>
    <tr id="<?= $data->id ?>" class="<?= ($key%2==0)?'even':'odd' ?> choosen" title="Double click untuk memilih">
        <td align="center"><?= $data->id ?></td>
        <td align="center"><?= datetime($data->waktu) ?></td>
        <td><?= $data->pasien ?></td>
        <td><?= $data->dokter ?></td>
        <td><?= $data->keterangan ?></td>
    </tr>
    <?php } ?>
</table>