<script src="<?php echo base_url(); ?>js/charts/suicideSingle.js"></script>

<div class="media bottom_spacer_50px place_headline">
    <a class="pull-left" href="#">
        <img class="media-object" src="<?php echo base_url(); ?>/img/48x48/patient.png" data-src="holder.js/32x32">
    </a>
    <div class="media-body">
        <h1 class="media-heading">Personalisierte Behandlungsempfehlung von <?php echo $patientcode; ?></h1>
    </div>
</div>

<nav class="menu">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="<?php echo base_url(); ?>index.php/<?php echo $userrole; ?>/dashboard">Meine Patientenübersicht</a></li>
		<li class="breadcrumb-item"><?php $link = $userrole.'/patient/list_all' ?> <?php echo anchor($link, 'Patientenliste'); ?> </li>
		<li class="breadcrumb-item"><a href="<?php echo base_url(); ?>index.php/user/patient/list/<?php echo $patientcode; ?>">Patientendetails</a></li>
		<li class="breadcrumb-item active">Personalisierte Behandlungsempfehlung</li>
	</ol>
</nav>


<!--Navigationsleiste -->
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="nav-item"><a href="#cite_note_1">Risiko</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<!-- Init Index für Graphiken -->
<?php $index = 0;?>

<!-- Suizidalität -->
<?php $graphindex = 0; ?>
<div class="card ">
	<div class="card-header">Risiko</div>
		<div class="card-body">
		<div class="col-md-10">
			<?php if (isset($suicideItems[0])): ?>
				<canvas id="suicideTwo" width="100" height="70"></canvas>
				<script>
					createSingleSuicide('suicideTwo',<?php echo json_encode($suicideItems[0]['HSC010'])?>,["Gedanken an den Tod und das Sterben ? (HSC010)"],<?php echo json_encode($graphindex);?>,[3,3,3],3,{0: ['überhaupt','nicht'], 1: 'ein bisschen', 2: 'ziemlich', 3: 'sehr'},false,"Wie sehr litten Sie in den letzten sieben Tagen unter ...",);
				</script>
				<?php $graphindex++; ?>
			<?php endif; ?>
			</div>
		</div>
	</div>
</div>