<html>
    <head>
        <title><?= $title ?></title>
        <link rel="shortcut icon" href="<?= base_url('assets/images/fav.ico') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>" />
        <script type="text/javascript" src="<?= base_url('assets/js/jquery-1.8.3.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery-ui-1.9.2.custom.js') ?>"></script>
        <script type="text/javascript">
        $(document).ready(function(){
            $('input').live('keyup', function(e) {
                if (e.keyCode===13) {
                    loginForm();
                }
            });
        });
        
        var tit = document.title;
        var c = 0;
        function writetitle() {
            document.title = tit.substring(0,c);
            if(c===tit.length) {
                c = 0;
                setTimeout("writetitle()", 500);
            }
            else {
                c++;
                setTimeout("writetitle()", 100);
            }
        }
        writetitle();
        
        function loginForm() {
            var Url = '<?= base_url('user/login') ?>';
                $.ajax({
                    type : 'POST',
                    url: Url,               
                    data: $('#loginform').serialize(),
                    cache: false,                        
                    dataType: 'json',
                    beforeSend: function() {
                        $('.loading').fadeIn();
                    },
                    success: function(data) { 
                        location.reload();
                    }
                });
        }
        </script>
        
    </head>
<body class="body-login">
        <div class="logo">&nbsp;</div>
        <div class="informasi">
            <br/>
            <p>Manajemen Operasional Bisnis Apotek:</p>
            <ul>
                <li><img src="<?= base_url('assets/images/icons/stock.png') ?>" align="center" /> Inventory Barang Obat & Non Obat</li>
                <li><img src="<?= base_url('assets/images/icons/stock.png') ?>" align="center" /> Pelayanan Obat</li>
                <li><img src="<?= base_url('assets/images/icons/stock.png') ?>" align="center" /> Medication Therapy Management</li>
                <li><img src="<?= base_url('assets/images/icons/stock.png') ?>" align="center" /> Laporan (Kas, Stok, Hutang, Laba-rugi)</li>
            </ul>
        </div>
        <div class="login-wrapper"> 
            <div class="loading">Loading...</div>
                <div class="introduce">Login</div>
                <?= form_open('', 'id=loginform name=formData') ?>
                    <div class="login-body">
                            <label id="username-label">Username :</label>
                            <div class='loadingbox'><input type="text" name="username" id="username" autocomplete="off" class="inputbox"/></div><br/>
                            <label id="password-label">Password :</label>
                            <div class='loadingbox'><input type="password" name="password" id="password" autocomplete="off" class="inputbox"/></div>
                            <input id="login-button" name="login_button" value="Login" type="button" class="buttonsave" onclick="loginForm();" />
                            <input type="hidden" name="last_link" value="<?=$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']?>" />
                    </div>
                <?= form_close() ?><br/>
                
        </div>
        
    
    </body>
</html>
