var base_url = "http://"+window.location.host+"/smart-school-5.0/";
var app2 = angular.module('myApp', ['jcs-autoValidate']);
var config = {
    headers: {
        'Content-Type': 'application/json;charset=utf-8;'
    }
};

function showNotification(type, message) {
    $("#toast-type").addClass(type);
    $("#toast-body").html(message);
    $("#toast-container").css({"display":"block"});
    setTimeout(function (){
        $("#toast-container").css({"display":"none"});
    }, 3000);
    
    
}

function Loading(selector, text, image, action) {
    $.LoadingOverlaySetup({
        color: "rgba(255, 255, 255, 0.7)",
        maxSize: "26px",
        minSize: "10px",
        resizeInterval: 0
    });
    var customElement = $("<div>", {
        id: "customElement",
        css: {
            "font-size": "12px"
        },
        text: text
    });

    $(selector).LoadingOverlay(action, {
        image: image,
        fontawesome: "fa fa-circle-o-notch fa-spin",
        custom: customElement
    });
}

app2.controller("subjectController", function ($scope, $http, $sce) {
    $scope.classes = {};
    $scope.batches = {};
    $scope.sessions = {};
    $scope.selecedVal = 'all';
    $scope.selecedVal2 = 'all';
    $scope.myDiv = false;
    
    $scope.initSessions =  function(){
        //Loading("#subGroupSessions", 'Loading...', "", "show");
        $http.post(base_url + 'admin/Examination/getSessions', "", config).then(
            function (success) {
                //Loading("#subGroupSessions", 'Loading...', "", "hide");
                $scope.sessions = success.data;
            },
            function (error) {
                //Loading("#subGroupSessions", 'Loading...', "", "hide");
                console.log(error.data);
            }
        );
    };

    $scope.fetchClasses = function (id) {
        $http.post(base_url + 'admin/Examination/getSchoolClasses', "", config).then(
            function (success) {
                $scope.classes = success.data;
                if (id !== 'all') {
                    $scope.loadClassBatches(id);
                }
            },
            function (error) {
                console.log(error.data);
            }
        );
    };

    $scope.loadClassBatches = function (id) {
        if (id !== 'all') {
            //Loading("#yasir_batches", 'Loading...', "", "show");
            $http.post(base_url + 'admin/Examination/getClassBatches', {id: id}, config).then(
                function (success) {
                    //Loading("#yasir_batches", 'Loading...', "", "hide");
                    $scope.batches = success.data;
                },
                function (error) {
                    //Loading("#yasir_batches", 'Loading...', "", "hide");
                    console.log(error.data);
                }
            );
        } else {
            $scope.batches = {};
            $scope.selecedVal2 = 'all';
            $scope.loadSubjects();
        }
    };

    $scope.loadSubjects = function () {
        $http.post(base_url + 'settings/getSubjects', {class_id: $scope.selecedVal, batch_id: $scope.selecedVal2}, config).then(
            function (success) {
                $scope.myDiv = $sce.trustAsHtml(success.data);
            },
            function (error) {
                console.log(error.data);
            }
        );
    };
});

app2.controller("resultCardGroupController", function ($scope, $http, $sce) {
    $scope.classes = {};
    $scope.batches = {};
    $scope.sessions = {};
    $scope.selecedVal = 'all';
    $scope.selecedVal2 = 'all';
    $scope.myDiv = false;
    
    $scope.initSessions =  function(){
        //Loading("#subGroupSessions", 'Loading...', "", "show");
        $http.post(base_url + 'admin/Examination/getSessions', "", config).then(
            function (success) {
                //Loading("#subGroupSessions", 'Loading...', "", "hide");
                $scope.sessions = success.data;
            },
            function (error) {
                //Loading("#subGroupSessions", 'Loading...', "", "hide");
                console.log(error.data);
            }
        );
    };

    $scope.fetchClasses = function (id) {
        $http.post(base_url + 'admin/Examination/getSchoolClasses', "", config).then(
            function (success) {
                $scope.classes = success.data;
                if (id !== 'all') {
                    $scope.loadClassBatches(id);
                }
            },
            function (error) {
                console.log(error.data);
            }
        );
    };

    $scope.loadClassBatches = function (id) {
        if (id !== 'all') {
            //Loading("#yasir_batches", 'Loading...', "", "show");
            $http.post(base_url + 'admin/Examination/getClassBatches', {id: id}, config).then(
                function (success) {
                    //Loading("#yasir_batches", 'Loading...', "", "hide");
                    $scope.batches = success.data;
                },
                function (error) {
                    //Loading("#yasir_batches", 'Loading...', "", "hide");
                    console.log(error.data);
                }
            );
        } else {
            $scope.batches = {};
            $scope.selecedVal2 = 'all';
            $scope.loadSubjects();
        }
    };

    $scope.loadSubjects = function () {
        $http.post(base_url + 'settings/getSubjects', {class_id: $scope.selecedVal, batch_id: $scope.selecedVal2}, config).then(
            function (success) {
                $scope.myDiv = $sce.trustAsHtml(success.data);
            },
            function (error) {
                console.log(error.data);
            }
        );
    };
});

app2.controller("templateController", function ($scope, $http, $sce) {
    $scope.getTemplate = function(id){
        $http.post(base_url + 'admin/Examination/showResultCard', {id: id}, config).then(
            function (success) {
                //console.log(success);
                $scope.myDiv = $sce.trustAsHtml(success.data.template.html);
            },
            function (error) {
                console.log(error.data);
            }
        );
    };
});

