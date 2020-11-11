<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Subjectgroup extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('subject_group', 'can_view')) {
            access_denied();
        }

        $json_array = array();
        $this->session->set_userdata('top_menu', 'Academics');
        $this->session->set_userdata('sub_menu', 'subjectgroup/index');
        $data['title']         = 'Add Class';
        $data['title_list']    = 'Class List';
        $class                 = $this->class_model->get();
        $data['classlist']     = $class;
        $data['section_array'] = $json_array;

        $res = $this->form_validation->set_rules(
            'name', $this->lang->line('name'), array(
                'required',
                array('class_exists', array($this->subjectgroup_model, 'class_exists')),
            )
        );
        
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('subject[]', $this->lang->line('subject'), 'trim|required|xss_clean');
        $this->form_validation->set_rules(
            'sections[]', $this->lang->line('section'), array(
                'required',
                array('check_section_exists', array($this->subjectgroup_model, 'check_section_exists'))
            )
        );
        
        if (!$this->form_validation->run()) {
            $data['section_array'] = $this->input->post('sections');
        } else {
            $name        = $this->input->post('name');
            $session     = $this->setting_model->getCurrentSession();
            $class_array = array(
                'name'        => $this->input->post('name'),
                'session_id'  => $session,
                'description' => $this->input->post('description'),
            );
            $subject  = $this->input->post('subject');
            $sections = $this->input->post('sections');
            $teachers = $this->input->post("teachers");
            
            $res = $this->subjectgroup_model->add($class_array, $subject, $sections, $teachers);
            if($res){
                $class_id = $this->input->post("class_id");
                $this->insertExamDetails($class_id, $subject, $sections);
            }
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/subjectgroup');
        }
        $subject_list             = $this->subject_model->get();
        $data['subjectlist']      = $subject_list;
        $subjectgroupList         = $this->subjectgroup_model->getByID();
        $data['subjectgroupList'] = $subjectgroupList;
        $data["teachers"] = $this->common_model->dbSelect("*","staff"," employee_id != '' ");
        $this->load->view('layout/header', $data);
        $this->load->view('admin/subjectgroup/subjectgroupList', $data);
        $this->load->view('layout/footer', $data);
    }

    function insertExamDetails($class_id, $subjects, $sections){
        $session_id     = $this->setting_model->getCurrentSession();
        $exams = $this->common_model->dbSelect("*","sh_exams"," session_id='$session_id' ");
        $exam_details_data = array();
        
        if(count($exams) > 0){
            $this->db->where("class_id", $class_id);
            $this->db->where("session_id", $session_id);
            $this->db->delete("sh_exam_details");
            foreach($exams as $ex){
                foreach($sections as $section) {
                    $subject_group_id = $this->common_model->dbSelect("subject_group_id","subject_group_class_sections"," class_section_id='$section' ");
                    if(count($subject_group_id) > 0){
                        $subject_group_id = $subject_group_id[0]->subject_group_id;
                    } else {
                        $subject_group_id = 0;
                    }
                    $section_id = $this->common_model->dbSelect("section_id","class_sections"," id= $section ");
                    if(count($section_id) > 0){
                        $section_id = $section_id[0]->section_id;
                    } else {
                        $section_id = 0;
                    }
                    foreach($subjects as $sub){
                        $exam_details = array(
                            "exam_id" => $ex->id,
                            "class_id" => $class_id,
                            "batch_id" => $section_id,
                            "subject_id" => $sub,
                            "exam_date" => date("Y-m-d"),
                            "start_time" => date("h:i:s"),
                            "end_time" => date("h:i:s"),
                            "total_marks" => 100,
                            "passing_marks" => 40,
                            "session_id" => $session_id,
                            "term_id" => $ex->term_id,
                            "subject_group_id" => $subject_group_id,
                        );
                        $exam_details_data[] = $exam_details;
                    }
                }
            }
        }
        
        $this->db->insert_batch("sh_exam_details",$exam_details_data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('subject_group', 'can_delete')) {
            access_denied();
        }
        $data['title'] = 'Fees Master List';
        $this->subjectgroup_model->remove($id);
        redirect('admin/subjectgroup');
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('subject_group', 'can_edit')) {
            access_denied();
        }
        
        $this->session->set_userdata('top_menu', 'Academics');
        $this->session->set_userdata('sub_menu', 'subjectgroup/index');
        $json_array               = array();
        $old_sections               = array();
        $old_subjects               = array();
        $old_teachers               = array();
        $data['title']            = 'Edit Class';
        $data['id']               = $id;
        $class                    = $this->class_model->get();
        $data['classlist']        = $class;
     
        $subject_list             = $this->subject_model->get();
        $data['subjectlist']      = $subject_list;
        $subjectgroupList         = $this->subjectgroup_model->getByID();
        $data['class_id']         = 0;
        $data['subjectgroupList'] = $subjectgroupList;
        $data["teachers"] = $this->common_model->dbSelect("*","staff"," employee_id != '' ");
        $subjectgroup             = $this->subjectgroup_model->getByID($id);

        if (!empty($subjectgroup[0]->sections)) {
            $data['class_id'] = $subjectgroup[0]->sections[0]->class_id;
            foreach ($subjectgroup[0]->sections as $key => $value) {
                $old_sections[] = ($value->class_section_id);
                $json_array[] = ($value->class_section_id);
            }
        }
        if (!empty($subjectgroup[0]->group_subject)) {
            foreach ($subjectgroup[0]->group_subject as $key => $value) {
                $old_subjects[] = $value->subject_id; 
                $old_teachers[] = $value->teacher_id;
            }
        }    
      
        $data['section_array'] = $json_array;
        $data['subjectgroup'] = $subjectgroup;
        $this->form_validation->set_rules(
            'name', $this->lang->line('name'), array(
                'required',
                array('class_exists', array($this->subjectgroup_model, 'class_exists')),
            )
        );

        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        
        $this->form_validation->set_rules(
                'sections[]', $this->lang->line('section'), array(
            'required',
            array('check_section_exists', array($this->subjectgroup_model, 'check_section_exists'))
                )
        );

        $this->form_validation->set_rules('subject[]', $this->lang->line('subject'), 'trim|required|xss_clean');

        if (!$this->form_validation->run()) {
            if($this->input->server('REQUEST_METHOD') == "POST"){
                $data['section_array'] = $this->input->post('sections');
            }

            foreach($subjectgroupList[0]->group_subject  as $g){
                foreach($data["subjectlist"] as $key=>$sub){
                    if($sub["id"] == $g->subject_id){
                        $data["subjectlist"][$key]["teacher_id"] = $g->teacher_id;
                    }
                }
            }
            $this->load->view('layout/header', $data);
            $this->load->view('admin/subjectgroup/subjectgroupEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $session     = $this->setting_model->getCurrentSession();
            $class_array = array(
                'id'        => $this->input->post('id'),
                'name'        => $this->input->post('name'),                
                'description' => $this->input->post('description'),
            );
            $subject  = $this->input->post('subject');
            $sections = $this->input->post('sections');
            $inputteachers = $this->input->post("teachers");
            $teachers = array();
            foreach($inputteachers as $t){
                if(!empty($t)){
                    array_push($teachers,$t);
                }
            }
            $delete_sections = array_diff($old_sections, $sections);
            $add_sections = array_diff($sections, $old_sections);
            $delete_subjects = array_diff($old_subjects, $subject);
            $add_subjects = array_diff($subject, $old_subjects);
            $delete_teachers = array_diff($old_teachers, $teachers);
            $add_teachers = array_diff($teachers, $old_teachers);
            $this->subjectgroup_model->edit($class_array,$delete_sections, $add_sections, $delete_subjects, $add_subjects, $delete_teachers, $add_teachers);
            $this->insertExamDetails($this->input->post("class_id"), $subject, $sections);
            redirect('admin/subjectgroup');
        }
    }

    
    public function addsubjectgroup()
    {
        $this->form_validation->set_rules('subject_group_id', $this->lang->line('fee_group'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'subject_group_id' => form_error('subject_group_id'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            $student_session_id     = $this->input->post('student_session_id');
            $subject_group_id       = $this->input->post('subject_group_id');
            $student_sesssion_array = isset($student_session_id) ? $student_session_id : array();
            $student_ids            = $this->input->post('student_ids');
            $delete_student         = array_diff($student_ids, $student_sesssion_array);

            $preserve_record = array();
            if (!empty($student_sesssion_array)) {
                foreach ($student_sesssion_array as $key => $value) {

                    $insert_array = array(
                        'student_session_id' => $value,
                        'subject_group_id'   => $subject_group_id,
                    );
                    $inserted_id = $this->studentsubjectgroup_model->add($insert_array);

                    $preserve_record[] = $inserted_id;
                }
            }

            if (!empty($delete_student)) {
                $this->studentsubjectgroup_model->delete($subject_group_id, $delete_student);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }

    public function getGroupByClassandSection()
    {
        $class_id   = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $data       = $this->subjectgroup_model->getGroupByClassandSection($class_id, $section_id);
        echo json_encode($data);
    }

    public function getSubjectByClassandSectionDate()
    {
         

         $date =date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date')));

        $day        = date('l', strtotime($date));
        
        $class_id   = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $data       = $this->subjecttimetable_model->getSubjectByClassandSectionDay($class_id, $section_id, $day);
        echo json_encode($data);
    }

     public function getSubjectByClassandSection()
    {
       
       
        $class_id   = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $data       = $this->subjecttimetable_model->getSubjectByClassandSection($class_id, $section_id);
        echo json_encode($data);
    }

    public function getGroupsubjects()
    {

        $subject_group_id = $this->input->post('subject_group_id');
        $data             = $this->subjectgroup_model->getGroupsubjects($subject_group_id);
        echo json_encode($data);
    }

}
