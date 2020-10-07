<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Paystack extends Parent_Controller {

    public $setting = "";

    function __construct() {
        parent::__construct();
        $this->load->library('stripe_payment');
        $this->setting = $this->setting_model->get();
    }

    public function index() {
        $this->session->set_userdata('top_menu', 'Library');
        $this->session->set_userdata('sub_menu', 'book/index');
        $data = array();
        $data['params'] = $this->session->userdata('params');
        $student_id = $this->session->userdata('students')['student_id'];
        $email = $this->common_model->dbSelect("guardian_email","students"," id='$student_id' ");
        $data["email"] = "ukeredi@gmail.com";
        if(Count($email) > 0){
            $data["email"] = $email[0]->guardian_email;
        }
        $data['setting'] = $this->setting;
        $this->load->view('parent/paystack', $data);
    }

    public function complete() {
        $srespose = array();
        $student_fees_master_id = $this->input->post('student_fees_master_id');
        $fee_groups_feetype_id = $this->input->post('fee_groups_feetype_id');
        $student_id = $this->input->post('student_id');
        //$stripeToken = $this->input->post('stripeToken');
       	// $stripeTokenType = $this->input->post('stripeTokenType');
        //$stripeEmail = $this->input->post('stripeEmail');
        $data = $this->input->post();
        $data['currency'] = 'USD';
        $response = $this->input->post("status");
        if ($response == 'success') {
            $transactionid = $this->input->post("transaction_ref");
            $params = $this->session->userdata('params');
            $payment_data['transactionid'] = $transactionid;
            $json_array = array(
                'amount' => $this->input->post("total"),
                'date' => date('Y-m-d'),
                'amount_discount' => 0,
                'amount_fine' => 0,
                'description' => "Online fees deposit through Stripe TXN ID: " . $transactionid,
                'payment_mode' => 'Paystack',
            );
            $data = array(
                'student_fees_master_id' => $params['student_fees_master_id'],
                'fee_groups_feetype_id' =>  $params['fee_groups_feetype_id'],
                'amount_detail' => $json_array
            );
            $send_to = $params['guardian_phone'];
            $inserted_id = $this->studentfeemaster_model->fee_deposit($data, $send_to);

            if ($inserted_id) {
                $invoice_detail = json_decode($inserted_id);
                $srespose = array("status"=>"success","redirect_url"=>base_url("parent/payment/successinvoice/" . $invoice_detail->invoice_id . "/" . $invoice_detail->sub_invoice_id));
            } else {
                $srespose = array("status"=>"error","redirect_url"=>base_url());
            }
        }
        echo json_encode($srespose);
    }

}

?>