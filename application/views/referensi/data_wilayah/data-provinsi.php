<script type="text/javascript">
    function create_form_provinsi() {
        var str = '<div id=datainput><div class="msg"></div>'+
                '<form action="" id=formpro>'+
                '<input type=hidden name=tipe value=add /><input type=hidden name=id />'+
                '<table>'+
                    '<tr><td align=right>Kode:</td><td><input type=text name=kode id=kode size=10 /></td></tr>'+
                    '<tr><td align=right>Nama Provinsi:</td><td><input type=text name=provinsi id=provinsi size=40 /></td></tr>'+
                '</table></div>';
        $('#loaddata').append(str);
        $('#datainput').dialog({
            title: 'Form Referensi Provinsi',
            autoOpen: true,
            height: 170,
            width: 370,
            modal: true,
            buttons: {
                "Simpan": function() {
                    save_prov();
                },
                "Batal": function() {
                    $(this).dialog().remove();
                }
            },
            close : function(){
                $(this).dialog().remove();
            }
        });
    }
    $(function() {
        //initial
        get_provinsi_list(1);
        $('#prov').click(function() {
            create_form_provinsi();
        });
        $("#prov").button({icons: {primary: "ui-icon-circle-plus"}});
        $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
        $('button[type=submit]').button({icons: {primary: 'ui-icon-circle-check'}});
        $('#reset, .resetan').button({icons: {secondary: 'ui-icon-refresh'}});
     
        $('#prov').click(function() {
            $('input[name=tipe]').val('add');
            $('#form-prov').dialog("option","title", "Tambah Data Provinsi");
            $('#form-prov').dialog("open");
        });
        
        $('#konfirmasi_prov').dialog({
            autoOpen: false,
            title :'Konfirmasi',
            height: 200,
            width: 300,
            modal: true,
            resizable : false,
            close : function(){
                
            },
            open : function(){
                
            },
            buttons: [ 
                { text: "Ok", click: function() { 
                        save_prov();
                        $( this ).dialog( "close" ); 
                    } 
                }, 
                { text: "Batal", click: function() { 
                        $( this ).dialog( "close" ); 
                    } 
                } 
            ]
        });
        
        $('#showProAll').click(function(){
            $('#loaddata').empty();
            $('#loaddata').load('<?= base_url('referensi/data_wilayah') ?>');
        });
        
        $('#reset').click(function() {
            reset_all();
        });
        
     
        
        $('#formpro').submit(function(){
            var provinsi = $('#provinsi').val();
            
            
            if($('#provinsi').val()!=''){
                $.ajax({
                    url: '<?= base_url('referensi/manage_provinsi') ?>/cek',
                    data:'provinsi='+provinsi,
                    cache: false,
                    dataType: 'json',
                    success: function(msg){
                        if (msg.status == false){
                            $('#text_konfirmasi_prov').html('Nama Provinsi <b>"'+provinsi+'"</b> sudah ada<br/> Apakah anda yakin akan menambahkannya lagi?');            
                        } else {
                            $('#text_konfirmasi_prov').html('Nama Provinsi <b>"'+provinsi+'"</b> <br/> Apakah anda akan menyimpan data?');                    
                        }
                        
                        $('#konfirmasi_prov').dialog("open");
                    }
                });
            }else{
                $('.msg').fadeIn('fast').html('Nama provinsi tidak boleh kosong !');
                $('#provinsi').focus();
                return false;
            }
            return false;
        });
   
    
    });
    
    function save_prov(){
        var Url = '';           
        var status = $('input[name=tipe]').val();
        if(status === 'edit'){
            Url = '<?= base_url('referensi/manage_provinsi') ?>/edit/';
        }else{
            Url = '<?= base_url('referensi/manage_provinsi') ?>/add/';
        }
        $.ajax({
            type : 'POST',
            url: Url+$('.noblock').html(),               
            data: $('#formpro').serialize(),
            cache: false,
            success: function(data) {
                $('#pro_list').html(data);
                $('#form-prov').dialog("close");
                
                if(status === 'edit'){
                    alert_edit();
                } else{
                    alert_tambah();
                }
                $('#datainput').dialog().remove();
            }
        });
    }
    
    function reset_all(){
        $('input[name=tipe]').val('');
        $('#provinsi').val('');
        $('#kode').val('');
        $('.msg').fadeOut('fast');
    }
    
    function get_provinsi_list(p){
        $.ajax({
            type : 'GET',
            url: '<?= base_url('referensi/manage_provinsi') ?>/list/'+p,
            cache: false,
            success: function(data) {
                $('#pro_list').html(data);
                reset_all();
            }
        });
    }
    
    function delete_provinsi(id){
        var del = confirm("Anda yakin akan menghapus data ini ?");
        if(del){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_provinsi') ?>/delete/'+$('.noblock').html(),
                data :'id='+id,
                cache: false,
                success: function(data) {
                    $('#pro_list').html(data);
                    alert_delete();
                }
            });
        }
    }
    
    function edit_provinsi(id, nama,kode){
        create_form_provinsi();
        $('input[name=tipe]').val('edit');
        $('input[name=id]').val(id);
        $('#provinsi').val(nama);
        $('#kode').val(kode);
        $('#form-prov').dialog("option","title", "Edit Data Provinsi");
        $('#form-prov').dialog("open");
        $('#simpan').focus();
    }
    
</script>
<?= form_button('', 'Tambah Data', 'id=prov') ?>
<?= form_button('', 'Reset', 'class=resetan id=showProAll') ?>
<div id="list" class="data-list">
    <div id="konfirmasi_prov" style="padding: 20px;">
        <div id="text_konfirmasi_prov"></div>
    </div>

    <div id="pro_list"></div>
</div>