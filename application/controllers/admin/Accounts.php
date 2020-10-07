<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Accounts extends Admin_Controller {

    function __construct() {
        parent::__construct();
    }

    public function manage_accounts() {
        $this->session->set_userdata('top_menu', 'Accounts');
        $this->session->set_userdata('sub_menu', 'Accounts/manage_accounts');


        $xcrud = xcrud_get_instance();
        $xcrud->table('y_accounts');
        $xcrud->where("deleted_at IS NULL ");
        $xcrud->show_primary_ai_field(false);
        $xcrud->columns('account_no, account_title, account_type, Balance, opening_balance');
        $xcrud->fields('account_no, account_title, account_type, opening_balance_date, opening_balance_type, opening_balance, memo');
        $xcrud->label('account_no', "Account Code");
        $xcrud->label('account_title', "Title");
        $xcrud->label('account_type', "Type");
        $xcrud->label('memo', "Particulars");
        $xcrud->column_callback('Balance', 'proper_number_format');
        $xcrud->column_callback('opening_balance', 'proper_number_format_with_dr_or_cr');
        //$xcrud->button(base_url().'admin/accounts/ledger/{id}','Ledger','fa fa-file');
        $xcrud->subselect("Balance","SELECT sum(amount) FROM y_transctions WHERE y_account_id={id} AND deleted_at IS NULL ");
        $xcrud->unset_title();
        $xcrud->unset_print();
        $xcrud->unset_csv();
        $xcrud->unset_limitlist();

        $data['accounts'] = $xcrud->render();
        $this->load->view('layout/header', $data);
        $this->load->view('admin/accounts/manage_accounts', $data);
        $this->load->view('layout/footer', $data);
    }

    public function journal_vouchers(){
        $this->session->set_userdata('top_menu', 'Accounts');
        $this->session->set_userdata('sub_menu', 'Accounts/journal_vouchers');
        $id = $this->session->userdata("admin")["id"];
        
        $xcrud = xcrud_get_instance();
        $xcrud->table('y_journal_voucher');
        $xcrud->where("deleted_at IS NULL AND posted_by=$id");
        $xcrud->order_by('id','desc');
        $xcrud->show_primary_ai_field(false);
        $xcrud->relation('posted_by', 'staff', 'id', 'name');
        $xcrud->columns('date,jv_title,jv_description,posted_by,amount');
        $xcrud->fields('date,jv_title,jv_description,posted_by');
        $xcrud->label('date', "Date");
        $xcrud->label('jv_title', "Title");
        $xcrud->label('jv_description', "Description");
        $xcrud->label('posted_by', "Posted By");
        $xcrud->subselect('amount',"SELECT max(amount) FROM y_transctions WHERE y_journal_voucher_id={id} AND deleted_at IS NULL");
        $xcrud->column_callback('amount', 'proper_number_format');

        $xcrud_nested = $xcrud->nested_table('transcation_list','id','y_transctions','y_journal_voucher_id');
        $xcrud_nested->columns('y_account_id,description,type, amount');
        $xcrud_nested->label("y_account_id", "Account");
        $xcrud_nested->relation('y_account_id', 'y_accounts', 'id', 'account_title');
        $xcrud_nested->unset_view();
        $xcrud_nested->unset_remove();
        $xcrud_nested->unset_title();
        $xcrud_nested->unset_print();
        $xcrud_nested->unset_add();
        $xcrud_nested->unset_edit();
        $xcrud_nested->unset_csv();
        $xcrud_nested->unset_search();
        $xcrud_nested->unset_limitlist();
        $xcrud_nested->load_view("list","voucher_transactions.php");
        
        $xcrud->unset_title();
        $xcrud->replace_remove('delete_journal_voucher');
        $xcrud->unset_print();
        $xcrud->unset_add();
        $xcrud->unset_edit();
        $xcrud->unset_csv();
        $xcrud->unset_search();
        $xcrud->unset_limitlist();

        $data['jvs'] = $xcrud->render();
        $this->load->view('layout/header', $data);
        $this->load->view('admin/accounts/journal_voucher', $data);
        $this->load->view('layout/footer', $data);

    }
    
    public function general_ledger_summary(){
        $this->session->set_userdata('top_menu', 'Accounts');
        $this->session->set_userdata('sub_menu', 'Accounts/general_ledger_summary');

        $data["from_date"] = date("d/m/Y");
        $data["to_date"] = date('d/m/Y', strtotime("+365 days"));
        $this->load->view('layout/header', $data);
        $this->load->view('admin/accounts/general_ledger_summary', $data);
        $this->load->view('layout/footer', $data);
    }
    public function trial_balance(){
        $this->session->set_userdata('top_menu', 'Accounts');
        $this->session->set_userdata('sub_menu', 'Accounts/trial_balance');

        $data["from_date"] = date("d/m/Y");
        $data["to_date"] = date('d/m/Y', strtotime("+365 days"));
        $this->load->view('layout/header', $data);
        $this->load->view('admin/accounts/trial_balance', $data);
        $this->load->view('layout/footer', $data);
    }

    public function accounts_ledger(){
        $this->session->set_userdata('top_menu', 'Accounts');
        $this->session->set_userdata('sub_menu', 'Accounts/accounts_ledger');

        $data["from_date"] = date("d/m/Y");
        $data["to_date"] = date('d/m/Y', strtotime("+365 days"));
        $this->load->view('layout/header', $data);
        $this->load->view('admin/accounts/ledger', $data);
        $this->load->view('layout/footer', $data);
    }

    public function get_accounts() {
        $res = $this->common_model->dbSelect("*","y_accounts"," deleted_at IS NULL ");
        if(count($res) > 0) { 
            $response = array("status"=>"success", "message"=>"data found","data"=>$res);
        } else {
            $response = array("status"=>"error", "message"=>"data not found","data"=>array());
        }
        echo json_encode($response);
    }

    public function save_jv() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $posted_by = $this->session->userdata("admin")["id"];

        $jv_data = array(
            "date" => to_mysql_date($request->date),
            "jv_manual_id" => $request->jv_manual_id,
            "jv_title" => $request->jv_title,
            "jv_description" => $request->jv_description,
            "posted_by" => $posted_by
        );
        $jv_id = $this->common_model->dbInsert("y_journal_voucher",$jv_data);
        $debit_amount=0;
        $credit_amount=0;
        $account_id = 0;
        foreach($request->transcations as $tran){
            if($tran->type == "credit"){
                $credit_amount -= $tran->amount;
            } else if ($tran->type == "debit"){
                $debit_amount -= $tran->amount;
            }
            $t_data = array(
                "y_journal_voucher_id" => $jv_id,
                "y_account_id" => $tran->account_id,
                "description" => $tran->memo,
                "amount" => $tran->amount,
                "type" => $tran->type
            );
            $account_id = $tran->account_id;
            $this->common_model->dbInsert("y_transctions",$t_data);
        }
        $this->common_model->update_where("y_accounts",array("id"=>$account_id),array("balance" => ($debit_amount - $credit_amount)));
        echo json_encode(array("status"=>"success","message"=>"journal voucher save successfully!"));
    }

    public function get_ledger(){
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $from_date = to_mysql_date($request->from_date);
        $to_date = to_mysql_date($request->to_date);
        $sql = "SELECT t.*, j.date as jv_date,j.jv_title, a.account_no, a.account_title, a.account_type, a.memo as account_memo, 0 as balance FROM y_transctions t INNER JOIN y_journal_voucher j ON t.y_journal_voucher_id=j.id INNER JOIN y_accounts a ON t.y_account_id=a.id WHERE t.y_account_id='$request->account_id' AND t.deleted_at IS NULL AND j.date BETWEEN '$from_date' AND '$to_date'";
        $result = $this->common_model->dbQuery($sql);
        $balance = 0;
        $opening_balance = 0;
        $opening_balance_type = 'debit';
        $opening_balance_date = null;
        $arrr = $this->common_model->dbSelect("opening_balance_type,opening_balance, opening_balance_date","y_accounts"," id= '$request->account_id' ");
        if(count($arrr) > 0) {
            $opening_balance = $arrr[0]->opening_balance;
            $opening_balance_type = $arrr[0]->opening_balance_type;
            $opening_balance_date = $arrr[0]->opening_balance_date;
            if($opening_balance_type == "credit") {
                $opening_balance = -$opening_balance;
            }
        }
        $balance = $opening_balance;
        if(count($result) > 0){
            foreach($result as $key=>$res){
                $balance = $balance + $res->amount;
                $result[$key]->balance = $balance;
            }

            $opening_balance = number_format($opening_balance, 2, '.', ',');

            foreach($result as $re){
                $re->amount = number_format($re->amount, 2, '.', ',');
                $re->balance = number_format($re->balance, 2, '.', ',');
            }
            $response = array("status"=>"success", "message"=>"data found", "data"=>$result, "opening_balance"=> $opening_balance, "opening_balance_type"=> $opening_balance_type, "opening_balance_date" => $opening_balance_date);
        } else {
            $response = array("status"=>"error", "message"=>"data not found", "data"=>array(), "opening_balance"=> $opening_balance);
        }
        echo json_encode($response);
    }

    public function get_ledger_summary(){
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $from_date = to_mysql_date($request->from_date);
        $to_date = to_mysql_date($request->to_date);
        $accounts = $this->common_model->dbSelect("*","y_accounts"," deleted_at IS NULL ");
        if(count($accounts) > 0){
            foreach($accounts as $acc){
                $acc->debit = 0;
                $acc->credit = 0;
                $acc->closing_balance = 0;
                $debit_amount = $this->common_model->dbSelect("sum(amount) as amount","y_transctions"," deleted_at IS NULL AND y_account_id='$acc->id' AND type='debit' ");
                if(count($debit_amount) > 0){
                    $acc->debit = $debit_amount[0]->amount;
                }
                $credit_amount = $this->common_model->dbSelect("sum(amount) as amount","y_transctions"," deleted_at IS NULL AND y_account_id='$acc->id' AND type='credit' ");
                if(count($credit_amount) > 0){
                    $acc->credit = $credit_amount[0]->amount;
                }

                $opening_balance = 0;
                if($acc->account_type  == "Liability" || $acc->account_type == "Income" || $acc->account_type == "Equity"){
                    /*if($acc->opening_balance_type == "credit"){
                        $opening_balance = $acc->opening_balance;
                    }*/
                    $acc->closing_balance = $acc->opening_balance + $acc->credit - $acc->debit;
                } else if($acc->account_type == "Assets" || $acc->account_type == "Expense") {
                    /*if($acc->opening_balance_type == "debit"){
                        $opening_balance = $acc->opening_balance;
                    }*/
                    $acc->closing_balance = $acc->opening_balance + $acc->debit - $acc->credit;
                }
            }

            $data["opening_total"] = 0;
            $data["closing_total"] = 0;
            $data["credit_total"] = 0;
            $data["debit_total"] = 0;
            foreach($accounts as $acc2){
                $data["opening_total"] += $acc2->opening_balance;
                $data["closing_total"] += $acc2->closing_balance;
                $data["debit_total"] += $acc2->debit;
                $data["credit_total"] += $acc2->credit;
            }
            foreach($accounts as $acc2){
                $acc2->debit = number_format($acc2->debit, 2, '.', ',');
                $acc2->credit = number_format($acc2->credit, 2, '.', ',');
                $acc2->opening_balance = number_format($acc2->opening_balance, 2, '.', ',');
                
                if($acc2->opening_balance != 0){
                    if($acc2->opening_balance_type == "debit"){
                        $acc2->opening_balance = $acc2->opening_balance . " (Dr)";
                    } else if($acc2->opening_balance_type == "credit") {
                        $acc2->opening_balance = $acc2->opening_balance . " (Cr)";
                    }
                }
                
                $acc2->closing_balance = number_format($acc2->closing_balance, 2, '.', ',');
            }  
            $data["accounts"] = $accounts;
            $data["opening_total"] = number_format($data["opening_total"], 2, '.', ',');
            $data["closing_total"] = number_format($data["closing_total"], 2, '.', ',');
            $data["debit_total"] = number_format($data["debit_total"], 2, '.', ',');
            $data["credit_total"] = number_format($data["credit_total"], 2, '.', ',');
            $response = array("status"=>"success", "message"=>"data found", "data"=>$data);
        } else {
            $response = array("status"=>"error", "message"=>"data not found", "data"=>array());
        }
        echo json_encode($response);
    }

    public function get_trial_balace_report() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $from_date = to_mysql_date($request->from_date);
        $to_date = to_mysql_date($request->to_date);
        $total_balance = 0.0;
        $total_debit_balance = 0.0;
        $total_credit_balance = 0.0;

        $accounts = $sql = $this->common_model->dbSelect("*","y_accounts"," deleted_at IS NULL ");
        if(count($accounts) > 0){
            foreach($accounts as $acc) {
                $acc->credit = 0;
                $acc->debit = 0;
                
                $ledger = $this->get_ledger2($acc->id, $from_date, $to_date);
                $ledger_balance = 0;
                $ledger_type = null;
                if(count($ledger) > 0){
                    $ledger_balance = $ledger[count($ledger)-1]->balance;
                    $ledger_type = $ledger[count($ledger)-1]->type;

                    if($acc->opening_balance_type == "debit") {
                        if($ledger_type == "debit"){
                            $acc->debit += $acc->opening_balance + $ledger_balance;
                        } else if ($ledger_type == "credit") {
                            $acc->debit += $acc->opening_balance - $ledger_balance;
                        }
                    } else if ($acc->opening_balance_type == "credit") {
                        if($ledger_type == "debit"){
                            $acc->debit += $acc->opening_balance - $ledger_balance;
                        } else if($ledger_type == "credit") {
                            $acc->credit += $acc->opening_balance + $ledger_balance;
                        }
                    }

                } else {
                    if($acc->opening_balance_type == "debit") {
                        $acc->debit = $acc->opening_balance;
                    } else if($acc->opening_balance_type == "credit") {
                        $acc->credit = $acc->opening_balance;
                    }
                }
                $total_credit_balance += $acc->credit;
                $total_debit_balance += $acc->debit;
            }
            foreach($accounts as $ac){
                if($ac->opening_balance > 0) {
                    $ac->opening_balance = number_format($ac->opening_balance, 2, '.', ',');
                }

                if($ac->credit > 0) {
                    $ac->credit = number_format($ac->credit, 2, '.', ',');
                }

                if($ac->debit > 0) {
                    $ac->debit = number_format($ac->debit, 2, '.', ',');
                }
            }
            $total_credit_balance = number_format($total_credit_balance, 2, '.', ',');
            $total_debit_balance = number_format($total_debit_balance, 2, '.', ',');
            $response = array("status"=>"success", "message"=>"data found", "data"=>$accounts, "total_balance"=> $total_balance, "total_debit_balance"=>$total_debit_balance, "total_credit_balance"=> $total_credit_balance);
        } else {
            $response = array("status"=>"error", "message"=>"data not found", "data"=>array());
        }
        echo json_encode($response);
    }

    public function all(){
        $data = $this->common_model->dbSelect("*","y_accounts"," deleted_at IS NULL ");
        echo json_encode($data);
    }

    public function get_ledger2($account_id, $from_date, $to_date){
        $sql = "SELECT t.id, t.amount, t.type, 0 as balance FROM y_transctions t INNER JOIN y_journal_voucher j ON t.y_journal_voucher_id=j.id INNER JOIN y_accounts a ON t.y_account_id=a.id WHERE t.y_account_id='$account_id' AND t.deleted_at IS NULL AND j.date BETWEEN '$from_date' AND '$to_date'";
        $result = $this->common_model->dbQuery($sql);
        $balance = 0;
        if(count($result) > 0){
            foreach($result as $key=>$res){
                $balance = $balance + $res->amount;
                $result[$key]->balance = $balance;
            }
        }
        return $result;
    }
}