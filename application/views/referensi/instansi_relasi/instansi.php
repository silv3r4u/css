<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<script type="text/javascript">
        function create_dialog() {
            var str = '<div id=form>'+
                    '<div id=result></div>'+
                    '<div class=msg></div>'+
                    '<form action="" id="formadd">'+
                    '<input type=hidden name=tipe>'+
                    '<input type=hidden name=id>'+
                    '<table width=100% class=tabel-input>'+
                        '<tr>'+
                            '<td width=15%>Nama:</td>'+
                            '<td><input type=text name=nama id=nama size=50 /></td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td width=15% valign=top>Alamat:</td>'+
                            '<td><textarea name=alamat cols=30 rows=2 id=alamat></textarea></td>'+
                        '</tr>'+
                        '<tr valign=top>'+
                            '<td width=15%>Kabupaten:</td>'+
                            '<td><input type=text size=50 class=kelurahan /><input type=hidden name=id_kelurahan id=id_kelurahan />'+
                                '<div id=ket></div>'+
                            '</td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td width=15%>Telepon:</td>'+
                            '<td><input type=text name=telp id=telp size=50 /></td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td width=15%>Fax:</td>'+
                            '<td><input type=text name=fax id=fax size=50 /></td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td width=15%>Email:</td>'+
                            '<td><input type=text name=email id=email size=50 /></td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td width=15%>Website:</td>'+
                            '<td><input type=text name=website id=website size=50 /></td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td width=15%>Jenis:</td>'+
                            '<td><select name=jenis>'+
                            '<?php foreach ($jenis as $rows) { echo '<option value="'.$rows->id.'">'.$rows->nama.'</option>'; } ?>'+
                            '</select></td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td width=15%>Diskon Penjualan (%):</td>'+
                            '<td><input type=text name=diskon_penjualan id=diskon_penjualan size=5 /></td>'+
                        '</tr>'+
                        '<tr>'+
                            '<td></td>'+
                            '<td>'+
                            '</td>'+
                        '</tr>'+
                    '</table>'+
            '</div>';
            $('#loaddata').append(str);
            $('.kelurahan').autocomplete("<?= base_url('referensi/get_kabupaten') ?>",
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
                    var str = '<div class=result>'+data.nama+'<br/>Kec: '+data.kecamatan+', Kab: '+data.kabupaten+', Pro: '+data.provinsi+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).attr('value',data.nama);
                $('input[name=id_kelurahan]').val(data.id);
                $('#ket').html(data.provinsi);
                
            });
            $('#formadd').submit(function(){
                          
                var nama = $('#nama').val();
                
                if($('#nama').val()===''){
                    $('.msg').fadeIn('fast').html('Nama instansi tidak boleh kosong !');
                    $('#nama').focus();
                } else if($('#jenis').val()===''){
                    $('.msg').fadeIn('fast').html('Pilih jenis instansi !');
                    $('#jenis').focus();
                } else{    
                    save();
                    return false;
                }
                return false;
            });
            $('#form').dialog({
                autoOpen: false,
                height: 450,
                width: 550,
                modal: true,
                close : function(){
                    $(this).dialog().remove();
                }, buttons: {
                    "Simpan": function() {
                        $('#formadd').submit();
                    },
                    "Reset": function() {
                        reset_all();
                    }
                }
            });
        }
        function remove_modal() {
            $("#form").remove();
        }
        var request;
        $(function() {
            $("#addnewrow").click(function() {
                create_dialog();
            });
            $("#addnewrow").button({icons: {primary: "ui-icon-circle-plus"}});
            $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('button[type=submit]').button({icons: {primary: 'ui-icon-circle-check'}});
            $('#reset, .resetan').button({icons: {secondary: 'ui-icon-refresh'}});
            $('.cari').button({icons: {secondary: 'ui-icon-search'}});
            $('#formcarirelasi').dialog({
                autoOpen: false,
                title: 'Pencarian',
                height: 150,
                width: 500,
                modal: true,
                resizable : false,
                close : function(){
                    reset_all();
                },
                open : function(){
                
                }
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
                            $(this).dialog().remove(); 
                        }
                    },
                    { text: "Batal", click: function() { 
                            $(this).dialog().remove();
                        } 
                    } 
                ]
            });
            
            $('#carirelasi').click(function(){
                $('#formcarirelasi').dialog('open');
                $('#nama_cari').focus();
            });
            $('#resetcari').click(function(){
                $('#nama_cari').val('');
            });
            //initial
            get_instansi_list(1,'null');
            //initial
            $('#showAll').click(function(){
                $('#loaddata').empty();
                $('#loaddata').load('<?= base_url('referensi/instansi_relasi') ?>');
                //get_instansi_list(1,'null');
            });
            $('#addnewrow').click(function() {
                $('input[name=tipe]').val('add');
                $('#form').dialog("option",  "title", "Tambah Data Instansi");
                $('#form').dialog("open");
            
            });
            $('#reset').click(function() {
                reset_all();
            });
            $('#form_carirelasi').submit(function(){
                var Url = '<?= base_url('referensi/manage_instansi') ?>/search/';         
            
                if($('#nama_cari').val()===''){
                    $('#msg_carirelasi').fadeIn('fast').html('Nama instansi tidak boleh kosong !');
                    $('#nama_cari').focus();
                    return false;
                }else{    
                    if(!request) {
                        request =  $.ajax({
                            type : 'POST',
                            url: Url+$('.noblock').html(),               
                            data: $(this).serialize(),
                            cache: false,
                            success: function(data) {
                                $('#ins_list').html(data);                           
                                $('#formcarirelasi').dialog('close');
                                reset_all(); 
                                request = null;                            
                            }
                        });
                    }
                    return false;
                }
                return false;
            });
        
        
        });
        
        function save(){
            var Url = ''; 
            var status = $('input[name=tipe]').val();
            if(status === 'add'){
                Url = '<?= base_url('referensi/manage_instansi') ?>/add/';
            }else{
                Url = '<?= base_url('referensi/manage_instansi') ?>/edit/';
            }
            if(!request) {
                request = $.ajax({
                    type : 'POST',
                    url: Url+$('.noblock').html(),               
                    data: $('#formadd').serialize(),
                    cache: false,
                    success: function(data) {
                        $('#ins_list').html(data);
                        $('#form').remove();
                        if(status === 'add'){
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
            $('input[name=id]').val('');
            $('input').val('');
            $('#nama').val('');
            $('#alamat').val('');
            $('.kelurahan').val('');
            $('input[name=id_kelurahan]').val('');
            $('#telp').val('');
            $('#fax').val('');
            $('#email').val('');
            $('#website').val('');
            $('#jenis').val('');
            $('#diskon_penjualan').val('');
            $('.msg').fadeOut('fast');
            $('#ket').html('');
        }
    
        function get_instansi_list(p,search){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_instansi') ?>/list/'+p,
                data : 'search='+search,
                cache: false,
                success: function(data) {
                    $('#ins_list').html(data);
                    reset_all();
                }
            });
        }
    
        function delete_instansi(id){
            var del = confirm("Anda yakin akan menghapus data ini ?");
            if(del){
                $.ajax({
                    type : 'GET',
                    url: '<?= base_url('referensi/manage_instansi') ?>/delete/'+$('.noblock').html(),
                    data :'id='+id,
                    cache: false,
                    success: function(data) {
                        $('#ins_list').html(data);
                        alert_delete();
                    }
                });
            }
        }
    
        function edit_instansi(arr){
            create_dialog();
            var data = arr.split("#");
            $('input[name=tipe]').val('edit');
            $('input[name=id]').val(data[0]);
            $('#nama').val(data[1]);
            $('#alamat').val(data[2]);
            $('.kelurahan').val(data[4]);
            $('input[name=id_kelurahan]').val(data[3]);
            $('#telp').val(data[5]);
            $('#fax').val(data[6]);
            $('#email').val(data[7]);
            $('#website').val(data[8]);
            $('#jenis').val(data[9]);
            $('#diskon_penjualan').val(data[10]);

            $('#form').dialog("option",  "title", "Edit Data Instansi");
            $('#form').dialog("open");
        }
        function paging(page, tab, cari){
            get_instansi_list(page,cari);
        }
        
       
    </script>
<div class="kegiatan">
    <h1><?= $title ?></h1>

    <?= form_button('', 'Tambah Data', 'id=addnewrow') ?>
    <?= form_button('', 'Cari', 'id=carirelasi class=cari') ?>
    <?= form_button('', 'Reset', 'class=resetan id=showAll') ?>  
    
    <div id="konfirmasi" style="display: none; padding: 20px;">
        <div id="text_konfirmasi"></div>
    </div>

    <div id="formcarirelasi" style="display: none; top: 20%; background: #fff; padding: 10px; ">
        <?= form_open('', 'id=form_carirelasi') ?>

        <div class="msg" id="msg_carirelasi"></div>
        <table width="100%" class="tabel-input">
            <tr>
                <td width="25%">Nama:</td>
                <td><?= form_input('nama', '', 'id=nama_cari class=nama size=50') ?> </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <?= form_submit('cari', 'Cari', 'class=cari id=cari_relasi') ?>
                    <?= form_button('', 'Reset', 'id=resetcari class=resetan') ?>
                </td>
            </tr>
        </table>
        <?= form_close() ?>
    </div>
    <div id="list" class="data-list">
        <div id="ins_list"></div>

    </div>
</div>