<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mailsmsconf {

    public function __construct() {
        $this->CI = & get_instance();
        $this->CI->config->load("mailsms");
        $this->CI->load->library('smsgateway');
        $this->CI->load->library('mailgateway');
        $this->CI->load->model('examresult_model');
        $this->CI->load->model('student_model');
        $this->config_mailsms = $this->CI->config->item('mailsms');
    }

    public function mailsms($send_for, $sender_details, $date = NULL, $exam_schedule_array = NULL) {

        $send_for = $this->config_mailsms[$send_for];
//=========
        $chk_mail_sms = $this->CI->customlib->sendMailSMS($send_for);

        if (!empty($chk_mail_sms)) {
            if ($send_for == "student_admission") {
                if ($chk_mail_sms['mail'] && $chk_mail_sms['template'] !="") {
                    $this->CI->mailgateway->sentRegisterMail($sender_details['student_id'], $sender_details['email'],$chk_mail_sms['template']);
                }
                if ($chk_mail_sms['sms'] && $chk_mail_sms['template'] !="") {
                    $this->CI->smsgateway->sentRegisterSMS($sender_details['student_id'], $sender_details['contact_no'],$chk_mail_sms['template']);
                }
            } elseif ($send_for == "exam_result") {

                $this->sendResult($chk_mail_sms, $sender_details, $exam_schedule_array,$chk_mail_sms['template']);

            } elseif ($send_for == "login_credential") {
                
                if ($chk_mail_sms['mail'] && $chk_mail_sms['template'] !="") {

                    $this->CI->mailgateway->sendLoginCredential($chk_mail_sms, $sender_details,$chk_mail_sms['template']);
                 
                }
                if ($chk_mail_sms['sms'] && $chk_mail_sms['template'] !="") {
                    $this->CI->smsgateway->sendLoginCredential($chk_mail_sms, $sender_details,$chk_mail_sms['template']);
                }
            } elseif ($send_for == "fee_submission") {
                // $invoice = json_decode($sender_details->invoice);

                if ($chk_mail_sms['mail'] && $chk_mail_sms['template'] !="")  {
                    $this->CI->mailgateway->sentAddFeeMail($sender_details,$chk_mail_sms['template']);
             
                }
                
                if ($chk_mail_sms['sms'] && $chk_mail_sms['template'] !="") {
                    $this->CI->smsgateway->sentAddFeeSMS($sender_details,$chk_mail_sms['template']);
                }
            } elseif ($send_for == "absent_attendence") {

                $this->sendAbsentAttendance($chk_mail_sms, $sender_details, $date,$chk_mail_sms['template']);
            } elseif ($send_for == "fees_reminder") {
                
          

                if ($chk_mail_sms['mail'] && $chk_mail_sms['template'] !="")  {
                    $this->CI->mailgateway->sentMail($sender_details,$chk_mail_sms['template'],'Fees Reminder');
             
                }
           
                if ($chk_mail_sms['sms'] && $chk_mail_sms['template'] !="") {
                    $this->CI->smsgateway->sendSMS($sender_details->guardian_phone,$chk_mail_sms['template'],$sender_details);
                }

            }  else {
                
            }
        }
    
        //===============
    }

    public function sendResult($chk_mail_sms, $exam_result, $exam_schedule_array,$template) {
        if ($chk_mail_sms['mail'] OR $chk_mail_sms['sms']) {
            $get_result = $this->getStudentResult($exam_result, $exam_schedule_array);
        

            foreach ($get_result as $res_key => $res_value) {
                $result = "passed";
                $exam_name = "";
                $total_marks = 0;
                $achive_marks = 0;
                $student_name = "";
                $guardian_phone = "";
                $email = "";
                foreach ($res_value as $each_key => $each_value) {
                    $subject_passing_marks = $each_value['passing_marks'];
                    $subject_get_marks = $each_value['get_marks'];
                    if ($subject_passing_marks > $subject_get_marks) {
                        $result = "failed";
                    }

                    $total_marks = $total_marks + $each_value['full_marks'];
                    $achive_marks = $achive_marks + $each_value['get_marks'];
                    $student_name = $each_value['firstname'] . " " . $each_value['lastname'];
                    $email = $each_value['email'];
                    $exam_name = $each_value['exam_name'];
                    $guardian_phone = $each_value['guardian_phone'];
                }

                //===========send Mail and sms================
                $detail = array(
                    'result' => $result,
                    'total_marks' => $total_marks,
                    'achive_marks' => $achive_marks,
                    'student_name' => $student_name,
                    'exam_name' => $exam_name,
                    'email' => $email,
                    'guardian_phone' => $guardian_phone,
                );
                if ($chk_mail_sms['mail']) {
                    $this->CI->mailgateway->sentExamResultMail($detail,$template);
                }
                if ($chk_mail_sms['sms']) {
                    $this->CI->smsgateway->sentExamResultSMS($detail,$template);
                }
            }
        }
    }

    public function getStudentResult($ex_array, $exam_schedule_array) {
        $result = array();
        $exam_schedule = implode(',', $exam_schedule_array);
        foreach ($ex_array as $ex_k => $ex_v) {
            $result[] = $this->CI->examresult_model->getStudentExamResultByStudent($ex_v, $ex_k, $exam_schedule);
        }
        return $result;
    }

    public function sendAbsentAttendance($chk_mail_sms, $student_session_array, $date,$template) {

        if ($chk_mail_sms['mail'] OR $chk_mail_sms['sms']) {
            $student_result = $this->getAbsentStudentlist($student_session_array);
            if (!empty($student_result)) {
                foreach ($student_result as $student_result_k => $student_result_v) {
                    $detail = array(
                        'date' => $date,
                        'firstname' => $student_result_v->firstname,
                        'lastname' => $student_result_v->lastname,
                        'mobileno' => $student_result_v->mobileno,
                        'email' => $student_result_v->email,
                        'father_name' => $student_result_v->father_name,
                        'father_phone' => $student_result_v->father_phone,
                        'father_occupation' => $student_result_v->father_occupation,
                        'mother_name' => $student_result_v->mother_name,
                        'mother_phone' => $student_result_v->mother_phone,
                        'guardian_name' => $student_result_v->guardian_name,
                        'guardian_phone' => $student_result_v->guardian_phone,
                        'guardian_occupation' => $student_result_v->guardian_occupation,
                        'guardian_email' => $student_result_v->guardian_email
                    );
       
                    if ($chk_mail_sms['mail']) {
                        $this->CI->mailgateway->sentAbsentStudentMail($detail,$template);
                       
                    }
                    if ($chk_mail_sms['sms']) {

                        $this->CI->smsgateway->sentAbsentStudentSMS($detail,$template);
                    }
                }
            }
        }
    }

    public function getAbsentStudentlist($student_session_array) {

        $result = $this->CI->student_model->getStudentListBYStudentsessionID($student_session_array);
        if (!empty($result)) {
            return $result;
        }
        return false;
    }




}
