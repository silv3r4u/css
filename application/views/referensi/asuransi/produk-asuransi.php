<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        var Url = '';
        $(function() {
            //initial
            get_produk_asuransi_list(1);
            $('#form-prov').hide();
            $('#addnewrow').button({icons: {secondary: 'ui-icon-circle-plus'}});
            $('input[type=submit]').each(function(){
                $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
            });
            $('button[type=submit]').button({
                icons: {
                    primary: 'ui-icon-circle-check'
                }
            });
            $('#reset, #resetproedit, .resetan').button({icons: {secondary: 'ui-icon-refresh'}});
            //initial
            $('#showAll').click(function(){
                get_produk_asuransi_list(1);
                reset_all();
            });
            $('#konfirmasi').dialog({
                autoOpen: false,
                title :'Konfirmasi',
                height: 200,
                width: 300,
                modal: true,
                resizable : false,
                buttons: [ 
                    { text: "Ok", click: function() { 
                            save();
                            $( this ).dialog( "close" ); 
                        } 
                    }, 
                    { text: "Batal", click: function() { 
                            $( this ).dialog( "close" ); 
                        } 
                    } 
                ]
            });
        
            $('#suplier').autocomplete("<?= base_url('referensi/get_relasi_instansi') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                        $('#id_ap').val('');
                    }
                    return parsed;
            
                },
        
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.nama+'<br/>'+data.alamat+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).attr('value',data.nama);
                $('input[name=id_ap]').val(data.id);
            });
            $('#addnewrow').click(function() {
                $.ajax({
                    url: '<?= base_url('referensi/get_produk_asuransi_last_no') ?>',   
                    dataType:'json',
                    cache: false,
                    success: function(data) {
                        $('input[name=id_produk]').val(data.no);
                        $('#no_produk').html(data.no);
                    
                    }
                });
                $('input[name=tipe]').val('add');
            
                $('#form-prov').fadeIn('fast', function() {
                    $('#asuransi').focus();
                });
            })
            $('#reset').click(function() {
                reset_all();
                $('#form-prov').fadeOut('fast');
           
            });
        
            $('#formadd').submit(function(){
                save();
                return false;
            });
        });
        
        function save(){
            if($('input[name=tipe]').val() == 'add'){
                Url = '<?= base_url('referensi/produk_asuransi_add') ?>/1';
            }else{
                Url = '<?= base_url('referensi/produk_asuransi_edit') ?>/1';
            }
            
            $.ajax({
                type : 'POST',
                url: Url,               
                data: $('#formadd').serialize(),
                cache: false,
                success: function(data) {
                    $('#asu_list').html(data);
                    $('#form-prov').fadeOut('fast');
                        
                    if($('input[name=tipe]').val() === 'add'){
                        alert_tambah();
                    }else{
                        alert_edit();
                    }
                    reset_all();
                }
            });
        }
    
        function reset_all(){
            $('#ya').removeAttr('checked');
            $('#tidak').attr('checked', true);
            $('input[name=id_ap]').val('');
            $('#suplier').val('');
            $('#nama').val('');
            $('#disk_persen').val('');
            $('#disk_rupiah').val('');
            $('.msg').fadeOut('fast');
        }
    
        function get_produk_asuransi_list(p){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/produk_asuransi_list') ?>/'+p, 
                cache: false,
                success: function(data) {
                    $('#asu_list').html(data);
                }
            });
        }
    
        function delete_produk_asuransi(id){
            var del = confirm("Anda yakin akan menghapus data ini ?");
            if(del){
                $.ajax({
                    type : 'GET',
                    url: '<?= base_url('referensi/produk_asuransi_delete') ?>/'+$('.noblock').html(),
                    data :'id='+id,
                    cache: false,
                    success: function(data) {
                        $('#asu_list').html(data);
                        alert_delete();
                    }
                });
            }
        }
    
        function edit_produk_asuransi(id, nama,id_ap,suplier,reim, persen, rupiah){
            $('input[name=tipe]').val('edit');
            $('input[name=id_produk]').val(id);
            $('input[name=id_ap]').val(id_ap);
            $('#suplier').val(suplier);
            $('#no_produk').html(id);  
            $('#nama').val(nama);    
            $('#disk_persen').val(persen);
            $('#disk_rupiah').val(numberToCurrency(rupiah));
            if(reim=='Ya'){
                $('#ya').attr('checked', 'checked');
            }
            $('#form-prov').show();
        
        }
        function paging(page, tab, cari){
            get_produk_asuransi_list(page);
        }
    
    </script>
    <h1><?= $title ?></h1>
    <?= form_button('', 'Tambah data', 'id=addnewrow') ?>
    <?= form_button('', 'Reset', 'class=resetan id=showAll') ?>
    <div id="list" class="data-list">
        <div id="form-prov" class="form-submit">
            <div class="msg"></div>
            <?= form_open('', 'id=formadd') ?>
            <table>
                <?= form_hidden('tipe') ?>
                <tr><td>No</td><td><span id="no_produk"></span><?= form_hidden('id_produk') ?></td> </tr>
                <tr><td>Nama Perusahaan</td><td><?= form_input('perusahaan', '', 'id=suplier size=40') ?>
                        <?= form_hidden('id_ap') ?>
                <tr><td>Nama Produk</td><td><?= form_input('nama', '', 'id=nama size=40') ?></td></tr>
                <tr><td>Reimbursement</td><td><?= form_radio('reimbursement', 'Ya', '', 'id=ya') ?>Ya
                        <?= form_radio('reimbursement', 'Tidak', 'true', 'id=tidak') ?>Tidak</td></tr>
                <tr><td>Diskon (%)</td><td><input type=text name=disk_persen id=disk_persen class=disk_persen value="0" /></td></tr>
                <tr><td>Diskon (Rp.)</td><td><input type=text name=disk_rupiah id=disk_rupiah class=disk_rupiah value="0" /></td></tr>
                <tr><td></td><td><?= form_submit('addasuransi', 'Simpan', null) ?>
                        <?= form_button('', 'Reset', 'id=reset') ?></td> </tr>
            </table>
            <?= form_close() ?>
            NB: Jika nama perusahaan asuransi sudah tersedia, dapat menggunakan <br/>fasilitas autocomplete. Jika belum ada isikan secara manual
        </div>

        <div id="konfirmasi" style="display: none; padding: 20px;">
            <div id="text_konfirmasi"></div>
        </div>
        <div id="asu_list"></div>

    </div>
</div>