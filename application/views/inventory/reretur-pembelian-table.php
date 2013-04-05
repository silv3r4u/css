<?php
$total = 0;
foreach ($list_data as $key => $rows) { ?>
    <tr class=tr_row>
        <td><input type=text name=pb[] id="pb<?= $key ?>" size=65 value="<?= $rows->barang ?> <?= ($rows->kekuatan == 1)?'':$rows->kekuatan ?> <?= $rows->satuan ?> <?= $rows->sediaan ?> <?= $rows->pabrik ?> @ <?= ($rows->isi=='1')?'':$rows->isi ?> <?= $rows->satuan_terkecil ?>" class=pb />
        <input type=hidden name=id_pb[] id="id_pb<?= $key ?>" value="<?= $rows->barang_packing_id ?>" class=pb />
        </td>
        <td align="right" id="hpp<?= $key ?>"><?= rupiah($rows->hpp) ?></td>
        <td><input type=text name=ed[] id="ed<?= $key ?>" size=15 value="<?= datefmysql($rows->ed) ?>" class=ed /></td>
        <td id="jml_retur<?= $key ?>"><?= $rows->keluar ?></td>
        <td><input type=text name=jml[] id="jml<?= $key ?>" value="<?= $rows->keluar ?>" size=7 class=jml onkeyup="checkMount(<?= $key ?>)" /></td></td>
        <td class=aksi><a class=delete onclick=eliminate(this)></a></td>
    </tr>
    <script type="text/javascript">
        function checkMount(i) {
            var retur = parseInt($('#jml_retur'+i).html());
            var diretur = parseInt($('#jml'+i).val());
            if (retur < diretur) {
                alert('Jumlah yang di reretur tidak boleh lebih dari yang ter-retur !');
                $('#jml'+i).val(retur).focus();
                //totalreretur();
                return false;
            }
            //totalreretur();
        }
        
        $("#ed<?= $key ?>").datepicker({
                changeYear: true,
                changeMonth: true
            });
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
                    width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
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
                    $('#id_pb'+i).val(data.id);
                    $('#bc'+i).val(data.barcode);
                    $('#sisa'+i).html(sisa);
                });
        
    </script>
<?php 
$total = $total + ($rows->keluar*$rows->hpp);
}
?>
    <script>
        $('#returan').html(numberToCurrency(<?= $total ?>));
    </script>
<!--<script type="text/javascript">
    function totalreretur() {
        var jumlah = $('.tr_row').length-1;
        var total = 0;
        for (i = 0; i <= jumlah; i++) {
            var hpp = currencyToNumber($('#hpp'+i).html());
            var jml = currencyToNumber($('#jml'+i).val());
            var total = total + (hpp*jml);
        }
        $('#returan').html(numberToCurrency(total));
        $('input[name=total]').val(total);
    }
    
    $('#returan').html(numberToCurrency(<?= $total ?>));
    $('.diskon_pr').blur(function() {
        hitungDetail();
        //alert('asd')
    })
    var jml = $('.tr_row').length-1;
    
    for (j = 0; j <= jml; j++) {
        var dis_pr= komaKeTitik(($('#diskon_pr'+j).val() == null)?'0':$('#diskon_pr'+j).val());
        var dis_rp= parseInt(currencyToNumber(($('#diskon_rp'+j).val() == null)?'0':$('#diskon_rp'+j).val()));
        var harga = parseInt(currencyToNumber($('#hpp'+j).val()));
        var jumlah= parseInt($('#jml'+j).val());
        
        //$('#diskon_rp'+i).removeAttr('disabled');
        //$('#diskon_pr'+i).removeAttr('disabled');
        var subttl= (harga * jumlah);
        if (dis_pr != 0 || dis_rp != '') {
            var subttl = subttl - ((dis_pr/100)*harga)*jumlah;
            //$('#diskon_rp'+i).attr('disabled','disabled');
        }
        if (dis_rp != '' || dis_rp != 0) {
            var subttl = subttl - (dis_rp * jumlah);
            //$('#diskon_pr'+i).attr('disabled','disabled');
        }
        $('#subttl'+j).val(subttl);
        $('#subtotal'+j).html(numberToCurrency(subttl));
    }
</script>-->