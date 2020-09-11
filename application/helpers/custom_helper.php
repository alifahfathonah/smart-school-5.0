<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

if (!function_exists('is_subAttendence')) {

    function is_subAttendence()
    {

        $CI = &get_instance();
        $CI->db->select('sch_settings.id,sch_settings.lang_id,sch_settings.attendence_type,sch_settings.is_rtl,sch_settings.timezone,
          sch_settings.name,sch_settings.email,sch_settings.biometric,sch_settings.biometric_device,sch_settings.phone,languages.language,
          sch_settings.address,sch_settings.dise_code,sch_settings.date_format,sch_settings.currency,sch_settings.currency_symbol,sch_settings.start_month,sch_settings.session_id,sch_settings.image,sch_settings.theme,sessions.session'
        );
        $CI->db->from('sch_settings');
        $CI->db->join('sessions', 'sessions.id = sch_settings.session_id');
        $CI->db->join('languages', 'languages.id = sch_settings.lang_id');
        $CI->db->order_by('sch_settings.id');
        $query  = $CI->db->get();
        $result = $query->row();
    
        if ($result->attendence_type) {
            return true;
        }
        return false;

    }

}

if (!function_exists('get_subjects')) {

    function get_subjects($class_batch_id)
    {
        $CI = &get_instance();
        $CI->db->select('class_batch_subjects.*,subjects.name as `subject_name`');
        $CI->db->from('class_batch_subjects');
        $CI->db->join('subjects', 'subjects.id = class_batch_subjects.subject_id');
        $CI->db->where('class_batch_id', $class_batch_id);
        $CI->db->order_by('class_batch_subjects.id', 'asc');

        $query         = $CI->db->get();
        $return_string = '<option value="">--Select--</option>';
        $result        = $query->result();
        if (!empty($result)) {
            foreach ($result as $result_key => $result_value) {
                $return_string .= '<option value="' . $result_value->id . '">' . $result_value->subject_name . '</option>';
            }
        }
        return $return_string;
    }

}

if (!function_exists('readmorelink')) {

    function readmorelink($string, $link = false)
    {
        $string = strip_tags($string);
        if (strlen($string) > 150) {

            // truncate string
            $stringCut = substr($string, 0, 150);
            $endPoint  = strrpos($stringCut, ' ');

            //if the string doesn't contain any space then it will cut without word basis.
            $string = $endPoint ? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
            $string .= ($link) ? "<a href='" . $link . "' target='_blank'>Read more...</a>" : "....";
        }

        return $string;
    }

}

if (!function_exists('readmorelinkUser')) {

    function readmorelinkUser($string, $link = false)
    {
        $string = strip_tags($string);
        if (strlen($string) > 150) {

            // truncate string
            $stringCut = substr($string, 0, 150);
            $endPoint  = strrpos($stringCut, ' ');

            //if the string doesn't contain any space then it will cut without word basis.
            $string = $endPoint ? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);

            $string .= ($link) ? "<a href='#" . $link . "' data-toggle='collapse' aria-expanded='false' aria-controls='" . $link . "' >Read more...</a>" : "....";
        }

        return $string;
    }

}

function expensegraphColors($color = null)
{

    $colors = array(
       '1' => "#9966ff",
        '2' => "#36a2eb",
        '3' => "#ff9f40",
        '4' => "#715d20",
        '5' => "#c9cbcf",
        '6' => "#4bc0c0",
        '7' => "#ffcd56",
        '8' => "#66aa18",
    );
    if ($color == null) {
        return $colors;
    } else {
        return $colors[$color];
    }
}
function incomegraphColors($color = null)
{

    $colors = array(
        '1' => "#66aa18",
        '2' => "#ffcd56",
        '3' => "#4bc0c0",
        '4' => "#c9cbcf",
        '5' => "#715d20",
        '6' => "#ff9f40",
        '7' => "#36a2eb",
        '8' => "#9966ff",
    );
    if ($color == null) {
        return $colors;
    } else {
        return $colors[$color];
    }

}
function isJSON($string)
{
    return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
}

function currentTime()
{
    return date("d/m/y : H:i:s", time());
}

