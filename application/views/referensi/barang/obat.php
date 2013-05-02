<script type="text/javascript">
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
        $( "#addobat" ).button({icons: {primary: "ui-icon-newwin"}});
        $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
        $('button[type=submit]').button({icons: {primary: 'ui-icon-circle-check'}});
        $('.resetan').button({icons: {primary: 'ui-icon-folder-open'}});
        $('#bt_cariobat').button({icons: {primary: 'ui-icon-search'}});
        get_obat_list(1,'null');
        $('#showObatAll').click(function(){
            get_obat_list(1,'null');
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
        $('#form_obat').dialog({
            autoOpen: false,
            height: 560,
            width: 500,
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
                reset_all();
            },
            open : function(){
                
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
                        konfirmasi_lanjut();
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
        $('#hna').val('');
        $('#stokmin').val('');
    }
    
    function get_obat_list(p,search){
        $.ajax({
            type : 'GET',
            url: '<?= base_url('referensi/manage_barang_obat') ?>/list/'+p,
            data :'search='+search,
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
        $('#form_obat').dialog("option",  "title", "Edit Obat");
        $('#form_obat').dialog("open");
    }
    
   
</script>

<?= form_button('', 'Tambah Data', 'id=addobat class=newrow style="margin-left: 2px;"') ?>
<?= form_button('', 'Cari', 'id=bt_cariobat class=cari style="margin-left: 0px;"') ?>
<?= form_button('', 'Tampilkan', 'class=resetan id=showObatAll style="margin-left: 0px;"') ?>
<br/><br/>
<div id="form_obat" style="display: none;">
    <div class="msg" id="msg_obat"></div>
    <?= form_open_multipart('', 'id=formobat') ?>
    <?= form_hidden('tipe') ?>
    <?= form_hidden('id_obat') ?>

    <table width="100%">
        <tr>
            <td width="25%" align="right">Nama:</td>
            <td><?= form_input('nama', '', 'id=namaobat class=nama size=60') ?> </td>
        </tr>
        <tr>
            <td width="15%" align="right">Pabrik:</td>
            <td>
                <?= form_input('', '', 'class=pabrik size=60') ?>
                <?= form_hidden('id_pabrik_obat', '', 'class=id_pabrik id=pabrik_id') ?>
            </td>
        </tr>
        <tr>
            <td width="15%" align="right">Kekuatan:</td>
            <td><?= form_input('kekuatan', '1', 'id=kekuatan size=10 onkeyup=Angka(this)') ?> </td>
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
            <td><?= form_textarea('indikasi', NULL, 'id=indikasi style="width: 90%; height: 40px;"') ?></td>
        </tr>
        <tr>
            <td align="right">Dosis:</td>
            <td><?= form_textarea('dosis', NULL, 'id=dosis style="width: 90%; height: 40px;"') ?></td>
        </tr>
        <tr>
            <td align="right">Kandungan:</td>
            <td><?= form_textarea('kandungan', NULL, 'id=kandungan style="width: 90%; height: 40px;"') ?></td>
        </tr>
        <tr>
            <td align="right">HNA (Rp.):</td>
            <td><?= form_input('hna', NULL, 'id=hna size=10 onkeyup=FormNum(this)') ?></td>
        </tr>
        <tr>
            <td align="right">Stok Minimal:</td>
            <td><?= form_input('stokmin', 0, 'id=stokmin size=10 onkeyup=Angka(this)') ?></td>
        </tr>
        <tr>
            <td width="15%"></td>
            <td>
                <?= form_radio('generik', 'Generik', true, 'id=a') ?>Generik
                <?= form_radio('generik', 'Non Generik', false, 'id=c') ?>Non Generik
            </td>
        </tr>
    </table>
    <?= form_close() ?>

</div>

<div id="konfirmasi_obat" style="display: none;">
    <div id="text_konfirmasi_obat"></div>
</div>

<div id="cariobat" style="display: none;" title="Parameter Pencarian">
    <?= form_open('', 'id=form_cariobat') ?>
    <?= form_hidden('kat_obat') ?>
    <?= form_hidden('id_barang_obat') ?>
    <div class="msg" id="msg_cariobat"></div>
    <table width="100%">
        <tr>
            <td width="15%" align="right">Nama Obat:</td>
            <td><?= form_input('nama', null, 'id=nama class=nama size=60') ?> </td>
        </tr>
        <tr>
            <td width="15%" align="right">Pabrik:</td>
            <td>
                <?= form_input('pabrik', null, 'class=pabrik size=60') ?>
                <?= form_hidden('id_pabriks_obat', null, 'class=id_pabrik') ?>
            </td>
        </tr>
        <tr>
            <td width="15%" align="right">Indikasi:</td>
            <td>
                <?= form_input('indikasi_obat', null, 'class=indikasi_obat size=60') ?>
            </td>
        </tr>
        <tr>
            <td width="15%" align="right">Dosis:</td>
            <td>
                <?= form_input('dosis_obat', null, 'class=dosis_obat size=60') ?>
            </td>
        </tr>
        <tr>
            <td width="15%" align="right">Kandungan:</td>
            <td>
                <?= form_input('kandungan', null, 'class=kandungan size=60') ?>
            </td>
        </tr>
        
    </table>
    <?= form_close() ?>
</div>
    
<div class="data-list" id="obat_list">

</div>