app2.controller("marksheetController", function ($scope, $http, $window, $location) {
    $scope.classes = {};
    $scope.batches = {};
    $scope.subjects = {};
    $scope.exams = {};
    $scope.students = [];
    $scope.filterModel = {};
    $scope.enteredMarks = {};
    $scope.message;
    $scope.printing_details = {};
    $scope.passing_marks = 0;
    $scope.students_group_wise = [];
    $scope.academicyears = {};
    
    
    $scope.initSessions =  function(){
        Loading("#marksFilterAcademicYears", 'Loading...', "", "show");
        $http.post(base_url + 'admin/Examination/getSessions', "", config).then(
            function (success) {
                Loading("#marksFilterAcademicYears", 'Loading...', "", "hide");
                $scope.academicyears = success.data;
            },
            function (error) {
                Loading("#marksFilterAcademicYears", 'Loading...', "", "hide");
                console.log(error.data);
            }
        );
    };
    
    $scope.initClasses = function () {
        Loading("#marksFilterClasses", 'Loading...', "", "show");
        $http.post(base_url + 'admin/Examination/getSchoolClasses', "", config).then(
            function (success) {
                Loading("#marksFilterClasses", 'Loading...', "", "hide");
                $scope.classes = success.data;
            },
            function (error) {
                Loading("#marksFilterClasses", 'Loading...', "", "hide");
                console.log(error.data);
            }
        );
    };
    
    
    $scope.initBatches = function (class_id) {
        if (class_id) {
            Loading("#marksFilterBatches", 'Loading...', "", "show");
            $http.post(base_url + 'admin/Examination/getClassBatches', {'id': class_id}, config).then(
                function (success) {
                    Loading("#marksFilterBatches", 'Loading...', "", "hide");
                    $scope.batches = success.data;
                    $scope.filterModel.batch_id = "";
                },
                function (error) {
                    Loading("#marksFilterBatches", 'Loading...', "", "hide");
                    console.log(error.data);
                }
            );
        }
    };
    
    $scope.initSubjects = function (session_id, class_id, batch_id) {
        Loading("#marksilterSubjects", 'Loading...', "", "show");
        $http.post(base_url + 'admin/Examination/getSubjects4rMarksheet', {'session_id': session_id, 'class_id':class_id, 'batch_id': batch_id}, config).then(
            function (success) {
                Loading("#marksFilterSubjects", 'Loading...', "", "hide");
                $scope.subjects = success.data.data;
                $scope.filterModel.subject_id = "";
            },
            function (error) {
                Loading("#marksFilterSubjects", 'Loading...', "", "hide");
                console.log(error.data);
            }
        );
    };
    
    $scope.initExams = function () {
        Loading("#marksFilterExams", 'Loading...', "", "show");
        $http.post(base_url + 'admin/Examination/getExams', $scope.filterModel, config).then(
            function (success) {
                Loading("#marksFilterExams", 'Loading...', "", "hide");
                $scope.exams = success.data;
                $scope.filterModel.exam_id = "";
            },
            function (error) {
                console.log(error.data);
            }
        );
    };
    
    $scope.saveExamId = function (exam_detail_id){
        angular.forEach($scope.exams, function (value, key) {
            if(value.id == exam_detail_id){
                $scope.filterModel.exam_id = value.exam_id;
            }
        });
    };
    
    $scope.inProcessMarkSheet = function(){
    Loading("body", 'Loading...', "", "show");
    $scope.filterModel.reason = $scope.requestText;
        if($scope.requestText != null){
            $http.post(base_url + 'examination/inProcessMarkSheet', $scope.filterModel, config).then(
                function (success) {
                    $('#requestModelMarksheet').modal('hide');
                    if(success.data.status === 'success'){
                        showNotification('<?php echo lang("success_app") ?>', success.data.message, "success");
                        $scope.disable = success.data.disable;
                        $scope.action = success.data.edit;
                        $scope.r_id = success.data.r_id;
                        var otherData = {class_id:$scope.filterModel.class_id, batch_id:$scope.filterModel.batch_id, subject_id:$scope.filterModel.subject_id};
                        $scope.getSchoolAdmins(otherData);
                        $scope.requestText ="";
                     }
                Loading("body", 'Loading...', "", "hide");

                },
                function (error) {
                    Loading("body", 'Loading...', "", "hide");
                    $window.location.href = 'errors/' + error.status;
                    //console.log(error);
                }
            );
        }else{
             $('#request_error').show();
        }
     };

   $scope.getSchoolAdmins = function(otherData){
      $http.post(base_url + 'syllabus/getSchoolAdmins', {}, config).then(
          function(success){
              publicNotificationViaPusher("lbl_approval_exam_marksheet", otherData,  success.data.new_ids, "applications/all", {'sender': success.data.sender}, $scope.r_id);
          },
          function(error){
               $window.location.href = 'errors/' + error.status;
          }
      );
  };

    $scope.onSubmit = function (valid) {
        if (valid) {
            Loading("#marksStudentsTable", 'Loading...', "", "show");
            $scope.savePrintingDetails($scope.filterModel);
            $http.post(base_url + 'admin/Examination/fetchStudents', $scope.filterModel, config).then(
                function (success) {
                    Loading("#marksStudentsTable", 'Loading...', "", "hide");
                    $scope.students = {};
                    $scope.students = success.data.students;
                    $scope.message = success.data.message;
                    $scope.passing_marks = success.data.passing_marks;
                    $scope.printing_details.class_name = success.data.class_name;
                    $scope.printing_details.batch_name = success.data.batch_name;
                    $scope.action = success.data.edit;
                    $scope.disable = success.data.disable;
                    $scope.exist = success.data.exist;
                },
                function (error) {
                    Loading("#marksStudentsTable", 'Loading...', "", "hide");
                    console.log(error);
                }
            );
        }
    };
    
    $scope.savePrintingDetails = function(obj){
        angular.forEach($scope.subjects, function (value, key) {
            if(value.id == $scope.filterModel.subject_id){
                $scope.printing_details.subject_name = value.name;
            }
        });
        angular.forEach($scope.exams, function (value, key) {
            if(value.id == $scope.filterModel.exam_detail_id){
                $scope.printing_details.exam_name = value.title;
                $scope.printing_details.total_marks = value.total_marks;
                $scope.printing_details.passing_marks = value.passing_marks;
                $scope.printing_details.exam_date = value.exam_date;
                $scope.printing_details.start_time = value.start_time;
                $scope.printing_details.end_time = value.end_time;
                $scope.printing_details.type = value.type;
            }
        });
        //$scope.getTeacherAndClassTeacher($scope.filterModel);
    };
    
    // not-used
    $scope.getTeacherAndClassTeacher = function(obj){
        $http.post(base_url + 'examination/get_teacher_of_class', obj, config).then(
            function (success) {
                $scope.printing_details.teacher_name = success.data.teacher_name;
            },
            function (error) {
                console.log(error.data);
            }
        );
    };
    
    $scope.saveMarksheet = function () {
        $scope.data = [];
        angular.forEach($scope.students, function (value, key) {
            remarks = $("#remarks_"+key).val();
            $scope.data.push({
                id: value.id, 
                class_id: value.class_id, 
                batch_id: value.section_id, 
                obtain_marks:value.obtained_marks,
                grade: value.grade,
                subject_id:$scope.filterModel.subject_id,
                exam_detail_id:$scope.filterModel.exam_detail_id,
                exam_id:$scope.filterModel.exam_id,
                passing_marks: $scope.passing_marks,
                remarks: remarks
            });
        });
        Loading("#marksStudentsTable", 'Loading...', "", "show");
        $http.post(base_url + 'admin/Examination/saveMarks', $scope.data, config).then(
                function (success) {
                    Loading("#marksStudentsTable", 'Loading...', "", "hide");
                    showNotification("toast-"+success.data.status,success.data.message);
                    $scope.onSubmit(true);
                },
                function (error) {
                    console.log(error.data);
                    Loading("#marksStudentsTable", 'Loading...', "", "hide");
                    //$window.location.href = 'errors/' + error.status;
                }
        );
    };
    
    $scope.moveNext = function(event, next_id, next_key){
        if (event.which === 13) {
            event.preventDefault();
            $("#"+next_id+next_key).focus();
        }
    };
});

