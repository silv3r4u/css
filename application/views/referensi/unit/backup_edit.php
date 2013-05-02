<?php $this->load->view('message') ?>
<script type="text/javascript">
    function create_dialog_unit() {
        var str = '<div class=data-input id=form_unit><form action="" id=formsave method=post>'+
                '<label>Nama Unit:</label><input type=text name=unit id=unit size=40 /></form></div>';
        $('#loaddata').append(str);
        $('#form_unit').dialog({
            autoOpen: true,
            title:'tambah Unit',
            height: 170,
            width: 400,
            modal: true,
            close: function() {
                $(this).dialog().remove();
            },
            buttons: {
                "Simpan": function() {
                    $('#formsave').submit();
                },
                "Batal": function() {
                    $(this).dialog().remove();
                }
            }
        });
    }
    $(function() {
        // initial
        get_unit_list();
        $('#form-prov-edit').hide();
        $('#addnewrow').button({icons: {secondary: 'ui-icon-circle-plus'}});
        $('input[type=submit]').each(function(){
            $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
        });
        $('button[type=submit]').button({
            icons: {
                primary: 'ui-icon-circle-check'
            }
        });
        $('#reset, #resetproedit').button({icons: {secondary: 'ui-icon-refresh'}});
        //initial
        $('#form-prov, #form-prov-edit').dialog({
            autoOpen: false,
            title:'tambah Unit',
            height: 170,
            width: 400,
            modal: true,
            resizable : false,
            close : function(){
                reset_all();
            }
        });
        
        $('#addnewrow').click(function() {
            create_dialog_unit();
        });
        
        $('#formsave').submit(function() {
            alert('asd');
                
            return false;
        });
        
        $('#formedit').submit(function(){
            if($('#unit_edit').val() == ''){
                $('#msg_edit').fadeIn('fast').html('Nama unit tidak boleh kosong !');
            }else{
                $.ajax({
                    type : 'POST',
                    url: '<?= base_url('referensi/master_unit_edit') ?>',               
                    data: $(this).serialize(),
                    cache: false,
                    success: function(data) {
                        $('#unit_list').html(data);
                        $('#form-prov-edit').dialog('close');
                        reset_all();
                        alert_edit();
                    }
                });
            }
                
            return false;
        });        
        
        $('#reset, #resetproedit').click(function() {
            reset_all();
        });
        
       
        $('#unit').blur(function() {
            var unit = $('#unit').val();
            $.ajax({
                url: '<?= base_url('referensi/master_unit_search') ?>',
                data:'unit='+unit,
                cache: false,
                dataType: 'json',
                success: function(msg){
                    if (msg.status == false){
                        $('.msg').fadeIn('fast').html('Nama unit sudah terdaftar !');
                        $('#simpan').attr('disabled', 'disabled');
                        return false;
                    } else {
                        $('.msg').fadeOut('fast');
                        $('#simpan').removeAttr('disabled');
                        return false;
                    }
                }
            });
        })
    });
    
    function get_unit_list(){
        $.ajax({
            type : 'GET',
            url: '<?= base_url('referensi/master_unit_list') ?>', 
            cache: false,
            success: function(data) {
                $('#unit_list').html(data);
            }
        });
    }
    
    function delete_unit(id){
        var del = confirm("Anda yakin akan menghapus data ini ?");
        if(del){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/master_unit_delete') ?>',
                data :'id='+id,
                cache: false,
                success: function(data) {
                    $('#unit_list').html(data);
                    alert_delete();
                }
            });
        }
    }
    
    function edit_unit(id, nama){
        $('#unit_edit').val(nama);
        $('input[name=id_edit]').val(id);
        $('#form-prov-edit').dialog('open');
        
    }
    function reset_all(){
        $('#unit').val('');
        $('#unit_edit').val('');
    }
</script>
<title><?= $title ?></title>
<div class="kegiatan">
    <h1><?= $title ?></h1>


    <?= form_button('', 'Tambah data', 'id=addnewrow') ?>
   


    <div id="form-prov-edit" style="display: none">
         <div class="msg" id="msg_edit"></div>
        
    </div>
    <div class="data-list">
        <div id="unit_list"></div>

    </div>

</div>
