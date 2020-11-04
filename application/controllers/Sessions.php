<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sessions extends Admin_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {
        $active_session_id = count($this->common_model->dbSelect("session_id","sch_settings"," id=1 ")) > 0 ? $this->common_model->dbSelect("session_id","sch_settings"," id=1 ")[0]->session_id : null;
        if (!$this->rbac->hasPrivilege('session_setting', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'sessions/index');
        $data['title'] = 'Session List';
        $session_result = $this->session_model->getAllSession();
        //echo "<pre/>"; print_r($session_result); die();
        $terms = $this->common_model->dbSelect("*","sh_result_card_groups"," session_id='$active_session_id' AND deleted_at IS NULL ");
        $data['terms'] = $terms;
        $data['sessionlist'] = $session_result;
        //echo $this->db->last_query();
        //echo "<pre/>"; print_r($data['sessionlist']); die();
        $this->load->view('layout/header', $data);
        $this->load->view('session/sessionList', $data);
        $this->load->view('layout/footer', $data);
    }

    function view($id) {
        if (!$this->rbac->hasPrivilege('session_setting', 'can_view')) {
            access_denied();
        }
        $data['title'] = 'Session List';
        $session = $this->session_model->get($id);
        $data['session'] = $session;
        $this->load->view('layout/header', $data);
        $this->load->view('session/sessionShow', $data);
        $this->load->view('layout/footer', $data);
    }

    function delete($id) {
        if (!$this->rbac->hasPrivilege('session_setting', 'can_delete')) {
            access_denied();
        }
        $this->session->set_flashdata('list_msg', '<div class="alert alert-success text-left">'.$this->lang->line('delete_message').'</div>');
        $this->session_model->remove($id);
        redirect('sessions/index');
    }

    function create() {
        $active_session_id = count($this->common_model->dbSelect("session_id","sch_settings"," id=1 ")) > 0 ? $this->common_model->dbSelect("session_id","sch_settings"," id=1 ")[0]->session_id : null;
        if (!$this->rbac->hasPrivilege('session_setting', 'can_add')) {
            access_denied();
        }
        $session_result = $this->session_model->getAllSession();
        $data['sessionlist'] = $session_result;
        $terms = $this->common_model->dbSelect("*","sh_result_card_groups"," session_id='$active_session_id' AND deleted_at IS NULL ");
        $data['terms'] = $terms;
        $data['title'] = 'Add Session';
        $this->form_validation->set_rules('session', $this->lang->line('session'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/header', $data);
            $this->load->view('session/sessionList', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'session' => $this->input->post('session'),
                'terms' => implode(",",$this->input->post('terms')),
            );
            $this->session_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">'.$this->lang->line('success_message').'</div>');
            redirect('sessions/index');
        }
    }

    function edit($id) {
        $active_session_id = count($this->common_model->dbSelect("session_id","sch_settings"," id=1 ")) > 0 ? $this->common_model->dbSelect("session_id","sch_settings"," id=1 ")[0]->session_id : null;
        if (!$this->rbac->hasPrivilege('session_setting', 'can_edit')) {
            access_denied();
        }
        $session_result = $this->session_model->getAllSession();
        $data['sessionlist'] = $session_result;
        $terms = $this->common_model->dbSelect("*","sh_result_card_groups"," session_id='$active_session_id' AND deleted_at IS NULL ");
        $data['terms'] = $terms;
        $data['title'] = 'Edit Session';
        $data['id'] = $id;
        $session = $this->session_model->get($id);
        $data['session'] = $session;
        $this->form_validation->set_rules('session', $this->lang->line('session'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/header', $data);
            $this->load->view('session/sessionEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'id' => $id,
                'session' => $this->input->post('session'),
                'terms' => implode(",",$this->input->post('terms'))
            );
            
            $this->session_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">'.$this->lang->line('update_message').'</div>');
            redirect('sessions/index');
        }
    }

    function term() {
        $active_session_id = count($this->common_model->dbSelect("session_id","sch_settings"," id=1 ")) > 0 ? $this->common_model->dbSelect("session_id","sch_settings"," id=1 ")[0]->session_id : null;
        if (!$this->rbac->hasPrivilege('session_setting', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'sessions/term');
       
        $result_card_groups = xcrud_get_instance();
        $result_card_groups->table('sh_result_card_groups');
        $result_card_groups->where('deleted_at IS NULL');
        $result_card_groups->where('session_id', $active_session_id);
        $result_card_groups->show_primary_ai_field(false);
        $result_card_groups->columns('name,class_id,batch_id,exam_id');
        $result_card_groups->fields('name,class_id,batch_id,exam_id');
        $result_card_groups->relation('class_id', 'classes', 'id', 'class');
        $result_card_groups->relation('batch_id', 'sh_batches', 'section_id', 'section','','', '', '', '', 'class_id', 'class_id');
        $result_card_groups->relation('exam_id', 'sh_exams', 'id', 'title', '', '', true, '', '', '', '');
        $result_card_groups->label('class_id', lang('class'));
        $result_card_groups->label('batch_id', lang('section'));
        $result_card_groups->label('name', lang('name'));
        $result_card_groups->label('exam_id', lang('assessments'));
        $result_card_groups->replace_remove('soft_delete');
        $result_card_groups->pass_var("sh_result_card_groups.session_id", $active_session_id);
        $result_card_groups->table_name(lang("result_card_groups"));
        $result_card_groups->unset_print();
        $result_card_groups->unset_csv();
        $result_card_groups->unset_title();
        $result_card_groups->unset_limitlist();
        
        $data["result_card_groups"] = $result_card_groups->render();
        $this->load->view('layout/header', $data);
        $this->load->view('session/term', $data);
        $this->load->view('layout/footer', $data);
    }

}

?>