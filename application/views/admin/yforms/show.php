<style>
    @media print
    {    
        .no-print, .no-print *
        {
            display: none !important;
        }
    }
    
    .result_card_container {
        font-size: 12px; 
        padding-top:25px;
        height: 27.5cm;
        margin-bottom: 5px;
        max-height:27.5cm;
        padding-left: 25px; 
        padding-right: 25px; 
        border: 3px solid black; 
        font-family: sans-serif;
    }
    #footer-table{
        margin-top:25px;
    }
    #fee_card_container {
        font-size: 12px; 
        height: 27.5cm; 
        max-height:27.5cm;
        padding-left: 25px; 
        padding-right: 25px; 
        border: 0px solid black; 
        font-family: sans-serif;
    }
    .hidden_row{
        display:none;
    }
    .border{
        border: 1px solid;
    }
    .border-top{
        border-top: 1px solid;
    }
    .border-right{
        border-right: 1px solid;
    }
    .border-bottom{
        border-bottom: 1px solid;
    }
    .border-left{
        border-left: 1px solid;
    }
</style>
<script>     
    function loadFormLanguageWise(lang_id,lang_name,tag, class_id, batch_id, exam_id, std_id,fee_id, class_name, batch_name, emp_id, salary_type_id) {
        
        var formData = {
            "lang_id": lang_id,
            "tag": tag, 
            "class_id": class_id, 
            "batch_id": batch_id,
            "exam_id": exam_id,
            "student_id": std_id,
            "fee_id": fee_id,
            "class_name": class_name,
            "batch_name": batch_name,
            "employee_id": emp_id,
            "salary_type_id": salary_type_id
        };
        
        jQuery.ajax({
            type: "POST",
            url: "<?php echo base_url(); ?>forms/get_form_language_wise",
            dataType: "json",
            data: formData,
            success: function (data) {
                if(data.status === "success"){
                    $("#myContainer").html(data.data);
                    if(lang_name === 'english'){
                        $("#myContainer").css({"direction":"ltr"});
                    }else{
                        $("#myContainer").css({"direction":"rtl"});
                    }
                } else if(data.status === "error"){
                    $("#myContainer").html(data.message);
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    }
</script>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
        <h1 align="center"><i class="fa fa-mortar-board"></i> <?php echo $this->lang->line('result'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Page Content -->
        <div class="container" style="background-color:white;">
            <div class="row">
                <div id="myContainer" style="width:60%; margin-left: 20%;">
                    <?= $template->html; ?>
                </div>
            </div>
        </div>
    </section>
</div>