app2.controller("majorSheetController", function ($scope, $http, $window, $location, $sce) {
    $scope.classes = {};
    $scope.batches = {};
    $scope.subjects = {};
    $scope.exams = {};
    $scope.students = [];
    $scope.overalltotal = 0;
    $scope.remarksModel = {};
    $scope.remarksModel2 = {};
    $scope.showModel = {};
    $scope.showModel2 = {};
    $scope.positions = [];
    $scope.status = "";
    $scope.message_from_server = "";
    $scope.selected_student_for_teacher_remarks = {};
    $scope.selected_student_for_administrator_remarks = {};
    $scope.total_obtained_marks_array = [];
    $scope.student_ids = [];
    $scope.exam_ids = [];
    $scope.class_ids = [];
    $scope.batch_ids = [];
    $scope.multi_result_cards = [];
    $scope.selectedClassName = "";
    $scope.selectedBatchName = "";
    $scope.selectedExamName = "";
    $scope.students_shift_wise = [];
    $scope.academicyears = {};
    $scope.filterModel = {'result_type':'single'};
    $scope.resultCardGroups = [];
    $scope.editData = {};
    $scope.c_result = [];
    $scope.c_colspan = 8;
    $scope.avg_class_score=0;
    $scope.class_low_score=0;
    $scope.class_high_score=0;
    $scope.final_term = "";

    $scope.showAllResultCards = function(ids, obj){
        $scope.multi_result_cards = [];
        Loading("body", 'Loading...', "", "show");
        $("#myNav").css({"width":"100%","display":"block"});
        $http.post(base_url + 'admin/Examination/print_all_result_cards', {"ids":ids,"obj":obj}, config).then(
            function (success) {
                Loading("body", 'Loading...', "", "hide");
                angular.forEach(success.data, function (value) {
                    $scope.multi_result_cards.push($sce.trustAsHtml(value));
                });
            },
            function (error) {
                Loading("body", 'Loading...', "", "hide");
                console.log(error);
            }
        );
    };

    $scope.showAllResultCards2 = function(objs){
        $("#myNav").css({"width":"100%","display":"block"});
        $scope.multi_result_cards = [];
        
        angular.forEach(objs, function (value) {
            value.final_term = $scope.final_term;
            $http.post(base_url + 'admin/Examination/renderCompletedResultCardSingle', value, config).then(
                function (success) {
                    $scope.multi_result_cards.push($sce.trustAsHtml(success.data.template.html));
                },
                function (error) {
                    console.log(error.data);
                }
            );
        });
    };

    $scope.loadEditData = function(std){
        $scope.editMarksForm.$setUntouched();
        $scope.editMarksForm.$setPristine();
        angular.forEach(std.subjects, function (value) {
            if(value.exams[0].obtained_marks == null){
                value.exams[0].new_marks = '';
            }else {
                value.exams[0].obtained_marks = parseInt(value.exams[0].obtained_marks);
                value.exams[0].new_marks = value.exams[0].obtained_marks;
            }
            if(value.exams[0].remarks == null){
                    value.exams[0].new_remarks = '';
            }else {
                value.exams[0].remarks = value.exams[0].remarks;
                value.exams[0].new_remarks = value.exams[0].remarks;
            }
        });

        if(std.grouped_subjects != null){
            //var total_subjects = std.grouped_subjects.split(",");
            angular.forEach(std.subjects, function (value) {
                if(std.grouped_subjects.indexOf(value.id) != -1){
                    value.is_subject = true;
                }else {
                    value.is_subject = false;
                }
            });
        } else {
        angular.forEach(std.subjects, function (value) {
                value.is_subject = true;      
            });
        }

        $scope.editData = std;
    }
    
    $scope.editsaveMarksheet = function(valid){
        if(valid){
            Loading("#majorsheetMarksEditModal", 'Loading...', "", "show");
            $http.post(base_url + 'admin/Examination/update_marks', $scope.editData, config).then(
                function (success) {
                    $('#editMarks').modal('toggle');
                    $scope.onSubmit(true);
                    Loading("#majorsheetMarksEditModal", 'Loading...', "", "hide");
                },
                function (error) {
                    Loading("#majorsheetMarksEditModal", 'Loading...', "", "hide");
                }
        );
    }
        
    };

    $scope.loadDeleteData = function (std){
        $scope.deleteModel = std;
        $scope.deleteModel.exam_id = $scope.deleteExamId;
    }

    $scope.deleteMarks = function(){
        Loading("body", 'Loading...', "", "show");
        $http.post(base_url + 'admin/Examination/delete_marks', $scope.deleteModel, config).then(
            function (success) {
                $('#deleteMarks').modal('toggle');
                $scope.onSubmit(true);
                Loading("body", 'Loading...', "", "hide");
            },
            function (error) {
                Loading("body", 'Loading...', "", "hide");
            }
        );
    }

    $scope.majorsheetPrint = function(id, logo, name, dirr){
        var d = '<div style='+ dirr +'>'+
            '<p style="text-align:center;"><img src='+logo+' width="120px"/></p>'+
            '<h3 style="text-align:center;"><b>'+name+'</b></h3>'+
            '<table style="width:100%; border-spacing: 5px; margin-bottom: 10px; border-collapse: separate;">'+
            '<tr><td><strong><?php echo lang("class_name"); ?></strong></td><td><u>'+$scope.selectedClassName+'</u></td>'+
            '<td><strong><?php echo lang("lbl_batch"); ?></strong></td><td><u>'+$scope.selectedBatchName+'</u></td>'+
            '</tr><tr><td><strong><?php echo lang("lbl_exam_session"); ?></strong></td><td><u>'+$scope.selectedExamName+'</u></td></tr>'+
            '</table>'+
        '</div>';
    
    
        $("#" + id).print({
            globalStyles: true,
            mediaPrint: false,
            stylesheet: "<?php echo base_url(); ?>assets/css/custom-majorsheet.css",
            noPrintSelector: ".no-print",
            iframe: true,
            append: null,
            prepend: d,
            manuallyCopyFormValues: true,
            deferred: $.Deferred(),
            timeout: 750,
            title: null,
            doctype: '<!doctype html>'
        });
    };
    
    $scope.is_all_subject_marks_added = function(subjects){
        var result = true;
        angular.forEach(subjects, function (value) {
            if(value.exams[0].obtained_marks != null) {
                result = false;
            }
        });
        return result;
    };
    
    $scope.closeNav = function (){
        $("#myNav").css({"width":"0","display":"none"});
    };
    
    $scope.initAcademicYears =  function(){
        Loading("#majorSheetFilterAcademicYears", 'Loading...', "", "show");
        $http.post(base_url + 'admin/Examination/getSessions', "", config).then(
            function (success) {
                Loading("#majorSheetFilterAcademicYears", 'Loading...', "", "hide");
                //$scope.filterModel.academic_year_id = success.data.current_academic_year_id;
                $scope.initClasses();
                $scope.initExams();
                $scope.academicyears = success.data;
                $scope.filterModel.class_id = "";
                $scope.filterModel.batch_id = "";
            },
            function (error) {
                Loading("#majorSheetFilterAcademicYears", 'Loading...', "", "hide");
                console.log(error.data);
            }
        );
    };
    
    $scope.initClasses = function () {
        Loading("#majorSheetFilterClasses", 'Loading...', "", "show");
        $http.post(base_url + 'admin/Examination/getSchoolClasses', "", config).then(
            function (success) {
                Loading("#majorSheetFilterClasses", 'Loading...', "", "hide");
                $scope.classes = success.data;
                //sconsole.log(success.data);
            },
            function (error) {
                Loading("#majorSheetFilterClasses", 'Loading...', "", "hide");
                console.log(error.data);
            }
        );
    };
    
    
    $scope.initBatches = function (class_id) {
        Loading("#majorSheetFilterBatches", 'Loading...', "", "show");
        $http.post(base_url + 'admin/Examination/getClassBatches', {'id': class_id}, config).then(
            function (success) {
                Loading("#majorSheetFilterBatches", 'Loading...', "", "hide");
                $scope.batches = success.data;
                $scope.filterModel.batch_id = "";
            },
            function (error) {
                console.log(error.data);
                Loading("#majorSheetFilterBatches", 'Loading...', "", "hide");
            }
        );
    };

    $scope.initResultCardGroups = function (session_id, class_id, batch_id) {
        Loading("#majorSheetFilterResultCardGroups", 'Loading...', "", "show");
        $http.post(base_url + 'admin/Examination/getResultCardGroups', {'session_id':session_id,'class_id':class_id,'batch_id': batch_id}, config).then(
            function (success) {
                Loading("#majorSheetFilterResultCardGroups", 'Loading...', "", "hide");
                $scope.resultCardGroups = success.data;
                $scope.filterModel.result_card_group_id = "";
            },
            function (error) {
                console.log(error.data);
                Loading("#majorSheetFilterResultCardGroups", 'Loading...', "", "hide");
            }
        );
    };
    
    $scope.initExams = function () {
        Loading("#majorSheetFilterExams", 'Loading...', "", "show");
        $http.post(base_url + 'admin/Examination/getSchoolExams',"", config).then(
            function (success) {
                Loading("#majorSheetFilterExams", 'Loading...', "", "hide");
                $scope.exams = success.data;
            },
            function (error) {
                console.log(error.data);
            }
        );
    };
    
    $scope.onSubmit = function(valid){
        if(valid){
            $scope.students = [];
            $scope.students_shift_wise = [];
            $scope.student_ids = [];
            $scope.class_ids = [];
            $scope.batch_ids = [];
            $scope.exam_ids = [];
            Loading("#majorSheetFilterForm", 'Loading...', "", "show");
            $scope.deleteExamId = $scope.filterModel.exam_id;
            $http.post(base_url + 'admin/Examination/getStudentsForMajorSheet', $scope.filterModel, config).then(
                function (success) {
                    Loading("#majorSheetFilterForm", 'Loading...', "", "hide");
                    if($scope.filterModel.result_type == 'single') {
                        $scope.overalltotal = success.data.exam_total_marks;
                        $scope.students = success.data.data;
                        $scope.status = success.data.status;
                        $scope.message_from_server = success.data.message;
                        angular.forEach($scope.students, function (value) {
                            $scope.student_ids.push(value.student_id);
                            $scope.class_ids.push(value.class_id);
                            $scope.batch_ids.push(value.batch_id);
                            $scope.exam_ids.push(value.subjects[0].exams[0].exam_id);
                        });
                        //console.log($scope.students);
                        angular.forEach($scope.students, function (value, key) {
                            if(value.is_shifted == 0){
                                $scope.students_shift_wise.push(value);
                            }
                        });
                        
                        angular.forEach($scope.classes, function (value) {
                            if(value.id == $scope.filterModel.class_id){
                                $scope.selectedClassName = value.name;
                            }
                        });
                        angular.forEach($scope.batches, function (value) {
                            if(value.id == $scope.filterModel.batch_id){
                                $scope.selectedBatchName = value.name;
                            }
                        });
                        angular.forEach($scope.exams, function (value) {
                            if(value.id == $scope.filterModel.exam_id){
                                $scope.selectedExamName = value.title;
                            }
                        });
                        
                        $('body').tooltip({
                            selector: '[rel=tooltip]'
                        });
                    } else if($scope.filterModel.result_type == 'multiple'){
                        $scope.c_total_marks = success.data.exam_total_marks;
                        $scope.c_result = success.data.data;
                        $scope.c_exams = success.data.data[0].subjects[0].exams;
                        $scope.c_subjects = success.data.data[0].subjects;
                        $scope.avg_class_score = success.data.avg_class_score;
                        $scope.class_low_score = success.data.class_low_score;
                        $scope.class_high_score = success.data.class_high_score;
                        $scope.final_term = success.data.term_name;
                    }
                },
                function (error) {
                    Loading("#majorSheetFilterForm", 'Loading...', "", "hide");
                    console.log(error.data);
                }
            );
        }
    };
    $scope.graph_html = "";
    $scope.showCompletedResultCardSingle = function(obj){
        obj.final_term = $scope.final_term;
        $("#myNav").css({"width":"100%","display":"block"});
        $scope.multi_result_cards = [];
        $http.post(base_url + 'admin/Examination/renderCompletedResultCardSingle', obj, config).then(
            function (success) {
                $scope.multi_result_cards.push($sce.trustAsHtml(success.data.template.html));
            },
            function (error) {
                console.log(error.data);
            }
        );
        /*$http.post(base_url + 'admin/Examination/saveGraphAsImage', obj, config).then(
            function (success) {
                $scope.graph_html = $sce.trustAsHtml(success.data);
                setTimeout(function (){
                    
                }, 0);
            },
            function (error) {
                console.log(error.data);
            }
        );*/
        
    };
    
    $scope.setSelectedStudentForRemarks = function(id, exam_id){
        $scope.remarksModel = {};
        $scope.teacherRemarksForm.$setUntouched();
        $scope.teacherRemarksForm.$setPristine();
        $scope.selected_student_for_teacher_remarks.student_id = id;
        $scope.selected_student_for_teacher_remarks.exam_id = exam_id;
    };

    $scope.setSelectedStudentForRemarks2 = function(id, result_card_group_id){
        $scope.remarksModel2 = {};
        $scope.AdministratorRemarksForm.$setUntouched();
        $scope.AdministratorRemarksForm.$setPristine();
        $scope.selected_student_for_administrator_remarks.student_id = id;
        $scope.selected_student_for_administrator_remarks.result_card_group_id = result_card_group_id;
    };
    
    $scope.saveTeacherRemarks = function(){
        $scope.remarksModel.student_id = $scope.selected_student_for_teacher_remarks.student_id;
        $scope.remarksModel.exam_id = $scope.selected_student_for_teacher_remarks.exam_id;
        Loading("#bs-teacher-remarks-modal-sm", 'Loading...', "", "show");
        $http.post(base_url + 'admin/examination/save_teacher_remarks', $scope.remarksModel, config).then(
            function (success) {
                Loading("#bs-teacher-remarks-modal-sm", 'Loading...', "", "hide");
                if(success.data.status == "success"){
                    $("#bs-teacher-remarks-modal-sm").modal("hide");
                    showNotification('<?php echo lang("success_app") ?>', success.data.message, "success");
                    $scope.onSubmit(true);
                }if(success.data.status == "danger"){
                    showNotification('<?php echo lang("error_app") ?>', success.data.message, "error");
                }
            },
            function (error) {
                Loading("#bs-teacher-remarks-modal-sm", 'Loading...', "", "hide");
                console.log(error.data);
            }
        );
    };

    $scope.saveAdministratorRemarks = function(){
        $scope.remarksModel2.student_id = $scope.selected_student_for_administrator_remarks.student_id;
        $scope.remarksModel2.result_card_group_id = $scope.selected_student_for_administrator_remarks.result_card_group_id;
        Loading("#bs-administrator-remarks-modal-sm", 'Loading...', "", "show");
        $http.post(base_url + 'admin/examination/save_administrator_remarks', $scope.remarksModel2, config).then(
            function (success) {
                Loading("#bs-administrator-remarks-modal-sm", 'Loading...', "", "hide");
                if(success.data.status == "success"){
                    $("#bs-administrator-remarks-modal-sm").modal("hide");
                    showNotification('<?php echo lang("success_app") ?>', success.data.message, "success");
                    $scope.onSubmit(true);
                }if(success.data.status == "danger"){
                    showNotification('<?php echo lang("error_app") ?>', success.data.message, "error");
                }
            },
            function (error) {
                Loading("#bs-administrator-remarks-modal-sm", 'Loading...', "", "hide");
                console.log(error.data);
            }
        );
    };
    
    $scope.showTeacherRemark = function(remark, id){
        $scope.showModel.remark = remark;
        $scope.showModel.id = id;
    };

    $scope.showAdministratorRemark = function(remark, id){
        $scope.showModel2.remark = remark;
        $scope.showModel2.id = id;
    };
    
    $scope.updateTeacherRemarks = function(){
        Loading("#bs-update-teacher-remarks-modal-sm", 'Loading...', "", "show");
        $http.post(base_url + 'admin/examination/update_teacher_remarks', $scope.showModel, config).then(
            function (success) {
                Loading("#bs-update-teacher-remarks-modal-sm", 'Loading...', "", "hide");
                if(success.data.status == "success"){
                    $("#bs-update-teacher-remarks-modal-sm").modal("hide");
                    showNotification('<?php echo lang("success_app") ?>', success.data.message, "success");
                    $scope.onSubmit(true);
                }if(success.data.status == "danger"){
                    showNotification('<?php echo lang("error_app") ?>', success.data.message, "error");
                }
            },
            function (error) {
                Loading("#bs-update-teacher-remarks-modal-sm", 'Loading...', "", "hide");
                console.log(error.data);
            }
        );
    };

    $scope.updateAdministratorRemarks = function(){
        Loading("#bs-update-administrator-remarks-modal-sm", 'Loading...', "", "show");
        $http.post(base_url + 'admin/examination/update_administrator_remarks', $scope.showModel2, config).then(
            function (success) {
                Loading("#bs-update-administrator-remarks-modal-sm", 'Loading...', "", "hide");
                if(success.data.status == "success"){
                    $("#bs-update-administrator-remarks-modal-sm").modal("hide");
                    showNotification('<?php echo lang("success_app") ?>', success.data.message, "success");
                    $scope.onSubmit(true);
                }if(success.data.status == "danger"){
                    showNotification('<?php echo lang("error_app") ?>', success.data.message, "error");
                }
            },
            function (error) {
                Loading("#bs-update-administrator-remarks-modal-sm", 'Loading...', "", "hide");
                console.log(error.data);
            }
        );
    };
});

