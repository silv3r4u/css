<fieldset>
<table width="100%">
    <tr><td>Tanggal Pemeriksaan:</td><td><?= isset($last->penduduk_id)?indo_tgl($last->tanggal):NULL ?></td></tr>
    <tr><td>RPD:</td><td><?= isset($last->penduduk_id)?$last->rpd:NULL ?></td></tr>
    <tr><td>RPK</td><td><?= isset($last->penduduk_id)?$last->rpk:NULL ?></td></tr>
    <tr><td>Pengobatan Sekarang:</td><td><?= isset($last->penduduk_id)?$last->ps:NULL ?></td></tr>
    <tr><td>Obat Herbal:</td><td><?= isset($last->penduduk_id)?$last->oh:NULL ?></td></tr>
    <tr><td>Alergi Obat:</td><td><?= isset($last->penduduk_id)?$last->ao:NULL ?></td></tr>
    <tr><td>Alergi Lain</td><td><?= isset($last->penduduk_id)?$last->al:NULL ?></td></tr>
    <tr><td>Dokter Langganan:</td><td><?= isset($last->penduduk_id)?$last->dl:NULL ?></td></tr>
    <tr><td>Merokok:</td><td><?= isset($last->penduduk_id)?$last->mk:NULL ?></td></tr>
    <tr><td>Konsumsi Alkohol:</td><td><?= isset($last->penduduk_id)?$last->ka:NULL ?></td></tr>
</table>
</fieldset>