function markSheetDigit()
{
    $number   = 190908100.25;
    $no       = floor($number);
    $point    = round($number - $no, 2) * 100;
    $hundred  = null;
    $digits_1 = strlen($no);
    $i        = 0;
    $str      = array();
    $words    = array('0' => '', '1'          => 'one', '2'       => 'two',
        '3'                   => 'three', '4'     => 'four', '5'      => 'five', '6' => 'six',
        '7'                   => 'seven', '8'     => 'eight', '9'     => 'nine',
        '10'                  => 'ten', '11'      => 'eleven', '12'   => 'twelve',
        '13'                  => 'thirteen', '14' => 'fourteen',
        '15'                  => 'fifteen', '16'  => 'sixteen', '17'  => 'seventeen',
        '18'                  => 'eighteen', '19' => 'nineteen', '20' => 'twenty',
        '30'                  => 'thirty', '40'   => 'forty', '50'    => 'fifty',
        '60'                  => 'sixty', '70'    => 'seventy',
        '80'                  => 'eighty', '90'   => 'ninety');
    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
    while ($i < $digits_1) {
        $divider = ($i == 2) ? 10 : 100;
        $number  = floor($no % $divider);
        $no      = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number) {
            $plural  = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str[]   = ($number < 21) ? $words[$number] .
            " " . $digits[$counter] . $plural . " " . $hundred
            :
            $words[floor($number / 10) * 10]
                . " " . $words[$number % 10] . " "
                . $digits[$counter] . $plural . " " . $hundred;
        } else {
            $str[] = null;
        }

    }
    $str    = array_reverse($str);
    $result = implode('', $str);
    $points = ($point) ?
    "." . $words[$point / 10] . " " .
    $words[$point = $point % 10] : '';
    return $result . $points;

}

function find_marks($student_id, $exam_detail_id, $marks) {
    foreach ($marks as $key => $m) {
        if ($exam_detail_id == $m->exam_detail_id && $student_id == $m->student_id) {
            return $key;
        }
    }
    return -1;
}