app2.controller("examSettingsController", function ($scope, $http, $window, $location, $sce) {
    $scope.finalResultCardGroups = [];
    $scope.examsInGroup = [];
    $scope.filterModel = {};
    $scope.filterModelExams = [];
    $scope.active_tab_name = "";

    $scope.setTabActive = function (mainTab, subTab){
        if(mainTab == 'single' && subTab == ""){
            $scope.active_tab_name = 'exams';
        } else if(mainTab == 'multi' && subTab == ""){
            $scope.active_tab_name = 'result_card_groups';
        }
    };

    $scope.initFinalResultCardGroups = function (){
        //Loading("#fw_result_card_groups", 'Loading...', "", "show");
        $http.post(base_url + 'admin/Examination/getFinalResultCardGroups', {}, config).then(
            function (success) {
                //Loading("#fw_result_card_groups", 'Loading...', "", "hide");
                $scope.finalResultCardGroups = success.data;
            },
            function (error) {
                console.log(error.data);
                //Loading("#fw_result_card_groups", 'Loading...', "", "hide");
            }
        );
    };

    $scope.getResultCardGroupExams = function (id){
        var exam_ids = 0;
        angular.forEach($scope.finalResultCardGroups, function (value, key) {
            if(value.id == id){
                exam_ids = value.exam_id;
            }
        });
        //Loading("#fw_result_card_groups", 'Loading...', "", "show");
        $http.post(base_url + 'admin/Examination/getExamsByID', {'exam_ids': exam_ids}, config).then(
            function (success) {
                //Loading("#fw_result_card_groups", 'Loading...', "", "hide");
                $scope.examsInGroup = success.data;
            },
            function (error) {
                console.log(error.data);
                //Loading("#fw_result_card_groups", 'Loading...', "", "hide");
            }
        );
    };

    $scope.saveWeighage = function(){
        //Loading("#weightage_form", 'Loading...', "", "show");
        $http.post(base_url + 'admin/Examination/saveFinalWeightage', {'group_id':$scope.filterModel.fwSelectedGroupID, 'exams':$scope.filterModelExams}, config).then(
            function (success) {
                //Loading("#weightage_form", 'Loading...', "", "hide");
                showNotification("toast-"+success.data.status,success.data.message);
            },
            function (error) {
                console.log(error.data);
                //Loading("#weightage_form", 'Loading...', "", "hide");
            }
        );
    };
});

