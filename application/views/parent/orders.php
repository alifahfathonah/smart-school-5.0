<div class="content-wrapper" style="min-height: 946px;">  
    <section class="content">
        <div class="row">
        	<?php if ($this->session->flashdata('msg')) { ?>
                <?php echo $this->session->flashdata('msg') ?>
            <?php } ?>
        	<div class="col-md-12">     
                <div class="box box-primary">
                	<div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('Orders'); ?></h3>
                    </div>

                    <div class="box-body">
                    	<?php echo $orders; ?>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>