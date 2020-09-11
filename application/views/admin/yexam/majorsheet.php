<style type="text/css">
    span.flag-icon.flag-icon-us{text-orientation: mixed;}
    .white-box {
        background: white;
        margin-left: 5px;
        margin-right: 5px;
        padding: 15px;
    }
    .hidden_row{
        border:0;
    }
    .cut-text { 
        text-overflow: ellipsis;
        overflow: hidden; 
        width: 115px; 
        height: 1.2em; 
        white-space: nowrap;
    }
    .setPointer:hover{
        cursor: pointer;
        font-size: 13px;
        font-weight: 700;
    }

    .overlay {
        height: 100%;
        width: 0;
        position: fixed;
        z-index: 1500;
        top: 0;
        left: 0;
        background-color: rgb(0,0,0);
        background-color: rgba(0,0,0, 0.9);
        overflow-x: hidden;
        transition: 0.5s;
    }

    .overlay-content {
        position: relative;
        top: 10%;
        width: 100%;
        text-align: center;
        margin-top: 0;
    }

    .overlay a {
        padding: 8px;
        text-decoration: none;
        font-size: 36px;
        color: #818181;
        display: block;
        transition: 0.3s;
    }

    .overlay a:hover, .overlay a:focus {
        color: #f1f1f1;
    }

    .overlay .closebtn {
        position: absolute;
        top: 20px;
        right: 45px;
        font-size: 60px;
    }

    .overlay .printAllBtn {
        position: absolute;
        top: 20px;
        left:45px;
    }
    .table-dark{
        background-color: black !important;
        color: white;
    }
    @media screen and (max-height: 450px) {
        .overlay a {font-size: 20px}
        .overlay .closebtn { font-size: 40px; top: 15px; right: 35px; }
    }
    .result_card_container{
        font-size: 10px;
        width: 70%;
        margin-left: 15%;
        background: white;
        padding: 25px;
        margin-bottom: 10px;
        border: 0px solid #464f61;
    }
    #footer-table{
        margin-top: 25px;
    }

    @media screen and (max-width: 768px) {
        .overlay .closebtn{top:0px;}
        .result_card_container{width: 80%; margin-left: 10%; padding: 25px; overflow: auto;}
    }
    @media screen and (max-width: 375px) {
        .overlay .closebtn{top:0px;}
        .result_card_container{width: 100%; margin-left: 0px; padding: 25px; overflow: auto;}
    }

    @media screen and (max-width: 414px) {
        .overlay .closebtn{top:0px;}
        .result_card_container{width: 100%; margin-left: 0px; padding: 25px; overflow: auto;}
    }
    @media screen and (max-width: 411px) {
        .overlay .closebtn{top:0px;}
        .result_card_container{width: 100%; margin-left: 0px; padding: 25px; overflow: auto;}
    }

    @media screen and (max-width: 320px) {
        .overlay .closebtn{top:-10px;}
        .overlay .printAllBtn {top:0px;}
        .result_card_container{width: 100%; margin-left: 0px; padding: 25px; overflow: auto;}
        #profile-img{margin-left:  5px;}
        #std-info-table{margin: -10px; margin-top: 10px}
        #footer-table {margin: -10px; margin-top: 25px;margin-bottom: 20px;}
        #other-info-table{margin: -8px;}
    }
    .align-middle{
        vertical-align: middle !important;
    }
