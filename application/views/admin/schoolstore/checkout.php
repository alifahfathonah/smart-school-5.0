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
        	<div class="col-md-12">     
                <div class="box box-primary">
                	<div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('checkout'); ?></h3>
                    </div>

                    <div class="box-body">
                    	<?php if(count($items) > 0) { ?>
                    		<form method="POST" action="<?php echo site_url('admin/schoolstore/placeorder'); ?>">
		                    	<div class="alert alert-success hide"></div>
		                    	<table class="table table-default">
		                    		<tr>
		                    			<th>Image</th>
		                    			<th>Name</th>
		                    			<th>Price</th>
		                    			<th>Quantity</th>
		                    			<th>Sub Total</th>
		                    			<th style="width: 200px;">Action</th>
		                    		</tr>
		                    		<?php $total=0; $item_ids=''; $quantities=''; foreach($items as $item) { $item_ids .= $item->id.","; $quantities .= $item->quantity.","; ?>
		                    			<tr>
		                    				<td><img src="<?php echo base_url(); ?>uploads/schoolstore/<?php echo $item->picture; ?>" style="width: 40px; height: 40px;" /></td>
		                    				<td><?php echo $item->item_store; ?></td>
		                    				<td><?php echo $this->session->userdata("admin")["currency_symbol"]; ?><?php echo $item->price; ?></td>
		                    				<td><?php echo $item->quantity; ?></td>
		                    				<td><?php echo $this->session->userdata("admin")["currency_symbol"]; ?><?php echo $item->price * $item->quantity; ?></td>
		                    				<td>
		                    					<a href="<?php echo base_url(); ?>admin/schoolstore/delete_item_from_cart/<?php echo $item->cart_id; ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
		                                                            <i class="fa fa-remove"></i>
		                                        </a>
		                                    </td>
		                    			</tr>
		                    		<?php $total += $item->price * $item->quantity; } ?>
		                    		<tr>
		                    			<td colspan="6">
		                    				<input type="hidden" name="item_ids" value="<?php echo $item_ids; ?>" />
		                    				<input type="hidden" name="quantities" value="<?php echo $quantities; ?>" />
		                    				<div class="col-md-4 col-md-offset-8">
			                    				<div class="form-group">
			                    					<label>Order By</label>
			                    					<select class="form-control" name="order_by">
			                    						<?php foreach($parents as $p) { ?>
			                    							<option value="<?php echo $p->id.','.$p->childs; ?>"><?php echo $p->username ." (". $p->guardian_name . ")"; ?></option>
			                    						<?php } ?>
			                    					</select>
			                    				</div>
		                    				</div>

		                    				<div class="col-md-4 col-md-offset-8">
			                    				<div class="form-group">
			                    					<label>Amount Paid</label>
			                    					<input type="number" name="amount_paid" class="form-control" value="<?php echo $total; ?>"/> 
			                    				</div>
		                    				</div>

		                    				<!--<div class="col-md-4 col-md-offset-8">
			                    				<div class="form-group">
			                    					<label>Status</label>
			                    					<select class="form-control" name="status">
			                    						<option value="Collected">Collected</option>
			                    						<option value="Not-Collected">Not Collected</option>
			                    						<option value="Partial">Partial</option>
			                    					</select>
			                    				</div>
		                    				</div>-->

		                    				<div class="col-md-4 col-md-offset-8">
			                    				<div class="form-group">
			                    					<label>Order Datetime</label>
			                    					<input type="text" name="created_at" class="form-control mydatepicker-autoclose" /> 
			                    				</div>
		                    				</div>

		                    			</td>
		                    		</tr>
		                    		<tr>
		                    			<th colspan="5" style="text-align: right;"><h4>Total</h4></th>
		                    			<td colspan="1">
		                    				<h4 style="display: inline-block; margin-right: 15px;"><?php echo $this->session->userdata("admin")["currency_symbol"]; ?><?php echo $total; ?></h4>
		                    				<button type="submit" class="btn btn-primary"> <?php echo $this->lang->line('sale_now'); ?> </button> 
		                    			</td>
		                    		</tr>
		                    	</table>
	                    	</form>
                    	<?php } else { ?>
                    		<div class="alert alert-danger">Shopping cart is empty.</div>
                    	<?php } ?>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>