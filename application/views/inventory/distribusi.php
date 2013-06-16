<?php $this->load->view('message'); ?>
<script type="text/javascript">
    $(function() {
        $('#reset').click(function() {
            url = '<?= base_url('inventory/distribusi') ?>';
            $('#loaddata').load(url);
        })
        $('#print').click(function() {
            var id = $('#id_distribusi').html();
            window.open('<?= base_url('inventory/distribusi_cetak') ?>/'+id, 'distribusi', 'width=400px, height=300px');
        })
        $("#form_distribusi").submit(function() {
            if ($('#unit').val() == '') {
                alert('Unit tujuan distribusi tidak boleh kosong !');
                $('#unit').focus;
                return false;
            }
            var jumlah = $('.tr_row').length-1;
            for(i = 0; i <= jumlah; i++) {
                if ($('#id_pb'+i).val() == '') {
                    alert('Data packing barang tidak boleh kosong !');
                    $('#pb'+i).focus();
                    return false;
                }
                if ($('#jl'+i).val() == '') {
                    if ($('#id_pb'+i).val() != '') {
                        alert('Jumlah tidak boleh kosong !');
                        $('#jl'+i).focus();
                        return false;
                    }
                }
            }
            var url = $(this).attr('action');
            $.ajax({
                type: 'POST',
                url: url,
                data: $(this).serialize(),
                dataType: 'json',
                success: function(data) {
                    if (data.status == true) {
                        $('button[type=submit]').hide();
                        $('#distribusi').html(data.id_distribusi);
                        alert_tambah();
                    }
                }
            })
            return false;

        });
        $('#tanggal').datetimepicker({
            changeYear: true,
            changeMonth: true
        });
        $('input[type=submit]').each(function(){
            $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
        });
        $('button[type=submit]').button({
            icons: {
                primary: 'ui-icon-circle-check'
            }
        });
        $('button[id=deletion]').button({
            icons: {
                primary: 'ui-icon-circle-close'
            }
        });
        $('button[id=print]').button({
            icons: {
                primary: 'ui-icon-print'
            }
        });
        $('button[id=addnewrow]').button({
            icons: {
                secondary: 'ui-icon-circle-plus'
            }
        });
        $('button[id=reset]').button({
            icons: {
                primary: 'ui-icon-refresh'
            }
        });
        $('button[id=print]').button({
            icons: {
                primary: 'ui-icon-print'
            }
        });
    })
    $(function() {
            for(x = 0; x <= 1; x++) {
                add(x);
            }
            $('#addnewrow').click(function() {
                row = $('.tr_row').length;
                add(row);
            });
        });
        function eliminate(el) {
            var parent = el.parentNode.parentNode;
            parent.parentNode.removeChild(parent);
            var jumlah = $('.tr_row').length-1;
            for (i = 0; i <= jumlah; i++) {
                $('.tr_row:eq('+i+')').children('td:eq(0)').html(i+1);
                $('.tr_row:eq('+i+')').children('td:eq(1)').children('.pb').attr('id','pb'+i);
                $('.tr_row:eq('+i+')').children('td:eq(1)').children('.id_pb').attr('id','id_pb'+i);
                $('.tr_row:eq('+i+')').children('td:eq(2)').attr('id','exp'+i);
                $('.tr_row:eq('+i+')').children('td:eq(3)').attr('id','sisa'+i);
                $('.tr_row:eq('+i+')').children('td:eq(4)').children('.ed').attr('id','ed'+i);
                $('.tr_row:eq('+i+')').children('td:eq(4)').children('.jl').attr('id','jml'+i);
            }
        }
        function add(i) {
            str = '<tr class=tr_row>'+
                '<td align=center>'+(i+1)+'</td>'+
                '<td><input type=text name=pb[] class=pb id=pb'+i+' size=80 /><input type=hidden class=id_pb name=id_pb[] id=id_pb'+i+' /></td>'+
                '<td align=center id=exp'+i+'></td>'+
                '<td align=center id=sisa'+i+'></td>'+
                '<td><input type=hidden name=ed[] class=ed id=ed'+i+' size=15 readonly /><input type=text name=jl[] class=jl id=jl'+i+' size=10 /></td>'+
                '<td class=aksi><a class=delete onclick=eliminate(this)></a></td>'+
                '</tr>';

            $('.form-inputan tbody').append(str);
            $('#jl'+i).keyup(function() {
                FormNum(this);
                var input = $(this).val();
                var sisa  = $('#sisa'+i).html();
                if (input > sisa) {
                    alert('Tidak boleh melebihi sisa stok');
                    $('#jl'+i).val('').focus();
                }
            });
            $('#pb'+i).autocomplete("<?= base_url('inv_autocomplete/load_data_packing_barang_per_ed') ?>",
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
                    var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = '';
                    if (data.isi !== '1') { var isi = '@ '+data.isi; }
                    if (data.kekuatan !== null && data.kekuatan !== '0') { var kekuatan = data.kekuatan; }
                    if (data.satuan !== null) { var satuan = data.satuan; }
                    if (data.sediaan !== null) { var sediaan = data.sediaan; }
                    if (data.pabrik !== null) { var pabrik = data.pabrik; }
                    if (data.satuan_terkecil !== null) { var satuan_terkecil = data.satuan_terkecil; }
                    if (data.id_obat === null) {
                        var str = '<div class=result>'+data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil+'<br/>'+datefmysql(data.ed)+'</div>';
                    } else {
                        if (data.generik === 'Non Generik') {
                            var str = '<div class=result>'+data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil+'<br/>'+datefmysql(data.ed)+'</div>';
                        } else {
                            var str = '<div class=result>'+data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil+'<br/>'+datefmysql(data.ed)+'</div>';
                        }
                    }
                    return str;
                },
                width: 400, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = '';
                if (data.isi !== '1') { var isi = '@ '+data.isi; }
                if (data.kekuatan !== null && data.kekuatan !== '0') { var kekuatan = data.kekuatan; }
                if (data.satuan !== null) { var satuan = data.satuan; }
                if (data.sediaan !== null) { var sediaan = data.sediaan; }
                if (data.pabrik !== null) { var pabrik = data.pabrik; }
                if (data.satuan_terkecil !== null) { var satuan_terkecil = data.satuan_terkecil; }
                if (data.id_obat === null) {
                    $(this).val(data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil+' '+datefmysql(data.ed));
                } else {
                    if (data.generik === 'Non Generik') {
                        $(this).val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil+' '+datefmysql(data.ed));
                    } else {
                        $(this).val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil+' '+datefmysql(data.ed));
                    }
                }
                $('#id_pb'+i).val(data.id);
                //alert(i);
                $('#ed'+i).val(datefmysql(data.ed));
                
                $('#exp'+i).html(datefmysql(data.ed));
                $('#sisa'+i).html(data.sisa);
                $('#jl'+i).focus();
                //jmlPakai(i);
            });
        }