app2.controller("skillAndAssessmentCtrl", function ($scope, $http, $window, $location, $sce) {
    $scope.classes = {};
    $scope.batches = {};
    $scope.students = [];
    $scope.evalModel = {};
    $scope.SAGroups = [];

    $scope.initClasses = function () {
        Loading("#SA_Filter_Classes", 'Loading...', "", "show");
        $http.post(base_url + 'admin/Examination/getSchoolClasses', "", config).then(
            function (success) {
                Loading("#SA_Filter_Classes", 'Loading...', "", "hide");
                $scope.classes = success.data;
            },
            function (error) {
                Loading("#SA_Filter_Classes", 'Loading...', "", "hide");
                console.log(error.data);
            }
        );
    };

    $scope.initBatches = function (class_id) {
        if (class_id) {
            Loading("#SA_Filter_Batches", 'Loading...', "", "show");
            $http.post(base_url + 'admin/Examination/getClassBatches', {'id': class_id}, config).then(
                function (success) {
                    Loading("#SA_Filter_Batches", 'Loading...', "", "hide");
                    $scope.batches = success.data;
                    $scope.filterModel.batch_id = "";
                },
                function (error) {
                    Loading("#SA_Filter_Batches", 'Loading...', "", "hide");
                    console.log(error.data);
                }
            );
        }
    };

    $scope.fetchStudentsForSkills = function(){
        Loading("#filterRow", 'Loading...', "", "show");
        $http.post(base_url + 'admin/Examination/get_students_for_skills', $scope.filterModel, config).then(
            function (success) {
                Loading("#filterRow", 'Loading...', "", "hide");
                $scope.students = success.data;
            },
            function (error) {
                Loading("#filterRow", 'Loading...', "", "hide");
                console.log(error.data);
            }
        );
    };

    $scope.initSkillAndAssessmentGroups = function(class_id){
        Loading("#SA_Groups", 'Loading...', "", "show");
        $http.post(base_url + 'admin/Examination/get_skill_and_assessment_groups_for_class', { 'class_id': class_id }, config).then(
            function (success) {
                Loading("#SA_Groups", 'Loading...', "", "hide");
                $scope.SAGroups = success.data;
            },
            function (error) {
                Loading("#SA_Groups", 'Loading...', "", "hide");
                console.log(error.data);
            }
        );
    };

    $scope.saveSkillAndAssessmentEvaluation = function(){
        angular.forEach($scope.students, function (value, key) {
            angular.forEach($scope.evalModel, function (value2, key2) {
                if(key2 == value.student_id){
                    value.evaluation = value2;
                    value.sa_group_id = $scope.filterModel.sa_group_id;
                }
            });
        });

        Loading("#resultantRow", 'Loading...', "", "show");
        $http.post(base_url + 'admin/Examination/save_students_skills_and_assessments', $scope.students, config).then(
            function (success) {
                Loading("#resultantRow", 'Loading...', "", "hide");
                showNotification("toast-"+success.data.status,success.data.message);
            },
            function (error) {
                Loading("#resultantRow", 'Loading...', "", "hide");
                console.log(error.data);
            }
        );
    };
});


