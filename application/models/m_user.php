<?php

class M_user extends CI_Model {
    
    function cek_login() {
        $query="select p.*, u.username, u.password, un.nama as unit from users u
            join penduduk p on (u.id = p.id)
            join unit un on (un.id = p.unit_id)
        where u.username = '".$this->input->post('username')."' and u.password = '".md5($this->input->post('password'))."'";
        $hasil=$this->db->query($query);
        return $hasil->row();
    }
    
    function module_load_data($id=null) {
        $q = null;
        if ($id != null) {
            $q.="where pp.penduduk_id = '$id' and m.show_desktop = '1'";
        }
        $sql = "select m.* from penduduk_privileges pp
            join privileges p on (pp.privileges_id = p.id)
            join module m on (p.module_id = m.id)
            $q group by p.module_id";
        //echo $sql;
        return $this->db->query($sql);
    }
    
    function menu_user_load_data($id = null) {
        $q = null;
        if ($id != null) {
            $q.="where p.module_id = '$id' and pp.penduduk_id = '".$this->session->userdata('id_user')."' and p.show_desktop = '1'";
        }
        $sql = "select m.*, p.form_nama, p.url, p.icon, p.module_id, p.id as id_privileges 
            from penduduk_privileges pp
            join privileges p on (pp.privileges_id = p.id)
            join module m on (p.module_id = m.id)
            $q order by p.sort";
        //echo $sql;
        return $this->db->query($sql);
    }
}
?>