</script>
<title><?= $title ?></title>
<div id="print_div" title="Cetak Distribusi" style="display: none"></div>
<div class="kegiatan">
    <h1><?= $title ?></h1>
    <?= form_open('inventory/distribusi', 'id=form_distribusi') ?>
    <div class="data-input">
        <fieldset><legend>Distribusi</legend>
            <label>No.:</label><span class="label" id="id_distribusi"><?= get_last_id('distribusi', 'id') ?></span>
            <label>Tanggal:</label> <?= form_input('tanggal', date('d/m/Y H:i'), 'id=tanggal') ?>
            <label>Unit:</label><?= form_dropdown('unit', $list_unit, NULL, 'id=unit') ?>
            <label></label>
            <?= isset($_GET['msg']) ? '' : form_button('Tambah Baris', 'Tambah Baris', 'id=addnewrow') ?>
        </fieldset>
    </div>
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="30%">Packing Barang</th>
                    <th width="15%">ED</th>
                    <th width="15%">Sisa Stok</th>
                    <th width="10%">Jumlah</th>
                    <th width="5%">#</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table><br/>
        <?= form_submit('save', 'Simpan', 'id=submit') ?>
        <?= form_button('Reset', 'Reset', 'id=reset') ?>
        <?= form_button('Cetak', 'Surat Distribusi', 'id=print') ?>
    </div>

    <?= form_close() ?>
</div>