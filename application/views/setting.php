<b>Setting Harga Untuk Kategori Obat</b><br/><br/>
<script type="text/javascript">
$(function() {
    $('#simpan').button({
        icons: {
            primary: 'ui-icon-circle-check'
        }
    }).click(function() {
        $('#setting').submit();
    });
    $('#setting').submit(function() {
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.status === true) {
                    alert_edit();
                }
            }
        });
        return false;
    });
});
</script>
<?= form_open('setting/save', 'id=setting') ?>
<table width="100%">
    <tr><td width="15%">HV ( % ):</td><td><?= form_input('hv', isset($set)?$set->hv:0, 'id=hv size=10') ?></td></tr>
    <tr><td>OWA ( % ):</td><td><?= form_input('owa', isset($set)?$set->owa:0, 'id=owa size=10') ?></td></tr>
    <tr><td width="15%">Harga Resep ( % ):</td><td><?= form_input('hresep', isset($set)?$set->h_resep:0, 'id=hresep size=10') ?></td></tr>
    <tr><td></td><td><?= form_button(NULL, 'Simpan Konfigurasi', 'id=simpan') ?></td></tr>
</table>
<?= form_close() ?>