<table class="table table-default">
	<tr>
		<td colspan="2" class="text-center"><?php echo $department_name; ?></td>
		<td colspan="2" class="text-center"><?php echo $staff_name; ?></td>
		<td colspan="2" class="text-center"><?php echo $delivery_time; ?></td>
	</tr>
	<tr>
		<th>Items</th>
		<th>Quality</th>
		<th>Quantity</th>
		<th>Specification</th>
		<th>Price</th>
		<th>Previous Price</th>
	</tr>	
	<?php $total_price=0; foreach($details as $d) { $total_price += $d["quantity"] * $d["price"]; ?>
		<tr>
			<td><?php echo $d["item_name"]; ?></td>
			<td><?php echo $d["quality"]; ?></td>
			<td><?php echo $d["quantity"]; ?></td>
			<td><?php echo $d["specification"]; ?></td>
			<td><?php echo $d["price"]; ?></td>
			<td><?php echo $d["previous_price"]; ?></td>
		</tr>
	<?php } ?>
	<tr>
		<td colspan="4"></td>
		<td colspan="2" class="text-center"><h4>Total Cost:- <?php echo $total_price; ?></h4></td>
	</tr>
	
		<tr>
			
			<td colspan="6" class="text-left">
				<?php foreach($approvals as $aps) { ?>
					<div style="display: inline-block; padding-right: 7.5px; padding-left: 7.5px;">
						<h3 style="margin-top:0; margin-bottom:10px;"><?php echo $aps->name; ?></h3>
						<div style="width: 300px; height: 50px; border: 1px solid; "></div>
						<br/>
						<br/>
						<label>Signature and date</label>
					</div>
				<?php } ?>
			</td>
			
		</tr>
	
</table>