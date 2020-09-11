<div class="content-wrapper" style="min-height: 946px;" ng-controller="jvController" ng-init="initAccounts()">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1><i class="fa fa-object-group"></i> Manage Accounts</h1>
    </section>

    <!-- Journal voucher Add New Modal --> 
    <div id="jv-add-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <form name="jvform" ng-submit="savejv(jvform.$valid)" novalidate="">
                <div class="modal-content" id="jv-add-form-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <div class="modal-title">
                            <strong style="font-size: 18px;">
                                Add New Journal Voucher
                            </strong>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="text" name="date" class="form-control mydatepicker-autoclose-modal" ng-model="formModel.date" required="" ng-init="formModel.date='<?php echo date('d/m/Y'); ?>'" />
                                </div>
                            </div>
                            
                            <!--<div class="col-md-6">
                                <div class="form-group">
                                    <label>JV Manual ID</label>
                                    <input type="text" name="jv_manual_id" class="form-control" ng-model="formModel.jv_manual_id"/>
                                </div>
                            </div>-->
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Document Title</label>
                                    <input type="text" name="jv_title" class="form-control" ng-model="formModel.jv_title" required="" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Document Description</label>
                                    <textarea cols="50" rows="3" class="form-control" ng-model="formModel.jv_description"></textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <h4 style="margin-top:0; font-weight: 700;">Transcations</h4>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="min-width: 200px;">Accounts*</th>
                                            <th>Memo</th>
                                            <th>Type</th>
                                            <th>Amount*</th>
                                        </tr>
                                        <tr ng-repeat="obj in formModel.transcations">
                                            <td style="min-width: 200px;">
                                                <div class="form-group">
                                                    <select class="form-control" ng-model="formModel.transcations[$index].account_id" ng-init="formModel.transcations[$index].account_id=''" required="">
                                                        <option value="">Select account</option>
                                                        <option value="{{acc.id}}" ng-repeat="acc in accounts">{{acc.account_no}} | {{acc.account_title}}</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <textarea class="form-control" rows="2" cols="100" ng-model="formModel.transcations[$index].memo"></textarea>
                                                </div>
                                            </td>
                                            <td style="width: 80px;">
                                                <div class="form-group">
                                                    <select class="form-control" ng-model="formModel.transcations[$index].type" ng-init="formModel.transcations[$index].type='debit'" required="">
                                                        <option value="debit">Dr.</option>
                                                        <option value="credit">Cr.</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td style="min-width: 80px;">
                                                <div class="form-group">
                                                    <input type="number" name="amount" class="form-control" ng-blur="setDebitCreditValue()" ng-model="formModel.transcations[$index].amount" required="" />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"><a href="javascript:void(0);" ng-click="addNewRow()">Add a row</a></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right" colspan="4">
                                                Debit: <span class="text-monospace">{{debit}}</span><br/>
                                                Credit: <span class="text-monospace">{{credit}}</span><br/>
                                                Balance: <span class="text-monospace">{{debit - credit}}</span>
                                            </td>
                                        </tr>
                                        <tr ng-show="alert.status">
                                            <td colspan="4">
                                                <div class="alert alert-{{alert.status}}"><span>{{alert.message}}</span></div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-info waves-effect">Add entry</button>
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">Journal Voucher</h3>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#jv-add-modal">Add New</button>  
                            </div>
                        </div>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <div class="box-body">
                        <?php echo $jvs; ?>
                    </div>
                </div>            
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div>
<script>
    var datepicker_config2 = {
        changeMonth: true,
        changeYear: true,
        showSecond: false,
        controlType: 'select',
        yearRange: "-50:+10",
        dateFormat: 'dd/mm/yy',
        timeFormat: 'hh:mm tt'
    };
    $(".mydatepicker-autoclose-modal").datepicker(datepicker_config2);
    var enforceModalFocusFn = $.fn.modal.Constructor.prototype.enforceFocus;
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    $confModal.on('hidden', function() {
        $.fn.modal.Constructor.prototype.enforceFocus = enforceModalFocusFn;
    });
    $confModal.modal({ backdrop : false });
</script>