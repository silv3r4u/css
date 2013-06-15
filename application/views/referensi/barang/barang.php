<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        var data = '';
        $(function() {
 
            $('#tabs').tabs();
            my_ajax('<?= base_url() ?>referensi/barang_obat','#obat');
        
            $('.nonobat').click(function(){
                $('#obat, #nonobat').html('');
                my_ajax('<?= base_url() ?>referensi/barang_non_obat','#nonobat');
            });
            $('.obat').click(function(){
                $('#obat, #nonobat').html('');
                my_ajax('<?= base_url() ?>referensi/barang_obat','#obat');
            });
            $('.config').click(function() {
                $('#obat, #nonobat').html('');
                my_ajax('<?= base_url('setting/config') ?>','#config');
            });
        });
       
        
        function my_ajax(url,element){
            $.ajax({
                url: url,
                dataType: '',
                success: function( response ) {
                    $(element).html(response);
                }
            });
        }
        function paging(page,mytab,search){
            if(mytab == 1){            
                get_nonobat_list(page,search);
            }else if(mytab == 2){
                get_obat_list(page,search);
            }
       
        }
    </script>
    <h1><?= $title ?></h1>
    <div class="data-input">
        <div id="tabs">
            <ul>
                <li><a class="obat" href="#obat">Obat</a></li>
                <li><a class="nonobat" href="#nonobat">Non Obat</a></li>
                <li><a class="config" href="#config">Konfigurasi Harga</a></li>

            </ul>
            <div id="obat"></div>
            <div id="nonobat"></div>
            <div id="config"></div>

        </div>
    </div>
</div>