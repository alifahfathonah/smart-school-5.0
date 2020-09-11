<div class="content-wrapper" style="min-height: 946px;" ng-controller="ledgerController">
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
                    <div class="box-body" id="ledgerSummaryFormBody">
                        <form name="ledgerSummaryForm" ng-submit="getLedgerSummaryData(ledgerSummaryForm.$valid)" novalidate="">
                            <div class="form-group">
                                <label>From:</label>
                                <input type="text" name="from_date" class="form-control mydatepicker-autoclose" ng-model="formModel2.from_date" ng-init="formModel2.from_date='<?php echo $from_date; ?>'"/>
                            </div>
                            <div class="form-group">
                                <label>To:</label>
                                <input type="text" name="to_date" class="form-control mydatepicker-autoclose" ng-model="formModel2.to_date" ng-init="formModel2.to_date='<?php echo $to_date; ?>'"/>
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
                                        <td colspan="8" style="border-top: 0px solid;">
                                            <h3><strong>Accounts Summary</strong></h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Today:</th>
                                        <td colspan="2"><?php echo date("Y-m-d"); ?></td>
                                        <th>Statement Period:</th>
                                        <td colspan="3"><?php echo $from_date ." - ". $to_date; ?></td>
                                    </tr>
                                    <tr>
                                        <th style="border-top: 2px solid; border-bottom: 2px solid;">ID</th>
                                        <th style="border-top: 2px solid; border-bottom: 2px solid;">Account Number</th>
                                        <th style="border-top: 2px solid; border-bottom: 2px solid;">Account Title</th>
                                        <th style="border-top: 2px solid; border-bottom: 2px solid;">Type</th>
                                        <th style="border-top: 2px solid; border-bottom: 2px solid;">Opening</th>
                                        <th style="border-top: 2px solid; border-bottom: 2px solid;">Debit</th>
                                        <th style="border-top: 2px solid; border-bottom: 2px solid;">Credit</th>
                                        <th style="border-top: 2px solid; border-bottom: 2px solid;">Closing</th>
                                    </tr>
                                    <tr ng-repeat="s in lederSummary" ng-if="lederSummary.length>0">
                                        <td>{{s.id}}</td>
                                        <td>{{s.account_no}}</td>
                                        <td>{{s.account_title}}</td>
                                        <td>{{s.account_type}}</td>
                                        <td>{{s.opening_balance}}</td>
                                        <td>{{s.debit}}</td>
                                        <td>{{s.credit}}</td>
                                        <td>{{s.closing_balance}}</td>
                                    </tr>
                                    <tr ng-if="lederSummary.length==0">
                                        <td colspan="8"><span class="text-danger">No record found!</span></td>
                                    </tr>
                                    <tr>
                                        <th style="border-top: 2px solid; border-bottom: 2px solid; text-align: right;" colspan="4">Total</th>
                                        <th style="border-top: 2px solid; border-bottom: 2px solid;">{{opening_total}}</th>
                                        <th style="border-top: 2px solid; border-bottom: 2px solid;">{{debit_total}}</th>
                                        <th style="border-top: 2px solid; border-bottom: 2px solid;">{{credit_total}}</th>
                                        <th style="border-top: 2px solid; border-bottom: 2px solid;">{{closing_total}}</th>
                                    </tr>
                                    <tr>
                                        <td colspan="8" class="text-center">This is computer generated statement does not need signature.</td>
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