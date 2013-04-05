<?php 
$totallica = 0;
foreach ($list_data as $noo => $rowz) {
?>
     <tr class="tr_row <?= ($noo%2==0)?'even':'odd' ?>">
         <td><?= $rowz->layanan ?></td>
         <td align="right"><?= rupiah($rowz->tarif) ?></td>
         <td align="center"><?= $rowz->frekuensi ?></td>
         <td align="right" id="subtotal<?= $noo ?>"><?= rupiah($rowz->subtotal) ?></td>
         <td align="center">-</td>
     </tr>
     <?php 
     $totallica = $totallica + $rowz->total;
} ?>
<script>
    $('#totals, #total').html(numberToCurrency(<?= $totallica ?>));
</script>