</style>
<script>
    function customPrint2(id) {
        $("#" + id).print({
            globalStyles: false,
            mediaPrint: false,
            stylesheet: "<?php echo base_url(); ?>assets/css/custom-result-card.css?v=<?php echo date('h-i-s'); ?>",
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
<div class="content-wrapper" ng-controller="majorSheetController" ng-init="initAcademicYears();initClasses();initExams();" >
    
    <!-- Overlay DIV -->
    <div id="myNav" class="overlay">
        <a href="javascript:onclick=customPrint2('overlay-content')" class="printAllBtn no-print"><i class="fa fa-print"></i></a>
        <a href="javascript:void(0)" class="closebtn no-print" ng-click="closeNav()">&times;</a>
        <div class="overlay-content" id="overlay-content">
            <div ng-bind-html="graph_html" class="hide no-print"></div>
            <div ng-repeat="card in multi_result_cards" ng-bind-html="card"></div>
        </div>
    </div>
    <!-- ./Overlay DIV -->
    
    <!-- sample modal content for class teacher remarks add-->
    <div id="bs-teacher-remarks-modal-sm" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="mySmallModalLabel"><?php echo lang('add_teacher_remarks'); ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form name="teacherRemarksForm" ng-submit="saveTeacherRemarks(teacherRemarksForm.$valid)" novalidate="">
                        <div class="form-group">
                            <label><?php echo lang('remarks'); ?></label>
                            <textarea cols="3" class="form-control" rows="4" required="" ng-model="remarksModel.remark"></textarea>
                        </div>
                        <div>
                            <input type="reset" data-dismiss="modal" aria-hidden="true" value="<?php echo lang('cancel'); ?>" class="btn btn-default" />
                            <input type="submit" value="<?php echo lang('save'); ?>" class="btn btn-success" />
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!-- sample modal content for class teacher remarks update -->
    <div id="bs-update-teacher-remarks-modal-sm" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="mySmallModalLabel"><?php echo lang('update_teacher_remarks'); ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form name="teacherRemarksUpdateForm" ng-submit="updateTeacherRemarks(teacherRemarksUpdateForm.$valid)" novalidate="">
                        <div class="form-group">
                            <label><?php echo lang('remarks'); ?></label>
                            <textarea cols="3" class="form-control" rows="4" required="" ng-model="showModel.remark">{{showModel.remark}}</textarea>
                        </div>
                        <div>
                            <input type="reset" data-dismiss="modal" aria-hidden="true" value="<?php echo lang('cancel'); ?>" class="btn btn-default" />
                            <input type="submit" value="<?php echo lang('update'); ?>" class="btn btn-info" />
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!-- sample modal content for class administrator remarks add-->
    <div id="bs-administrator-remarks-modal-sm" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="mySmallModalLabel"><?php echo lang('add_administrator_remarks'); ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form name="AdministratorRemarksForm" ng-submit="saveAdministratorRemarks(AdministratorRemarksForm.$valid)" novalidate="">
                        <div class="form-group">
                            <label><?php echo lang('remarks'); ?></label>
                            <textarea cols="3" class="form-control" rows="4" required="" ng-model="remarksModel2.remark"></textarea>
                        </div>
                        <div>
                            <input type="reset" data-dismiss="modal" aria-hidden="true" value="<?php echo lang('cancel'); ?>" class="btn btn-default" />
                            <input type="submit" value="<?php echo lang('save'); ?>" class="btn btn-success" />
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!-- sample modal content for administrator remarks update-->
    <div id="bs-update-administrator-remarks-modal-sm" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="mySmallModalLabel"><?php echo lang('update_administrator_remarks'); ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form name="AdministratorRemarksUpdateForm" ng-submit="updateAdministratorRemarks(AdministratorRemarksUpdateForm.$valid)" novalidate="">
                        <div class="form-group">
                            <label><?php echo lang('remarks'); ?></label>
                            <textarea cols="3" class="form-control" rows="4" required="" ng-model="showModel2.remark">{{showModel2.remark}}</textarea>
                        </div>
                        <div>
                            <input type="reset" data-dismiss="modal" aria-hidden="true" value="<?php echo lang('cancel'); ?>" class="btn btn-default" />
                            <input type="submit" value="<?php echo lang('update'); ?>" class="btn btn-info" />
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->


    <!-- Resutl Edit Model-->
    <div id="editMarks" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content" id="majorsheetMarksEditModal">
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo lang('edit_marks'); ?> ({{editData.student_name}} - {{editData.rollno}})</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>

                <div class="modal-body">
                    <form class="form" name="editMarksForm">
                        <div ng-repeat="subj in editData.subjects">
                            <div ng-if="subj.is_subject && subj.exams[0].exam_detail_id != null">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>{{subj.name}}</label>
                                        <input type="number" ng-init="editData.subjects[$index].exams[0].new_marks = editData.subjects[$index].exams[0].obtained_marks" ng-model="editData.subjects[$index].exams[0].new_marks" ng-value="editData.subjects[$index].exams[0].obtained_marks" min="0" max="{{editData.subjects[$index].exams[0].total_marks}}" class="form-control" ng-if="subj.exams[0].type == 'number'">
                                        <input type="text" ng-init="editData.subjects[$index].exams[0].new_grade = editData.subjects[$index].exams[0].grade" ng-model="editData.subjects[$index].exams[0].new_grade" ng-value="editData.subjects[$index].exams[0].grade"class="form-control" ng-if="subj.exams[0].type == 'grade'">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label><?php echo lang('remarks'); ?></label>
                                        <input type="text" ng-init="editData.subjects[$index].exams[0].new_remarks = editData.subjects[$index].exams[0].remarks" ng-model="editData.subjects[$index].exams[0].new_remarks" ng-value="editData.subjects[$index].exams[0].remarks" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo lang('cancel') ?></button>
                    <button type="button" ng-click="editsaveMarksheet(editMarksForm.$valid)" class="btn btn-success waves-effect waves-light" ng-click=""><?php echo lang('save') ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- ./Resutl Edit Model-->

    <!-- Result Delete Modal -->
    <div id="deleteMarks" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{deleteModel.student_name}} - {{deleteModel.rollno}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <p><?php echo lang('delete_marks'); ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo lang('cancel') ?></button>
                    <button type="button" class="btn btn-danger waves-effect waves-light" ng-click="deleteMarks()"><?php echo lang('yes');?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- ./Result Delete Modal -->

    <section class="content-header">
        <h1><i class="fa fa-mortar-board"></i> <?php echo $this->lang->line('academics'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-search"></i> Select Criteria</h3>
            </div>
            <div class="box-body" id="majorSheetFilterForm">
                <form name="majorSheetFilterForm" ng-submit="onSubmit(majorSheetFilterForm.$valid)" novalidate="">
                    <div class="row">
                        <div class="col-sm-6 col-lg-3 col-md-3 col20" id="majorSheetFilterResultType">
                            <div class="form-group">
                                <label class="control-label"><?php echo lang('result_type') ?></label>
                                <select class="form-control" ng-model="filterModel.result_type" required="">
                                    <option value="single"><?php echo lang('assessments') ?></option>
                                    <option value="multiple"><?php echo lang('cumulative_result') ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3 col-md-3 col20" id="majorSheetFilterAcademicYears">
                            <div class="form-group">
                                <label class="control-label"><?php echo lang("session"); ?></label>
                                <select class="form-control" name="academic_year_id" ng-model="filterModel.academic_year_id" required="">
                                    <option value=""><?php echo lang("please_select_a_session"); ?></option>
                                    <option ng-repeat="ay in academicyears" value="{{ ay.id }}">{{ ay.session }}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-sm-6 col-lg-3 col-md-3 col20" id="majorSheetFilterClasses">
                            <div class="form-group">
                                <label class="control-label"><?php echo lang('classes') ?></label>
                                <select class="form-control" ng-model="filterModel.class_id" ng-change="initBatches(filterModel.class_id);" required="">
                                    <option value=""><?php echo lang('please_select_a_class') ?></option>
                                    <option ng-repeat="cls in classes" value="{{cls.id}}">{{cls.class}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3 col-md-3 col20" id="majorSheetFilterBatches">
                            <div class="form-group">
                                <label class="control-label"><?php echo lang('section') ?></label>
                                <select class="form-control" ng-model="filterModel.batch_id" required="" ng-change="initResultCardGroups(filterModel.academic_year_id, filterModel.class_id, filterModel.batch_id)">
                                    <option value=""><?php echo lang('please_select_a_section') ?></option>
                                    <option ng-repeat="bth in batches" value="{{bth.section_id}}">{{bth.section}}</option>
                                </select>
                            </div>
                        </div>

                        

                        <div class="col-sm-6 col-lg-3 col-md-3 col20" ng-if="filterModel.result_type =='single'" id="majorSheetFilterExams">
                            <div class="form-group">
                                <label class="control-label"><?php echo lang('exams') ?></label>
                                <select class="form-control" ng-model="filterModel.exam_id" required="">
                                    <option value=""><?php echo lang('please_select_an_exam') ?></option>
                                    <option ng-repeat="em in exams" value="{{em.id}}">{{em.title}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3 col-md-3 col20" ng-if="filterModel.result_type =='multiple'" id="majorSheetFilterResultCardGroups">
                            <div class="form-group">
                                <label class="control-label"><?php echo lang('result_card_groups') ?></label>
                                <select class="form-control" ng-model="filterModel.result_card_group_id" required="">
                                    <option value=""><?php echo lang('select_result_card_group') ?></option>
                                    <option ng-repeat="rcg in resultCardGroups" value="{{rcg.id}}">{{rcg.name}}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <p class="error" ng-show="message"><b>{{ message }}</b></p>
                        </div>
                        <div class="col-md-4">
                            <input type="submit" class="btn btn-primary pull-right" value="<?php echo lang('search') ?>" />
                        </div>
                    </div>
                </form>
            </div>

            <div class="white-box" id="majorSheetTable" ng-if="filterModel.result_type=='single'" ng-show="status=='success' && students[0].is_all_subjects_marks_added=='true'">
                <div class="row no-print">
                    <div class="col-md-12">
                        <div class="col-md-9 p-0">
                            <div class="form-group">
                                <button type="button" ng-click="showAllResultCards(student_ids,filterModel)" class="btn btn-primary"><i class="fa fa-tv"></i> <?php echo lang('menu_view_all'); ?></button>
                                <button type="button" ng-click="majorsheetPrint('marjorsheet_print_container','<?php echo base_url()."uploads/school_content/admin_small_logo/1.png"; ?>','<?php echo $this->session->userdata("admin")["school_name"]; ?>','<?php if($this->session->userdata("admin")["language"]["language"] != "english") { echo "direction:rtl;"; }?>')" class="btn btn-info"><i class="fa fa-print"></i> <?php echo lang("lbl_print_majorsheet"); ?></button>
                            </div>
                        </div>
                        <div class="col-md-3 p-0">
                            <input type="text" ng-model="searchedValue" placeholder="<?= lang("lbl_enter_some_text_to_search"); ?>" class="form-control" />
                        </div>
                    </div>
                </div>
                <table id="mytabletemp" style="display: none;">
                    <thead>
                        <tr>
                            <th class="text-center">Sr#</th>
                            <th><?php echo lang('student_name') ?></th>
                            <th><?php echo lang('imp_std_roll_no') ?></th>
                            <th class="text-center" ng-repeat="sub in students[0].subjects">{{sub.subject_name}}<small>({{sub.exams[0].total_marks}})</small></th>
                            <th><?php echo lang('total_marks'); ?></th>
                            <th><?php echo lang('percentage'); ?></th>
                            <th><?php echo lang('exam_position'); ?></th>
                            <th><?php echo lang('lbl_status'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="std in students | orderBy:'position'">
                            <td> {{$index+1}}</td>
                            <td>{{std.student_name}}</td>
                            <td>{{std.rollno}}</td>
                            <td ng-repeat="sub2 in std.subjects">
                                <span ng-show="sub2.exams[0].obtained_marks!== null && sub2.exams[0].type == 'number'">
                                    {{sub2.exams[0].obtained_marks}}
                                </span>
                                <span ng-show="sub2.exams[0].grade!== null && sub2.exams[0].type == 'grade'">
                                    {{sub2.exams[0].grade}}
                                </span>
                            </td>
                            <td>{{ std.obtained_total }}</td>
                            <td>{{ std.percentage.replace('%','') }}</td>
                            <td><span>{{std.new_position}}</span></td>
                            <td>
                                <span ng-if="std.result=='-'" class='text-info'>-</span>
                                <span ng-if="std.result=='<?php echo lang("fail"); ?>'" class='text-danger'><?php echo lang('fail'); ?></span>
                                <span ng-if="std.result=='<?php echo lang("pass"); ?>'" class='text-success'><?php echo lang('pass'); ?></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <form action="#">
                    <div class="row" style="overflow:auto;">
                        <div class="col-md-12" id="marjorsheet_print_container" style="">
                            <table id="mjshttable" class="table table-bordered table-striped table-hover text-center">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sr#.</th>
                                        <th class="text-center"><?php echo lang('roll_no') ?></th>
                                        <th class="text-center"><?php echo lang('image') ?></th>
                                        <th class="text-center"><?php echo lang('student_name') ?></th>
                                        <th class="text-center" ng-repeat="sub in students[0].subjects">{{sub.name}} <br/><small ng-if="sub.exams[0].type == 'number'">{{sub.exams[0].total_marks}}</small></th>
                                        <th class="text-center"><?php echo lang('total_marks'); ?></th>
                                        <th class="text-center"><?php echo lang('percentage'); ?>
                                        <th class="text-center"><?php echo lang('exam_position'); ?></th>
                                        <th class="text-center"><?php echo lang('status'); ?></th>
                                        <th class="text-center" ng-if="students[0].subjects[0].exams[0].total_marks==60">Teacher Comment's</th>
                                        <th class="text-center no-print" style="min-width:150px"><?php echo lang('lbl_action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-show="(students | filter:searchedValue).length == 0" >
                                        <td class="text-left no-print" colspan="{{students[0].subjects.length + 10}}"><?= lang("no_record"); ?></td>
                                    </tr>
                                    <tr ng-repeat="std in students | filter:searchedValue | orderBy:'position'">
                                        <td> {{$index+1}}</td>
                                        <td> {{std.rollno}}</td>
                                        <td>
                                            <span class="round">
                                            <object data="<?php echo base_url(); ?>{{ std.student_avatar }}" width="30px" style="border-radius: 50px;" type="image/png">
                                                <img src="<?php echo base_url(); ?>uploads/user/profile.png" alt="user-image" width="30px" style="border-radius: 50px;"/>
                                              </object>
                                            </span>
                                        </td>
                                        <td>
                                            {{std.student_name}} 
                                            <a ng-show="std.is_shifted" href="javascript:void(0)" data-html="true" data-toggle="tooltip" data-placement="top" title="Student has been shifted to another section."><i class="fa fa-info-circle"></i></a>
                                        </td>
                                        <td ng-repeat="sub2 in std.subjects">
                                            <span ng-show="sub2.exams[0].obtained_marks!== null && sub2.exams[0].type == 'number'">
                                                {{sub2.exams[0].obtained_marks}}
                                                <small ng-if="sub2.exams[0].marksheet_status == 'Fail'" class="text-danger">F</small>
                                                <small ng-if="sub2.exams[0].marksheet_status == 'Pass'" class="text-success">P</small>
                                            </span>
                                            <span ng-show="sub2.exams[0].grade!== null && sub2.exams[0].type == 'grade'">
                                                {{sub2.exams[0].grade}}
                                            </span>
                                            <span ng-show="sub2.exams[0].obtained_marks=== null" class="text-danger no-print">-</span>
                                        </td>
                                        <td>{{ std.obtained_total }}</td>
                                        <td>{{ std.percentage }}</td>
                                        <td><span>{{std.new_position}}</span></td>
                                        <td>
                                            <span ng-if="std.result=='-'" class='text-info'>-</span>
                                            <span ng-if="std.result=='<?php echo lang("fail"); ?>'" class='text-danger'><?php echo lang('fail'); ?></span>
                                            <span ng-if="std.result=='<?php echo lang("pass"); ?>'" class='text-success'><?php echo lang('pass'); ?></span>
                                        </td>
                                        <td ng-if="std.subjects[0].exams[0].total_marks == 60">
                                            <p style="white-space: nowrap; overflow: hidden;text-overflow: ellipsis; width: 100px;" class="setPointer">
                                                <small ng-if="std.teacher_remark_id!=null" data-toggle="modal" data-target="#bs-update-teacher-remarks-modal-sm" ng-click="showTeacherRemark(std.teacher_remark,std.teacher_remark_id)">{{std.teacher_remark}}</small>
                                            </p>

                                            <a href="javascript:void();" ng-if="std.teacher_remark_id==null" ng-click="setSelectedStudentForRemarks(std.student_id, std.subjects[0].exams[0].exam_id)" data-toggle="modal" data-target="#bs-teacher-remarks-modal-sm" class="btn btn-primary btn-circle btn-sm no-print"><i class="fa fa-plus"></i></a>
                                        </td>
                                        <td class="no-print">
                                            <a target="_blank" href="<?php echo base_url() ?>admin/Examination/single_result_card_view?session_id={{filterModel.academic_year_id}}&id={{std.student_id}}&exam_id={{std.subjects[0].exams[0].exam_id}}&requested_page=result_card&class_id={{std.class_id}}&batch_id={{filterModel.batch_id}}" ng-class="{'custom_disable':is_all_subject_marks_added(std.subjects)}" class="btn btn-success btn-circle btn-sm"><i class="fa fa-eye"></i></a>
                                            <a href="javascript:void(0);" data-toggle="modal" data-target="#editMarks" ng-click="loadEditData(std)" class="btn btn-info btn-sm btn-circle"><i class="fa fa-pencil"></i></a>
                                            <a href="javascript:void(0);" data-toggle="modal" data-target="#deleteMarks" ng-click="loadDeleteData(std)" class="btn btn-danger btn-sm btn-circle"><i class="fa fa-trash-o"></i></a>
                                        </td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>

            <div class="white-box" ng-if="filterModel.result_type=='multiple'" ng-show="c_result.length>0">
                <div class="row no-print">
                    <div class="col-md-12">
                        <div class="col-md-9 p-0">
                            <div class="form-group">
                                <button type="button" ng-click="showAllResultCards2(c_result)" class="btn btn-primary"><i class="fa fa-tv"></i> <?php echo lang('menu_view_all'); ?></button>
                                <!--<button type="button" ng-click="majorsheetPrint('marjorsheet_print_container','<?php //echo $this->session->userdata("userdata")["sh_logo"]; ?>','<?php //echo $this->session->userdata("userdata")["sh_name"]; ?>','<?php //if($this->session->userdata("site_lang") != "english") { echo "direction:rtl;"; }?>')" class="btn btn-info"><i class="fa fa-print"></i> <?php //echo lang("lbl_print_majorsheet"); ?></button>-->
                            </div>
                        </div>
                        <div class="col-md-3 p-0">
                            <input type="text" ng-model="searchedValue2" placeholder="<?= lang("lbl_enter_some_text_to_search"); ?>" class="form-control" />
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover text-center">
                        <tr>
                            <th class="text-center align-middle">Sr#.</th>
                            <th class="text-center align-middle">Student Name</th>
                            <th class="text-center align-middle">Profile</th>
                            <th class="text-center align-middle">Roll No</th>
                            <th ng-repeat="sub in c_subjects" class="text-center align-middle">{{sub.name}}</th>
                            <th class="text-center align-middle">Total Marks ({{c_total_marks}})</th>
                            <th class="text-center align-middle">Percentage</th>
                            <th class="text-center align-middle">Administrator Remarks</th>
                            <th style="width: 80px;" class="text-center align-middle">Action</th>
                        </tr>
                        <tr ng-repeat="res in c_result | filter:searchedValue2" class="text-center">
                            <td>{{$index+1}}</td>
                            <td>{{res.student_name}}</td>
                            <td><img src="<?php echo base_url(); ?>{{res.student_avatar}}" width="30px" style="border-radius: 50px;"/></td>
                            <td>{{res.rollno}}</td>
                            <td ng-repeat="(key,sub1) in res.subjects">
                                <table width="100%" border="1px" style="border: 1px solid black; border-collapse: collapse;">
                                    <tr style="background:black; color:white;">
                                        <th style="padding: 2px; width: {{e0.exam_name.length * 10}}px;" ng-repeat="e0 in c_exams" class="text-center">{{e0.exam_name}}</th>
                                        <th style="padding: 2px;" class="text-center">Total Score (100)</th>
                                        <th style="padding: 2px;" class="text-center">Class Avg Score</th>
                                        <th style="padding: 2px;" class="text-center">Class Low Score</th>
                                        <th style="padding: 2px;" class="text-center">Class High Score</th>
                                        <th style="padding: 2px;" class="text-center">Position</th>
                                        <th style="padding: 2px;" class="text-center">Term Subject Grade</th>
                                        <th style="padding: 2px;" class="text-center" ng-repeat="e12 in sub1.prev_term_data">{{e12.term_name}}</th>
                                        <th style="padding: 2px;" class="text-center">Cumulative Score</th>
                                        <th style="padding: 2px;" class="text-center">Subject Cumulative Position</th>
                                        <th style="padding: 2px;" class="text-center">Cum Grade</th>
                                        <!--<th style="padding: 2px;" class="text-center">Result</th>-->
                                    </tr>
                                    <tr>
                                        <td ng-repeat="e1 in sub1.exams" class="text-center" style="padding: 2px; width: {{e1.exam_name.length * 10}}px;">
                                            <span ng-if="e1.obtained_marks != undefined">{{e1.obtained_marks}}</span>
                                        </td>
                                        <td class="text-center" style="padding: 2px;">{{sub1.obtained_total}}</td>
                                        <td class="text-center" style="padding: 2px;">{{sub1.subject_avg_score}}</td>
                                        <td class="text-center" style="padding: 2px;">{{sub1.low_score}}</td>
                                        <td class="text-center" style="padding: 2px;">{{sub1.high_score}}</td>
                                        <td class="text-center" style="padding: 2px;">{{sub1.position}}</td>
                                        <td class="text-center" style="padding: 2px;"><span style="color:{{sub1.grade_color}}">{{sub1.grade}}</span></td>
                                        <td class="text-center" style="padding: 2px;" ng-repeat="e122 in sub1.prev_term_data">{{e122.obtained_total}}</td>
                                        <td class="text-center" style="padding: 2px;">{{sub1.cum_score}}</td>
                                        <td class="text-center" style="padding: 2px;">{{sub1.position}}</td>
                                        <td class="text-center" style="padding: 2px;"><span style="color:{{sub1.cum_grade_color}}">{{sub1.cum_grade}}</span></td>
                                        <!--<td class="text-center" style="padding: 2px;">
                                            <small ng-class="{'text-success': sub1.cum_result == 'Pass', 'text-danger': sub1.cum_result == 'Fail'}">{{sub1.cum_result}}</small>
                                        </td>-->
                                    </tr>
                                </table>
                            </td>
                            <td>
                                {{res.exam_obtained_total}}
                            </td>
                            <td>{{res.exam_obtained_percentage}}%</td>
                            <td>
                                <p style="white-space: nowrap; overflow: hidden;text-overflow: ellipsis; width: 100px;" class="setPointer">
                                    <small ng-if="res.administrator_remark_id!=null" data-toggle="modal" data-target="#bs-update-administrator-remarks-modal-sm" ng-click="showAdministratorRemark(res.administrator_remark,res.administrator_remark_id)">{{res.administrator_remark}}</small>
                                </p>

                                <a href="javascript:void();" ng-if="res.administrator_remark_id==null" ng-click="setSelectedStudentForRemarks2(res.student_id, filterModel.result_card_group_id)" data-toggle="modal" data-target="#bs-administrator-remarks-modal-sm" class="btn btn-primary btn-circle btn-sm no-print"><i class="fa fa-plus"></i></a>
                            </td>
                            <!--<td>
                                <span ng-class="{'text-danger': res.result==='Fail', 'text-success': res.result==='Pass'}">{{res.result}}</span>
                            </td>
                            <td>{{class_low_score}}</td>
                            <td>{{class_high_score}}</td>-->
                            <td class="no-print">
                                <a href="javascript:void(0);" class="btn btn-sm btn-success btn-circle" ng-click="showCompletedResultCardSingle(res)" ng-class="{'custom_disable':is_all_subject_marks_added(std.subjects)}"><i class="fa fa-eye"></i></a>
                                <!--<a href="javascript:void(0)" style="display: inline-block;" data-toggle="modal" data-target="#deleteMarks" ng-click="loadDeleteData(std)"><i class="fa fa-trash-o text-danger"></i></a>-->
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <!--<script>
            window.onload = function () {
                var options = {
                    animationEnabled: true,
                    title: {
                        text: "GDP Growth Rate - 2016"
                    },
                    axisY: {
                        title: "Growth Rate (in %)",
                        suffix: "%",
                        includeZero: false
                    },
                    axisX: {
                        title: "Countries"
                    },
                    data: [{
                        type: "column",
                        yValueFormatString: "#,##0.0#"%"",
                        dataPoints: [
                            { label: "Iraq", y: 10.09 },    
                            { label: "Turks & Caicos Islands", y: 9.40 },   
                            { label: "Nauru", y: 8.50 },
                            { label: "Ethiopia", y: 7.96 }, 
                            { label: "Uzbekistan", y: 7.80 },
                            { label: "Nepal", y: 7.56 },
                            { label: "Iceland", y: 7.20 },
                            { label: "India", y: 7.1 }
                            
                        ]
                    }]
                };
                $("#chartContainer").CanvasJSChart(options);
            }
        </script>
        <div id="chartContainer" style="height: 300px; width: 100%;"></div>-->
        <div class="alert alert-danger" ng-show="status=='success' && students[0].is_all_subjects_marks_added=='false'">
            <div class="box box-body">
                <div class="col-md-12 text-danger"><?= "lbl_all_subjects_marks_not_added"; ?></div>
            </div>
        </div>
        <div class="alert alert-danger" ng-show="status=='error'">
            <div class="box-body">
                <div class="col-md-12 text-danger"><?php echo lang("no_record_found"); ?></div>
            </div>
        </div>

    </section>
</div>
   