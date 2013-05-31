<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv='expires' content='-1' />
        <meta http-equiv='pragma' content='no-cache' />
        <link rel="shortcut icon" href="<?= base_url('../favicon.ico') ?>" />
        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/workspace.css') ?>" media="all" />
        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/base.css') ?>" media="screen" /> 
        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/jquery-ui-1.9.1.custom.css') ?>" media="all" />
        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/jquery.autocomplete.css') ?>" media="all" />
        <link rel="stylesheet" href="<?= base_url('assets/js/sorter/style.css') ?>" media="all" />
        
        <script type="text/javascript" src="<?= base_url('assets/js/jquery-1.8.3.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery-ui-1.9.2.custom.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery-ui-timepicker-addon.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery.tablesorter.js') ?>"></script>

        <script type="text/javascript" src="<?= base_url('assets/js/jquery.autocomplete.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/library.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/workspace.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery.watermark.js') ?>"></script>


        <script type="text/javascript">
            function ganti_pwd() {
                $('.logoutbutton').toggle();
                $.ajax({
                    type : 'GET',
                    url: '<?= base_url('referensi/ganti_password') ?>',
                    cache: false,
                    success: function(data) {
                        $('#loaddata').html(data);
                    }
                });
            }
            $(function() {
                initMenus();
                $('#loading').hide();
                $('.fixed').fadeOut(15000);
                $("#loading").ajaxStop(function(){$(this).fadeOut();});
                $('a.submenu').click(function() {
                    $('#loaddata').empty();
                    var val = $(this).attr('href');
                    $.ajax({
                        url: val,
                        cache: false,
                        beforeSend: function() {
                            $("#loading").show();
                        },
                        success:function(data) {
                            $('#loaddata').html(data);
                        }
                    });
                    return false;
                });
                $('#hide').click(function() {
                    $('#hide').hide();
                    $('#show').show();
                    $(".menu-detail").hide("slide", { direction: "left" }, 500);
                    $('#loaddata').css('width','100%');
                });
                $('#show').click(function() {
                    $('#show').hide();
                    $('#hide').show();
                    $(".menu-detail").show("slide", { direction: "left" }, 500);
                    $('#loaddata').css('width','80%');
                });
            });

        </script>
        <body>
            <div style="height: 100%">
                <div class="mainribbon-min">
                    <div class="logo-apotek">&nbsp;</div>
                </div>
                <div id="show" style="position: absolute; top: 14%;"><?= img('assets/images/left-arrow.png') ?></div>
                <div id="hide" style="position: absolute; top: 14%;"><?= img('assets/images/right-arrow.png') ?></div>
                <div class="menu-detail">
                    <div class="info-user">
                        <img src="<?= base_url('assets/images/user-aktif.png') ?>" align="left" /> Anda Login Sebagai:<br/> <b><?= $this->session->userdata('nama') ?></b><br/>
                        <?= anchor('user/logout', 'Logout') ?> - <span style="cursor: pointer; color: " onclick="ganti_pwd()">Ganti Password</span>
                    </div>
                    <div>
                        <!-- id="menu3" class="menu noaccordion"  -->
                        <ul id="menu4" class="menu collapsible expandfirst">
                            <?php foreach ($master_menu as $menu) { ?>
                            <li>
                                <a href="#" class="root"><img src="<?= base_url('assets/images/cpanel/'.$menu->icons) ?>" align="left" />&nbsp;<?= $menu->nama ?></a>
                                <ul>
                                    <?php 
                                    $detail_menu = $this->m_user->menu_user_load_data($menu->id)->result();
                                    foreach ($detail_menu as $rows) { ?>
                                        <li><a class="submenu" href="<?= base_url($rows->url) ?>"><?= $rows->form_nama ?></a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <!--            <div class="arrow-left">&nbsp;</div>
                            <div class="arrow-right">&nbsp;</div>-->

                <div id="loading"></div>
                <div id="loaddata">
                    <?php $this->load->view('registrasi') ?>
                </div>
                
            </div>
        </body>
</html>
<noscript><div class="windowsjavascript"><div>Maaf Javascript pada browser anda tidak aktif.<br/>mohon aktifkan untuk menggunakan sistem ini.</div></div></noscript>