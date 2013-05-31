<?php foreach ($list_data as $key => $data) { 
    $margin = $data->margin/100;
    $diskon = $data->diskon/100;
    $harga_jual = (($data->hna*($margin+1))-(($data->hna*($margin+1))*$diskon))*$data->isi;
    ?>
<tr class=rows>
    <td><?= form_hidden('id_kemasan[]', $data->id) ?><input type=text name=barcode[] size=10 style="min-width: 100px;" value="<?= $data->barcode ?>" /></td>
    <td><select name="kemasan[]" style="min-width: 120px;"><option value="">Pilih kemasan ...</option><?php foreach ($kemasan as $rows) { echo '<option value="'.$rows->id.'"'; if ($data->terbesar_satuan_id == $rows->id) echo 'selected'; echo '>'.$rows->nama.'</option>'; } ?></select></td>
    <td><input type=text name=isi[] size=10 class=isi id=isi<?= $key ?> onkeyup="set_harga_jual(<?= $key ?>);" style="min-width: 100px;" value="<?= $data->isi ?>" /></td>
    <td><select name="satuan_kecil[]" style="min-width: 120px;"><option value="">Pilih satuan ...</option><?php foreach ($kemasan as $rows) { echo '<option value="'.$rows->id.'"'; if ($data->terkecil_satuan_id == $rows->id) echo 'selected'; echo '>'.$rows->nama.'</option>'; } ?></select></td>
    <td align="center"><input type=text name=margin[] size=5 onkeyup="set_harga_jual(<?= $key ?>);" id="margin<?= $key ?>" style="min-width: 50px;" value="<?= $data->margin ?>" /></td>
    <td align="center"><input type=text name=diskon[] size=5 onkeyup="set_harga_jual(<?= $key ?>);" id="diskon<?= $key ?>" style="min-width: 50px;" value="<?= $data->diskon ?>" /></td>
    <td align="right" id="hj<?= $key ?>"><input type=text name=harga_jual[] size=5 onblur="FormNum(this);" value="<?= rupiah($harga_jual) ?>" onkeyup="set_margin(<?= $key ?>);" style="min-width: 50px;" id=harga_jual<?= $key ?> /></td>
    <td><input type=button value="delete" onclick="eliminate(this, <?= $data->id ?>)" /></td>'
</tr>
<?php } ?>