<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        $(function() {
            $('#addnewrow').button({icons: {secondary: 'ui-icon-circle-plus'}});
            $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('button[type=submit]').button({icons: {primary: 'ui-icon-circle-check'}});
            $('#reset').button({icons: {secondary: 'ui-icon-refresh'}});
            $('#reset').click(function(){
                $('#loaddata').empty().load('<?= base_url('referensi/asuransi') ?>');
            });
            initial_row();
        
            $('#form').submit(function(){
                var Url ='<?= base_url('referensi/manage_asuransi_kepesertaan') ?>/add/';
                var id_pdd =$('input[name=id_penduduk]').val();
                if ( id_pdd== '') {
                    $('.msg').fadeIn('fast').html('Nama pasien tidak boleh kosong !')
                    $('#nama').focus();
                    return false;
                }else{    
                
                    $.ajax({
                        type : 'POST',
                        url: Url,               
                        data: $(this).serialize(),
                        cache: false,
                        success: function(data) {  
                            get_ak_list(id_pdd);
                            alert_tambah();                        
                            //reset_all();                    
                        }
                    });
              
                    // alert($('input[name=tipe]').val());
                    return false;
                }
                return false;
            });
        
        
            $('#nama').autocomplete("<?= base_url('inv_autocomplete/load_penduduk') ?>",
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
                    var str = '<div class=result>'+data.nama+'<br/>'+data.alamat+'</div>';
                    return str;
                },
                max: 50,
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('input[name=id_penduduk]').val(data.id);
                var id_penduduk = data.id;
                get_ak_list(id_penduduk);
        
            });
            $('#addnewrow').click(function() {
                row = $('.tr_row').length + 1;
                add(row);
                i++;
            });
        
        
        })
    
        function initial_row(){
            i = 2;
            for(x = 1; x <= i; x++) {
                add(x);
            }
        }
    
        function get_ak_list(id_penduduk){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_asuransi_kepesertaan') ?>/list',
                data: 'id='+id_penduduk,
                cache: false,
                success: function(data) {
                    $('.form-inputan tbody').html(data);
               
                }
            })
        }
        function eliminate(el) {
            var parent = el.parentNode.parentNode;
            parent.parentNode.removeChild(parent);
            var jumlah = $('.tr_row').length;
            //alert(jumlah);
            for (i = 1; i <= jumlah; i++) {
                $('.tr_row:eq('+i+')').children('td:eq(0)').children('.bc').attr('id','bc'+(i+1));
                $('.tr_row:eq('+i+')').children('td:eq(1)').children('.pb').attr('id','pb'+(i+1));
                $('.tr_row:eq('+i+')').children('td:eq(1)').children('.id_pb').attr('id','id_pb'+(i+1));
                $('.tr_row:eq('+i+')').children('td:eq(2)').children('.jml').attr('id','jml'+(i+1));
            }
        }
    
        function reset_all(){
            $('.form-inputan tbody').html('');
            $('#nama').val('');
            $('input[name=id_penduduk]').val('');
            $('.msg').fadeOut('fast');
            initial_row();
        }
        function add(i) {
            str = '<tr class=tr_row>'+
                '<td><input type=text name=np[] id=np'+i+' class=bc size=15 style="width: 100%" /></td>'+
                '<td><input type=text name=produk[] id=produk'+i+' class=produk size=40 style="width: 100%" /><input type=hidden name=id_produk[] id=id_produk'+i+' class=id_produk size=10 /></td>'+
                '<td class=center><span href=# class=delete onclick=eliminate(this)><?= img('assets/images/icons/delete.png') ?></span></td>'+
                '</tr>';

            $('.form-inputan tbody').append(str);
            $('#produk'+i).autocomplete("<?= base_url('inv_autocomplete/load_data_produk_asuransi') ?>",
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
                    var str = '<div class=result>'+data.nama+' <br/>'+data.instansi+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
        
                $(this).val(data.nama);
                $('#id_produk'+i).val(data.id);
            });
        }
    
        function delete_ak(id_ak, obj){
            var del = confirm("Anda yakin akan menghapus data ini ?");
            if(del){
                $.ajax({
                    type : 'GET',
                    url: '<?= base_url('referensi/manage_asuransi_kepesertaan') ?>/delete/',
                    data :'id='+id_ak,
                    cache: false,
                    success: function(data) {
                        eliminate(obj);
                        alert_delete();
                    }
                });
           
            }
        
       
        }
    </script>
    <h1><?= $title ?></h1>

    <?= form_open('', 'id = form') ?>
    <div class="data-input">
        <fieldset><legend>Kepesertaan Asuransi</legend>
            <label>Nama Lengkap</label><?= form_input('nama', '', 'id=nama size=30') ?><?= form_hidden('id_penduduk') ?>
            <label></label><?= form_button('', 'Tambah Baris', 'id=addnewrow') ?>
        </fieldset>
    </div>
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
                <tr>
                    <th width="10%">No. Polis</th>
                    <th width="85%"><h3>Nama Produk Asuransi</h3></th>
                    <th width="5%"><h3>Aksi</h3></th>
                </tr>
            </thead>
            <tbody class="tbod">

            </tbody>
        </table>
        <br/>
        <?= form_submit('save', 'Simpan', 'id=save') ?>
        <?= form_button('Reset', 'Reset', 'id=reset') ?>

    </div>
    <?= form_close() ?>
</div>