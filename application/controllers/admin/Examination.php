<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Examination extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->config->load("mailsms");
        $this->load->library('mailsmsconf');
    }

    public function multi_exam_settings(){
        $active_session_id = count($this->common_model->dbSelect("session_id","sch_settings"," id=1 ")) > 0 ? $this->common_model->dbSelect("session_id","sch_settings"," id=1 ")[0]->session_id : null;
        if (!$this->rbac->hasPrivilege('exam_group', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Examinations_New');
        $this->session->set_userdata('sub_menu', 'Examinations_New/multi_exam_settings');
        /*$data['title']      = 'Add Batch';
        $data['title_list'] = 'Recent Batch';
        $data['examType']   = $this->exam_type;*/

        $params = $this->input->get();
        $data["selected_tab"] = "";
        $data["tab_cumulative_settings_selected_session_id"] = "";
        $data["tab_cumulative_settings_selected_class_id"] = "all";
        $data["tab_cumulative_settings_selected_batch_id"] = "all";

        $cumulative_settings = xcrud_get_instance();
        $cumulative_settings->table('sh_complete_result_card_settings');
        $cumulative_settings->where('deleted_at IS NULL');
        $cumulative_settings->where('session_id',$active_session_id);
        $cumulative_settings->show_primary_ai_field(false);
        $cumulative_settings->columns('name,class_id,batch_id,exam_result_card_id,last_exam_group_id');
        $cumulative_settings->fields('name,class_id,batch_id,exam_result_card_id,last_exam_group_id');
        $cumulative_settings->relation('class_id', 'classes', 'id', 'class');
        $cumulative_settings->relation('batch_id', 'sh_batches', 'section_id', 'section','','', '', '', '', 'class_id', 'class_id');
        $cumulative_settings->relation('exam_result_card_id', 'sh_result_card_groups', 'id', 'name', '', '', true, '', '', 'class_id', 'class_id');
        $cumulative_settings->relation('last_exam_group_id', 'sh_result_card_groups', 'id', 'name','','',false,'','','class_id', 'class_id');
        $cumulative_settings->label('class_id', lang('class'));
        $cumulative_settings->label('batch_id', lang('section'));
        $cumulative_settings->label('name', lang('name'));
        $cumulative_settings->label('exam_result_card_id', lang('result_card_groups'));
        $cumulative_settings->label('last_exam_group_id', lang('current_term'));
        $cumulative_settings->replace_remove('soft_delete');
        $cumulative_settings->table_name(lang("cumulative_settings"));
        $cumulative_settings->unset_print();
        $cumulative_settings->unset_csv();
        $cumulative_settings->unset_title();
        $cumulative_settings->unset_limitlist();
        $cumulative_settings->pass_var('sh_complete_result_card_settings.session_id', $active_session_id);

        $data["cumulative_settings"] = $cumulative_settings->render();
        $this->load->view('layout/header', $data);
        $this->load->view('admin/yexam/multi_exam_settings', $data);
        $this->load->view('layout/footer', $data);
    }

    public function single_exam_settings() {
        
        $active_session_id = count($this->common_model->dbSelect("session_id","sch_settings"," id=1 ")) > 0 ? $this->common_model->dbSelect("session_id","sch_settings"," id=1 ")[0]->session_id : null;
        if (!$this->rbac->hasPrivilege('exam_group', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Examinations_New');
        $this->session->set_userdata('sub_menu', 'Examinations_New/single_exam_settings');
        /*$data['title']      = 'Add Batch';
        $data['title_list'] = 'Recent Batch';
        $data['examType']   = $this->exam_type;*/

        $params = $this->input->get();
        $data["selected_tab"] = "";
        $data["tab_subjects_selected_class_id"] = "all";
        $data["tab_subjects_selected_batch_id"] = "all";
        $data["tab_rules_selected_class_id"] = "all";
        $data["tab_rules_selected_batch_id"] = "all";
        $data["tab_subject_groups_class_id"] = "all";
        $data["tab_subject_groups_batch_id"] = "all";
        $data["tab_subjects_selected_session_id"] = "";
        $data["tab_rules_selected_session_id"] = "";
        $data["tab_result_card_groups_selected_session_id"] = "";
        $data["tab_result_card_groups_selected_class_id"] = "all";
        $data["tab_result_card_groups_selected_batch_id"] = "all";

        $exams = xcrud_get_instance();
        $exams->table('sh_exams');
        $exams->where('deleted_at IS NULL');
        $exams->where('session_id', $active_session_id);
        $exams->show_primary_ai_field(false);
        $exams->columns('session_id, term_id, title,start_date,end_date,total_marks,passing_marks');
        $exams->fields('session_id, term_id, title,start_date,end_date,total_marks,passing_marks');
        $exams->relation('session_id', 'sessions', 'id', 'session','','', '', '', '', '', '');
        $exams->relation('term_id', 'sh_result_card_groups', 'id', 'name','','', '', '', '', 'session_id', 'session_id');
        $exams->label('session_id', lang('session'));
        $exams->label('term_id', lang('term'));
        $exams->label('title', $this->lang->line('assessment_name'));
        $exams->label('start_date', $this->lang->line('lbl_start_date'));
        $exams->label('end_date', $this->lang->line('lbl_end_date'));
        $exams->label('total_marks', $this->lang->line('total_marks'));
        $exams->label('passing_marks', $this->lang->line('passing_marks'));
        $exams->unset_print();
        $exams->before_insert('check_exam_session');
        $exams->before_update('check_exam_session_update');
        $exams->replace_remove('soft_delete');
        $exams->unset_csv();
        $exams->unset_title();
        $exams->unset_search();
        $exams->unset_limitlist();
        $exams->pass_var("sh_exams.session_id", $active_session_id);
        $exams->table_name('lbl_exam_session');
        
        $exam_details = xcrud_get_instance();
        $exam_details->table('sh_exam_details');
        $exam_details->where('deleted_at IS NULL');
        $exam_details->where('session_id', $active_session_id);
        $exam_details->columns('exam_id,class_id,batch_id,subject_group_id,subject_id, teacher_id');
        $exam_details->fields('exam_id,class_id,batch_id,subject_group_id,subject_id, teacher_id');
        $exam_details->relation('exam_id', 'sh_exams', 'id', 'title',"deleted_at IS NULL AND session_id='$active_session_id'",'', true, '', '', '', '');
        $exam_details->relation('class_id', 'classes', 'id', 'class');
        $exam_details->relation('teacher_id', 'staff', 'id', array('name', 'surname'),"employee_id!=''",'','',' ');
        $exam_details->relation('batch_id', 'sh_batches', 'section_id', 'section','','', true, '', '', 'class_id', 'class_id');
        $exam_details->relation('subject_id', 'sh_subjects_with_group', 'subject_id', 'name','','', true, '', '', 'subject_group_id', 'subject_group_id');
        $exam_details->relation('subject_group_id', 'sh_subject_groups', 'id', 'group_name', "deleted_at IS NULL AND session_id='$active_session_id'", '', false, '', '', 'class_id', 'class_id');
        $exam_details->label('teacher_id', $this->lang->line('teacher_id'));
        $exam_details->label('exam_id', $this->lang->line('assessments'));
        $exam_details->label('class_id', $this->lang->line('class'));
        $exam_details->label('batch_id', $this->lang->line('section'));
        $exam_details->label('subject_id', $this->lang->line('subject'));
        $exam_details->label('total_marks', $this->lang->line('total_marks'));
        $exam_details->label('passing_marks', $this->lang->line('passing_marks'));
        $exam_details->label('subject_group_id', $this->lang->line('subject_group'));
        $exam_details->unset_print();
        $exam_details->replace_remove('soft_delete');
        $exam_details->replace_insert('custom_exam_details');
        $exam_details->unset_csv();
        $exam_details->unset_title();
        $exam_details->unset_limitlist();
        $exam_details->table_name('lbl_exam_details');
        $exam_details->pass_var("sh_exam_details.session_id", $active_session_id);
        $exam_details->replace_update('check_exam_details_update');

        $passing_rules = xcrud_get_instance();
        $passing_rules->table('sh_passing_rules');
        $passing_rules->where('deleted_at IS NULL');
        $passing_rules->where('session_id', $active_session_id);
        $passing_rules->columns('exam_id,class_id,subject_group_id,subjects_which_passed,operator,minimum_percentage');
        $passing_rules->fields('exam_id,class_id,subject_group_id,operator,subjects_which_passed,minimum_percentage');
        $passing_rules->relation('exam_id', 'sh_exams', 'id', 'title');
        $passing_rules->relation('class_id', 'classes', 'id', 'class');
        $passing_rules->relation('subject_group_id', 'sh_subject_groups', 'id', 'group_name', '', '', false, '', '', 'class_id', 'class_id');
        $passing_rules->relation('subjects_which_passed', 'sh_subjects_with_group', 'subject_id', 'name','','', true, '', '', 'subject_group_id', 'subject_group_id');
        $passing_rules->label('exam_id', $this->lang->line('assessments'));
        $passing_rules->label('class_id', $this->lang->line('class'));
        $passing_rules->label('subjects_which_passed', $this->lang->line('subjects'));
        $passing_rules->label('subject_group_id', $this->lang->line('subject_group_name'));
        $passing_rules->label('operator', $this->lang->line('operator_rule'));
        $passing_rules->label('minimum_subjects', $this->lang->line('minimum_subjects_pass'));
        $passing_rules->label('minimum_percentage', $this->lang->line('minimum_percentage'));
        $passing_rules->unset_print();
        $passing_rules->replace_remove('soft_delete');
        $passing_rules->unset_csv();
        $passing_rules->unset_limitlist();
        $passing_rules->unset_title();
        $passing_rules->pass_var("sh_passing_rules.session_id", $active_session_id);


        $cum_passing_rules = xcrud_get_instance();
        $cum_passing_rules->table('sh_cumulative_passing_rules');
        $cum_passing_rules->where('deleted_at IS NULL');
        $cum_passing_rules->columns('class_id,result_card_group_id,subject_group_id,subjects_which_passed,operator,minimum_percentage,remarks');
        $cum_passing_rules->fields('class_id,result_card_group_id,subject_group_id,operator,subjects_which_passed,minimum_percentage,remarks');
        $cum_passing_rules->relation('result_card_group_id', 'sh_result_card_groups', 'id', 'name', '', '', false, '', '', 'class_id', 'class_id');
        $cum_passing_rules->relation('class_id', 'classes', 'id', 'class');
        $cum_passing_rules->relation('subject_group_id', 'sh_subject_groups', 'id', 'group_name', '', '', false, '', '', 'class_id', 'class_id');
        $cum_passing_rules->relation('subjects_which_passed', 'sh_subjects_with_group', 'subject_id', 'name','','', true, '', '', 'subject_group_id', 'subject_group_id');
        $cum_passing_rules->label('remarks', $this->lang->line('admin_remarks'));
        $cum_passing_rules->label('result_card_group_id', $this->lang->line('terms'));
        $cum_passing_rules->label('class_id', $this->lang->line('class'));
        $cum_passing_rules->label('subjects_which_passed', $this->lang->line('subjects'));
        $cum_passing_rules->label('subject_group_id', $this->lang->line('subject_group_name'));
        $cum_passing_rules->label('operator', $this->lang->line('operator_rule'));
        $cum_passing_rules->label('minimum_subjects', $this->lang->line('minimum_subjects_pass'));
        $cum_passing_rules->label('minimum_percentage', $this->lang->line('minimum_percentage'));
        $cum_passing_rules->unset_print();
        $cum_passing_rules->replace_remove('soft_delete');
        $cum_passing_rules->unset_csv();
        $cum_passing_rules->unset_limitlist();
        $cum_passing_rules->unset_title();


        $grades = xcrud_get_instance();
        $grades->table('sh_grades');
        $grades->where('deleted_at IS NULL');
        $grades->where('session_id', $active_session_id);
        $grades->show_primary_ai_field(false);
        $grades->relation('class_id', 'classes', 'id', 'class');
        $grades->columns('name,class_id,percent_from, percent_upto, color');
        $grades->fields('name,class_id, percent_from, percent_upto, color');
        $grades->change_type('color','text','#000000', array('id'=>"colorpicker"));
        $grades->label('color', lang('color'));
        $grades->label('name', lang('lbl_grade_name'));
        $grades->label('percent_from', lang('lbl_percent_from'));
        $grades->label('percent_upto', lang('lbl_percent_upto'));
        $grades->label('class_id', lang('class'));
        $grades->order_by('percent_from', 'desc');
        $grades->table_name(lang('lbl_grades'));
        $grades->before_insert('checkValidation');
        $grades->before_update('checkValidationUpdate');
        $grades->unset_print();
        $grades->unset_csv();
        $grades->unset_title();
        $grades->unset_limitlist();
        $grades->pass_var("sh_grades.session_id", $active_session_id);
        

        $subject_groups = xcrud_get_instance();
        $subject_groups->table('sh_subject_groups');
        $subject_groups->where('deleted_at IS NULL');
        $subject_groups->where('session_id', $active_session_id);
        $subject_groups->show_primary_ai_field(false);
        $subject_groups->columns('group_name,class_id,batch_id,subjects');
        $subject_groups->fields('group_name,class_id,batch_id,subjects');
        $subject_groups->relation('class_id', 'classes', 'id', 'class');
        $subject_groups->relation('batch_id', 'sh_batches', 'section_id', 'section','','', true, '', '', 'class_id', 'class_id');
        $subject_groups->relation('subjects', 'subjects', 'id', 'name','','', true, '', '', '', '');
        $subject_groups->label('class_id', lang('class'));
        $subject_groups->label('batch_id', lang('section'));
        $subject_groups->label('group_name', lang('name'));
        $subject_groups->label('subjects', lang('subjects'));
        $subject_groups->before_insert('new_subject_group');
        $subject_groups->table_name(lang('subject_groups'));
        $subject_groups->pass_var('sh_subject_groups.session_id', $active_session_id);
        $subject_groups->unset_print();
        $subject_groups->unset_csv();
        $subject_groups->unset_title();
        $subject_groups->unset_limitlist();


        $data["sessions"] = $this->common_model->dbSelect("*","sessions"," 1 ");

        $data["subject_groups"] = $subject_groups->render();
        $data["exam_details"] = $exam_details->render();
        $data["passing_rules"] = $passing_rules->render();
        $data["cum_passing_rules"] = $cum_passing_rules->render();
        $data["grades"] = $grades->render();
        $data["exams_new"] = $exams->render();
        $data["active_session_id"] = $active_session_id;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/yexam/single_exam_settings', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getFinalResultCardGroups(){
        $data = $this->common_model->dbSelect("*","sh_result_card_groups"," 1 ");
        echo json_encode($data);
    }

    public function getExamsByID(){
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $sql = "SELECT e.*,fe.weightage FROM sh_exams e left join sh_final_exam_weightage fe ON e.id=fe.exam_id WHERE e.id IN ($request->exam_ids) ";
        $data = $this->common_model->dbQuery($sql);
        if(count($data) > 0) {
            foreach($data as $d){
                $d->weightage = intval($d->weightage);
            }
        }
        //$data = $this->common_model->dbSelect("*","sh_exams"," id IN ($request->exam_ids) ");
        echo json_encode($data);
    }

    public function saveFinalWeightage(){
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        foreach($request->exams as $e){
            $data = array("result_card_group_id"=>$request->group_id, "exam_id"=>$e->exam_id, "weightage"=>$e->weightage);
            $this->db->replace('sh_final_exam_weightage', $data);
        }
        echo json_encode(array("status"=>"success","message"=>"Reult card weightage updated successfully!"));
    }

    public function resultcard(){
        $this->session->set_userdata('top_menu', 'Examinations_New');
        $this->session->set_userdata('sub_menu', 'Examinations_New/resultcard');
        $sql = "SELECT t.*, c.class as class_name FROM sh_templates t INNER JOIN classes c ON t.class_id=c.id AND t.deleted_at IS NULL ";
        $data["templates"] = $this->common_model->dbQuery($sql);
        $this->load->view('layout/header', $data);
        $this->load->view("admin/yforms/view",$data);
        $this->load->view('layout/footer', $data); 
    }

    public function showResultCard(){
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $template = $this->common_model->dbSelect("*","sh_templates"," id=$request->id AND deleted_at IS NULL ");
        if(count($template) > 0){
            $data["template"] = $template[0];
        }
        echo json_encode($data);
        //$this->load->view('layout/header', $data);
        //$this->load->view("admin/yforms/show",$data);
        //$this->load->view('layout/footer', $data);
    }

    public function editResultCard($id){
        $template = $this->common_model->dbSelect("*","sh_templates"," id=$id AND deleted_at IS NULL ");
        $data = array();
        if(count($template) > 0){
            $data["template"] = $template[0];
        }
        $this->load->view('layout/header', $data);
        $this->load->view("admin/yforms/edit",$data);
        $this->load->view('layout/footer', $data);
    }

    public function saveResultCard(){
        $title = $this->input->post("title");
        $description = $this->input->post("description");
        $is_default = $this->input->post("is_default");

        if($is_default?true:false){
            $sql = "UPDATE sh_templates set is_default='No' WHERE deleted_at IS NULL ";
            $this->db->query($sql);
            $data = array("name"=>$title, "html"=>$description,"is_default"=>'Yes');
            $this->common_model->dbInsert("sh_templates",$data);
        } else {
            $data = array("name"=>$title, "html"=>$description,"is_default"=>'No');
            $this->common_model->dbInsert("sh_templates",$data);
        }
        $response = array("status"=>"success","message"=>"New result card added successfully!");
        echo json_encode($response);
    }

    public function marks() {
        $this->load->view("admin/yexam/marksheet");
    }

    public function markexam(){
        $data = array();
        $this->session->set_userdata('top_menu', 'Examinations_New');
        $this->session->set_userdata('sub_menu', 'Examinations_New/markexam');

        $data["role"] = "";
        //echo "<pre/>"; print_r($this->session->userdata("admin")); die();
        foreach($this->session->userdata("admin")["roles"] as $key=>$a){
            $data["role_id"] = $a;
            $data["role"] = $key;
        }
        $this->load->view('layout/header', $data);
        $this->load->view("admin/yexam/marksheet",$data);
        $this->load->view('layout/footer', $data);
    }

    public function getSessions(){
        $data = $this->common_model->dbSelect("*","sessions"," 1 ");
        echo json_encode($data);
    }

    public function getSessions2(){
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        
        if($request->role_name == "Admin" || $request->role_name == "Super Admin") {
            $data = $this->common_model->dbSelect("*","sessions"," 1 ");
        } else {
            $session_id = $this->common_model->dbSelect("session_id","sch_settings"," 1 ")[0]->session_id;
            $data = $this->common_model->dbSelect("*","sessions"," id='$session_id' ");
        }
        echo json_encode($data);
    }

    function getSubjects4rMarksheet() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $active_session_id = count($this->common_model->dbSelect("session_id","sch_settings"," id=1 ")) > 0 ? $this->common_model->dbSelect("session_id","sch_settings"," id=1 ")[0]->session_id : null;
        $subject_group_id = $data = $this->common_model->dbSelect("id", "sh_subject_groups", " class_id='$request->class_id' AND FIND_IN_SET('$request->batch_id',batch_id) OR session_id='$request->session_id' AND deleted_at IS NULL ");
        
        if(count($subject_group_id) > 0){
            $subject_group_id = $subject_group_id[0]->id;
            $sql = "SELECT s.* FROM sh_subjects_with_group sg INNER JOIN subjects s ON sg.subject_id=s.id WHERE subject_group_id='$subject_group_id'";
            $subjects = $this->common_model->dbQuery($sql);

            $role_id=0;
            foreach($this->session->userdata("admin") as $key=>$d){
                if($d == 2){
                    $role_id = $d;
                }
            }
            if($role_id == 2){
                $newsubjects = array();
                $teacher_id = $this->session->userdata("admin")["id"];
                $sql = "SELECT s.id, s.name, s.code, s.type, s.is_active, s.created_at, s.updated_at, concat(st.name,' ',st.surname) as teacher_name, d.teacher_id as teacher_id FROM sh_exam_details d INNER JOIN subjects s ON d.subject_id=s.id INNER JOIN staff st ON st.id=d.teacher_id WHERE d.teacher_id='$teacher_id' AND d.session_id='$active_session_id' AND d.deleted_at IS NULL ";
                $res = $this->common_model->dbQuery($sql);
                
                $assigned_subjects = array();
                if(count($res) > 0){    
                    foreach($res as $r){
                        array_push($assigned_subjects, (array)$r);
                    }
                }
                $subjects = $assigned_subjects;
            }
            $response = array("status"=>"error","message"=>"no data found", "data"=>$subjects);
        } else {
            $response = array("status"=>"error","message"=>"no data found", "data"=>array());
        }

        echo json_encode($response);
    }

    function getSubjects() {
        $data = $this->common_model->dbSelect("*", "subjects", " 1 ");
        echo json_encode($data);
    }

    function getExams() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $sql = "SELECT * FROM sh_exams e "
            . "LEFT JOIN sh_exam_details d ON e.id=d.exam_id "
            . "WHERE "
            . "d.class_id='$request->class_id' "
            . "AND d.batch_id='$request->batch_id' "
            . "AND d.subject_id='$request->subject_id' "
            . "AND e.deleted_at IS NULL "
            . "AND d.deleted_at IS NULL ";
        $data = $this->common_model->dbQuery($sql);
        echo json_encode($data);
    }

    function getSchoolExams() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        //$active_academic_year = $request->academic_year_id;
        $sql = "SELECT * FROM sh_exams WHERE deleted_at IS NULL ";
        $data = $this->common_model->dbQuery($sql);
        echo json_encode($data);
    }

    public function fetchStudents() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        
        $class_id = $request->class_id;
        $batch_id = $request->batch_id;
        $subject_id = $request->subject_id;
        $exam_detail_id = $request->exam_detail_id;
        $session_id = $request->academic_year_id;
        
        $where = " class_id=" . $class_id . " AND batch_id=" . $batch_id . " AND exam_detail_id=" . $exam_detail_id . " AND deleted_at IS NULL ";
        $exists = $this->common_model->dbSelect("*", "sh_marksheets", $where);
        $passing_marks = 'NULL';
        $exam_total_marks = 'NULL';
        $exam_type = 'NULL';
        $res = $this->common_model->dbSelect("*", "sh_exam_details", " id='$exam_detail_id' AND deleted_at IS NULL ");
        if (count($res) > 0) {
            $passing_marks = $res[0]->passing_marks;
            $exam_total_marks = $res[0]->total_marks;
            $exam_type = $res[0]->type;
        }
        $sql = '';
        if (count($exists) > 0) {
            $data["message"] = lang('marksheet_exists');
            $data["exist"] = true;
            
            $sql = "SELECT 
                students.id, 
                concat(students.firstname,' ', students.lastname) as name, 
                students.image as student_avatar, 
                students.roll_no as rollno,
                (SELECT class_id FROM student_session WHERE student_id=students.id) as class_id,
                (SELECT section_id FROM student_session WHERE student_id=students.id) as section_id,
                sh_marksheets.status,
                sh_marksheets.obtained_marks,
                sh_marksheets.grade,
                sh_marksheets.remarks
                FROM students 
                LEFT JOIN sh_marksheets ON students.id=sh_marksheets.student_id AND sh_marksheets.exam_detail_id='$exam_detail_id' 
                WHERE 
                students.id IN (SELECT student_id FROM student_session WHERE class_id='$class_id' AND section_id='$batch_id' AND session_id='$session_id')";
            
            $students = $this->common_model->dbQuery($sql);
            foreach ($students as $value) {
                $value->is_mark_exists = "false";
                if ($value->obtained_marks != null) {
                    $value->obtained_marks = (int) $value->obtained_marks;
                    $value->is_mark_exists = "true";
                }
            }
        } else {
            $sql = "SELECT 
                students.id, 
                concat(students.firstname,' ', students.lastname) as name, 
                students.image as student_avatar, 
                students.roll_no as rollno,
                (SELECT class_id FROM student_session WHERE student_id=students.id) as class_id,
                (SELECT section_id FROM student_session WHERE student_id=students.id) as section_id
                FROM students 
                WHERE 
                students.id IN (SELECT student_id FROM student_session WHERE class_id='$class_id' AND section_id='$batch_id' AND session_id='$session_id')";
            $data["message"] = "";
            $data["exist"] = false;
            $students = $this->common_model->dbQuery($sql);
            foreach ($students as $value) {
                $value->is_mark_exists = "false";
                $value->obtained_marks = null;
                $value->grade = null;
            }
        }
        
        $exam_id = $res[0]->exam_id;
        $exam = $this->common_model->dbSelect('title', 'sh_exams', "id='$exam_id'");
        $class = $this->common_model->dbSelect('class', 'classes', "id='$class_id'");
        $batch = $this->common_model->dbSelect('section', 'sh_batches', " section_id=' $batch_id' AND class_id='$class_id' ");

        $data["exam_name"] = $exam[0]->title;
        $data["class_name"] = $class[0]->class;
        $data["batch_name"] = $batch[0]->section;
        $data['total_marks'] = $res[0]->total_marks;
        $data['exam_date'] = $res[0]->exam_date;
        $data['start_time'] = $res[0]->start_time;
        $data['end_time'] = $res[0]->start_time;

        $data["students"] = $students;
        $data["passing_marks"] = $passing_marks;
        $data["exam_type"] = $exam_type;
        echo json_encode($data);
    }

    public function inProcessMarkSheet() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        $class_id = $request->class_id;
        $batch_id = $request->batch_id;
        $subject_id = $request->subject_id;
        $exam_detail_id = $request->exam_detail_id;
        $school_id = $this->session->userdata('userdata')['sh_id'];

        $mark_sheetExist = $this->common_model->dbSelect("*", "request_log", "class_id= " . $class_id . " AND batch_id=" . $batch_id . " AND school_id=" . $school_id . " AND subject_id=" . $subject_id . " AND exam_detail_id=" . $exam_detail_id . "  and marked = 'N'");
        if (count($mark_sheetExist) > 0) {
            //request found
            $r_id = $mark_sheetExist[0]->id;
            $sql1 = "UPDATE sh_request_log SET status = 'inprocess', request_time = '" . date('Y-m-d H:i:s') . "', edit_reason= '" . $request->reason . "' WHERE  class_id= " . $class_id . " AND batch_id=" . $batch_id . " AND school_id=" . $school_id . " AND subject_id=" . $subject_id . " AND exam_detail_id=" . $exam_detail_id . "  and marked = 'N'";
            $result = $this->common_model->query($sql1);
            $data['edit'] = 'inprocess';
            $data["message"] = lang('edit_attendance');
            $data["status"] = 'success';
            $data["disable"] = "TRUE";
            $data["r_id"] = $r_id;
        } else {
            //request not found
            // $record = array(
            //     "user_id" => $this->session->userdata("userdata")["user_id"],
            //     "school_id" => $school_id,
            //     "type" => 'mark_sheet',
            //     "class_id" => $class_id,
            //     "batch_id" => $batch_id,
            //     "subject_id" => $subject_id,
            //     "exam_detail_id" => $exam_detail_id,
            //     "status" => 'inprocess',
            //     "edit_reason" => $request->reason
            // );
            // $r_id = $this->common_model->dbInsert("request_log", $record);
            $data["r_id"] = $r_id;
            $data["message"] = lang('edit_not_admin');
            $data["status"] = 'error';
            $data["disable"] = "TRUE";
        }
        echo json_encode($data);
    }

    public function saveMarks() {
        $postdata = file_get_contents("php://input");
        $data = json_decode($postdata);
        
        $response = array();
        $sql = "Replace INTO sh_marksheets (`exam_id`,`exam_detail_id`, `obtained_marks`, `grade`, `status`,`student_id`, `class_id`, `batch_id`, `remarks`) VALUES ";
        $values = "";
        foreach ($data as $val) {
            $status = "Pass";
            if(!is_null($val->obtain_marks)) {
                if($val->obtain_marks < $val->passing_marks){
                    $status = "Fail";
                }
            }
            $values .= "(" . $val->exam_id . "," . $val->exam_detail_id . ",'" . $val->obtain_marks . "','".$val->grade."', '" .$status. "',". $val->id . "," . $val->class_id . "," . $val->batch_id . ",'". $val->remarks ."'),";
        }
        $sql = $sql . rtrim($values, ",");
        $res = $this->db->query($sql);
        
        if ($res) {
            $response = array("status" => "success", "message" => "Marksheet save successfully.");
        }
        echo json_encode($response);
    }


    public function majorSheet() {
        $data = array();
        $this->session->set_userdata('top_menu', 'Examinations_New');
        $this->session->set_userdata('sub_menu', 'Examinations_New/majorSheet');

        $data["role"] = "";
        //echo "<pre/>"; print_r($this->session->userdata("admin")); die();
        foreach($this->session->userdata("admin")["roles"] as $key=>$a){
            $data["role_id"] = $a;
            $data["role"] = $key;
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/yexam/majorsheet', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getStudentsForMajorSheet() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $data = array();
        
        if($request->result_type == 'single'){
            $data = $this->getSingleResultCard($request);
        } else if($request->result_type == 'multiple'){
            $data = $this->getCompleteResultCard($request);
        }

        echo json_encode($data);
    }

    public function getCompleteResultCard($request) {
        $active_academic_year = $request->academic_year_id;
        $session_name = $this->common_model->dbSelect("session","sessions"," id='$active_academic_year' ")[0]->session;
        $exam_name = "";
        $result_card_exam_groups = $this->common_model->dbSelect("*","sh_complete_result_card_settings"," id='$request->result_card_group_id' ");
        $terms = array();
        $last_term_id=0;
        if(count($result_card_exam_groups) > 0){
            $temp = $result_card_exam_groups[0]->exam_result_card_id;
            $last_term_id = $result_card_exam_groups[0]->last_exam_group_id;
            $terms = explode(",", $temp);
        }

        foreach($terms as $ty) {
            $data = array();
            $students = array();
            $sql = "SELECT 
                s.id as student_id, 
                concat(s.firstname,' ', s.lastname) as student_name, 
                s.gender as student_gender,
                s.admission_no,
                s.dob as student_dob, 
                s.image as student_avatar, 
                s.roll_no as rollno,
                s.father_name, 
                ss.class_id, 
                ss.section_id 
                FROM 
                    student_session ss LEFT JOIN students s ON ss.student_id=s.id 
                WHERE 
                    ss.class_id='$request->class_id' 
                    AND ss.section_id='$request->batch_id' 
                    OR ss.session_id='$active_academic_year' ";
            $students = $this->common_model->dbQuery($sql);

            $subjects = array();
            $subject_group_id = $this->common_model->dbSelect("id","sh_subject_groups"," class_id='$request->class_id' AND FIND_IN_SET('$request->batch_id', batch_id) OR session_id='$active_academic_year' AND deleted_at IS NULL ");
            if(count($subject_group_id) > 0){
                $subject_group_id = $subject_group_id[0]->id;
                $sql = "SELECT s.* FROM sh_subjects_with_group sg INNER JOIN subjects s ON sg.subject_id=s.id WHERE subject_group_id='$subject_group_id'";
                $subjects = $this->common_model->dbQuery($sql);
            }

            $term = $this->common_model->dbSelect("*","sh_result_card_groups"," id='$ty' ");
            $term_name = null;
            $exam_groups = 0;
            if(count($term) > 0){
                $term = $term[0];
                $term_name = $term->name;
                $exam_groups = explode(",",$term->exam_id);
            }
            
            $exam_detail_ids = array();
            $sql = "SELECT 
                e.id as exam_id,
                e.title as title, 
                ed.id as exam_detail_id, 
                e.title as examname, 
                ed.subject_id, 
                ed.type, 
                ed.total_marks, 
                ed.passing_marks,
                ed.start_time, 
                ed.end_time, 
                ed.exam_date, 
                ed.class_id, 
                ed.batch_id 
                FROM 
                    sh_exams e LEFT JOIN sh_exam_details ed ON e.id=ed.exam_id AND ed.class_id='$request->class_id' AND ed.batch_id='$request->batch_id'
                WHERE 
                    e.deleted_at IS NULL 
                    AND ed.deleted_at IS NULL 
                    AND e.id IN (".implode(',',$exam_groups).")
                    OR e.session_id='$active_academic_year' ";
            
            $exam_details = $this->common_model->dbQuery($sql);
            if (count($exam_details) > 0) {
                foreach ($subjects as $key => $s) {
                    $s->exams = array();
                    $exams = array();
                    foreach ($exam_details as $exam) {
                        if ($s->id == $exam->subject_id && in_array($exam->exam_id, $exam_groups)) {
                            $array = (object) array(
                                'exam_id' => $exam->exam_id,
                                'exam_name' => $exam->title,
                                'exam_detail_id' => $exam->exam_detail_id,
                                'total_marks' => $exam->total_marks,
                                'passing_marks' => $exam->passing_marks,
                                'type' => $exam->type,
                                'start_time' => $exam->start_time,
                                'end_time' => $exam->end_time,
                                'exam_date' => $exam->exam_date,
                                'obtained_marks' => NULL,
                                'marksheet_status' => NULL,
                                'grade' => NULL
                            );
                            array_push($exam_detail_ids, $exam->exam_detail_id);
                            $exams[$exam->exam_id] = $array;
                        } else if(is_null($exam->exam_detail_id)){
                            $array = (object) array(
                                'exam_id' => $exam->exam_id,
                                'exam_name' => $exam->title,
                                'exam_detail_id' => NULL,
                                'total_marks' => NULL,
                                'passing_marks' => NULL,
                                'type' => NULL,
                                'start_time' => NULL,
                                'end_time' => NULL,
                                'exam_date' => NULL,
                                'obtained_marks' => null,
                                'marksheet_status' => NULL,
                                'grade' => NULL
                            );
                            $exams[$exam->exam_id] = $array;
                        }
                    }
                    $s->exams = $exams;
                }
            }
            foreach ($students as $index => $std) {
                $temp = array();
                foreach ($subjects as $t) {
                    $temp2 = array();
                    foreach ($t->exams as $key => $e) {
                        $temp2[$key] = clone $e;
                    }
                    $t->exams = $temp2;
                    $temp[] = clone $t;
                }
                $std->subjects = $temp;
            }

            $sql4 = "SELECT "
            . "sh_marksheets.*,"
            . "sh_remarks_and_positions.remark as teacher_remark, "
            . "sh_remarks_and_positions.id as teacher_remark_id, "
            . "sh_administrator_remarks.remarks as administrator_remark, "
            . "sh_administrator_remarks.id as administrator_remark_id "
            . "FROM sh_marksheets "
            . "LEFT JOIN sh_remarks_and_positions ON sh_marksheets.student_id=sh_remarks_and_positions.student_id AND sh_marksheets.exam_id=sh_remarks_and_positions.exam_id "
            . "LEFT JOIN sh_administrator_remarks ON sh_marksheets.student_id=sh_administrator_remarks.student_id "
            . "WHERE sh_marksheets.exam_detail_id "
            . "IN (" . implode(',', $exam_detail_ids) . ") "
            . "AND sh_marksheets.deleted_at IS NULL "
            . "AND sh_administrator_remarks.deleted_at IS NULL "
            . "AND sh_remarks_and_positions.deleted_at IS NULL";
        
            $marks = $this->common_model->dbQuery($sql4);
            
            foreach ($students as $key => $value) { 
                $teacher_remark = null;
                $teacher_remark_id = null;
                $administrator_remark = null;
                $administrator_remark_id = null;
                foreach ($value->subjects as $key2 => $value2) {
                    if (count($value2->exams) > 0) {
                        foreach($value2->exams as $xk=>$x) {
                            $marks_index = find_marks($value->student_id, $x->exam_detail_id, $marks);
                            if ($marks_index != -1) {
                                $value->subjects[$key2]->exams[$xk]->obtained_marks = $marks[$marks_index]->obtained_marks;
                                $value->subjects[$key2]->exams[$xk]->grade = $marks[$marks_index]->grade;
                                $value->subjects[$key2]->exams[$xk]->remarks = $marks[$marks_index]->remarks;
                                $value->subjects[$key2]->exams[$xk]->marksheet_status = $marks[$marks_index]->status;
                                if (($marks[$marks_index]->teacher_remark != null && $marks[$marks_index]->teacher_remark_id != null) || (!empty($marks[$marks_index]->teacher_remark) && !empty($marks[$marks_index]->teacher_remark_id))) {
                                    $teacher_remark = $marks[$marks_index]->teacher_remark;
                                    $teacher_remark_id = $marks[$marks_index]->teacher_remark_id;
                                }
                                if (($marks[$marks_index]->administrator_remark != null && $marks[$marks_index]->administrator_remark_id != null) || (!empty($marks[$marks_index]->administrator_remark) && !empty($marks[$marks_index]->administrator_remark_id))) {
                                    $administrator_remark = $marks[$marks_index]->administrator_remark;
                                    $administrator_remark_id = $marks[$marks_index]->administrator_remark_id;
                                }
                            } else {
                                $value->subjects[$key2]->exams[$xk]->obtained_marks = null;
                                $value->subjects[$key2]->exams[$xk]->grade = null;
                                $value->subjects[$key2]->exams[$xk]->remarks = null;
                                $value->subjects[$key2]->exams[$xk]->marksheet_status = null;
                                $teacher_remark = null;
                                $teacher_remark_id = null;
                                $administrator_remark = null;
                                $administrator_remark_id = null;
                            }
                        }
                    }
                }
                $value->teacher_remark = $teacher_remark;
                $value->administrator_remark = $administrator_remark;
                $value->teacher_remark_id = $teacher_remark_id;
                $value->administrator_remark_id = $administrator_remark_id;
                $value->position = null;
                $value->obtained_total = null;
                $value->result = null;   
            }

            //-------- handle subjects groups -------//
            $subject_group = array();
            $subjectgroup = $this->common_model->dbSelect("subjects","sh_subject_groups"," class_id='$request->class_id' AND FIND_IN_SET('$request->batch_id',batch_id) AND session_id='$active_academic_year' AND deleted_at IS NULL ");
            if(count($subjectgroup) > 0){
                $subjectgroup = $subjectgroup[0]->subjects;
                $subject_group = explode(",", $subjectgroup);
                foreach($students as $subgroup){
                    $subgroup->grouped_subjects = $subject_group;
                }
            } else {
                foreach($students as $subgroup){
                    $subgroup->grouped_subjects = $subject_group;
                }
            }
            
            //----------- Start::Obtained total marks ------------//
            foreach ($students as $kkk => $ss) {  
                foreach ($ss->subjects as $sub) {
                    $sub->obtained_total = 0;
                    foreach($sub->exams as $e){
                        if($e->type == 'number') {
                            $sub->obtained_total += intval($e->obtained_marks);
                        }   
                    }
                }
            }
            //----------- End::Obtained total marks ------------//

            //----------- Start::Subject Avg Score------------//
            $subject_obtained_marks = array();
            foreach ($students as $s1) { 
                foreach ($s1->subjects as $sub) {
                    $subject_obtained_marks[$sub->id] = 0;
                }
            }

            foreach ($students as $s2) { 
                foreach ($s2->subjects as $sub) {
                    $subject_obtained_marks[$sub->id] += $sub->obtained_total;
                }
            }
            foreach ($students as $s3) { 
                foreach ($s3->subjects as $key=>$sub) {
                    $sub->subject_avg_score = 0;
                    foreach($subject_obtained_marks as $k=>$m){
                        if($k == $sub->id){
                            $sub->subject_avg_score = number_format((float)$m/count($students), 2, '.', '');
                        }
                    }
                }
            }
            //----------- End::Subject Avg Score------------//        

            //----------- Start::Subject High/Low Score------------//
            $subject_high_low_scores = array();
            foreach ($students as $s1) { 
                foreach ($s1->subjects as $sub) {
                    $subject_high_low_scores[$sub->id] = "0,";
                }
            }

            foreach ($students as $s2) { 
                foreach ($s2->subjects as $sub) {
                    $subject_high_low_scores[$sub->id] .= $sub->obtained_total.",";
                }
            }
        
            foreach ($students as $s3) { 
                foreach ($s3->subjects as $key=>$sub) {
                    $sub->high_score = 0;
                    $sub->low_score = 0;
                    foreach($subject_high_low_scores as $k2=>$m2){
                        if($k2 == $sub->id){
                            $temp = array();
                            $temp = explode(",",rtrim($m2,','));
                            unset($temp[0]);
                            $sub->high_score = max($temp);
                            $sub->low_score = min($temp);
                        }
                    }
                }
            }
            //----------- Start::Subject High/Low Score------------//

            //----------- Start::Exam total marks ------------//
            foreach ($students as $kkkk => $ss) {
                $data["exam_total_marks"] = null;
                $exam_total_total = 0;
                foreach ($ss->subjects as $sub) {
                    foreach($sub->exams as $h){
                        $exam_total_total = intval($h->total_marks);
                        $data["exam_total_marks"] += intval($exam_total_total);
                    }
                }
                break;
            }
            //----------- End::Exam total marks ------------//
        
            //----------- Start::Class Average Score ------------//
            $data["avg_class_score"] = 0;
            if(count($students) > 0){
                $sum_of_obtained_total = 0;
                foreach($students as $stdd){
                    $sum_of_obtained_total += intval($stdd->obtained_total);
                }
                $data["avg_class_score"] = number_format((float)$sum_of_obtained_total/count($students), 2, '.', '');
            }
            //----------- End::Class Average Score ------------//

            //----------- Start::Class High & Low Points ------------//
            $data["class_low_score"] = 0;
            $data["class_high_score"] = 0;
            if(count($students) > 0){
                $arr = array();
                foreach($students as $stdd1){
                    array_push($arr, intval($stdd1->obtained_total));
                }
                $data["class_low_score"] = min($arr);
                $data["class_high_score"] = max($arr);
            }
            //----------- End::Class High & Low Points ------------//

            //----------- Start::Calculate subjectwise position ------------//
            $subjects_obtained_marks_arr = array();
            foreach ($students as $s1) { 
                foreach ($s1->subjects as $sub) {
                    $subjects_obtained_marks_arr[$sub->id] = "0,";
                }
            }
            foreach($students as $stdp){
                foreach($stdp->subjects as $stds){
                    $subjects_obtained_marks_arr[$stds->id] .= $stds->obtained_total.",";
                }
            }
            foreach($students as $stddp){
                foreach($stddp->subjects as $stdds){
                    $stdds->position = "-";
                    foreach($subjects_obtained_marks_arr as $kr=>$arr){
                        if($kr == $stdds->id && $stdds->obtained_total > 0){
                            $temp = explode(",",rtrim($arr,','));
                            unset($temp[0]);
                            rsort($temp);
                            $position_key = array_search($stdds->obtained_total, $temp)+1;
                            $stdds->position = $this->position_string($position_key);
                        }
                    }
                }
            }
            //----------- End::Calculate subjectwise position ------------//

            //----------- Start::Calculate Class position ------------//
            $arr = array();
            $arr2 = array();
            foreach ($students as $sk => $ssd) {
                if ($ssd->result == lang("pass")) {
                    array_push($arr, $ssd->percentage);
                }
            }
            //---------By Umar---------//
            foreach ($students as $sk1 => $ssd1) {
                if ($ssd1->result == lang("fail")) {
                    array_push($arr2, $ssd1->percentage);
                }
            }


            rsort($arr);
            $old_unique = array_unique($arr);
            $unique = array();
            foreach ($old_unique as $uuu) {
                array_push($unique, $uuu);
            }
            rsort($arr2);
            $old_unique2 = array_unique($arr2);
            $unique2 = array();
            foreach ($old_unique2 as $uuu2) {
                array_push($unique2, $uuu2);
            }

            foreach ($students as $k1 => $std1) {
                if ($std1->result == lang("pass")) {
                    if (in_array($std1->percentage, $unique)) {
                        $position_key = array_search($std1->percentage, $unique);
                        $students[$k1]->position = $position_key + 1;
                        $students[$k1]->percentage .= "%";
                    }
                }
            }

        
            foreach ($students as $key => $val) {
                $val->new_position = $this->position_string($val->position);
            }
            //----------- End::Calculate position ------------//

            //----------- Start::Calculate Grade Subjectwise ------------//  
            $grades = $this->common_model->dbSelect("*","sh_grades"," class_id='$request->class_id' AND deleted_at IS NULL ");
            if(count($grades) > 0){
                foreach($students as $stdg){
                    foreach($stdg->subjects as $stdgs){
                        $stdgs->grade="-";
                        $stdgs->grade_color="#000000";
                        foreach($grades as $g){
                            if($stdgs->obtained_total >= $g->percent_from && $stdgs->obtained_total <= $g->percent_upto){
                                $stdgs->grade = $g->name;
                                $stdgs->grade_color = $g->color;
                            }
                        }
                    }
                }
            } else {
                foreach($students as $stdg){
                    foreach($stdg->subjects as $stdgs){
                        $stdgs->grade="-";
                    }
                }
            }
            //----------- End::Calculate Grade Subjectwise ------------//        

            //-----------Code by Umar-------------//
            foreach ($students as $k1 => $std1) {
                $std1->session_id = $active_academic_year;
                $std1->session_name = $session_name;
                $std1->exam_name = $exam_name;
                if ($std1->result == lang("fail")) {
                    if (in_array($std1->percentage, $unique2)) {
                        $position_key = array_search($std1->percentage, $unique2);
                        $students[$k1]->position = $position_key + 1000;
                        $students[$k1]->percentage .= "%";
                    }
                }
            }
            $data["data"] = $students;
            $data["term_name"] = $term_name;
            $terms_data[$ty] = $data;
        }

        //------------Preveious Term recrod-----------//
        $terms_data_new = array();
        foreach($terms_data as $key=>$value){
            if($key == $last_term_id){
                $terms_data_new = $value;
                break;
            }
        }

        foreach($terms_data_new["data"] as $dd){
            foreach($dd->subjects as $sdd){
                $sdd->prev_term_data = array();
                $sdd->cum_score = 0;
            }
        }

        foreach($terms_data as $key1=>$value1){
            //if($key1 != $last_term_id){
                foreach($value1["data"] as $key11 => $std){
                    foreach($std->subjects as $key12 => $sub){
                        $array = array(
                            "term_id"=>$key1,
                            "term_name" => $value1["term_name"], 
                            "subject_id"=>$sub->id, 
                            "obtained_total"=>$sub->obtained_total, 
                            "grade"=>$sub->grade
                        );
                        array_push($terms_data_new["data"][$key11]->subjects[$key12]->prev_term_data, $array);
                    }
                }
            //}
        }
        foreach($terms_data_new["data"] as $cdata){
            foreach($cdata->subjects as $csub){
                $temp_array = array();
                foreach($csub->prev_term_data as $trm){
                    if($trm["obtained_total"] != 0){
                        array_push($temp_array, $trm);
                    }
                }
                $csub->cum_grade = "-";
                $csub->cum_grade_color = "#000000";
                $tt = 0;
                foreach($csub->prev_term_data as $vl){
                    $tt += intval($vl["obtained_total"]);
                }
                if(count($temp_array) > 0){
                    $csub->cum_score = number_format((float)$tt/count($temp_array), 2, '.', '');
                    foreach($grades as $g){
                        if($csub->cum_score >= $g->percent_from && $csub->cum_score <= $g->percent_upto){
                            $csub->cum_grade = $g->name;
                            $csub->cum_grade_color = $g->color;
                        }
                    }
                }
            }
        }
        //------------------End of previous term record----------------//

        //------------------Exam Obtain Total Marks----------------//        
        foreach($terms_data_new["data"] as $oData){
            $oData->exam_obtained_total = 0;
            $oData->exam_obtained_percentage = 0;
            foreach($oData->subjects as $oSub){
                $oData->exam_obtained_total += $oSub->obtained_total;
            }
            $oData->exam_obtained_percentage = ($oData->exam_obtained_total * 100) / $terms_data_new["exam_total_marks"];
            $oData->exam_obtained_percentage = number_format((float)$oData->exam_obtained_percentage, 2, '.', '');
        }
        //------------------Exam Obtain Total Marks----------------//        

        return $terms_data_new;
    }

    public function getSingleResultCard($request){
        $active_academic_year = $request->academic_year_id;
        $data = array();
        $sql = "SELECT 
            s.id as student_id, 
            concat(s.firstname,' ', s.lastname) as student_name, 
            s.image as student_avatar, 
            s.roll_no as rollno, 
            ss.class_id, 
            ss.section_id 
            FROM 
                student_session ss LEFT JOIN students s ON ss.student_id=s.id 
            WHERE 
                ss.class_id='$request->class_id' 
                AND ss.section_id='$request->batch_id' 
                AND ss.session_id='$active_academic_year' ";
        $students = $this->common_model->dbQuery($sql);
        
        $subjects = array();
        $subject_group_id = $this->common_model->dbSelect("id","sh_subject_groups"," class_id='$request->class_id' AND FIND_IN_SET('$request->batch_id', batch_id) OR session_id='$active_academic_year' AND deleted_at IS NULL ");
        if(count($subject_group_id) > 0){
            $subject_group_id = $subject_group_id[0]->id;
            $sql = "SELECT s.* FROM sh_subjects_with_group sg INNER JOIN subjects s ON sg.subject_id=s.id WHERE subject_group_id='$subject_group_id'";
            $subjects = $this->common_model->dbQuery($sql);
        }

        $sql = "SELECT e.id as exam_id,e.title as title, ed.id as exam_detail_id, e.title as examname, ed.subject_id, ed.type, ed.total_marks, ed.passing_marks,ed.start_time, ed.end_time, ed.exam_date, ed.class_id, ed.batch_id FROM sh_exams e LEFT JOIN sh_exam_details ed ON e.id=ed.exam_id WHERE e.deleted_at IS NULL AND ed.deleted_at IS NULL AND e.id='$request->exam_id' OR e.session_id='$active_academic_year' ";
        $exams = $this->common_model->dbQuery($sql);
        
        if (count($exams) > 0) {
            if ($exams[0]->exam_detail_id == NULL) {
                $data = array("status" => "error", "data" => array(), "message" => "No exams detail found");
            } else {
                $array2 = array();
                $exam_detail_ids = array();
                array_push($exam_detail_ids, $request->exam_id);
                $student_ids = array();
                foreach ($subjects as $key => $s) {
                    $subjects[$key]->exams = array();
                    foreach ($exams as $exam) {
                        if ($s->id == $exam->subject_id && $exam->exam_id == $request->exam_id) {
                            $array = (object) array(
                                'exam_id' => $exam->exam_id,
                                'exam_name' => $exam->title,
                                'exam_detail_id' => $exam->exam_detail_id,
                                'total_marks' => $exam->total_marks,
                                'passing_marks' => $exam->passing_marks,
                                'type' => $exam->type,
                                'start_time' => $exam->start_time,
                                'end_time' => $exam->end_time,
                                'exam_date' => $exam->exam_date,
                                'obtained_marks' => NULL,
                                'marksheet_status' => NULL,
                                'grade' => NULL
                            );
                            array_push($subjects[$key]->exams, $array);
                            array_push($exam_detail_ids, $exam->exam_detail_id);
                        }
                    }
                }

                foreach ($students as $index => $std) {
                    array_push($student_ids, $std->student_id);
                    $students[$index]->subjects = $subjects;
                }

                $student_ids_string = 0;
                if (count($student_ids) > 0) {
                    $student_ids_string = implode(',', $student_ids);
                }

                $sql4 = "SELECT "
                    . "sh_marksheets.*,"
                    . "sh_remarks_and_positions.remark as teacher_remark, "
                    . "sh_remarks_and_positions.id as teacher_remark_id "
                    . "FROM sh_marksheets "
                    . "LEFT JOIN sh_remarks_and_positions ON sh_marksheets.student_id=sh_remarks_and_positions.student_id "
                    . "AND sh_marksheets.exam_id=sh_remarks_and_positions.exam_id "
                    . "WHERE sh_marksheets.exam_detail_id "
                    . "IN (" . implode(',', $exam_detail_ids) . ") "
                    . "AND sh_marksheets.deleted_at IS NULL AND sh_remarks_and_positions.deleted_at is null ";
                $marks = $this->common_model->dbQuery($sql4);
                
                $array = array();
                foreach ($students as $key => $value) {
                    $teacher_remark = null;
                    $teacher_remark_id = null;
                    foreach ($value->subjects as $key2 => $value2) {
                        if (count($value2->exams) > 0) {
                            $marks_index = find_marks($value->student_id, $value2->exams[0]->exam_detail_id, $marks);
                            if ($marks_index != -1) {
                                $students[$key]->subjects[$key2]->exams[0]->obtained_marks = $marks[$marks_index]->obtained_marks;
                                $students[$key]->subjects[$key2]->exams[0]->grade = $marks[$marks_index]->grade;
                                $students[$key]->subjects[$key2]->exams[0]->remarks = $marks[$marks_index]->remarks;
                                $students[$key]->subjects[$key2]->exams[0]->marksheet_status = $marks[$marks_index]->status;
                                if (($marks[$marks_index]->teacher_remark != null && $marks[$marks_index]->teacher_remark_id != null) || (!empty($marks[$marks_index]->teacher_remark) && !empty($marks[$marks_index]->teacher_remark_id))) {
                                    $teacher_remark = $marks[$marks_index]->teacher_remark;
                                    $teacher_remark_id = $marks[$marks_index]->teacher_remark_id;
                                }
                            } else {
                                $students[$key]->subjects[$key2]->exams[0]->obtained_marks = null;
                                $students[$key]->subjects[$key2]->exams[0]->grade = null;
                                $students[$key]->subjects[$key2]->exams[0]->remarks = null;
                                $students[$key]->subjects[$key2]->exams[0]->marksheet_status = null;
                                $teacher_remark = null;
                                $teacher_remark_id = null;
                                
                            }
                        } else {
                            $array222 = (object) array(
                                'exam_id' => $request->exam_id,
                                'exam_detail_id' => NULL,
                                'total_marks' => NULL,
                                'passing_marks' => NULL,
                                'start_time' => NULL,
                                'type' => NULL,
                                'end_time' => NULL,
                                'exam_date' => NULL,
                                'obtained_marks' => NULL,
                                'remarks' => NULL,
                                'marksheet_status' => NULL
                            );
                            array_push($students[$key]->subjects[$key2]->exams, $array222);
                        }
                    }

                    $students[$key]->teacher_remark = $teacher_remark;
                    $students[$key]->teacher_remark_id = $teacher_remark_id;
                    $students[$key]->position = null;
                    $students[$key]->obtained_total = null;
                    $students[$key]->result = null;
                    array_push($array, json_encode($value));
                }

                foreach ($array as $val) {
                    array_push($array2, json_decode($val));
                }

                if ($student_ids_string == 0) {
                    $data = array("status" => "error", "data" => $array2, "message" => lang("no_record"));
                } else {
                    $data = array("status" => "success", "data" => $array2, "message" => "data found");
                }
            }
        }

        //-------- handle subjects groups -------//
        $subject_group = array();
        $subjectgroup = $this->common_model->dbSelect("subjects","sh_subject_groups"," class_id='$request->class_id' AND FIND_IN_SET('$request->batch_id',batch_id) OR session_id='$active_academic_year' AND deleted_at IS NULL ");
        if(count($subjectgroup) > 0){
            $subjectgroup = $subjectgroup[0]->subjects;
            $subject_group = explode(",", $subjectgroup);
            foreach($data["data"] as $subgroup){
                $subgroup->grouped_subjects = $subject_group;
            }
        } else {
            foreach($data["data"] as $subgroup){
                $subgroup->grouped_subjects = $subject_group;
            }
        }
        
        //----------- Start::All subjects marks added or not ------------//
        foreach ($data["data"] as $keey1 => $d) {
            $data["data"][$keey1]->is_all_subjects_marks_added = 'true';
        }
        //----------- End::All subjects marks added or not ------------//
        
        
        //----------- Start::Obtained total marks ------------//
        foreach ($data["data"] as $kkk => $ss) {
            $obtained_total = 0;
            foreach ($ss->subjects as $sub) {
                if($sub->exams[0]->type == 'number') {
                    $obtained_total = $sub->exams[0]->obtained_marks;
                    $data["data"][$kkk]->obtained_total += intval($obtained_total);
                }
            }
        }
        //----------- End::Obtained total marks ------------//

        //----------- Start::Exam total marks ------------//
        foreach ($data["data"] as $kkkk => $ss) {
            $data["exam_total_marks"] = null;
            $exam_total_total = 0;
            foreach ($ss->subjects as $sub) {
                $exam_total_total = $sub->exams[0]->total_marks;
                $data["exam_total_marks"] += intval($exam_total_total);
            }
            break;
        }
        //----------- End::Exam total marks ------------//
        
        //----------- Start::Result Pass or Fail According to Rules ------------//
        $passing_rules = $this->common_model->dbSelect("*", "sh_passing_rules", " class_id='$request->class_id' OR batch_id='$request->batch_id' AND exam_id='$request->exam_id' AND deleted_at IS NULL OR session_id='$active_academic_year' ");
        $passing_rules_obj = null;
        if (count($passing_rules) > 0) {
            $passing_rules[0]->subjects_which_passed = explode(",",$passing_rules[0]->subjects_which_passed);
            $passing_rules_obj = $passing_rules[0];
        }
        if (is_null($passing_rules_obj)) {
            foreach ($data["data"] as $kk => $ss) {
                $data["data"][$kk]->result = "";
            }
        } else {
            foreach ($data["data"] as $kk => $ss) {
                $grouped_subjects = null;
                if (!is_null($ss->grouped_subjects)) {
                    $grouped_subjects = $ss->grouped_subjects;
                }

                $number_of_subjects_passed = 0;
                $exam_total_total = 0;
                $is_complusary_subject_passed = false;
                foreach ($ss->subjects as $sub) {
                    if ($sub->exams[0]->marksheet_status == 'Pass') {
                        if(in_array($sub->id, $passing_rules_obj->subjects_which_passed)){
                            $is_complusary_subject_passed = true;
                        }
                        if (!is_null($grouped_subjects)) {
                            if (in_array($sub->id, $grouped_subjects)) {
                                $exam_total_total += intval($sub->exams[0]->total_marks);
                            }
                        } else if (is_null($grouped_subjects)) {
                            $exam_total_total += intval($sub->exams[0]->total_marks);
                        }
                        $number_of_subjects_passed++;
                    } else {
                        if (!is_null($grouped_subjects)) {
                            if (in_array($sub->id, $grouped_subjects)) {
                                $exam_total_total += intval($sub->exams[0]->total_marks);
                            }
                        } else if (is_null($grouped_subjects)) {
                            $exam_total_total += intval($sub->exams[0]->total_marks);
                        }
                    }
                }
                
                $data["data"][$kk]->result = lang("fail");
                $obtained_percentage = "";
                if ($exam_total_total != 0) {
                    $obtained_percentage = $ss->obtained_total * 100 / $exam_total_total;
                }

                if ($passing_rules_obj->operator == "AND") {
                    if ($is_complusary_subject_passed && $obtained_percentage >= $passing_rules_obj->minimum_percentage) {
                        $data["data"][$kk]->result = lang("pass");
                    }
                } else if ($passing_rules_obj->operator == "OR") {
                    if ($is_complusary_subject_passed || $obtained_percentage >= $passing_rules_obj->minimum_percentage) {
                        $data["data"][$kk]->result = lang("pass");
                    }
                }
                $data["data"][$kk]->obtained_total_old = $data["data"][$kk]->obtained_total;
                $data["data"][$kk]->obtained_total = $data["data"][$kk]->obtained_total . "/" . $data["exam_total_marks"];
                if ($data["exam_total_marks"] != 0) {
                    $data["data"][$kk]->percentage = round((intval($data["data"][$kk]->obtained_total) * 100 ) / intval($data["exam_total_marks"]), 2);
                } else {
                    $data["data"][$kk]->percentage = "";
                }
                
            }
        }
        //----------- Start::Result Pass or Fail According to Rules ------------//
        
        //----------- Start::Calculate position ------------//
        $arr = array();
        $arr2 = array();
        foreach ($data["data"] as $sk => $ssd) {
            if ($ssd->result == lang("pass") || $ssd->result == lang("fail")) {
                array_push($arr, $ssd->percentage);
            }
        }
        //---------By Umar---------//
        /*foreach ($data["data"] as $sk1 => $ssd1) {
            if ($ssd1->result == lang("fail")) {
                array_push($arr2, $ssd1->percentage);
            }
        }*/


        rsort($arr);
        $old_unique = array_unique($arr);
        $unique = array();
        foreach ($old_unique as $uuu) {
            array_push($unique, $uuu);
        }
        /*rsort($arr2);
        $old_unique2 = array_unique($arr2);
        $unique2 = array();
        foreach ($old_unique2 as $uuu2) {
            array_push($unique2, $uuu2);
        }*/
        foreach ($data["data"] as $k1 => $std1) {
            if ($std1->result == lang("pass")) {
                if (in_array($std1->percentage, $unique)) {
                    $position_key = array_search($std1->percentage, $unique);
                    $data["data"][$k1]->position = $position_key + 1;
                    $data["data"][$k1]->percentage .= "%";
                } 
            } else if($std1->result == lang("fail")){
                if (in_array($std1->percentage, $unique)) {
                    $position_key = array_search($std1->percentage, $unique);
                    $data["data"][$k1]->position = $position_key + 1;
                    $data["data"][$k1]->percentage .= "%";
                }
            }
        }
        
        //----------- End::Calculate position ------------//
        foreach ($data["data"] as $key => $val) {
            $data["data"][$key]->new_position = $this->position_string($val->position);
        }

        //-----------Code by Umar-------------//
        /*foreach ($data["data"] as $k1 => $std1) {
            if ($std1->result == lang("fail")) {
                if (in_array($std1->percentage, $unique2)) {
                    $position_key = array_search($std1->percentage, $unique2);
                    $data["data"][$k1]->position = $position_key + 1;
                    $data["data"][$k1]->percentage .= "%";
                }
            }
        }*/
        $this->savePosition($data["data"], $request->exam_id);
        return $data;
    }

    public function position_string($i) {
        if (empty($i) || is_null($i)) {
            return "";
        }
        $j = $i % 10;
        $k = $i % 100;
        if ($j == 1 && $k != 11) {
            return $i . "st";
        }
        if ($j == 2 && $k != 12) {
            return $i . "nd";
        }
        if ($j == 3 && $k != 13) {
            return $i . "rd";
        }
        return $i . "th";
    }

    public function savePosition($data, $exam_id) {
        foreach ($data as $std) {
            if ($std->new_position != null || !empty($std)) {
                $student_id = $std->student_id;
                $res = $this->common_model->dbSelect("*", "sh_remarks_and_positions", " student_id='$student_id' AND exam_id='$exam_id'  AND deleted_at IS NULL ");
                if (count($res) > 0) {
                    $this->common_model->update_where("sh_remarks_and_positions", array("student_id" => $student_id, "exam_id" => $exam_id), array("position" => $std->new_position));
                } else {
                    $array_data = array('student_id' => $student_id, 'exam_id' => $exam_id, 'remark' => NULL, 'position' => $std->new_position);
                    $this->common_model->dbInsert("sh_remarks_and_positions", $array_data);
                }
            }
        }
    }

    function get_teacher_of_class() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $school_id = $this->session->userdata("userdata")["sh_id"];
        $teacher_id_object = $this->common_model->dbSelect("teacher_id", "assign_subjects", " class_id='$request->class_id' AND batch_id='$request->batch_id' AND subject_id='$request->subject_id' AND school_id='$school_id' AND deleted_at IS NULL ");
        $teacher_id = null;
        $teacher_name = null;
        if (count($teacher_id_object) > 0) {
            $teacher_id = $teacher_id_object[0]->teacher_id;
            $teacher_name_object = $this->common_model->dbSelect("name", "users", " id='$teacher_id' AND deleted_at=0 ");
            if (count($teacher_name_object) > 0) {
                $teacher_name = $teacher_name_object[0]->name;
            }
        }
        echo json_encode(array('teacher_name' => $teacher_name));
    }

    function save_teacher_remarks() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $school_id = $this->session->userdata("userdata")["sh_id"];
        $res = $this->common_model->update_where("sh_remarks_and_positions", array("student_id" => $request->student_id, "exam_id" => $request->exam_id), array("remark" => $request->remark));
        //$data = array("student_id"=>$request->student_id,"exam_id"=>$request->exam_id, "remark"=>$request->remark, "school_id"=>$school_id);
        //$res = $this->common_model->insert("sh_remarks_and_positions",$data);
        if ($res) {
            echo json_encode(array("status" => "success", "message" => "Teacher remarks added successfully!"));
        } else {
            echo json_encode(array("status" => "danger", "message" => "Teacher remarks could not add."));
        }
    }

    function save_administrator_remarks() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $school_id = $this->session->userdata("userdata")["sh_id"];
        $isRemarksExists = $this->common_model->dbSelect("*","sh_administrator_remarks"," student_id='$request->student_id' AND cumulative_result_card_group_id='$request->result_card_group_id' AND deleted_at IS NULL ");
        $res = false;
        if(count($isRemarksExists) > 0) {
            $res = $this->common_model->update_where("sh_administrator_remarks", array("student_id" => $request->student_id, "cumulative_result_card_group_id" => $request->result_card_group_id), array("remarks" => $request->remark));
            //$data = array("student_id"=>$request->student_id,"exam_id"=>$request->exam_id, "remark"=>$request->remark, "school_id"=>$school_id);
        } else {
            //$sql = "INSERT INTO sh_administrator_remarks (student_id, cumulative_result_card_group_id, remarks) VALUES ($request->student_id, $request->result_card_group_id, '$request->remark');";
            $dd = array("student_id" => $request->student_id, "cumulative_result_card_group_id" => $request->result_card_group_id, "remarks" => $request->remark);
            $res = $this->common_model->dbInsert("sh_administrator_remarks", $dd);
        }
        //$res = $this->common_model->insert("sh_remarks_and_positions",$data);
        if ($res) {
            echo json_encode(array("status" => "success", "message" => "Administrator remarks added successfully!"));
        } else {
            echo json_encode(array("status" => "danger", "message" => "Administrator remarks could not add."));
        }
    }

    function update_administrator_remarks() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $res = $this->common_model->update_where("sh_administrator_remarks", array("id" => $request->id), array("remarks" => $request->remark));
        if ($res) {
            echo json_encode(array("status" => "success", "message" => "Administrator remarks updated successfully!"));
        } else {
            echo json_encode(array("status" => "danger", "message" => "Administrator remarks could not update."));
        }
    }

    function update_teacher_remarks() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $res = $this->common_model->update_where("sh_remarks_and_positions", array("id" => $request->id), array("remark" => $request->remark));
        if ($res) {
            echo json_encode(array("status" => "success", "message" => "Teacher remarks updated successfully!"));
        } else {
            echo json_encode(array("status" => "danger", "message" => "Teacher remarks could not update."));
        }
    }

    function update_marks() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $student_id = $request->student_id;
        $class_id = $request->class_id;
        $batch_id = $request->section_id;
        //$school_id = $this->session->userdata("userdata")["sh_id"];
        $subjects = $request->subjects;
        foreach ($subjects as $val) {
            if ($val->is_subject == true) {
                $exam_id = $val->exams[0]->exam_id;
                $exam_detail_id = $val->exams[0]->exam_detail_id;
                $new_marks = $val->exams[0]->new_marks;
                $new_grade = $val->exams[0]->new_grade;
                $passing_marks = $val->exams[0]->passing_marks;
                $status = "Pass";
                if ($new_marks < $passing_marks) {
                    $status = "Fail";
                }
                $new_remarks = $val->exams[0]->new_remarks;
                if (is_null($new_remarks)) {
                    $new_remarks = '';
                }
                $data = array(
                    'exam_id' => $exam_id,
                    'exam_detail_id' => $exam_detail_id,
                    'obtained_marks' => $new_marks,
                    'grade' => $new_grade,
                    'status' => $status,
                    'student_id' => $student_id,
                    'class_id' => $class_id,
                    'batch_id' => $batch_id,
                    'remarks' => $new_remarks
                );

                if($exam_detail_id != null){
                    $this->db->replace('sh_marksheets', $data);
                }
            }
        }
    }

    public function delete_marks() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $student_id = $request->student_id;
        $exam_id = $request->exam_id;
        $this->common_model->update_where("sh_marksheets",array("student_id"=>$student_id,"exam_id"=>$exam_id),array("deleted_at"=>date('Y-m-d h:i:s')));
        //$this->db->set('deleted_at', date('Y-m-d H:i:s'));
        //$this->db->where('student_id', $student_id);
        //$this->db->where('exam_id', $exam_id);
        //$this->db->update('sh_marksheets');
        $this->common_model->update_where("sh_remarks_and_positions",array("student_id"=>$student_id,"exam_id"=>$exam_id),array("deleted_at"=>date('Y-m-d h:i:s')));
        //$this->db->where('student_id', $student_id);
        //$this->db->where('exam_id', $exam_id);
        //$this->db->delete('sh_remarks_and_positions');
    }
    
    public function getClasses() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        //-------------------------
        $data = "";
        if (login_user()->user->role_id == EMPLOYEE_ROLE_ID && get_teacher_dept_id() == login_user()->user->teacher_dept_id) {
            if (count(login_user()->t_data->classes) > 0) {
                $data = $this->common_model->dbSelect("*", "classes", " id IN (" . implode(',', login_user()->t_data->classes) . ") AND academic_year_id = ".$request->academic_year_id." ORDER BY name asc ");
            }
        } else if (login_user()->user->role_id == EMPLOYEE_ROLE_ID) {
            $data = $this->common_model->dbSelect("*", "classes", " school_id=".login_user()->user->sh_id." AND academic_year_id = ".$request->academic_year_id." AND deleted_at is null ORDER BY name asc ");
        } else if (login_user()->user->role_id == ADMIN_ROLE_ID) {
            $data = $this->common_model->dbSelect("*", "classes", " school_id=" . login_user()->user->sh_id . " AND academic_year_id = ".$request->academic_year_id." AND deleted_at IS NULL ORDER BY name asc ");
        } else if (login_user()->user->role_id == PARENT_ROLE_ID) {
            
        } else if (login_user()->user->role_id == STUDENT_ROLE_ID) {
            
        }
        //-------------------------
        
        echo json_encode($data);
    }
    
    public function getClassBatches() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        $data = $this->common_model->dbSelect("*", "sh_batches", "class_id='$request->id'");
        echo json_encode($data);
    }
    
    public function getResultCardGroups() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        $data = $this->common_model->dbSelect("*", "sh_complete_result_card_settings", " class_id='$request->class_id' AND batch_id='$request->batch_id' OR session_id='$request->session_id' AND deleted_at IS NULL ");
        echo json_encode($data);
    }

    public function getAcademicYears(){
        $school_id = $this->session->userdata("userdata")["sh_id"];
        
        $data = $this->common_model->dbSelect("*","academic_years"," school_id='$school_id' AND deleted_at IS NULL ");
        $academic_year_id = "-1";
        if(isset($this->session->userdata("userdata")["academic_year"]) && !empty($this->session->userdata("userdata")["academic_year"])){
            $academic_year_id = $this->session->userdata("userdata")["academic_year"];
        }
        $response = array('current_academic_year_id'=>$academic_year_id,"data"=>$data);
        echo json_encode($response);
    }

    public function getSchoolClasses(){
        $data = $this->common_model->dbSelect("*","classes"," 1 ");
        echo json_encode($data);
    }

    public function print_all_result_cards(){
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $class_id = $request->obj->class_id;
        $batch_id = $request->obj->batch_id;
        $exam_id = $request->obj->exam_id;
        $session_id = $request->obj->academic_year_id;
        $session_name = $this->common_model->dbSelect("session","sessions"," id='$session_id' ")[0]->session;
        $std_reading_subjects = 0;
        

        $selected_language_id = 1;
        $language_where = " language_id=1 ";
        if($this->session->userdata("admin")["language"]["language"] == "English") {
            $language_where = " language_id=1 ";
            $selected_language_id = 1;
        } else {
            $language_where = " language_id=2 ";
            $selected_language_id = 2;
        }
        
        /****** Template Handleing *******/
        $template_obj = $this->common_model->dbSelect("*","sh_templates","tag='result-card' AND is_default='Yes' AND deleted_at IS NULL AND ".$language_where);
        if(count($template_obj) > 0){
            $data["template"] = $template_obj[0];
        } else {
            $temp = array(
                "name" => "Result Card",
                "is_custom" => "Yes",
                "tag" => "result-card",
                "html" => system_result_card_template_english(),
                "language_id" => 1,
                "is_default" => "Yes"
            );
            $inserted_id = $this->common_model->dbInsert("sh_templates",$temp);
            $template = $this->common_model->dbSelect("*","sh_templates"," id='$inserted_id' ");
            $data["template"] = $template[0];
        }
        
        $result_cards = array();
        $exam_detail_ids = array();
        $exam_details = $this->common_model->dbSelect("*","sh_exam_details"," exam_id=$exam_id AND class_id=$class_id AND batch_id=$batch_id AND deleted_at IS NULL ");
        foreach($exam_details as $e){
            array_push($exam_detail_ids, $e->id);
        }
        
        foreach($request->ids as $student_id){
            $ysql = "SELECT u.id as student_id, concat(u.firstname,' ',u.lastname) as student_name, u.roll_no as rollno, u.mobileno as mobile_phone, u.image as avatar, u.father_name, u.father_phone, u.mother_name, u.mother_phone, u.guardian_name, u.guardian_phone, c.id as class_id, c.class as class_name, b.section as batch_id, b.section as section_name, e.title as exam_name,s.id as subject_id, s.name as subject_name, m.obtained_marks, m.grade, m.status, m.remarks, rp.remark as teacher_remark,rp.position, d.exam_date, d.start_time, d.end_time, d.total_marks, d.passing_marks, d.type 
                FROM sh_marksheets m 
                LEFT JOIN sh_remarks_and_positions rp ON m.student_id=rp.student_id AND rp.exam_id='$exam_id' 
                INNER JOIN students u ON m.student_id=u.id 
                INNER JOIN classes c ON m.class_id=c.id 
                INNER JOIN sh_batches b ON m.batch_id=b.section_id AND b.class_id='$class_id'
                INNER JOIN sh_exam_details d ON m.exam_detail_id=d.id 
                RIGHT JOIN sh_exams e ON d.exam_id=e.id 
                INNER JOIN subjects s ON d.subject_id=s.id 
                WHERE m.student_id='$student_id' 
                AND m.exam_detail_id IN (". implode(",", $exam_detail_ids).") 
                AND m.class_id='$class_id' 
                AND m.batch_id='$batch_id'
                AND m.deleted_at IS NULL ";
            $res = $this->common_model->dbQuery($ysql);
            
            $info = array();
            if(count($res) > 0){
                $info["student_id"] = $res[0]->student_id;
                $info["student_name"] = $res[0]->student_name;
                $info["father_name"] = $res[0]->father_name;
                $info["rollno"] = $res[0]->rollno;
                $info["mobile_phone"] = $res[0]->mobile_phone;
                $info["avatar"] = $res[0]->avatar;
                $info["class_id"] = $res[0]->class_id;
                $info["batch_id"] = $res[0]->batch_id;
                $info["class_name"] = $res[0]->class_name;
                $info["section_name"] = $res[0]->section_name;
                $info["exam_name"] = $res[0]->exam_name;
                $info["position"] = $res[0]->position;
                $info["session_name"] = $session_name;
                $info["class_teacher_remarks"] = $res[0]->teacher_remark;
                $info["details"] = array();
                foreach($res as $r){
                    $ar = (object) array();
                    $ar->subjnect_id = $r->subject_id;
                    $ar->subjnect_name = $r->subject_name;
                    $ar->obtained_marks = $r->obtained_marks;
                    $ar->grade = $r->grade;
                    $ar->status = $r->status;
                    $ar->remarks = $r->remarks;
                    $ar->exam_date = $r->exam_date;
                    $ar->start_time = $r->start_time;
                    $ar->end_time = $r->end_time;
                    $ar->total_marks = $r->total_marks;
                    $ar->passing_marks = $r->passing_marks;
                    $ar->type = $r->type;
                    array_push($info["details"], $ar);
                }
            }
            $data["data"] = $info;
            if(count($data["data"]) > 0) { 
                $rendered_form_html = render_resultcard_tags($data, $data["template"]);
                array_push($result_cards, $rendered_form_html);
            }
        }
        echo json_encode($result_cards);
    }

    public function single_result_card_view(){
        $id = $_GET["id"];
        $page = $_GET["requested_page"];
        $data["request_page"] = $page; 
        $class_id = 0;
        $exam_id = 0;
        $batch_id = 0;
        $student_id = 0;
        $session_id = 0;
        $data = array();
        
        $selected_language_id = 0;
        $language_where = "";
        if($this->session->userdata("admin")["language"]["language"] == "English") {
            $language_where = " language_id=1 ";
            $selected_language_id = 1;
        } else {
            $language_where = " language_id=2 ";
            $selected_language_id = 2;
        }
        
        if($page == "result_card"){
            $student_id = $_GET["id"];
            $class_id = isset($_GET["class_id"])?$_GET["class_id"]:0;
            $exam_id = isset($_GET["exam_id"])?$_GET["exam_id"]:0;
            $batch_id = isset($_GET["batch_id"])?$_GET["batch_id"]:0;
            $session_id = isset($_GET["session_id"])?$_GET["session_id"]:0;
            $template_obj = $this->common_model->dbSelect("*","sh_templates","tag='result-card' AND is_default='Yes' AND deleted_at IS NULL AND class_id='$class_id' AND ".$language_where);
            
            if(count($template_obj) > 0){
                $data["template"] = $template_obj[0];
            } else {
                $temp = array(
                    "name" => "Result Card",
                    "is_custom" => "Yes",
                    "tag" => "result-card",
                    "html" => system_result_card_template_english(),
                    "language_id" => 1,
                    "class_id" => $class_id,
                    "is_default" => "Yes"
                );
                $inserted_id = $this->common_model->dbInsert("sh_templates",$temp);
                $template = $this->common_model->dbSelect("*","sh_templates"," id='$inserted_id' ");
                $data["template"] = $template[0];
            }

            $exam_detail_ids = "";
            $exam_details = $this->common_model->dbSelect("*","sh_exam_details"," exam_id='$exam_id' AND class_id='$class_id' AND batch_id='$batch_id' AND deleted_at IS NULL ");
            foreach($exam_details as $e){
                $exam_detail_ids .= $e->id . ",";
            }
            $exam_detail_ids = rtrim($exam_detail_ids,",");
            
            $std_reading_subjects_obj = $this->common_model->dbSelect("subjects","sh_subject_groups"," class_id='$class_id' AND FIND_IN_SET('$batch_id',batch_id) AND deleted_at IS NULL OR session_id='$session_id' ");
            $std_reading_subjects = 0;
            if(count($std_reading_subjects_obj) > 0){
                $std_reading_subjects = $std_reading_subjects_obj[0]->subjects;
            }
            
            $sql = "SELECT u.id as student_id, concat(u.firstname,' ',u.lastname) as student_name, u.roll_no as rollno, u.mobileno as mobile_phone, u.image as avatar, u.father_name, u.father_phone, u.mother_name, u.mother_phone, u.guardian_name, u.guardian_phone, c.id as class_id, c.class as class_name, b.section as batch_id, b.section as section_name, e.title as exam_name,s.id as subject_id, s.name as subject_name, m.obtained_marks, m.grade, m.status, m.remarks, rp.remark as teacher_remark,rp.position, d.exam_date, d.start_time, d.end_time, d.total_marks, d.passing_marks, d.type 
                FROM sh_marksheets m 
                LEFT JOIN sh_remarks_and_positions rp ON m.student_id=rp.student_id AND rp.exam_id='$exam_id'
                INNER JOIN students u ON m.student_id=u.id 
                INNER JOIN classes c ON m.class_id=c.id 
                INNER JOIN sh_batches b ON m.batch_id=b.section_id AND b.class_id='$class_id'
                INNER JOIN sh_exam_details d ON m.exam_detail_id=d.id 
                RIGHT JOIN sh_exams e ON d.exam_id=e.id 
                INNER JOIN subjects s ON d.subject_id=s.id 
                WHERE m.student_id='$student_id' 
                AND m.exam_detail_id IN (". $exam_detail_ids .") 
                AND m.class_id='$class_id' 
                AND m.batch_id='$batch_id'
                AND m.deleted_at IS NULL 
                AND s.id IN (".$std_reading_subjects.")";
            
            $res = $this->common_model->dbQuery($sql);
            
            $info = array();
            if(count($res) > 0){
                $info["session_name"] = $this->common_model->dbSelect("session","sessions"," id='$session_id' ")[0]->session;
                $info["student_id"] = $res[0]->student_id;
                $info["student_name"] = $res[0]->student_name;
                $info["father_name"] = $res[0]->father_name;
                $info["rollno"] = $res[0]->rollno;
                $info["mobile_phone"] = $res[0]->mobile_phone;
                $info["avatar"] = $res[0]->avatar;
                $info["class_id"] = $res[0]->class_id;
                $info["batch_id"] = $res[0]->batch_id;
                $info["class_name"] = $res[0]->class_name;
                $info["section_name"] = $res[0]->section_name;
                $info["exam_name"] = $res[0]->exam_name;
                $info["position"] = $res[0]->position;
                $info["class_teacher_remarks"] = $res[0]->teacher_remark;
                $info["details"] = array();
                foreach($res as $r){
                    $ar = (object) array();
                    $ar->subjnect_id = $r->subject_id;
                    $ar->subjnect_name = $r->subject_name;
                    $ar->obtained_marks = $r->obtained_marks;
                    $ar->type = $r->type;
                    $ar->grade = $r->grade;
                    $ar->status = $r->status;
                    $ar->remarks = $r->remarks;
                    $ar->exam_date = $r->exam_date;
                    $ar->start_time = $r->start_time;
                    $ar->end_time = $r->end_time;
                    $ar->total_marks = $r->total_marks;
                    $ar->passing_marks = $r->passing_marks;
                    array_push($info["details"], $ar);
                }
            }
            $data["data"] = $info;
            $rendered_form_html = render_resultcard_tags($data, $data["template"]);
            $data["template"]->html =  $rendered_form_html;
        }
        
        $this->load->view("admin/yforms/show",$data);
    }

    public function renderCompletedResultCardSingle(){
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $data = array();
        $selected_language_id = 0;
        $language_where = "";
        if($this->session->userdata("admin")["language"]["language"] == "English") {
            $language_where = " language_id=1 ";
            $selected_language_id = 1;
        } else {
            $language_where = " language_id=2 ";
            $selected_language_id = 2;
        }

        $template_obj = $this->common_model->dbSelect("*","sh_templates","tag='multi-exam-result-card' AND is_default='Yes' AND deleted_at IS NULL AND class_id='$request->class_id' AND ".$language_where);
            
        if(count($template_obj) > 0){
            $data["template"] = $template_obj[0];
        } else {
            $temp = array(
                "name" => "Multi Exam Result Card",
                "is_custom" => "Yes",
                "tag" => "multi-exam-result-card",
                "html" => system_result_card_template_english2(),
                "language_id" => 1,
                "class_id" => $request->class_id,
                "is_default" => "Yes"
            );
            $inserted_id = $this->common_model->dbInsert("sh_templates",$temp);
            $template = $this->common_model->dbSelect("*","sh_templates"," id='$inserted_id' ");
            $data["template"] = $template[0];
        }
        $request->school_name = $this->session->userdata("admin")["school_name"];
        $request->school_email = $this->session->userdata("admin")["email"];
        $request->school_address = $this->session->userdata("admin")["address"]; 
        $request->school_phone = $this->session->userdata("admin")["phone"];
        $data["data"] = $request;
        $rendered_form_html = render_complete_resultcard_single_tags($data, $data["template"]);
        $data["template"]->html =  $rendered_form_html;
        echo json_encode($data);
    }

    public function saveGraphAsImage(){
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        $gdata = array();
        $graphArray = array();
        foreach($request->subjects as $gsub){
            $obj = (object) array();
            $obj->label = $gsub->name; 
            $obj->y = $gsub->obtained_total;
            array_push($graphArray, $obj);
        }
        $gdata["graphArray"] = $graphArray;
        $gdata["student_id"] = $request->student_id;
        $d = $this->load->view('admin/yexam/graph.php', $gdata, TRUE);
        echo $d;
    }

    public function uploadGraph(){
        $image = $this->input->post("image");
        $student_id = $this->input->post("std_id");
        save_image($image,$student_id);
    }

    public function updateResultCard(){
        $id = $this->input->post("id");
        $title = $this->input->post("title");
        $html = $this->input->post("html");

        $data = array("name"=>$title,"html"=>$html);
        $res = $this->common_model->update_where("sh_templates",array("id"=>$id),$data);
        if($res){
            $this->session->set_flashdata('alert', array("status" => "success", "message" => lang('result_card_update_message')));
            redirect("admin/Examination/resultcard","refresh");
        }
    }

    public function skill_and_assessment(){
        if (!$this->rbac->hasPrivilege('exam_group', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Examinations_New');
        $this->session->set_userdata('sub_menu', 'Examinations_New/skill_and_assessment');
        /*$data['title']      = 'Add Batch';
        $data['title_list'] = 'Recent Batch';
        $data['examType']   = $this->exam_type;*/

        foreach($this->session->userdata("admin")["roles"] as $key=>$val){
            $data["role_id"] = $val;
        }
        
        $skill_and_assessment_groups = xcrud_get_instance();
        $skill_and_assessment_groups->table('sh_skill_and_assessment_groups');
        //$subject_groups->where("class_id", $params["class_id"])->where("FIND_IN_SET($batch_id,batch_id)")->where("session_id", $params["session_id"]);
        $skill_and_assessment_groups->where('deleted_at IS NULL');
        $skill_and_assessment_groups->show_primary_ai_field(false);
        $skill_and_assessment_groups->relation('class_id', 'classes', 'id', 'class', '', '', true, '', '', '', '');
        $skill_and_assessment_groups->columns('assessment,code,class_id');
        $skill_and_assessment_groups->fields('assessment,code,class_id');
        $skill_and_assessment_groups->label('class_id', lang('class'));
        $skill_and_assessment_groups->label('assessment', lang('assessment'));
        $skill_and_assessment_groups->label('code', lang('code'));
        $skill_and_assessment_groups->replace_insert('save_skill_and_assessment_groups');
        $skill_and_assessment_groups->replace_update('save_skill_and_assessment_groups');
        $skill_and_assessment_groups->replace_remove('soft_delete');
        $skill_and_assessment_groups->unset_print();
        $skill_and_assessment_groups->unset_csv();
        $skill_and_assessment_groups->unset_title();
        //$skill_and_assessment_groups->unset_search();
        $skill_and_assessment_groups->unset_limitlist();
        
        
        $data["skill_and_assessment_groups"] = $skill_and_assessment_groups->render();
        $this->load->view('layout/header', $data);
        $this->load->view('admin/yexam/skill_and_assessment', $data);
        $this->load->view('layout/footer', $data);
    }

    public function get_students_for_skills(){
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        $sql = "SELECT 
            s.id as student_id, 
            concat(s.firstname,' ', s.lastname) as student_name, 
            s.image as student_avatar, 
            s.roll_no as rollno, 
            ss.class_id, 
            ss.section_id,
            ssa.evaluation,
            ssa.sa_group_id
            FROM 
                student_session ss LEFT JOIN students s ON ss.student_id=s.id 
                LEFT JOIN sh_student_skill_and_assessments ssa ON s.id=ssa.student_id AND ssa.sa_group_id='$request->sa_group_id'
            WHERE 
                ss.class_id='$request->class_id' 
                AND ss.section_id='$request->batch_id' ";
        $students = $this->common_model->dbQuery($sql);
        echo json_encode($students);
    }

    public function save_students_skills_and_assessments() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        foreach($request as $std){
            $data = array(
                "student_id" => $std->student_id,
                "class_id" => $std->class_id,
                "batch_id" => $std->section_id,
                "evaluation" => $std->evaluation,
                "sa_group_id" => $std->sa_group_id
            );
            $this->db->replace("sh_student_skill_and_assessments", $data);
        }

        $response = array("status"=>"success","message"=>"Student Skills & Assessments Saved Successfully!");
        echo json_encode($response);
    }

    public function get_skill_and_assessment_groups_for_class(){
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        $data = $this->common_model->dbSelect("*","sh_skill_and_assessment_groups", " class_id=$request->class_id AND deleted_at IS NULL ");
        echo json_encode($data);
    }

    function restore_session_settings() {
        $from = $this->input->post("session_from");
        $to = $this->input->post("session_to");
        $selected_tab = $this->input->post("selected_tab");
        $from_session_name = $this->common_model->dbSelect("session","sessions"," id='$from' ")[0]->session;
        $to_session_name = $this->common_model->dbSelect("session","sessions"," id='$to' ")[0]->session;
        $response = array();
        if($from != $to){
            // restore it
            $subject_groups = $this->common_model->dbSelect("*","sh_subject_groups", " session_id='$from' ");
            $exams = $this->common_model->dbSelect("*","sh_exams", " session_id='$from' ");
            $result_card_groups = $this->common_model->dbSelect("*","sh_result_card_groups", " session_id='$from' ");
            $exam_details = $this->common_model->dbSelect("*","sh_exam_details", " session_id='$from' ");
            $passing_rules = $this->common_model->dbSelect("*","sh_passing_rules", " session_id='$from' ");
            $grades = $this->common_model->dbSelect("*","sh_grades", " session_id='$from' ");
            
            // copy subject groups
            if(count($subject_groups) > 0){
                foreach($subject_groups as $sg){
                    $where = " class_id='$sg->class_id' AND batch_id='$sg->batch_id' AND group_name='$sg->group_name' AND subjects='$sg->subjects' AND session_id='$to' AND deleted_at IS NULL ";
                    $exists = $this->common_model->dbSelect("*","sh_subject_groups"," $where ");
                    if(count($exists) == 0){
                        $sg->id = '';
                        $sg->session_id = $to;
                        $sg->created_at = date("Y-m-d h:i:s");
                        $sg = (array)$sg;
                        $this->common_model->dbInsert('sh_subject_groups', $sg);
                        $response[] = array("status"=> "success", "message" => $sg['group_name']." copied successfully into session - $to_session_name.");
                    } else {
                        $response[] = array("status"=> "danger", "message" => $sg->group_name." already exists in session - $to_session_name.");
                    }
                }
            }

            // copy exams
            if(count($exams) > 0){
                foreach($exams as $e){
                    $where = " title='$e->title' AND start_date='$e->start_date' AND end_date='$e->end_date' AND session_id='$to' AND total_marks='$e->total_marks' AND passing_marks='$e->passing_marks' AND deleted_at IS NULL ";
                    $exists = $this->common_model->dbSelect("*","sh_exams"," $where ");
                    if(count($exists) == 0){
                        $e->id = '';
                        $e->session_id = $to;
                        $e->created_at = date("Y-m-d h:i:s");
                        $e = (array)$e;
                        $this->common_model->dbInsert('sh_exams', $e);
                        $response[] = array("status"=> "success", "message" => $e['title']." copied successfully into session - $to_session_name.");
                    } else {
                        $response[] = array("status"=> "danger", "message" =>$e->title." already exists in session - $to_session_name.");
                    }
                }
            }

            // copy exams details
            if(count($exam_details) > 0){
                foreach($exam_details as $ed){
                    //echo "<pre/>"; print_r($ed); die();
                    $where = " exam_id='$ed->exam_id' AND class_id='$ed->class_id' AND batch_id='$ed->batch_id' AND subject_id='$ed->subject_id' AND type='$ed->type' AND session_id='$to' AND subject_group_id='$ed->subject_group_id' AND deleted_at IS NULL ";
                    $exists = $this->common_model->dbSelect("*","sh_exam_details"," $where ");
                    if(count($exists) == 0){
                        $ed->id = '';
                        $ed->session_id = $to;
                        $ed->created_at = date("Y-m-d h:i:s");
                        $ed = (array)$ed;
                        $this->common_model->dbInsert('sh_exam_details', $ed);
                        $response[] = array("status"=> "success", "message" => "Exam detail under Exam ID ".$ed['exam_id']." copied successfully into session - $to_session_name.");
                    } else {
                        $response[] = array("status"=> "danger", "message" => "Exam detail already exists against (ID: $ed->id) into session - $to_session_name.");
                    }
                }
            }

            // copy passing rules
            if(count($passing_rules) > 0){
                foreach($passing_rules as $r){
                    //echo "<pre/>"; print_r($r); die();
                    $where = " exam_id='$r->exam_id' AND class_id='$r->class_id' AND batch_id='$r->batch_id' AND minimum_percentage='$r->minimum_percentage' AND operator='$r->operator' AND subjects_which_passed='$r->subjects_which_passed' AND session_id='$to' AND subject_group_id='$r->subject_group_id' AND deleted_at IS NULL ";
                    $exists = $this->common_model->dbSelect("*","sh_passing_rules"," $where ");
                    if(count($exists) == 0) {
                        $r->id = '';
                        $r->session_id = $to;
                        $r->created_at = date("Y-m-d h:i:s");
                        $r = (array)$r;
                        $inserted_id = $this->common_model->dbInsert('sh_passing_rules', $r);
                        $response[] = array("status"=> "success", "message" => "Passing rule Against (ID:$inserted_id) copied successfully into session - $to_session_name.");
                    } else {
                        $response[] = array("status"=> "danger", "message" => "Passing rule already exists against (ID: $r->id) into session - $to_session_name.");
                    }
                }
            }

            // copy grades
            if(count($grades) > 0){
                foreach($grades as $g){
                    $where = " name='$g->name' AND class_id='$g->class_id' AND percent_from='$g->percent_from' AND percent_upto='$g->percent_upto' AND description='$g->description' AND color='$g->color' AND session_id='$to' AND deleted_at IS NULL ";
                    $exists = $this->common_model->dbSelect("*","sh_grades"," $where ");
                    if(count($exists) == 0) {
                        $g->id = '';
                        $g->session_id = $to;
                        $g->created_at = date("Y-m-d h:i:s");
                        $g = (array)$g;
                        $this->common_model->dbInsert('sh_grades', $g);
                        $response[] = array("status"=> "success", "message" => $g['name']." copied successfully into session - $to_session_name.");
                    } else {
                        $response[] = array("status"=> "danger", "message" => "$g->name already exists into session - $to_session_name.");
                    }
                }
            }

            // copy result card groups
            if(count($result_card_groups) > 0){
                foreach($result_card_groups as $rcg){
                    $where = " name='$rcg->name' AND class_id='$rcg->class_id' AND batch_id='$rcg->batch_id' AND session_id='$to' AND deleted_at IS NULL ";
                    $exists = $this->common_model->dbSelect("*","sh_result_card_groups"," $where ");
                    if(count($exists) == 0) {
                        $rcg->id = '';
                        $rcg->session_id = $to;
                        $rcg->created_at = date("Y-m-d h:i:s");
                        $rcg = (array)$rcg;
                        $this->common_model->dbInsert('sh_result_card_groups', $rcg);
                        $response[] = array("status"=> "success", "message" => $rcg['name']." copied successfully into session - $to_session_name.");
                    } else {
                        $response[] = array("status"=> "danger", "message" => "$rcg->name already exists into session - $to_session_name.");
                    }
                }
            }
            $this->session->set_flashdata('success_message',$response);
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            // can't restore ot same session
            $this->session->set_flashdata('error_message',"You can't copy to same session! choose two different sessions.");
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
}