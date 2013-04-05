<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <?php $this->load->view('message') ?>
    <script type="text/javascript">
        function load_detail_bayar(id_kunjungan) {
            $.ajax({
                url: '<?= base_url('billing/load_data_pembayaran') ?>/'+id_kunjungan,
                cache: false,
                success: function(data) {
                    $('#result-pembayaran').html(data);
                }
            })
        }
        $(function() {
            var jumlah = $('.tr_rows').length-1;
            //for (i = 0; i <= jumlah; i++) {
            $('button[id=resetan]').button({
                icons: {
                    secondary: 'ui-icon-refresh'
                }
            });
            $('#click').each(function(){
                $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
            });
            $('#click').button({
                icons: {
                    secondary: 'ui-icon-circle-check'
                }
            });
        
        
            $('.print').click(function() {
                var id_nota = $(this).attr('title');
                var pembayaran_ke = $(this).attr('name');
                window.open('<?= base_url('billing/cetak') ?>/'+id_nota+'/'+pembayaran_ke, 'cetakbilling', 'location=1,status=1,scrollbars=1,width=820px,height=500px');
            })
            //}
            $('#resetan').click(function() {
                reset_all();
            })
            $('#cetak_kartu').click(function() {
                var id_kunjungan = $('#id_kunjungan').val();
                window.open('<?= base_url('billing/cetak') ?>/'+id_kunjungan, 'cetakbilling', 'location=1,status=1,scrollbars=1,width=820px,height=500px');
            })
            $('#id_kunjungan').autocomplete("<?= base_url('billing/get_data_kunjungan/') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].no_rm // nama field yang dicari
                        };
                    }
                    reset_all();
                    $('#no_rm, #nama_pasien').val('');
                    return parsed;
                
                },
                formatItem: function(data,i,max){
                    if (data.no_daftar != null) {
                        var str = '<div class=result>'+data.no_daftar+' - '+data.nama+'</div>';
                    }
                    return str;
                },
                width: 370, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.no_daftar);
                fill_field(data);
            });
        
            $('#no_rm').autocomplete("<?= base_url('billing/get_data_pasien/') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].no_rm // nama field yang dicari
                        };
                    }
                    reset_all();
                    $('#id_kunjungan, #nama_pasien').val('');
                    return parsed;
                
                },
                formatItem: function(data,i,max){
                    if (data.no_daftar != null) {
                        var str = '<div class=result>'+data.no_rm+' - '+data.nama+'</div>';
                    }
                    return str;
                },
                width: 370, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.no_rm);
                fill_field(data);
            });
        
            $('#nama_pasien').autocomplete("<?= base_url('billing/get_data_pasien/') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].no_rm // nama field yang dicari
                        };
                    }
                    reset_all();
                    $('#id_kunjungan, #no_rm').val('');
                    return parsed;
                
                },
                formatItem: function(data,i,max){
                    if (data.no_daftar != null) {
                        var str = '<div class=result>'+data.no_rm+' - '+data.nama+'</div>';
                    }
                    return str;
                },
                width: 370, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                fill_field(data);
            });
        
        
            $('#bulat, #serahuang').keyup(function() {
                hitung_kembalian();
            })
        
        
            $('#formbayar').submit(function(){
                var url = $(this).attr('action');
                var id_kunjungan = $('#id_kunjungan').val();
                $.ajax({
                    type: 'POST',
                    data: $(this).serialize(),
                    url: url,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == true) {
                            load_detail_bayar(id_kunjungan);
                            $('#')
                            alert_tambah();
                        }
                    }
                })
                return false;
            });
        
        });
    
        function reset_all(){
            $('#no_rm, #nama_pasien, #id_kunjungan,#bulat,#serahuang, #bayar').val('');   
            $('#kembalian, #total-pembayaran, #produk, #result-pembayaran').html('');
        }
        function hitung_kembalian() {
            var diserahkan = currencyToNumber($('#serahuang').val());
            var pembulatan = currencyToNumber($('#bulat').val());
            var kembalian  = diserahkan - pembulatan;
            if (!isNaN(kembalian)) {
                if (kembalian < 0) {
                    $('#kembalian').html(kembalian);
                } else {
                    $('#kembalian').html(numberToCurrency(kembalian));
                }
            }
        }
        function pembulatan_seratus(angka) {
            var kelipatan = 100;
            var sisa = angka % kelipatan;
            if (sisa != 0) {
                var kekurangan = kelipatan - sisa;
                var hasilBulat = angka + kekurangan;
                return Math.ceil(hasilBulat);
            } else {
                return Math.ceil(angka);
            }
        }
        function fill_field(data){
            $('input[name=no_daftar]').val(data.id);
            $('#nama_pasien').val(data.nama);  
            $('#no_rm').val(data.no_rm);  
            $('input[name=kunjungan_billing_id]').val(data.kunjungan_billing_id);
            $('input[name=totallica]').val(data.tagihan);
            $('#id_kunjungan').val(data.no_daftar);
            //alert(data.tagihan)
            var id = data.id_pasien;
            $.ajax({
                url: '<?= base_url('billing/asuransi_kepesertaan_get_data') ?>/'+id,
                data: '',
                cache: false,
                success: function(msg) {
                    $('#produk').html(msg);
                }
            })
            var no_daftar = data.no_daftar;
            $.ajax({
                url: '<?= base_url('billing/total_tagihan') ?>/'+no_daftar,
                data: '',
                cache: false,
                dataType: 'json',
                success: function(msg) {
                    $('#total-pembayaran').html(numberToCurrency(msg.fuck));
                    $('#bayar').val(numberToCurrency(msg.fuck));
                    $('#bulat').val(numberToCurrency(pembulatan_seratus(msg.fuck)));
                    $('input[name=totallica]').val(msg.fuck);
                    if (msg.you != null || msg.you != 0) {
                        var sisa = msg.fuck - msg.you;
                        //alert('Sisa pembayaran untuk pasien '+data.nama+' adalah '+numberToCurrency(sisa)+' ');
                    }
                    
                }
            })
            var id_kunjungan = data.no_daftar;
            load_detail_bayar(id_kunjungan);
        }
    </script>
    <h1><?= $title ?></h1>
    <div class="data-input">

        <?php
        if (isset($attribute)) {
            foreach ($attribute as $rows)
                ;
        }
        ?>
        <?= form_open('billing/pembayaran_save', 'id=formbayar') ?>
        <fieldset><legend>Summary</legend>
            <label>No</label><span class="label" id="id_pembayaran"><?= get_last_id('kunjungan_billing_pembayaran', 'id') ?></span>
            <label>Tanggal</label><span class="label"><?= date("d/m/Y") ?></span>
            <label>No. RM</label><?= form_input('norm', isset($rows->no_daftar) ? $rows->no_rm : null, 'id=no_rm size=30') ?>
            <label>Nama Pasien</label><?= form_input('nama_pasien', isset($rows->no_daftar) ? $rows->nama : null, 'id=nama_pasien size=30') ?>
            <label>Nomor Kunjungan</label><?= form_input('id_kunjungan', isset($rows->no_daftar) ? $rows->no_daftar : null, 'id=id_kunjungan size=30') ?> <?= form_hidden('kunjungan_billing_id', isset($rows->kunjungan_billing_id) ? $rows->kunjungan_billing_id : null) ?>
            <label>Produk Asuransi</label><span class="label" id="produk"><?php
        if (isset($attribute)) {
            $asu = $this->m_billing->asuransi_kepesertaan_get_data($rows->id_pasien)->result();
            foreach ($asu as $value) {
                ?>
                        <?= $value->asuransi ?><br/>
                        <?php
                    }
                }
                ?></span>
            <label>Total (Rp.)</label><span class="label" id="total-pembayaran"><?= isset($rows->no_daftar) ? rupiah($rows->total_barang + $rows->total_jasa) : null ?></span>
            <label>Bayar (Rp.)</label><?= form_input('bayar', null, 'id=bayar onKeyup=FormNum(this) style="text-align: right" size=30') ?>
            <label>Pembulatan (Rp.)</label><?= form_input('bulat', NULL, 'id=bulat style="text-align: right" onKeyup=FormNum(this) size=30') ?>
            <label>Uang Diserahkan (Rp.)</label><?= form_input('serahuang', NULL, 'id=serahuang style="text-align: right" onKeyup=FormNum(this) size=30') ?>
            <label>Kembalian (Rp.)</label><span class="label" id="kembalian"></span>
            <label></label><?= form_hidden('totallica', null) ?> 
            <?= isset($attribute) ? '' : form_submit('data', 'Simpan', 'id=click') ?> 
            <?= form_button(null, 'Reset', 'id=resetan') ?>
        </fieldset>
        <?= form_close() ?>

        <div id="result-pembayaran">
            <?php if (isset($attribute)) { ?>
                <div class="circle">

                    <table class="tabel" width="100%">
                        <tr>
                            <th>No</th>
                            <th>Waktu</th>
                            <th>Total</th>
                            <th>Bayar</th>
                            <th>Pembulatan Bayar</th>
                            <th>Sisa</th>
                            <th>Cetak</th>
                        </tr>
                        <?php foreach ($list_data as $key => $data) { ?>
                            <tr class="tr_rows <?= ($key % 2 == 0) ? 'even' : 'odd' ?>">
                                <td align="center"><?= ++$key ?></td>
                                <td align="center"><?= datetime($data->waktu) ?></td>
                                <td align="right"><?= rupiah($data->total) ?></td>
                                <td align="right"><?= rupiah($data->bayar) ?></td>
                                <td align="right"><?= rupiah($data->pembulatan_bayar) ?></td>
                                <td align="right"><?= rupiah($data->sisa) ?></td>
                                <td align="center"><?= form_button($key . '/' . $rows->no_daftar . '/' . $rows->no_daftar, 'Cetak', 'title="' . $data->id_nota . '" class="print" id=cetak' . $key) ?></td>
                            </tr>
                            <script>
                                $(function() {
                                                                                                                                                                                                                            
                                    $('button[id=cetak<?= $key ?>]').button({
                                        icons: {
                                            secondary: 'ui-icon-print'
                                        }
                                    })
                                })
                            </script>
                        <?php } ?>
                    </table>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php die; ?>