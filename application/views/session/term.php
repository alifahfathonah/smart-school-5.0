<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" ng-controller="examSettingsController" ng-init="initFinalResultCardGroups()">
    <section class="content-header">
        <h1><i class="fa fa-mortar-board"></i> <?php echo $this->lang->line('academics'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!--.row-->
        <div class="row">
            <div class="col-md-12"> 
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#cumulative_settings" class="nav-link"
                            aria-controls="profile" role="tab"
                            data-toggle="tab"
                            aria-expanded="true"><span
                            class="visible-xs"><i class="fa fa-indent"></i></span><span
                            class="hidden-xs"><?php echo $this->lang->line('terms');?></span></a>
                        </li>
                    </ul>
                    
                    <div class="tab-content" style="padding-top: 15px;">
                        
                        <div class="tab-pane active" id="cumulative_settings">
                            <div class="row">
                                <?php echo $result_card_groups; ?>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            <!--/panel-->
        </div>
    </section>
</div>
<!-- /.content-wrapper -->
