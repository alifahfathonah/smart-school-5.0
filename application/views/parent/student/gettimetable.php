<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1>
            <i class="fa fa-user-plus"></i> <?php echo $this->lang->line('student_information'); ?><small><?php echo $this->lang->line('student1'); ?></small>        </h1>
    </section>    
    <section class="content">
        <div class="row">
            <div class="col-md-3">            
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <img class="profile-user-img img-responsive img-circle" src="<?php echo base_url() . $student['image'] ?>" alt="User profile picture">
                        <h3 class="profile-username text-center"><?php echo $student['firstname'] . " " . $student['lastname']; ?></h3>
                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b><?php echo $this->lang->line('admission_no'); ?></b> <a class="pull-right text-aqua"><?php echo $student['admission_no']; ?></a>
                            </li>
                            <li class="list-group-item">
                                <b><?php echo $this->lang->line('roll_no'); ?></b> <a class="pull-right text-aqua"><?php echo $student['roll_no']; ?></a>
                            </li>
                            <li class="list-group-item">
                                <b><?php echo $this->lang->line('class'); ?></b> <a class="pull-right text-aqua"><?php echo $student['class']; ?></a>
                            </li>
                            <li class="list-group-item">
                                <b><?php echo $this->lang->line('section'); ?></b> <a class="pull-right text-aqua"><?php echo $student['section']; ?></a>
                            </li>
                            <li class="list-group-item">
                                <b><?php echo $this->lang->line('rte'); ?></b> <a class="pull-right text-aqua"><?php echo $student['rte']; ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">
                            <?php echo $this->lang->line('class_timetable'); ?>
                        </h3>
                    </div>
                    <div class="box-body">
                        <?php
                        if (!empty($result_array)) {
                            ?>
                            <div class="table-responsive">
                                <div class="download_label"><?php echo $this->lang->line('class_timetable'); ?></div>
                                <table class="table  table-bordered example">
                                    <thead>
                                        <tr>
                                            <th>
                                                <?php echo $this->lang->line('day'); ?>
                                            </th>
                                            <th>
                                                <?php echo $this->lang->line('subject'); ?>
                                            </th>
                                            <th>
                                                <?php echo $this->lang->line('teacher'); ?>
                                            </th>
                                            <th>
                                                <?php echo $this->lang->line('time')." ".$this->lang->line('from'); ?>
                                            </th>
                                            <th>
                                                <?php echo $this->lang->line('time')." ".$this->lang->line('to'); ?>
                                            </th>
                                            <th>
                                                <?php echo $this->lang->line('room_no'); ?>
                                            </th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($result_array as $key => $timetable) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php
                                                    echo $timetable->day;
                                                    ?></td>
                                                <td>
                                                    <?php
                                                    echo $timetable->name . "(" . $timetable->code . ")";
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo ($timetable->surname != "") ? $timetable->staff_name . " " . $timetable->surname : $timetable->staff_name;
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo $timetable->time_from;
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo $timetable->time_to;
                                                    ?></td>
                                                <td>
                                                    <?php
                                                    echo $timetable->room_no;
                                                    ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
                            <?php
                        }
                        ?>
                    </div>  
                </div>
            </div>
        </div>
</div>
</section>
</div>