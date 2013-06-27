<script type="text/javascript">
    function set_harga_jual(i) {
        var hna = currencyToNumber($('#hna'+i).html());
        var margin = parseInt($('#margin'+i).val())/100;
        var diskon = parseInt($('#diskon'+i).val())/100;
        //var harga_jual = (hna+(hna*margin)) - ((hna+(hna*margin))*diskon);
        var harga_jual = (hna*(margin+1))-((hna*(margin+1))*diskon);
        $('#harga_jual'+i).val(numberToCurrency(parseInt(harga_jual)));
        //($data->hna+($data->hna*$data->margin/100)) - (($data->hna+($data->hna*$data->margin/100))*($data->diskon/100));
    }

    function set_margin(i) {
        var hna = currencyToNumber($('#hna'+i).html());
        var harga_jual = currencyToNumber($('#harga_jual'+i).val());
        var diskon = parseInt($('#diskon'+i).val())/100;
        var margin = (harga_jual - (hna+(hna*diskon)))/(hna - (hna*diskon));
        var hsl = margin;
        if (isNaN(margin)) {
            var hsl = '';
        }
        $('#margin'+i).val(hsl*100);
    }
    function konfirmasi_lanjut() {
        var str = '<div id=konfirmasi_lanjut>'+
                '<p><span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;"></span>'+
                'Proses penambahan data obat berhasil dilakukan, <br/>Apakah anda akan melanjutkann ke proses pengemasan obat ?</p></div>';
        $('#loaddata').append(str);
        $('#konfirmasi_lanjut').dialog({
            autoOpen: true,
            title :'Konfirmasi',
            height: 200,
            width: 300,
            modal: true,
            buttons: {
                "Ya": function() {
                    $('#loaddata').load('<?= base_url('referensi/packing_barang') ?>');
                    $(this).dialog().remove();
                },
                "Tidak": function() {
                    $(this).dialog().remove();
                }
            }
        });
    }
    var request;
    $(function(){
        $('#form_obat').tabs();
        $('#key').watermark('Search ...');
        $('input,textarea,select').removeAttr('disabled');
        $( "#addobat" ).button({icons: {primary: "ui-icon-newwin"}});
        $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
        $('button[type=submit]').button({icons: {primary: 'ui-icon-circle-check'}});
        $('.resetan').button({icons: {primary: 'ui-icon-folder-open'}});
        $('#bt_cariobat').button({icons: {primary: 'ui-icon-search'}});
        $('#add_kemasan').button({icons: {primary: 'ui-icon-plus'}}).click(function() {
            var row = $('.rows').length;
            add_kemasan(row);
        });
        add_kemasan(0);
        get_obat_list(1,'null');
        $('#showObatAll').click(function(){
            $('#loaddata').load('<?= base_url('referensi/barang') ?>');
        });
        $('#bt_cariobat').click(function(){           
            $('#cariobat').dialog({
                modal: true,
                width: 450,
                height: 250,
                autoOpen: true,
                buttons: {
                    "Cari Obat": function() {
                        $('#form_cariobat').submit();
                    },
                    "Batal": function() {
                        $(this).dialog('close');
                    }
                }
            });
            $('#nama').focus();
        });
        $('#resetproedit').click(function(){
            reset_all();
        });
        $('#resetobat').click(function(){
            reset_all();
        });
        
        $('#addobat').click(function() {
            $('input[name=tipe]').val('add');
            $('#form_obat').dialog("option",  "title", "Tambah Obat");
            $('#form_obat').dialog("open");
        });
        
        $('.pabrik').autocomplete("<?= base_url('inv_autocomplete/load_data_pabrik') ?>",
        {
            parse: function(data){
                var parsed = [];
                for (var i=0; i < data.length; i++) {
                    parsed[i] = {
                        data: data[i],
                        value: data[i].nama // nama field yang dicari
                    };
                }
                $('input[name=id_pabrik_obat]').val('');
                return parsed;
            },
            formatItem: function(data,i,max){
                var str = '<div class=result>'+data.nama+'</div>';
                return str;
            },
            width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $(this).attr('value',data.nama);
            $('input[name=id_pabrik_obat]').val(data.id);
            $('input[name=id_pabriks_obat]').val(data.id);
        });
        $('#asuransi_produk').autocomplete("<?= base_url('inv_autocomplete/load_data_asuransi_produk') ?>",
        {
            parse: function(data){
                var parsed = [];
                for (var i=0; i < data.length; i++) {
                    parsed[i] = {
                        data: data[i],
                        value: data[i].nama // nama field yang dicari
                    };
                }
                $('input[name=id_pabrik_obat]').val('');
                return parsed;
            },
            formatItem: function(data,i,max){
                var str = '<div class=result>'+data.nama+'</div>';
                return str;
            },
            width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $(this).attr('value',data.nama);
            $('input[name=id_asuransi_produk]').val(data.id);
        });
        $('#form_obat').dialog({
            autoOpen: false,
            height: 580,
            width: 780,
            modal: true,
            resizable : true,
            buttons: {
                "Simpan": function() {
                    $('#formobat').submit();
                },
                "Batal": function() {
                    $(this).dialog('close');
                }
            },
            close : function(){
                $(this).dialog('close');
                reset_all();
            },
            open : function(){
                $('.kemasan tbody').html('');
                add_kemasan(0);
                set_harga_jual(0);
            }
        });
        
        $('#konfirmasi_obat').dialog({
            autoOpen: false,title :'Konfirmasi',height: 200,width: 300,
            modal: true,resizable : false,
            buttons: [ 
                { text: "Ok", click: function() { 
                        save_obat();
                        $( this ).dialog( "close" ); 
                    } 
                }, 
                { text: "Batal", click: function() { 
                        $( this ).dialog( "close" ); 
                    } 
                } 
            ]
        });
        $('#key').live('keyup', function(e) {
            if (e.keyCode === 13) {
                $.ajax({
                    type : 'GET',
                    url: '<?= base_url('referensi/manage_barang_obat') ?>/search',
                    data: 'search='+$('#key').val(),
                    cache: false,
                    success: function(data) {
                        $('#obat_list').html(data);
                    }
                });
            }
        });
        $('#form_cariobat').submit(function(){
            var Url = '<?= base_url('referensi/manage_barang_obat') ?>/search/';             
            if(!request) {
                request =  $.ajax({
                    type : 'POST',
                    url: Url+$('.noblock').html(),               
                    data: $(this).serialize(),
                    cache: false,
                    success: function(data) {
                        $('#obat_list').html(data);
                        $('#cariobat').dialog('close');
                        reset_all(); 
                        request = null;                            
                    }
                });
            }
            return false;
        });
        
        
        $('#formobat').submit(function(){
            var Url = '<?= base_url('referensi/manage_barang_obat') ?>/cek/1';
            var namaobat = $('#namaobat').val();
            if($('#namaobat').val()===''){
                $('#msg_obat').fadeIn('fast').html('Nama obat tidak boleh kosong !');
                $('#namaobat').focus();
                return false;
            }else{    
                
                $.ajax({
                    type : 'GET',
                    url: Url,               
                    data: 'nama='+namaobat,
                    dataType: 'json',
                    cache: false,
                    success: function(data) {
                        if (data.status === false){
                            $('#text_konfirmasi_obat').html('Apakah anda yakin akan menyimpan data obat "'+namaobat+'"?');            
                        } else {
                            $('#text_konfirmasi_obat').html('Apakah anda yakin akan menyimpan data obat "'+namaobat+'"?');                    
                        }
                        $('#konfirmasi_obat').dialog("open");
                            
                    }
                });        
                
            }
            return false;
        });

    });
    
    function save_obat(){
        var Url = '';       
        var tipe = $('input[name=tipe]').val();
        if(tipe === 'edit') {
            Url = '<?= base_url('referensi/manage_barang_obat') ?>/edit/';
        } else {
            Url = '<?= base_url('referensi/manage_barang_obat') ?>/add/';
        }            
        
        if(!request) {
            request =  $.ajax({
                type : 'POST',
                url: Url+$('.noblock').html(),               
                data: $('#formobat').serialize(),
                cache: false,
                success: function(data) {
                    $('#obat_list').html(data);
                    $('#form_obat').dialog("close");
                    if (tipe === 'edit') {
                        alert_edit();
                    } else {
                        //konfirmasi_lanjut();
                        alert_tambah();
                    }
                    reset_all(); 
                    $('#form_obat').dialog("close");
                    request = null;                            
                },error: function() {
                    alert('Gagal menambah data baru');
                }
            });
        }   
    }
    
    function reset_all(){
        $('#msg_obat').fadeOut('fast');
        $('#msg_cariobat').fadeOut('fast');
        
        $('.nama').val('');
        $('.pabrik').val('');
        //$('#kekuatan').val('');
        $('#atc').val('');
        $('#ddd').val('');
        $('#kekuatan').val(1);
        $('#admr').val(0);
        $('#a').attr('checked','checked');
        $('#c').removeAttr('checked');
        $('#j').attr('checked','checked');
        $('#k').removeAttr('checked');
        $('#perundangan').val('');
        $('select[name=satuan]').val('');
        $('select[name=sediaan]').val('');
        $('input[name=id_pabrik_obat]').val('');
        $('input[name=id_pabriks_obat]').val('');
        $('#id_barang').val('');
        $('#konsinyasi').removeAttr('checked');
        $('#hna,#dosis,#indikasi,#kandungan,#lokasi_rak').val('');
        $('#stokmin').val('');
    }
    
    function get_obat_list(p,search){
        $.ajax({
            type : 'GET',
            url: '<?= base_url('referensi/manage_barang_obat') ?>/list/'+p,
            data : 'search='+search+'&'+$('#form_cariobat').serialize(),
            cache: false,
            success: function(data) {
                $('#obat_list').html(data);
                reset_all();
            }
        });
    }
    
    function delete_obat(id){
        var del = confirm("Anda yakin akan menghapus data ini ?");
        if(del){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_barang_obat') ?>/delete/'+$('.noblock').html(),
                data :'id='+id,
                cache: false,
                success: function(data) {
                    $('#obat_list').html(data);
                    alert_delete();
                }
            });
        }
    }
    
    function edit_obat(arr){
        var data = arr.split("#");
        $('input[name=id_obat]').val(data[0]);
        $('#namaobat').val(data[1]);
        $('input[name=id_pabrik_obat]').val(data[2]);
        $('.pabrik').val(data[3]);
        $('#kekuatan').val(data[4]);
        $('select[name=satuan]').val(data[5]);
        $('select[name=sediaan]').val(data[6]);
        $('#atc').val(data[7]);
        $('#ddd').val(data[8]);
        $('#admr').val(data[9]);
        $('#perundangan').val(data[10]);
        $('#indikasi').val(data[13]);
        $('#dosis').val(data[14]);
        $('#kandungan').val(data[15]);
        $('#hna').val(numberToCurrency(data[16]));
        $('#stokmin').val(data[17]);
        $('#konsinyasi').removeAttr('checked');
        $('#lokasi_rak').val(data[19]);
        $('#asuransi_produk').val(data[20]);
        $('input[name=id_asuransi_produk]').val(data[21]);
        if (data[18] === '1') {
            $('#konsinyasi').attr('checked','checked');
        }
        if(data[11] !=='Generik'){
            $('#c').attr('checked','checked');
            $('#a').removeAttr('checked');
        }
        
          
        if(data[12] !=='Ya'){
            $('#k').attr('checked','checked');
            $('#j').removeAttr('checked');
        }
        
        
        $('#savebarang').removeAttr('disabled');
         
        $('input[name=tipe]').val('edit');
        $.ajax({
            url: '<?= base_url('referensi/load_data_edit_kemasan') ?>/'+data[0],
            cache: false,
            success: function(data) {
                $('.kemasan tbody').html(data);
            }
        });
        $('#form_obat').dialog("option",  "title", "Edit Obat");
        $('#form_obat').dialog("open");
    }
    
    function set_harga_jual(i) {
        var hna = currencyToNumber($('#hna').val());
        if (isNaN(hna)) {
            var hna = 0;
        }
        var isi = parseInt($('#isi'+i).val());
        if (isNaN(isi)) {
            var isi = 0;
        }
        
        var margin = parseInt($('#margin'+i).val())/100;
        var diskon = parseInt($('#diskon'+i).val())/100;
        var harga_jual = (hna*(margin+1))-((hna*(margin+1))*diskon);
        $('#harga_jual'+i).val(numberToCurrency(parseInt(harga_jual*isi)));
        
        //($data->hna+($data->hna*$data->margin/100)) - (($data->hna+($data->hna*$data->margin/100))*($data->diskon/100));
    }

    function set_margin(i) {
        var hna = currencyToNumber($('#hna').val());
        var harga_jual = currencyToNumber($('#harga_jual'+i).val());
        var diskon = parseInt($('#diskon'+i).val())/100;
        var isi = parseInt($('#isi'+i).val());
        var satu = harga_jual/isi;
        var dua  = satu - (hna-(hna*diskon));
        var tiga = (dua/hna)*100;
        var margin = tiga;
        var hsl = margin;
        var margin = (harga_jual - (hna+(hna*diskon)))/(hna - (hna*diskon));
        if (isNaN(margin)) {
            hsl = '';
        }
        $('#margin'+i).val(hsl);
    }
    
    function eliminate(el, id) {
        if (id !== '') {
            var ok = confirm('Anda yakin akan menghapus data kemasan ini');
            if (ok) {
                $.ajax({
                    type : 'GET',
                    url: '<?= base_url('referensi/manage_packing') ?>/delete/'+id,
                    data :'id='+id,
                    cache: false,
                    success: function(data) {
                        var parent = el.parentNode.parentNode;
                        parent.parentNode.removeChild(parent);
                        alert_delete();
                    }
                });
            }
        } else {
            var parent = el.parentNode.parentNode;
            parent.parentNode.removeChild(parent);
        }
    }
    
    function add_kemasan(i) {
        var str = '<tr class=rows>'+
                '<td><input type=hidden name=id_kemasan[] value="" /><input type=text name=barcode[] size=10 style="min-width: 100px;" /></td>'+
                '<td><select name="kemasan[]" style="min-width: 120px;"><option value="">Pilih kemasan ...</option><?php foreach ($kemasan as $rows) { echo '<option value="'.$rows->id.'">'.$rows->nama.'</option>'; } ?></select></td>'+
                '<td><input type=text name=isi[] size=10 class=isi id=isi'+i+' onkeyup=set_harga_jual('+i+') style="min-width: 100px;" /></td>'+
                '<td><select name="satuan_kecil[]" style="min-width: 120px;"><option value="">Pilih satuan ...</option><?php foreach ($kemasan as $rows) { echo '<option value="'.$rows->id.'">'.$rows->nama.'</option>'; } ?></select></td>'+
                '<td align="center"><input type=text name=margin[] size=5 onkeyup=set_harga_jual('+i+') value="0" id=margin'+i+' style="min-width: 50px;" /></td>'+
                '<td align="center"><input type=text name=diskon[] size=5 onkeyup=set_harga_jual('+i+') value="0" id=diskon'+i+' style="min-width: 50px;" /></td>'+
                '<td align="right" id="hj'+i+'"><input type=text name=harga_jual[] size=5 onblur=FormNum(this) value="0" onkeyup=set_margin('+i+') style="min-width: 50px;" id=harga_jual'+i+' /></td>'+
                '<td><input type=button value="delete" onclick=eliminate(this,"") /></td>'+
            '</tr>';
        $('.kemasan tbody').append(str);
    }
   
