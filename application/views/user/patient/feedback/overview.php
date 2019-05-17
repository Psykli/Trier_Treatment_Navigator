<script src="<?php echo base_url(); ?>js/charts/hsclProcess.js"></script>

<div class="media bottom_spacer">
	<a class="pull-left" href="#">
		<img class="media-object" src="<?php echo base_url(); ?>/img/48x48/patient.png" data-src="holder.js/32x32">
	</a>
	<div class="media-body">
		<h2 class="media-heading">Feedback Übersicht für Patient <?php echo $patientcode; ?></h2>
	</div>
</div>

<ol class="breadcrumb">
	<li><a href="<?php echo base_url(); ?>index.php<?php echo $userrole; ?>/dashboard"><?php echo lang('list_overview');?></a></li>
	<li><?php $link = $userrole.'/patient/list_all' ?>
		<?php echo anchor( $link, lang('list_list1') ); ?></li>
    <?php if($userrole === 'admin')
        $userrole = 'user';
    ?>
	<li><?php $link = $userrole.'/patient/list/'.$patientcode ?>
		<?php echo anchor( $link, lang('details_details2') ); ?></li>
    <li class="active">Feedback Übersicht</li>
</ol> 
<div class="col-md-8">
    <div class="panel panel-default">
        <div class="panel-heading">Allgemeiner Symptomverlauf</div>
        <div class="panel-body">
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

<div class="col-md-4">
    <?php $secondary_panel_opened = false; $counter = 0;?>
        <?php 
                echo 
               '<div class="panel panel-default">'.
                '<div class="panel-heading">Allgemeiner Symptomverlauf</div>'.
                '<div class="panel-body">';

                $img = '<img class="media-object pull-left" src="'.base_url().'/img/feedback/'.$signcolor.'.png" />'; 

                echo '<div class="media">
                    '.$img.'
                    
                    <div class="media-body"><br />
                        '.$symptomtext.'
                    </div>
                </div>';
                echo '</div></div>';?>
                    </div>
                    </div>

<div class="col-md-12">
<p>Wenn der allgemeine Symptomverlauf nicht erwartungsgemäß verläuft empfiehlt es sich auch die drei Tools zur dyadischen Synchronizität anzusehen. Diese geben Ihnen Aufschluss über die Kohärenz zwischen Ihrer Sicht und der Patientensicht zu zentralen Fragen der Symptomeinschätzung, Therapiebeziehung und des emotionalen Erlebens.</p>
</div>

<?php if(isset($process['tsb-ee1']['graph']) OR isset($process['tsb-wb']['graph'])):?>
<div class="col-md-12">

    <hr/>       


    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" href="#collapse1">
                Dyadische Synchronizität zwischen Therapeut und Patient im Bereich Symptomeinschätzung
                </a>
            </h4>
        </div>
        <div id="collapse1" class="panel-collapse collapse">
            <div class="panel-body">                    
                <p>
                    <b>Patient:</b> Wie gut sind Sie in den letzten sieben Tagen emotional und psychisch zurecht gekommen? <br/>
                    <b>Therapeut:</b> Wie gut kommt Ihr Patient emotional und psychisch zurecht?
                    <br/>
                    (hohe Werte = gutes Zurechtkommen)
                </p>
                <?php if( !isset( $process['tsb-wb']['graph'] ) ): ?>
                    <div class="alert alert-warning"> Es liegen nicht genug Daten vor um eine Grafik zu erstellen</div>
                <?php else: ?>
                    <div class="graph">
                        <img src="<?php echo base_url( $process['tsb-wb']['graph'] ); ?>" alt="Statusgraph" />
                    </div>
                <?php endif; ?>
            </div>
         </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" href="#collapse2">
                    Dyadische Synchronizität zwischen Therapeut und Patient im Bereich Prozesswahrnehmung
                </a>
            </h4>
        </div>
        <div id="collapse2" class="panel-collapse collapse">
            <div class="panel-body">      
                <?php if( !isset( $process['tsb-tb']['graph'] ) ): ?>
                    <div class="alert alert-warning"> Es liegen nicht genug Daten vor um eine Grafik zu erstellen</div>
                <?php else: ?>
                    <div class="graph">
                        <img src="<?php echo base_url( $process['tsb-tb']['graph'] ); ?>" alt="Statusgraph" />
                    </div>
                <?php endif; ?>
                <hr/>
                <?php if( !isset( $process['tsb-pa']['graph'] ) ): ?>
                    <div class="alert alert-warning"> Es liegen nicht genug Daten vor um eine Grafik zu erstellen</div>
                <?php else: ?>
                    <div class="graph">
                        <img src="<?php echo base_url( $process['tsb-pa']['graph'] ); ?>" alt="Statusgraph" />
                    </div>
                <?php endif; ?>
                <hr/>
                <?php if( !isset( $process['tsb-pbmo']['graph'] ) ): ?>
                    <div class="alert alert-warning"> Es liegen nicht genug Daten vor um eine Grafik zu erstellen</div>
                <?php else: ?>
                    <div class="graph">
                        <img src="<?php echo base_url( $process['tsb-pbmo']['graph'] ); ?>" alt="Statusgraph" />
                    </div>
                <?php endif; ?>
            </div>
        </div>
            
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" href="#collapse3">
                Dyadische Synchronizität zwischen Therapeut und Patient im Bereich emotionalen Erlebens
                </a>
            </h4>
        </div>
        <div id="collapse3" class="panel-collapse collapse">
            <div class="panel-body"> 
            <?php for($i = 1; $i <= 7; $i++):?>
                <?php $str = 'tsb-ee'.$i;?>
                <?php if( !isset( $process[$str]['graph'] ) ): ?>
                    <div class="alert alert-warning"> Es liegen nicht genug Daten vor um eine Grafik zu erstellen</div>
                <?php else: ?>
                    <p> <?php echo ($process[$str]['desc']) ; ?> </p>
                    <div class="graph">
                        <img src="<?php echo base_url( $process[$str]['graph'] ); ?>" alt="Statusgraph" />
                    </div>
                <?php endif; ?>
                <br/><br/>
            <?php endfor;?>
            </div>
        </div>
    </div>
</div>
<?php endif;?>

<script>
	$(function(){
		$('[rel="popover"]').popover({
			container: 'body',
			html: true,
			content: function () {
				var clone = $($(this).data('popover-content')).clone(true).removeClass('hide');
				return clone;
			}
		}).click(function(e) {
			e.preventDefault();
		});
	});
</script>

                    
                        
                        