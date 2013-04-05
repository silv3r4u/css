<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
    
        $(function() {
            $('#cari').click(function(){
                document.location ='<?= base_url('billing') ?>';
            });
            $('#search').button({
                icons: {
                    secondary: 'ui-icon-search'
                }
            });
            $('#click').each(function(){
                $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
            });
            $('#click').button({
                icons: {
                    secondary: 'ui-icon-circle-check'
                }
            });
            when_load();
            $('#nopasien').autocomplete("<?= base_url('billing/get_data_pasien/') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].no_rm // nama field yang dicari
                        };
                    }
                    return parsed;
                
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.no_rm+' - '+data.nama+'</div>';
                    return str;
                },
                width: 370, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.no_rm);
                $('input[name=id_pasien]').val(data.id);
                $('#pasien').html(data.nama); 
                $('#kunjungan').html(data.no_daftar);
                var id = data.id;
                $.ajax({
                    url: '<?= base_url('billing/asuransi_kepesertaan_get_data') ?>/'+id,
                    data: '',
                    cache: false,
                    success: function(msg) {
                        $('#produk').html(msg);
                    }
                })
            });
            $('#click').click(function() {
        
                var id =  $('input[name=id_pasien]').val();
  
                if(id!=''){
            
                    $.ajax({
                        url: '<?= base_url('billing/load_data') ?>/'+id,
                        cache: false,
                        success: function(data) {
                            $('#result').html(data);
                        }
                    })
                }else{
                    alert("Masukkan id pasien dulu");
                }
            })
        
      
        })
    
        function when_load(){
            var id = $('input[name=id_pasien]').val();
            $.ajax({
                url: '<?= base_url('billing/load_data') ?>/'+id,
                cache: false,
                success: function(data) {
                    $('#result2').html(data);
                }
            })
        }
    
    
    </script>
    <?php if (isset($pasien) and ($pasien != null)): ?>
        <h1><?= $title ?></h1>
        <div class="data-input">
            <fieldset><legend>Summary</legend>
                <label>Tanggal</label><?= date("d/m/Y") ?>
                <label>No. RM</label><?= form_input('nopasien', $pasien->no_rm, 'id=nopasien size=30') ?> <?= form_hidden('id_pasien', $pasien->id) ?>
                <label>No. Kunjungan</label><span class="label" id="kunjungan"><?= $pasien->no_daftar ?></span>
                <label>Nama Pasien</label><span class="label" id="pasien"><?= $pasien->nama ?></span>
                <label>Produk Asuransi</label>
                <span class="label" id="produk">
                    <?php
                    foreach ($asuransi as $data) {
                        echo $data->asuransi . ' ' . $data->polis_no . '<br/>';
                    }
                    ?>
                </span>

                <label>Total (Rp.)</label><td id="total">
                    <label></label><?= form_submit('', 'Reset', 'id=cari') ?>

            </fieldset>
        </div>
        <div id="result2"></div>

    <?php else: ?>
        <h1><?= $title ?></h1>
        <div class="data-input">
            <fieldset><legend>Summary</legend>
                <label>Tanggal</label><?= date("d/m/Y") ?>
                <label>Pasien</label><?= form_input('nopasien', null, 'id=nopasien size=30') ?> <?= form_hidden('id_pasien') ?>
                <label>No. Kunjungan</label><span class="label" id="kunjungan"></span>
                <label>Pasien</label><span class="label" id="pasien"></span>
                <label>Produk Asuransi</label><span class="label" id="produk"></span>
                <label>Total (Rp.)</label><span class="label" id="total"></span>
                <label></label><?= form_submit('data', 'Tampilkan', 'id=click') ?>
            </fieldset>
            <div id="result"></div>
        </div>


    <?php endif; ?>

</div>
<?php die; ?>

<!--


-->