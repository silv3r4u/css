<?php if (count($asuransi) > 0) : ?>
    <?php foreach ($asuransi as $no => $rows): ?>
        <tr class="<?= ($no % 2 == 0) ? 'odd' : 'even' ?>">
            <td><?= $rows->polis_no ?></td>
            <td><?= $rows->nama ?></td>
            <td class="aksi"><a class="delete" onclick="delete_ak(<?= $rows->id_ak ?>,this)"></a></td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <?php for ($i = 0; $i <= 1; $i++): ?>

        <tr class=tr_row>
            <td><input type=text name=np[] id=np<?= $i ?> class=bc style="width: 100%" /></td>
            <td><input type=text name=produk[] id=produk<?= $i ?> class=produk style="width: 100%" /><input type=hidden name=id_produk[] id=id_produk<?= $i ?> class=id_produk size=10 /></td>
            <td class=aksi><a href=# class=delete onclick=eliminate(this)></a></td>'+
        </tr>
        <script>
            $(function() {
                $('#produk<?= $i ?>').autocomplete("<?= base_url('inv_autocomplete/load_data_produk_asuransi') ?>",
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
                        var str = '<div class=result>'+data.nama+' <br/>'+data.instansi+'</div>';
                        return str;
                    },
                    width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                    dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
                }).result(
                function(event,data,formated){

                    $(this).val(data.nama);
                    $('#id_produk<?= $i ?>').val(data.id);
                });
            })
        </script>

    <?php endfor; ?>
<?php endif; ?>

<?php die; ?>
