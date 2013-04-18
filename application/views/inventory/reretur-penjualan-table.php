<?php
$total = 0;
foreach ($list_data as $key => $rows) { 
    $harga_jual = $rows->hna+($rows->hna*($rows->margin/100) - ($rows->hna*($rows->diskon/100)))
    ?>
    <tr class=tr_row>
        <td><input type=text name=pb[] id="pb<?= $key ?>" size=65 value="<?= $rows->barang ?> <?= ($rows->kekuatan != '1')?$rows->kekuatan:null ?> <?= $rows->satuan ?> <?= $rows->sediaan ?> <?= $rows->pabrik ?> @ <?= ($rows->isi=='1')?'':$rows->isi ?> <?= $rows->satuan_terkecil ?>" class=pb />
        <input type=hidden name=id_pb[] id="id_pb<?= $key ?>" value="<?= $rows->barang_packing_id ?>" class=pb />
        </td>
        <td align="right" id="hpp<?= $key ?>"><?= rupiah($harga_jual) ?></td>
        <td align="center" id="jml_retur<?= $key ?>"><?= $rows->masuk ?> <?= form_hidden('ed[]', $rows->ed) ?></td>
        <td><input type=text name=jml[] id="jml<?= $key ?>" value="<?= $rows->masuk ?>" size=7 class=jml onkeyup="checkMount(<?= $key ?>)" /></td></td>
        <td class=aksi><span class=delete onclick=eliminate(this)><?= img('assets/images/icons/delete.png') ?></span></td>
    </tr>
    <script type="text/javascript">
        function checkMount(i) {
            var retur = parseInt($('#jml_retur'+i).html());
            var diretur = parseInt($('#jml'+i).val());
            if (retur < diretur) {
                alert('Jumlah yang di reretur tidak boleh lebih dari yang ter-retur !');
                $('#jml'+i).val(retur).focus();
                totalreretur();
                return false;
            }
            totalreretur();
        }
        $(function() {
            $('#pb<?= $key ?>').autocomplete("<?= base_url('inv_autocomplete/load_data_packing_barang') ?>",
                {
                    parse: function(data){
                        var parsed = [];
                        for (var i=0; i < data.length; i++) {
                            parsed[i] = {
                                data: data[i],
                                value: data[i].nama // nama field yang dicari
                            };
                        }
                        return parsed;
                    },
                    formatItem: function(data,i,max){
                        var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = '';
                        if (data.isi != '1') { var isi = '@ '+data.isi; }
                        if (data.satuan != null) { var satuan = data.satuan; }
                        if (data.kekuatan != null && data.kekuatan != '0') { var kekuatan = data.kekuatan; }
                        if (data.sediaan != null) { var sediaan = data.sediaan; }
                        if (data.pabrik != null) { var pabrik = data.pabrik; }
                        if (data.satuan_terkecil != null) { var satuan_terkecil = data.satuan_terkecil; }
                        if (data.id_obat == null) {
                            var str = '<div class=result>'+data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
                        } else {
                            if (data.generik == 'Non Generik') {
                                var str = '<div class=result>'+data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil+'</div>';
                            } else {
                                var str = '<div class=result>'+data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
                            }

                        }
                        return str;
                    },
                    width: 370, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                    dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
                }).result(
                function(event,data,formated){
                    var sisa = data.sisa;
                    if (data.sisa == null) {
                        var sisa = 0;
                    }
                    if (data.isi != '1') { var isi = '@ '+data.isi; }
                    if (data.satuan != null) { var satuan = data.satuan; }
                    if (data.kekuatan != null && data.kekuatan != '0') { var kekuatan = data.kekuatan; }
                    if (data.sediaan != null) { var sediaan = data.sediaan; }
                    if (data.pabrik != null) { var pabrik = data.pabrik; }
                    if (data.satuan_terkecil != null) { var satuan_terkecil = data.satuan_terkecil; }
                    if (data.id_obat == null) {
                        $(this).val(data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil);
                    } else {
                        if (data.generik == 'Non Generik') {
                            $(this).val(data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil);
                        } else {
                            $(this).val(data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil);
                        }
                    }
                    $('#id_pb<?= $key ?>').val(data.id);
                    $('#bc<?= $key ?>').val(data.barcode);
                    $('#sisa<?= $key ?>').html(sisa);
                });
        })
    </script>
<?php 
$total = $total + ($rows->hpp*$rows->masuk);
}
?>
<script type="text/javascript">

function totalreretur() {
    
    var jumlah = $('.tr_row').length-1;
    var total = 0;
    for (i = 0; i <= jumlah; i++) {
        var hpp = currencyToNumber($('#hpp'+i).html());
        var jml = $('#jml'+i).val();
        var total = total + (hpp*jml);
        
    }
    $('#returan').html(numberToCurrency(total));
    $('input[name=totalreretur]').val(total);
}
</script>
