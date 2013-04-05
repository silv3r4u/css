<?php

function is_logged_in($destroy = null) {
    //session_start();
    // Get current CodeIgniter instance
    $CI =& get_instance();
    
    $user = $CI->session->userdata('user');
    //$user = isset($_SESSION['id_user'])?$_SESSION['id_user']:'';
    if ($user == '') { redirect(base_url()); } else { return true; }
}
?>