<?php $this->load->view('message'); ?>
<script type="text/javascript">
$(function() {
    $('#tanggal').datetimepicker();
    $('button[id=reset]').button({
        icons: {
            primary: 'ui-icon-refresh'
        }
    });
    $('button[id=pmr_open]').button({
        icons: {
            primary: 'ui-icon-print'
        }
    });
    $('input[type=submit]').each(function(){
        $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
    });
    $('button[type=submit]').button({
        icons: {
            primary: 'ui-icon-circle-check'
        }
    });
    $('#nopembelian').autocomplete("<?= base_url('inv_autocomplete/get_nomor_pembelian') ?>",
        {
            parse: function(data)
            {
                var parsed = [];
                for (var i=0; i < data.length; i++)
                {
                    parsed[i] =
                    {
                        data: data[i],
                        value: data[i].id // nama field yang dicari
                    };
                }
                return parsed;
            },
            formatItem: function(data,i,max)
            {
                if (data.id !== null) {
                    var str = '<div class=result>'+data.id+' - '+datefmysql(data.dokumen_tanggal)+' - '+data.instansi+'</div>';
                }
                return str;
            },
            width: 270, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
            function(event,data,formated) {
                $('#id_inkaso').html();
                $(this).val(data.id);
                $('input[name=nopemb]').val(data.id);
                $('#no_pembelian').html(data.id);
                $('#suplier').html(data.instansi);
                $('#total_tagihan').html(numberToCurrency(Math.ceil(data.total)));
                $('#total_terbayar').html(numberToCurrency(Math.ceil(data.jumlah_terbayar)));
                var sisa = data.total-data.jumlah_terbayar;
                if (sisa <= 0) {
                    var hasil_sisa = numberToCurrency(Math.ceil(sisa));
                    $('#simpan').hide();
                    $('#bayar, #tanggal').attr('disabled','disabled');
                    $('#tanggal, #bayar').attr('disabled', 'disabled');
                } else {
                    $('#simpan').show();
                    var hasil_sisa = Math.ceil(sisa);
                    $('#tanggal, #bayar').removeAttr('disabled', 'disabled');
                }
                $('#sisa_tagihan').html(numberToCurrency(hasil_sisa));
                $('#bayar').val(numberToCurrency(hasil_sisa));
            }
        );
        $('#bayar, #serahuang').keyup(function() {
            var serahuang = parseInt(currencyToNumber($('#serahuang').val()));
            var bayar = parseInt(currencyToNumber($('#bayar').val()));
            var kembali = serahuang - bayar;
            if (!isNaN(kembali)) {
                $('#kembalian').html(kembali);
            }
            
            if (kembali > 0) {
                $('#kembalian').html(numberToCurrency(kembali));
            }
        });
        $('#reset').click(function() {
            $('#loaddata').html('');
            var url = '<?= base_url('inventory/inkaso') ?>';
            $('#loaddata').load(url);
        });
        
        $('#form_inkaso').submit(function() {
           if ($('input[name=nopemb]').val() === '') {
                alert('Nomor pembelian tidak boleh kosong');
                $('#nopembelian').focus();
                return false;
            }
            if ($('#bayar').val() === '') {
                alert('Jumlah pembayaran tidak boleh kosong');
                $('#bayar').focus();
                return false;
            }
            var url = $(this).attr('action');
            $.ajax({
                type: 'POST',
                url: url,
                data: $(this).serialize(),
                dataType: 'json',
                success: function(data) {
                    if (data['result'].status == true) {
                        $('#bayar, #nopembelian').attr('disabled', 'disabled');
                        $('button[type=submit]').hide();
                        $('input[name=nopemb]').val(data['detail'].id);
                        $('#no_pembelian').html(data['detail'].id);
                        $('#suplier').html(data['detail'].instansi);
                        $('#total_tagihan').html(numberToCurrency(Math.ceil(data['detail'].total)));
                        $('#total_terbayar').html(numberToCurrency(Math.ceil(data['detail'].jumlah_terbayar)));
                        $('#sisa_tagihan').html(numberToCurrency(Math.ceil(data['detail'].total-data['detail'].jumlah_terbayar)));
                        alert_tambah();
                    } else {
                        
                    }
                }
            })
            return false;
        })
})
</script>
<title><?= $title ?></title>
<div class="kegiatan">
    <h1><?= $title ?></h1>
    <?= form_open('inventory/inkaso', 'id=form_inkaso') ?>
    <div class="data-input">
    <fieldset><legend>Summary</legend>
        <div class="one_side">
        <?= form_hidden('id_layanan', isset($rows['id'])?$rows['id']:null, null) ?>
        
            <label>No.:</label><span class="label"> <?= isset($_GET['id'])?$_GET['id']:get_last_id('inkaso', 'id') ?></span>
            <label>Waktu:</label><?= form_input('tanggal', date("d/m/Y H:i"), 'id=tanggal') ?>
            <label>No. Pembelian:</label><?= form_input('nopembelian', isset($_GET['id'])?$rows['id_pembelian']:null, 'id=nopembelian size=30') ?> <?= form_hidden('nopemb', isset($_GET['id'])?$rows['id_pembelian']:null) ?>
            <label>Suplier:</label> <span class="label" id="suplier"><?= isset($_GET['id'])?$rows['nama']:null ?></span>
            <label>Total Tagihan (Rp.):</label><span class="label" id="total_tagihan"></span>
            <label>Total Terbayar (Rp.):</label><span class="label" id="total_terbayar"></span>
            <label>Sisa Tagihan (Rp.):</label><span class="label" id="sisa_tagihan"></span>
            <label>Bayar (Rp.):</label><?= form_input('bayar', null, 'id="bayar" onkeyup=FormNum(this) size=30') ?>
        </div>
    </fieldset>
    </div>
    <?= form_submit('simpan', 'Simpan', 'id=simpan') ?> 
    <?= form_button('Reset', 'Reset', 'id=reset') ?>
    <?= form_close() ?>
</div>