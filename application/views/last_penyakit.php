<fieldset>
<table width="100%">
    <tr><td>Tanggal Pemeriksaan:</td><td><?= isset($last->id_penduduk)?indo_tgl($last->tanggal):NULL ?></td></tr>
    <tr><td>RPD:</td><td><?= isset($last->id_penduduk)?$last->rpd:NULL ?></td></tr>
    <tr><td>RPK</td><td><?= isset($last->id_penduduk)?$last->rpk:NULL ?></td></tr>
    <tr><td>Pengobatan Sekarang:</td><td><?= isset($last->id_penduduk)?$last->ps:NULL ?></td></tr>
    <tr><td>Obat Herbal:</td><td><?= isset($last->id_penduduk)?$last->oh:NULL ?></td></tr>
    <tr><td>Alergi Obat:</td><td><?= isset($last->id_penduduk)?$last->ao:NULL ?></td></tr>
    <tr><td>Alergi Lain</td><td><?= isset($last->id_penduduk)?$last->al:NULL ?></td></tr>
    <tr><td>Dokter Langganan:</td><td><?= isset($last->id_penduduk)?$last->dl:NULL ?></td></tr>
    <tr><td>Merokok:</td><td><?= isset($last->id_penduduk)?$last->mk:NULL ?></td></tr>
    <tr><td>Konsumsi Alkohol:</td><td><?= isset($last->id_penduduk)?$last->ka:NULL ?></td></tr>
</table>
</fieldset>