app2.controller("onlineAdmissionCtrl", function ($scope, $http, $window, $location, $sce) { 
    $scope.payment_settings = {};
    $scope.alert = {};
    $scope.formModel = {
        'roll_no' : '',                   
        'firstname' : '',
        'lastname' : '',
        'mobileno' : '',
        'guardian_is' : '',
        'dob' : '',
        'current_address' : '',
        'permanent_address' : '',
        'father_name' : '',
        'father_phone' : '',
        'father_occupation' : '',
        'mother_name' : '',
        'mother_phone' : '',
        'mother_occupation' : '',
        'guardian_occupation' : '',
        'guardian_email' : '',
        'gender' : '',
        'guardian_name' : '',
        'guardian_relation' : '',
        'guardian_phone' : '',
        'guardian_address' : '',
        'transaction_ref_no' : '',
        'paid_amount' : '',
        'document' : '',
        'profile' : '',
    };

    $scope.setVal = function() {
        if($scope.formModel.guardian_is == 'father'){
            $scope.formModel.guardian_name = $scope.formModel.father_name;
            $scope.formModel.guardian_relation = 'father';
            $scope.formModel.guardian_phone = $scope.formModel.father_phone;
            $scope.formModel.guardian_occupation = $scope.formModel.father_occupation;
        }
        if($scope.formModel.guardian_is == 'mother'){
            $scope.formModel.guardian_name = $scope.formModel.mother_name;
            $scope.formModel.guardian_relation = 'mother';
            $scope.formModel.guardian_phone = $scope.formModel.mother_phone;
            $scope.formModel.guardian_occupation = $scope.formModel.mother_occupation;
        }
        if($scope.formModel.guardian_is == 'other'){
            $scope.formModel.guardian_name = '';
            $scope.formModel.guardian_relation = '';
            $scope.formModel.guardian_phone = '';
            $scope.formModel.guardian_occupation = '';
        }
    };

    $scope.initPaystackSettings = function (){
        $http.post(base_url + 'welcome/get_payment_settings', {}, config).then(
            function (success) {
                $scope.payment_settings = success.data;

            },
            function (error) {
                console.log(error.data);
            }
        );
    }

    $scope.save = function (){
        var file = document.getElementById('file').files[0];
        var file2 = document.getElementById('file2').files[0];
        if(file){
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function() {
                $scope.formModel.document = reader.result;
            };
            reader.onerror = function(error) {
                console.log('Error: ', error);
            };
        }

        if(file2){
            var reader = new FileReader();
            reader.readAsDataURL(file2);
            reader.onload = function() {
                $scope.formModel.profile = reader.result;
            };
            reader.onerror = function(error) {
                console.log('Error: ', error);
            };
        }
        
        
        var handler = PaystackPop.setup({
        key: $scope.payment_settings.api_secret_key,
        email: $scope.payment_settings.api_email,
        amount: $scope.payment_settings.admission_fee * 100,
        metadata: {
            custom_fields: [
                {
                    display_name: "Mobile Number",
                    variable_name: "mobile_number",
                    value: "+2348012345678"
                }
            ]
        },
        callback: function(response){
            $scope.formModel.transaction_ref_no = response.reference;
            $scope.formModel.paid_amount = $scope.payment_settings.admission_fee;
            $http.post(base_url + 'welcome/save_admission', $scope.formModel, config).then(
                function (success) {
                    $scope.alert = success.data;
                    $scope.formModel = {
                        'roll_no' : '',                   
                        'firstname' : '',
                        'lastname' : '',
                        'mobileno' : '',
                        'guardian_is' : '',
                        'dob' : '',
                        'current_address' : '',
                        'permanent_address' : '',
                        'father_name' : '',
                        'father_phone' : '',
                        'father_occupation' : '',
                        'mother_name' : '',
                        'mother_phone' : '',
                        'mother_occupation' : '',
                        'guardian_occupation' : '',
                        'guardian_email' : '',
                        'gender' : '',
                        'guardian_name' : '',
                        'guardian_relation' : '',
                        'guardian_phone' : '',
                        'guardian_address' : '',
                        'transaction_ref_no' : '',
                        'paid_amount' : '',
                        'document' : '',
                        'profile': ''
                    };
                    $scope.employeeform.$setUntouched();
                    $scope.employeeform.$setPristine();
                },
                function (error) {
                    console.log(error.data);
                }
            );
          },
          onClose: function(){
              alert('window closed');
          }
        });
        handler.openIframe();
    };

});

