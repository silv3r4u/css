<?php $this->load->view('message') ?>
<title><?= $title ?></title>
    <script type="text/javascript">
        function create_form() {
            var str = '<div id="form_penduduk" style="display: none;position: relative; background: #fff; padding: 10px;">'+
                    '<div class="msg" id="msg_penduduk"></div>'+
                    '<form action="" id="formpenduduk">'+
                    '<input type=hidden name=tipe />'+
                    '<input type=hidden name=id_penduduk />'+
                    '<table width="100%" class="tabel-input">'+
                        '<tr>'+
                            '<td width="25%">Nama:</td>'+
                            '<td><input type=text name=nama id=nama size=50 /></td>'+
                        '</tr>'+
                        '<tr valign=top>'+
                            '<td width="25%">Alamat:</td>'+
                            '<td><textarea name=alamat cols=30 rows=2></textarea>'+
                                '<input type=hidden name=alamat_nama size=50 /></td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td width="25%">Kab. / Kodya:</td>'+
                            '<td>'+
                                '<code><?php echo form_input("", "", "class=kelurahan id=alamat_kab size=50") ?><br/>'+
                                '<input type=hidden name=id_kabupaten id=id_alamat_kab />'+
                            '</td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td width="25%">Telepon:</td>'+
                            '<td>'+
                                '<input type=text name=telp id=telp size=50 />'+
                            '</td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td width="25%">Tempat Lahir:</td>'+
                            '<td>'+
                                '<input type=text name=kabupaten class=kabupaten id=tempat_lahir size=50 />'+
                                '<input type=hidden name=id_kabupaten id=id_tempat_lahir />'+
                            '</td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td width="25%">Gender:</td>'+
                            '<td>'+
                                '<input type=radio name=kelamin value=L class=l />Laki -laki'+
                                '<input type=radio name=kelamin value=P class=p />Perempuan'+
                            '</td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td width="25%">Golongan Darah:</td>'+
                            '<td><select name=gol_darah id=gol_darah><?php foreach ($gol_darah as $rowg) { echo '<option value="'.$rowg.'">'.$rowg.'</option>'; } ?></select></td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td width="25%">Tanggal Lahir:</td>'+
                            '<td><code><?php echo form_input("tgl_lahir", "", "id=awal class=tgl size=10 placeholder=dd/mm/yyyy"); ?> </td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td width="25%">No. Identitas:</td>'+
                            '<td><code><?php echo form_input("noid", "", "id=noid size=50"); ?> </td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td width="25%">Pernikahan:</td>'+
                            '<td><select name=pernikahan id=pernikahan><?php foreach ($gol_darah as $rowg) { echo '<option value="'.$rowg.'">'.$rowg.'</option>'; } ?></select></td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td width="25%">Pendidikan:</td>'+
                            '<td><select name=pendidikan id=pendidikan><?php foreach ($pendidikan as $rowpdd) { echo '<option value="'.$rowpdd->id.'">'.$rowpdd->nama.'</option>'; } ?></select></td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td width="25%">Profesi:</td>'+
                            '<td><select name=profesi id=profesi><?php foreach ($profesi as $rowpdd) { echo '<option value="'.$rowpdd->id.'">'.$rowpdd->nama.'</option>'; } ?></select></td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td width="25%">No. STR: </td>'+
                            '<td><code><?php echo form_input("nostr", "", "id=nostr size=50"); ?> </td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td width="25%">No. SIP:</td>'+
                            '<td><code><?php echo form_input("nosip", "", "id=nosip size=50"); ?> </td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td width="25%">No. Surat Ijin Kerja:</td>'+
                            '<td><code><?php echo form_input("nosik", "", "id=nosik size=50"); ?> </td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td width="25%">Jabatan:</td>'+
                            '<td><select name=jabatan id=jabatan><?php foreach ($jabatan as $rowg) { echo '<option value="'.$rowg.'">'.$rowg.'</option>'; } ?></select></td>'+
                        '</tr>'+
                    '</table></form>'+
                    '</div>';
            
            $('#loaddata').append(str);
            $('.tgl').datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat : 'dd/mm/yy',
                maxDate: 0
            });
            $('#alamat_kab').autocomplete("<?= base_url('referensi/get_kabupaten') ?>",
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
                    var str = '<div class=result>'+data.nama+'<br/>Provinsi: '+data.provinsi+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('#id_alamat_kab').val(data.id);
            });
            $('#tempat_lahir').autocomplete("<?= base_url('referensi/get_kabupaten') ?>",
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
                    var str = '<div class=result>'+data.nama+'<br/>Provinsi: '+data.provinsi+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('#id_tempat_lahir').val(data.id);
            });
            $('#form_penduduk').dialog({
                autoOpen: true,
                height: 550,
                width: 800,
                modal: true,
                title: 'Form Data Penduduk',
                resizable : false,
                close : function(){
                    $(this).dialog().remove();
                }, buttons: {
                    "Simpan": function() {
                        $('#formpenduduk').submit();
                    },
                    "Reset": function() {
                        reset_all();
                    }
                }
            });
        }
        function remove_modal() {
            $("#form_penduduk").remove();
        }
        var request;
        $(function(){
            $( "#addpenduduk" ).button({icons: {primary: "ui-icon-circle-plus"}});
            $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('button[type=submit]').button({icons: {primary: 'ui-icon-circle-check'}});
            $('.resetan').button({icons: {secondary: 'ui-icon-refresh'}});
            $('.cari').button({icons: {secondary: 'ui-icon-search'}});
            get_penduduk_list(1,'null');
            
            
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
            
            $('#showAll').click(function(){
                get_penduduk_list(1, 'null');
            });
        
            $('#form_cari_pdd').dialog({
                autoOpen: false,
                height: 450,
                width: 500,
                modal: true,
                resizable : false,
                close : function(){
                    reset_all();
                },
                open : function(){
                
                }
            });
            $( "#tab" ).tabs({selected: 0 });
            $('#addpenduduk').click(function() {
                //get_last_id();
//                $('input[name=tipe]').val('add');
//                $('#form_penduduk').dialog("option",  "title", "Tambah Data Penduduk");
//                $('#form_penduduk').dialog("open");
                //$( "#tab" ).tabs({selected: 0 });
                create_form();
            });
        
            $('.resetan').click(function() {
                reset_all();
            });
            
            
            
            $('#showAll').click(function() {
                get_penduduk_list(1,'null');
            });
        
            $('#bt_cari').click(function() {
                $('#form_cari_pdd').dialog("option",  "title", "Pencarian Penduduk");
                $('#form_cari_pdd').dialog("open");
                reset_all();
            });
        
            $('#formcaripenduduk').submit(function(){
                var Url = '<?= base_url('referensi/manage_penduduk') ?>/search/';            
                if(!request) {
                    request =  $.ajax({
                        type : 'POST',
                        url: Url+$('.noblock').html(),               
                        data: $(this).serialize(),
                        cache: false,
                        success: function(data) {
                            $('#penduduk_list').html(data);                           
                            $('#form_cari_pdd').dialog('close');
                            reset_all(); 
                            request = null;                            
                        }
                    });
                }
                return false;
            });
        
            $('#formdinamis').submit(function(){     
                Url = '<?= base_url('referensi/manage_penduduk') ?>/edit_dinamis/';              
        
                if(!request) {
                    request =  $.ajax({
                        type : 'POST',
                        url: Url,               
                        data: $(this).serialize(),
                        cache: false,
                        success: function(res) {
                            var data = $.parseJSON(res);
                            get_penduduk_list($('.noblock').html(), 'null')
                            get_dinamis_penduduk_list(data.id);
                            alert_edit();
                            $('#form_penduduk').dialog("close");
                            reset_all();
                            request = null;                            
                        }
                    });
                }  
           
            
                return false;
            });
        
            $('.kelurahan').autocomplete("<?= base_url('referensi/get_kelurahan') ?>",
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
                    var str = '<div class=result>'+data.nama+'<br/>Kec: '+data.kecamatan+', Kab: '+data.kabupaten+', Prov: '+data.provinsi+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('input[name=id_kelurahan]').val(data.id);
                // $('.id_kabupaten').val(data.id_kabupaten);
            });
            
            $('#formpenduduk').submit(function(){
                var nama = $('#nama').val();
                if(nama===''){
                    $('#msg_penduduk').fadeIn('fast').html('Nama penduduk tidak boleh kosong !');
                    $('#nama').focus();
                    return false;
                }else{    
                    save();
                }
                return false;
            });
        });
        
        function save(){
            var Url = '';       
            var tipe = $('input[name=tipe]').val();
            if( tipe === 'add'){
                Url = '<?= base_url('referensi/manage_penduduk') ?>/add/';
            }else{
                Url = '<?= base_url('referensi/manage_penduduk') ?>/edit/';
            }   
            
            if(!request) {
                request =  $.ajax({
                    type : 'POST',
                    url: Url+$('.noblock').html(),               
                    data: $("#formpenduduk").serialize(),
                    cache: false,
                    success: function(data) {
                        $('#penduduk_list').html(data);
                        $('#form_penduduk').dialog("close");
                        if(tipe === 'add'){
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
            $('#nomor_penduduk').html('');
            $('#nama').val('');
            $('#nama_cari').val('');
            $('#alamat').val('');
            $('#alamat_cari').val('');
            $('#telp').val('');
            $('#telp_cari').val('');
            $('.kabupaten').val('');
            $('.l').removeAttr('checked');
            $('.p').removeAttr('checked');
            $('#gol_darah').val('');
            $('#gol_darah_cari').val('');
            $('.tgl').val('');
        
        
            $('input[name=id_kabupaten]').val('');
            $('input[name=id_kabupaten_cari]').val('');
      
            $('#msg_penduduk').fadeOut('fast');
            $('#msg_cari_pdd').fadeOut('fast');
        }
    
        function get_last_id(){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/get_last_id') ?>/penduduk/id',
                cache: false,
                dataType : 'json',
                success: function(data) {
                    $('#nomor_penduduk').html(data.last_id);
                    $('input[name=id_penduduk]').val(data.last_id);
                }
            });
        }
    
        function get_penduduk_list(p,search){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_penduduk') ?>/list/'+p,
                data :'search='+search ,
                cache: false,
                success: function(data) {
                    $('#penduduk_list').html(data);
                    reset_all();
                }
            });
        }    
     
        function delete_penduduk(id){
            var del = confirm("Anda yakin akan menghapus data ini ?");
            if(del){
                $.ajax({
                    type : 'GET',
                    url: '<?= base_url('referensi/manage_penduduk') ?>/delete/'+$('.noblock').html(),
                    data :'id='+id,
                    cache: false,
                    success: function(data) {
                        $('#penduduk_list').html(data);
                        alert_delete();
                    }
                });
            }
        }
    
        function edit_penduduk(arr){
            var data = arr.split("#");
            $(".dinamis").show();
            $('input[name=tipe]').val('edit');
        
            $('#nomor_penduduk').html(data[0]);
            $('#id_pdd_dinamis').html(data[0]);
            $('input[name=id_penduduk]').val(data[0]);       
            $('input[name=id_pdd_dinamis]').val(data[0]);
            $('#nama').val(data[1]);
            $('#nama_pdd').html(data[1]);
            $('#alamat').val(data[2]);  
            $('input[name=alamat_lama]').val(data[2]);
            $('#telp').val(data[3]);
            $('input[name=id_kabupaten]').val(data[4]);
            $('#kabupaten').val(data[5]);
            if(data[6] === 'L'){
                $('.l').attr('checked','checked');
            }else{
                $('.p').attr('checked','checked');
            }
      
            $('#gol_darah').val(data[7]);
            $('#awal').val(datefmysql(data[8]));
            $( "#tab" ).tabs({selected: 0 });
    
        
            $('#form_penduduk').dialog("option",  "title", "Edit Data Penduduk");
            $('#form_penduduk').dialog("open");
        }
    
        function paging(page, tab,search){
            get_penduduk_list(page,search);
        }
    </script>
    <div class="kegiatan">
        <h1><?= $title ?></h1>
        <?= form_button('', 'Tambah Data', 'id=addpenduduk') ?>
        <?= form_button('', 'Cari', 'id=bt_cari class=cari') ?>
        <?= form_button('', 'Reset', 'class=resetan id=showAll') ?>
        
        <!-- end of form -->
        <div id="konfirmasi" style="display: none;">
            <div id="text_konfirmasi"></div>
        </div>

        <div id="penduduk_list"></div>

        <div id="form_cari_pdd" style="display: none;position: relative; background: #fff; padding: 10px;">
            <div class='msg' id="msg_cari_pdd"></div>
            <?= form_open('', 'id=formcaripenduduk') ?>

            <table width="100%">
                <tr>
                    <td width="25%" style="text-align: right;">Nama:</td>
                    <td><?= form_input('nama_cari', '', 'id=nama_cari size=50') ?> </td>
                </tr>
                <tr>
                    <td width="25%" style="text-align: right;">Alamat:</td>
                    <td> <?= form_input('alamat_cari', '', 'id=alamat_cari size=50') ?></td>
                </tr>
                <tr>
                    <td width="25%" style="text-align: right;">Telepon:</td>
                    <td>
                        <?= form_input('telp_cari', '', 'id=telp_cari size=50') ?>
                    </td>
                </tr>
                <tr>
                    <td width="25%" style="text-align: right;">Tempat Lahir:</td>
                    <td>
                        <?= form_input('kabupaten_cari', '', 'class=kabupaten size=50') ?>
                        <?= form_hidden('id_kabupaten_cari') ?>
                    </td>
                </tr>
                <tr>
                    <td width="25%" style="text-align: right;">Gender:</td>
                    <td>
                        <?= form_radio('kelamin_cari', 'L', false, 'class=l') ?>Laki -laki
                        <?= form_radio('kelamin_cari', 'P', false, 'class=p') ?>Perempuan
                    </td>
                </tr>
                <tr>
                    <td width="25%" style="text-align: right;">Golongan Darah:</td>
                    <td><?= form_dropdown('gol_darah_cari', $gol_darah, null, 'id=gol_darah_cari') ?></td>
                </tr>
                <tr>
                    <td width="25%" style="text-align: right;">Tanggal Lahir:</td>
                    <td><?= form_input('tgl_lahir_cari', '', 'id=awal_cari class=tgl size=10 placeholder=dd/mm/yyyy') ?> </td>
                </tr>

                <tr>
                    <td></td>
                    <td>
                        <?= form_submit('cari', 'Cari', 'id=cari class=cari') ?>
                        <?= form_button('', 'Reset', 'id=batal_cari class=resetan') ?>
                    </td>
                </tr>
            </table>
            <?= form_close() ?>
        </div>
</div>