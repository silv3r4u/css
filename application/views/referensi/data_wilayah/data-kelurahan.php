<script type="text/javascript">

    $(function()
    {
        get_kelurahan_list(1);
        $( "#addkel" ).button({icons: {primary: "ui-icon-circle-plus"}})
        $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
        $('button[type=submit]').button({icons: {primary: 'ui-icon-circle-check'}});
        $('#resetkel, .resetan').button({icons: {secondary: 'ui-icon-refresh'}});
        
        $('#addkel').click(function() {
            $('input[name=tipe]').val('add');
            $('#form-kel').dialog("option","title","Tambah Data Kelurahan");
            $('#form-kel').dialog('open');
            $('.kecamatan-kel').focus();
        });
        
        $('#form-kel').dialog({
            autoOpen: false,
            height: 200,
            width: 400,
            modal: true,
            resizable : false,
            close : function(){
                reset_all();
            }
        });
        
        $('#konfirmasi_kel').dialog({
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
                        save_kel();
                        $( this ).dialog( "close" ); 
                    } 
                }, 
                { text: "Batal", click: function() { 
                        $( this ).dialog( "close" ); 
                    } 
                } 
            ]
        });
        
        $('#showKelAll').click(function(){
            get_kelurahan_list(1);
        });
        
        $('#resetkel').click(function() {
            reset_all();
        });
        $('.kecamatan-kel').focus();
        $('.kecamatan-kel').autocomplete("<?= base_url('referensi/get_kecamatan') ?>",
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
               $('input[name=idkecamatankel]').val("");
                return parsed;
            },
            formatItem: function(data,i,max)
            {
                var str = '<div class=result><b style="text-transform:capitalize">'+data.nama+'</b><br />Kab: '+data.kabupaten+' - Prov: '+data.provinsi+'</div>';
                return str;
            },
            width: 270, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated)
        {
            $(this).val(data.nama);
            $('input[name=idkecamatankel]').val(data.id);
        }
    );
       
         
        $('#formkel').submit(function(){
            var kelurahan = $('#kelurahan').val();
            var kec = $('.kecamatan-kel').val();
            var kecid=  $('input[name=idkecamatankel]').val();
            
            if($('#kelurahan').val()==''){
                $('.msg').fadeIn('fast').html('Nama kelurahan tidak boleh kosong !');
                $('#kelurahan').focus();
                return false;
            }else if($('input[name=idkecamatankel]').val() == ''){
                $('.msg').fadeIn('fast').html('Data Kecamatan tidak boleh kosong !');
                $('.kecamatan-kel').focus();
                return false;
            }else{               
                $.ajax({
                    url: '<?= base_url('referensi/manage_kelurahan') ?>/cek',
                    data:'kelurahan='+kelurahan+'&kecid='+kecid,
                    cache: false,
                    dataType: 'json',
                    success: function(msg){
                        if (!msg.status){
                            $('#text_konfirmasi_kel').html('Nama Kelurahan <b>"'+kelurahan+'"</b> dengan Kecamatan <b>"'+kec+'"</b> sudah ada<br/> Apakah anda yakin akan menambahkannya lagi?');            
                        } else {
                            $('#text_konfirmasi_kel').html('Nama Kelurahan <b>"'+kelurahan+'"</b> dengan Kecamatan <b>"'+kec+'"</b> <br/> Apakah anda akan menyimpan data?');                    
                        }
                        
                        $('#konfirmasi_kel').dialog("open");
                    }
                });
            }
                
            return false;
        });
    });
    
    function save_kel(){
        var Url = '';           
        var status = $('input[name=tipe]').val();
        if($('input[name=tipe]').val() === 'add'){
            Url = '<?= base_url('referensi/manage_kelurahan') ?>/add/';
        }else{
            Url = '<?= base_url('referensi/manage_kelurahan') ?>/edit/';
        }
        
        $.ajax({
            type : 'POST',
            url: Url+$('.noblock').html(),               
            data: $('#formkel').serialize(),
            cache: false,
            success: function(data) {
                $('#kel_list').html(data);
                $('#form-kel').dialog("close");
                if(status == 'add'){
                    alert_tambah();
                }else{
                    alert_edit();
                }
                reset_all();
                    
            }
        });
    }
    
    function reset_all(){
        $('input[name=tipe]').val('');
        $('input[name=idkecamatankel]').val('');
        $('input[name=id]').val('');
        $('.kecamatan-kel').val('');
        $('#kelurahan').val('');
        $('#kodekel').val('');
        $('.msg').fadeOut('fast');
    }
    
    function get_kelurahan_list(p){
        $.ajax({
            type : 'GET',
            url: '<?= base_url('referensi/manage_kelurahan') ?>/list/'+p,
            cache: false,
            success: function(data) {
                $('#kel_list').html(data);
                reset_all();
            }
        });
    }
    
    function delete_kelurahan(id){
        var del = confirm("Anda yakin akan menghapus data ini ?");
        if(del){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_kelurahan') ?>/delete/'+$('.noblock').html(),
                data :'id='+id,
                cache: false,
                success: function(data) {
                    $('#kel_list').html(data);
                    alert_delete();
                }
            });
        }
    }
    
    function edit_kelurahan(id, nama,kec_id,kec_nama,kode){
        $('input[name=tipe]').val('edit');
        $('input[name=id]').val(id);
        $('#kelurahan').val(nama);
        $('.kecamatan-kel').val(kec_nama);
        $('input[name=idkecamatankel]').val(kec_id);
        $('#kodekel').val(kode);
        $('#form-kel').dialog("option", "title", "Edit Data Kelurahan");
        $('#form-kel').dialog("open");   
        $('#savekel').focus();
    }
    
   

</script>
<?= form_button('', 'Tambah Data', 'id=addkel') ?>
<?= form_button('', 'Reset', 'class=resetan id=showKelAll') ?>
<div class="data-list">
    <div id="form-kel" style="display: none">
        <div class="msg"></div>
        <?= form_open('', 'id=formkel') ?>
        <table>
            <?= form_hidden('tipe') ?>
            <?= form_hidden('id') ?>
            <tr><td>Nama Kecamatan</td><td><?= form_input('', null, 'class=kecamatan-kel size=30') ?> <?= form_hidden('idkecamatankel') ?></td></tr>
            <tr><td>Nama Kelurahan</td><td><?= form_input('kelurahan', null, 'id=kelurahan size=30') ?></td></tr>
            <tr><td>Kode Kelurahan</td><td><?= form_input('kodekel', null, 'id=kodekel size=10') ?></td></tr>
            <tr><td></td><td><?= form_submit('addkel', 'Simpan', 'id=savekel') ?> <?= form_button('', 'Reset', 'class=cancel id=resetkel') ?></td> </tr>
        </table>
        <?= form_close() ?>
    </div>
    <div id="konfirmasi_kel" style="display: none; padding: 20px;">
        <div id="text_konfirmasi_kel"></div>
    </div>
    <div id="kel_list"></div>

</div>

