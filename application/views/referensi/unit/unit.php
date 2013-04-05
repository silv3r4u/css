<?php $this->load->view('message') ?>
<script type="text/javascript">
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
            $('#form-prov').dialog('open');
        });
        
        $('#formsave').submit(function(){
            if($('#unit').val() != ''){
                $.ajax({
                    type : 'POST',
                    url: '<?= base_url('referensi/master_unit_save') ?>',               
                    data: $(this).serialize(),
                    cache: false,
                    success: function(data) {
                        $('#unit_list').html(data);
                        $('#form-prov').dialog('close');
                        reset_all();
                        alert_tambah();
                    
                    }
                });
            }else{
                $('.msg').fadeIn('fast').html('Nama unit tidak boleh kosong !');
            }
                
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
    <div id="form-prov" style="display: none">
        <div class="msg"></div>
        <?= form_open('', 'id = formsave') ?>
        <table>
            <tr><td>Nama unit</td><td><?= form_input('unit', null, 'id=unit size=30') ?></td></tr>
            <tr><td></td><td><?= form_submit('addunit', 'Simpan', 'id=simpan') ?>
                    <?= form_button('', 'Reset', 'id=reset') ?></td> </tr>
        </table>
        <?= form_close() ?>
    </div>


    <div id="form-prov-edit" style="display: none">
         <div class="msg" id="msg_edit"></div>
        <?= form_open('', 'id = formedit') ?>
        <table>
            <tr><td>Nama unit:</td><td><?= form_hidden('id_edit') ?><?= form_input('unit_edit', '', 'id=unit_edit size=30') ?></td></tr>
            <tr><td></td>
                <td>
                    <?= form_submit('editunit', 'Simpan', 'id=edit_unit') ?>
                    <?= form_button('', 'Reset', 'id=resetproedit') ?>
                </td>
            </tr>
        </table>
        <?= form_close() ?>
    </div>
    <div class="data-list">
        <div id="unit_list"></div>

    </div>

</div>
