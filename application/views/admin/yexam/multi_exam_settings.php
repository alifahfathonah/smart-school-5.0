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
                            class="hidden-xs"><?php echo $this->lang->line('result_card_groups');?></span></a>
                        </li>
                    </ul>
                    
                    <div class="tab-content" style="padding-top: 15px;">
                        
                        <div class="tab-pane active" id="cumulative_settings">
                            <div class="row">
                                <!--<div class="well" style="background:#e4e7ea;" ng-controller="resultCardGroupController">
                                    <div class="row">
                                        <div class="col-md-4" id="examDetailsSessions">
                                            <div class="form-group">
                                                <label class="control-label"><?php //echo $this->lang->line('session'); ?></label>
                                                <small class="req"> *</small>
                                                <select class="form-control" id="sessions" ng-model="selecedVal1_1_1" ng-init="selecedVal1_1_1='<?= $tab_cumulative_settings_selected_session_id; ?>';initSessions()">
                                                    <option value=""><?php //echo $this->lang->line('please_select_a_session'); ?></option>
                                                    <option ng-repeat="ses in sessions" value="{{ses.id}}">{{ses.session}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4" id="examDetailsClasses">
                                            <div class="form-group">
                                                <label class="control-label"><?php //echo $this->lang->line('class'); ?></label>
                                                <select class="form-control" id="classes" ng-model="selecedVal_1_1_2" ng-init="selecedVal_1_1_2='<?= $tab_cumulative_settings_selected_class_id; ?>';fetchClasses(selecedVal_1_1_2)" ng-change="loadClassBatches(selecedVal_1_1_2)">
                                                    <option value="all"><?php //echo $this->lang->line('option_all'); ?></option>
                                                    <option ng-repeat="cls in classes" value="{{cls.id}}">{{cls.class}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4" id="examDetailsSections">
                                            <div class="form-group">
                                                <label class="control-label"><?php //echo $this->lang->line('section'); ?></label>
                                                <select class="form-control" ng-model="selecedVal2_1" id="batches" ng-init="selecedVal2_1='<?= $tab_cumulative_settings_selected_batch_id; ?>'">
                                                    <option value="all"><?php //echo $this->lang->line('option_all'); ?></option>
                                                    <option ng-repeat="batch in batches" value="{{batch.section_id}}">{{batch.section}}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-right">
                                            <a class="btn btn-sm btn-primary text-white" href="<?php //echo base_url() ?>admin/Examination/multi_exam_settings?tab=tab_cumulative_settings&class_id={{selecedVal_1_1_2}}&batch_id={{selecedVal2_1}}&session_id={{selecedVal1_1_1}}"><?php //echo 'search'; ?></a>
                                        </div>
                                    </div>
                                </div>-->
                                <?php echo $cumulative_settings; ?>
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
