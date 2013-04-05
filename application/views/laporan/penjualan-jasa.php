<title><?= $title ?></title>
<div class="kegiatan">
<script type="text/javascript">
    $(function() {
        $('#cari').each(function(){
            $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
        });
        $('#cari').button({
            icons: {
                primary: 'ui-icon-circle-check'
            }
        });
        $('#reset').button({
            icons: {
                primary: 'ui-icon-refresh'
            }
        });
        $('#awal,#akhir').datepicker({
            changeYear: true,
            changeMonth: true
        })
        $('#reset').click(function() {
            $('#loaddata').load('<?= base_url('laporan/penjualan_jasa') ?>?_='+Math.random());
        })
        $('#nakes').autocomplete("<?= base_url('inv_autocomplete/load_data_profesi_by_nakes') ?>",
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
                var str = '<div class=result>'+data.nama+' - '+data.sip_no+'</div>';
                return str;
            },
            width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $(this).val(data.nama+' - '+data.sip_no);
            $('input[name=id_nakes]').val(data.id_penduduk);
            $('#profesi').html(data.profesi);
            $('input[name=profesi]').val(data.profesi);
        });
        $('#penjualan_jasa').submit(function() {
            var url = $(this).attr('action');
            $.ajax({
                type: 'GET',
                url: url,
                data: $(this).serialize(),
                success: function(data) {
                    $('#loaddata').html(data);
                }
            })
            return false;
        })
    })
</script>
<h1><?= $title ?></h1>
<div class="data-input">
    <?= form_open('laporan/penjualan_jasa', 'id=penjualan_jasa') ?>
    <fieldset><legend>Parameter</legend>
        <label>Waktu</label><?= form_input('awal', isset($_GET['awal'])?$_GET['awal']:date("d/m/Y"), 'id=awal size=10') ?> <span class="label"> s.d </span><?= form_input('akhir', isset($_GET['akhir'])?$_GET['akhir']:date("d/m/Y"), 'id=akhir size=10') ?>
        <label>Nama Nakes</label><?= form_input('nakes', isset($_GET['nakes'])?$_GET['nakes']:null, 'id=nakes size=32') ?> <?= form_hidden('id_nakes', isset($_GET['id_nakes'])?$_GET['id_nakes']:null) ?>
        <label>&nbsp;</label><span class="label" id="profesi"><?= isset($_GET['profesi'])?$_GET['profesi']:null ?></span>
        <label></label><?= form_hidden('profesi', isset($_GET['profesi'])?$_GET['profesi']:null) ?><?= form_submit('submit', 'Cari', 'id=cari') ?> <?= form_button(null, 'Reset', 'id=reset') ?>
    </fieldset>
    <?= form_close() ?>
</div>
<div class="data-list">
    <table class="tabel" width="100%">
        <tr>
            <th>Waktu</th>
            <th>No. RM</th>
            <th>Nama Pasien</th>
            <th>Layanan</th>
            <th>Nominal</th>
        </tr>
        <?php if (isset($_GET['awal'])) {
            $total = 0;
            foreach ($list_data as $key => $data) { ?>
        <tr class="<?= ($key%2==1)?'even':'odd' ?>">
            <td align="center"><?= datetime($data->waktu) ?></td>
            <td align="center"><?= $data->no_rm ?></td>
            <td><?= $data->pasien ?></td>
            <td><?= $data->layanan ?></td>
            <td align="right"><?= rupiah($data->nominal_jasa_klinis*$data->frekuensi) ?></td>
        </tr>
        <?php 
            
            $total = $total + ($data->nominal_jasa_klinis*$data->frekuensi);
            } ?>
        <tr>
            <td colspan="4" align="right">Total Jasa Nakes</td><td align="right"><?= rupiah($total) ?></td>
        </tr>
        <?php
        } else { ?>
        <?php for($i = 0; $i <= 1; $i++)  { ?>
            <tr class="<?= ($i%2==1)?'even':'odd' ?>">
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        <?php } 
        }?>
    </table>
</div>
</div>