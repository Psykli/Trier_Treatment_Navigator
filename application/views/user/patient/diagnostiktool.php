<script src="<?php echo base_url(); ?>js/charts/suicideSingle.js"></script>

<div class="media bottom_spacer place_headline">
    <a class="pull-left" href="#">
        <img class="media-object" src="<?php echo base_url(); ?>/img/48x48/patient.png" data-src="holder.js/32x32">
    </a>
    <div class="media-body">
        <h1 class="media-heading">Personalisierte Behandlungsempfehlung von <?php echo $patient[0]->code; ?></h1>
    </div>
</div>

<ol class="breadcrumb">
    <li><a href="<?php echo base_url(); ?>index.php/<?php echo $userrole; ?>/dashboard">Meine Patientenübersicht</a></li>
    <li><?php $link = $userrole.'/patient/list_all' ?> <?php echo anchor($link, 'Patientenliste'); ?> </li>
    <li><a href="<?php echo base_url(); ?>index.php/user/patient/list/<?php echo $patient[0]->code; ?>">Patientendetails</a></li>
    <li class="active">Personalisierte Behandlungsempfehlung</li>
</ol>


<!--Navigationsleiste -->
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="#cite_note_1">Risiko</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<!-- Init Index für Graphiken -->
<?php $index = 0;?>

<!-- Suizidalität -->
<?php $graphindex = 0; ?>
<div class="panel panel-default">
	<div class="panel-heading">Risiko</div>
		<div class="panel-body">
			<?php if (isset($suicideItems[0])): ?>
				<canvas id="suicideOne" width="70" height="40"></canvas>
				<br>
				<script>
					createSingleSuicide('suicideOne',<?php echo json_encode($suicideItems[0]['PHQ009'])?>,["Gedanken, dass Sie lieber tot wären","oder sich Leid zufügen möchten (PHQ009)"],<?php echo json_encode($graphindex);?>,[3,3,3],3,{0: ['überhaupt','nicht'], 1: 'einzelne Tage', 2: ['mehr als die','Hälfte der Tage'], 3: ['beinahe','jeden Tag']},true,"Wie oft fühlten Sie sich im Verlauf der letzten 2 Wochen durch die folgenden Beschwerden beeinträchtigt?");
				</script>
				<?php $graphindex++; ?>
			<?php endif; ?>
			<?php if (isset($suicideItems[1])): ?>
				<canvas id="suicideTwo" width="100" height="70"></canvas>
				<script>
					createSingleSuicide('suicideTwo',<?php echo json_encode($suicideItems[1]['HSC010'])?>,["Gedanken an den Tod und das Sterben ? (HSC010)"],<?php echo json_encode($graphindex);?>,[3,3,3],3,{0: ['überhaupt','nicht'], 1: 'ein bisschen', 2: 'ziemlich', 3: 'sehr'},false,"Wie sehr litten Sie in den letzten sieben Tagen unter ...",);
				</script>
				<?php $graphindex++; ?>
			<?php endif; ?>
		</div>
	</div>
</div>