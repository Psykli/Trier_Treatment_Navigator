<script src="<?php echo base_url(); ?>js/charts/hsclProcess.js"></script>
<script src="<?php echo base_url(); ?>js/charts/compare.js"></script>

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


<div class="col-md-12">
<p>Wenn der allgemeine Symptomverlauf nicht erwartungsgemäß verläuft empfiehlt es sich auch die drei Tools zur dyadischen Synchronizität anzusehen. Diese geben Ihnen Aufschluss über die Kohärenz zwischen Ihrer Sicht und der Patientensicht zu zentralen Fragen der Symptomeinschätzung, Therapiebeziehung und des emotionalen Erlebens.</p>
</div>

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
                    <?php 
                        $tables = array_keys($means['Symptomeinschätzung']);
                    ?>
                    <div class="graph">
                    <canvas id="Symptomeinschätzung" width="680px" height="400px"></canvas>
                    
                    <script>
                        createLineChart("Symptomeinschätzung","Symptomeinschätzung", <?php echo json_encode($means['Symptomeinschätzung'][$tables[0]]);?>,
                            <?php echo json_encode($means['Symptomeinschätzung'][$tables[1]]);?>,<?php echo json_encode($infos['Symptomeinschätzung']);?>);
                    </script>
                    </div>
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
                    <?php 
                        $tables = array_keys($means['TSB Therapiebeziehung']);
                    ?>
                    <div class="graph">
                    <canvas id="TSB_Therapiebeziehung" width="680px" height="400px"></canvas>
                    
                    <script>
                        createLineChart("TSB_Therapiebeziehung","TSB Therapiebeziehung", <?php echo json_encode($means['TSB Therapiebeziehung'][$tables[0]]);?>,
                            <?php echo json_encode($means['TSB Therapiebeziehung'][$tables[1]]);?>,<?php echo json_encode($infos['TSB Therapiebeziehung']);?>);
                    </script>
                    </div>
                <hr/>
                    <?php 
                        $tables = array_keys($means['TSB Problemaktualisierung']);
                    ?>
                    <div class="graph">
                    <canvas id="TSB_Problemaktualisierung" width="680px" height="400px"></canvas>
                    
                    <script>
                        createLineChart("TSB_Problemaktualisierung","TSB Problemaktualisierung", <?php echo json_encode($means['TSB Problemaktualisierung'][$tables[0]]);?>,
                            <?php echo json_encode($means['TSB Problemaktualisierung'][$tables[1]]);?>,<?php echo json_encode($infos['TSB Problemaktualisierung']);?>);
                    </script>
                    </div>
                <hr/>
                    <?php 
                        $tables = array_keys($means['TSB Korrektive Erfahrungen (Bewältigung + Klärung)']);
                    ?>
                    <div class="graph">
                    <canvas id="TSB_Korrektive Erfahrungen_Bewältigung_Klärung" width="680px" height="400px"></canvas>
                    
                    <script>
                        createLineChart("TSB_Korrektive Erfahrungen_Bewältigung_Klärung","TSB Korrektive Erfahrungen (Bewältigung + Klärung)", <?php echo json_encode($means['TSB Korrektive Erfahrungen (Bewältigung + Klärung)'][$tables[0]]);?>,
                            <?php echo json_encode($means['TSB Korrektive Erfahrungen (Bewältigung + Klärung)'][$tables[1]]);?>,<?php echo json_encode($infos['TSB Korrektive Erfahrungen (Bewältigung + Klärung)']);?>);
                    </script>
                    </div>
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
                    <?php 
                        $tables = array_keys($means['Traurig']);
                    ?>
                    <div class="graph">
                        <canvas id="Traurig" width="680px" height="400px"></canvas>
                        
                        <script>
                            createLineChart("Traurig","Traurig", <?php echo json_encode($means['Traurig'][$tables[0]]);?>,
                                <?php echo json_encode($means['Traurig'][$tables[1]]);?>,<?php echo json_encode($infos['Traurig']);?>);
                        </script>
                    </div>
                    <hr/>
                    <?php 
                        $tables = array_keys($means['Beschämt']);
                    ?>
                    <div class="graph">
                        <canvas id="Beschämt" width="680px" height="400px"></canvas>
                        
                        <script>
                            createLineChart("Beschämt","Beschämt", <?php echo json_encode($means['Beschämt'][$tables[0]]);?>,
                                <?php echo json_encode($means['Beschämt'][$tables[1]]);?>,<?php echo json_encode($infos['Beschämt']);?>);
                        </script>
                    </div>
                    <hr/>
                    <?php 
                        $tables = array_keys($means['Ängstlich']);
                    ?>
                    <div class="graph">
                        <canvas id="Ängstlich" width="680px" height="400px"></canvas>
                        
                        <script>
                            createLineChart("Ängstlich","Ängstlich", <?php echo json_encode($means['Ängstlich'][$tables[0]]);?>,
                                <?php echo json_encode($means['Ängstlich'][$tables[1]]);?>,<?php echo json_encode($infos['Ängstlich']);?>);
                        </script>
                    </div>
                    <hr/>
                    <?php 
                        $tables = array_keys($means['Wütend']);
                    ?>
                    <div class="graph">
                        <canvas id="Wütend" width="680px" height="400px"></canvas>
                        
                        <script>
                            createLineChart("Wütend","Wütend", <?php echo json_encode($means['Wütend'][$tables[0]]);?>,
                                <?php echo json_encode($means['Wütend'][$tables[1]]);?>,<?php echo json_encode($infos['Wütend']);?>);
                        </script>
                    </div>
                    <hr/>
                    <?php 
                        $tables = array_keys($means['Zufrieden']);
                    ?>
                    <div class="graph">
                        <canvas id="Zufrieden" width="680px" height="400px"></canvas>
                        
                        <script>
                            createLineChart("Zufrieden","Zufrieden", <?php echo json_encode($means['Zufrieden'][$tables[0]]);?>,
                                <?php echo json_encode($means['Zufrieden'][$tables[1]]);?>,<?php echo json_encode($infos['Zufrieden']);?>);
                        </script>
                    </div>
                    <hr/>
                    <?php 
                        $tables = array_keys($means['Energiegeladen']);
                    ?>
                    <div class="graph">
                        <canvas id="Energiegeladen" width="680px" height="400px"></canvas>
                        
                        <script>
                            createLineChart("Energiegeladen","Energiegeladen", <?php echo json_encode($means['Energiegeladen'][$tables[0]]);?>,
                                <?php echo json_encode($means['Energiegeladen'][$tables[1]]);?>,<?php echo json_encode($infos['Energiegeladen']);?>);
                        </script>
                    </div>
                    <hr/>
                    <?php 
                        $tables = array_keys($means['Entspannt']);
                    ?>
                    <div class="graph">
                        <canvas id="Entspannt" width="680px" height="400px"></canvas>
                        
                        <script>
                            createLineChart("Entspannt","Entspannt", <?php echo json_encode($means['Entspannt'][$tables[0]]);?>,
                                <?php echo json_encode($means['Entspannt'][$tables[1]]);?>,<?php echo json_encode($infos['Entspannt']);?>);
                        </script>
                    </div>
            </div>
        </div>
    </div>
</div>
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

                    
                        
                        