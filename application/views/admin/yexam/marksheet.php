<style>
    .hideBorder{
        border: 0;
        background: none;
    }
</style>
<script>
    function customPrint() {
        $("#marksheet_table").print({
            globalStyles: true,
            mediaPrint: false,
            stylesheet: null,
            noPrintSelector: ".no-print",
            iframe: true,
            append: null,
            prepend: null,
            manuallyCopyFormValues: true,
            deferred: $.Deferred(),
            timeout: 750,
            title: null,
            doctype: '<!doctype html>'
        });
    }
</script>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" ng-controller="marksheetController" ng-init="initSessions(); initClasses();">
    <section class="content-header">
        <h1><i class="fa fa-mortar-board"></i> <?php echo $this->lang->line('marksheet'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Page Content -->
        <div class="row">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-search"></i> Select Criteria</h3>
                        </div>
                        <div class="box-body">
                            <form name="marktsFilterForm" ng-submit="onSubmit(marktsFilterForm.$valid)" novalidate="">
                                <div class="row">
                                    <div class="col-sm-6 col-lg-3 col-md-3 col20" id="marksFilterAcademicYears">
                                        <div class="form-group">
                                            <label class="control-label"><?php echo lang("session"); ?></label>
                                            <small class="req"> *</small>
                                            <select class="form-control" name="academic_year_id" ng-model="filterModel.academic_year_id" required="">
                                                <option value=""><?php echo lang("please_select_a_session"); ?></option>
                                                <option ng-repeat="ay in academicyears" value="{{ ay.id }}">{{ ay.session }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-lg-3 col-md-3 col20" id="marksFilterClasses">
                                        <div class="form-group">
                                            <label class="control-label"><?php echo lang('classes') ?></label>
                                            <small class="req"> *</small>
                                            <select class="form-control" ng-model="filterModel.class_id" ng-change="initBatches(filterModel.class_id)" required="">
                                                <option value=""><?php echo lang('please_select_a_class') ?></option>
                                                <option ng-repeat="cls in classes" value="{{cls.id}}">{{cls.class}}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-lg-3 col-md-3 col20" id="marksFilterBatches">
                                        <div class="form-group">
                                            <label class="control-label"><?php echo lang('section') ?></label>
                                            <small class="req"> *</small>
                                            <select class="form-control" ng-model="filterModel.batch_id" required="" ng-change="initSubjects(filterModel.academic_year_id, filterModel.class_id, filterModel.batch_id)">
                                                <option value=""><?php echo lang('please_select_a_section') ?></option>
                                                <option ng-repeat="bth in batches" value="{{bth.section_id}}">{{bth.section}}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-lg-3 col-md-3 col20" id="marksFilterSubjects">
                                        <div class="form-group">
                                            <label class="control-label"><?php echo lang('subjects') ?></label>
                                            <small class="req"> *</small>
                                            <select class="form-control" ng-model="filterModel.subject_id" ng-change="initExams()" required="">
                                                <option value=""><?php echo lang('please_select_a_subject') ?></option>
                                                <option ng-repeat="sub in subjects" value="{{sub.id}}">{{sub.name}}</option>
                                            </select>
                                        </div>
                                    </div>
                                
                                
                                    <div class="col-sm-6 col-lg-3 col-md-3 col20" id="marksFilterExams">
                                        <div class="form-group">
                                            <label class="control-label"><?php echo lang('assessments') ?></label>
                                            <small class="req"> *</small>
                                            <select class="form-control" ng-show="exams.length == 0" ng-model="filterModel.exam_id">
                                                <option value=""><?php echo lang("no_record_found"); ?></option>
                                            </select>
                                            <select class="form-control" ng-show="exams.length != 0" ng-model="filterModel.exam_detail_id" ng-change="saveExamId(filterModel.exam_detail_id)" required="">
                                                <option value=""><?php echo lang('please_select_an_exam') ?></option>
                                                <option ng-repeat="em in exams" value="{{em.id}}">{{em.title}}</option>
                                            </select>
                                        </div>
                                    </div>  
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <span class="error" ng-show="message"><b>{{ message }}</b></span>
                                        <br/><span>
                                            <a href="javascript:void(0);" data-toggle="modal" data-target="#requestModelMarksheet"  >
                                                <small ng-if="action == 'draft' || action == 'not-approved' " class="text-info"><?php echo lang('lbl_request_for_edit'); ?></small>
                                            </a>
                                            <small ng-if="action == 'inprocess' " ng-class="{custom_disable:action}" class="text-warning"><?php echo lang('lbl_request_in_process'); ?></small>
                                            <small ng-if="action == 'approved' " ng-class="{custom_disable:action}" class="text-success"><?php echo lang('lbl_request_for_approved'); ?></small>
                                        </span>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="submit" class="btn btn-primary pull-right" value="<?php echo lang('search') ?>" />
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box box-primary" id="marksStudentsTable" ng-show="students[0].name">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <button type="button" onclick="customPrint()" class="btn btn-info pull-right" ng-show="exist"><i class="fa fa-print"></i></button>
                        </div>
                        <div class="col-md-12" id="marksheet_table">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="table-responsive m-t-10  col-md-12">
                                            <table id="myTable" style="width:100%; border-spacing: 5px; border-collapse: separate;">
                                                <tr class="text-center"><td colspan="4"><img class="mb-5" src="<?php echo base_url(); ?>uploads/school_content/admin_small_logo/1.png" width="50px" /></td></tr>
                                                <tr>
                                                    <th><?= lang("class"); ?></th>
                                                    <td><u>{{printing_details.class_name}}</u></td>
                                                    <th><?= lang("section"); ?></th>
                                                    <td><u>{{printing_details.batch_name}}</u></td>
                                                </tr>
                                                <tr>
                                                    <th><?= lang("subject"); ?></th>
                                                    <td><u>{{printing_details.subject_name}}</u></td>
                                                    <th><?= lang("exam"); ?></th>
                                                    <td><u>{{printing_details.exam_name}}</u></td>
                                                </tr>
                                                <tr>
                                                    <th><?= lang("passing_marks"); ?></th>
                                                    <td><u>{{printing_details.passing_marks}}</u></td>
                                                    <th><?= lang("total_marks"); ?></th>
                                                    <td><u>{{printing_details.total_marks}}</u></td>
                                                    <!--<th><?= lang("lbl_exam_date"); ?></th>
                                                    <td><u>{{printing_details.exam_date}}</u></td>-->
                                                </tr>
                                                <!--<tr>
                                                    <th><?= lang("start_time"); ?></th>
                                                    <td><u>{{printing_details.start_time}}</u></td>
                                                    <th><?= lang("end_time"); ?></th>
                                                    <td><u>{{printing_details.end_time}}</u></td>
                                                </tr>-->
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-md-12 table-responsive">
                                <form name="marksheetForm" ng-submit="saveMarksheet(marksheetForm.$valid)" novalidate="" ng-class="{custom_disable:disable == 'TRUE'}">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="m-t-10  col-md-12">

                                                    <table id="myTable" class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Sr#.</th>
                                                                <th><?php echo lang('roll_no') ?></th>
                                                                <th><?php echo lang('image') ?></th>
                                                                <th><?php echo lang('student_name') ?></th>
                                                                <th ng-if="printing_details.type == 'number' "><?php echo lang('obtained_marks') ?></th>
                                                                <th ng-if="printing_details.type == 'grade' ">Grade</th>
                                                                <th><?php echo lang('remarks') ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr ng-repeat="(key, std) in students">
                                                                <td style="padding: 5px;">
                                                                    {{ $index+1 }}
                                                                </td>
                                                                <td style="padding: 5px;">{{ std.rollno }}</td>
                                                                <td style="padding: 5px;"><span class="round"><img src="<?php echo base_url(); ?>{{ std.student_avatar }}" width="30px" alt="student-profile"/></span></td>
                                                                <td style="padding: 5px;">{{ std.name }}</td>
                                                                <td style="padding:5px; width: 200px;">
                                                                    <div class="form-group" style="margin-bottom: 0;"  ng-if="printing_details.type == 'number' ">
                                                                        <input type="number" required="" min="0" max="{{printing_details.total_marks}}" ng-model="std.obtained_marks" id="id_{{key}}" ng-keypress="moveNext($event,'remarks_',key)" class="form-control"/>
                                                                    </div>
                                                                    <div class="form-group" style="margin-bottom: 0;" ng-if="printing_details.type == 'grade' ">
                                                                        <input type="text" required="" ng-model="std.grade" id="id_{{key}}" ng-keypress="moveNext($event,'remarks_',key)" class="form-control"/>
                                                                    </div>
                                                                </td>
                                                                <td style="padding:5px; width: 220px;">
                                                                    <input type="text" ng-model="remarks" ng-value="std.remarks" id="remarks_{{key}}" class="form-control" ng-keypress="moveNext($event,'id_',(key+1))"/>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <input type="submit" ng-hide="disable=='TRUE'" class="btn btn-primary pull-right no-print" value="<?php echo lang('save_marks'); ?>" />
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12" style="padding-left:0; padding-right:0;" id="marksStudentsTable" ng-show="students.length==0">
                <div class="alert alert-danger">
                    <div class="box-body">
                        <div class="col-md-12 text-danger"><?php echo lang("no_record_found"); ?></div>
                    </div>
                </div>
            </div>

        </div>


    </section>
</div>