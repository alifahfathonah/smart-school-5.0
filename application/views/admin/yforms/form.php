<?php include(APPPATH . "views/inc/header.php"); ?>
<?php include(APPPATH . "views/inc/menu.php"); ?>
<?php include(APPPATH . "views/inc/sidebar.php"); ?>
<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title"><?php echo lang('lbl_new_form') ?></h4>
            </div>
            <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

                <ol class="breadcrumb">
                    <li><a href="#"><?php echo lang('crumb_dashboard') ?></a></li>
                    <li><a href="#"><?php echo lang('lbl_forms') ?></a>
                    </li>
                    <li class="active"><?php echo lang('lbl_new_form') ?></li>
                </ol>
            </div>
        </div>

        
        <!-- Page Content -->
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3" style="height: 630px; max-height: 630px; overflow-y: auto;">
                    <?php include(APPPATH . "views/forms/tags.php"); ?>
                </div>
                <div class="col-md-9">
                    
                    <form class="form-material" action="<?php echo base_url('forms/save');?>" method="post">
                        <div class="col-md-12">
                            <div class="white-box">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><?php echo lang("lbl_template_name"); ?></label>
                                            <input type="text" class="form-control" required="required" name="title" placeholder="title" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><?php echo lang("title_category"); ?></label>
                                            <select class="form-control" name="category_id" required="required">
                                                <option value="" disabled="">---<?php echo lang("select_category"); ?>---</option>
                                                <?php foreach ($formCategories as $cat) { ?>
                                                    <option value="<?php echo $cat->id; ?>"><?php echo $cat->name; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label><?php echo lang("description"); ?></label>
                                            <textarea class="mymce form-control" rows="15" name="html" placeholder="Enter text ..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                        <!-- Start::Container -->
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-info pull-right"> <?php echo lang("lbl_save"); ?></button>
                        </div>
                        <!-- End::Container -->
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
    <?php include(APPPATH . "views/inc/footer.php"); ?>