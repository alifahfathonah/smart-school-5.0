<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class User extends Student_Controller
{

    public $school_name;
    public $school_setting;
    public $setting;
    public $payment_method;

    public function __construct()
    {
        parent::__construct();
        $this->payment_method     = $this->paymentsetting_model->getActiveMethod();
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function unauthorized()
    {
        $data = array();
        $this->load->view('layout/student/header');
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/student/footer');
    }

    public function dashboard()
    {
         // $student_session = $this->session->all_userdata();
         // print_r($student_session);
         // exit();

        $this->session->set_userdata('top_menu', 'Dashboard');
        $student_id            = $this->customlib->getStudentSessionUserID();
        $student_current_class = $this->customlib->getStudentCurrentClsSection();

        $student = $this->student_model->getStudentByClassSectionID($student_current_class->class_id, $student_current_class->section_id, $student_id);

        $data = array();
        if (!empty($student)) {
            $student_session_id           = $student_current_class->student_session_id;
            $gradeList                    = $this->grade_model->get();
            $student_due_fee              = $this->studentfeemaster_model->getStudentFees($student_session_id);
            $student_discount_fee         = $this->feediscount_model->getStudentFeesDiscount($student_session_id);
            $data['student_discount_fee'] = $student_discount_fee;
            $data['student_due_fee']      = $student_due_fee;
            $timeline                     = $this->timeline_model->getStudentTimeline($student["id"], $status = 'yes');
            $data["timeline_list"]        = $timeline;
            $data['sch_setting']          = $this->sch_setting_detail;
            $data['adm_auto_insert']      = $this->sch_setting_detail->adm_auto_insert;

            $data['examSchedule'] = array();
            $data['exam_result']  = $this->examgroupstudent_model->searchStudentExams($student['student_session_id'], true);
           
            $data['exam_grade'] = $this->grade_model->getGradeDetails();
            $student_doc            = $this->student_model->getstudentdoc($student_id);
            $data['student_doc']    = $student_doc;
            $data['student_doc_id'] = $student_id;
            $category_list          = $this->category_model->get();
            $data['category_list']  = $category_list;
            $data['gradeList']      = $gradeList;
            $data['student']        = $student;
        }

        $this->load->view('layout/student/header', $data);
        $this->load->view('user/dashboard', $data);
        $this->load->view('layout/student/footer', $data);
    }

    public function changepass()
    {
        $data['title'] = 'Change Password';
        $this->form_validation->set_rules('current_pass', 'Current password', 'trim|required|xss_clean');
        $this->form_validation->set_rules('new_pass', 'New password', 'trim|required|xss_clean|matches[confirm_pass]');
        $this->form_validation->set_rules('confirm_pass', 'Confirm password', 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $sessionData            = $this->session->userdata('loggedIn');
            $this->data['id']       = $sessionData['id'];
            $this->data['username'] = $sessionData['username'];
            $this->load->view('layout/student/header', $data);
            $this->load->view('user/change_password', $data);
            $this->load->view('layout/student/footer', $data);
        } else {
            $sessionData = $this->session->userdata('student');
            $data_array  = array(
                'current_pass' => ($this->input->post('current_pass')),
                'new_pass'     => ($this->input->post('new_pass')),
                'user_id'      => $sessionData['id'],
                'user_name'    => $sessionData['username'],
            );
            $newdata = array(
                'id'       => $sessionData['id'],
                'password' => $this->input->post('new_pass'),
            );
            $query1 = $this->user_model->checkOldPass($data_array);
            if ($query1) {
                $query2 = $this->user_model->saveNewPass($newdata);
                if ($query2) {

                    $this->session->set_flashdata('success_msg', 'Password changed successfully');
                    $this->load->view('layout/student/header', $data);
                    $this->load->view('user/change_password', $data);
                    $this->load->view('layout/student/footer', $data);
                }
            } else {

                $this->session->set_flashdata('error_msg', 'Invalid current password');
                $this->load->view('layout/student/header', $data);
                $this->load->view('user/change_password', $data);
                $this->load->view('layout/student/footer', $data);
            }
        }
    }

    public function changeusername()
    {
        $sessionData = $this->customlib->getLoggedInUserData();

        $data['title'] = 'Change Username';
        $this->form_validation->set_rules('current_username', 'Current username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('new_username', 'New username', 'trim|required|xss_clean|matches[confirm_username]');
        $this->form_validation->set_rules('confirm_username', 'Confirm username', 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

        } else {

            $data_array = array(
                'username'     => $this->input->post('current_username'),
                'new_username' => $this->input->post('new_username'),
                'role'         => $sessionData['role'],
                'user_id'      => $sessionData['id'],
            );
            $newdata = array(
                'id'       => $sessionData['id'],
                'username' => $this->input->post('new_username'),
            );
            $is_valid = $this->user_model->checkOldUsername($data_array);

            if ($is_valid) {
                $is_exists = $this->user_model->checkUserNameExist($data_array);
                if (!$is_exists) {
                    $is_updated = $this->user_model->saveNewUsername($newdata);
                    if ($is_updated) {
                        $this->session->set_flashdata('success_msg', 'Username changed successfully');
                        redirect('user/user/changeusername');
                    }
                } else {
                    $this->session->set_flashdata('error_msg', 'Username Already Exists, Please choose other');
                }
            } else {
                $this->session->set_flashdata('error_msg', 'Invalid current username');
            }
        }
        $this->data['id']       = $sessionData['id'];
        $this->data['username'] = $sessionData['username'];
        $this->load->view('layout/student/header', $data);
        $this->load->view('user/change_username', $data);
        $this->load->view('layout/student/footer', $data);
    }

    public function download($student_id, $doc)
    {
        $this->load->helper('download');
        $filepath = "./uploads/student_documents/$student_id/" . $this->uri->segment(5);
        $data     = file_get_contents($filepath);
        $name     = $this->uri->segment(6);
        force_download($name, $data);
    }

    public function user_language($lang_id)
    {
        $language_name = $this->db->select('languages.language')->from('languages')->where('id', $lang_id)->get()->row_array();
        $student       = $this->session->userdata('student');
        if (!empty($student)) {
            $this->session->unset_userdata('student');
        }
        $language_array      = array('lang_id' => $lang_id, 'language' => $language_name['language']);
        $student['language'] = $language_array;
        $this->session->set_userdata('student', $student);

        $session         = $this->session->userdata('student');
        $id              = $session['student_id'];
        $data['lang_id'] = $lang_id;
        $language_result = $this->language_model->set_studentlang($id, $data);
    }

    public function timeline_download($timeline_id, $doc)
    {
        $this->load->helper('download');
        $filepath = "./uploads/student_timeline/" . $doc;
        $data     = file_get_contents($filepath);
        $name     = $doc;
        force_download($name, $data);
    }

    public function view($id)
    {
        $data['title']           = 'Student Details';
        $student                 = $this->student_model->get($id);
        $student_due_fee         = $this->studentfee_model->getDueFeeBystudent($student['class_id'], $student['section_id'], $id);
        $data['student_due_fee'] = $student_due_fee;
        $transport_fee           = $this->studenttransportfee_model->getTransportFeeByStudent($student['student_session_id']);
        $data['transport_fee']   = $transport_fee;
        $examList                = $this->examschedule_model->getExamByClassandSection($student['class_id'], $student['section_id']);
        $data['examSchedule']    = array();
        if (!empty($examList)) {
            $new_array = array();
            foreach ($examList as $ex_key => $ex_value) {
                $array         = array();
                $x             = array();
                $exam_id       = $ex_value['exam_id'];
                $exam_subjects = $this->examschedule_model->getresultByStudentandExam($exam_id, $student['id']);
                foreach ($exam_subjects as $key => $value) {
                    $exam_array                     = array();
                    $exam_array['exam_schedule_id'] = $value['exam_schedule_id'];
                    $exam_array['exam_id']          = $value['exam_id'];
                    $exam_array['full_marks']       = $value['full_marks'];
                    $exam_array['passing_marks']    = $value['passing_marks'];
                    $exam_array['exam_name']        = $value['name'];
                    $exam_array['exam_type']        = $value['type'];
                    $exam_array['attendence']       = $value['attendence'];
                    $exam_array['get_marks']        = $value['get_marks'];
                    $x[]                            = $exam_array;
                }
                $array['exam_name']   = $ex_value['exam_name'];
                $array['exam_result'] = $x;
                $new_array[]          = $array;
            }
            $data['examSchedule'] = $new_array;
        }
        return $data['student'] = $student;
    }

    public function getfees()
    {

        $id                    = $this->customlib->getStudentSessionUserID();
        $student_current_class = $this->customlib->getStudentCurrentClsSection();

        $this->session->set_userdata('top_menu', 'fees');
        $this->session->set_userdata('sub_menu', 'student/getFees');
        $category                = $this->category_model->get();
        $data['categorylist']    = $category;
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $paymentoption           = $this->customlib->checkPaypalDisplay();
        $data['paymentoption']   = $paymentoption;
        $data['payment_method']  = false;
        if (!empty($this->payment_method)) {
            $data['payment_method'] = true;
        }
        $student_id                   = $id;
        $student                      = $this->student_model->getStudentByClassSectionID($student_current_class->class_id, $student_current_class->section_id, $student_id);
        $class_id                     = $student_current_class->class_id;
        $section_id                   = $student_current_class->section_id;
        $data['title']                = 'Student Details';
        $student_due_fee              = $this->studentfeemaster_model->getStudentFees($student_current_class->student_session_id);
        $student_discount_fee         = $this->feediscount_model->getStudentFeesDiscount($student_current_class->student_session_id);
        $data['student_discount_fee'] = $student_discount_fee;
        $data['student_due_fee']      = $student_due_fee;
        $data['student']              = $student;
        $this->load->view('layout/student/header', $data);
        $this->load->view('student/getfees', $data);
        $this->load->view('layout/student/footer', $data);
    }

    public function results(){
        $this->session->set_userdata('top_menu', 'Results');
        $this->session->set_userdata('sub_menu', 'user/user/results');
        $student_id = $this->customlib->getStudentSessionUserID();
        $array_childs = array();
        $array_childs[] = $this->student_model->get($student_id);
        
        /*echo $student_id; die(); 
        $ch = $this->session->userdata('parent_childs');
        $child_ids = [];
        foreach ($ch as $key_ch => $value_ch) {
            $array_childs[] = $this->student_model->get($value_ch['student_id']);
            $child_ids[] = $value_ch['student_id'];
        }*/
        $session_id = $this->setting_model->getCurrentSession();
        $data['sessions'] = $this->common_model->dbSelect("*","sessions"," id='$session_id' ");
        $data['exams'] = $this->common_model->dbSelect("*","sh_exams"," deleted_at IS NULL AND session_id=$session_id AND is_locked=0 ");
        $data['student_list'] = $array_childs;
        $this->load->view('layout/student/header', $data);
        $this->load->view('student/results', $data);
        $this->load->view('layout/student/footer', $data);
    }

    public function getStudentResult(){

        $student_id = $this->input->post("student_id"); 
        $record = $this->student_model->get($student_id);
        $class_id = $record["class_id"];
        $batch_id = $record["section_id"];
        $session_id = $this->input->post("academic_year_id"); 
        $exam_id = $this->input->post("exam_id"); 
        $request = (object)array(
            "academic_year_id" => $session_id,
            "batch_id" => $batch_id,
            "class_id" => $class_id,
            "exam_id" => $exam_id,
            "result_card_group_id" => "",
            "result_type" => "single"
        );

        $results = $this->getSingleResultCard($request);
        $studentResult = array();
        foreach($results["data"] as $res){
            if($student_id == $res->student_id){
                array_push($studentResult, $res);
                break;
            }
        }
        
        $table = "<table class='table table-default'>
            <tr>
                <th>Subjects</th>
                <th>Total Marks</th>
                <th>Obtained Marks</th>
                <th>Grade</th>
                <th>Status</th>
                <th>Remarks</th>
            </tr>";
        
        foreach($studentResult as $std){
            $arr = explode("/",$std->obtained_total);
            foreach($std->subjects as $sub) {
                $total_marks = $sub->exams[0]->total_marks == "" ? '-' :  $sub->exams[0]->total_marks;
                $obtained_marks = $sub->exams[0]->obtained_marks == "" ? '-' :  $sub->exams[0]->obtained_marks;
                $grade = $sub->exams[0]->grade == "" ? '-' :  $sub->exams[0]->grade;
                $remarks = $sub->exams[0]->remarks == "" ? '-' :  $sub->exams[0]->remarks;
                $table .= "<tr>
                    <td>".$sub->name."</td>
                    <td>".$total_marks."</td>
                    <td>".$obtained_marks."</td>
                    <td>".$grade."</td>";
                if($sub->exams[0]->obtained_marks > $sub->exams[0]->passing_marks){
                    $table .= "<td><span class='text-success'>Pass</span></td>";
                } else {
                    $table .= "<td><span class='text-danger'>Fail</span></td>";
                }
                    
                $table .= "<td>".$remarks."</td>";
                $table .= "</tr>";
            }
            $table .= "<tr><th>Total</th><td>".$arr[1]."</td><td colspan='4' class='text-center'>".$arr[0]."</td></tr>";
        }
        $table .= "</table>";
        echo $table;
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
        $subject_group_id = $this->common_model->dbSelect("id","sh_subject_groups"," class_id='$request->class_id' AND FIND_IN_SET('$request->batch_id', batch_id) OR session_id='$active_academic_year' ");
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
        $subjectgroup = $this->common_model->dbSelect("subjects","sh_subject_groups"," class_id='$request->class_id' AND FIND_IN_SET('$request->batch_id',batch_id) OR session_id='$active_academic_year' ");
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

}
