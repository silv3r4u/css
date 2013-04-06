<?php $this->load->view('message') ?>
<script src="<?= base_url() ?>assets/js/jquery-barcode-2.0.2.min.js"></script>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        var request;
        $(function(){
            $( "#addpacking" ).button({icons: {primary: "ui-icon-circle-plus"}});
            $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('button[type=submit]').button({icons: {primary: 'ui-icon-circle-check'}});
            $('#reset, #cetak_batal,.resetan').button({icons: {secondary: 'ui-icon-refresh'}}); 
            $('#bt_caripack').button({icons: {secondary: 'ui-icon-search'}});
            $('#cetak-jumlah').button({icons: {secondary: 'ui-icon-print'}});
            get_packing_list(1);
            $('#packAll').click(function(){
                get_packing_list(1);
            });
            
            $('#form_cari_packing').dialog({
                autoOpen: false,
                title:'Pencarian Data Kemasan Barang',
                height: 170,
                width: 350,
                modal: true,
                resizable : false,
                close : function(){
                    reset_all();
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
                            $( this ).dialog( "close" ); 
                        } 
                    }, 
                    { text: "Batal", click: function() { 
                            $( this ).dialog( "close" ); 
                        } 
                    } 
                ]
            });
            
            $('#bt_caripack').click(function(){
                $('#form_cari_packing').dialog('open');
            });
            
            $('#barang_cari').blur(function(){
                if($('#barang_cari').val()== ''){
                    $('input[name=barang_cari]').val(''); 
                }               
            });
       
            $('#form_cari').submit(function(){
                var Url = '<?= base_url('referensi/manage_packing') ?>/search/';
                
                if($('input[name=barang_cari]').val() ==''){
                    $('#msg_cari_packing').fadeIn('fast').html('Nama barang tidak boleh kosong<br/>atau pilih barang yang tersedia !');
                    $('#barang_cari').focus();
                }else{    
                    if(!request) {
                        request =$.ajax({
                            type : 'POST',
                            url: Url,               
                            data: $(this).serialize(),
                            cache: false,
                            success: function(data) {
                                $('#packing_list').html(data);
                                $('#form_cari_packing').dialog('close');
                                reset_all();    
                                request = null;
                            }
                        });
                    }
              
                    return false;
                }
                return false;
                
            });
       
            $('#form_packing').dialog({
                autoOpen: false,
                height: 300,
                width: 800,
                modal: true,
                resizable : false,
                close : function(){
                    reset_all();
                },
                open : function(){
                
                }
            });
            $('#addpacking').click(function() {
                $('input[name=tipe]').val('add');
                $('#form_packing').dialog("option",  "title", "Tambah Data Packing Barang");
                $('#form_packing').dialog("open");
                $('#barcode').focus();
            
            });
        
            $('#reset').click(function() {
                reset_all();
            });
            
            $('#reset_cari').click(function() {
                reset_all();
            });
        
            $('.barang').autocomplete("<?= base_url('inv_autocomplete/load_data_barang') ?>",
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
                    if (data.id_obat !== null) {
                        if (data.kekuatan !== null && data.satuan !== null && data.sediaan !== null) {
                            var str = '<div class=result>'+data.nama+' '+((data.kekuatan === '1')?'':data.kekuatan)+' '+data.satuan+' '+data.sediaan+'  <i> '+data.pabrik+'</i></div>';
                        } 
                        else if (data.kekuatan !== null && data.satuan !== null && data.sediaan === null) {
                            var str = '<div class=result>'+data.nama+' '+((data.kekuatan === '1')?'':data.kekuatan)+' '+data.satuan+' '+data.sediaan+' <i> '+data.pabrik+'</i></div>';
                        } else {
                            var str = '<div class=result>'+data.nama+'</div>';
                        }	
                    } else {
                        if (data.pabrik !== null) {
                            var str = '<div class=result>'+data.nama+'<i> '+data.pabrik+'</i></div>';
                        } else {
                            var str = '<div class=result>'+data.nama+'</div>';
                        }
                    }
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                if (data.id_obat != null) {
                    if (data.kekuatan != null && data.satuan != null && data.sediaan != null) {
                        var str = data.nama+' '+data.kekuatan+' '+data.satuan+' '+data.sediaan+' '+data.pabrik;
                    } 
                    else if (data.kekuatan != null && data.satuan != null && data.sediaan == null) {
                        var str = data.nama+' '+data.kekuatan+' '+data.satuan+' '+data.pabrik+'';
                    } else {
                        var str = data.nama;
                    }	
                } else {
                    var str = data.nama+' '+data.pabrik;
                }
                $(this).val(str);
                $('input[name=id_barang]').val(data.id_barang);
                $('input[name=barang_cari]').val(data.nama);
            });
        
            $('#barcode').live('keydown', function(e) {
                if (e.keyCode==13) {
                    $('input[name=barcode]').val($('#barcode').val());
                }
            });
            $('#barcode').keyup(function() {
                $('input[name=barcode]').val($('#barcode').val());
            });
                    
        
            $('#cetak-jumlah').click(function() {
                var barcode = $('#real-text').html();
                var jumlah  = $('#jml').val();
                window.open('<?= base_url('referensi/cetak_barcode') ?>?barcode='+barcode+'&jumlah='+jumlah, 'MyWindow', 'width=500px,height=400px,scrollbars=1');
            });
        
        
            $('#cetak_batal').click(function() {
                $('#cetak-barcode').fadeOut('fast');
            });
            
            $('#formpacking').submit(function(){
                var barcode = $('#barcode').val();
                var id_barang = $('input[name=id_barang]').val();
                var nama = $('#barang').val();
                var kemasan = $('#kemasan').val();
                var isi = $('#isi').val();
                var satuan = $('#satuan').val();
                
                if($('input[name=id_barang]').val()==''){
                    $('#msg_packing').fadeIn('fast').html('Nama barang tidak boleh kosong !');
                    $('#barang').focus();
                } else if($('#kemasan').val()==''){
                    $('#msg_packing').fadeIn('fast').html('Jenis kemasan harus dipilih !');
                    $('#kemasan').focus();
                }else if($('#isi').val()==''){
                    $('#msg_packing').fadeIn('fast').html('Isi tidak boleh kosong !');
                    $('#isi').focus();
                }else if($('#satuan').val() ==''){
                    $('#msg_packing').fadeIn('fast').html('Jenis satuan harus dipilih !');
                    $('#satuan').focus();
                }else{    
                    $.ajax({
                        url: '<?= base_url('referensi/manage_packing') ?>/cek',
                        data:'barcode='+barcode+'&id_barang='+id_barang+'&kemasan='+kemasan+'&isi='+isi+'&satuan='+satuan,
                        cache: false,
                        dataType: 'json',
                        success: function(msg){
                            if (msg.status == false){
                                $('#text_konfirmasi').html('Nama Packing Barang <b>"'+nama+'"</b> sudah ada<br/> Apakah anda yakin akan menambahkannya lagi?');            
                            } else {
                                $('#text_konfirmasi').html('Nama Packing Barang<b>"'+nama+'"</b><br/> Apakah anda akan menyimpan data?');                    
                            }
                        
                            $('#konfirmasi').dialog("open");
                        }
                    });
              
                    
                }
                return false;
            });
        
        
        });
    
        function save(){
            var Url = '';           
            if($('input[name=tipe]').val() == 'add'){
                Url = '<?= base_url('referensi/manage_packing') ?>/add/';
            }else{
                Url = '<?= base_url('referensi/manage_packing') ?>/edit/';
            }
            
            if(!request) {
                request =$.ajax({
                    type : 'POST',
                    url: Url+$('.noblock').html(),               
                    data: $("#formpacking").serialize(),
                    cache: false,
                    success: function(data) {
                        $('#packing_list').html(data);
                        $('#form_packing').dialog("close");
                        if($('input[name=tipe]').val() == 'add'){
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
            $('input').val('');
            $('#kemasan').val('');
            $('#satuan').val('');
      
            $('.msg').fadeOut('fast');
        
        }
        function get_packing_list(p){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_packing') ?>/list/'+p,
                cache: false,
                success: function(data) {
                    $('#packing_list').html(data);
                    reset_all();
                }
            });
        }
    
        function delete_packing(id){
            var del = confirm("Anda yakin akan menghapus data ini ?");
            if(del){
                $.ajax({
                    type : 'GET',
                    url: '<?= base_url('referensi/manage_packing') ?>/delete/'+$('.noblock').html(),
                    data :'id='+id,
                    cache: false,
                    success: function(data) {
                        $('#packing_list').html(data);
                        alert_delete();
                    }
                });
            }
        }
    
        function edit_packing(arr){
            var data = arr.split("#");
            $('input[name=tipe]').val('edit');
        
            $('input[name=id]').val(data[0]);
            $('#barcode').val(data[1]);
            $('input[name=barcode]').val(data[1]);
            $('input[name=id_barang]').val(data[2]);
            $('#barang').val(data[3]);
            $('#kemasan').val(data[4]);
            $('#isi').val(data[5]);
            $('#satuan').val(data[6]);

            $('#form_packing').dialog("option",  "title", "Edit Data Packing Barang");
            $('#form_packing').dialog("open");
        
        }
    
        function paging(page, tab){
            get_packing_list(page);
        }
    
        function cetak_barcode(barcode){
            $('#cetak-barcode').fadeIn('fast');
            $('#real-text').html(barcode);
            $('#text-barcode').barcode(barcode, "code128",{barWidth:2, barHeight:40});
        }
    </script>
    <h1><?= $title ?></h1>

    <?= form_button('', 'Tambah Data', 'id=addpacking') ?>
    <?= form_button('', 'Cari', 'id=bt_caripack class=cari') ?>
    <?= form_button('', 'Reset', 'class=resetan id=packAll') ?>
    <div id="form_packing" style="display: none;position: relative; background: #fff; padding: 10px;">
        <div class="msg" id="msg_packing"></div>
        <table width="100%">
            <tr>
                <td width="15%">Barcode</td>
                <td> <?= form_input('barcode', '', 'id=barcode size=40') ?></td>
            </tr>
        </table>

        <?= form_open('', 'id=formpacking') ?>
        <?= form_hidden('tipe') ?>
        <?= form_hidden('id') ?>


        <table width="100%">
            <tr>
                <td width="15%">Barang</td>
                <td>
                    <?= form_hidden('barcode', '', 'size=40') ?>
                    <?= form_input('barang', '', 'class=barang id=barang size=40') ?>
                    <?= form_hidden('id_barang') ?> </td>
            </tr>
            <tr>
                <td width="15%">Kemasan</td>
                <td><?= form_dropdown('kemasan', $kemasan, null, 'id=kemasan') ?> </td>
            </tr>
            <tr>
                <td width="15%">Isi @</td>
                <td><?= form_input('isi', '', 'id=isi size=40') ?> </td>
            </tr>
            <tr>
                <td width="15%">Satuan</td>
                <td><?= form_dropdown('satuan', $satuan, null, 'id=satuan') ?> </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <?= form_submit('save', 'Simpan', 'id=simpan') ?>
                    <?= form_button('', 'Reset', 'id=reset') ?>
                </td>
            </tr>
        </table>
    </div>
    <?= form_close() ?>


    <div id="cetak-barcode" style="z-index: 2;display: none;position: absolute;background: #fff;" class="popup">
        <span style="font-size: 40px;font-family: 'barcode','free 3 of 9'; display: block" id="text-barcode"></span>
        <span style="letter-spacing:8px; font: 15px arial,tahoma; line-height: 18px" id="real-text"></span><br/>
        Jumlah cetak: <?= form_input('jml', null, 'id=jml size=5') ?> 
        <?= form_button('', 'Cetak', 'id=cetak-jumlah') ?>
        <?= form_button('', 'Batal', 'id=cetak_batal') ?>
    </div>
    <div id="form_cari_packing" style="display: none;background: #fff; padding: 10px">
        <div class="msg" id="msg_cari_packing"></div>
        <?= form_open('', 'id=form_cari') ?>
        <table width="100%">
            <tr>
                <td width="30%">Barang</td>
                <td><?= form_input('barang', '', 'class=barang id=barang_cari size=40') ?>
                    <?= form_hidden('barang_cari') ?></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <?= form_submit('', 'Cari', 'id=cari_packing') ?>
                    <?= form_button('', 'Reset', 'id=reset_cari class=resetan') ?>
                </td>
            </tr>
        </table>
        <?= form_close() ?>
    </div>
    <div id="konfirmasi" style="display: none; padding: 20px;padding-top: 30px">
        <div id="text_konfirmasi"></div>
    </div>
    <div id="list" class="data-list">
        <div id="packing_list"></div>
    </div>
</div>