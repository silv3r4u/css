<?php
$no = 0;
$total = 0; $disc = 0;
if (count($list_data) == 0) {?>
<span style="color: red; ">Tidak ada data di stok !</span>
<?php }
foreach ($list_data as $key => $data) { 
    $harga_jual = $data->hna+($data->hna*$data->margin/100) - ($data->hna*($data->diskon/100));
    $subtotal = ($harga_jual - (($harga_jual*($data->percent/100))))*$data->pakai_jumlah;
    $total = $total + $subtotal;
    $disc = $disc + (($data->percent/100)*$harga_jual);
    $alert=NULL;
    if ($data->sisa <= 0) {
        $alert = "style=background:red";
    }
    ?>
    <tr <?= $alert ?> class="tr_row <?= ($key%2==0)?'odd':'even' ?>">
        <td><input type=text name=nr[] id=bc<?= $no ?> class=bc size=20 value="<?= $data->barcode ?>" /></td>
        <td><input type=text name=dr[] id=pb<?= $no ?> class=pb size=60 value="<?= $data->barang ?> <?= ($data->kekuatan == '1')?'':$data->kekuatan ?>  <?= $data->satuan ?> <?= $data->sediaan ?> <?= ($data->generik == '1')?'':$data->pabrik ?> <?= ($data->isi==1)?'':'@'.$data->isi ?> <?= $data->satuan_terkecil ?>" />
            <input type=hidden name=id_pb[] id=id_pb<?= $no ?> class=id_pb value="<?= $data->barang_packing_id ?>" /></td>
        <td align="right" id=hj<?= $no ?>><?= rupiah($harga_jual) ?></td>
        <td align="center" id=diskon<?= $no ?>><?= $data->percent ?></td>
        <td><input type=text name=jl[] id=jl<?= $no ?> class=jl size=10 value="<?= $data->pakai_jumlah ?>" onkeyup="subTotal(<?= $no ?>)" />
        <input type=hidden name=subtotal[] id="subttl<?= $no ?>" class=subttl /></td>
        <td id=subtotal<?= $no ?> align="right"><?= rupiah($subtotal) ?></td>
        <td class=aksi><a class=delete onclick="eliminate(this)"></a> 
            <input type=hidden name="disc[]" id="disc<?= $no ?>" value="<?= $data->percent ?>" />
            <input type=hidden name="harga_jual[]" id="harga_jual<?= $no ?>" value="<?= $harga_jual ?>" /></td>
    </tr>
    <script type="text/javascript">
        $(function() {
            <?php if ($data->sisa <= 0) { ?>
                alert('Stok barang untuk <?= $data->barang ?> <?= ($data->kekuatan == '1')?'':$data->kekuatan ?>  <?= $data->satuan ?> <?= $data->sediaan ?> <?= ($data->generik == '1')?'':$data->pabrik ?> <?= ($data->isi==1)?'':'@'.$data->isi ?> <?= $data->satuan_terkecil ?> = 0 !');
            <?php } ?>
            $('#bc<?= $no ?>').live('keydown', function(e) {
                if (e.keyCode==13) {
                    var bc = $('#bc<?= $no ?>').val();
                    $.ajax({
                        url: '<?= base_url('inventory/fillField') ?>',
                        data: 'do=getPenjualanField&barcode='+bc,
                        cache: false,
                        dataType: 'json',
                        success: function(data) {
                            if (data.nama !== null) {
                                var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = '';
                                if (data.isi !== '1') { var isi = '@ '+data.isi; }
                                if (data.kekuatan !== null && data.kekuatan !== '0') { var kekuatan = data.kekuatan; }
                                if (data.satuan !== null) { var satuan = data.satuan; }
                                if (data.sediaan !== null) { var sediaan = data.sediaan; }
                                if (data.pabrik !== null) { var pabrik = data.pabrik; }
                                if (data.satuan_terkecil !== null) { var satuan_terkecil = data.satuan_terkecil; }
                                if (data.id_obat === null) {
                                    $('#pb<?= $no ?>').val(data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil);
                                } else {
                                    if (data.generik === 'Non Generik') {
                                        $('#pb<?= $no ?>').val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil);
                                    } else {
                                        $('#pb<?= $no ?>').val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil);
                                    }
                                    
                                }
                                $('#id_pb<?= $no ?>').val(data.id);
                                $('#kekuatan<?= $no ?>').html(data.kekuatan);
                                $('#hj<?= $no ?>').html(numberToCurrency(Math.ceil(data.harga))); // text asli
                                $('#harga_jual<?= $no ?>').val(data.harga);
                                $('#disc<?= $no ?>').val(data.diskon);
                                $('#diskon<?= $no ?>').html(data.diskon);
                                subTotal(i);
                                var jml = $('.tr_row').length;
                                //alert(jml+' - '+i)
                                if (jml - i === 1) {
                                    add(jml);
                                }
                                $('#jl<?= $no ?>').focus();
                            } else {
                                alert('Barang yang diinputkan tidak ada !');
                                $('#bc<?= $no ?>').val('');
                                $('#pb<?= $no ?>').val('');
                                $('#id_pb<?= $no ?>').val('');
                                $('#kekuatan<?= $no ?>').html('');
                                $('#hj<?= $no ?>').html(''); // text asli
                                $('#harga_jual<?= $no ?>').val('');
                                $('#disc<?= $no ?>').val('');
                                $('#diskon<?= $no ?>').html('');
                            }
                        }
                    });
                    return false;
                }
            });
            $('#pb<?= $no ?>').autocomplete("<?= base_url('inv_autocomplete/load_data_packing_barang') ?>",
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
                    var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = '';
                    if (data.isi !== '1') { var isi = '@ '+data.isi; }
                    if (data.satuan !== null) { var satuan = data.satuan; }
                    if (data.kekuatan !== null && data.kekuatan !== '0') { var kekuatan = data.kekuatan; }
                    if (data.sediaan !== null) { var sediaan = data.sediaan; }
                    if (data.pabrik !== null) { var pabrik = data.pabrik; }
                    if (data.satuan_terkecil !== null) { var satuan_terkecil = data.satuan_terkecil; }
                    if (data.id_obat === null) {
                        var str = '<div class=result>'+data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
                    } else {
                        if (data.generik === 'Non Generik') {
                            var str = '<div class=result>'+data.nama+' '+kekuatan+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil+'</div>';
                        } else {
                            var str = '<div class=result>'+data.nama+' '+kekuatan+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
                        }

                    }
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                var sisa = data.sisa;
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
                        $(this).val(data.nama+' '+kekuatan+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil);
                    } else {
                        $(this).val(data.nama+' '+kekuatan+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil);
                    }

                }
                $('#id_pb<?= $no ?>').val(data.id);
                $('#bc<?= $no ?>').val(data.barcode);
            });
        })
    </script>
<?php 

$no++;
}
?>
<script>
    $(function() {
        <?php if (count($list_data) == 0) { ?>
            $('button[type=submit]').hide();
        <?php } else { ?>
            $('button[type=submit]').show();
        <?php } ?>
        $('#total-tagihan').html(numberToCurrency(<?= $total ?>));
        $('#total-diskon').html(<?= ceil($disc) ?>);
        $('#total').html(numberToCurrency(<?= ($total-ceil($disc)) ?>));
        var jumlah = $('.tr_row').length;
        for (i = 1; i <= jumlah; i++) {
            subTotal(i);
        }
    })
</script>