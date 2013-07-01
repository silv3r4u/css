<style>
	* { background: #f2f0ea; font-family: Arial, "Century Gothic", CenturyGothic, AppleGothic, sans-serif; font-size: 12px; -webkit-print-color-adjust:exact; }
	.form { width: 400px; margin: 0 auto; position: fixed; left: 0; right: 0; background: #fff; margin-top: 5%; padding: 20px; }
        body { padding: 0; margin: 0; }
        .form h1 { background: #fff; }
        .form a { background: none; }
        h1.judul { font-size: 14px; margin-left: 5px; font-weight: normal; border-bottom: 1px solid #d9d9d9; padding-bottom: 5px; }
</style>
<div style="background: #b8d806; height: 91px;">
    <div style="background: url(../assets/images/headbang.jpg) no-repeat top; height: 95px;">
        <div style="background: url(../assets/images/logo.png) no-repeat; height: 90px;"></div>
    </div>
</div>
<h1 class="judul">Import Data</h1>
<div class="form">
<h1>Import Data Pabrik, Supplier, Instansi</h1>
<form method="post" enctype="multipart/form-data" action="proses-instansi.php">
File Excel: <input name="userfile" type="file">
<input name="upload" type="submit" value="Import">
</form>
<br/><br/>
<h1>Import Data Barang</h1>
<form method="post" enctype="multipart/form-data" action="proses.php">
File Excel: <input name="userfile" type="file">
<input name="upload" type="submit" value="Import">
</form>
<br/>
<br/><br/>
<h1>Import Data Pasien / Customer</h1>
<form method="post" enctype="multipart/form-data" action="proses_cust.php">
File Excel: <input name="userfile" type="file">
<input name="upload" type="submit" value="Import">
</form>
<a href="../">Kembali</a>
</div>

