<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Schoolstore extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->load->helper('url');
        $this->load->model("Schoolstore_model");
    }

    function orders(){
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'schoolstore/orders');
        
        $xcrud = xcrud_get_instance();
        $xcrud->table('orders');
        $xcrud->where("deleted_at IS NULL ");
        $xcrud->show_primary_ai_field(false);
        $xcrud->relation('parent_id', 'users', 'id', 'username');
        $xcrud->relation('parent_id', 'students', 'parent_id', 'guardian_name');
        $xcrud->label("item_id", "Items");
        $xcrud->label("parent_id", "Order By");
        $xcrud->label("quantity", "Quantities");
        $xcrud->label("created_at", "Order Datetime");
        $xcrud->columns('parent_id,amount_paid, status, reference_no, created_at');
        $xcrud->fields('parent_id, amount_paid, status, reference_no,created_at');
        $xcrud->button('javascript:customPrint({id})','Print','fa fa-print', 'myclass');
        $xcrud->button('javascript:customPrintPOS({id})','POS Print','fa fa-print', 'myclass');
        $xcrud->disabled('parent_id');
        //$xcrud->replace_update("custom_order_update");
        
        $order_list = $xcrud->nested_table('item_id','id','order_details','order_id');
        $order_list->relation('item_id', 'school_store', 'id', 'item_store');
        $order_list->relation('collected_by', 'students', 'id', array('firstname','lastname'));
        $order_list->columns("item_id,is_collected, collected_by,quantity, updated_at");
        $order_list->fields("item_id,is_collected, collected_by,quantity, updated_at");
        $order_list->label("item_id","Name");
        $order_list->label("updated_at","Collected DateTime");
        $order_list->before_update('update_stock');
        $order_list->after_update('update_order_status');
        $order_list->unset_add();
        $order_list->unset_title();
        $order_list->unset_remove();
        $order_list->unset_print();
        $order_list->unset_csv();
        $order_list->unset_search();
        $order_list->unset_limitlist();
        $order_list->disabled("item_id");
        $order_list->disabled("quantity");

        $xcrud->column_callback('amount_paid','currency_callback2');
        $xcrud->column_callback('item_id','concate_items');
        $xcrud->column_callback('quantity','quantities_callback');
        $xcrud->unset_title();
        $xcrud->unset_add();
        $xcrud->unset_csv();
        $xcrud->unset_limitlist();

        $data['orders'] = $xcrud->render();
        $this->load->view('layout/header', $data);
        $this->load->view('admin/schoolstore/orders', $data);
        $this->load->view('layout/footer', $data);
    }

    function sell_items($cat=0){
        $parent_id = $this->session->userdata("student")["id"];
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'schoolstore/sell_items');
        
        if($cat != 0){
            $items = $this->common_model->dbSelect("*","school_store"," item_category_id=$cat AND deleted_at IS NULL ");
        } else {
            $sql = "SELECT s.* FROM school_store s INNER JOIN item_category c ON s.item_category_id=c.id WHERE s.deleted_at IS NULL AND c.type='store_category'";
            $items = $this->common_model->dbQuery($sql);
        }
        
        $data['items'] = $items;
        $itemcategories = $this->common_model->dbSelect("*","item_category"," type='store_category' ");
        $data["itemcategories"] = $itemcategories;
        $this->load->view('layout/header');
        $this->load->view('admin/schoolstore/sell_items', $data);
        $this->load->view('layout/footer');

        
        /*$xcrud = xcrud_get_instance();
        $xcrud->table('orders');
        $xcrud->where("deleted_at IS NULL ");
        $xcrud->where("reference_no IS NULL ");
        $xcrud->show_primary_ai_field(false);
        $xcrud->relation('parent_id', 'users', 'id', 'username');
        $xcrud->relation('collected_by', 'students', 'id', array('firstname','lastname'));
        $xcrud->label("item_id", "Items");
        $xcrud->label("parent_id", "Order By");
        $xcrud->label("quantity", "Quantities");
        $xcrud->label("created_at", "Order Datetime");
        $xcrud->label("updated_at", "Collection Datetime");
        $xcrud->columns('parent_id,amount_paid, status, created_at, updated_at');
        $xcrud->fields('parent_id, amount_paid, status,created_at');
        $xcrud->button('javascript:customPrint({id})','print','fa fa-print', 'myclass');
        //$xcrud->replace_update("custom_order_update");
        
        $order_list = $xcrud->nested_table('item_id','id','order_details','order_id');
        $order_list->relation('item_id', 'school_store', 'id', 'item_store');
        $order_list->relation('collected_by', 'students', 'id', array('firstname','lastname'));
        $order_list->columns("item_id,is_collected, collected_by,quantity, updated_at");
        $order_list->fields("item_id,is_collected, collected_by,quantity, updated_at");
        $order_list->label("item_id","Name");
        $order_list->label("updated_at","Collected DateTime");
        $order_list->before_update('update_stock2');
        $order_list->after_update('update_order_status');
        $order_list->unset_remove();
        $order_list->unset_print();
        $order_list->unset_csv();
        $order_list->unset_title();
        $order_list->unset_search();
        $order_list->unset_limitlist();
        
        $xcrud->column_callback('amount_paid','currency_callback2');
        $xcrud->column_callback('item_id','concate_items');
        $xcrud->column_callback('quantity','quantities_callback');
        $xcrud->unset_title();
        $xcrud->unset_csv();
        $xcrud->unset_limitlist();

        $data['orders'] = $xcrud->render();
        $this->load->view('layout/header');
        $this->load->view('admin/schoolstore/sell_items');
        $this->load->view('layout/footer');*/
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
                        <form action="'.site_url("admin/schoolstore/savetocart").'" method="post">
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

    public function savetocart(){
        $items = $this->common_model->dbSelect("*","school_store"," deleted_at IS NULL ");
        $this->session->set_userdata('top_menu', 'Store');
        $this->session->set_userdata('sub_menu', '');
        $data['items'] = $items;
        $itemcategories = $this->common_model->dbSelect("*","item_category"," 1 ");
        $data["itemcategories"] = $itemcategories;
        
        $admin_id = $this->session->userdata("admin")["id"];
        $this->form_validation->set_rules('quantity', 'Quantity', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/header');
            $this->load->view('admin/schoolstore/sell_items', $data);
            $this->load->view('layout/footer');
        } else {
            $data = array(
                "item_id" => $this->input->post("item_id"),
                "parent_id" => $admin_id,
                "quantity" => $this->input->post("quantity")
            );
            $this->common_model->dbInsert("parent_shopping_cart",$data);
            $arr = $this->common_model->dbSelect("*","parent_shopping_cart"," parent_id='$admin_id' ");
            if(count($arr)>0){
                $this->session->set_userdata("admin_cart_count", count($arr));
            }
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">'.$this->lang->line('success_message').'</div>');
            redirect('admin/schoolstore/sell_items');
        }
    }

    public function checkout(){
        $admin_id = $this->session->userdata("admin")["id"];
        $sql = "SELECT s.*, c.id as cart_id, c.quantity as quantity FROM parent_shopping_cart c INNER JOIN school_store s ON c.item_id=s.id WHERE c.parent_id='$admin_id' ";
        $items = $this->common_model->dbQuery($sql);
        $this->session->set_userdata('top_menu', 'Store');
        $this->session->set_userdata('sub_menu', 'checkout');
        $data['items'] = $items;

        $sql = "SELECT u.*, concat(s.firstname,' ', s.lastname) as student_name, guardian_name FROM users u LEFT JOIN students s ON u.childs=s.id WHERE u.role='parent' AND u.is_active='yes' ";
        $data["parents"] = $this->common_model->dbQuery($sql);
        $this->load->view('layout/header');
        $this->load->view('admin/schoolstore/checkout', $data);
        $this->load->view('layout/footer');
    }

    function custom_print_order($id){
        $currency_symbol = $this->session->userdata("admin")["currency_symbol"];
        $sql = "SELECT o.*,d.*,s.firstname, s.lastname,u.username,i.item_store as item_name,i.price,d.updated_at as collected_datetime FROM orders o LEFT JOIN order_details d ON o.id=d.order_id LEFT JOIN students s ON d.collected_by=s.id INNER JOIN users u ON o.parent_id=u.id INNER JOIN school_store i ON d.item_id=i.id WHERE o.id='$id' ";
        $data = $this->common_model->dbQuery($sql);
        $order = (object) array();
        $table = "<table class='table table-default'>";
        if(count($data) > 0){
            //$table .= "<tr><td></td><td>". $data[0]->id . "</td></tr>";
            $table .= "<tr><th>Amount Paid</th><td>". $data[0]->amount_paid . "</td></tr>";
            $table .= "<tr><th>Reference No#</th><td>". $data[0]->reference_no . "</td></tr>";
            $table .= "<tr><th>Order DdateTime</th><td>". $data[0]->created_at . "</td></tr>";
            $table .= "</table><table class='table table-default'><tr>";
            $table .= "<th>Sr#.</th>";
            $table .= "<th>Item</th>";
            $table .= "<th>Price</th>";
            $table .= "<th>Quantity</th>";
            $table .= "<th>Is Collected</th>";
            $table .= "<th>Collected By</th>";
            $table .= "<th>Collected Datetime</th></tr>";
            $count = 1;
            $total = 0;
            foreach($data as $d){
                //$arr = (object) array();
                //$arr->item_id = $d->item_id;
                $table .= "<tr>";
                $table .= "<td>" . $count++ . "</td>";
                $table .= "<td>" . $d->item_name . "</td>";
                $table .= "<td>" . $d->price . "</td>";
                $table .= "<td>" . $d->quantity . "</td>";
                $table .= "<td>" . $d->is_collected . "</td>";
                //$table .= "<td></td><td>" . $d->collected_by . "</td>";
                $table .= "<td>" . $d->firstname." ".$d->lastname . "</td>";
                $table .= "<td>" . $d->collected_datetime . "</td>";
                $table .= "</tr>";
                $total += ($d->price * $d->quantity);
                //array_push($order->details, $arr);
            }
            $table .= "<tr><th colspan='4' style='text-align:right'><h4>Total</h4></th><td colspan='3'><h4>".$currency_symbol.$total."</h4></td></tr>";
            $table .= "</table>";
        }
        echo $table;
        //echo json_encode($order);
    }

    function custom_print_order2($id){
        $currency_symbol = $this->session->userdata("admin")["currency_symbol"];
        $sql = "SELECT o.*,d.*,s.firstname, s.lastname,u.username,i.item_store as item_name,i.price,d.updated_at as collected_datetime FROM orders o LEFT JOIN order_details d ON o.id=d.order_id LEFT JOIN students s ON d.collected_by=s.id INNER JOIN users u ON o.parent_id=u.id INNER JOIN school_store i ON d.item_id=i.id WHERE o.id='$id' ";
        $data = $this->common_model->dbQuery($sql);
        $order = (object) array();

        if(count($data) > 0){
            $total = 0;
            $table = "<table class='table table-default' id='mytable'>
                <tr><td colspan='4'><h4 style='text-align:center; font-weight:bold;'>".$this->session->userdata('admin')['school_name']."</h4></td></tr>
                <tr><td colspan='4'>Slip: 00000000000</td></tr>
                <tr><td colspan='2'>Staff: ".$this->session->userdata('admin')['username']."</td><td colspan='2'>Trans: ".$data[0]->id."</td></tr>
                <tr><td colspan='4'>Date: ".$data[0]->created_at."</td></tr>
                <tr><td colspan='4'>**** POS Customer ****</td></tr>
                <tr><th colspan='2' style='text-align:left;'>Description</th><th colspan='2' style='text-align: right;'>Amount</th></tr>";
                foreach($data as $d){
                    $table .= "<tr><td colspan='2'>".$d->item_name."    ".$d->quantity."</td><td colspan='2' style='text-align: right;'>".$currency_symbol.$d->price."</td></tr>";
                    $total += ($d->price * $d->quantity);
                }
                $table .= "<tr><th colspan='2' style='text-align:left;'>Total</th><td colspan='2' style='text-align:right;'>".$currency_symbol.$total."</td></tr>
                <tr><th colspan='4' style='text-align:left;'>Welcome Again</th></tr>
            </table>";
        } else {
          $table .= "<table class='table table-default' id='mytable'><tr><td>No record found!</td></tr></table>";  
        }
        echo $table;
        //echo json_encode($order);
    }

    function index() {
        if (!$this->rbac->hasPrivilege('store', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'schoolstore/index');
        $data['title'] = 'Item School Store List';
        $schoolstore_result = $this->Schoolstore_model->get();
        $data['schoolstorelist'] = $schoolstore_result;
        $data["itemcategories"] = $this->common_model->dbSelect("*","item_category"," is_active='yes' AND type='store_category' ");
        $data["itemsuppliers"] = $this->common_model->dbSelect("*","item_supplier"," 1 ");
        $this->load->view('layout/header', $data);
        $this->load->view('admin/schoolstore/itemstoreList', $data);
        $this->load->view('layout/footer', $data);
    }

    function delete($id) {
        if (!$this->rbac->hasPrivilege('store', 'can_delete')) {
            access_denied();
        }
        $data['title'] = 'Item School Store List';
        $this->Schoolstore_model->remove($id);
        redirect('admin/schoolstore/index');
    }

    function create() {
        if (!$this->rbac->hasPrivilege('store', 'can_add')) {
            access_denied();
        }
        $data['title'] = 'Add Item school store';
        $schoolstore_result = $this->Schoolstore_model->get();
        $data['schoolstorelist'] = $schoolstore_result;
        
        $this->form_validation->set_rules('name', $this->lang->line('item_store_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('price', $this->lang->line('price'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('stock', $this->lang->line('stock'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/schoolstore/itemstoreList', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $newfilename = "default_item.png";
            if (isset($_FILES['picture']) && !empty($_FILES['picture'])) {
            // uploads image in the folder images
                $temp = explode(".", $_FILES["picture"]["name"]);
                $newfilename = substr(md5(time()), 0, 10) . '.' . end($temp);
                move_uploaded_file($_FILES['picture']['tmp_name'], 'uploads/schoolstore/' . $newfilename);
            }

            if(substr($newfilename, -1) == "."){
                $newfilename = "default_item.png";
            }

            $data = array(
                'item_store' => $this->input->post('name'),
                'code' => $this->input->post('code'),
                'picture' => $newfilename,
                'price' => $this->input->post("price"),
                'stock' => $this->input->post("stock"),
                'description' => $this->input->post('description'),
                'item_category_id' => $this->input->post("item_category_id"),
                'item_supplier_id' => $this->input->post("item_supplier_id")
            );
            $this->Schoolstore_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">'.$this->lang->line('success_message').'</div>');
            redirect('admin/schoolstore/index');
        }
    }

    function edit($id) {
        
        if (!$this->rbac->hasPrivilege('store', 'can_edit')) {
            access_denied();
        }

        $data['title'] = 'Edit Item School Store';
        $schoolstore_result = $this->Schoolstore_model->get();
        $data['schoolstorelist'] = $schoolstore_result;
        $data['id'] = $id;
        $store = $this->Schoolstore_model->get($id);
        $data['schoolstore'] = $store;
        $data["itemcategories"] = $this->common_model->dbSelect("*","item_category"," is_active='yes' AND type='store_category' ");
        $data["itemsuppliers"] = $this->common_model->dbSelect("*","item_supplier"," 1 ");

        $this->form_validation->set_rules('name', $this->lang->line('item_store_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('price', $this->lang->line('price'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('stock', $this->lang->line('stock'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/schoolstore/itemstoreEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $newfilename = "default.png";
            if (isset($_FILES['picture'])) {
                $temp = explode(".", $_FILES["picture"]["name"]);
                $newfilename = substr(md5(time()), 0, 10) . '.' . end($temp);
                move_uploaded_file($_FILES['picture']['tmp_name'], 'uploads/schoolstore/' . $newfilename);
            }

            if(substr($newfilename, -1) == "."){
                $newfilename = "default_item.png";
            }

            $data = array(
                'id' => $id,
                'item_store' => $this->input->post('name'),
                'code' => $this->input->post('code'),
                'picture' => $newfilename,
                'price' => $this->input->post("price"),
                'stock' => $this->input->post("stock"),
                'description' => $this->input->post('description'),
                'item_category_id' => $this->input->post("item_category_id"),
                'item_supplier_id' => $this->input->post("item_supplier_id"),
            );
            $this->Schoolstore_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">'.$this->lang->line('update_message').'</div>');
            redirect('admin/schoolstore/index');
        }
    }

    public function delete_item_from_cart($id){
        $data['title'] = 'Item School Store List';
        $res = $this->common_model->delete('id',$id,"parent_shopping_cart");
        $parent_id = $this->session->userdata("admin")["id"];
        $arr = $this->common_model->dbSelect("*","parent_shopping_cart"," parent_id='$parent_id' ");
        if(count($arr)>0){
            $this->session->set_userdata("admin_cart_count", count($arr));
        } else if(count($arr) == 0){
            $this->session->set_userdata("admin_cart_count", 0);
        }
        if($res){
            redirect('admin/schoolstore/checkout');
        }
    }

    function generateRandomString($length) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function placeorder(){
        $item_ids = rtrim($this->input->post("item_ids"),",");
        $item_ids = explode(",",$item_ids);
        $quantity = rtrim($this->input->post("quantities"),",");
        $quantity = explode(",",$quantity);
        $amount_paid = $this->input->post("amount_paid");
        $orr = explode(",", $this->input->post("order_by"));
        $parent_id = $orr[0];
        $collected_by = $orr[1];
        

        $created_at = date("Y-m-d h:i:s");
        if(!empty($this->input->post("created_at"))){
            $created_at = to_mysql_date($this->input->post("created_at")) ." ". date("h:i:s");
        }
        
        $order = array(
            "amount_paid" => $amount_paid,
            "parent_id" => $parent_id,
            "status" => "Not-Collected",
            "reference_no" => "DC".$this->generateRandomString(9),
            "created_at" => $created_at
        );
        $order_id = $this->common_model->dbInsert("orders",$order);
        foreach($item_ids as $key=>$item_id){
            $order_detail = array(
                "order_id" => $order_id,
                "item_id" => $item_id,
                "quantity" => $quantity[$key],
                "is_collected" => "No",
                "collected_by" => 0
            );
            $this->common_model->dbInsert("order_details",$order_detail);
            $stock = $this->common_model->dbSelect("stock","school_store"," id='$item_id' ")[0]->stock;
            $this->common_model->update_where("school_store", array("id" => $item_id), array("stock" => $stock - $quantity[$key]));
        }
        
        foreach($item_ids as $i){
            $this->db->where(array("item_id"=>$i, "parent_id" => $this->session->userdata("admin")["id"]));
            $this->db->delete("parent_shopping_cart");
        }
        $this->session->set_userdata("admin_cart_count",0);
        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Item sale successfully!</div>');
        redirect('admin/schoolstore/sell_items');
    }
}

?>