function render_resultcard_tags($data, $html) {
    $ci = & get_instance();
    
    $logo = base_url().'uploads/school_content/admin_small_logo/1.png';
    $form = $data["template"];
    $data2 = $data["data"];
    $min_passing_percentage = "0";
    $yas_exam_id = $ci->common_model->dbSelect("id","sh_exams"," title='".$data2['exam_name']."' ")[0]->id;
    $rules = $ci->common_model->dbSelect("*","sh_passing_rules", " class_id=".$data2['class_id']." AND batch_id='".$data2['batch_id']."' AND exam_id=$yas_exam_id AND deleted_at IS NULL ");
    if(count($rules) > 0){
        $min_passing_percentage = $rules[0]->minimum_percentage;
    }

    $tags = array();
    $tags["date"] = date("d/m/Y");
    $tags["time"] = date("h:i:s");
    $tags["school_name"] = $ci->session->userdata("admin")["school_name"]; //froms ession
    $tags["school_email"] = $ci->session->userdata("admin")["email"]; //froms ession
    $tags["school_phone"] = "00000000000"; //froms ession
    $tags["school_address"] = "None"; //froms ession
    $tags["school_website"] = base_url(); //froms ession
    $tags["school_country"] = "None"; //froms ession
    $tags["school_city"] = "None"; //froms ession
    $tags["school_logo"] = "<img src='$logo' id='school-logo' style='width:120px;'/>";
    $tags["logged_in_user"] = $ci->session->userdata("admin")["username"];
    $tags["form_title"] = $form->name;
    $tags["student_name"] = $data2["student_name"];
    $tags["father_name"] = $data2["father_name"];
    $tags["class_name"] = $data2["class_name"];
    $tags["section_name"] = $data2["section_name"];
    $tags["phone_number"] = $data2["mobile_phone"];
    $tags["student_rollno"] = $data2["rollno"];
    $tags["student_profile"] = "<img id='profile-img' src='".base_url(). $data2["avatar"] . "' alt='".base_url()."uploads/student_images/no_image.png' style='border:2px solid #222222;' width='140px'/>";
    $tags["exam_name"]=$data2["exam_name"];
    $tags["passing_percentage"]= $min_passing_percentage."%";
    $tags["attendance"]="OK";
    $tags["class_activity"]="Active";
    $tags["conduct"]="Good";
    $tags["class_teacher_remarks"]=$data2["class_teacher_remarks"];
    $tags["position"]=$data2["position"];
    $tags["session_name"]=$data2["session_name"];
    
    /*$active_academic_year_array = $ci->common_model->dbSelect("*", "academic_years", " school_id='$school_id' AND deleted_at IS NULL AND is_active='Y' ");
    $active_academic_year = array();
    if (count($active_academic_year_array) > 0) {
        $active_academic_year = $active_academic_year_array[0]->name;
    }*/
    
    $grades_array = $ci->common_model->dbSelect("*", "sh_grades", " deleted_at IS NULL ORDER BY percent_upto desc ");
    $grades = array();
    if (count($grades_array) > 0) {
        $grades = $grades_array;
    }
    
    //--------------Exam detail table----------------//
    $result_details = "";
    $exam_total_marks = 0;
    $exam_obtained_total_marks = 0;
    foreach($data["data"]["details"] as $d) {
        if($d->obtained_marks != null || !empty($d->obtained_marks)) { 
            $exam_total_marks += intval($d->total_marks);
            if($d->type == 'number') {
                $exam_obtained_total_marks += intval($d->obtained_marks);
            }
            //------------------------Subject Percentage-------------------------//
            $sub_grade = NULL;
            $subject_percentage = null;
            if ($d->total_marks != 0) {
                $subject_percentage = round(($d->obtained_marks * 100) / $d->total_marks);
            }
            foreach ($grades as $g) {
                if ($subject_percentage >= $g->percent_from && $subject_percentage <= $g->percent_upto) {
                    $sub_grade = $g->name;
                }
            }
            //------------------------------------------------------------------//
            if($d->type == 'number'){
                $result_details .= '<tr><td style="text-align: center;">'.$d->subjnect_name.'</td>
                    <td style="text-align: center;">'.$d->total_marks.'</td>
                    <td style="text-align: center;">'.$d->obtained_marks.'</td>
                    <td style="text-align: center;">'.$sub_grade.'</td>
                    <td style="text-align: center;">'.lang(strtolower($d->status)).'</td>
                    <td style="text-align: center;">'.$d->remarks.'</td>
                </tr>';
        }else{
            $result_details .= '<tr><td style="text-align: center;">'.$d->subjnect_name.'</td>
                <td style="text-align: center;">-</td>
                <td style="text-align: center;">-</td>
                <td style="text-align: center;">'.$d->grade.'</td>
                <td style="text-align: center;">-</td>
                <td style="text-align: center;">'.$d->remarks.'</td>
            </tr>';
        }
        }
    }
    $tags["exam_total_marks"] = $exam_total_marks;
    $tags["exam_obtained_total_marks"] = $exam_obtained_total_marks;
    
    $percentage = "";
    if ($exam_total_marks != 0) {
        $percentage = ($exam_obtained_total_marks * 100) / $exam_total_marks;
    }
    //---------------------------------------------------------//
    
    //-------------------Calculate pass/fail status--------------------//
    $pass_fail_status = null;
    if(count($rules)>0){
        $number_of_subjects_passed = 0;
        $pass_fail_status = lang('fail');
        foreach ($data["data"]["details"] as $sub) {
            if ($sub->status == "Pass") {
                $number_of_subjects_passed++;
            }
        }
        if ($rules[0]->operator == "AND") {
            if($number_of_subjects_passed >= $rules[0]->minimum_subjects && $percentage >= $rules[0]->minimum_percentage) {
                $pass_fail_status = lang("pass");
            }
        } else if($rules[0]->operator == "OR") {
            if($number_of_subjects_passed >= $rules[0]->minimum_subjects || $percentage >= $rules[0]->minimum_percentage) {
                $pass_fail_status = lang("pass");
            }
        }
    } else {
        $pass_fail_status = lang('pass');
        foreach ($data["data"]["details"] as $sub) {
            if ($sub->status == "Fail") {
                $pass_fail_status = lang('fail');
                break;
            }
        }
    }
    $tags["result_status"]=$pass_fail_status;
    //----------------------------------------------------------------//
    
    $tags["result_details"]=$result_details;
    $tags["obtained_percentage"]=intval($percentage)."%";
    
    //-------------------------------------------//
    $grading_table = "";
    //$grading_table .= '<table width="100%"><tr><td colspan="6" class="text-center"><h4><b><u>'.lang("lbl_grading_system").'</u></b></h4></td></tr>';
    if (count($grades) > 0) {
        foreach ($grades as $g) {
            $grading_table .= '<tr class="text-center"><td>' . $g->percent_from . '</td><td>-</td><td>' . $g->percent_upto . '</td><td>%</td><td>' . $g->name . '</td><td class="text-left">' . $g->description . '&nbsp;&nbsp;&nbsp;</td></tr>';
        }
    } else {
        $grading_table .= '<tr><td colspan="6">' . lang("no_record") . '</td></tr>';
    }
    //$grading_table .= '</table>';
    $tags["grading_details"]=$grading_table;
    //-------------------------------------------//
    
    $rendered_html = $html->html;
    foreach ($tags as $key => $value) {
        $rendered_html = str_replace("{" . $key . "}", $tags[$key], $rendered_html);
    }

    return $rendered_html;
}

