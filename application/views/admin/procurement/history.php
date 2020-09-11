<div class="content-wrapper" style="min-height: 946px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i> <?php echo $this->lang->line('inventory'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('history', 'can_add')) { ?>
                <div class="col-md-12">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('requisition_history'); ?></h3>
                        </div><!-- /.box-header -->
                        <!-- form start -->
                        <div class="box-body"><?php echo $requisitions; ?></div>
                    </div>            
                </div><!--/.col (right) -->
                <!-- left column -->
            <?php } ?>
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div>