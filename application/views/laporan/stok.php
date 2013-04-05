<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div id="result_detail" style="display: none"></div>
<div id="stelling_cetak" style="display: none"></div>
<div class="kegiatan">
    <script type="text/javascript">
        $(function() {
            $('#excelpsi,#cetakabc').hide();
            <?php if (isset($_GET['perundangan']) and $_GET['perundangan'] == 'Psikotropika') { ?>
                $('#excelpsi').show();
            <?php } ?>
            <?php if (isset($_GET['sort']) and $_GET['sort'] == 'History' and $_GET['transaksi_jenis'] == 'Penjualan') { ?>
                $('#cetakabc').show();
            <?php } ?>
                $('button[id=reset]').button({
                    icons: {
                        primary: 'ui-icon-circle-check'
                    }
                });
                $('#cetakrl').click(function() {
                    var awal = $('#awal').val();
                    var akhir= $('#akhir').val();
                    if (awal == '') {
                        alert('Tanggal tidak boleh kosong !');
                        $('#awal').focus();
                        return false;
                    } 
                    if (akhir == '') {
                        alert('Tanggal tidak boleh kosong !');
                        $('#akhir').focus();
                        return false;
                    }
                    location.href='<?= base_url('laporan/rekap_laporan') ?>?awal='+awal+'&akhir='+akhir;
            
                })
                $('button[id=reset]').click(function() {
                    $('#hasil').html('');
                })
                $('input[type=submit]').each(function(){
                    $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
                });
                $('button[type=submit]').button({
                    icons: {
                        primary: 'ui-icon-circle-check'
                    }
                });
                $('button[id=stelling], button[id=excelpsi], button[id=excel], button[id=cetakrl], button[id=cetakabc]').button({
                    icons: {
                        primary: 'ui-icon-print'
                    }
                })
                $('#excel').click(function() {
                    var perundangan = '<?= (isset($_GET['perundangan']) and $_GET['perundangan'] != '') ? $_GET['perundangan'] : NULL ?>';
                    if (perundangan != 'Narkotika') {
                        location.href='<?= base_url('laporan/print_stok') ?>?<?= generate_get_parameter($_GET) ?>';
                    } else {
                        var awal = $('#awal').val();
                        var akhir= $('#akhir').val();
                        window.open('<?= base_url('laporan/narkotika') ?>?awal='+awal+'&akhir='+akhir,'mywindow','location=1,status=1,scrollbars=1,width=730px,height=500px');
                    }
                })
                $('#cetakabc').click(function() {
                    var awal = $('#awal').val();
                    var akhir= $('#akhir').val();
                    location.href='<?= base_url('laporan/laporan_abc') ?>?awal='+awal+'&akhir='+akhir;
                })
                $('#stelling').click(function() {
                    var awal = $('#awal').val();
                    var akhir= $('#akhir').val();
                    var id = $('input[name=id_pb]').val();
                    if (id != '') {
                        $.ajax({
                            url: '<?= base_url('laporan/stelling') ?>',
                            data: 'id_pb='+id+'&awal='+awal+'&akhir='+akhir,
                            cache: false,
                            success: function(data) {
                                $('#stelling_cetak').html(data);
                                $('#stelling_cetak').dialog({
                                    autoOpen: true,
                                    height: 400,
                                    width: 870,
                                    modal: true
                                });
                            }
                        })
                        //window.open('<?= base_url('cetak/inventory/stelling') ?>?id_pb='+id+'&awal='+awal+'&akhir='+akhir,'mywindow','location=1,status=1,scrollbars=1,width=830px,height=500px');
                    } else {
                        alert('Pilih terlebih dahulu nama packing barang!');
                    }
                })
                $('#excelpsi').click(function() {
                    var awal = $('#awal').val();
                    var akhir= $('#akhir').val();
                    var id = $('input[name=id_pb]').val();
                    $.ajax({
                        url: '<?= base_url('laporan/psikotropika') ?>',
                        data: 'awal='+awal+'&akhir='+akhir+'&perundangan=Psikotropika&sort=History',
                        cache: false,
                        success: function(data) {
                            $('#stelling_cetak').html(data);
                            $('#stelling_cetak').dialog({
                                autoOpen: true,
                                height: 400,
                                width: 870,
                                modal: true
                            });
                        }
                    })
                })
                $('#awal, #akhir').datepicker({
                    changeYear: true,
                    changeMonth: true
                })
                $('#last').click(function() {
                    if ($('#last').is(':checked') == true) {
                
                        $('#awal,#akhir').val('').attr('disabled', 'disabled');
                    }
                })
                $('#history').click(function() {
                    if ($('#history').is(':checked') == true) {
                        $('#awal,#akhir').removeAttr('disabled', 'disabled');
                    }
                })
                $('#pb').autocomplete("<?= base_url('inv_autocomplete/load_data_packing_barang') ?>",
                {
                    parse: function(data){
                        var parsed = [];
                        for (var i=0; i < data.length; i++) {
                            parsed[i] = {
                                data: data[i],
                                value: data[i].nama // nama field yang dicari
                            };
                        }
                        $('#id_pb').val('');
                        return parsed;
                
                    },
                    formatItem: function(data,i,max){
                        var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = '';
                        if (data.isi != '1') { var isi = '@ '+data.isi; }
                        if (data.kekuatan != null) { var kekuatan = data.kekuatan; }
                        if (data.satuan != null) { var satuan = data.satuan; }
                        if (data.sediaan != null) { var sediaan = data.sediaan; }
                        if (data.pabrik != null) { var pabrik = data.pabrik; }
                        if (data.satuan_terkecil != null) { var satuan_terkecil = data.satuan_terkecil; }
                        if (data.id_obat == null) {
                            var str = '<div class=result>'+data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
                        } else {
                            if (data.generik == 'Non Generik') {
                                var str = '<div class=result>'+data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil+'</div>';
                            } else {
                                var str = '<div class=result>'+data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
                            }
                        }
                        return str;
                    },
                    width: 370, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                    dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
                }).result(
                function(event,data,formated){
                    var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = '';
                    if (data.isi != '1') { var isi = '@ '+data.isi; }
                    if (data.kekuatan != null) { var kekuatan = data.kekuatan; }
                    if (data.satuan != null) { var satuan = data.satuan; }
                    if (data.sediaan != null) { var sediaan = data.sediaan; }
                    if (data.pabrik != null) { var pabrik = data.pabrik; }
                    if (data.satuan_terkecil != null) { var satuan_terkecil = data.satuan_terkecil; }
                    if (data.id_obat == null) {
                        $(this).val(data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil);
                    } else {
                        if (data.generik == 'Non Generik') {
                            $(this).val(data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil);
                        } else {
                            $(this).val(data.nama+' '+((kekuatan == '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil);
                        }
                    }
                    $('input[name=id_pb]').val(data.id);           
                });
                $('#reset').click(function() {
                    var url = $('#form_stok').attr('action');
                    $('#loaddata').load(url)
                })
                $('#form_stok').submit(function() {
                    var url = $(this).attr('action');
                    $.ajax({
                        type: 'GET',
                        url: url,
                        data: $(this).serialize(),
                        success: function(data) {
                            $('#loaddata').html(data);
                        }
                    })
                    return false;
                })
                $('.view_transaction').click(function() {
                    var url = $(this).attr('href');
                    $.get(url, function(data) {
                        $('#result_detail').html(data);
                        $('#result_detail').dialog({
                            autoOpen: true,
                            height: 500,
                            width: 900,
                            modal: true
                        });
                    });
                    return false;
                })
            })
    </script>
    <h1><?= $title ?></h1>
    <div class="data-input">
        <fieldset><legend>Parameter Pencarian</legend>
            <?= form_open('laporan/stok', 'id=form_stok') ?>
            <?php
            $disabled = null;
            if (isset($_GET['sort'])) {
                if ($_GET['sort'] == 'Terakhir') {
                    $disabled = "disabled";
                }
            }
            ?>
            <label>Urutkan:</label>
            <span class="label"><?= form_radio('sort', 'Terakhir', isset($_GET['sort']) and ($_GET['sort'] == 'Terakhir') ? TRUE : FALSE, 'id=last ') ?> Terakhir</span>
            <span class="label"><?= form_radio('sort', 'History', isset($_GET['sort']) and ($_GET['sort'] == 'History') ? TRUE : FALSE, 'id=history ') ?> History</span>
            <label>Waktu:</label><?= form_input('awal', isset($_GET['awal']) ? $_GET['awal'] : NULL, 'id=awal size=10 ' . $disabled) ?> <span class="label"> s . d </span><?= form_input('akhir', isset($_GET['akhir']) ? $_GET['akhir'] : NULL, 'id=akhir size=10 ' . $disabled) ?>
            <label>Jenis Transaksi:</label><?= form_dropdown('transaksi_jenis', $jenis_transaksi, isset($_GET['transaksi_jenis']) ? $_GET['transaksi_jenis'] : null) ?></td></tr>
            <label>Jenis Barang:</label><?= form_dropdown('jenis', array('' => 'Semua Jenis ...', 'Obat' => 'Obat','Non Obat' => 'Non Obat'), isset($_GET['jenis'])?$_GET['jenis']:NULL) ?>
            <label>Packing Barang:</label><?= form_input('pb', isset($_GET['pb']) ? $_GET['pb'] : null, 'id=pb size=50') ?> <?= form_hidden('id_pb', isset($_GET['id_pb']) ? $_GET['id_pb'] : null) ?>
            <label>Sediaan:</label><?= form_dropdown('sediaan', $sediaan, isset($_GET['pb']) ? $_GET['sediaan'] : null) ?>
            <label>Perundangan:</label><?= form_dropdown('perundangan', $perundangan, isset($_GET['perundangan']) ? $_GET['perundangan'] : null) ?>
            <label>Generik:</label><?= form_dropdown('generik', $generik, isset($_GET['generik']) ? $_GET['generik'] : null) ?>
            <label></label>
            <?= form_submit('cari', 'Cari', null) ?>  
            <?= form_button('Reset', 'Reset', 'id=reset') ?>
            <?= form_button('Cetak ', 'Cetak Psikotropika', 'id=excelpsi') ?>    
            <?= form_button('Cetak ', 'Cetak', 'id=excel') ?>
            <?= form_button(null, 'Cetak Kartu Stelling', 'id=stelling') ?>
            </table>
            <?= form_close() ?>
        </fieldset>
    </div>
    <div class="data-list">
        <table class="tabel" width="100%">
            <tr>
                <th>Tanggal</th>
                <th>No.Transaksi</th>
                <?php if (isset($_GET['transaksi_jenis']) and $_GET['transaksi_jenis'] == 'Penjualan') { ?>
                <th>Pembeli / Pasien</th>
                <?php } ?>
                <th>Jenis Transaksi</th>
                <th>Packing Barang</th>
                <th>ED</th>
                <th>HPP</th>
                <?php if (isset($_GET['sort']) and $_GET['sort'] == 'History') { ?>
                <th>Awal</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <?php } ?>
                <th>Sisa</th>
            </tr>
            <?php
            if (isset($_GET['sort'])) {
                $hpp = 0; $sisa = 0; $asset = 0;
                //$stok = stok_barang_muat_data(isset($_GET->awal)?$_GET->awal:NULL, isset($_GET->akhir)?$_GET->akhir:NULL, isset($_GET->id_pb)?$_GET->id_pb:NULL, isset($_GET->atc)?$_GET->atc:NULL, isset($_GET->ddd)?$_GET->ddd:NULL, isset($_GET->perundangan)?$_GET->perundangan:NULL, isset($_GET->generik)?$_GET->generik:NULL, isset($_GET->transaksi_jenis)?$_GET->transaksi_jenis:NULL, isset($_GET->sort)?$_GET->sort:NULL, isset($_GET->unit)?$_GET->unit:NULL);
                foreach ($list_data as $key => $data) {
                    $extra = NULL;
                    $link = NULL;
                    if ($data->transaksi_jenis == 'Pemesanan') {
                        $link = "inventory/pemesanan_detail";
                    } else if ($data->transaksi_jenis == 'Pembelian') {
                        $link = "inventory/pembelian_detail";
                    } else if ($data->transaksi_jenis == 'Stok Opname') {
                        $link = "inventory/stok_opname_detail";
                    } else if ($data->transaksi_jenis == 'Repackage') {
                        $link = "inventory/repackage_detail";
                    } else if ($data->transaksi_jenis == 'Retur Pembelian') {
                        $link = "inventory/retur_pembelian_detail";
                        $extra = "&trans=retur";
                    } else if ($data->transaksi_jenis == 'Penjualan') {
                        $link = "inventory/penjualan_detail";
                    } else if ($data->transaksi_jenis == 'Pemusnahan') {
                        $link = "inventory/pemusnahan_detail";
                    } else if ($data->transaksi_jenis == 'Retur Penjualan') {
                        $link = "inventory/retur_penjualan_detail";
                    } else if ($data->transaksi_jenis == 'Penerimaan Retur Pembelian') {
                        $link = "inventory/reretur_pembelian_detail";
                    } else if ($data->transaksi_jenis == 'Pengeluaran Retur Penjualan') {
                        $link = "inventory/reretur_penjualan_detail";
                    } else if ($data->transaksi_jenis == 'Distribusi') {
                        $link = "inventory/distribusi_detail";
                    } else if ($data->transaksi_jenis == 'Pemakaian') {
                        $link = "inventory/pemakaian_detail";
                    } else if ($data->transaksi_jenis == 'Retur Distribusi') {
                        $link = "inventory/retur_distribusi_detail";
                    } else if ($data->transaksi_jenis == 'Penerimaan Distribusi') {
                        $link = "inventory/penerimaan_distribusi_detail";
                    } else if ($data->transaksi_jenis == 'Penerimaan Retur Distribusi') {
                        $link = "inventory/penerimaan_retur_distribusi_detail";
                    }

                    $time = mktime(0, 0, 0, date("m"), date("d") + 180, date("Y"));
                    $new = date("Y-m-d", $time);
                    if ($data->transaksi_jenis != 'Pemesanan') {
                        if ($data->ed < date("Y-m-d")) {
                            $class = "class=alertred";
                        } else if ($data->ed > date("Y-m-d") and $data->ed <= $new) {
                            $class = "class=alertyellow";
                        } else {
                            $class = "class=" . (($key % 2 == 0) ? 'odd' : 'even') . "";
                        }
                    } else {
                        $class = "class=" . (($key % 2 == 0) ? 'odd' : 'even') . "";
                    }
                    ?>
                    <tr <?= $class ?>>
                        <td align="center"><?= datetimefmysql($data->waktu) ?></td>
                        <td align="center"><a class="view_transaction" href="<?= base_url($link . '/' . $data->transaksi_id.'?'.  generate_get_parameter($_GET)) ?>"><?= $data->transaksi_id ?></a></td>
                        <?php if (isset($_GET['transaksi_jenis']) and $_GET['transaksi_jenis'] == 'Penjualan') { 
                            $pembeli = $this->db->query("select pd.nama, pdd.nama as pasien
                                from penjualan p 
                                left join penduduk pd on (pd.id = p.pembeli_penduduk_id) 
                                left join resep r on (p.resep_id = r.id) 
                                left join penduduk pdd on (pdd.id = r.pasien_penduduk_id) 
                                where p.id = '".$data->transaksi_id."'")->row();
                            ?>
                        <td><?= isset($pembeli->nama)?$pembeli->nama:$pembeli->pasien ?></td>
                        <?php } ?>
                        <td><?= $data->transaksi_jenis ?></td>
                        <td><?= $data->barang ?> <?= ($data->kekuatan != '1') ? $data->kekuatan : null ?>  <?= $data->satuan ?> <?= $data->sediaan ?> <?= (($data->generik == 'Non Generik') ? '' : $data->pabrik) ?> @ <?= ($data->isi == 1) ? '' : $data->isi ?> <?= $data->satuan_terkecil ?></td>
                        <td align="center"><?= ($data->transaksi_jenis == 'Pemesanan') ? '-' : datefmysql($data->ed) ?></td>
                        <td align="right"><?= inttocur($data->hpp) ?></td>
                        <?php if (isset($_GET['sort']) and $_GET['sort'] == 'History') { ?>
                        <td><?= $data->awal ?></td>
                        <td><?= $data->masuk ?></td>
                        <td><?= $data->keluar ?></td>
                        <?php } ?>
                        <td><?= $data->sisa ?></td>
                    </tr>
                    <?php
                    $hpp = $hpp+$data->hpp;
                    $sisa = $sisa+$data->sisa;
                    $asset = $asset+($data->hpp*$data->sisa);
                }
            } else {
                for ($i = 1; $i <= 2; $i++) {
                    ?>
                    <tr class="<?= ($i % 2 == 1) ? 'odd' : 'even' ?>">
                        <td align="center">&nbsp;</td>
                        <td align="center"></td>
                        <td align="center"></td>
                        <td align="center"></td>
                        <td align="center"></td>
                        <td align="center"></td>
                        <td align="center"></td>
                    </tr>
    <?php }
}
?>
        </table>
    <br/>
    <?php
    if (isset($list_data) and (count($list_data) > 0) and isset($_GET['sort']) and $_GET['transaksi_jenis'] != '' and $_GET['sort'] == 'Terakhir') { ?>
    <b>TOR: <?= rupiah(($hpp/count($list_data))/($sisa/count($list_data))) ?></b><br/>
    <b>Nilai Asset: <?= rupiah($asset) ?></b>
    <?php } ?>
    </div>
</div>