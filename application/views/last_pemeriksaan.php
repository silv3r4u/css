<fieldset>
<table width="100%">
    <tr><td>Tanggal Pemeriksaan:</td><td><?= isset($last->id_penduduk)?indo_tgl($lastp->tanggal):NULL ?></td></tr>
    <tr><td>Subjektif:</td><td><?= isset($last->id_penduduk)?$lastp->subjektif:NULL ?></td></tr>
    <tr><td>Objektif:</td><td></td></tr>
    <tr><td>&nbsp; &nbsp; Suhu Badan:</td><td><?= isset($last->id_penduduk)?$lastp->suhu_badan:NULL ?></td></tr>
    <tr><td>&nbsp; &nbsp; Tekanan Darah:</td><td><?= isset($last->id_penduduk)?$lastp->tek_darah:NULL ?></td></tr>
    <tr><td>&nbsp; &nbsp; Respiration Rate:</td><td><?= isset($last->id_penduduk)?$lastp->res_rate:NULL ?></td></tr>
    <tr><td>&nbsp; &nbsp; Nadi:</td><td><?= isset($last->id_penduduk)?$lastp->nadi:NULL ?></td></tr>
    <tr><td>&nbsp; &nbsp; Gula Darah Sewaktu:</td><td><?= isset($last->id_penduduk)?$lastp->gds:NULL ?></td></tr>
    <tr><td>&nbsp; &nbsp; Angka Kolesterol:</td><td><?= isset($last->id_penduduk)?$lastp->angka_kolesterol:NULL ?></td></tr>
    <tr><td>&nbsp; &nbsp; Asam Urat:</td><td><?= isset($last->id_penduduk)?$lastp->asam_urat:NULL ?></td></tr>
    <tr><td>Assessment:</td><td><?= isset($last->id_penduduk)?$lastp->assessment:NULL ?></td></tr>
    <tr><td>Saran Pengobatan:</td><td><?= isset($last->id_penduduk)?$lastp->saran_pengobatan:NULL ?></td></tr>
    <tr><td>Saran Non Farmakoterapi:</td><td><?= isset($last->id_penduduk)?$lastp->saran_non_farmakoterapi:NULL ?></td></tr>
</table>
</fieldset>