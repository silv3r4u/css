<?php
foreach ($list_data as $key => $data) {
?>
<tr class=tr_row>
    <td><input type=text name=pb[] id="pb<?= $key ?>" size=80 value="<?= $data->barang ?> <?= $data->kekuatan ?> <?= $data->satuan ?> <?= $data->sediaan ?> <?= ($data->generik == 'Non Generik')?'':$data->pabrik ?> @ <?= ($data->isi=='1')?'':$data->isi ?> <?= $data->satuan_terkecil ?>" />
        <input type=hidden name=id_pb[] id="id_pb<?= $key ?>" value="<?= $data->barang_packing_id ?>" /></td>
    <td><input type=text name=ed[] id="ed<?= $key ?>" readonly="readonly" size=15 value="<?= datefmysql($data->ed) ?>" /></td>
    <td align="center" id="jml_dist<?= $key ?>"><?= $data->keluar ?></td>
    <td><input type=text name=jp[] id=jp<?= $key ?> size=10 value="<?= $data->keluar ?>" onkeyup="cek_jumlah(<?= $key ?>)" /></td>
    <td class=aksi><a href=# class=delete onclick=eliminate(this)></a></td>
</tr>
<script type="text/javascript">
$(function() {
    $('#pb'+<?= $key ?>).autocomplete("<?= base_url('common/autocomplete?opsi=packing-barang') ?>",
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
            var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terbesar = '';
            if (data.isi != '1') { var isi = '@ '+data.isi; }
            if (data.satuan != null) { var satuan = data.satuan; }
            if (data.sediaan != null) { var sediaan = data.sediaan; }
            if (data.pabrik != null) { var pabrik = data.pabrik; }
            if (data.satuan_terbesar != null) { var satuan_terbesar = data.satuan_terbesar; }
            var str = '<div class=result>'+data.nama+' '+data.kekuatan+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terbesar+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        var sisa = data.sisa;
        if (data.isi != '1') { var isi = '@ '+data.isi; }
        if (data.satuan != null) { var satuan = data.satuan; }
        if (data.sediaan != null) { var sediaan = data.sediaan; }
        if (data.pabrik != null) { var pabrik = data.pabrik; }
        if (data.satuan_terbesar != null) { var satuan_terbesar = data.satuan_terbesar; }
        $(this).val(data.nama+' '+data.kekuatan+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terbesar);
        $('#id_pb'+i).val(data.id);
        $('#bc'+i).val(data.barcode);
    });
})
</script>
<?php
}
die;
?>