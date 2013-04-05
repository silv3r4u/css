<?= $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
<h1><?= $title ?></h1>
<script type="text/javascript">
$(function() {
    $('#reset').click(function() {
        var url = '<?= base_url('inventory/penerimaan_retur_unit') ?>';
        $('#loaddata').load(url);
    })
    $('#tanggal').datetimepicker();
    $('input[type=submit]').each(function(){
        $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
    });
    $('button[type=submit]').button({
        icons: {
            primary: 'ui-icon-circle-check'
        }
    });
    $('button[id=reset]').button({
        icons: {
            primary: 'ui-icon-refresh'
        }
    });
    $('#cetakexcel').click(function() {
        location.href='<?= base_url('cetak/inventory/pembelian') ?>?id=<?= isset($_GET['id'])?$_GET['id']:NULL ?>';
    })
    $('#printhasil').click(function() {
        window.open('<?= base_url('cetak/inventory/pembelian') ?>?id=<?= isset($_GET['id'])?$_GET['id']:NULL ?>','mywindow','location=1,status=1,scrollbars=1,width=900px,height=400px');
    })
    $( ".tanggals" ).datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#returan').click(function() {
        
        var id = $('#nopemesanan').val();
        var id_pembelian = '<?= isset($_GET['id'])?$_GET['id']:NULL ?>';
        if (id_pembelian == '') {
            alert('Untuk melakukan retur harus melalui menu informasi stok');
            return false;
        }
        $.ajax({
            url: '<?= base_url('inventory/fillField') ?>',
            data: 'act=checkpembelian&id='+id,
            cache: false,
            success: function(msg) {
                if (msg) {
                    location.href='<?= base_url('inventory/retur-pembelian') ?>?id='+id_pembelian;
                } else {
                    alert('Nomor pembelian belum terdaftar');
                    return false;
                }
            }
        })
        //
    })
    $('#noretur').autocomplete("<?= base_url('inv_autocomplete/get_no_retur_distribusi') ?>",
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
            var str = '<div class=result>'+data.id+' - '+datefmysql(data.waktu)+' - '+data.unit+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.id);
        $('#pegawai').html(data.pegawai);
        var id = data.id;
        $.ajax({
            url: '<?= base_url('inv_autocomplete/load_data_retur_unit') ?>/'+id,
            data: 'id='+id,
            cache: false,
            success: function(msg) {
                $('.form-inputan tbody').html(msg);
            }
        })
    });
    $('#penerimaan_retur_dist_save').submit(function() {
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            dataType: 'json',
            cache: false,
            success: function(data) {
                if (data.status == true) {
                    $('input').attr('disabled','disabled');
                    $('button[type=submit]').hide();
                    alert_tambah();
                }
            }
        })
        return false;
    })
    
})
function eliminate(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);
}



</script>

    <?= form_open('inventory/penerimaan_retur_distribusi_save', 'id=penerimaan_retur_dist_save') ?>
    <div class="data-input">
        <fieldset><legend>Summary</legend>
            <label>Tanggal</label><?= form_input('tanggal', date("d/m/Y H:i"), 'id=tanggal') ?>
            <label>No. Retur</label><?= form_input('noretur', null, 'id=noretur size=30') ?>
            <label>Pegawai</label><span class="label" id="pegawai"></span>
        </fieldset>
    </div>
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="25%">Packing Barang</th>
                <th width="11%">HPP</th>
                <th width="11%">ED</th>
                <th width="5%">Jumlah Retur</th>
                <th width="11%">Jumlah Penerimaan</th>
                <th width="5%">#</th>
            </tr>
            </thead>
            <tbody>
                <?php for($i = 0; $i <= 1; $i++) { ?>
                <tr class="<?= ($i%2==1)?'even':'odd' ?>">
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php } ?>
            </tbody>
        </table><br/>
            <?= form_submit('save', 'Simpan', 'id=save') ?>
            <?= form_button(null, 'Reset', 'id=reset') ?>
    </div>
    <?= form_close(); ?>
</div>