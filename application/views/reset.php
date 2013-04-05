<title><?= $title ?></title>
<?= $this->load->view('message') ?>
<script type="text/javascript">
$(function() {
    $('button[id=reset]').button({
        icons: {
            primary: 'ui-icon-refresh'
        }
    })
    $('#reset').click(function() {
        var ok = confirm('Anda yakin akan me reset data transaksi & data referensi ?');
        if (ok) {
            $.ajax({
                url: '<?= base_url('inisialisasi/delete_data') ?>',
                cache: false,
                dataType: 'json',
                success: function(data) {
                    if (data.status == true) {
                        alert_resets();
                    }
                }
            })
        } else {
            return false;
        }
    })
})
</script>
<div class="kegiatan">
    <h1><?= $title ?></h1>
    <div class="data-input">
        <fieldset>
            Menu ini digunakan untuk menghapus data transaksi & referensi
        </fieldset>
        <?= form_button(NULL, 'Reset Data', 'id=reset') ?>
    </div>
</div>