<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Parents extends Parent_Controller {

    public $payment_method;

    function __construct() {
        parent::__construct();
        $this->payment_method = $this->paymentsetting_model->getActiveMethod();
       
    }

    function unauthorized() {
        $data = array();
        $this->load->view('layout/parent/header');
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/parent/footer');
    }

    function dashboard() {
        $this->session->set_userdata('top_menu', 'My Children');
        $this->session->set_userdata('sub_menu', 'parent/parents/dashboard');
        $student_id = $this->customlib->getStudentSessionUserID();
        $array_childs = array();
        $ch = $this->session->userdata('parent_childs');
        $cart = $this->common_model->dbSelect("*","parent_shopping_cart"," parent_id='$student_id' ");
        $this->session->set_userdata("cart_count", 0);
        if(count($cart) > 0){
            $cart_count = count($cart);
            $this->session->set_userdata("cart_count", $cart_count);
        } 
        foreach ($ch as $key_ch => $value_ch) {
            $array_childs[] = $this->student_model->get($value_ch['student_id']);
        }
        $data['student_list'] = $array_childs;
        $this->load->view('layout/parent/header', $data);
        $this->load->view('parent/dashboard', $data);
        $this->load->view('layout/parent/footer', $data);
    }
      public function user_language($lang_id){
        
            $language_name=$this->db->select('languages.language')->from('languages')->where('id',$lang_id)->get()->row_array();
            $student=$this->session->userdata('student');
            if(!empty($student)){
            $this->session->unset_userdata('student');
            }
            $language_array=array('lang_id'=>$lang_id,'language'=>$language_name['language']);            
            $student['language']=$language_array;
            $this->session->set_userdata('student',$student);
            $session=$this->session->userdata('student');
            $id=$session['id'];
            $data['lang_id']=$lang_id;
            $language_result = $this->language_model->set_parentlang($id,$data);
        
    }

    public function download($student_id, $doc) {
        $this->load->helper('download');
        $filepath = "./uploads/student_documents/$student_id/" . $this->uri->segment(5);
        $data = file_get_contents($filepath);
        $name = $this->uri->segment(6);
        force_download($name, $data);
    }


  public function downloadSchool_content($file) {
        $this->load->helper('download');
        $filepath = "./uploads/school_content/material/" . $this->uri->segment(7);
        $data = file_get_contents($filepath);
        $name = $this->uri->segment(7);
        force_download($name, $data);
    }


    public function timeline_download($timeline_id, $doc) {
        $this->load->helper('download');
        $filepath = "./uploads/student_timeline/" . $doc;
        $data = file_get_contents($filepath);
        $name = $doc;
        force_download($name, $data);
    }


    function changepass() {
        $data['title'] = 'Change Password';
        $this->form_validation->set_rules('current_pass', 'Current password', 'trim|required|xss_clean');
        $this->form_validation->set_rules('new_pass', 'New password', 'trim|required|xss_clean|matches[confirm_pass]');
        $this->form_validation->set_rules('confirm_pass', 'Confirm password', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $sessionData = $this->session->userdata('loggedIn');
            $this->data['id'] = $sessionData['id'];
            $this->data['username'] = $sessionData['username'];
            $this->load->view('layout/parent/header', $data);
            $this->load->view('parent/change_password', $data);
            $this->load->view('layout/parent/footer', $data);
        } else {
            $sessionData = $this->session->userdata('student');
            $data_array = array(
                'current_pass' => ($this->input->post('current_pass')),
                'new_pass' => ($this->input->post('new_pass')),
                'user_id' => $sessionData['id'],
                'user_name' => $sessionData['username']
            );
            $newdata = array(
                'id' => $sessionData['id'],
                'password' => $this->input->post('new_pass')
            );
            $query1 = $this->user_model->checkOldPass($data_array);
            if ($query1) {
                $query2 = $this->user_model->saveNewPass($newdata);
                if ($query2) {

                    $this->session->set_flashdata('success_msg', 'Password changed successfully');
                    $this->load->view('layout/parent/header', $data);
                    $this->load->view('parent/change_password', $data);
                    $this->load->view('layout/parent/footer', $data);
                }
            } else {

                $this->session->set_flashdata('error_msg', 'Invalid current password');
                $this->load->view('layout/parent/header', $data);
                $this->load->view('parent/change_password', $data);
                $this->load->view('layout/parent/footer', $data);
            }
        }
    }

    function changeusername() {
        $sessionData = $this->customlib->getLoggedInUserData();

        $data['title'] = 'Change Username';
        $this->form_validation->set_rules('current_username', 'Current username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('new_username', 'New username', 'trim|required|xss_clean|matches[confirm_username]');
        $this->form_validation->set_rules('confirm_username', 'Confirm username', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            
        } else {

            $data_array = array(
                'username' => $this->input->post('current_username'),
                'new_username' => $this->input->post('new_username'),
                'role' => $sessionData['role'],
                'user_id' => $sessionData['id'],
            );
            $newdata = array(
                'id' => $sessionData['id'],
                'username' => $this->input->post('new_username')
            );
            $is_valid = $this->user_model->checkOldUsername($data_array);

            if ($is_valid) {
                $is_exists = $this->user_model->checkUserNameExist($data_array);
                if (!$is_exists) {
                    $is_updated = $this->user_model->saveNewUsername($newdata);
                    if ($is_updated) {
                        $this->session->set_flashdata('success_msg', 'Username changed successfully');
                        redirect('parent/parents/changeusername');
                    }
                } else {
                    $this->session->set_flashdata('error_msg', 'Username Already Exists, Please choose other');
                }
            } else {
                $this->session->set_flashdata('error_msg', 'Invalid current username');
            }
        }
        $this->data['id'] = $sessionData['id'];
        $this->data['username'] = $sessionData['username'];
        $this->load->view('layout/parent/header', $data);
        $this->load->view('parent/change_username', $data);
        $this->load->view('layout/parent/footer', $data);
    }

    function getstudent($id = NULL) {

        $this->session->set_userdata('top_menu', 'My Children');
        $this->session->set_userdata('sub_menu', 'parent/parents/getStudent');
        $this->auth->validate_child($id);
        $student_id = $id;

        $payment_setting = $this->paymentsetting_model->get();
        $data['payment_setting'] = $payment_setting;
        $category = $this->category_model->get();
        $data['category_list'] = $category;
        $student = $this->student_model->get($student_id);
        $timeline = $this->timeline_model->getStudentTimeline($student["id"], $status = 'yes');
        $data["timeline_list"] = $timeline;

       
        $class_id = $student['class_id'];
        $section_id = $student['section_id'];
        $data['title'] = 'Student Details';
        $student_session_id = $student['student_session_id'];
        $student_due_fee = $this->studentfeemaster_model->getStudentFees($student_session_id);
        $student_discount_fee = $this->feediscount_model->getStudentFeesDiscount($student_session_id);
        $data['student_discount_fee'] = $student_discount_fee;
        $data['student_due_fee'] = $student_due_fee;
        $examList = $this->examschedule_model->getExamByClassandSection($student['class_id'], $student['section_id']);
      $data['exam_grade'] = $this->grade_model->getGradeDetails();

         $data['exam_result']  = $this->examgroupstudent_model->searchStudentExams($student['student_session_id'], true);
         
        $data['student'] = $student;
        $this->load->view('layout/parent/header', $data);
        $this->load->view('parent/student/getstudent', $data);
        $this->load->view('layout/parent/footer', $data);
    }

    function getfees($id = NULL) {
        $this->auth->validate_child($id);
        $this->session->set_userdata('top_menu', 'Fees');
        $this->session->set_userdata('sub_menu', 'parent/parents/getFees');
         $category                     = $this->category_model->get();
        $data['categorylist']         = $category;
        $paymentoption = $this->customlib->checkPaypalDisplay();
        $data['paymentoption'] = $paymentoption;
        $data['payment_method'] = false;
        if (!empty($this->payment_method)) {
            $data['payment_method'] = true;
        }
        $student_id = $id;
        $student = $this->student_model->get($student_id);
        $class_id = $student['class_id'];
        $section_id = $student['section_id'];
        $data['title'] = 'Student Details';
        $student_due_fee = $this->studentfeemaster_model->getStudentFees($student['student_session_id']);
        $student_discount_fee = $this->feediscount_model->getStudentFeesDiscount($student['student_session_id']);
        $data['student_discount_fee'] = $student_discount_fee;
        $data['student_due_fee'] = $student_due_fee;
        $data['student'] = $student;
        $this->load->view('layout/parent/header', $data);
        $this->load->view('parent/student/getfees', $data);
        $this->load->view('layout/parent/footer', $data);
    }

    function gettimetable($id = NULL) {
        $this->auth->validate_child($id);
        $this->session->set_userdata('top_menu', 'Time Table');
        $this->session->set_userdata('sub_menu', 'parent/parents/gettimetable');
        $student_id = $id;
        $student = $this->student_model->get($student_id);
        $class_id = $student['class_id'];
        $section_id = $student['section_id'];
        
        $subjet_timetable         = $this->subjectgroup_model->getClassandSectionTimetable($class_id, $section_id);
        $data['subjet_timetable'] = $subjet_timetable;
        $data['result_array'] = $subjet_timetable;


        $data['student'] = $student;
        $this->load->view('layout/parent/header', $data);
        $this->load->view('parent/student/gettimetable', $data);
        $this->load->view('layout/parent/footer', $data);
    }

    function getsubject($id = NULL) {
        $this->auth->validate_child($id);
        $this->session->set_userdata('top_menu', 'Subjects');
        $this->session->set_userdata('sub_menu', 'parent/parents/getsubject');
        $student_id = $id;
        $student = $this->student_model->get($student_id);
        $data['student'] = $student;
        $class_id = $student['class_id'];
        $section_id = $student['section_id'];
        $data['title'] = 'Student Details';
        $subject_list = $this->teachersubject_model->getSubjectByClsandSection($class_id, $section_id);
        $data['result_array'] = $subject_list;
        $this->load->view('layout/parent/header', $data);
        $this->load->view('parent/student/getsubject', $data);
        $this->load->view('layout/parent/footer', $data);
    }

    function getattendence($id = NULL) {
        $this->auth->validate_child($id);
        $this->session->set_userdata('top_menu', 'Attendance');
        $this->session->set_userdata('sub_menu', 'parent/parents/getattendence');
   $setting_result     = $this->setting_model->get();

        $setting_result = ($setting_result[0]);
        $setting_result['attendence_type'];
        $student_id = $id;
        $student = $this->student_model->get($student_id);




        $data['student'] = $student;



      
        $this->load->view('layout/parent/header', $data);
  if ($setting_result['attendence_type']) {
    $this->load->view('parent/student/attendenceSubject', $data);
         
        } else {
            $this->load->view('parent/student/getattendence', $data);

        }


    
        $this->load->view('layout/parent/footer', $data);
    }

    public function getAjaxAttendence() {
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $student_session_id = $this->input->get('student_session');
        $result = array();
        $new_date = "01-" . $month . "-" . $year;
        $totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $first_day_this_month = date('01-m-Y');
        $fst_day_str = strtotime(date($new_date));
        $array = array();
        for ($day = 2; $day <= $totalDays; $day++) {
            $fst_day_str = ($fst_day_str + 86400);
            $date = date('Y-m-d', $fst_day_str);
            $student_attendence = $this->attendencetype_model->getStudentAttendence($date, $student_session_id);
            if (!empty($student_attendence)) {
                $s = array();
                $s['date'] = $date;
                $s['badge'] = false;
                $s['footer'] = "Extra information";
                $s['body'] = "Information for this date<\/p>You can add html<\/strong> in this block<\/p>";
                $type = $student_attendence->type;
                $s['title'] = $type;
                if ($type == 'Present') {
                    $s['classname'] = "grade-4";
                } else if ($type == 'Absent') {
                    $s['classname'] = "grade-1";
                } else if ($type == 'Late') {
                    $s['classname'] = "grade-3";
                } else if ($type == 'Late with excuse') {
                    $s['classname'] = "grade-2";
                } else if ($type == 'Holiday') {
                    $s['classname'] = "grade-5";
                } else if ($type == 'Half Day') {
                    $s['classname'] = "grade-2";
                }
                $array[] = $s;
            }
        }
        if (!empty($array)) {
            echo json_encode($array);
        } else {
            echo false;
        }
    }

    function getexams($id = NULL) {
        $this->auth->validate_child($id);
        $this->session->set_userdata('top_menu', 'Examination');
        $this->session->set_userdata('sub_menu', 'parent/parents/getexams');
        $student_id = $id;
        $student = $this->student_model->get($student_id);
        $class_id = $student['class_id'];
        $section_id = $student['section_id'];
        $data['title'] = 'Student Details';

             $data['exam_grade'] = $this->grade_model->getGradeDetails();

         $data['exam_result']  = $this->examgroupstudent_model->searchStudentExams($student['student_session_id'], true);
        // $examList = $this->examschedule_model->getExamByClassandSection($student['class_id'], $student['section_id']);
        $data['examSchedule'] = array();
        // if (!empty($examList)) {
        //     $new_array = array();
        //     foreach ($examList as $ex_key => $ex_value) {
        //         $array = array();
        //         $x = array();
        //         $exam_id = $ex_value['exam_id'];
        //         $exam_subjects = $this->examschedule_model->getresultByStudentandExam($exam_id, $student['id']);
        //         foreach ($exam_subjects as $key => $value) {
        //             $exam_array = array();
        //             $exam_array['exam_schedule_id'] = $value['exam_schedule_id'];
        //             $exam_array['exam_id'] = $value['exam_id'];
        //             $exam_array['full_marks'] = $value['full_marks'];
        //             $exam_array['passing_marks'] = $value['passing_marks'];
        //             $exam_array['exam_name'] = $value['name'];
        //             $exam_array['exam_type'] = $value['type'];
        //             $exam_array['attendence'] = $value['attendence'];
        //             $exam_array['get_marks'] = $value['get_marks'];
        //             $x[] = $exam_array;
        //         }
        //         $array['exam_name'] = $ex_value['name'];
        //         $array['exam_result'] = $x;
        //         $new_array[] = $array;
        //     }
        //     $data['examSchedule'] = $new_array;
        // }
        $data['student'] = $student;
        $this->load->view('layout/parent/header', $data);
        $this->load->view('parent/student/getexams', $data);
        $this->load->view('layout/parent/footer', $data);
    }

    function getexamresult() {
        $student_id = $this->uri->segment('4');
        $exam_id = $this->uri->segment('5');
        $student = $this->student_model->get($student_id);
        $class_id = $student['class_id'];
        $section_id = $student['section_id'];
        $data['title'] = 'Exam Result';
        $data['student'] = $student;
        $new_array = array();
        $array = array();
        $x = array();
        $exam_detail_array = $this->exam_model->get($exam_id);
        $exam_subjects = $this->examschedule_model->getresultByStudentandExam($exam_id, $student_id);
        foreach ($exam_subjects as $key => $value) {
            $exam_array = array();
            $exam_array['exam_schedule_id'] = $value['exam_schedule_id'];
            $exam_array['exam_id'] = $value['exam_id'];
            $exam_array['full_marks'] = $value['full_marks'];
            $exam_array['passing_marks'] = $value['passing_marks'];
            $exam_array['exam_name'] = $value['name'];
            $exam_array['exam_type'] = $value['type'];
            $exam_array['attendence'] = $value['attendence'];
            $exam_array['get_marks'] = $value['get_marks'];
            $x[] = $exam_array;
        }
        $array['exam_name'] = $exam_detail_array['name'];
        $array['exam_result'] = $x;
        $new_array[] = $array;
        $data['examSchedule'] = $new_array;
        $this->load->view('layout/parent/header', $data);
        $this->load->view('parent/student/examresult', $data);
        $this->load->view('layout/parent/footer', $data);
    }

    function getexamtimetable() {
        $data['title'] = 'Student Details';
        $class_id = $this->uri->segment('4');
        $section_id = $this->uri->segment('5');
        $exam_id = $this->uri->segment('6');
        $examSchedule = $this->examschedule_model->getDetailbyClsandSection($class_id, $section_id, $exam_id);
        $data['examSchedule'] = $examSchedule;
        $exam_detail_array = $this->exam_model->get($exam_id);
        $data['exam_name'] = $exam_detail_array['name'];
        $this->load->view('layout/parent/header', $data);
        $this->load->view('parent/student/examtimetable', $data);
        $this->load->view('layout/parent/footer', $data);
    }

    public function assignment() {
        $this->session->set_userdata('top_menu', 'Downloads');
        $this->session->set_userdata('sub_menu', 'content/assignment');
        $student_id = $this->customlib->getStudentSessionUserID();
        $student = $this->student_model->get($student_id);

        $class_id = $student['class_id'];
        $section_id = $student['section_id'];
        $data['title_list'] = 'List of Assignment';
        $list = $this->content_model->getListByCategoryforUser($class_id, $section_id, "Assignments");
        $data['list'] = $list;
        $this->load->view('layout/parent/header');
        $this->load->view('parent/student/assignment', $data);
        $this->load->view('layout/parent/footer');
    }

    public function studymaterial() {
        $this->session->set_userdata('top_menu', 'Downloads');
        $this->session->set_userdata('sub_menu', 'content/studymaterial');
        $student_id = $this->customlib->getStudentSessionUserID();
        $student = $this->student_model->get($student_id);
        $class_id = $student['class_id'];
        $section_id = $student['section_id'];
        $data['title_list'] = 'List of Assignment';
        $list = $this->content_model->getListByCategoryforUser($class_id, $section_id, "Study Material");
        $data['list'] = $list;
        $this->load->view('layout/parent/header');
        $this->load->view('parent/student/studymaterial', $data);
        $this->load->view('layout/parent/footer');
    }

    public function download_docs($student_id){
        $this->session->set_userdata('top_menu', 'Downloads');
        $this->session->set_userdata('sub_menu', 'parent/parents/getStudent'.$student_id);
        $student = $this->student_model->get($student_id);
        $class_id = $student['class_id'];
        $section_id = $student['section_id'];
        $data['title_list'] = 'List of Syllabus';
        $list = $this->content_model->getListByforUser($class_id, $section_id);
       
        $data['list'] = $list;
        
        $this->load->view('layout/parent/header');
        $this->load->view('parent/student/syllabus', $data);
        $this->load->view('layout/parent/footer');
    }

    public function syllabus() {
        $this->session->set_userdata('top_menu', 'Downloads');
        $this->session->set_userdata('sub_menu', 'content/syllabus');
        $student_id = $this->customlib->getStudentSessionUserID();
        $student = $this->student_model->get($student_id);
        $class_id = $student['class_id'];
        $section_id = $student['section_id'];
        $data['title_list'] = 'List of Syllabus';
        $list = $this->content_model->getListByCategoryforUser($class_id, $section_id, "Syllabus");
      
        $data['list'] = $list;
        $this->load->view('layout/parent/header');
        $this->load->view('parent/student/syllabus', $data);
        $this->load->view('layout/parent/footer');
    }

    public function other() {
        $this->session->set_userdata('top_menu', 'Downloads');
        $this->session->set_userdata('sub_menu', 'content/other');
        $student_id = $this->customlib->getStudentSessionUserID();
        $student = $this->student_model->get($student_id);
        $class_id = $student['class_id'];
        $section_id = $student['section_id'];
        $data['title_list'] = 'List of Other Download';
        $list = $this->content_model->getListByCategoryforUser($class_id, $section_id, "Other Download");
        $data['list'] = $list;
        $this->load->view('layout/parent/header');
        $this->load->view('parent/student/other', $data);
        $this->load->view('layout/parent/footer');
    }

    public function get_student_teachers($studentid){
      
        $this->session->set_userdata('top_menu', 'teacher/index');
        $this->session->set_userdata('sub_menu', 'parent/parents/get_student_teachers_'.$studentid);
        $data['title'] = 'Add Teacher';
        
        $schoolsessionId=$this->setting_model->getCurrentSession();
     
        $currentClassSectionById=$this->student_model->currentClassSectionById($studentid,$schoolsessionId);
       
        $genderList = $this->customlib->getGender();
        $session_id = $this->session->userdata('parent_childs');

        $data['class_id']=$class_id=$currentClassSectionById['class_id'];
        $data['section_id']=$section_id=$currentClassSectionById['section_id'];        
        $data['resultlist']=$this->subjecttimetable_model->getTeacherByClassandSection($class_id, $section_id);
        $subject=array();
        $teachers=array();
        foreach($data['resultlist'] as $value){        
            $teachers[$value->staff_id][]=$value;                       
        } 
     
        $session_id = $this->session->userdata('student');
        $data['teacherlist'] = $teachers;
        $data['user_id']=$session_id['id'];
        $data['role']=$session_id['role'];
        $data['genderList'] = $genderList;

        $user_ratedstafflist=$this->staff_model->get_RatedStaffByUser($session_id['id']);
        $data['user_ratedstafflist']=$user_ratedstafflist;

        $all_rating=$this->staff_model->all_rating();
        $all_rating=$this->staff_model->all_rating();

        $data['rate_canview']=0;

        foreach ($all_rating as $value) {
            if($value['total']>=3){
            $r=($value['rate']/$value['total']);

            $data['avg_rate'][$value['staff_id']]=$r;
            $data['rate_canview']=1;
            }else{
            $data['avg_rate'][$value['staff_id']]=0;
            }
            $data['reviews'][$value['staff_id']]=$value['total'];
        }
     
        $this->load->view('layout/parent/header', $data);
        $this->load->view('parent/teacher/teacherList', $data);
        $this->load->view('layout/parent/footer', $data);
    }
   public function getdaysubattendence()
    {
        $date=$this->input->post('date');
        $date=date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date')));

        $attendencetypes = $this->attendencetype_model->get();
        // $date=date('2019-11-11');   
        $timestamp = strtotime($date);
        $day = date('l', $timestamp);

        $student_id            = $this->input->post('student_id');
        
        $student               = $this->student_model->get($student_id);
       
         $class_id = $student['class_id'];
        $section_id = $student['section_id'];
        $data['title'] = 'Student Details';
        $student_session_id = $student['student_session_id'];



        $result['attendencetypeslist'] = $attendencetypes;
        $result['attendence']=$this->studentsubjectattendence_model->studentAttendanceByDate($class_id, $section_id, $day, $date,$student_session_id);
        $result_page=$this->load->view('parent/student/_getdaysubattendence', $result,true);
       echo json_encode(array('status' => 1,'result_page'=>$result_page));   
    }

    public function store($cat=0){
        if($cat != 0){
            $items = $this->common_model->dbSelect("*","school_store"," item_category_id=$cat AND deleted_at IS NULL ");
        } else {
            $sql = "SELECT s.* FROM school_store s INNER JOIN item_category c ON s.item_category_id=c.id WHERE s.deleted_at IS NULL AND c.type='store_category'";
            $items = $this->common_model->dbQuery($sql);
        }
        
        $this->session->set_userdata('top_menu', 'Parents');
        $this->session->set_userdata('sub_menu', 'parents/store');
        $data['items'] = $items;
        $itemcategories = $this->common_model->dbSelect("*","item_category"," type='store_category' AND is_active='yes' ");
        $data["itemcategories"] = $itemcategories;
        $this->load->view('layout/parent/header', $data);
        $this->load->view('parent/store', $data);
        $this->load->view('layout/parent/footer', $data);
    }

    public function savetocart(){
        $items = $this->common_model->dbSelect("*","school_store"," deleted_at IS NULL ");
        $this->session->set_userdata('top_menu', 'Store');
        $this->session->set_userdata('sub_menu', '');
        $data['items'] = $items;
        $itemcategories = $this->common_model->dbSelect("*","item_category"," type='store_category' ");
        $data["itemcategories"] = $itemcategories;
        $parent_id = $this->session->userdata("student")["id"];
        
        $this->form_validation->set_rules('quantity', 'Quantity', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/parent/header', $data);
            $this->load->view('parent/store', $data);
            $this->load->view('layout/parent/footer', $data);
        } else {
            $data = array(
                "item_id" => $this->input->post("item_id"),
                "parent_id" => $parent_id,
                "quantity" => $this->input->post("quantity")
            );
            $this->common_model->dbInsert("parent_shopping_cart",$data);
            $arr = $this->common_model->dbSelect("*","parent_shopping_cart"," parent_id='$parent_id' ");
            if(count($arr)>0){
                $this->session->set_userdata("cart_count", count($arr));
            }
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">'.$this->lang->line('success_message').'</div>');
            redirect('parent/parents/store');
        }
    }

    public function checkout(){
        $data["payment_settings"] = $this->common_model->dbSelect("*","payment_settings"," is_active='yes' ");
        $parent_id = $this->session->userdata("student")["id"];
        if(count($data["payment_settings"]) > 0){
            $data["payment_settings"][0]->api_email = "ukeredi@gmail.com";
            $email = $this->common_model->dbSelect("guardian_email","students"," id='$parent_id' ");
            if(Count($email) > 0){
                $data["payment_settings"][0]->api_email = $email[0]->guardian_email;
            }
        }   
        $sql = "SELECT s.*, c.id as cart_id, c.quantity as quantity FROM parent_shopping_cart c INNER JOIN school_store s ON c.item_id=s.id WHERE c.parent_id='$parent_id' ";
        $items = $this->common_model->dbQuery($sql);
        $this->session->set_userdata('top_menu', 'Store');
        $this->session->set_userdata('sub_menu', 'checkout');
        $data['items'] = $items;
        $this->load->view('layout/parent/header', $data);
        $this->load->view('parent/checkout', $data);
        $this->load->view('layout/parent/footer', $data);
    }

    function delete($id) {
        $data['title'] = 'Item School Store List';
        $res = $this->common_model->delete('id',$id,"parent_shopping_cart");
        $parent_id = $this->session->userdata("student")["id"];
        $arr = $this->common_model->dbSelect("*","parent_shopping_cart"," parent_id='$parent_id' ");
        if(count($arr)>0){
            $this->session->set_userdata("cart_count", count($arr));
        } else if(count($arr) == 0){
            $this->session->set_userdata("cart_count", 0);
        }
        if($res){
            redirect('parent/parents/checkout');
        }
    }

    function placeorder(){
        $item_ids = rtrim($this->input->post("item_ids"),",");
        $item_ids = explode(",",$item_ids);
        $quantity = rtrim($this->input->post("quantities"),",");
        $quantity = explode(",",$quantity);
        $amount_paid = $this->input->post("amount_paid");
        $reference_no = $this->input->post("reference_no");
        $parent_id = $this->session->userdata("student")["id"];
        $order = array(
            "amount_paid" => $amount_paid,
            "reference_no" => $reference_no,
            "parent_id" => $parent_id
        );
        $order_id = $this->common_model->dbInsert("orders",$order);
        foreach($item_ids as $key=>$item_id){
            $order_detail = array(
                "order_id" => $order_id,
                "item_id" => $item_id,
                "quantity" => $quantity[$key]
            );
            $this->common_model->dbInsert("order_details",$order_detail);
        }
        
        foreach($item_ids as $i){
            $this->db->where(array("item_id"=>$i, "parent_id" => $parent_id));
            $this->db->delete("parent_shopping_cart");
        }
        $this->session->set_userdata("cart_count",0);
        $response = array("status"=>"success","message"=>"order place successfully! Now you can collect your order from school anytime.");
        echo json_encode($response);
    }

    public function orders() {
        $parent_id = $this->session->userdata("student")["id"];
        $this->session->set_userdata('top_menu', 'Parents');
        $this->session->set_userdata('sub_menu', 'parents/orders');

        $xcrud = xcrud_get_instance();
        $xcrud->table('orders');
        $xcrud->where("parent_id=$parent_id AND deleted_at IS NULL ");
        $xcrud->show_primary_ai_field(false);
        $xcrud->relation('parent_id', 'students', 'parent_id', 'guardian_name');
        //$xcrud->relation('collected_by', 'students', 'id', array('guardian_name'));
        $xcrud->label("item_id", "Items");
        $xcrud->label("parent_id", "Order By");
        $xcrud->label("quantity", "Quantities");
        $xcrud->label("created_at", "Order Datetime");
        $xcrud->columns('parent_id,amount_paid, status, reference_no, created_at');
        $xcrud->fields('parent_id, amount_paid, status, reference_no,created_at');
        
        $order_list = $xcrud->nested_table('item_id','id','order_details','order_id');
        $order_list->relation('item_id', 'school_store', 'id', 'item_store');
        $order_list->relation('collected_by', 'students', 'id', array('firstname','lastname'));
        $order_list->columns("item_id,quantity,is_collected, collected_by, updated_at");
        //$order_list->fields("item_id,quantity,is_collected, collected_by");
        $order_list->label("updated_at","Collection Datetime");
        $order_list->label("item_id","Name");
        $order_list->unset_title();
        $order_list->unset_add();
        $order_list->unset_view();
        $order_list->unset_remove();
        $order_list->unset_edit();
        $order_list->unset_print();
        $order_list->unset_csv();
        $order_list->unset_search();
        $order_list->unset_limitlist();

        $xcrud->column_callback('amount_paid','currency_callback');
        $xcrud->column_callback('item_id','concate_items');
        $xcrud->column_callback('quantity','quantities_callback');
        $xcrud->unset_title();
        $xcrud->unset_add();
        $xcrud->unset_remove();
        $xcrud->unset_edit();
        $xcrud->unset_print();
        $xcrud->unset_csv();
        $xcrud->unset_limitlist();

        $data['orders'] = $xcrud->render();
        $this->load->view('layout/parent/header', $data);
        $this->load->view('parent/orders', $data);
        $this->load->view('layout/parent/footer', $data);
    }

    public function searchItems(){
        $searchBy = $this->input->post("searchBy");
        $sql = "SELECT s.* FROM school_store s INNER JOIN item_category c ON s.item_category_id=c.id WHERE s.item_store LIKE '%$searchBy%' AND s.deleted_at IS NULL AND c.type='store_category' ";
        $Items = $this->common_model->dbQuery($sql);
        $newItems = "";
        if(count($Items) > 0){
            foreach($Items as $item){
                $disabled = "";
                if($item->stock <= 0){
                    $disabled = "disabled";
                }
                $newItems .= '<div class="col-md-3 text-center" style="margin-bottom: 15px;">
                    <div class="col-md-12" style="border: 1px solid orange; border-radius: 10px;">
                        <img src="'.base_url()."uploads/schoolstore/".$item->picture.'" style="height: 150px; width: 100%; margin-top: 5px;"/>
                        <h5 class="text-center">'.$item->item_store.'</h5>
                        <p class="text-center">
                            <strong style="border-right: 1px solid; padding-right: 5px;">Stock: '.$item->stock.'</strong>
                            <span class="text-danger">'.$this->session->userdata("student")["currency_symbol"].$item->price.'</span>
                        </p>
                        <form action="'.site_url("parent/parents/savetocart").'" method="post">
                            <input type="hidden" name="item_id" value="'.$item->id.'"/>
                            <table style="margin-bottom: 15px;" align="center">
                                <tr>
                                    <td><strong>Qty</strong></td>
                                    <td style="padding-left: 5px;" class="text-right">
                                        <input type="number" max="'.$item->stock.'" '.$disabled.' min="1" name="quantity" style="height: 30px; width: 100%;" />
                                        <span class="text-danger">'.form_error("quantity") .'</span>
                                    <td>
                                    <td style="padding-left: 5px;" class="text-left">
                                        <button type="submit" class="btn btn-info btn-sm" '.$disabled.'>Add to cart</button>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>';
            }
        } else {
            $newItems .= "<li class='list-group-item text-danger'><a href='javascript:void(0);'>No item found.</a></li>";
        }
        echo $newItems;
    }
}

?>