<fieldset>
<table width="100%">
    <tr><td>Tanggal Pemeriksaan:</td><td><?= isset($lastp->penduduk_id)?indo_tgl($lastp->tanggal):NULL ?></td></tr>
    <tr><td>Subjektif:</td><td><?= isset($lastp->penduduk_id)?$lastp->subjektif:NULL ?></td></tr>
    <tr><td>Objektif:</td><td></td></tr>
    <tr><td>&nbsp; &nbsp; Suhu Badan:</td><td><?= isset($lastp->penduduk_id)?$lastp->suhu_badan:NULL ?></td></tr>
    <tr><td>&nbsp; &nbsp; Tekanan Darah:</td><td><?= isset($lastp->penduduk_id)?$lastp->tek_darah:NULL ?></td></tr>
    <tr><td>&nbsp; &nbsp; Respiration Rate:</td><td><?= isset($lastp->penduduk_id)?$lastp->res_rate:NULL ?></td></tr>
    <tr><td>&nbsp; &nbsp; Nadi:</td><td><?= isset($lastp->penduduk_id)?$lastp->nadi:NULL ?></td></tr>
    <tr><td>&nbsp; &nbsp; Gula Darah Sewaktu:</td><td><?= isset($lastp->penduduk_id)?$lastp->gds:NULL ?></td></tr>
    <tr><td>&nbsp; &nbsp; Angka Kolesterol:</td><td><?= isset($lastp->penduduk_id)?$lastp->angka_kolesterol:NULL ?></td></tr>
    <tr><td>&nbsp; &nbsp; Asam Urat:</td><td><?= isset($lastp->penduduk_id)?$lastp->asam_urat:NULL ?></td></tr>
    <tr><td>Assessment:</td><td><?= isset($lastp->penduduk_id)?$lastp->assessment:NULL ?></td></tr>
    <tr><td>Saran Pengobatan:</td><td><?= isset($lastp->penduduk_id)?$lastp->saran_pengobatan:NULL ?></td></tr>
    <tr><td>Saran Non Farmakoterapi:</td><td><?= isset($lastp->penduduk_id)?$lastp->saran_non_farmakoterapi:NULL ?></td></tr>
</table>
</fieldset>