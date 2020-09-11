<style type="text/css">
    #regiration_form fieldset:not(:first-of-type) {
        display: none;
    }
</style>

<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1>
            <i class="fa fa-language"></i> <?php echo $this->lang->line('subjects'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-4">
            </div>
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-body">
                        <div id="box_header"></div>

                        <form id="regiration_form" novalidate action="action.php"  method="post">
                      <?php
                            $counter = 1;
                            $total_count = count($examquestion);
                            foreach ($examquestion as $question_key => $question_value) {
                                ?>
                                <fieldset>
                                    <h2>Question :<?php echo $counter; ?></h2>
                                    <?php echo $question_value->question; ?>
                                    <?php
                                    foreach ($questionOpt as $question_opt_key => $question_opt_value) {
                                        ?>
                                        <input type="radio" name="radio[]" value=""><?php echo $question_value->{$question_opt_key}; ?>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                    if ($counter == 1 && $counter == $total_count) {
                                        ?>
                                        <input type="submit" name="submit" class="submit btn btn-success" value="Submit" />
                                        <?php
                                    }elseif ($counter ==1) {
                                        ?>
                                        
                                        <input type="button" name="next" class="next btn btn-info" value="Next" />
                                        <?php
                                    } elseif ($counter !=1 && $counter < $total_count) {
                                        ?>
                                        <input type="button" name="previous" class="previous btn btn-default" value="Previous" />
                                        <input type="button" name="next" class="next btn btn-info" value="Next" />
                                        <?php
                                    }elseif ($counter == $total_count) {
                                   ?>
 <input type="button" name="previous" class="previous btn btn-default" value="Previous" />
    <input type="submit" name="submit" class="submit btn btn-success" value="Submit" />
                                   <?php
                                    }
                                    ?>

                                </fieldset>
                                <?php
                            $counter++;
                            }
                            ?>


                        </form>


                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
    function get_elapsed_time_string(total_seconds) {
        function pretty_time_string(num) {
            return (num < 10 ? "0" : "") + num;
        }

        var hours = Math.floor(total_seconds / 3600);
        total_seconds = total_seconds % 3600;

        var minutes = Math.floor(total_seconds / 60);
        total_seconds = total_seconds % 60;

        var seconds = Math.floor(total_seconds);

        // Pad the minutes and seconds with leading zeros, if required
        hours = pretty_time_string(hours);
        minutes = pretty_time_string(minutes);
        seconds = pretty_time_string(seconds);

        // Compose the string for display
        var currentTimeString = hours + ":" + minutes + ":" + seconds;

        return currentTimeString;
    }

    var elapsed_seconds = 0;
    setInterval(function () {
        elapsed_seconds = elapsed_seconds + 1;
        $('#box_header').text(get_elapsed_time_string(elapsed_seconds));
    }, 1000);
</script>

<script type="text/javascript">
    $(document).ready(function () {
        var current = 1, current_step, next_step, steps;
        steps = $("fieldset").length;
        $(".next").click(function () {
            current_step = $(this).parent();
            next_step = $(this).parent().next();
            next_step.show();
            current_step.hide();
           
        });
        $(".previous").click(function () {
            current_step = $(this).parent();
            next_step = $(this).parent().prev();
            next_step.show();
            current_step.hide();
        
        });

     
    });
</script>