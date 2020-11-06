<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" ng-controller="examSettingsController" ng-init="initFinalResultCardGroups()">
    <section class="content-header">
        <h1><i class="fa fa-mortar-board"></i> <?php echo $this->lang->line('academics'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        
        <?php if ($this->session->flashdata('error_message')) { ?>
            <div class="alert alert-danger"> <?= $this->session->flashdata('error_message') ?> </div>
        <?php } ?>

        <?php if ($this->session->flashdata('success_message')) { ?>
            <div class="alert alert-success"> 
                <ul>
                    <?php foreach($this->session->flashdata('success_message') as $msg) { ?>
                        <li><span class="text-<?= $msg['status'] ?>"><?= $msg["message"] ?></span></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>

        <!--.row-->
        <div class="row">
            <div class="col-md-12"> 
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" style="display:none;" class="<?php if ($selected_tab == 'tab_subject_groups') { echo "active"; } ?>">
                            <a href="#subject_groups" class="nav-link"
                            aria-controls="profile" role="tab"
                            data-toggle="tab"
                            aria-expanded="true"><span
                            class="visible-xs"><i class="fa fa-calculator"></i></span><span
                            class="hidden-xs"><?php echo $this->lang->line('subject_group'); ?></span></a>
                        </li>
                        <li role="presentation" class="<?php if ($selected_tab == 'exams') { echo "active"; } else if($selected_tab == NULL || $selected_tab == "") { echo "active"; } ?>">
                            <a href="#exams" class="nav-link"
                            aria-controls="profile" role="tab"
                            data-toggle="tab"
                            aria-expanded="true"><span
                            class="visible-xs"><i class="fa fa-calculator"></i></span><span
                            class="hidden-xs"><?php echo $this->lang->line('assessments'); ?></span></a>
                        </li>
                        <li role="presentation" class="<?php if ($selected_tab == 'exam_details') { echo "active"; } ?>">
                            <a href="#exam_details" class="nav-link"
                            aria-controls="profile" role="tab"
                            data-toggle="tab"
                            aria-expanded="true"><span
                            class="visible-xs"><i class="fa fa-indent"></i></span><span
                            class="hidden-xs"><?php echo $this->lang->line('lbl_exam_details'); ?></span></a>
                        </li>
                        <li role="presentation" class="<?php if ($selected_tab == 'passing_rules') { echo "active"; } ?>">
                            <a href="#passing_rules" class="nav-link"
                            aria-controls="profile" role="tab"
                            data-toggle="tab"
                            aria-expanded="true"><span
                            class="visible-xs"><i class="fa fa-indent"></i></span><span
                            class="hidden-xs"><?php echo $this->lang->line('assessment_passing_rules');?></span></a>
                        </li>
                        <li role="presentation" class="<?php if ($selected_tab == 'grading') { echo "active"; } ?>">
                            <a href="#grading" class="nav-link"
                            aria-controls="profile" role="tab"
                            data-toggle="tab"
                            aria-expanded="true"><span
                            class="visible-xs"><i class="fa fa-indent"></i></span><span
                            class="hidden-xs"><?php echo $this->lang->line('grade');?></span></a>
                        </li>
                        <li role="presentation" class="<?php if ($selected_tab == 'restore_settings') { echo "active"; } ?>">
                            <a href="#restore_settings" class="nav-link"
                            aria-controls="restore_settings" role="tab"
                            data-toggle="tab"
                            aria-expanded="true"><span
                            class="visible-xs"><i class="fa fa-indent"></i></span><span
                            class="hidden-xs"><?php echo $this->lang->line('restore');?></span></a>
                        </li>
                    </ul>
                    
                    <div class="tab-content" style="padding-top: 15px;">
                        <div class="tab-pane <?php if ($selected_tab === 'tab_subject_groups') { echo 'active'; }?>" style="display:none;" id="subject_groups">
                            <?php echo $subject_groups; ?>
                        </div>

                        <div class="tab-pane <?php if ($selected_tab === 'exams') { echo "active"; } else if($selected_tab == NULL || $selected_tab == '') { echo 'active'; } ?>" id="exams">
                            <?php echo $exams_new; ?>
                        </div>

                        <div class="tab-pane <?php if ($selected_tab === 'exam_details') { echo "active"; } ?>" id="exam_details">
                            <?php echo $exam_details; ?>
                        </div>


                        <div class="tab-pane <?php if ($selected_tab === 'passing_rules') { echo "active"; } ?>" id="passing_rules">
                            <?php echo $passing_rules; ?>
                        </div>

                        <div class="tab-pane <?php if ($selected_tab === 'grading') { echo "active"; } ?>" id="grading">
                            
                            <!--<div class="well" style="background:#e4e7ea;" ng-controller="subjectController">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label"><?php //echo $this->lang->line('class'); ?></label>
                                            <select class="form-control" id="classes" ng-model="selecedVal" ng-init="selecedVal='<?= $tab_subject_groups_class_id; ?>';fetchClasses(selecedVal)" ng-change="loadClassBatches(selecedVal)">
                                                <option value="all"><?php //echo $this->lang->line('option_all'); ?></option>
                                                <option ng-repeat="cls in classes" value="{{cls.id}}">{{cls.class}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <a class="btn btn-sm btn-primary text-white" href="<?php //echo base_url() ?>admin/Examination/single_exam_settings?tab=tab_subject_groups&class_id={{selecedVal}}&batch_id={{selecedVal2}}&&session_id={{selecedVal1}}"><?php //echo 'search'; ?></a>
                                    </div>
                                </div>
                            </div>-->

                            <?php echo $grades; ?>
                        </div>

                        <div class="tab-pane <?php if ($selected_tab === 'restore_settings') { echo "active"; } ?>" id="restore_settings">
                            <form method="post" action="<?php echo base_url(); ?>admin/Examination/restore_session_settings">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>From</label>
                                            <select class="form-control" name="session_from" required>
                                                <option>Select a session</option>
                                                <?php if(count($sessions) > 0) { foreach($sessions as $se) { ?>
                                                    <option value="<?php echo $se->id; ?>" <?php if($se->id == $active_session_id){ echo "selected"; }?> ><?php echo $se->session; ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>To</label>
                                            <select class="form-control" name="session_to" required>
                                                <option>Select a session</option>
                                                <?php if(count($sessions) > 0) { foreach($sessions as $se) { ?>
                                                    <option value="<?php echo $se->id; ?>" <?php if($se->id == $active_session_id){ echo "selected"; }?>><?php echo $se->session; ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="selected_tab" value="restore_settins" />
                                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--/panel-->
        </div>
    </section>
</div>
<!-- /.content-wrapper -->
