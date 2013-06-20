<!--<script type="text/javascript" src="<?= base_url('assets/js/colResizable-1.3.min.js') ?>"></script>-->
<script type="text/javascript">
$("#table").tablesorter({sortList:[[0,0]]});
//$(function() {
//    var onSampleResized = function(e){
//            var columns = $(e.currentTarget).find("th");
//            var msg = "columns widths: ";
//            columns.each(function(){ msg += $(this).width() + "px; "; });
//
//    };
//    $("#table").colResizable({
//        liveDrag:true,
//        gripInnerHtml:"<div class='grip'></div>", 
//        draggingClass:"dragging", 
//        onResize:onSampleResized
//    });
//});
</script>

<table class="sortable" id="table" width="100%">
    <thead>
    <tr>
        <th width="5%"><h3>No.</h3> </th>
        <th width="25%"><h3>Nama</h3> </th>
        <th width="8%"><h3>HNA</h3></th>
        <th width="8%"><h3>HV (Rp.)</h3></th>
        <th width="8%"><h3>OWA (Rp.)</h3></th>
        <th width="10%"><h3>H.Resep (Rp.)</h3></th>
        <th width="10%">Lokasi Rak</th>
        <th width="8%"><h3>Kekuatan</h3></th>
        <th width="8%"><h3>Sediaan</h3></th>
        <th width="8%"><h3>Adm R</h3></th>
        <th width="10%"><h3>Perundangan</h3></th>
        <th width="10%"><h3>Generik</h3></th>
        <th width="25%"><h3>Kandungan</h3></th>
        <th width="25%"><h3>Indikasi</h3></th>
        <th width="25%"><h3>Dosis</h3></th>
        <th width="10%"><h3>Aksi</h3></th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($barang) == 0) : ?>

        <?php for ($i = 1; $i <= 2; $i++) : ?>
            <tr class="<?= ($i % 2 == 1) ? 'even' : 'odd' ?>">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="aksi"></td>
            </tr>
        <?php endfor; ?>
    <?php else: ?>
        <?php foreach ($barang as $key => $rows): 
            $set = $this->m_referensi->get_setting()->row();
            $margin = $rows->margin/100;
            $diskon = $rows->diskon/100;
            $harga_jual = (($rows->hna*($margin+1))-(($rows->hna*($margin+1))*$diskon))*$rows->isi;
            $hv =  $harga_jual+($harga_jual*($set->hv/100));
            $owa = $harga_jual+($harga_jual*($set->owa/100));
            $resep = $harga_jual+($harga_jual*($set->h_resep/100));
            ?>
            <tr class="<?= ($key % 2 == 1) ? 'even' : 'odd' ?>">
                <td align="center"><?= (++$key+$start) ?></td>
                <td class="no-wrap"><?= $rows->nama ?></td>
                <td align="right"><?= rupiah($rows->hna) ?></td>
                <td align="right"><?= rupiah($hv) ?></td>
                <td align="right"><?= rupiah($owa) ?></td>
                <td align="right"><?= rupiah($resep) ?></td>
                <td align="center"><?= $rows->lokasi_rak ?></td>
                <td align="center"><?= $rows->kekuatan ?></td>
                <td><?= $rows->sediaan ?></td>
                <td><?= $rows->adm_r ?></td>
                <td align="center"><?= $rows->perundangan ?></td>
                <td class="no-wrap" align="center"><?= $rows->generik ?></td>
                <td><?= $rows->kandungan ?></td>
                <td><?= $rows->indikasi ?></td>
                <td><?= $rows->dosis ?></td>
                <td class="aksi" align="center"> 
                    <?php
                    $str = $rows->id . "#" . $rows->nama
                            . "#" . $rows->id_pabrik . "#" . $rows->pabrik
                            . "#" . $rows->kekuatan . "#" . $rows->satuan_id
                            . "#" . $rows->sediaan_id . "#" . $rows->atc . "#" . $rows->ddd . "#" . $rows->adm_r
                            . "#" . $rows->perundangan . "#" . $rows->generik . "#" . $rows->formularium
                            . "#" . $rows->indikasi . "#" . $rows->dosis
                            . "#" . $rows->kandungan .'#'. $rows->hna
                            . "#" . $rows->stok_minimal. "#".$rows->is_konsinyasi."#".$rows->lokasi_rak;
                    ?>
                    <span class="edit" onclick="edit_obat('<?= $str ?>')"><?= img('assets/images/icons/edit.png') ?></span>
                    <span class="delete" onclick="delete_obat('<?= $rows->id ?>')"><?= img('assets/images/icons/delete.png') ?></span>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<br/>
<?= $this->load->view('paging') ?>
<div id="paging"><?= $paging ?></div>