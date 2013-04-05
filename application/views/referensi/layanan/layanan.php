<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        var request;
        $(function() {        
            $( "#addlayanan" ).button({icons: {primary: "ui-icon-circle-plus"}});
            $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('button[type=submit]').button({icons: {primary: 'ui-icon-circle-check'}});
            $('#reset, .resetan').button({icons: {secondary: 'ui-icon-refresh'}});
            get_layanan_list(1);
            $('#form_layanan').dialog({
                autoOpen: false,
                height: 250,
                width: 500,
                modal: true,
                resizable : false,
                close : function(){
                    reset_all();
                },
                open : function(){
                
                }
            });
            $('#addlayanan').click(function() {
                get_last_id();
                $('input[name=tipe]').val('add');
                $('#form_layanan').dialog("option",  "title", "Tambah Data Layanan");
                $('#form_layanan').dialog("open");
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
            
            $('#layAll').click(function(){
                get_layanan_list(1);
            });
        
            $('#reset').click(function() {
                reset_all();
            });
        
           
            $('#formlayanan').submit(function(){
                var Url = '';           
                var nama = $('#nama_layanan').val();
            
                
                if($('#nama_layanan').val()==''){
                    $('#msg_layanan').fadeIn('fast').html('Nama layanan tidak boleh kosong !');
                    $('#nama_layanan').focus();
                } else{    
                    $.ajax({
                        url: '<?= base_url('referensi/manage_layanan') ?>/cek',
                        data:'layanan='+nama,
                        cache: false,
                        dataType: 'json',
                        success: function(msg){
                            if (msg.status == false){
                                $('#text_konfirmasi').html('Nama Layanan <b>"'+nama+'"</b> sudah ada<br/> Apakah anda yakin akan menambahkannya lagi?');            
                            } else {
                                $('#text_konfirmasi').html('Nama Layanan <b>"'+nama+'"</b><br/> Apakah anda akan menyimpan data?');                    
                            }
                        
                            $('#konfirmasi').dialog("open");
                        }
                    });
                
                   
              
                    return false;
                }
                return false;
            });
        
        
        });
    
        function save(){
            var tipe = $('input[name=tipe]').val();
            if(tipe == 'add'){
                Url = '<?= base_url('referensi/manage_layanan') ?>/add/';
            }else{
                Url = '<?= base_url('referensi/manage_layanan') ?>/edit/';
            }
            if(!request) {
                request = $.ajax({
                    type : 'POST',
                    url: Url+$('.noblock').html(),               
                    data: $('#formlayanan').serialize(),
                    cache: false,
                    success: function(data) {
                        $('#layanan_list').html(data);
                        $('#form_layanan').dialog("close");
                        if(tipe == 'add'){
                            alert_tambah();
                        }else{
                            alert_edit();
                        }
                        reset_all();  
                        request = null;
                    }
                });
            }
        }
    
        function reset_all(){
            $('input[name=tipe]').val('');
            $('input[name=id_layanan]').val('');
            $('#nama_layanan').val('');
            $('#bobot').val('');
            $('#kelas').val('');
      
            $('#msg_layanan').fadeOut('fast');
        }
    
        function get_last_id(){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/get_last_id') ?>/layanan/id',
                cache: false,
                dataType : 'json',
                success: function(data) {
                    $('#nomor_id').html(data.last_id);
                    $('input[name=id_layanan]').val(data.last_id);
                }
            });
        }
    
        function get_layanan_list(p){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_layanan') ?>/list/'+p,
                cache: false,
                success: function(data) {
                    $('#layanan_list').html(data);
                    reset_all();
                }
            });
        }
    
        function delete_layanan(id){
            var del = confirm("Anda yakin akan menghapus data ini ?");
            if(del){
                $.ajax({
                    type : 'GET',
                    url: '<?= base_url('referensi/manage_layanan') ?>/delete/'+$('.noblock').html(),
                    data :'id='+id,
                    cache: false,
                    success: function(data) {
                        $('#layanan_list').html(data);
                        alert_delete();
                    }
                });
            }
        }
    
        function edit_layanan(arr){
            var data = arr.split("#");
        
            $('input[name=tipe]').val('edit');
        
            $('input[name=id_layanan]').val(data[0]);
            $('#nomor_id').html(data[0]);
            $('#nama_layanan').val(data[1]);
            $('#bobot').val(data[2]);
            $('#kelas').val(data[3]);
            $('#form_layanan').dialog("option",  "title", "Edit Data Layanan");
            $('#form_layanan').dialog("open");
            $('input[name=save]').focus();
        
        }
    
        function paging(page, tab,search){
            get_layanan_list(page);
        }
    </script>
    <h1><?= $title ?></h1>

    <?= form_button('', 'Tambah Data', 'id=addlayanan') ?>
    <?= form_button('', 'Reset', 'class=resetan id=layAll') ?>
    <div id="form_layanan" style="display: none;position: relative; background: #fff; padding: 10px;">

        <div class='msg' id="msg_layanan"></div>
        <?= form_open('', 'id=formlayanan') ?>
        <?= form_hidden('tipe') ?>

        <table width="100%">
            <tr><td width="15%">No.</td><td><span id="nomor_id"></span> <?= form_hidden('id_layanan') ?></td> </tr>
            <tr><td>Nama</td><td><?= form_input('nama', '', 'id=nama_layanan size=45') ?></td> </tr>
            <tr><td>Bobot</td><td><?= form_dropdown('bobot', $bobot, null, 'id=bobot') ?></td></tr>
            <tr><td>Kelas</td><td><?= form_dropdown('kelas', $kelas, null, 'id=kelas') ?></td> </tr>
            <tr><td></td><td><?= form_submit('simpan', 'Simpan', 'id=simpan') ?> 
                    <?= form_button('', 'Reset', 'id=reset') ?></td> </tr>
        </table>
        <?= form_close() ?>
    </div>

    <div id="konfirmasi" style="display: none; padding: 20px;">
        <div id="text_konfirmasi"></div>
    </div>

    <div id="list" class="data-list">
        <div id="layanan_list"></div>
    </div>
</div>