<script src="<?php echo base_url(); ?>js/charts/hsclProcess.js"></script>
<script src="<?php echo base_url(); ?>js/charts/compare.js"></script>

<div class="media bottom_spacer_50px">
	<a class="pull-left" href="#">
		<img class="media-object" src="<?php echo base_url(); ?>/img/48x48/patient.png" data-src="holder.js/32x32">
	</a>
	<div class="media-body">
		<h2 class="media-heading">Feedback Übersicht für Patient <?php echo $patientcode; ?></h2>
	</div>
</div>

<nav class="menu">
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="<?php echo base_url(); ?>index.php/<?php echo $userrole; ?>/dashboard"><?php echo lang('list_overview');?></a></li>
	<li class="breadcrumb-item"><?php $link = $userrole.'/patient/list_all' ?>
		<?php echo anchor( $link, lang('list_list1') ); ?></li>
    <?php if($userrole === 'admin')
        $userrole = 'user';
    ?>
	<li class="breadcrumb-item"><?php $link = $userrole.'/patient/list/'.$patientcode ?>
		<?php echo anchor( $link, lang('details_details2') ); ?></li>
    <li class="breadcrumb-item active">Feedback Übersicht</li>
</ol> 
</nav>
<div class="row">
<div class="col-md-8">
    <div class="card ">
        <div class="card-header">Allgemeiner Symptomverlauf</div>
        <div class="card-body">
            <!-- Einzeichnen der Grafik -->
            <canvas id="HsclProcess" width="680px" height="400px"></canvas>
            <script>
                createHsclChart("HsclProcess","TEST", <?php echo json_encode($hsclData['MEANS']);?>,
                <?php echo json_encode($hsclData['INSTANCES']);?>,<?php echo json_encode($hsclData['BOUNDARIES']);?>,
                <?php echo json_encode($hsclData['EXPECTED']);?>);
            </script>
        </div>
    </div>
</div>
<script>
	$(function(){
		$('[rel="popover"]').popover({
			container: 'body',
			html: true,
			content: function () {
				var clone = $($(this).data('popover-content')).clone(true).removeClass('sr-only');
				return clone;
			}
		}).click(function(e) {
			e.preventDefault();
		});
	});
</script>

                    
                        
                        