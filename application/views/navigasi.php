<script type="text/javascript" src="<?= base_url('assets/js/jquery.cookies.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $('.logout').click(function() {
            $('.logoutbutton').toggle();
        })
    })
    window.setTimeout("waktu()",1000);  
    function waktu() {   
        var tanggal = new Date();  
        setTimeout("waktu()",1000);  
        document.getElementById("jam").innerHTML = tanggal.getHours()+":"+tanggal.getMinutes()+":"+tanggal.getSeconds();
    }
    
    function ganti_pwd(){
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
</script>
<div class="main-menu-user">
    

    <!--<div class="logout"> <img src="<?= base_url('assets/images/account.png') ?>" align="center" /> <?= $this->session->userdata('nama') ?> ( <?= $this->session->userdata('unit') ?> )<div id="jam" style="color: #fff;"></div></div>-->
    <div class="show-hide" style="display: none">
    <div class="logoutbutton" onclick=location.href="<?= base_url('user/logout') ?>"><img src="<?= base_url('assets/images/logoff.png') ?>" align="center" /> Logout </div>
    <div class="logoutbutton" onclick="ganti_pwd()"><img src="<?= base_url('assets/images/passwd.png') ?>" align="center" /> Ganti Password </div>
    </div>
    <script type="text/javascript">
        $(function() {
            $('.leftmenu').hide();
            $('.'+$.cookie('session')).show();
            //alert($.cookie('session'));
        })
    </script>

</div>

