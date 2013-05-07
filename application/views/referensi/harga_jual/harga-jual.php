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
    function selected_item() {
        var jumlah = $('.tr_row').length-1;
        for (i = 0; i <= jumlah; i++) {
            if ($('#check'+i).is(':checked') === true) {
                $('#listdata'+i).addClass('selected');
            } else {
                $('#listdata'+i).removeClass('selected');
            }
        }
    }
    $('.check').live('click', function() {
        selected_item();
    });
    var request;
    $(function(){
        $('#checkall').button();
        $('button[id=reset]').button({
            icons: {
                primary: 'ui-icon-refresh'
            }
        });
        $('#update').button({
            icons: {
                primary: 'ui-icon-pencil'
            }
        });
        $('#checkall').live('click', function() {
            $('#checkall .ui-button-text').html('Uncheck all');
            $('#checkall').attr('id', 'uncheckall');
            $('.check').attr('checked', 'checked');
            selected_item();
        });
        $('#uncheckall').live('click', function() {
            $('#uncheckall .ui-button-text').html('Check all');
            $('#uncheckall').attr('id', 'checkall');
            $('.check').removeAttr('checked');
            selected_item();
        });
        $('#key').watermark('Search ...');
        $('input,textarea,select').removeAttr('disabled');
        $( "#addobat" ).button({icons: {primary: "ui-icon-newwin"}});
        $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
        $('button[type=submit]').button({icons: {primary: 'ui-icon-circle-check'}});
        $('.resetan').button({icons: {primary: 'ui-icon-folder-open'}});
        $('#bt_cariobat').button({icons: {primary: 'ui-icon-search'}});
        get_obat_list(1,'null');
        $('#showObatAll').click(function(){
            get_obat_list(1,'null');
        });
        $('#key').keyup(function(e) {
            if (e.keyCode === 13) {
                var id_pb= $('#key').val();
                $.ajax({
                    type: 'POST',
                    url: '<?= base_url('referensi/manage_harga_jual') ?>/search/',
                    data: 'barang_cari='+id_pb,
                    cache: false,
                    success: function(msg) {
                        $('#obat_list').html(msg);  
                    }
                });
                return false;
            }
        });
        
        $('#resetproedit').click(function(){
            reset_all();
        });
        $('#resetobat').click(function(){
            reset_all();
        });
        
        $('#update').click(function() {
            //$('#form_harga_jual2').submit();
            var status = ($('.check').is(':checked') === true);
            if (status === true) {
                $.ajax({
                    type: 'POST',
                    url: '<?= base_url('referensi/harga_jual_update') ?>',
                    data: $('#form_harga_jual2').serialize(),
                    success: function(data) {
                        $('#result_load').html(data);
                        $('#result_load').dialog({
                            autoOpen: true,
                            modal: true,
                            width: 750,
                            title: 'Update Harga Jual',
                            height: 400,
                            close: function() {
                                $("#result_load").dialog().remove();
                                var id_pb= $('#key').val();
                                $.ajax({
                                    type: 'POST',
                                    url: '<?= base_url('referensi/manage_harga_jual') ?>/search/',
                                    data: 'barang_cari='+id_pb,
                                    cache: false,
                                    success: function(msg) {
                                        $('#obat_list').html(msg);
                                        $('#loaddata').append('<div id=result_load></div>');
                                    }
                                })
                            }
                        })
                    }
                })
            } else {
                alert('Barang belum ada yang dipilih !');
            }
            return false;
        });
        
        $('#form_harga_jual2').submit(function() {
            alert('asad');
            return false;
            
        });
    });
    
    function save_obat(){
        var Url = '';       
        var tipe = $('input[name=tipe]').val();
        if(tipe === 'edit') {
            Url = '<?= base_url('referensi/manage_harga_jual') ?>/edit/';
        } else {
            Url = '<?= base_url('referensi/manage_harga_jual') ?>/add/';
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
        $('#konsinyasi').removeAttr('checked');
        $('#hna').val('');
        $('#stokmin').val('');
    }
    
    function get_obat_list(p,search){
        $.ajax({
            type : 'GET',
            url: '<?= base_url('referensi/manage_harga_jual') ?>/list/'+p,
            data :'search='+search,
            cache: false,
            success: function(data) {
                $('#obat_list').html(data);
                reset_all();
            }
        });
    }
    function paging(p, tab, search) {
        get_obat_list(p, search);
    }
    function delete_obat(id){
        var del = confirm("Anda yakin akan menghapus data ini ?");
        if(del){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_harga_jual') ?>/delete/'+$('.noblock').html(),
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
        $('#form_obat').dialog("option",  "title", "Edit Obat");
        $('#form_obat').dialog("open");
    }
    
   
</script>
<div class="kegiatan">
<h1><?= $title ?></h1>
<div id="result_load"></div>
<?= form_button('', 'Tampilkan', 'class=resetan id=showObatAll style="margin-left: 0px;"') ?>
<?= form_button(NULL, 'Check all', 'id=checkall') ?> <?= form_button('submit', 'Pilih', 'id=update style="margin-left:0"') ?>
<div style="margin-bottom: 2px; float: right;"><?= form_input('key', isset($_GET['pb'])?$_GET['pb']:NULL, 'id=key size=30 style="padding: 4px 5px 5px 5px;"') ?></div>
    <div class="data-list" id="obat_list">

    </div>

</div>