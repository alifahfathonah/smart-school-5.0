<script src="<?php echo base_url(); ?>backend/plugins/ckeditor/ckeditor.js"></script>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" ng-controller="templateController">
    <section class="content-header">
        <h1><i class="fa fa-mortar-board"></i> <?php echo $this->lang->line('academics'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Alert message -->
        <?php if (!empty($this->session->flashdata("alert"))) { ?>
            <div class="alert alert-<?php echo $this->session->flashdata('alert')['status'] ?>"> 
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> 
                <?php echo $this->session->flashdata("alert")["message"] ?>
            </div>
        <?php } ?>
        <!-- End of alert -->

        <div class="modal fade" id="myModalView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content modal-media-content">
                    <div class="modal-header modal-media-header">
                        <button type="button" class="close" data-dismiss="modal">×</button>
                        <h4 class="box-title">View Result Card</h4> 
                    </div>

                    <div class="modal-body pt0 pb0">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12" ng-bind-html="myDiv">
                                </div>
                            <!--./col-md-12-->       
                        </div>
                        <!--./row--> 
                    </div>
                </div>
            </div>    
        </div>

        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content modal-media-content">
                    <div class="modal-header modal-media-header">
                        <button type="button" class="close" data-dismiss="modal">×</button>
                        <h4 class="box-title">Result Card Form</h4> 
                    </div>

                    <div class="modal-body pt0 pb0">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <form id="formadd" method="post" class="ptt10">
                                    <?php echo $this->customlib->getCSRF(); ?>
                                    <div class="row">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" id="title" name="title" class="form-control" />
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo lang("description"); ?></label>
                                            <textarea id="editor1" name="description" class="form-control ss" rows="15" name="html" placeholder="Enter text ..."></textarea>
                                        </div>
                                        <div class="form-group switch-inline">
                                            <label>Set as default</label>
                                            <div class="material-switch switchcheck">
                                                <input id="default" name="default" type="checkbox" class="chk" value="1">
                                                <label for="default" class="label-success"></label>
                                            </div>
                                        </div>
                                    </div>  
                                </form>                       
                            </div>
                            <!--./col-md-12-->       
                        </div>
                        <!--./row--> 
                    </div>
                    <div class="box-footer">
                        <div class="pull-right paddA10">
                            <a onclick="saveResultCard()" class="btn btn-info pull-right">Save</a>
                        </div>
                    </div>
                </div>
            </div>    
        </div>

        <!-- Page Content -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-search"></i> Template Forms</h3>
                <!--<button type="button" class="btn btn-sm btn-primary" style="float: right;" data-toggle="modal" data-target="#myModal" autocomplete="off"><i class="fa fa-plus"></i> Add</button>-->
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-default">
                        <tr>
                            <th><?php echo lang('class') ?></th>
                            <th><?php echo lang('lbl_template_name') ?></th>
                            <th style="text-align: center;"><?php echo lang('lbl_action') ?></th>
                        </tr>
                        <?php if (count($templates) > 0) { ?>
                            <?php foreach ($templates as $template) { ?>
                                <tr>
                                    <td><?php echo $template->class_name; ?></td>
                                    <td><?php echo $template->name; ?></td>
                                    <td style="width: 150px; text-align: center;">
                                        <button type="button" ng-click="getTemplate(<?php echo $template->id; ?>)" data-toggle="modal" data-target="#myModalView" autocomplete="off" class="btn btn-success btn-circle" ><i class="fa fa-eye"></i></button>
                                        <a type="button" href="<?php echo site_url(); ?>admin/Examination/editResultCard/<?php echo $template->id; ?>" class="btn btn-info btn-sm btn-circle" ><i class="fa fa-pencil"></i></a>
                                        <a href="javascript:void(0)" value="<?php echo $template->id; ?>,forms/delete" class="sa-warning btn btn-danger btn-sm btn-circle text-white <?php if($template->is_custom === 'Yes') { echo "disabled"; } ?>"><i class="fa  fa-trash-o"></i></a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
        <!-- Page Content End -->
    </section>
</div>
<script>
$(document).ready(function(){
    CKEDITOR.replace('editor1', {
        allowedContent: true
    });
});

function saveResultCard(){
    var value = CKEDITOR.instances['editor1'].getData();
    var formData = {
        title: $("#title").val(),
        description: value,
        is_default: $("#default").val()
    };
    $.ajax({
        url: '<?php echo base_url(); ?>admin/Examination/saveResultCard/',
        type: 'POST',
        dataType: 'json',
        data: formData,
        success: function (data) {
            console.log(data);
            /*if (data.status == "fail") {
                var message = "";
                $.each(data.error, function (index, value) {
                    message += value;
                });
                errorMsg(message);
            } else {

                successMsg(data.message);
                window.location.reload(true);
            }*/

        },
        error: function () {
            alert("Fail");
        }
    });
}
</script>