function render_complete_resultcard_single_tags($data, $html){
    $ci = & get_instance();
    $min_passing_percentage = 0;
    $logo = base_url().'uploads/school_content/admin_small_logo/1.png';
    $final_term_name = $data["data"]->final_term;   
    $data2 = $data["data"];
    $form = $data["template"];
    $class_name = $ci->common_model->dbSelect("class","classes"," id='$data2->class_id' ")[0]->class;
    $section_name = $ci->common_model->dbSelect("section","sh_batches"," class_id='$data2->class_id' AND section_id='$data2->section_id' ")[0]->section;
    $grades = $ci->common_model->dbSelect("*","sh_grades"," deleted_at IS NULL ");
    $tags = array();
    $tags["date"] = date("d/m/Y");
    $tags["time"] = date("h:i:s");
    $tags["school_name"] = $data2->school_name; //froms ession
    $tags["school_email"] = $data2->school_email; //froms ession
    $tags["school_phone"] = $data2->school_phone; //froms ession
    $tags["school_address"] = $data2->school_address; //froms ession
    $tags["school_website"] = base_url(); //froms ession
    $tags["school_country"] = "None"; //froms ession
    $tags["school_city"] = "None"; //froms ession
    $tags["school_logo"] = "<img src='$logo' id='school-logo' style='width:100px;'/>";
    $tags["logged_in_user"] = $ci->session->userdata("admin")["username"];
    $tags["form_title"] = $form->name;
    $tags["student_name"] = $data2->student_name;
    $tags["father_name"] = $data2->father_name;
    $tags["class_name"] = $class_name;
    $tags["section_name"] = $section_name;
    $tags["student_rollno"] = $data2->rollno;
    $tags["addmission_no"] = $data2->admission_no;
    $tags["student_gender"] = $data2->student_gender;
    $tags["student_dob"] = $data2->student_dob;
    
    $class_population = $ci->common_model->dbSelect("count(*) as count"," students ", " id IN (SELECT student_id FROM student_session WHERE class_id='$data2->class_id' AND section_id='$data2->section_id' AND session_id='$data2->session_id') ")[0]->count;
    $date1 = date('Y-m-d',strtotime($data2->student_dob));
    $date2 = date('Y-m-d',strtotime(date("Y-m-d")));
    $diff = date_diff(date_create($date1), date_create($date2));
    $tags["age_count"] = $diff->format("%y");
    
    $tags["student_profile"] = "<img id='profile-img' src='".base_url(). $data2->student_avatar . "' alt='".base_url()."uploads/student_images/no_image.png' style='border:1px solid #222222;' width='100px'/>";
    $tags["exam_name"] = $data2->final_term;
    $tags["passing_percentage"]= $min_passing_percentage."%";
    $tags["attendance"]="OK";
    $tags["class_activity"]="Active";
    $tags["conduct"]="Good";
    //$tags["class_teacher_remarks"]=$data2["class_teacher_remarks"];
    $tags["position"]=$data2->new_position;
    $tags["session_name"] = $data2->session_name;

    $exams = $data2->subjects[0]->exams;
    $exam_cols = "";
    $colspan = 7;
    foreach($exams as $e){
        $exam_cols .= "<th class='text-center'>".$e->exam_name."</th>";
        $colspan++;
    }
    
    $complete_result_details = "<table class='complete_result_card_details_table' border='1px' width='100%'><tr style='background: black; color: white;'><th colspan='$colspan' class='text-center first-row-style'>TERM REPORT</th>";
    if(count($data2->subjects) > 0 && count($data2->subjects[0]->prev_term_data) > 0){
        $complete_result_details .= "<th colspan='6' class='text-center'>CUMULATIVE RECORD</th></tr>";
    }
    $complete_result_details .= "<tr style='background: black; color: white;'>
        <th class='text-center'>Subjects</th>".$exam_cols."
        <th class='text-center'>Total Score (100)</th>
        <th class='text-center'>Class Avg Score</th>
        <th class='text-center'>Class High Score</th>
        <th class='text-center'>Class Low Score</th>
        <th class='text-center'>Class Subject Position</th>
        <th class='text-center'>Term Subject Grade</th>";
    
    if(count($data2->subjects) > 0 && count($data2->subjects[0]->prev_term_data) > 0){
        foreach($data2->subjects as $sbb){
            foreach($sbb->prev_term_data as $prev){
                $complete_result_details .= "<th class='text-center'>".$prev->term_name."</th>";
            }
            $complete_result_details .= "<th class='text-center'>Cum Score (100%)</th>";
            $complete_result_details .= "<th class='text-center'>Subject Cum Position</th>";
            $complete_result_details .= "<th class='text-center'>Cum Grade</th>";
            break;
        }
    }

    $complete_result_details .= "</tr>";
    foreach($data2->subjects as $sb){
        $complete_result_details .= "<tr class='text-center'><td class='text-left' style='padding-left: 5px;'>".$sb->name."</td>";
        foreach($sb->exams as $e){
            $complete_result_details .= "<td>".$e->obtained_marks."</td>";
        }
        $grade = "<span style='color: ".$sb->grade_color."'>".$sb->grade."</span>";
        $complete_result_details .= "<td>".$sb->obtained_total."</td>";
        $complete_result_details .= "<td>".$sb->subject_avg_score."</td>";
        $complete_result_details .= "<td>".$sb->high_score."</td>";
        $complete_result_details .= "<td>".$sb->low_score."</td>";
        $complete_result_details .= "<td>".$sb->position."</td>";
        $complete_result_details .= "<td>".$grade."</td>";
        
        if(count($data2->subjects) > 0 && count($data2->subjects[0]->prev_term_data) > 0){
            foreach($sb->prev_term_data as $prev1){
                $complete_result_details .= "<td>".$prev1->obtained_total."</td>";
            }
            $cumulative_grade = "<span style='color: ".$sb->cum_grade_color."'>".$sb->cum_grade."</span>";
            $complete_result_details .= "<td>".$sb->cum_score."</td>";
            $complete_result_details .= "<td>".$sb->position."</td>";
            $complete_result_details .= "<td>".$cumulative_grade."</td>";
        }
        $complete_result_details .= "</tr>";
    }

    $grade_analyses = "<p style='border: 1px solid; padding: 10px; text-align: left;'><b>Grade Analysis: </b>";
    foreach($grades as $g){
        $grade_analyses .= " <strong><i>".$g->name."</i></strong> (".$g->percent_from."-".$g->percent_upto.")";
    }
    $grade_analyses .= "</p>";
    $complete_result_details .= "</table>".$grade_analyses;
    
    $tags["complete_result_details"] = $complete_result_details;
    $tags["result_graph"] = "<img id='profile-img' src='".base_url(). "uploads/chart/".$data2->student_id.".jpeg' alt='".base_url()."uploads/student_images/no_image.png' style='width: 250px; float:right;'/>";

    $result_analysis = "<table class='result_analysis_table' border='1px' style='border: 1px solid; width: 100%;'><tr style='background: black; color: white;'><td class='text-center' colspan='2'><b>RESULT ANALYSIS</b></td></tr>";
    $result_analysis .= "<tr><td class='text-left' style='padding-left: 5px;'>CLASS CUMULATIVE POSITION</td><td>PENDING</td></tr>";
    $result_analysis .= "<tr><td class='text-left' style='padding-left: 5px;'>CLASS TERM POSITION</td><td>PENDING</td></tr>";
    $result_analysis .= "<tr><td class='text-left' style='padding-left: 5px;'>CLASS ARM POSITION</td><td>PENDING</td></tr>";
    $result_analysis .= "<tr><td class='text-left' style='padding-left: 5px;'>TOTAL TERM SCORE</td><td>PENDING</td></tr>";
    $result_analysis .= "<tr><td class='text-left' style='padding-left: 5px;'>TOTAL CUMULATIVE SOCRE</td><td>PENDING</td></tr>";
    $result_analysis .= "<tr><td class='text-left' style='padding-left: 5px;'>AVERAGE TERM SCORE (STUDENT)</td><td>PENDING</td></tr>";
    $result_analysis .= "<tr><td class='text-left' style='padding-left: 5px;'>AVERAGE TERM SOCRE (CLASS)</td><td>PENDING</td></tr>";
    $result_analysis .= "<tr><td class='text-left' style='padding-left: 5px;'>AVERAGE CUMULATIVE SCORE</td><td>PENDING</td></tr>";
    $result_analysis .= "<tr><td class='text-left' style='padding-left: 5px;'>NO OF SUBJECTS OFFERED</td><td>".count($data2->subjects)."</td></tr>";
    $result_analysis .= "<tr><td class='text-left' style='padding-left: 5px;'>NO OF SUBJECTS PASSED</td><td>PENDING</td></tr>";
    $result_analysis .= "<tr><td class='text-left' style='padding-left: 5px;'>NO OF SUBJECTS FAILED</td><td>PENDING</td></tr>";
    $result_analysis .= "<tr><td class='text-left' style='padding-left: 5px;'>ATTENDANCE A VIEW DETAILS</td><td>PENDING</td></tr>";
    $result_analysis .= "<tr><td class='text-left' style='padding-left: 5px;'>CLASS POPULATION</td><td>".$class_population."</td></tr>";
    $result_analysis .= "</table>";

    $teacher_and_administrator_remarks = null;
    $teacher_and_administrator_remarks .= "<table id='teacher_and_administrator_remarks_table' border='1px' style='border: 1px solid; width: 100%; margin-top: 15px;'><tr style='background: black; color: white;'><td class='text-left' style='padding-left: 5px;'><b>Teacher Comment's 1</b></td></tr>";
    $teacher_and_administrator_remarks .= "<tr><td class='text-left' style='padding-left: 5px;'>".$data2->teacher_remark."</td></tr>";
    $teacher_and_administrator_remarks .= "<tr style='background: black; color: white;'><td class='text-left' style='padding-left: 5px;'><b>Administrator Comment</b></td></tr>";
    $teacher_and_administrator_remarks .= "<tr><td class='text-left' style='padding-left: 5px;'>".$data2->administrator_remark."</td></tr>";
    $teacher_and_administrator_remarks .= "</table>";

    $sa_groups = $ci->common_model->dbSelect("id,assessment","sh_skill_and_assessment_groups"," class_id=$data2->class_id AND deleted_at IS NULL ");
    foreach($sa_groups as $grp){
        $record = $ci->common_model->dbSelect("id,evaluation","sh_student_skill_and_assessments"," student_id='$data2->student_id' AND sa_group_id=$grp->id AND deleted_at IS NULL ");
        $grp->evaluations = $record;
    }
    
    $std_skill_and_evaluations = "";
    if(count($sa_groups) > 0){
        $std_skill_and_evaluations = "<table id='std_skill_and_evaluations_table' width='100%' border='1px' style='border: 1px solid;'><tr style='background: black; color: white;'><td class='text-center' colspan='6'><b>Student's Skill & Evaluations</b></td></tr><tr><td style='padding-left: 5px;'><b>ASSESSEMENT...</b></td><td class='text-center'><b>1 (POOR)</b></td><td class='text-center'><b>2 (FAIR)</b></td><td class='text-center'><b>3 (GOOD)</b></td><td class='text-center'><b>4 (VERY GOOD)</b></td><td class='text-center'><b>5 (EXECELLET)</b></td></tr>";
        foreach($sa_groups as $grp2){
            $std_skill_and_evaluations .= "<tr><td style='padding-left: 5px;'><b>".$grp2->assessment."</b></td>";
            if(count($grp2->evaluations) > 0){
                $tick = "<i class='fa fa-check text-success'></i>";
                switch ($grp2->evaluations[0]->evaluation) {
                    case 'poor':
                        $std_skill_and_evaluations .= "<td>".$tick."</td><td>---</td><td>---</td><td>---</td><td>---</td>";
                        break;
                    case 'fair':
                        $std_skill_and_evaluations .= "<td>---</td><td>".$tick."</td><td>---</td><td>---</td><td>---</td>";
                        break;
                    case 'good':
                        $std_skill_and_evaluations .= "<td>---</td><td>---</td><td>".$tick."</td><td>---</td><td>---</td>";
                        break;
                    case 'verygood':
                        $std_skill_and_evaluations .= "<td>---</td><td>---</td><td>---</td><td>".$tick."</td><td>---</td>";
                        break;
                    case 'excellent':
                        $std_skill_and_evaluations .= "<td>---</td><td>---</td><td>---</td><td>---</td><td>".$tick."</td>";
                        break;
                }
            } else {
                $std_skill_and_evaluations .= "<td>---</td><td>---</td><td>---</td><td>---</td><td>---</td>";
            }
            $std_skill_and_evaluations .= "</tr>";
        }
        $std_skill_and_evaluations .= "</table>";
    }
    
    $tags["result_analysis"] = $result_analysis;
    $tags["student_skill_and_behaviour"] = $std_skill_and_evaluations;
    $tags["teacher_and_administrator_remarks"] = $teacher_and_administrator_remarks;

    $rendered_html = $html->html;
    foreach ($tags as $key => $value) {
        $rendered_html = str_replace("{" . $key . "}", $tags[$key], $rendered_html);
    }
    return $rendered_html;
}

