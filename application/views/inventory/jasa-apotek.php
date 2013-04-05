<script type="text/javascript">
$(function() {
    $('input[name=pegawai]').autocomplete("<?= app_base_url('common/autocomplete?opsi=pegawai') ?>",
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
            var kelurahan = data.kelurahan;
            if (data.kelurahan != 'null') {
                var kelurahan = '-';
            }
            var str = '<div class=result>'+data.nama+'<br/>'+data.alamat+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).attr('value',data.nama);
        $('#id_pegawai').val(data.penduduk_id);
    });
})
</script>
<?php
require 'app/lib/common/master-data.php';
?>
<div class="kegiatan" title="Rekap Jasa Apoteker">
    <fieldset class="circle"><legend>Rekap Jasa Apoteker</legend>
        <?= Form('inventory/jasa-apotek', 'get', null) ?>
        <table width="100%">
            <tr><td width="15%">Tanggal</td><td><?= InputText('awal', isset($_GET['awal'])?$_GET['awal']:date("d/m/Y"), 'id=awal size=10') ?> s . d <?= InputText('akhir', isset($_GET['awal'])?$_GET['akhir']:date("d/m/Y"), 'id=akhir size=10') ?></td> </tr>
            <tr><td>Pegawai</td><td><?= InputText('pegawai',isset($_GET['awal'])?$_GET['pegawai']:null,'size=40') ?><?= InputHidden('id_pegawai', isset($_GET['awal'])?$_GET['id_pegawai']:null, 'id=id_pegawai') ?></td></tr>
            <tr><td></td><td><?= ButtonSubmit(null, 'Cari', 'class=buttonsave') ?> <?= ButtonCancel('Reset', 'inventory/jasa-apotek','id=reset') ?></td></tr>
        </table>
        <?= EndForm() ?>
    </fieldset>
    <div class="circle">
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th>ID Billing</th>
                <th>Nilai (Rp)</th>
                <th>Frekuensi</th>
                <th>Subtotal</th>
            </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_GET['id_pegawai'])) {
                    $jasa = jasa_apoteker_get_data($_GET['id_pegawai'], date2mysql($_GET['awal']), date2mysql($_GET['akhir']));
                    $total = 0;
                    foreach ($jasa as $key => $data) { ?>
                    <tr class="<?= ($key%2==0)?'even':'odd' ?>">
                        <td align="center"><?= $data['id'] ?></td>
                        <td align="right"><?= rupiah($data['nominal']) ?></td>
                        <td align="center"><?= $data['frekuensi'] ?></td>
                        <td align="right"><?= rupiah($data['subtotal']) ?></td>
                    </tr>
                    <?php 
                    $total = $total + $data['subtotal'];
                    } 
                } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" align="right">TOTAL</td>
                    <td align="right"><?= isset($_GET['id_pegawai'])?rupiah($total):null ?></td>
                </tr>
            </tfoot>
        </table>
        
    </div>
</div>