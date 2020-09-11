<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1>
            <i class="fa fa-calendar-times-o"></i> <?php echo $this->lang->line('class_timetable'); ?> </h1>
    </section>
    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-12">

                <div class="box box-warning">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"> <?php echo $this->lang->line('class_timetable'); ?></h3>
                        <div class="box-tools pull-right">
                        </div>
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

    </section>
</div>