function system_result_card_template_english(){
    return '<!DOCTYPE html><html><head></head><body><div class="result_card_container" style="direction: ltr;">
    <p align="center">{school_logo}</p><h3 class="text-center" style="text-align: center;" id="school-name"><strong>{school_name}</strong></h3>
    <h5 class="text-center" style="text-align: center;" id="exam-name">Examination {exam_name}</h5>
    <h5 class="text-center" style="text-align: center;" id="session-name"><span style="text-decoration: underline;">{session_name}</span></h5>
    <table style="width: 100%;" id="std-info-table"><tbody><tr><td><strong>Student Name</strong></td><td><u>{student_name}</u></td>
    <td><strong>Roll No.</strong></td><td><u>{student_rollno}</u></td><td style="text-align: right;" colspan="1" rowspan="3">{student_profile}</td>
    </tr><tr><td><strong>Father Name</strong></td><td><u>{father_name}</u></td><td><strong>Class / Section</strong></td>
    <td><u>{class_name}-{section_name}</u></td></tr><tr><td><strong>Result</strong></td><td><u>{result_status}</u></td>
    <td><strong>Position</strong></td><td><u>{position}</u></td></tr></tbody></table>
    <table id="result-marks-table" style="border: 1px solid black; margin-top: 15px;" border="1px solid" width="100%">
    <tbody><tr><td style="text-align: center;" colspan="6"><strong>Result</strong></td></tr><tr>
    <td style="text-align: center;"><strong>Subjects</strong></td><td style="text-align: center;"><strong>Total Marks</strong></td>
    <td style="text-align: center;"><strong>Obtain Marks</strong></td><td style="text-align: center;"><strong>Grade</strong></td>
    <td style="text-align: center;"><strong>Status</strong></td><td style="text-align: center;"><strong>Remarks</strong></td>
    </tr><tr class="hidden_row"><td colspan="6">{result_details}</td></tr><tr><td style="text-align: center;"><strong>Total</strong></td>
    <td style="text-align: center;">{exam_total_marks}</td><td style="text-align: center;" colspan="4">{exam_obtained_total_marks}</td>
    </tr></tbody></table><table id="other-info-table" style="border: 1px solid black; margin-top: 15px;" border="1px solid" width="100%">
    <tbody><tr><td>&nbsp;&nbsp;&nbsp;<strong>Pass Percentage:</strong>&nbsp;&nbsp;{passing_percentage}</td>
    <td>&nbsp;&nbsp;&nbsp;<strong>Obtained Percentage: </strong>&nbsp;&nbsp;{obtained_percentage}</td></tr><tr>
    <td>&nbsp;&nbsp;&nbsp;<strong>Attendance:</strong> {attendance}</td><td>&nbsp;&nbsp;&nbsp;<strong>Class Activity:</strong>
    &nbsp;&nbsp;{class_activity}</td></tr><tr><td colspan="2">&nbsp;&nbsp;&nbsp;<strong>Conduct:</strong> {conduct}</td></tr><tr>
    <td class="text-center"><table width="100%" id="grading-table"><tbody><tr><td class="text-center" colspan="6">
    <h4><strong><u>Grading System</u></strong></h4></td></tr><tr class="hidden_row"><td colspan="6">{grading_details}</td>
    </tr></tbody></table></td><td style="max-width: 50%; width: 50%;">
    <table class="table table-default" id="teacher-remarks-table" style="margin-bottom: 0;"><tbody><tr>
    <td style="padding: 5px 5px 0px 15px;"><h5><em style="box-sizing: inherit;">Class Teacher`s Remarks</em></h5></td></tr>
    <tr><td style="height: 60px; max-height:80px;" id="teacher-remark-place">{class_teacher_remarks}</td></tr>
    <tr><td style="padding: 5px 5px 0px 15px;"><h5><span style="box-sizing: inherit; font-weight: bolder;">
    <em style="box-sizing: inherit;">Keep it up</em></span></h5></td></tr><tr><td><em>Signature of Class Teacher:</em>&nbsp;<br />Date:</td></tr>
    </tbody></table></td></tr></tbody></table><table width="100%" id="footer-table"><tbody><tr>
    <td><strong>Sec. Head Sign:</strong><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br /><strong>Parent`s Sign</strong><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></td>
    <td class="text-center"><strong><label>PRINCIPAL</label></strong><br /><strong>Date:&nbsp;&nbsp;&nbsp;{date}</strong></td></tr></tbody>
    </table></div></body></html>';    
}

