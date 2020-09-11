
<style type="text/css">
    @media print
    {
        .no-print, .no-print *
        {
            display: none !important;
        }
    }
</style>
<div class="content-wrapper" style="min-height: 946px;" ng-controller="accountController" ng-init="initDepartments(); initStaff();">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1><i class="fa fa-object-group"></i> <?php echo $this->lang->line('inventory'); ?></h1>
    </section>

    <!-- Modals -->
    <div id="requisition-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <form name="requisitionform" ng-submit="addRequisition(requistionform.$valid)" novalidate="">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <div class="modal-title"><strong style="font-size: 18px;">Add Requisition form</strong>
                        </div>
                    </div>
                    <div class="modal-body" id="requisition-modal-body">
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('item'); ?></label><small class="req"> *</small>
                            <input id="items" name="items" placeholder="" type="text" required="" class="form-control" ng-model="reqModel.item"/>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('quality'); ?></label><small class="req"> *</small>
                            <input id="qualities" name="qualities" placeholder="" type="text" required="" class="form-control" ng-model="reqModel.quality"/>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('quantity'); ?></label><small class="req"> *</small>
                            <input d="quality" name="quality" placeholder="" type="number" required="" class="form-control"  ng-model="reqModel.quantity"/>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('specification'); ?></label><small class="req"> *</small>
                            <input id="specification" name="specification" placeholder="" required="" type="text" class="form-control" ng-model="reqModel.specification"/>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('price'); ?></label><small class="req"> *</small>
                            <input id="price" name="price" placeholder="" required="" type="number" class="form-control" ng-model="reqModel.price"/>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('previous_price'); ?></label>
                            <input autofocus="" id="price_range" name="price_range" placeholder="" type="number" class="form-control" ng-model="reqModel.previous_price" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
                        <button type="submit" class="btn btn-success waves-effect waves-light"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="requisition-edit-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <form name="requisitioneditform" ng-submit="updateRequisition(requistioneditform.$valid)" novalidate="">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <div class="modal-title"><strong style="font-size: 18px;">Edit Requisition form</strong>
                        </div>
                    </div>
                    <div class="modal-body" id="requisition-edit-modal-body">
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('item'); ?></label><small class="req"> *</small>
                            <input id="items" name="items" placeholder="" type="text" required="" class="form-control" ng-model="editModel.item"/>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('quality'); ?></label><small class="req"> *</small>
                            <input id="qualities" name="qualities" placeholder="" type="text" required="" class="form-control" ng-model="editModel.quality"/>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('quantity'); ?></label><small class="req"> *</small>
                            <input d="quality" name="quality" placeholder="" type="number" required="" class="form-control"  ng-model="editModel.quantity"/>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('specification'); ?></label><small class="req"> *</small>
                            <input id="specification" name="specification" placeholder="" required="" type="text" class="form-control" ng-model="editModel.specification"/>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('price'); ?></label><small class="req"> *</small>
                            <input id="editprice" name="editprice" placeholder="" required="" type="number" class="form-control" ng-model="editModel.price"/>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('previous_price'); ?></label>
                            <input autofocus="" id="price_range" name="price_range" placeholder="" type="number" class="form-control" ng-model="editModel.previous_price" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
                        <button type="submit" class="btn btn-success waves-effect waves-light"><?php echo $this->lang->line('update'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ./Modals -->

    <!-- Main content -->
    <section class="content">
        <div class="alert alert-success" ng-show="alert.message">{{alert.message}}</div>
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('requisition', 'can_add')) { ?>
                <form name="requisitionform1" ng-submit="saveRequisition(requistionform1.$valid)" novalidate="">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?php echo $this->lang->line('add_requisition'); ?></h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('department'); ?></label><small class="req"> *</small>
                                            <select class="form-control" name="department_id" ng-model="formModel.department_id" ng-value="formModel.department_id='<?php echo $this->session->userdata('admin')['department']; ?>'" required="" <?php if(!empty($this->session->userdata('admin')['department'])){ echo 'disabled'; } ?>>
                                                <option value="">Select a department</option>
                                                <option ng-repeat="dept in departments" value="{{dept.id}}">{{dept.department_name}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('staff'); ?></label><small class="req"> *</small>
                                            <select class="form-control" name="staff_id" ng-model="formModel.staff_id" ng-value="formModel.staff_id='<?php echo $this->session->userdata('admin')['id']; ?>'" required="" <?php if(!empty($this->session->userdata('admin')['department'])){ echo 'disabled'; } ?>>
                                                <option value="">Select a member</option>
                                                <option ng-repeat="stf in staff" value="{{stf.id}}">{{stf.name}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('delivery_time'); ?></label><small class="req"> *</small>
                                            <input name="delivery_time" type="text" ng-model="formModel.delivery_time" class="form-control mydatepicker-autoclose" required="" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table">
                                            <thead>
                                                <tr class="bg-primary">
                                                    <td><strong><?php echo $this->lang->line('items'); ?></strong></td>
                                                    <td><strong><?php echo $this->lang->line('quality'); ?></strong></td>
                                                    <td><strong><?php echo $this->lang->line('quantity'); ?></strong></td>
                                                    <td><strong><?php echo $this->lang->line('specification'); ?></strong></td>
                                                    <td><strong><?php echo $this->lang->line('price'); ?></strong></td>
                                                    <td><strong><?php echo $this->lang->line('previous_price'); ?></strong></td>
                                                    <td class="text-center no-print"><strong><?php echo $this->lang->line('action'); ?></strong></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr ng-repeat="requi in requisitions">
                                                    <td>{{requi.item}}</td>
                                                    <td>{{requi.quality}}</td>
                                                    <td>{{requi.quantity}}</td>
                                                    <td>{{requi.specification}}</td>
                                                    <td>{{requi.price}}</td>
                                                    <td>{{requi.previous_price}}</td>
                                                    <td class="text-center">
                                                        <button ng-click="editRow(requi, $index)" type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#requisition-edit-modal"><i class="fa fa-pencil"></i></button>
                                                        <button ng-click="removeRow(requi)" class="btn btn-danger btn-sm" type="button"><i class="fa fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="7" class="text-center"><a href="javascript:void(0);" data-toggle="modal" data-target="#requisition-modal">Click to Add new cell for another item</a></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3 col-md-offset-9 bg-primary" style="padding: 10px;"><b>Total Cost:-</b> {{totalCost}}</div>
                                    <div class="col-md-12 text-center">
                                        <button type="submit" class="btn btn-info" ng-disabled="requisitions.length==0">Make a Requisition</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            <?php } ?>
        </div>
    </section>
</div>