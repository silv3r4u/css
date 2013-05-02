<table width="100%">
    <tr><td colspan="7" align="center" style="text-transform: uppercase"><?= $datas->nama ?></td> </tr>
    <tr><td colspan="7" align="center"><?= $datas->alamat ?> <?= $datas->kabupaten ?></td> </tr>
    <tr><td colspan="7" align="center">Telp. <?= $datas->telp ?>,  Fax. <?= $datas->fax ?>, Email <?= $datas->email ?></td> </tr>
</table>
<table class="tabel" width="100%" style="margin-top: 10px;">
    <tr>
        <th rowspan="2">Barang</th>
        <th rowspan="2">Saldo Awal</th>
        <th colspan="2">Pemasukkan</th>
        <th colspan="2">Penggunaan</th>
        <th rowspan="2">Saldo Akhir</th>
    </tr>
    <tr><th>Dari</th><th>Jumlah</th>
        <th>Untuk</th><th>Jumlah</th>
    </tr>
    <?php foreach ($list_data as $key => $data) { 
        if ($data->transaksi_jenis != 'Pemesanan') {
            $prsh = $this->db->query("select * from pembelian p join relasi_instansi r on (p.suplier_relasi_instansi_id = r.id) where p.id = '".$data->transaksi_id."'")->row();
            $pasien=$this->db->query("select * from penjualan p join resep r on (r.id = p.resep_id) join penduduk pd on (r.pasien_penduduk_id = pd.id) where p.id = '".$data->transaksi_id."'")->row();
        ?>
    <tr class="<?= ($key%2==0)?'even':'odd' ?>">
        <td><?= $data->barang ?> <?= ($data->kekuatan != '1')?$data->kekuatan:null ?>  <?= $data->satuan ?> <?= $data->sediaan ?> <?= (($data->generik == 'Non Generik')?'':$data->pabrik) ?> @ <?= ($data->isi==1)?'':$data->isi ?> <?= $data->satuan_terkecil ?></td>
        <td align="center"><?= $data->awal ?></td>
        <td><?= (isset($prsh->nama) and $data->transaksi_jenis == 'Pembelian')?$prsh->nama:'-' ?></td>
        <td align="center"><?= $data->masuk ?></td>
        <td><?= (isset($pasien->nama) and $data->transaksi_jenis == 'Penjualan')?$pasien->nama:'-' ?></td>
        <td align="center"><?= $data->keluar ?></td>
        <td align="center"><?= $data->sisa ?></td>
    </tr>
    <?php } 
    } ?>
    
</table>