function system_result_card_template_english2() {
    return '<!DOCTYPE html>
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1">
        </head>
        <body>
            <div class="result_card_container" style="direction: ltr;">
                <table width="100%" border="0px" style="border-collapse: collapse;">
                    <tr style="border-bottom: 1px solid black;">
                        <td width="10%">{school_logo}</td>
                        <td style="width: 90%; padding-left: 10px; text-align:left; padding-bottom: 10px;">
                            <h3 style="margin-bottom: 0;">{school_name}</h3>
                            <span><i>......purposeful education is only the best</i></span><br/>
                            <span><b>Address: </b>{school_address}</span><br/>
                            <span><b>Telephone: </b>{school_phone}</span><br/>
                            <span><b>Email: </b>{school_email}</span>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid black;">
                        <td colspan="2" width="100%">
                            <p><b>Report For:</b> {exam_name}, {session_name} ( Next Term Resumes: 07-january-2019 )</p>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid black;">
                        <td colspan="2" width="100%" style="padding-bottom: 0px;">
                            <table width="100%" border="0px">
                                <tr>
                                    <th style="text-align: left;"><i>Full Name (Gender)</i></th>
                                    <td style="text-align:left;"><i>{student_name} ({student_gender})</i></td>
                                    <td rowspan="4" style="width:120px; text-align:right;">{student_profile}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;"><i>Addmission Number</i></th>
                                    <td style="text-align:left;"><i>{addmission_no}</i></td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;"><i>Class (Arm)</i></th>
                                    <td style="text-align:left;"><i>{class_name} ({section_name})</i></td>
                                </tr>
                                <tr>
                                    <th style="text-align: left;"><i>Date of Birth</i></th>
                                    <td style="text-align:left;"><i>{student_dob} ({age_count})</i></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table width="100%">
                                <tr>
                                    <td width="60%" style="vertical-align:top; padding-top:5px;">{complete_result_details}</td>
                                    <td width="40%" style="vertical-align:top; padding-left:5px; padding-top: 5px;">
                                        {student_skill_and_behaviour}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table width="100%">
                                <tr>
                                    <td width="60%" style="vertical-align:top;">{result_analysis}{teacher_and_administrator_remarks}</td>
                                    <td width="40%" style="vertical-align:top; padding-left: 10px;"></td>
                                </tr>
                            </table>
                        </td>
                        
                    </tr>
                </table>
            </div>
        </body>
    </html>';
}

function save_image($base64_string, $student_id) {
    $image_parts = explode(";base64,", $base64_string);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1];
    $image_base64 = base64_decode($image_parts[1]);
    $file = 'uploads/chart/'. $student_id .'.' . $image_type;
    file_put_contents($file, $image_base64);
    return $file;
}

function save_image2($base64_string, $path) {
    $image_parts = explode(";base64,", $base64_string);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1];
    $image_base64 = base64_decode($image_parts[1]);
    $file = uniqid() . '.' . $image_type;
    file_put_contents($path.$file, $image_base64);
    return $file;
}

function to_mysql_date($date) {
    //'12/12/2018

    if (empty($date)) {
        return "";
    }

    $myDateTime = DateTime::createFromFormat('d/m/Y', $date);
    if (gettype($myDateTime) == 'boolean') {
        return "error";
    }


    return $myDateTime->format('Y-m-d');
}

function to_html_date($date) {
    //'2012-12-01
    if (empty($date) || $date =='0000-00-00') {
        return "";
    }

    $myDateTime = DateTime::createFromFormat('Y-m-d', $date);
    return $myDateTime->format('d/m/Y');
}