<?php include(APPPATH . "views/inc/header.php"); ?>
<?php include(APPPATH . "views/inc/menu.php"); ?>
<?php include(APPPATH . "views/inc/sidebar.php"); ?>
<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title"><?php echo lang('lbl_forms') ?></h4>
            </div>
            <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

                <ol class="breadcrumb">
                    <li><a href="#"><?php echo lang('crumb_dashboard') ?></a></li>
                    <li><a href="#"><?php echo lang('lbl_forms') ?></a>
                    </li>
                    <li class="active"><?php echo lang('menu_view_all') ?></li>
                </ol>
            </div>
        </div>


        <!-- sample modal content -->
<!--        <div id="myModal" class="modal fade forms-view-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Form Name</h4>
                    </div>

                    <div class="modal-body">
                        <input type="text" name="templateId" class="form-control"/>
                        <input type="text" name="templateName" class="form-control"/>
                        <input type="text" name="templateCategory" class="form-control"/>
                        <input type="text" name="templateHtml" class="form-control"/>
                    </div>

                    <div class="modal-footer">
                        Footer
                    </div>

                </div>
            </div>
        </div>-->
        <!-- /.modal -->


        <!-- Alert message -->
        <?php if (!empty($this->session->flashdata("alert"))) { ?>
            <div class="alert alert-<?php echo $this->session->flashdata('alert')['status'] ?>"> 
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> 
                <?php echo $this->session->flashdata("alert")["message"] ?>
            </div>
        <?php } ?>
        <!-- End of alert -->

        <!-- Page Content -->
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <div class="table-responsive">
                        <table class="myTable display nowrap" cellspacing="0">
                            <thead>
                                <tr>
                                    <th><?php echo lang('lbl_template_name') ?></th>
                                    <!--<th><?php echo lang('lbl_is_custom') ?></th>-->
                                    <th><?php echo lang('lbl_template_category') ?></th>
                                    <th style="text-align: center;"><?php echo lang('th_action') ?></th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th><?php echo lang('lbl_template_name') ?></th>
                                    <!--<th><?php echo lang('lbl_is_custom') ?></th>-->
                                    <th><?php echo lang('lbl_template_category') ?></th>
                                    <th style="text-align: center;"><?php echo lang('th_action') ?></th>
                                </tr>
                            </tfoot>
                            <tbody id="myTableBody">
                                <?php
                                echo "<pre>";
                                print_r($templates);
                                echo "</pre>";
                                //die();
                                if (count($templates) > 0) {
                                    foreach ($templates as $template) {
                                        ?>
                                        <tr>
                                            <td><?php echo $template->name; ?></td>
<!--                                            <td><?php echo $template->is_custom; ?></td>-->
                                            <td><?php echo $template->form_category; ?></td>
                                            <td style="text-align: center;">
                                                <!--<a type="button" href="javascript:void(0)"  data-toggle="modal" data-form-template-id="<?php echo $template->id; ?>" data-form-template-name="<?php echo $template->name; ?>" data-form-template-html="<?php echo $template->html; ?>" data-form-template-category="<?php echo $template->form_category; ?>" data-target=".forms-view-modal-lg" class="btn btn-success btn-circle" ><i class="fa fa-eye"></i></a>-->
                                                <a type="button" href="<?php echo site_url(); ?>forms/show?id=<?php echo $template->id; ?>" target="_blank" class="btn btn-success btn-circle" >
                                                    <?php if($template->is_custom === 'Yes') { ?>
                                                    <i class="fa  fa-print"></i>
                                                    <?php } else { ?>
                                                    <i class="fa fa-eye"></i>
                                                    <?php } ?>
                                                </a>
                                                <a type="button" href="<?php echo site_url(); ?>forms/edit?id=<?php echo $template->id; ?>" class="btn btn-info btn-circle" ><i class="fa fa-pencil"></i></a>
                                                <a href="javascript:void(0)" value="<?php echo $template->id; ?>,forms/delete" class="sa-warning btn btn-danger btn-circle text-white <?php if($template->is_custom === 'Yes') { echo "disabled"; } ?>"><i class="fa  fa-trash-o"></i></a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Page Content End -->

    </div>
    <!-- /.container-fluid -->
    <?php include(APPPATH . "views/inc/footer.php"); ?>

<!--    <script>
        $('.forms-view-modal-lg').on('show.bs.modal', function (e) {

            //get data-id attribute of the clicked element
            var templateId = $(e.relatedTarget).data('form-template-id');
            var templateName = $(e.relatedTarget).data('form-template-name');
            var templateCategory = $(e.relatedTarget).data('form-template-category');
            var templateHtml = $(e.relatedTarget).data('form-template-html');

            //populate the textbox
            $(e.currentTarget).find('#myModalLabel').text(templateName);
            $(e.currentTarget).find('input[name="templateId"]').val(templateId);
            $(e.currentTarget).find('input[name="templateName"]').val(templateName);
            $(e.currentTarget).find('input[name="templateCategory"]').val(templateCategory);
            $(e.currentTarget).find('input[name="templateHtml"]').val(templateHtml);
        });
    </script>-->