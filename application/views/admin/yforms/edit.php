<script src="<?php echo base_url(); ?>backend/plugins/ckeditor/ckeditor.js"></script>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-mortar-board"></i> <?php echo $this->lang->line('academics'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Page Content -->
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3" style="height: 630px; max-height: 630px; overflow-y: auto;">
                    <?php include(APPPATH . "views/admin/yforms/tags.php"); ?>
                </div>
                <div class="col-md-9">
                    
                    
                    <form action="<?php echo base_url('admin/Examination/updateResultCard');?>" method="post">
                        <div class="col-md-12">
                            <div class="white-box">
                                <input type="hidden" name="id" value="<?php echo $template->id; ?>" />
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><?php echo lang("title"); ?></label>
                                            <input type="text" class="form-control" required="required" name="title" placeholder="title" value="<?php echo $template->name; ?>"/>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label><?php echo lang("description"); ?></label>
                                            <textarea id="editor1" class="form-control ss" rows="15" name="html" placeholder="Enter text ..."><?php echo $template->html; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                        <!-- Start::Container -->
                        <div class="col-md-12">
                            <a href="<?php echo base_url(); ?>admin/Examination/resultcard" class="btn btn-default pull-right" style="margin-left: 5px;"><?php echo lang("cancel"); ?></a>
                            <button type="submit" class="btn btn-info pull-right"> <?php echo lang("update"); ?></button>
                        </div>
                        <!-- End::Container -->
                    </form>
                    
                </div>
            </div>
        </div>
    </section>
</div>
<script>
$(document).ready(function(){
    CKEDITOR.replace('editor1', {
        allowedContent: true
    });
});
</script>