<?php
//$total = 0;
foreach ($list_data as $key => $rows) { ?>
    <tr class="tr_row <?= ($key%2==1)?'even':'odd' ?>">
        <td>
        <?= $rows->barang ?> <?= ($rows->kekuatan == 1)?'':$rows->kekuatan ?> <?= $rows->satuan ?> <?= $rows->sediaan ?> <?= ($rows->generik == 'Non Generik')?'':$rows->pabrik ?> @ <?= ($rows->isi=='1')?'':$rows->isi ?> <?= $rows->satuan_terkecil ?>
        <input type=hidden name=id_pb[] id="id_pb<?= $key ?>" value="<?= $rows->barang_packing_id ?>" class=pb />
        </td>
        <td align="right" id="hpp<?= $key ?>"><?= rupiah($rows->hpp) ?></td>
        <td align="center"><?= datefmysql($rows->ed) ?><input type=hidden name=ed[] id="ed<?= $key ?>" size=10 value="<?= $rows->ed ?>" readonly="readonly" class=ed /></td>
        <td align="center" id="jml_retur<?= $key ?>"><?= $rows->keluar ?></td>
        <td><input type=text name=jml[] id="jml<?= $key ?>" value="<?= $rows->keluar ?>" size=15 class=jml onkeyup="checkMount(<?= $key ?>)" /></td></td>
        <td class=aksi><a class=delete onclick=eliminate(this)></a></td>
    </tr>
<?php 
//$total = $total + ($rows->keluar*$rows->hpp);
}
?>