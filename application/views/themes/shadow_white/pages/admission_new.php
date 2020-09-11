<style>
    .req{
        color:red;
    }
</style>
<?php
if (!$form_admission) {
    ?>
    <div class="alert alert-danger">
        <?php echo $this->lang->line('admission_form_disable_please_contact_to_administrator');?>
    </div>
    <?php
    return;
}
?>

<div class="about-title relative fullwidth" ng-init="initPaystackSettings()">
<div class="innermain">

</div>
</div>
<script src="https://js.paystack.co/v1/inline.js"></script>

<form id="form1" class="spaceb60 spacet60 onlineform" ng-submit="save()"  name="employeeform" method="post" accept-charset="utf-8" enctype="multipart/form-data" novalidate="">
    <div class="alert alert-success" ng-if="alert.message"><strong>Alert!</strong> {{alert.message}}</div>
    
    <div class="row">
        <div class="col-md-12"><h4 class="pagetitleh2"><?php echo $this->lang->line('image'); ?></h4></div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="exampleInputEmail2"> <?php echo $this->lang->line('upload')." ".$this->lang->line('student_image');?></label>
               <input type="file" id="file2" class="form-control" name="file2"/>
            </div>
        </div>

        <!--<div class="col-md-3">
            <div class="form-group">
                <label for="exampleInputEmail1"><?php echo $this->lang->line('passport'); ?></label><small class="req"> *</small> 
                <input id="passport" name="passport" ng-model="formModel.passport" placeholder="" type="text" class="form-control" required="" />
            </div>
        </div>-->

    </div>
    <div class="row">
    <div class="col-md-12"><h4 class="pagetitleh2"><?php echo $this->lang->line('student_personal_details'); ?></h4></div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="exampleInputEmail1"><?php echo $this->lang->line('class'); ?></label><small class="req"> *</small>
            <select ng-model="formModel.class_id" name="class_id" class="form-control" required="">
                <option value=""><?php echo $this->lang->line('select'); ?></option>
                <?php foreach ($classlist as $class) { ?>
                    <option value="<?php echo $class['id'] ?>"><?php echo $class['class'] ?></option>
                <?php $count++; } ?>
            </select>
        </div>
    </div>
    
    <!--<div class="col-md-3">
        <div class="form-group">
            <label for="exampleInputEmail1"><?php echo $this->lang->line('section'); ?></label><small class="req"> *</small> 
            <select  id="section_id" name="section_id" class="form-control" >
                <option value=""   ><?php echo $this->lang->line('select'); ?></option>
            </select>
            <span class="text-danger"><?php echo form_error('section_id'); ?></span>
        </div>
    </div>-->
    
    <div class="col-md-3">
        <div class="form-group">
            <label for="exampleInputEmail1"><?php echo $this->lang->line('first_name'); ?></label><small class="req"> *</small> 
            <input ng-model="formModel.firstname" name="firstname" placeholder="" type="text" class="form-control"  required=""/ >
        </div>
    </div>



    <div class="col-md-3">
        <div class="form-group">
            <label for="exampleInputEmail1"><?php echo $this->lang->line('last_name'); ?></label>
            <input ng-model="formModel.lastname" name="lastname" placeholder="" type="text" class="form-control" />
        </div>
    </div>
  </div><!--./row--> 
  <div class="row"> 
    <div class="col-md-3">
        <div class="form-group">
            <label for="exampleInputFile"> <?php echo $this->lang->line('gender'); ?></label><small class="req"> *</small> 
            <select class="form-control" ng-model="formModel.gender" name="gender" required="">
                <option value=""><?php echo $this->lang->line('select'); ?></option>
                <?php foreach ($genderList as $key => $value) { ?>
                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="exampleInputEmail1"><?php echo $this->lang->line('date_of_birth'); ?></label><small class="req"> *</small> 
            <input  type="text" class="form-control mydatepicker-autoclose" ng-model="formModel.dob"  id="dob" name="dob" readonly="readonly" required=""/>
        </div>
    </div>




</div><!--./row--> 
    <div class="row">  
        <div class="col-md-12"><h4 class="pagetitleh2"><?php echo $this->lang->line('parent_guardian_detail'); ?></h4></div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo $this->lang->line('father_name'); ?></label>
                        <input ng-model="formModel.father_name" name="father_name" placeholder="" type="text" class="form-control" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo $this->lang->line('father_phone'); ?></label>
                        <input ng-model="formModel.father_phone" name="father_phone" placeholder="" type="text" class="form-control" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo $this->lang->line('father_occupation'); ?></label>
                        <input ng-model="formModel.father_occupation" name="father_occupation" placeholder="" type="text" class="form-control" />
                    </div>
                </div>

            
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo $this->lang->line('mother_name'); ?></label>
                        <input ng-model="formModel.mother_name" name="mother_name" placeholder="" type="text" class="form-control" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo $this->lang->line('mother_phone'); ?></label>
                        <input ng-model="formModel.mother_phone" name="mother_phone" placeholder="" type="text" class="form-control" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo $this->lang->line('mother_occupation'); ?></label>
                        <input ng-model="formModel.mother_occupation" name="mother_occupation" placeholder="" type="text" class="form-control" />
                    </div>
                </div>
        </div><!--./row-->        
           
        <div class="row">
                <div class="form-group col-md-12">
                    <label><?php echo $this->lang->line('if_guardian_is'); ?><small class="req"> *</small>&nbsp;&nbsp;&nbsp;</label>
                    <label class="radio-inline">
                        <input type="radio" name="guardian_is" required="" ng-model="formModel.guardian_is" value="father" ng-click="setVal()"> <?php echo $this->lang->line('father'); ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="guardian_is" required="" ng-model="formModel.guardian_is" value="mother" ng-click="setVal()"> <?php echo $this->lang->line('mother'); ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="guardian_is" required="" ng-model="formModel.guardian_is" value="other" ng-click="setVal()"> <?php echo $this->lang->line('other'); ?>
                    </label>
                </div>


                <div class="col-md-4">
                    <div class="form-group">
                        <label for="exampleInputEmail1"> <?php echo $this->lang->line('upload')." ".$this->lang->line('documents');?></label>
                       <input type="file" class="form-control" id="file" name="file"/>
                    </div>
                </div>




                    <div class="col-md-4">
                            <div class="form-group">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('guardian_name'); ?></label>
                                <input ng-model="formModel.guardian_name" name="guardian_name" placeholder="" type="text" class="form-control"  />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('guardian_relation'); ?></label>
                                <input ng-model="formModel.guardian_relation" name="guardian_relation" placeholder="" type="text" class="form-control" />
                            </div>
                        </div>
                </div><!--./row-->    
                <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('guardian_phone'); ?></label>
                                <input ng-model="formModel.guardian_phone" name="guardian_phone" placeholder="" type="text" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('guardian_occupation'); ?></label>
                                <input ng-model="formModel.guardian_occupation" name="guardian_occupation" placeholder="" type="text" class="form-control" />
                            </div>
                        </div>

               
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo $this->lang->line('guardian_email'); ?></label>
                        <input ng-model="formModel.guardian_email" name="guardian_email" placeholder="" type="text" class="form-control" />
                    </div>

                </div>

                <div class="col-md-12">
                  <div class="form-group">   
                    <label for="exampleInputEmail1"><?php echo $this->lang->line('guardian_address'); ?></label>
                    <textarea ng-model="formModel.guardian_address" name="guardian_address" placeholder="" class="form-control" rows="2"></textarea>
                  </div>  
                </div>
            
           
            <div class="col-md-12"> 
              <div class="form-group pull-right">   
                <button type="submit" class="onlineformbtn"><?php echo $this->lang->line('pay'); ?></button>
               </div> 
            </div>    
        </div><!--./row-->    
</form>


<script type="text/javascript">
    if ($(".xcrud-container").length == 0) {
        $.getScript("<?php echo base_url(); ?>assets/xcrud/plugins/jquery-ui/jquery-ui.min.js").done(function () {
            $.getScript("<?php echo base_url(); ?>assets/xcrud/plugins/timepicker/jquery-ui-timepicker-addon.js").done(function () {
                var datepicker_config = {
                    changeMonth: true,
                    changeYear: true,
                    showSecond: false,
                    controlType: 'select',
                    yearRange: "-50:+10",
                    dateFormat: 'dd/mm/yy',
                    timeFormat: 'hh:mm tt'
                };
                $(".mydatepicker-autoclose").datepicker(datepicker_config);
            });
        });
    }
    if ($.trim($('.hint').html()).length) {
        $('.hint').show();
    }
</script>
<!-- Angular js -->
<script src="<?php echo base_url(); ?>assets/angularjs/angular.min.js"></script>
<!-- Angular auto validator lib -->
<script src="<?php echo base_url(); ?>assets/angularjs-auto-validator/dist/jcs-auto-validate.js"></script>
<!-- App js -->
<script src="<?php echo base_url(); ?>assets/js/app.js?v=<?= date("h.i.s") ?>"></script>