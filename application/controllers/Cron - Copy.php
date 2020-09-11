<?php
 if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cron extends CI_Controller
{
/**
 * This is default constructor of the class
 */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('feereminder_model');
    }
    public function autobackup($key = '')
    {

        $setting_result      = $this->setting_model->get();
        $data['settinglist'] = $setting_result;

        $cronkey = $setting_result[0]["cron_secret_key"];
        if ($cronkey == $key) {

            $this->load->dbutil();
            $filename = "db-" . date("Y-m-d_H-i-s") . ".sql";
            $prefs    = array(
                'ignore'     => array(),
                'format'     => 'txt',
                'filename'   => 'mybackup.sql',
                'add_drop'   => true,
                'add_insert' => true,
                'newline'    => "\n",
            );
            $backup = $this->dbutil->backup($prefs);
            $this->load->helper('file');
            write_file('./backup/database_backup/' . $filename, $backup);
            echo "success";
        } else {

            echo "Please pass Cron Secret Key or passed Cron Secret Key is not valid";
        }
    }

    public function feereminder()
    {
    $this->load->library('mailsmsconf');
        $feereminder   = $this->feereminder_model->get(null, 1);
        $reminter_type = array();
        $studentList   = array();

        if (!empty($feereminder)) {
            foreach ($feereminder as $feereminder_key => $feereminder_value) {
                if ($feereminder_value->reminder_type == "before") {

                    $date               = date('Y-m-d', strtotime('+' . $feereminder_value->day . ' days'));
                    $fees_type_reminder = $this->feegrouptype_model->getFeeTypeDueDateReminder($date);
                    if (!empty($fees_type_reminder)) {
                        foreach ($fees_type_reminder as $reminder_key => $reminder_value) {

                            $students = $this->feegrouptype_model->getFeeTypeStudents($reminder_value->fee_session_group_id, $reminder_value->id);

                            foreach ($students as $student_key => $student_value) {
                                $students[$student_key]->{'due_date'}       = $date;
                                $students[$student_key]->{'fee_type'}       = $reminder_value->type;
                                $students[$student_key]->{'fee_code'}       = $reminder_value->code;
                                $students[$student_key]->{'fee_amount'}     = $reminder_value->amount;
                                $students[$student_key]->{'due_amount'}     = $reminder_value->amount;
                                $students[$student_key]->{'deposit_amount'} = number_format((float) 0, 2, '.', '');
                                $fees_array                                 = json_decode($student_value->amount_detail);
                                if (json_last_error() == JSON_ERROR_NONE) {
                                    $deposit_amount = 0;
                                    foreach ($fees_array as $fee_collected_key => $fee_collected_value) {
                                        $deposit_amount = $deposit_amount + $fee_collected_value->amount;
                                    };
                                    $students[$student_key]->{'deposit_amount'} = number_format((float) ($deposit_amount), 2, '.', '');
                                    $students[$student_key]->{'due_amount'}     = number_format((float) ($reminder_value->amount - $deposit_amount), 2, '.', '');
                                };

                                $studentList[] = $student_value;
                            }

                        }
                    }

                } else if ($feereminder_value->reminder_type == "after") {
                   $date               = date('Y-m-d', strtotime('-' . $feereminder_value->day . ' days'));
                    $fees_type_reminder = $this->feegrouptype_model->getFeeTypeDueDateReminder($date);
                    if (!empty($fees_type_reminder)) {
                        foreach ($fees_type_reminder as $reminder_key => $reminder_value) {

                            $students = $this->feegrouptype_model->getFeeTypeStudents($reminder_value->fee_session_group_id, $reminder_value->id);

                            foreach ($students as $student_key => $student_value) {
                                $students[$student_key]->{'due_date'}       = $date;
                                $students[$student_key]->{'fee_type'}       = $reminder_value->type;
                                $students[$student_key]->{'fee_code'}       = $reminder_value->code;
                                $students[$student_key]->{'fee_amount'}     = $reminder_value->amount;
                                $students[$student_key]->{'due_amount'}     = $reminder_value->amount;
                                $students[$student_key]->{'deposit_amount'} = number_format((float) 0, 2, '.', '');
                                $fees_array                                 = json_decode($student_value->amount_detail);
                                if (json_last_error() == JSON_ERROR_NONE) {
                                    $deposit_amount = 0;
                                    foreach ($fees_array as $fee_collected_key => $fee_collected_value) {
                                        $deposit_amount = $deposit_amount + $fee_collected_value->amount;
                                    };
                                    $students[$student_key]->{'deposit_amount'} = number_format((float) ($deposit_amount), 2, '.', '');
                                    $students[$student_key]->{'due_amount'}     = number_format((float) ($reminder_value->amount - $deposit_amount), 2, '.', '');
                                };

                                $studentList[] = $student_value;
                            }

                        }
                    }


                }

            }

            if (!empty($studentList)) {
                foreach ($studentList as $eachStudent_key => $eachStudent_value) {
                    if ($eachStudent_value->due_amount <= 0) {
                        unset($studentList[$eachStudent_key]);
                    }
                }

            }
            if (!empty($studentList)) {
                foreach ($studentList as $eachStudent_key => $eachStudent_value) {
                    $this->mailsmsconf->mailsms('fees_reminder', $eachStudent_value);
                }

            }
        }

    }

}
