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
                        value: data[i].dokumen_no // nama field yang dicari
                    };
                }
                return parsed;
            },
            formatItem: function(data,i,max)
            {
                if (data.id !== null) {
                    var str = '<div class=result>'+data.id+' - '+data.dokumen_no+' <br/>'+data.instansi+'</div>';
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
                $.ajax({
                    url: '<?= base_url('inv_autocomplete/cek_inkaso') ?>/'+data.id,
                    dataType: 'json',
                    cache: false,
                    success: function(hsl) {
                        var terbayar = (hsl.jumlah_terbayar !== null)?hsl.jumlah_terbayar:'0';
                        var sisa = data.total-terbayar;
                        if (sisa <= 0) {
                            alert('Sisa tagihan untuk nomor faktur '+data.dokumen_no+' adalah Rp. 0, \nsilahkan entrikan nomer pembelian / faktur dengan benar');
                            var hasil_sisa = numberToCurrency(Math.ceil(sisa));
                            $('#simpan').hide();
                            $('input').attr('disabled','disabled');
                        } else {
                            $('#simpan').show();
                            var hasil_sisa = Math.ceil(sisa);
                            $('#tanggal, #bayar').removeAttr('disabled', 'disabled');
                        }
                        $('#sisa_tagihan').html(numberToCurrency(hasil_sisa));
                        $('#bayar').val(numberToCurrency(hasil_sisa));
                    }
                });
                
                
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
                        $('#bayar, #nopembelian, #tanggal').attr('disabled', 'disabled');
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
        
        <?= form_hidden('id_layanan', isset($rows['id'])?$rows['id']:null, null) ?>
            <table width="100%" class="tabel-new">
                <tr style="line-height: 20px;"><td width="15%">No.:</td><td><span class="label"> <?= isset($_GET['id'])?$_GET['id']:get_last_id('inkaso', 'id') ?></span></td></tr>
                <tr style="line-height: 20px;"><td>Waktu:</td><td><?= form_input('tanggal', date("d/m/Y H:i"), 'id=tanggal') ?></td></tr>
                <tr style="line-height: 20px;"><td>No. Faktur / Pembelian:</td><td><?= form_input('nopembelian', isset($_GET['id'])?$rows['id_pembelian']:null, 'id=nopembelian size=30') ?> <?= form_hidden('nopemb', isset($_GET['id'])?$rows['id_pembelian']:null) ?></td></tr>
                <tr style="line-height: 20px;"><td>Suplier:</td><td> <span class="label" id="suplier"><?= isset($_GET['id'])?$rows['nama']:null ?></span></td></tr>
                <tr style="line-height: 20px;"><td>Total Tagihan (Rp.):</td><td><span class="label" id="total_tagihan"></span></td></tr>
                <tr style="line-height: 20px;"><td>Total Terbayar (Rp.):</td><td><span class="label" id="total_terbayar"></span></td></tr>
                <tr style="line-height: 20px;"><td>Sisa Tagihan (Rp.):</td><td><span class="label" id="sisa_tagihan"></span></td></tr>
                <tr style="line-height: 20px;"><td>Bayar (Rp.):</td><td><?= form_input('bayar', null, 'id="bayar" onkeyup=FormNum(this) size=30') ?></td></tr>
                <tr><td></td><td>
                        <?= form_submit('simpan', 'Simpan', 'id=simpan') ?> 
                        <?= form_button('Reset', 'Reset', 'id=reset') ?>
                    </td></tr>
            </table>
        
    </fieldset>
    </div>
    
    <?= form_close() ?>
</div>