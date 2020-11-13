
<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="content-wrapper" style="min-height: 946px;">  
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> <?php echo $this->lang->line('my_children'); ?> <small><?php echo $this->lang->line('student1'); ?></small>        </h1>
    </section>
    <section class="content">
        
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-search"></i> Select Criteria</h3>
            </div>
            <div class="box-body" id="majorSheetFilterForm">
                <form name="majorSheetFilterForm" id="filterForm">
                    <div class="row">
                        
                        <div class="col-sm-6 col-lg-3 col-md-3 col20">
                            <div class="form-group">
                                <label class="control-label"><?php echo lang("session"); ?></label>
                                <select class="form-control" name="academic_year_id" id="session_id" required>
                                    <option value=""><?php echo lang("please_select_a_session"); ?></option>
                                    <?php foreach($sessions as $sess) { ?>
                                        <option value="<?= $sess->id; ?>"><?= $sess->session; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3 col-md-3 col20">
                            <div class="form-group">
                                <label class="control-label"><?php echo lang('my_children') ?></label>
                                <select class="form-control" name="student_id" id="student_id" required>
                                    <option value=""><?php echo lang('select_a_child') ?></option>
                                    <?php foreach($student_list as $std) { ?>
                                        <option value="<?= $std['id']; ?>"><?= $std['firstname'] . " " . $std['lastname']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3 col-md-3 col20">
                            <div class="form-group">
                                <label class="control-label"><?php echo lang('exams') ?></label>
                                <select class="form-control" name="exam_id" required="">
                                    <option value=""><?php echo lang('please_select_an_exam') ?></option>
                                    <?php foreach($exams as $x) { ?>
                                        <option value="<?= $x->id; ?>"><?= $x->title; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <input type="submit" class="btn btn-primary pull-right" value="<?php echo lang('search') ?>" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">     
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <div id="result-container">ASDF</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function(){
        $("#filterForm").on("submit", function(e){
            e.preventDefault();
            formData = $("#filterForm").serialize();
            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>parent/parents/getStudentResult",
                data: formData,
                dataType: "html",
                success: function (data) { 
                    $("#result-container").html(data);
                }
            });
        });
    });
</script>