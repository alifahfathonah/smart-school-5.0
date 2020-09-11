<div class="content-wrapper" style="min-height: 946px;" ng-controller="ledgerController" ng-init="initAccounts()">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1><i class="fa fa-object-group"></i> Manage Accounts</h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <!-- Horizontal Form -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Select statement period</h3>
                        <p>Please select date range to check accounts transaction summary.</p>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <div class="box-body" id="ledgerFormBody">
                    	<form name="ledgerForm" ng-submit="getLedgerData(ledgerForm.$valid)" novalidate="">
                			<div class="form-group" id="accounts-select">
                                <label>Accounts:</label>
                                <select class="form-control" ng-model="formModel.account_id" required="">
                                    <option value="">Select an account</option>
                                    <option ng-repeat="a in accounts" value="{{a.id}}">{{a.account_no}} - {{a.account_title}}</option>
                                </select>
                            </div>
                            <div class="form-group">
                				<label>From:</label>
                				<input type="text" name="from_date" class="form-control mydatepicker-autoclose" ng-model="formModel.from_date" ng-init="formModel.from_date='<?php echo $from_date; ?>'"/>
                			</div>
                			<div class="form-group">
                				<label>To:</label>
                				<input type="text" name="to_date" class="form-control mydatepicker-autoclose" ng-model="formModel.to_date" ng-init="formModel.to_date='<?php echo $to_date; ?>'"/>
                			</div>
                			<button type="submit" class="btn btn-info btn-sm">Go</button>
                    	</form> 	
                    </div>
                </div>            
            </div>
            <!--/.col (right) -->
            <div class="col-md-9">
                <div class="box box-primary">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <table align="center">
                                    <tr>
                                        <td style="width: 100px;">
                                            <img src="<?php echo base_url(); ?>uploads/school_content/admin_small_logo/1.png" width="80px" height="80px"/>
                                        </td>
                                        <td class="text-center">
                                            <h3 style="margin-top:0; padding-top:0;">
                                                <strong><?php echo $this->session->userdata("admin")["school_name"]; ?></strong>
                                            </h3>
                                            <p><?php echo $this->session->userdata("admin")["address"]; ?></p>
                                            Email:
                                            <span><?php echo $this->session->userdata("admin")["email"]; ?></span>
                                            Phone:
                                            <span><?php echo $this->session->userdata("admin")["phone"]; ?></span>
                                        </td>
                                    </tr>
                                </table>
                                <hr/>
                            </div>


                            <div class="col-md-12">
                                <table class="table table-default">
                                    <tr>
                                        <td colspan="6" style="border-top: 0px solid;">
                                            <h3><strong>Transcations Summary</strong></h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Account Number:</th>
                                        <td colspan="2">
                                            <span>{{details[0].account_no}}</span>
                                        </td>
                                        <th>Account Title:</th>
                                        <td colspan="3">{{details[0].account_title}}</td>
                                    </tr>
                                    <tr>
                                        <th>Account Type:</th>
                                        <td colspan="2">{{details[0].account_type}}</td>
                                        <th>Memo:</th>
                                        <td colspan="3">{{details[0].account_memo}}</td>
                                    </tr>
                                    <tr>
                                        <th>Today:</th>
                                        <td colspan="2"><?php echo date("Y-m-d"); ?></td>
                                        <th>Statement Period:</th>
                                        <td colspan="3"><?php echo $from_date ." - ". $to_date; ?></td>
                                    </tr>
                                    <tr>
                                        <th style="border-top: 2px solid; border-bottom: 2px solid;">Tr ID</th>
                                        <th style="border-top: 2px solid; border-bottom: 2px solid;">JV ID</th>
                                        <th style="border-top: 2px solid; border-bottom: 2px solid;">Date</th>
                                        <th style="border-top: 2px solid; border-bottom: 2px solid;">Name</th>
                                        <th style="border-top: 2px solid; border-bottom: 2px solid;">Debit</th>
                                        <th style="border-top: 2px solid; border-bottom: 2px solid;">Credit</th>
                                        <th style="border-top: 2px solid; border-bottom: 2px solid;">Balance</th>
                                    </tr>
                                    <tr ng-show="details.length>0">
                                        <td></td>
                                        <td></td>
                                        <td>{{opening_balance_date}}</td>
                                        <td>Opening Balance</td>
                                        <td><span ng-if="opening_balance_type=='debit'">{{opening_balance}}</span></td>
                                        <td><span ng-if="opening_balance_type=='credit'">{{opening_balance}}</span></td>
                                        <td>{{opening_balance}}</td>
                                    </tr>
                                    <tr ng-repeat="d in details" ng-show="details.length>0">
                                        <td>{{d.id}}</td>
                                        <td>{{d.y_journal_voucher_id}}</td>
                                        <td>{{d.jv_date}}</td>
                                        <td>{{d.jv_title}}</td>
                                        <td>
                                            <span ng-if="d.type=='debit'">{{d.amount}}</span>
                                        </td>
                                        <td>
                                            <span ng-if="d.type=='credit'">{{d.amount}}</span>
                                        </td>
                                        <td>
                                            <span>{{d.balance}}</span>
                                        </td>
                                    </tr>
                                    <tr ng-show="details.length == 0">
                                        <td colspan="7"><span class="text-danger">No record found.</span></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-center">This is computer generated statement does not need signature.</td>
                                    </tr>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>   
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>