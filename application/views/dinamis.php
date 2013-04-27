<div class="data-input" style="display: inline-block">
    <table width="100%">
    <tr valign="top"><td width="50%">
    <label>Subjektif:</label><?= form_textarea('subjektif') ?>
    <label>Objektif:</label>
    <label>&nbsp; &nbsp; Suhu Badan:</label><?= form_input('sb', NULL, 'style="min-width: 5px;"') ?> <span class="label"> <sup>o</sup>C</span>
    <label>&nbsp; &nbsp; Tekanan Darah:</label><?= form_input('td', NULL, 'style="min-width: 5px;"') ?> <span class="label">mmHg</span>
    <label>&nbsp; &nbsp; Respiration Rate:</label><?= form_input('rr', NULL, 'style="min-width: 5px;"') ?> <span class="label">x / menit</span>
    <label>&nbsp; &nbsp; Nadi:</label><?= form_input('nadi', NULL, 'style="min-width: 5px;"') ?> <span class="label">x / menit</span>
    <label>&nbsp; &nbsp; Gula Darah Sewaktu:</label><?= form_input('gds', NULL, 'style="min-width: 5px;"') ?> <span class="label">mg/dL</span>
    <label>&nbsp; &nbsp; Angka Kolesterol Total:</label><?= form_input('kol', NULL, 'style="min-width: 5px;"') ?> <span class="label">mg/dL</span>
        <label>&nbsp; &nbsp; Kadar Asam Urat:</label><?= form_input('au', NULL, 'style="min-width: 5px;"') ?> <span class="label">mg/dL </span>
    <label>Assessment:</label> <?= form_textarea('assessment') ?>
    <label>Goal Terapi:</label> <?= form_textarea('goal') ?>
    <label>Saran Pengobatan:</label> <?= form_textarea('saran_pengobatan') ?>
    <label>Saran Non Farmakoterapi:</label> <?= form_textarea('saran_non_f') ?>
    </td>
    <td width="50%" id="last_pemeriksaan"></td>
    </tr></table>
</div>