<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i> <?php echo $this->lang->line('item_store'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('store', 'can_add') || $this->rbac->hasPrivilege('store', 'can_edit')) { ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('edit_item_school_store'); ?></h3>
                        </div><!-- /.box-header -->
                        <!-- form start -->
                        <form action="<?php echo site_url("admin/schoolstore/edit/" . $id) ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                            <div class="box-body">
                                <?php echo validation_errors(); ?>
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"> <?php echo $this->lang->line('item'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="name" name="name" placeholder="name" type="text" class="form-control"  value="<?php echo set_value('itemstore', $schoolstore['item_store']); ?>" />
                                    <span class="text-danger"><?php echo form_error('itemstore'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('item_category'); ?></label><small class="req"> *</small>
                                    <select class="form-control" name="item_category_id" value="<?php echo set_value('itemstore', $schoolstore['item_category_id']); ?>">
                                        <?php foreach($itemcategories as $c) { ?>
                                            <option value="<?php echo $c->id; ?>"><?php echo $c->item_category; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('item_category_id'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('item_supplier'); ?></label><small class="req"> *</small>
                                    <select class="form-control" name="item_supplier_id" value="<?php echo set_value('itemstore', $schoolstore['item_supplier_id']); ?>">
                                        <?php foreach($itemsuppliers as $s) { ?>
                                            <option value="<?php echo $s->id; ?>"><?php echo $s->item_supplier; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('item_supplier_id'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1"> <?php echo $this->lang->line('item_store_code'); ?></label>
                                    <input id="code" name="code" placeholder="code" type="text" class="form-control"  value="<?php echo set_value('itemstore', $schoolstore['code']); ?>" />
                                    <span class="text-danger"><?php echo form_error('itemstore'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('image'); ?></label>
                                    <input id="picture" name="picture" placeholder="" type="file" class="form-control"  value="<?php echo set_value('itemstore', $schoolstore['picture']); ?>" style="border: 1px solid lightgray; opacity: 100;"/>
                                    <span class="text-danger"><?php echo form_error('picture'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('price'); ?></label>
                                    <input id="price" name="price" placeholder="" type="number" class="form-control"  value="<?php echo set_value('itemstore', $schoolstore['price']); ?>" />
                                    <span class="text-danger"><?php echo form_error('price'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('stock'); ?></label>
                                    <input id="stock" name="stock" placeholder="" type="number" class="form-control"  value="<?php echo set_value('itemstore', $schoolstore['stock']); ?>" />
                                    <span class="text-danger"><?php echo form_error('stock'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                                    <textarea class="form-control" id="description" name="description" placeholder="" rows="3" placeholder="Enter ..."><?php echo set_value('description', $schoolstore['description']); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('description'); ?></span>
                                </div>
                            </div><!-- /.box-body -->
                            <div class="box-footer">

                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                        </form>
                    </div>
                </div><!--/.col (right) -->
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('store', 'can_add') || $this->rbac->hasPrivilege('store', 'can_edit')) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">
                <!-- general form elements -->
                <div class="box box-primary" id="exphead">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('item_school_store_list'); ?></h3>
                    </div><!-- /.box-header -->
                    <div class="box-body  ">
                        <div class="mailbox-messages">
                            <div class="download_label"><?php echo $this->lang->line('item_school_store_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('image'); ?></th>
                                        <th><?php echo $this->lang->line('item'); ?></th>
                                        <th><?php echo $this->lang->line('item_store_code'); ?></th>
                                        <th><?php echo $this->lang->line('price'); ?></th>
                                        <th><?php echo $this->lang->line('stock'); ?></th>
                                        <th class="text-right no-print"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($schoolstorelist)) {
                                        ?>

                                        <?php
                                    } else {
                                        $count = 1;
                                        foreach ($schoolstorelist as $store) {
                                            ?>
                                            <tr>
                                                <td><img src="<?php echo base_url(); ?>uploads/schoolstore/<?php echo $store['picture'] ?>" style="width: 40px; height: 40px;"/></td>   
                                                <td class="mailbox-name">

                                                    <a href="#" data-toggle="popover" class="detail_popover" >
        <?php echo $store['item_store'] ?>
                                                    </a>

                                                    <div class="fee_detail_popover" style="display: none">
                                                        <?php
                                                        if ($store['description'] == "") {
                                                            ?>
                                                            <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <p class="text text-info"><?php echo $store['description']; ?></p>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                                <td class="mailbox-name"><?php echo $store['code'] ?></td>
                                                <td class="mailbox-name"><?php echo $this->session->userdata("admin")["currency_symbol"]; ?><?php echo $store['price'] ?></td>
                                                <td class="mailbox-name"><?php echo $store['stock'] ?></td>                                       

                                                <td class="mailbox-date pull-right no-print">
        <?php if ($this->rbac->hasPrivilege('store', 'can_edit')) { ?>
                                                        <a href="<?php echo base_url(); ?>admin/itemstore/edit/<?php echo $store['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
        <?php } if ($this->rbac->hasPrivilege('store', 'can_delete')) { ?>
                                                        <a href="<?php echo base_url(); ?>admin/itemstore/delete/<?php echo $store['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                            <i class="fa fa-remove"></i>
                                                        </a>
        <?php } ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        $count++;
                                    }
                                    ?>

                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div>


        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<script>
    $(document).ready(function () {
        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
    });
</script>
