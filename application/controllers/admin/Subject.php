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
            $newteacherlist = array();
            foreach($subject_result as $res){
                if($res['teacher_id'] == $this->session->userdata("admin")["id"]) {
                    array_push($newteacherlist, $res);
                }
            }
            $data["subjectlist"] = $newteacherlist;
        }
        $data["role_id"] = $role_id;
        //echo "<pre/>"; print_r($newteacherlist); die();
        
        $this->form_validation->set_rules('name', $this->lang->line('subject_name'), 'trim|required|xss_clean|callback__check_name_exists');
        $this->form_validation->set_rules('type', $this->lang->line('type'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('teacher_id', $this->lang->line('teacher_id'), 'required|xss_clean');
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
                'code' => $this->input->post('code'),
                'type' => $this->input->post('type'),
                'teacher_id' => $this->input->post('teacher_id')
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
        $this->form_validation->set_rules('teacher_id', $this->lang->line('teacher_id'), 'required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
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
            //echo "<pre/>"; print_r($data["teacherlist"]); die();

            $this->load->view('layout/header', $data);
            $this->load->view('admin/subject/subjectEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'id' => $id,
                'name' => $this->input->post('name'),
                'code' => $this->input->post('code'),
                'type' => $this->input->post('type'),
                'teacher_id' => $this->input->post('teacher_id'),
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