</script>

<?= form_button('', 'Tambah Data', 'id=addobat class=newrow style="margin-left: 2px;"') ?>
<?= form_button('', 'Reset', 'class=resetan id=showObatAll style="margin-left: 0px;"') ?>
<div style="margin-bottom: 2px; float: right;"><?= form_input('barang_cari', null, 'id=key size=10 style="padding: 4px 5px 5px 5px; min-width: 200px;"') ?></div>
<br/><br/>
<div id="form_obat" style="display: none;" class="data-input">
    <?= form_open('', 'id=formobat') ?>
    <ul>
        <li><a href="#tabs-1">Atribut Obat</a></li>
        <li><a href="#tabs-2">Kemasan Obat & Adm. Harga</a></li>
    </ul>
    <div id="tabs-1">
    <div class="msg" id="msg_obat"></div>
    
    <?= form_hidden('tipe') ?>
    <?= form_hidden('id_obat') ?>

    <table width="100%" class="inputan">
        <tr>
            <td width="25%" align="right">Nama:</td>
            <td><?= form_input('nama', '', 'id=namaobat class=nama') ?> </td>
        </tr>
        <tr>
            <td width="15%" align="right">Pabrik:</td>
            <td>
                <?= form_input('', '', 'class=pabrik') ?>
                <?= form_hidden('id_pabrik_obat', '', 'class=id_pabrik id=pabrik_id') ?>
            </td>
        </tr>
        <tr>
            <td width="15%" align="right">Kekuatan:</td>
            <td><?= form_input('kekuatan', '1', 'id=kekuatan size=10') ?> </td>
        </tr>
        <tr>
            <td width="15%" align="right">Satuan:</td>
            <td><?= form_dropdown('satuan', $satuan, null) ?></td>
        </tr>
        <tr>
            <td align="right">Macam Sediaan:</td>
            <td><?= form_dropdown('sediaan', $sediaan, null) ?></td>
        </tr>
        <tr>
            <td width="15%" align="right">Adm. R:</td>
            <td><?= form_dropdown('admr', $admr, null, 'id=admr') ?></td>
        </tr>
        <tr>
            <td width="15%" align="right">Perundangan:</td>
            <td><?= form_dropdown('perundangan', $perundangan, null, 'id=perundangan') ?></td>
        </tr>
        <tr>
            <td align="right">Indikasi:</td>
            <td><?= form_input('indikasi', NULL, 'id=indikasi') ?></td>
        </tr>
        <tr>
            <td align="right">Dosis:</td>
            <td><?= form_input('dosis', NULL, 'id=dosis') ?></td>
        </tr>
        <tr>
            <td align="right">Kandungan:</td>
            <td><?= form_input('kandungan', NULL, 'id=kandungan') ?></td>
        </tr>
        <tr>
            <td align="right">HNA (Rp.):</td>
            <td><?= form_input('hna', '0', 'id=hna size=10 onkeyup=FormNum(this)') ?></td>
        </tr>
        <tr>
            <td align="right">Stok Minimal:</td>
            <td><?= form_input('stokmin', 0, 'id=stokmin size=10 onkeyup=Angka(this)') ?></td>
        </tr>
        <tr>
            <td align="right">Lokasi Rak:</td>
            <td><?= form_input('lokasi_rak', NULL, 'id=lokasi_rak') ?></td>
        </tr>
        <tr>
            <td align="right">Konsinyasi?:</td>
            <td><?= form_checkbox('konsinyasi', '1', FALSE, 'id=konsinyasi') ?></td>
        </tr>
        <tr>
            <td align="right">Asuransi Produk:</td>
            <td><?= form_input('', NULL, 'id=asuransi_produk') ?><?= form_hidden('id_asuransi_produk') ?></td>
        </tr>
        <tr>
            <td width="15%"></td>
            <td>
                <span class="label"><?= form_radio('generik', 'Generik', true, 'id=a') ?>Generik</span>
                <span class="label"><?= form_radio('generik', 'Non Generik', false, 'id=c') ?>Non Generik</span>
            </td>
        </tr>
    </table>
    
    </div>
    <div id="tabs-2">
        <table class="tabel kemasan" width="100%">
            <thead>
                <tr>
                    <th width="15%">Barcode</th>
                    <th width="15%">Kemasan</th>
                    <th width="10%">Isi @</th>
                    <th width="15%">Satuan</th>
                    <th width="10%">Margin %</th>
                    <th width="10%">Diskon %</th> 
                    <th width="15%">Harga Jual (Rp.)</th>
                    <th width="10%">Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table><br/>
        <?= form_button(NULL, 'Tambah Kemasan', 'id=add_kemasan') ?>
    </div>
    <?= form_close() ?>
</div>

<div id="konfirmasi_obat" style="display: none;">
    <div id="text_konfirmasi_obat"></div>
</div>
    
<div class="data-list" id="obat_list">

</div>

