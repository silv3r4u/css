<?php
// menggunakan class phpExcelReader
include "excel_reader2.php";

// koneksi ke mysql
mysql_connect("localhost", "root", "");
mysql_select_db("db_css"); 
if (isset($_POST['upload'])) {
// membaca file excel yang diupload
$data = new Spreadsheet_Excel_Reader($_FILES['userfile']['tmp_name']);

// membaca jumlah baris dari data excel
$baris = $data->rowcount($sheet_index=0);

// nilai awal counter untuk jumlah data yang sukses dan yang gagal diimport
$sukses = 0;
$gagal = 0;

// import data excel mulai baris ke-2 (karena baris pertama adalah nama kolom)
mysql_query("insert into opname_stok values ('','7','Inisialisasi Data Awal')");
$id_opname = mysql_insert_id();
for ($i=2; $i<=$baris; $i++)
{
    
    $nama = $data->val($i, 1);
    $kekuatan = $data->val($i, 2);
    $satuan = $data->val($i, 3);
    $adm_r = $data->val($i, 4);
    $perundangan = $data->val($i, 5);
    $kandungan = $data->val($i, 6);
    $indikasi = $data->val($i, 7);
    $dosis = $data->val($i, 8);
    $hna = $data->val($i, 9);
    $stokmin = $data->val($i, 10);
    $kategori = $data->val($i, 11);
    $kat = (($kategori != NULL)?$kategori:"NULL");
    $sat = (($satuan != NULL)?$satuan:"NULL");
    $query = "INSERT INTO barang (nama,barang_kategori_id, is_konsinyasi, stok_minimal, hna) VALUES ('$nama',$kat,'0','$stokmin','$hna')";
   // echo $query."<br/>";
    $hasil = mysql_query($query);
    
    $id_barang = mysql_insert_id();
    mysql_query("insert into obat (id, kekuatan, satuan_id, adm_r, perundangan, indikasi, kandungan, dosis) values ('$id_barang', '$kekuatan',$sat,'$adm_r','$perundangan','$indikasi', '$kandungan','$dosis')");
    mysql_query("insert into barang_packing (barcode, barang_id, isi, margin) values ('$id_barang','$id_barang','1','0')");
    
  
  if ($hasil) $sukses++;
  else $gagal++;
}

// tampilan status sukses dan gagal
echo "
<style>
	* { font-family: 'Century Gothic', CenturyGothic, AppleGothic, sans-serif; font-size: 13px; -webkit-print-color-adjust:exact; }
	.form { width: 400px; margin: auto; position: fixed; left: 0; right: 0; margin-top: 10%; background: #f2f0ea; padding: 20px; }
</style>
";
echo "<div class='form'><h3>Proses import data selesai.</h3>";
echo "<p>Jumlah data yang sukses diimport : ".$sukses."<br>";
echo "Jumlah data yang gagal diimport : ".$gagal."</p><br/><a href='index.php'>Kembali</a></div>";
}
?>
