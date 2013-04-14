<html>
    <head>
        <title>CSS - Login</title>
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
        /*"+window.location.hostname+"*/
        function loginForm(){
            
            //$('#login-button').html("<img src='http://"+window.location.hostname+"/simpeg/images/background/loading.gif' alt=''/>Login");
            $('#username-label').html('Checking Username..');
            $('#password-label').html('Checking Password..');
            $('#username-check .loadingbar').fadeIn().animate({'width':'280px'},1000,function(){
              $(this).children('span').fadeIn();
            });
            $('#password-check .loadingbar').delay(500).fadeIn().animate({'width':'280px'},1000,function(){
                $(this).children('span').fadeIn(function(){
                    if ($('#username').val() == '') {
                        $('#username-label').html('Username:');
                        $('#password-label').html('Password:');
                        $('#loading').show().html('Username tidak boleh kosong');
                        $('#username-check .loadingbar,#password-check .loadingbar').fadeOut();
                        $('#username').focus();
                        return false;
                    }
                    if ($('#password').val() == '') {
                        $('#username-label').html('Username:');
                        $('#password-label').html('Password:');
                        $('#loading').show().html('Password tidak boleh kosong');
                        $('#username-check .loadingbar,#password-check .loadingbar').fadeOut();
                        $('#password').focus();
                        return false;
                    }
                    $('#loginform').submit();
                });
            });
        }


        </script>
        
    </head>
<body class="body-login">
        <div class="transparent">&nbsp;</div>
        <div class="base-first">
                <div class="logo">&nbsp;</div>
        </div>

        <div class="login-wrapper">   
            
                <?= form_open('user/login', 'id=loginform name=formData') ?>
                    <div class="login-body">
                        <!--<h1>Login</h1>-->
                        <div id="loading"></div>
                        <div class="logo-key">&nbsp;</div>
                        <div class="login-input">
                            <label id="username-label">Username :</label>
                            <div class='loadingbox' id='username-check'><input type="text" name="username" id="username" class="inputbox"/><div class="loadingbar"><span>Completed!..</span></div></div><br/>
                            <label id="password-label">Password :</label>
                            <div class='loadingbox' id='password-check'><input type="password" name="password" id="password" class="inputbox"/><div class="loadingbar"><span>Completed!..</span></div></div>
                            <input id="login-button" name="login_button" value="Login" type="button" class="buttonsave" onclick="loginForm()" />
                            <input type="hidden" name="last_link" value="<?=$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']?>" />
                        </div>
                    </div>
                <?= form_close() ?>
        </div>

    
    </body>
</html>