app2.controller("accountController", function ($scope, $http, $sce) {
    $scope.requisitions = [];
    $scope.reqModel = {};
    $scope.departments = [];
    $scope.staff = [];
    $scope.formModel = {};
    $scope.totalCost=0;
    $scope.alert = {};
    $scope.editRowIndex = 0;
    $scope.editModel = {};

    $scope.addRequisition = function(){
        $scope.requisitions.push($scope.reqModel);
        angular.forEach($scope.requisitions, function (value, key) {
            $scope.totalCost += value.price;
        });
        $scope.requisitionform.$setUntouched();
        $scope.requisitionform.$setPristine();
        $("#requisition-modal").modal("hide");
        $scope.reqModel = {};
    };

    $scope.removeRow = function(item){
        var index = $scope.requisitions.indexOf(item);
        $scope.requisitions.splice(index, 1); 
    };

    $scope.editRow = function(item, index){
        $scope.editRowIndex = index;
        $scope.editModel = item;
    };

    $scope.initDepartments = function(){
        $http.post(base_url + 'admin/requisition/getDepartments', {}, config).then(
            function (success) {
                $scope.departments = success.data.data;
            },
            function (error) {
                console.log(error.data);
            }
        );
    };

    $scope.initStaff = function(){
        $http.post(base_url + 'admin/requisition/getStaff', {}, config).then(
            function (success) {
                $scope.staff = success.data.data;
            },
            function (error) {
                console.log(error.data);
            }
        );
    };

    $scope.updateRequisition = function(){
        $("#requisition-edit-modal").modal("hide");
        $scope.requisitions[$scope.editRowIndex] = $scope.editModel;
    };

    $scope.saveRequisition = function(){
        angular.forEach($scope.requisitions, function (value, key) {
            if(value.previous_price == undefined){
                value.previous_price = '';
            }
        });
        $scope.formModel.details = $scope.requisitions;
        $http.post(base_url + 'admin/requisition/saveRequisition', $scope.formModel, config).then(
            function (success) {
                $scope.alert = success.data;
                if(success.data.status == "success"){
                    $scope.requisitions = [];
                    $scope.formModel = {};
                    $scope.requisitionform1.$setUntouched();
                    $scope.requisitionform1.$setPristine();
                }
                //$scope.staff = success.data.data;
            },
            function (error) {
                console.log(error.data);
            }
        );
    };
});

