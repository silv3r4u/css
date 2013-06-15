<?php

class Setting extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('m_referensi');
    }
    
    function config() {
        $data['set'] = $this->m_referensi->get_setting()->row();
        $this->load->view('setting', $data);
    }
    
    function save() {
        $data = array(
            'hv' => $this->input->post('hv'),
            'owa' => $this->input->post('owa'),
            'h_resep' => $this->input->post('hresep')
        );
        $cek = $this->db->query("select * from setting")->num_rows();
        if ($cek > 0) {
            $this->db->update('setting',$data);
        } else {
            $this->db->insert('setting',$data);
        }
        die(json_encode(array('status' => TRUE)));
    }
}
?>
