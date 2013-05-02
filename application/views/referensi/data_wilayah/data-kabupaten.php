<script type="text/javascript">
    function create_form_kabupaten() {
        var str = '<div id=datainput><div class="msg"></div>'+
                '<form action="" id=formkab>'+
                '<input type=hidden name=tipe value=add /><input type=hidden name=id />'+
                '<table>'+
                    '<tr><td align=right>Nama Provinsi:</td><td><input type=text name=provinsi class=provinsi-kab size=30 /><input type=hidden name=idprovinsikab /></td></tr>'+
                    '<tr><td align=right>Kode:</td><td><input type=text name=kodekab id=kodekab size=10 /></td></tr>'+
                    '<tr><td align=right>Nama Kabupaten:</td><td><input type=text name=kabupaten id=kabupaten size=30 /></td></tr>'+
                '</table></form></div>';
        $('#loaddata').append(str);
        $('.provinsi-kab').autocomplete("<?= base_url('referensi/get_provinsi') ?>",
        {
            parse: function(data)
            {
                var parsed = [];
                for (var i=0; i < data.length; i++)
                {
                    parsed[i] =
                        {
                        data: data[i],
                        value: data[i].nama // nama field yang dicari
                    };
                }
                $('input[name=idprovinsikab]').val('');
                return parsed;
            },
            formatItem: function(data,i,max)
            {
                var str = '<div class=result>'+data.nama+'</div>';
                return str;
            },
            width: 270, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated) {
            $(this).attr('value',data.nama);
            $('input[name=idprovinsikab]').val(data.id);
        });
        $('#datainput').dialog({
            title: 'Form Referensi Kabupaten',
            autoOpen: true,
            height: 200,
            width: 350,
            modal: true,
            buttons: {
                "Simpan": function() {
                    save_kab();
                    $(this).dialog().remove();
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
        get_kabupaten_list(1);
        //initial
        $( "#addkab" ).click(function() {
            create_form_kabupaten();
        });
        $( "#addkab" ).button({icons: {primary: "ui-icon-circle-plus"}});
        $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
        $('button[type=submit]').button({icons: {primary: 'ui-icon-circle-check'}});
        $('#resetkab, .resetan').button({icons: {secondary: 'ui-icon-refresh'}});
        $('#addkab').click(function() {
            $('input[name=tipe]').val('add');
            $('#form-kab').dialog("option","title","Tambah Data Kabupaten");
            $('#form-kab').dialog('open');
            $('#provinsi').focus();
        });
        $('#form-kab').dialog({
            autoOpen: false,
            height: 200,
            width: 400,
            modal: true,
            resizable : false,
            close : function(){
                reset_all();
            }
        });
        
        $('#konfirmasi_kab').dialog({
            autoOpen: false,
            title :'Konfirmasi',
            height: 200,
            width: 300,
            modal: true,
            resizable : false,
            buttons: [ 
                { text: "Ok", click: function() { 
                        save_kab();
                        $( this ).dialog( "close" ); 
                    } 
                }, 
                { text: "Batal", click: function() { 
                        $( this ).dialog( "close" ); 
                    } 
                } 
            ]
        });
        
        $('#resetkab').click(function() {
            $('.form-submit').fadeOut('fast');
            reset_all();
        });
        
        $('#showKabAll').click(function(){
            get_kabupaten_list(1);
        });
        
        $('#provinsi-kab').focus();
        $('#formkab').submit(function(){    
            var kabupaten = $('#kabupaten').val();
            var provid= $('input[name=idprovinsikab]').val();
            var prov = $('.provinsi-kab').val();
            
            if($('#kabupaten').val()==''){
                $('.msg').fadeIn('fast').html('Nama kabupaten tidak boleh kosong !');
                $('#kabupaten').focus();
                return false;
            }else if($('input[name=idprovinsikab]').val() == ''){
                $('.msg').fadeIn('fast').html('Data Provinsi tidak boleh kosong !');
                $('.provinsi-kab').focus();
                return false;
            }else{               
                $.ajax({
                    url: '<?= base_url('referensi/manage_kabupaten') ?>/cek',
                    data:'kabupaten='+kabupaten+'&provid='+provid,
                    cache: false,
                    dataType: 'json',
                    success: function(msg){
                        if (!msg.status){
                            $('#text_konfirmasi_kab').html('Nama Kabupaten <b>"'+kabupaten+'"</b> dengan Provinsi <b>"'+prov+'"</b><br/>Apakah anda akah menyimpan data '+kabupaten+'?');            
                        } else {
                            $('#text_konfirmasi_kab').html('Nama Kabupaten <b>"'+kabupaten+'"</b> dengan Provinsi <b>"'+prov+'"</b> <br/> Apakah anda akan menyimpan data '+kabupaten+'?');                    
                        }
                        $('#konfirmasi_kab').dialog("open");
                    }
                });
            }
                
            return false;
        });
    });
    
    function save_kab(){
        var Url = '';           
        var status = $('input[name=tipe]').val();
        if(status === 'edit'){
            Url = '<?= base_url('referensi/manage_kabupaten') ?>/edit/';
        }else{
            Url = '<?= base_url('referensi/manage_kabupaten') ?>/add/';
        }
        $.ajax({
            type : 'POST',
            url: Url+$('.noblock').html(),               
            data: $('#formkab').serialize(),
            cache: false,
            success: function(data) {
                $('#kab_list').html(data);
                $('#form-kab').dialog('close');
                if (status === 'edit'){
                    alert_edit();
                } else{
                    alert_tambah();
                }
                reset_all();
                    
            }
        });
    }
    
    function reset_all(){
        $('input[name=tipe]').val('');
        $('#kabupaten').val('');
        $('input[name=idprovinsikab]').val('');
        $('.provinsi-kab').val('');
        $('#kode').val('');
        $('.msg').fadeOut('fast');
    }
    
    function get_kabupaten_list(p){
        $.ajax({
            type : 'GET',
            url: '<?= base_url('referensi/manage_kabupaten') ?>/list/'+p,
            cache: false,
            success: function(data) {
                $('#kab_list').html(data);
                reset_all();
            }
        });
    }
    
    function delete_kabupaten(id){
        var del = confirm("Anda yakin akan menghapus data ini ?");
        
        if(del){
            
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_kabupaten') ?>/delete/'+$('.noblock').html(),
                data :'id='+id,
                cache: false,
                success: function(data) {
                    $('#kab_list').html(data);
                    alert_delete();                   
                }
            });
        }
    }
    
    function edit_kabupaten(id, nama,p_id,p_nama,kode){
        create_form_kabupaten();
        $('input[name=tipe]').val('edit');
        $('input[name=id]').val(id);
        $('#kabupaten').val(nama);
        $('.provinsi-kab').val(p_nama);
        $('input[name=idprovinsikab]').val(p_id);
        $('#kodekab').val(kode);
        $('#form-kab').dialog("option", "title", "Edit Data Kabupaten");
        $('#form-kab').dialog('open');
        $('#savekab').focus();
        
    }
   
</script>
<?= form_button('', 'Tambah Data', 'id=addkab') ?>
<?= form_button('', 'Reset', 'class=resetan id=showKabAll') ?>
<div class="data-list">
    <div id="konfirmasi_kab" style="display: none; padding: 20px;">
        <div id="text_konfirmasi_kab"></div>
    </div>
    <div id="kab_list"></div>
</div>

