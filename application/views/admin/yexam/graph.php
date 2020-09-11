<script src="<?php echo base_url(); ?>backend/custom/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.canvasjs.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jQuery.print.js"></script>
<script>
    var chart = new CanvasJS.Chart("chartContainer", {
		exportFileName:"CanvasJS",
		animationEnabled: true,
		theme: "light2",
		title:{
			text: ""              
		},
		axisY: {
            title: "Scores",
            suffix: "",
            includeZero: false
        },
        axisX: {
            title: "Subjects"
        },
		data: [{
			type: "column",
			dataPoints: <?php echo json_encode($graphArray); ?>
		}]
	});
	chart.render();
	//$('.canvasjs-chart-toolbar div>:nth-child(2)').click();
	$(document).ready(function(){
 		setTimeout(function(){
 			var canvas = $("#chartContainer .canvasjs-chart-canvas").get(0);
    		var dataURL = canvas.toDataURL('image/jpeg');
    		
    		$.ajax({
	            type: "POST",
	            url: base_url + "admin/Examination/uploadGraph",
	            data: { "image": dataURL, "std_id": <?php echo $student_id; ?> },
	            dataType: "json",
	            success: function (data) {
	                

	            }
	        });

    		/*console.log(dataURL);
 			chart.exportChart({
	 			format: "jpg"
	 		});*/
 		}, 0);
 		
 	});
 	

    /*var options = {
    	exportEnabled: true,
    	exportChart: {format: "jpg"},
        animationEnabled: true,
        title: {
            text: ""
        },
        axisY: {
            title: "Scores",
            suffix: "%",
            includeZero: false
        },
        axisX: {
            title: "Subjects"
        },
        data: [{
            type: "column",
            yValueFormatString: "#,##0.0#"%"",
            dataPoints: <?php echo json_encode($graphArray); ?>
        }]
    };
    $("#chartContainer").CanvasJSChart(options);*/
</script>
<div id="chartContainer" style="height: 300px; width: 100%;"></div>