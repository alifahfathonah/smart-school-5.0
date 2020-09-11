<html>
    <head>
        <title>Print Out</title>
        <base href = "<?php echo base_url(); ?>" />
        <link href="assets/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container" style="margin-left: 20%; width: 60%;">
            <div class="row">
                <div class="col-md-6">
                    <b>Form:</b>
                    <span><?= $template->name; ?></span>
                </div>
                <div class="col-md-6">
                    <b>Category:</b>
                    <span><?= $template->form_category; ?></span>
                    <a href="javascript:onclick=window.print();" class="btn btn-info"><i class="fa fa-print"></i> Print</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?= $html; ?>
                </div>
            </div>
        </div>
    </body>
</html>