<script type="text/javascript">
    $(function() {
        $('#checkall').click(function() {
            var status = ($(this).is(':checked') == true);
            if (status == true) {
                $('.check').attr('checked', 'checked');
            } else {
                $('.check').removeAttr('checked');
            }
        })
        $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
        $('button[type=submit]').button({icons: {primary: 'ui-icon-circle-check'}});
        $('#batal').button({icons: {secondary: 'ui-icon-refresh'}});
       
        get_privileges_list();
        $('#all').click(function(){
            $(".check").each( function() {
                $(this).attr("checked",'checked');
            })
        });
        $('#uncek').click(function(){
            $(".check").each( function() {
                $(this).removeAttr('checked');
            })
        });
        
        
        $('#batal').click(function(){
            $('#privform').dialog("close");
        });
        
        $('#form_priv').submit(function(){
            var Url = '<?= base_url('referensi/manage_privileges') ?>/add/';
           
            if ($('#unit').val() == '') {
                $('#pesan').fadeIn('fast').html('Unit harus dipilih terlebih dulu !');
                $('#unit').focus();
                return false;
            }else{    
                
                $.ajax({
                    type : 'POST',
                    url: Url,              
                    data: $(this).serialize(),
                    cache: false,
                    success: function(data) {
                        $('#list').html(data);
                        reset_all();
                        alert_edit_akun();
                    
                    }
                });
              
                return false;
            }
            return false;
            
           
        });
       
    })
    function alert_edit_akun() {
        $( "#edit_akun" ).dialog({
            modal: true,
            buttons: {
                Ok: function() {
                    $( this ).dialog( "close" );
                    //location.reload();
                }
            }
        });
    }
    
    
</script>

<h1><?= $title ?></h1>
<?= form_open('', 'id = form_priv') ?>
<fieldset><legend>User Account Permission</legend>
    <div class='msg' id="pesan"></div>
    <table width="100%">
        <tr><td width="20%">ID</td><td><?= $user->id ?><?= form_hidden('id_user', $user->id) ?></td> </tr>
        <tr><td>Nama</td><td><?= $user->nama ?></td> </tr>
    </table>
</fieldset>

<div class="data-list">
    <div id="list" style="padding-top: 10px;padding-bottom: 10px"></div>
    <?= form_submit('addprevileges', 'Simpan', 'id=simpan') ?>
</div>
<?= form_hidden('id_penduduk') ?>

<?= form_close() ?>
<div id="edit_akun" style="display: none" title="Information Alert">
    <p>
        <span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;"></span>
        Data Telah Berhasil di Update
    </p>
</div>

