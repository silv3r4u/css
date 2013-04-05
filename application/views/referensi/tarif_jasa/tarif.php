<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        var request;
        $(function() {         
            $( "#addtarif" ).button({icons: {primary: "ui-icon-circle-plus"}});
            $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('button[type=submit]').button({icons: {primary: 'ui-icon-circle-check'}});
            
            $('input[type=reset]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('button[type=reset], button[id=showAll]').button({icons: {primary: 'ui-icon-refresh'}});
            
            $('#search, .cari').button({icons: { primary: 'ui-icon-search' }})
            $('#batal').button({icons: { primary: 'ui-icon-refresh' }})
            get_tarif_list(1,'null');
            $('#search').click(function() {
                $('#searching').dialog('open');
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
            $('#batal').click(function() {
                reset_all();
            })
            $('#reset').click(function(){
                reset_all();
            });
            
            $('#searching').dialog({
                autoOpen: false,
                height: 150,
                width: 400,
                modal: true,
                title : 'Form Pencarian Tarif Jasa',
                resizable : false,
                close : function(){
                    reset_all();
                }
            })
            $('#showAll').click(function(){
                get_tarif_list(1,'null');
            });
        
            $('#form_tarif').dialog({
                autoOpen: false,
                height: 400,
                width: 600,
                modal: true,
                resizable : false,
                close : function(){
                    reset_all();
                },
                open : function(){
                
                }
            });
            $('#addtarif').click(function() {
                get_last_id();
                $('input').val('');
                $('input[name=tipe]').val('add');
                $('#form_tarif').dialog("option",  "title", "Tambah Data Tarif Jasa");
                $('#form_tarif').dialog("open");
            
            
            });
        
            $('#nama').autocomplete("<?= base_url('inv_autocomplete/get_layanan') ?>",
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
                    return parsed;
                },
                formatItem: function(data,i,max)
                {
                    var str = '<div class=result>'+data.nama+' - '+((data.bobot == null)?'':data.bobot)+' - '+((data.kelas == null)?'':data.kelas)+'</div>';
                    return str;
                },
                width: 322, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated) {
                $(this).val(data.nama+' '+((data.bobot == null)?'':' - '+data.bobot)+' '+((data.kelas == null)?'':' - '+data.kelas));
                $('input[name=id_layanan]').val(data.id);
                $('#kategori').focus();
                $.ajax({
                    url: '<?= base_url('referensi/get_jasa_profesi') ?>',
                    data: 'id_layanan='+data.id,
                    cache: false,
                    dataType: 'json',
                    success: function(msg) {
                        try{
                            $('#jp').html(numberToCurrency(msg.total_jp));
                            $('input[name=jp]').val(msg.total_jp);
                        }catch(e){
                            $('#jp').html('');
                            $('input[name=jp]').val('');
                        }
                    }
                })
            }
        );
    
            $('#kategori').autocomplete("<?= base_url('inv_autocomplete/get_tarif_kategori') ?>",
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
                    return parsed;
                },
                formatItem: function(data,i,max)
                {
                    var str = '<div class=result>'+data.nama+'</div>';
                    return str;
                },
                width: 322, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated) {
                $(this).val(data.nama);
                $('input[name=id_kategori]').val(data.id);
                $('#js').focus();
            }
        );
            
            $('#formcaritarif').submit(function(){
                var Url = '<?= base_url('referensi/manage_tarif') ?>/search/';
                $.ajax({
                    type : 'POST',
                    url: Url,               
                    data: $(this).serialize(),
                    cache: false,
                    success: function(data) {
                        $('#tarif_list').html(data);
                        $('#searching').dialog("close");
                        reset_all();                    
                    }
                });
                return false;
            });
        
        
            $('#formtarif').submit(function(){
                var layanan = $('input[name=id_layanan]').val();
                var kategori= $('input[name=id_kategori]').val();
                var js = currencyToNumber($('#js').val());
                var js_rs = currencyToNumber($('#js_rs').val());
                var jp = currencyToNumber($('#jp').html());
                var bhp= currencyToNumber($('#bhp').val());
                var uc = $('input[name=unit_cost]').val();
                var margin = $('#margin').val();
                var nominal = currencyToNumber($('#nominal').html());
                
                if($('input[name=id_layanan]').val()==''){
                    $('#msg_tarif').fadeIn('fast').html('Nama layanan tidak boleh kosong !');
                    $('#nama').focus();
                } else if($('input[name=id_kategori]').val()==''){
                    $('#msg_tarif').fadeIn('fast').html('Nama kategori tidak boleh kosong !');
                    $('#kategori').focus();
                }else if($('#js').val()==''){
                    $('#msg_tarif').fadeIn('fast').html('Jasa sarana tidak boleh kosong !');
                    $('#jenis').focus();
                }else if($('#js_rs').val() ==''){
                    $('#msg_tarif').fadeIn('fast').html('Jasa sarana RS tidak boleh kosong !');
                    $('#js_rs').focus();
                }else if($('#bhp').val()==''){
                    $('#msg_tarif').fadeIn('fast').html('BHP tidak boleh kosong !');
                    $('#bhp').focus();
                }else if($('#margin').val()==''){
                    $('#msg_tarif').fadeIn('fast').html('Margin tidak boleh kosong !');
                    $('#margin').focus();
                }else{                
                    $.ajax({
                        url: '<?= base_url('referensi/manage_tarif') ?>/cek',
                        data:'tarif=tarif&layanan='+layanan+'&kategori='+kategori+'&js='+js+'&js_rs='+js_rs+'&jp='+jp+'&uc='+uc+'&bhp='+bhp+'&margin='+margin+'&nominal='+nominal,
                        cache: false,
                        dataType: 'json',
                        success: function(msg){
                            if (msg.status != true){
                                $('#text_konfirmasi').html('Nama Tarif Jasa <b>"'+$('#nama').val()+'"</b> sudah ada<br/> Apakah anda yakin akan menambahkannya lagi?');            
                            } else {
                                $('#text_konfirmasi').html('Nama Tarif Jasa <b>"'+$('#nama').val()+'"</b><br/> Apakah anda akan menyimpan data?');                    
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
            var Url = '';          
            if($('input[name=tipe]').val() == 'add'){
                Url = '<?= base_url('referensi/manage_tarif') ?>/add/1';
            }else{
                Url = '<?= base_url('referensi/manage_tarif') ?>/edit/1';
            }
        
            if(!request) {
                request = $.ajax({
                    type : 'POST',
                    url: Url,               
                    data: $('#formtarif').serialize(),
                    cache: false,
                    success: function(data) {
                        $('#tarif_list').html(data);
                        $('#form_tarif').dialog("close");
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
            $('input[name=tipe]').val('');
            $('input[name=id_tarif]').val('');
            $('input[name=nominals]').val('');
            $('input[name=unit_cost]').val('');
            $('input[name=jp]').val('');
            $('input[name=id_kategori]').val('');
            $('input').val('');
            $('#jp, #uc, #nominal').html('');
            $('#msg_tarif').fadeOut('fast');
            $('#msg_cari_tarif').fadeOut('fast');
            
        }
    
        function get_last_id(){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/get_last_id') ?>/tarif/id',
                cache: false,
                dataType : 'json',
                success: function(data) {
                    $('#nomor').html(data.last_id);
                    $('input[name=id_tarif]').val(data.last_id);
                }
            });
        }
    
        function get_tarif_list(p,search){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_tarif') ?>/list/'+p,
                data : 'search='+search,
                cache: false,
                success: function(data) {
                    $('#tarif_list').html(data);
                    reset_all();
                }
            });
        }
    
        function delete_tarif(id){
            var del = confirm("Anda yakin akan menghapus data ini ?");
            if(del){
                $.ajax({
                    type : 'GET',
                    url: '<?= base_url('referensi/manage_tarif') ?>/delete/'+$('.noblock').html(),
                    data :'id='+id,
                    cache: false,
                    success: function(data) {
                        $('#tarif_list').html(data);
                        alert_delete();
                    }
                });
            }
        }
    
        function paging(page, tab,search){
            get_tarif_list(page, search);
        }
    
        function subtotal_uc() {
            var js = currencyToNumber($('#js').val());
            var js_rs = currencyToNumber($('#js_rs').val());
            var jp = currencyToNumber($('#jp').html());
            var bhp= currencyToNumber($('#bhp').val());
            if (isNaN(js)) { js = 0;}
            if (isNaN(js_rs)) { js_rs = 0; }
            if (isNaN(jp)) { jp = 0; }
            if (isNaN(bhp)) { bhp = 0; }
            var val = js+js_rs+jp+bhp;
            $('#uc').html(numberToCurrency(val));
            $('input[name=unit_cost]').val(val);
            var margin = $('#margin').val()/100;
            var nominal= val+(margin*val);
            $('#nominal').html(numberToCurrency(nominal));
            $('input[name=nominals]').val(nominal);
        }
    
    
        function edit_tarif(arr){
            var data = arr.split("#");

            $('input[name=tipe]').val('edit');

            $('input[name=id_tarif]').val(data[0]);
            $('#nomor').html(data[0]);
            $('input[name=id_layanan]').val(data[1]);
            $('#nama').val(data[2]);
            $('input[name=id_kategori]').val(data[3]);
            $('#kategori').val(data[4]);
            $('#js').val(numberToCurrency(data[5]));
            $('#js_rs').val(numberToCurrency(data[6]));
            $('input[name=jp]').val(numberToCurrency(data[7]));
            $('#jp').html(numberToCurrency(data[7]));
            $('#bhp').val(numberToCurrency(data[8]));
            $('input[name=unit_cost]').val(data[9]);
            $('#uc').html(numberToCurrency(data[9]));
            $('#margin').val(data[10]);
            $('input[name=nominals]').val(data[11]);
            $('#nominal').html(numberToCurrency(data[11]));

            $('#form_tarif').dialog("option",  "title", "Edit Data Tarif Jasa");
            $('#form_tarif').dialog("open");
            $('input[name=save]').focus();

        }
    </script>
    <h1><?= $title ?></h1>
    <?= form_button('', 'Tambah Data', 'id=addtarif') ?>
    <?= form_button('', 'Cari', 'id=search') ?>
    <?= form_button('', 'Reset', 'class=resetan id=showAll') ?>
    <div id="searching" style="display: none; " class="data-input">
        <?= form_open('', 'id=formcaritarif') ?>
        <div class='msg' id="msg_cari_tarif"></div>
        <table width="100%">
            <tr><td>Nama Layanan</td><td><?= form_input('nama_layanan', null, 'id=nama_layanan size=20') ?></td></tr>
            <tr><td></td><td><?= form_submit('cari', 'Cari', ' class=cari') ?><?= form_button('batal', 'Reset', 'id=batal class=resetan') ?></td></tr>
        </table>
        <?= form_close(); ?>
    </div>
    <div id="list" class="data-list">
        <div id="form_tarif" style="display: none;position: relative; background: #fff; padding: 10px;">
            <div id="result"></div>
            <div class='msg' id="msg_tarif"></div>
            <?= form_open('', 'id=formtarif') ?>
            <?= form_hidden('tipe') ?>
            <?= form_hidden('id_tarif') ?>
            <?= form_hidden('jp') ?>
            <?= form_hidden('unit_cost') ?>
            <?= form_hidden('nominals') ?>
            <table width="100%">
                <tr><td width="25%">No.</td><td><span id="nomor"></span></td></tr>
                <tr><td>Nama Layanan</td><td><?= form_input('nama', '', 'id=nama size=50 ') ?>
                        <?= form_hidden('id_layanan') ?></td> </tr>
                <tr><td>Kategori</td><td><?= form_input('kategori', '', 'id=kategori size=50 ') ?>
                        <?= form_hidden('id_kategori') ?></td> </tr>
                <tr><td>Jasa Sarana</td><td><?= form_input('js', '', 'id=js onkeyup=FormNum(this) onblur=subtotal_uc()') ?></td></tr>
                <tr><td>Jasa Tindakan R.S</td><td><?= form_input('js_rs', '', 'id=js_rs onkeyup=FormNum(this) onblur=subtotal_uc()') ?></td></tr>
                <tr><td>Jasa Profesi (Total)</td><td style="padding-left: 6px;" id="jp"></td></tr>
                <tr><td>BHP</td><td><?= form_input('bhp', '', 'id=bhp onkeyup=FormNum(this) onblur=subtotal_uc()') ?></td></tr>
                <tr><td>Subtotal (Unit Cost)</td><td style="padding-left: 6px;" id="uc"></td></tr>
                <tr><td>Margin (%)</td><td><?= form_input('margin', '', 'id=margin size=5 onblur=subtotal_uc() maxlength=3') ?></td></tr>
                <tr><td>Nominal (Rp.)</td><td style="padding-left: 6px;" id="nominal"></td> </tr>
                <tr><td></td><td><?= form_submit('simpan', 'Simpan', 'id=simpan') ?> <?= form_reset('Reset', 'Reset', 'id=reset class=resetan') ?></td> </tr>
            </table>
            <?= form_close() ?>

        </div>

        <div id="konfirmasi" style="display: none; padding: 20px;">
            <div id="text_konfirmasi"></div>
        </div>
        <div id="tarif_list"></div>
    </div>
</div>