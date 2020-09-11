<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Requisition extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->load->helper('url');
        $this->load->model("staff_model");
    }

    function index() {
        if (!$this->rbac->hasPrivilege('requisition', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Procurement');
        $this->session->set_userdata('sub_menu', 'Procurement/index');
        
        $data['title'] = 'Requisition';
        $data['list'] = array("items"=>1);
        $this->load->view('layout/header', $data);
        $this->load->view('admin/procurement/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function listing() {
        $this->session->set_userdata('top_menu', 'Procurement');
        $this->session->set_userdata('sub_menu', 'Procurement/listing');
        $user_id = $this->session->userdata("admin")["id"];
        
        $role = "";
        foreach($this->session->userdata("admin")["roles"] as $key=>$r){
            $role = $key;
        }

        $xcrud = xcrud_get_instance();
        $xcrud->table('requisitions');
        if($role == "Super Admin") {
            $xcrud->where("deleted_at IS NULL");
        } else {
            $xcrud->where("deleted_at IS NULL AND staff_id=$user_id");
            $otherRoles = array();
            
            $proc_roles = $this->common_model->dbSelect("proc_roles","staff"," id='$user_id' ");
            if(count($proc_roles) > 0){
                $proc_roles = $proc_roles[0]->proc_roles;
                if($proc_roles == null){
                    $proc_roles = 0;
                }
                $otherRoles = $this->common_model->dbSelect("id","proc_roles"," id IN ($proc_roles) ");
            }
        }    

        
        $xcrud->where("approval_count <= 5");
        $xcrud->show_primary_ai_field(false);
        $xcrud->relation('department_id', 'department', 'id', 'department_name');
        $xcrud->relation('staff_id', 'staff', 'id', 'name');
        $xcrud->columns('department_id, staff_id, delivery_time,Approval');
        
        $xcrud->button('javascript:requisition_report({id})','Print','fa fa-print', 'myclass');

        $xcrud->label("department_id", $this->lang->line("department"));
        $xcrud->label("staff_id", $this->lang->line("requisition_staff"));
        $xcrud->label("delivery_time", $this->lang->line("delivery_date"));
        $xcrud->column_callback('Approval','approvals_by_callback');
        
        if($role == "Super Admin"){
            $xcrud->fields('department_id, staff_id, delivery_time, Approvals');
            $xcrud->subselect("Approval","SELECT count(*) FROM requisition_approvals WHERE requisition_id={id} AND proc_role_id != 0 AND deleted_at IS NULL AND status='approved'");
            $xcrud->subselect("Approvals","SELECT GROUP_CONCAT('<strong>Name:</strong> ',s.name,',<strong>Dept:</strong> ',d.department_name,',<strong>Role: </strong>',pr.name,'<br/>') FROM requisition_approvals rr INNER JOIN staff s ON FIND_IN_SET(s.id, rr.approved_by) INNER JOIN proc_roles pr ON pr.id=rr.proc_role_id INNER JOIN department d ON s.department=d.id WHERE rr.requisition_id={id} AND rr.status='approved'");
        } else {
            $xcrud->fields('department_id, staff_id, delivery_time');
            $xcrud->subselect("Approval","SELECT count(*) FROM requisition_approvals WHERE requisition_id={id} AND proc_role_id IN (".$proc_roles.") AND deleted_at IS NULL AND status='approved'");

            if(count($otherRoles) > 0){
                $xcrud->button(site_url('admin/requisition/approve_request/{id}/'.$user_id.'/'.str_replace(",", "y", $proc_roles)),"Approve","fa fa-check","btn btn-success");
                $xcrud->button(site_url('admin/requisition/reject_request/{id}/'.$user_id.'/'.str_replace(",", "y", $proc_roles)),"Reject","fa fa-times", "btn btn-danger");
            }
        }

        $requisition_details = $xcrud->nested_table('requisition_id','id','requisition_details','requisition_id');
        $requisition_details->columns("item_name,quality,quantity,specification,price,previous_price");
        $requisition_details->fields("item_name,quality,quantity,specification,price,previous_price");
        $requisition_details->unset_view();
        $requisition_details->unset_edit();
        $requisition_details->unset_add();
        $requisition_details->unset_title();
        $requisition_details->unset_remove();
        $requisition_details->unset_print();
        $requisition_details->unset_csv();
        $requisition_details->unset_search();
        $requisition_details->unset_limitlist();
        
        if($role != "Super Admin"){
            $xcrud->unset_remove();
        }
        $xcrud->unset_edit();
        $xcrud->unset_print();
        $xcrud->unset_title();
        $xcrud->unset_add();
        $xcrud->unset_csv();
        $xcrud->unset_limitlist();

        $data['requisitions'] = $xcrud->render();
        $this->load->view('layout/header', $data);
        $this->load->view('admin/procurement/listing', $data);
        $this->load->view('layout/footer', $data);
    }


    public function approve_request($requisition_id, $staff_id, $roles){
        $arr = explode("y", $roles);
        $roles_name = $this->common_model->dbSelect("name","proc_roles"," id IN (".implode(',',$arr).")");
        $role_names = "";
        if(count($roles_name) > 0){
            foreach($roles_name as $n){
                $role_names .= $n->name .", ";
            }
            $role_names = rtrim($role_names, ", ");
        }

        foreach($arr as $ar){
            $exists = $this->common_model->dbSelect("*","requisition_approvals"," requisition_id='$requisition_id' AND approved_by='$staff_id' AND proc_role_id='$ar' ");
            if(count($exists) == 0){
                $data = array(
                    "requisition_id" => $requisition_id,
                    "approved_by" => $staff_id,
                    "proc_role_id" => $ar,
                    "status" => "approved"
                );
                $this->common_model->dbInsert("requisition_approvals", $data);
            } else {
                $this->common_model->update_where("requisition_approvals",array("id"=>$exists[0]->id),array("status"=>"approved"));
            }
        }
        $this->common_model->update_where("requisitions",array("id"=>$requisition_id),array("approval_count"=>count($arr)));
        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">'.$role_names.' Request approved successfully!</div>');
        redirect("admin/requisition/listing", "refresh");
    }

    public function reject_request($requisition_id, $staff_id, $roles){
        $arr = explode("y", $roles);
        $roles_name = $this->common_model->dbSelect("name","proc_roles"," id IN (".implode(',',$arr).")");
        $role_names = "";
        if(count($roles_name) > 0){
            foreach($roles_name as $n){
                $role_names .= $n->name .", ";
            }
            $role_names = rtrim($role_names, ", ");
        }

        foreach($arr as $ar){
            $exists = $this->common_model->dbSelect("*","requisition_approvals"," requisition_id='$requisition_id' AND approved_by='$staff_id' AND proc_role_id='$ar' ");
            if(count($exists) == 0){
                $data = array(
                    "requisition_id" => $requisition_id,
                    "approved_by" => $staff_id,
                    "proc_role_id" => $ar,
                    "status" => "rejected"
                );
                $this->common_model->dbInsert("requisition_approvals", $data);
            } else {
                $this->common_model->update_where("requisition_approvals",array("id"=>$exists[0]->id),array("status"=>"rejected"));
            }
        }
        $this->common_model->update_where("requisitions",array("id"=>$requisition_id),array("approval_count"=>count($arr)));
        $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">'.$role_names.' Request rejected successfully!</div>');
        redirect("admin/requisition/listing", "refresh");
    }

    public function history() {
        $this->session->set_userdata('top_menu', 'Procurement');
        $this->session->set_userdata('sub_menu', 'Procurement/history');
        
        $xcrud = xcrud_get_instance();
        $xcrud->table('requisitions');
        $xcrud->where("deleted_at IS NULL ");
        $xcrud->where("approval_count=5 ");
        $xcrud->show_primary_ai_field(false);
        $xcrud->relation('department_id', 'department', 'id', 'department_name');
        $xcrud->relation('staff_id', 'staff', 'id', 'name');
        $xcrud->columns('department_id, staff_id, delivery_time, Approvals');
        $xcrud->fields('department_id, staff_id, delivery_time');
        $xcrud->label("department_id", $this->lang->line("department"));
        $xcrud->label("staff_id", $this->lang->line("requisition_staff"));
        $xcrud->label("delivery_time", $this->lang->line("delivery_date"));
        $xcrud->label("approvals_by", $this->lang->line("approvals"));
        $xcrud->column_callback('Approvals','approvals_by_callback');
        $xcrud->subselect("Approvals","SELECT count(*) FROM requisition_approvals WHERE requisition_id={id} AND deleted_at IS NULL AND status='approved'");
        
        $xcrud->button('javascript:requisition_report({id})','Print','fa fa-print', 'myclass');

        $requisition_details = $xcrud->nested_table('requisition_id','id','requisition_details','requisition_id');
        $requisition_details->columns("item_name,quality,quantity,specification,price,previous_price");
        $requisition_details->fields("item_name,quality,quantity,specification,price,previous_price");
        $requisition_details->unset_view();
        $requisition_details->unset_edit();
        $requisition_details->unset_add();
        $requisition_details->unset_title();
        $requisition_details->unset_remove();
        $requisition_details->unset_print();
        $requisition_details->unset_csv();
        $requisition_details->unset_search();
        $requisition_details->unset_limitlist();

        $xcrud->unset_remove();
        $xcrud->unset_edit();
        $xcrud->unset_print();
        $xcrud->unset_title();
        $xcrud->unset_add();
        $xcrud->unset_csv();
        $xcrud->unset_limitlist();

        $data['requisitions'] = $xcrud->render();
        $this->load->view('layout/header', $data);
        $this->load->view('admin/procurement/history', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getDepartments() {
        $data = $this->common_model->dbSelect("*","department", " 1 ");
        echo json_encode(array("status"=>"success","message"=>"data found", "data"=>$data));
    }

    public function getStaff(){
        $data = $this->staff_model->get();
        echo json_encode(array("status"=>"success","message"=>"data found", "data"=>$data));
    }

    public function saveRequisition(){
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $data = array(
            "department_id" => $request->department_id,
            "staff_id" => $request->staff_id,
            "delivery_time" => to_mysql_date($request->delivery_time)
        );
        $requisition_id = $this->common_model->dbInsert("requisitions", $data);
        foreach($request->details as $d){
            $row = array(
                "requisition_id" => $requisition_id, 
                "item_name" => $d->item, 
                "quality" => $d->quality,
                "quantity" => $d->quantity, 
                "specification" => $d->specification, 
                "price" => $d->price, 
                "previous_price" => $d->previous_price
            );
            $this->common_model->dbInsert("requisition_details", $row);
        }
        if($requisition_id){
            echo json_encode(array("status"=>"success","message"=>"Requisition saved successfully!"));
        }
    }

    public function get_report() {
        $id = $this->input->post("id");
        $sql = "SELECT r.*,d.id as detail_id,d.item_name,d.quantity, d.quality, d.specification, d.price, d.previous_price, s.name as staff_name, dpt.department_name FROM requisitions r LEFT JOIN requisition_details d ON r.id=d.requisition_id INNER JOIN staff s ON r.staff_id=s.id INNER JOIN department dpt ON r.department_id=dpt.id WHERE r.id=$id AND r.deleted_at IS NULL ";
        $requisitions = $this->common_model->dbQuery($sql);
        $data = array();
        if(count($requisitions) > 0){
            $data["id"] = $requisitions[0]->id;
            $data["department_id"] = $requisitions[0]->department_id;
            $data["department_name"] = $requisitions[0]->department_name;
            $data["staff_id"] = $requisitions[0]->staff_id;
            $data["staff_name"] = $requisitions[0]->staff_name;
            $data["delivery_time"] = $requisitions[0]->delivery_time;
            $data["approval_count"] = $requisitions[0]->approval_count;
            $data["details"] = array();
            foreach($requisitions as $req){
                $arr = array();
                $arr["id"] = $req->detail_id;
                $arr["item_name"] = $req->item_name;
                $arr["quantity"] = $req->quantity;
                $arr["quality"] = $req->quality;
                $arr["specification"] = $req->specification;
                $arr["price"] = $req->price;
                $arr["previous_price"] = $req->previous_price;
                array_push($data["details"], $arr);
            }
        }
        if(count($data) > 0){
            $sql = "SELECT * FROM proc_roles p LEFT JOIN  requisition_approvals r ON p.id=r.proc_role_id WHERE r.requisition_id=$id";
            $approvals = $this->common_model->dbQuery($sql);
            $data["approvals"] = $approvals;
        }
        echo $this->load->view("admin/procurement/print.php", $data, true);
    }

}

?>