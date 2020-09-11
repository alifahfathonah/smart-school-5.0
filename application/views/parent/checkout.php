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
	                    				<td><?php echo $this->session->userdata("student")["currency_symbol"]; ?><?php echo $item->price; ?></td>
	                    				<td><?php echo $item->quantity; ?></td>
	                    				<td><?php echo $this->session->userdata("student")["currency_symbol"]; ?><?php echo $item->price * $item->quantity; ?></td>
	                    				<td>
	                    					<a href="<?php echo base_url(); ?>parent/parents/delete/<?php echo $item->cart_id; ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
	                                                            <i class="fa fa-remove"></i>
	                                        </a>
	                                    </td>
	                    			</tr>
	                    		<?php $total += $item->price * $item->quantity; } ?>
	                    		<tr>
	                    			<th colspan="5">Total</th>
	                    			<td colspan="1">
	                    				<h4 style="display: inline-block; margin-right: 15px;"><?php echo $this->session->userdata("student")["currency_symbol"]; ?><?php echo $total; ?></h4>
	                    				<form style="display: inline-block;">
										   <script src="https://js.paystack.co/v1/inline.js"></script>
										   <script>
											   	function payWithPaystack(){
											    	var handler = PaystackPop.setup({
											        	key: '<?php echo $payment_settings[0]->api_secret_key; ?>',
											        	email: '<?php echo $payment_settings[0]->api_email; ?>',
											        	amount: <?php echo $total * 100; ?>,
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
											        		var formData = {};
											        		formData.item_ids = '<?php echo $item_ids; ?>';
											        		formData.quantities = '<?php echo $quantities; ?>';
											        		formData.amount_paid = <?php echo $total; ?>;
											        		formData.reference_no = response.reference;
											        		$.ajax({
											        			url: '<?php echo site_url("parent/parents/placeorder"); ?>',
											        			method: "POST",
											        			data: formData,
											        			dataType: "json",
											        			success: function(success){
											        				$(".alert").html(success.message);
											        				$(".alert").removeClass("hide");
											        				setTimeout(function(){
											        					window.location.href = '<?php echo base_url(); ?>parent/parents/store';
											        				},2000);
											        			},
											        			error: function(error){
											        				console.log(error.data);
											        			}
											        		});
											      		},
											      		onClose: function(){
											          		alert('window closed');
											      		}
											    	});
											    	handler.openIframe();
											  	}
											</script>
										    <button type="button" class="btn btn-primary" onclick="payWithPaystack()"> <?php echo $this->lang->line('pay'); ?> </button> 
										</form>
	                    			</td>
	                    		</tr>
	                    	</table>
                    	<?php } else { ?>
                    		<div class="alert alert-danger">Shopping cart is empty.</div>
                    	<?php } ?>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>