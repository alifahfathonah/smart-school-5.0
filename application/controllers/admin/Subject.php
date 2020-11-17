<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Subject extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('file');
       
    }

    function index() {
     if (!$this->rbac->hasPrivilege('subject', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu','Academics');
        $this->session->set_userdata('sub_menu','Academics/subject');
        $data['title'] = 'Add subject';
        $subject_result = $this->subject_model->get();

        $data['subjectlist'] = $subject_result;
        $data['subject_types']=$this->customlib->subjectType();
        
        $assignteacherlist = $this->class_model->getClassTeacher();
        $data['assignteacherlist'] = $assignteacherlist;
        foreach ($assignteacherlist as $key => $value) {
            $class_id = $value["class_id"];
            $section_id = $value["section_id"];
            $tlist[] = $this->classteacher_model->teacherByClassSection($class_id, $section_id);
        }
        if(!empty($tlist)){
            $data["tlist"] = $tlist;
        }
        $teacherlist = $this->staff_model->getStaffbyrole($role = 2);
        $data['teacherlist'] = $teacherlist;

        $role_id=0;
        foreach($this->session->userdata("admin") as $key=>$d){
            if($d == 2){
                $role_id = $d;
            }
        }
        
        if($role_id == 2){
            $teacher_id = $this->session->userdata("admin")["id"];
            $active_session_id = count($this->common_model->dbSelect("session_id","sch_settings"," id=1 ")) > 0 ? $this->common_model->dbSelect("session_id","sch_settings"," id=1 ")[0]->session_id : null;
            
            $sql = "SELECT s.id, s.name, s.code, s.type, s.is_active, s.created_at, s.updated_at, concat(st.name,' ',st.surname) as teacher_name, d.teacher_id as teacher_id FROM sh_exam_details d INNER JOIN subjects s ON d.subject_id=s.id INNER JOIN staff st ON st.id=d.teacher_id WHERE d.teacher_id='$teacher_id' AND d.session_id='$active_session_id' AND d.deleted_at IS NULL ";
            $res = $this->common_model->dbQuery($sql);
            
            $newteacherlist = array();
            if(count($res) > 0){    
                foreach($res as $r){
                    array_push($newteacherlist, (array)$r);
                }
            }
            $data["subjectlist"] = $newteacherlist;
        }
        $data["role_id"] = $role_id;
        //echo "<pre/>"; print_r($data[""]); die();
        
        $this->form_validation->set_rules('name', $this->lang->line('subject_name'), 'trim|required|xss_clean|callback__check_name_exists');
        if ($this->input->post('code')) {
            $this->form_validation->set_rules('code', $this->lang->line('code'), 'trim|required|callback__check_code_exists');
        }
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/subject/subjectList', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code')
            );
            $this->subject_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">'.$this->lang->line('success_message').'</div>');
            redirect('admin/subject/index');
        }
    }

    function view($id) {
        if (!$this->rbac->hasPrivilege('subject', 'can_view')) {
            access_denied();
        }
        $data['title'] = 'Subject List';
        $subject = $this->subject_model->get($id);
        $data['subject'] = $subject;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/subject/subjectShow', $data);
        $this->load->view('layout/footer', $data);
    }

    function delete($id) {
        if (!$this->rbac->hasPrivilege('subject', 'can_delete')) {
            access_denied();
        }
        $data['title'] = 'Subject List';
        $this->subject_model->remove($id);
        redirect('admin/subject/index');
    }



    function _check_name_exists() {
        $data['name'] = $this->security->xss_clean($this->input->post('name'));
        if ($this->subject_model->check_data_exists($data)) {
            $this->form_validation->set_message('_check_name_exists', $this->lang->line('name_already_exists'));
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function _check_code_exists() {
        $data['code'] = $this->security->xss_clean($this->input->post('code'));
        if ($this->subject_model->check_code_exists($data)) {
            $this->form_validation->set_message('_check_code_exists', $this->lang->line('code_already_exists'));
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function edit($id) {
        if (!$this->rbac->hasPrivilege('subject', 'can_edit')) {
            access_denied();
        }
        $subject_result = $this->subject_model->get();
        $data['subjectlist'] = $subject_result;
        $data['title'] = 'Edit Subject';
        $data['id'] = $id;
        $subject = $this->subject_model->get($id);
        $data['subject'] = $subject;
        $data['subject_types']=$this->customlib->subjectType();
        $this->form_validation->set_rules('name', $this->lang->line('subject'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/subject/subjectEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'id' => $id,
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
            );
            $this->subject_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">'.$this->lang->line('success_message').'</div>');
            redirect('admin/subject/index');
        }
    }

    function getSubjctByClassandSection() {
        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $date = $this->teachersubject_model->getSubjectByClsandSection($class_id, $section_id);
        echo json_encode($data);
    }

}

?>