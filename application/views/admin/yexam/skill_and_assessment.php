<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" ng-controller="skillAndAssessmentCtrl" ng-init="initClasses();">
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
                            <a href="#skill_and_assessment_groups" class="nav-link"
                            aria-controls="profile" role="tab"
                            data-toggle="tab"
                            aria-expanded="true"><span
                            class="visible-xs"><i class="fa fa-indent"></i></span><span
                            class="hidden-xs"><?php echo $this->lang->line('groups_slash_categories');?></span></a>
                        </li>
                        <li role="presentation" class="">
                            <a href="#mark_skills_and_assessments" class="nav-link"
                            aria-controls="profile" role="tab"
                            data-toggle="tab"
                            aria-expanded="true"><span
                            class="visible-xs"><i class="fa fa-indent"></i></span><span
                            class="hidden-xs"><?php echo $this->lang->line('mark_skills_and_assessments');?></span></a>
                        </li>
                    </ul>
                    
                    <div class="tab-content" style="padding-top: 15px;">
                        <div class="tab-pane active" id="skill_and_assessment_groups">
                            <div class="row">
                                <?php echo $skill_and_assessment_groups; ?>
                            </div>
                        </div>

                        <div class="tab-pane" id="mark_skills_and_assessments">
                            <div class="row" id="filterRow">
                                <form name="mark_skills_and_assessments_form" ng-submit="fetchStudentsForSkills()" novalidate="">
                                    
                                    <div class="col-md-12 col-md-offset-0">
                                        <div class="col-md-3" id="SA_Filter_Classes">
                                            <div class="form-group">
                                                <label class="control-label"><?php echo lang('classes') ?></label>
                                                <select class="form-control" ng-model="filterModel.class_id" ng-change="initBatches(filterModel.class_id); initSkillAndAssessmentGroups(filterModel.class_id)" required="">
                                                    <option value=""><?php echo lang('please_select_a_class') ?></option>
                                                    <option ng-repeat="cls in classes" value="{{cls.id}}">{{cls.class}}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3" id="SA_Filter_Batches">
                                            <div class="form-group">
                                                <label class="control-label"><?php echo lang('section') ?></label>
                                                <select class="form-control" ng-model="filterModel.batch_id" required="">
                                                    <option value=""><?php echo lang('please_select_a_section') ?></option>
                                                    <option ng-repeat="bth in batches" value="{{bth.section_id}}">{{bth.section}}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3" id="SA_Groups">
                                            <div class="form-group">
                                                <label class="control-label"><?php echo lang('groups') ?></label>
                                                <select required="" class="form-control" ng-model="filterModel.sa_group_id">
                                                    <option value="">Select Assessment Group</option>
                                                    <option ng-repeat="g in SAGroups" ng-value="g.id">{{g.assessment}}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-lg-12 col-md-12">
                                            <div class="col-md-4">
                                                <input type="submit" class="btn btn-primary" value="<?php echo lang('search') ?>" />
                                            </div>
                                        </div>
                                    </div>
                                    

                                </form>
                            </div>

                            <div class="row" id="resultantRow" style="padding:15px;">
                                <form name="skillAndAssessmentEvaluationForm" ng-if="students.length>0" ng-submit="saveSkillAndAssessmentEvaluation()">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-default">
                                                <tr>
                                                    <th>Full Names</th>
                                                    <th>REG_NO</th>
                                                    <th>1 (Poor)</th>
                                                    <th>2 (Fair)</th>
                                                    <th>3 (Good)</th>
                                                    <th>4 (Very Good)</th>
                                                    <th>5 (Excellent)</th>
                                                </tr>
                                                <tr ng-repeat="std in students">
                                                    <td>{{std.student_name}}</td>
                                                    <td>{{std.rollno}}</td>
                                                    <td><div class="form-group"><input type="radio" name="{{std.rollno}}" ng-model="evalModel[std.student_id]" ng-init="evalModel[std.student_id]=std.evaluation" value="poor" /></div></td>
                                                    <td><div class="form-group"><input type="radio" name="{{std.rollno}}" ng-model="evalModel[std.student_id]" ng-init="evalModel[std.student_id]=std.evaluation" value="fair" /></div></td>
                                                    <td><div class="form-group"><input type="radio" name="{{std.rollno}}" ng-model="evalModel[std.student_id]" ng-init="evalModel[std.student_id]=std.evaluation" value="good" /></div></td>
                                                    <td><div class="form-group"><input type="radio" name="{{std.rollno}}" ng-model="evalModel[std.student_id]" ng-init="evalModel[std.student_id]=std.evaluation" value="verygood" /></div></td>
                                                    <td><div class="form-group"><input type="radio" name="{{std.rollno}}" ng-model="evalModel[std.student_id]" ng-init="evalModel[std.student_id]=std.evaluation" value="excellent" /></div></td> 
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary pull-right">Save</button>
                                    </div>
                                </form>
                                <div class="col-md-12" ng-if="students.length == 0">
                                    <span class="text-danger">no record found!</span>                                
                                </div>

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
