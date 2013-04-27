<?php

class M_klinis extends CI_Model {
    
    function save() {
        $data = array(
            'penduduk_id' => $this->input->post('id_penduduk'),
            'tanggal' => date("Y-m-d"),
            'rpd' => $this->input->post('rpd'),
            'rpk' => $this->input->post('rpk'),
            'ps' => $this->input->post('ps'),
            'oh' => $this->input->post('oh'),
            'ao' => $this->input->post('ao'),
            'al' => $this->input->post('al'),
            'dl' => $this->input->post('dl'),
            'mk' => $this->input->post('mk'),
            'ka' => $this->input->post('ka')
        );
        $this->db->insert('riwayat_penyakit', $data);
        
        $data_pemerikasaan = array(
            'penduduk_id' => $this->input->post('id_penduduk'),
            'tanggal' => date("Y-m-d"),
            'subjektif' => $this->input->post('subjektif'),
            'suhu_badan' => $this->input->post('sb'),
            'tek_darah' => $this->input->post('td'),
            'res_rate' => $this->input->post('rr'),
            'nadi' => $this->input->post('nadi'),
            'gds' => $this->input->post('gds'),
            'angka_kolesterol' => $this->input->post('kol'),
            'asam_urat' => $this->input->post('au'),
            'assessment' => $this->input->post('assessment'),
            'goal' => $this->input->post('goal'),
            'saran_pengobatan' => $this->input->post('saran_pengobatan'),
            'saran_non_farmakoterapi' => $this->input->post('saran_non_f')
        );
        $this->db->insert('riwayat_pemeriksaan', $data_pemerikasaan);
        return $status['status'] = TRUE;
    }
}
?>
