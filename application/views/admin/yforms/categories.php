<?php include(APPPATH . "views/inc/header.php"); ?>
<?php include(APPPATH . "views/inc/menu.php"); ?>
<?php include(APPPATH . "views/inc/sidebar.php"); ?>
<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title"><?php echo lang('lbl_form_categories') ?></h4>
            </div>
            <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

                <ol class="breadcrumb">
                    <li><a href="#"><?php echo lang('crumb_dashboard') ?></a></li>
                    <li><a href="#"><?php echo lang('lbl_forms') ?></a>
                    </li>
                    <li class="active"><?php echo lang('lbl_form_categories') ?></li>
                </ol>
            </div>
        </div>

        <!-- Page Content -->
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <?= $form_categories; ?>
                </div>
            </div>
        </div>
        <!-- /.Page Content -->
        
    </div>
    <!-- /.container-fluid -->
<?php include(APPPATH . "views/inc/footer.php"); ?>