app2.controller("ledgerController", function ($scope, $http) {
    $scope.formModel = {};
    $scope.details = [];
    $scope.lederSummary = [];
    $scope.trialBalance = [];
    $scope.opening_total = 0;
    $scope.closing_total = 0;
    $scope.credit_total = 0;
    $scope.debit_total = 0;
    $scope.total_trial_balance = 0.0;
    $scope.total_trial_credit_balance = 0.0;
    $scope.total_trial_debit_balance = 0.0;
    $scope.accounts = [];
    $scope.opening_balance = 0;
    $scope.opening_balance_type;
    $scope.opening_balance_date = null;
    
    $scope.getLedgerData = function(){
        Loading("#ledgerFormBody", 'Loading...', "", "show");
        $http.post(base_url + 'admin/accounts/get_ledger', $scope.formModel, config).then(
            function (success) {
                Loading("#ledgerFormBody", 'Loading...', "", "hide");
                $scope.details = success.data.data;
                $scope.opening_balance = success.data.opening_balance;
                $scope.opening_balance_type = success.data.opening_balance_type;
                $scope.opening_balance_date = success.data.opening_balance_date;
            },
            function (error) {
                Loading("#ledgerFormBody", 'Loading...', "", "hide");
                console.log(error.data);
            }
        );
    };

    $scope.getTrialBalanceData = function(){
        Loading("#trialBalanceFormBody", 'Loading...', "", "show");
        $http.post(base_url + 'admin/accounts/get_trial_balace_report', $scope.formModel3, config).then(
            function (success) {
                Loading("#trialBalanceFormBody", 'Loading...', "", "hide");
                $scope.trialBalance = success.data.data;
                $scope.total_trial_balance = success.data.total_balance;
                $scope.total_trial_debit_balance = success.data.total_debit_balance;
                $scope.total_trial_credit_balance = success.data.total_credit_balance;
            },
            function (error) {
                Loading("#trialBalanceFormBody", 'Loading...', "", "hide");
                console.log(error.data);
            }
        );
    };

    $scope.getLedgerSummaryData = function(){
        Loading("#ledgerSummaryFormBody", 'Loading...', "", "show");
        $http.post(base_url + 'admin/accounts/get_ledger_summary', $scope.formModel2, config).then(
            function (success) {
                Loading("#ledgerSummaryFormBody", 'Loading...', "", "hide");
                if(success.data.status == "success"){
                    $scope.lederSummary = success.data.data.accounts;
                    $scope.opening_total = success.data.data.opening_total;
                    $scope.closing_total = success.data.data.closing_total;
                    $scope.credit_total = success.data.data.credit_total;
                    $scope.debit_total = success.data.data.debit_total;
                } else if(success.data.status == "error"){
                    $scope.lederSummary = [];
                    $scope.opening_total = 0;
                    $scope.closing_total = 0;
                    $scope.credit_total = 0;
                    $scope.debit_total = 0;
                }
            },
            function (error) {
                Loading("#ledgerSummaryFormBody", 'Loading...', "", "hide");
                console.log(error.data);
            }
        );
    };

    $scope.initAccounts = function(){
        Loading("#accounts-select", 'Loading...', "", "show");
        $http.post(base_url + 'admin/accounts/all', $scope.formModel2, config).then(
            function(success){
                Loading("#accounts-select", 'Loading...', "", "hide");
                $scope.accounts = success.data;
            },
            function(error){
                Loading("#accounts-select", 'Loading...', "", "hide");
                console.log(error.data);
            }
        );
    };
});

app2.controller("jvController", function ($scope, $http, $sce) {
    $scope.formModel = {
        transcations: [
            {
                account_id:'',
                memo: '',
                amount:0    
            }
        ],
        jv_description: '',
        jv_manual_id: ''
    };
    $scope.accounts = [];
    $scope.debit=0;
    $scope.credit=0;
    $scope.alert = {};
    
    $scope.initAccounts = function () {
        $http.post(base_url + 'admin/accounts/get_accounts', {}, config).then(
            function (success) {
                if(success.data.status == "success"){
                    $scope.accounts = success.data.data;    
                } else if (success.data.status == "error"){
                    alert("please add an account first.");
                }
            },
            function (error) {
                console.log(error.data);
            }
        );
    };
    $scope.addNewRow = function (){
        var obj = {
            account_id:'',
            memo: '',
            amount:0    
        };
        $scope.formModel.transcations.push(obj);
    };

    $scope.savejv = function() {
        $scope.alert = {};
        if($scope.formModel.transcations.length > 1 && ($scope.debit-$scope.credit) == 0){
            Loading("#jv-add-form-content", 'Loading...', "", "show");
            $http.post(base_url + 'admin/accounts/save_jv', $scope.formModel, config).then(
                function (success) {
                    Loading("#jv-add-form-content", 'Loading...', "", "hide");
                    if(success.data.status == "success"){
                        window.location.reload();
                    }
                },
                function (error) {
                    Loading("#jv-add-form-content", 'Loading...', "", "hide");
                    console.log(error.data);
                }
            );
            console.log($scope.formModel);    
        } else {
            $scope.alert = {"status":"danger","message":"Balance should be zero."};
        }
        
    };

    $scope.setDebitCreditValue = function(){
        $scope.debit=0;
        $scope.credit=0;
        angular.forEach($scope.formModel.transcations, function (value, key) {
            if(value.amount != undefined) {
                if(value.type == "debit"){
                    $scope.debit += value.amount;
                } else if(value.type == "credit") {
                    $scope.credit += value.amount;
                }
            }
        });
    };
});