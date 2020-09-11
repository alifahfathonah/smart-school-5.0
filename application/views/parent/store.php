<style>
	.cart-item{
		height: auto;
		max-height: 350px; 
		border: 1px solid orange; 
		border-radius: 10px;
        margin-bottom: 25px;
        margin-right: 25px;
	}
</style>
<script>
    $(document).ready(function(){
        $("#searchForm").on("submit", function(e){
            e.preventDefault();
            $.ajax({
                url: "<?php echo site_url('parent/parents/searchItems'); ?>",
                method: "POST",
                data: $("#searchForm").serialize(),
                success: function(success){
                    $("#itemListContainer").html(success);
                },
                error: function (error){
                    console.log(error);
                }
            });
        });
    });
</script>
<div class="content-wrapper" style="min-height: 946px;">  
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> <?php echo $this->lang->line('my_children'); ?> <small><?php echo $this->lang->line('student1'); ?></small>        </h1>
    </section>
    <section class="content">
        <div class="row">
        	<?php if ($this->session->flashdata('msg')) { ?>
                <?php echo $this->session->flashdata('msg') ?>
            <?php } ?>
        	<div class="col-md-3">     
                <div class="box box-primary">
                	<div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('categories'); ?></h3>
                    </div>
                    <div class="box-body">
                        <div class="col-md-12">
                            <ul class="list-group">
                                <?php foreach($itemcategories as $c) { ?>
                                    <li class="list-group-item"><a href="<?php echo base_url(); ?>parent/parents/store/<?php echo $c->id; ?>"><?php echo $c->item_category; ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9">     
                <div class="box box-primary">
                	<div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('school_store_items'); ?></h3>
                    </div>

                    <div class="box-body">
                        <?php if(count($items) > 0){ ?>

                            <div class="col-md-12" style="text-align: right;">
                                <form id="searchForm">
                                    <div class="col-md-5" style="margin-bottom: 5px;">
                                        <input type="text" name="searchBy" class="form-control" placeholder="Search By..." />
                                    </div>
                                    <div class="col-md-1"><button type="submint" class="btn btn-sm btn-block btn-primary" style="font-size: 10px;">Search</button></div>
                                </form>
                            </div>
                            <hr/>
                            <div class="col-md-12" id="itemListContainer">
    	                    	<?php foreach($items as $item) { ?>
                                    <div class="col-md-3 text-center" style="margin-bottom: 15px;">
                                        <div class="col-md-12" style="border: 1px solid orange; border-radius: 10px;">
        		                    		<img src="<?php echo base_url(); ?>uploads/schoolstore/<?php echo $item->picture; ?>" style="height: 150px; width: 100%; margin-top: 5px;"/>
        		                    		<h5 class="text-center"><?php echo $item->item_store; ?></h5>
        		                    		<p class="text-center">
                                                <strong style="border-right: 1px solid; padding-right: 5px;">Stock: <?php echo $item->stock; ?></strong>
                                                <span class="text-danger"><?php echo $this->session->userdata("student")["currency_symbol"] ."". $item->price; ?></span>
                                            </p>
        		                    		<form action="<?php echo site_url('parent/parents/savetocart'); ?>" method="post">
        			                    		<input type="hidden" name="item_id" value="<?php echo $item->id; ?>"/>
        			                    		<table style="margin-bottom: 15px;" align="center">
        			                    			<tr>
        			                    				<td><strong>Qty</strong></td>
                                                        <td style="padding-left: 5px;">
        			                    					<input type="number" max="<?php echo $item->stock; ?>" <?php if($item->stock == 0) { echo "disabled"; } ?> min="1" name="quantity" style="height: 30px; width: 100%;" />
        			                    					<span class="text-danger"  style="font-size: 10px;"><?php echo form_error('quantity'); ?></span>
        			                    				<td>
        			                    				<td style="padding-left: 5px;" class="text-left">
                                                            <button type="submit" class="btn btn-info btn-sm" <?php if($item->stock == 0) { echo "disabled"; } ?>>Add to cart</button>
                                                        </td>
        			                    			</tr>
        			                    		</table>
        		                    		</form>
                                        </div>
    		                    	</div>
    	                    	<?php } ?>
                            </div>
                    	<?php } else { ?>
                    		<div class="alert alert-danger">No record found!</div>
                    	<?php } ?>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>