<script type="text/javascript">
    
    $(function(){
            
        get_kecamatan_list(1);
           
        $( "#addkec" ).button({icons: {primary: "ui-icon-circle-plus"}});
        $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
        $('button[type=submit]').button({icons: {primary: 'ui-icon-circle-check'}});
        $('#resetkec,.resetan').button({icons: {secondary: 'ui-icon-refresh'}});
        
        $('#addkec').click(function() {
            $('input[name=tipe]').val('add');
            $('#form-kec').dialog("option",  "title", "Tambah Data Kecamatan");
            $('#form-kec').dialog('open');
            $('.kabupaten-kec').focus();
        });
        
        $('#form-kec').dialog({
            autoOpen: false,
            height: 200,
            width: 400,
            modal: true,
            resizable : false,
            close : function(){
                reset_all();
            }
        });
        
        $('#konfirmasi_kec').dialog({
            autoOpen: false,
            title :'Konfirmasi',
            height: 200,
            width: 300,
            modal: true,
            resizable : false,
            buttons: [ 
                { text: "Ok", click: function() { 
                        save_kec();
                        $( this ).dialog( "close" ); 
                    } 
                }, 
                { text: "Batal", click: function() { 
                        $( this ).dialog( "close" ); 
                    } 
                } 
            ]
        });
            
        $('#showKecAll').click(function(){
            get_kecamatan_list(1);
        });
        
        $('#resetkec').click(function() {
            reset_all();
        });
        $('#kabupaten_kec').focus();
        $('.kabupaten-kec').autocomplete("<?= base_url('referensi/get_kabupaten') ?>",
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
                $('input[name=idkabupatenkec]').val("");
                return parsed;
            },
            formatItem: function(data,i,max)
            {
                var str = '<div class=result>'+data.nama+' - '+data.provinsi+'</div>';
                return str;
            },
            width: 270, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated)
        {
            $(this).val(data.nama);
            $('input[name=idkabupatenkec]').val(data.id);
        }
    );
        
        
        $('#formkec').submit(function(){
            var kecamatan = $('#kecamatan').val();
            var kab = $('.kabupaten-kec').val();
            var kabid=  $('input[name=idkabupatenkec]').val();
            
            if($('#kecamatan').val()==''){
                $('.msg').fadeIn('fast').html('Nama kecamatan tidak boleh kosong !');
                $('#kabupaten').focus();
                return false;
            }else if($('input[name=idkabupatenkec]').val() == ''){
                $('.msg').fadeIn('fast').html('Data Kabupaten tidak boleh kosong !');
                $('.kabupaten-kec').focus();
                return false;
            }else{  
                $.ajax({
                    url: '<?= base_url('referensi/manage_kecamatan') ?>/cek',
                    data:'kecamatan='+kecamatan+'&kabid='+kabid,
                    cache: false,
                    dataType: 'json',
                    success: function(msg){
                        if (!msg.status){
                            $('#text_konfirmasi_kec').html('Nama Kecamatan <b>"'+kecamatan+'"</b> dengan Kabupaten <b>"'+kab+'"</b> sudah ada<br/> Apakah anda yakin akan menambahkannya lagi?');            
                        } else {
                            $('#text_konfirmasi_kec').html('Nama Kecamatan <b>"'+kecamatan+'"</b> dengan Kabupaten <b>"'+kab+'"</b> <br/> Apakah anda akan menyimpan data?');                    
                        }
                        
                        $('#konfirmasi_kec').dialog("open");
                    }
                });
                
            }
                
            return false;
        });
        
        
    
    });
    
    function save_kec(){
        var Url = '';    
        var status = $('input[name=tipe]').val();
        if($('input[name=tipe]').val() === 'add'){
            Url = '<?= base_url('referensi/manage_kecamatan') ?>/add/';
        }else{
            Url = '<?= base_url('referensi/manage_kecamatan') ?>/edit/';
        }
        
        $.ajax({
            type : 'POST',
            url: Url+$('.noblock').html(),               
            data: $('#formkec').serialize(),
            cache: false,
            success: function(data) {
                $('#kec_list').html(data);
                $('#form-kec').dialog('close');
                if(status === 'add'){
                    alert_tambah();
                }else{
                    alert_edit();
                }
                reset_all();
                    
            }
        });
    }
    
    function reset_all(){
        $('input[name=id]').val('');
        $('input[name=tipe]').val('');
        $('input[name=idkabupatenkec]').val('');
        $('.kabupaten-kec').val('');
        $('#kecamatan').val('');
        $('#kodekec').val('');
        $('.msg').fadeOut('fast');
    }
    
    function get_kecamatan_list(p){
        $.ajax({
            type : 'GET',
            url: '<?= base_url('referensi/manage_kecamatan') ?>/list/'+p,
            cache: false,
            success: function(data) {
                $('#kec_list').html(data);
                reset_all();
            }
        });
    }
    
    function delete_kecamatan(id){
        var del = confirm("Anda yakin akan menghapus data ini ?");
        if(del){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_kecamatan') ?>/delete/'+$('.noblock').html(),
                data :'id='+id,
                cache: false,
                success: function(data) {
                    $('#kec_list').html(data);
                    alert_delete();
                }
            });
        }
    }
    
    function edit_kecamatan(id, nama,kab_id,kab_nama,kode){
        $('input[name=tipe]').val('edit');
        $('input[name=id]').val(id);
        $('#kecamatan').val(nama);
        $('.kabupaten-kec').val(kab_nama);
        $('input[name=idkabupatenkec]').val(kab_id);
        $('#kodekec').val(kode);
        $('#form-kec').dialog("option",  "title", "Edit Data Kecamatan");
        $('#form-kec').dialog("open");
        $('#savekec').focus();
    }
    
    
  
</script>
<?= form_button('', 'Tambah Data', 'id=addkec') ?>
<?= form_button('', 'Reset', 'class=resetan id=showKecAll') ?>
<div class="data-list">
    <div id="form-kec" style="display: none">
        <div class="msg"></div>
        <?= form_open('', 'id=formkec') ?>
        <table>
            <?= form_hidden('tipe') ?>
            <?= form_hidden('id') ?>
            <tr><td>Nama Kabupaten</td><td><?= form_input('', null, 'class=kabupaten-kec size=30') ?> <?= form_hidden('idkabupatenkec') ?></td></tr>
            <tr><td>Nama Kecamatan</td><td><?= form_input('kecamatan', null, 'id=kecamatan size=30') ?></td></tr>
            <tr><td>Kode Kecamatan</td><td><?= form_input('kodekec', null, 'id=kodekec size=10') ?></td></tr>
            <tr><td></td><td><?= form_submit('addkec', 'Simpan', 'id=savekec') ?> <?= form_button('', 'Reset', 'id=resetkec') ?></td> </tr>
        </table>
        </form>
    </div>

    <div id="konfirmasi_kec" style="display: none; padding: 20px;">
        <div id="text_konfirmasi_kec"></div>
    </div>

    <div id="kec_list"></div>

</div>

