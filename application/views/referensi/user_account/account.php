<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        $(function() {
            get_user_list(1);
        
            $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('button[type=submit]').button({icons: {primary: 'ui-icon-circle-check'}});
            $('#reset,.resetan').button({icons: {secondary: 'ui-icon-refresh'}});
            $('#showAllUser').click(function(){
                get_user_list(1);
            });
            
            $('#nama').autocomplete("<?= base_url('inv_autocomplete/load_data_user_system') ?>",
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
                    var kelurahan = '';
                    if (data.kelurahan!=null) { var kelurahan = data.kelurahan; }
                    var str = '<div class=result>'+data.nama+' <br/> '+data.alamat+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('input[name=id_penduduk]').val(data.id);
        
            });
            $('#reset').click(function(){
                reset_all();
            });
      
            $('#privform').dialog({
                autoOpen: false,
                height: 500,
                width: 800,
                modal: true,
                resizable : false,
                close : function(){
                    reset_all();
                },
                open : function(){
              
                }
            });
        
            $('#form').submit(function(){
                var Url = '<?= base_url('referensi/manage_user') ?>/add/';
           
                if($('input[name=id_penduduk]').val()==''){
                    $('.msg').fadeIn('fast').html('Nama tidak boleh kosong !');
                    $('#nama').focus();
                } else if($('#username').val()==''){
                    $('.msg').fadeIn('fast').html('Username tidak boleh kosong !');
                    $('#username').focus();
                }else{    
                
                    $.ajax({
                        type : 'POST',
                        url: Url+$('.noblock').html(),               
                        data: $(this).serialize(),
                        cache: false,
                        success: function(data) {
                            $('#user_list').html(data);
                            reset_all();
                            alert_tambah();
                    
                        }
                    });
              
                    return false;
                }
                return false;
            });
        });
    
        function reset_all(){
            $('input[name=id_penduduk]').val('');
            $('input[name=id]').val('');
            $('#nama').val('');
            $('#username').val('');
            $('.msg').fadeOut('fast');
        }
    
        function get_user_list(p){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_user') ?>/list/'+p,
                cache: false,
                success: function(data) {
                
                    $('#user_list').html(data);
                    reset_all();
                }
            });
        }
    
        function get_privileges_list(){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_privileges') ?>/list',
                data :'id='+$('input[name=id]').val(),
                cache: false,
                success: function(data) {
                
                    $('#list').html(data);
                    reset_all();
                }
            });
        }
        function paging(page, tab){
            get_user_list(page);
        }
    
        function edit_user(id){
            $('input[name=id]').val(id);
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_user') ?>/edit/'+1,
                data :'id='+id,
                cache: false,
                success: function(data) {                
                    $('#privileges').html(data);             
                }
            });
            $('#privform').dialog("option",  "title", "Edit User Privileges");
            $('#privform').dialog("open");
        }
    
        function delete_user(id){
            var del = confirm("Anda yakin akan menghapus data ini ?");
            if(del){
                $.ajax({
                    type : 'GET',
                    url: '<?= base_url('referensi/manage_user') ?>/delete/'+$('.noblock').html(),
                    data :'id='+id,
                    cache: false,
                    success: function(data) {
                        $('#user_list').html(data);
                        alert_delete();
                    }
                });
            }
        }
        
        
    </script>
    <h1><?= $title ?></h1>
    <div class="data-input">
        <fieldset><legend>Parameter</legend>
            <div class='msg'></div>
            <?= form_open('', 'id = form') ?>
            
                <?= form_hidden('id') ?>
                <label>Nama Penduduk</label><?= form_input('nama', null, 'id=nama size=30') ?> <?= form_hidden('id_penduduk') ?>
                <label>Username</label><?= form_input('username', '', 'id=username size=30') ?>
                <label>Password</label><?= form_input('password', NULL, 'id=password size=30') ?>
                <label></label>
                    <?= form_submit('ubahpassword', 'Simpan', 'class=tombol') ?>
                    <?= form_button('', 'Reset', 'class=resetan id=showAllUser') ?>
            <?= form_close() ?>
        </fieldset>
    </div>
    <div id="user_list"></div>
    <div id="privform" style="display: none;position: relative; background: #fff; padding: 10px;">
        <div id="privileges"></div>
    </div>
</div>