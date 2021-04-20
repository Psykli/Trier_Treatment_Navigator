<script src="<?php echo base_url(); ?>js/charts/status.js"></script>
<script src="<?php echo base_url(); ?>js/charts/suicideSingle.js"></script>

<div class="media bottom_spacer_50px place_headline">
	<a class="pull-left" href="#">
		<img class="media-object" src="<?php echo base_url( ); ?>/img/48x48/patient.png" data-src="holder.js/32x32">
	</a>
	<div class="media-body">
		<h1 class="media-heading">Statusrückmeldung</h1>
	</div>
</div>

<!-- Navigation -->
<nav class="menu">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url( ); ?>index.php/<?php echo $userrole; ?>/dashboard">Meine Patientenübersicht</a></li>
        <li class="breadcrumb-item"><?php $link = $userrole.'/patient/list_all' ?>
            <?php echo anchor( $link, 'Patientenliste' ); ?></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url( ); ?>index.php/user/patient/list/<?php echo $patientcode; ?>">Patientendetails</a></li>
        <li class="breadcrumb-item active">Statusrückmeldung</li>
    </ol>
</nav>
<?php if( !isset( $instance ) OR !isset( $patientcode ) ): //ERROR during transmit ?>
	<div class="alert alert-danger">
		Fehlerhafte Übermittlung von Sitzung und Patientencode.
	</div>
<?php elseif( is_null( $instance ) OR is_null( $patientcode ) ): ?>
	<div class="alert alert-info">
		Noch keine Daten vorhanden.
	</div>
<?php else: ?>
    <div class="row">
        <div class="col-sm-8">
            <div class="card ">
                    <div class="card-body">
                        <b>Code: </b> <?php echo $patientcode; ?> - 
                        <b>Sitzung: </b>                        
                        <?php if( isset( $instance ) ): ?>
                            <?php echo $instance; ?>
                        <?php else: /* keine Daten vorhanden*/?>
                            kein Eintrag
                        <?php endif; ?> - 
                        <b>Datum der Sitzung: </b>                            
                        <?php if( isset( $instance_date ) ): ?>
                            <?php echo $instance_date; ?>
                        <?php else: /* keine Daten vorhanden*/?>
                            kein Eintrag
                        <?php endif; ?>
                        <hr />
                        <?php $link = $instance !== "PR-PO Vergleich" ? 'user/status/index' . $instance . '/' .$patientcode.'/status_all' : 'user/status/index/NULL/' .$patientcode.'/pr_po_status_all'; ?>			
                    </div><!-- card-body -->
                </div><!-- card  -->
            <!-- Init Index für Graphiken; wird nach jeder erstellten Graphik inkrementiert und dem Funktionsaufruf übergeben, da alle Graphiken in einem JS Object vorhanden sind; der andere Index wird für die Darstellung der Statusinformation benötigt-->
            <?php $graphindex = 0; ?>
            <?php if (isset($suicideItems[0])): ?>
			<div class="card ">
				<div class="card-header">Suizidalitäts-Items</div>
				<div class="card-body">
                    <canvas id="suicideTwo" width="100" height="70"></canvas>
                    <script>
                        createSingleSuicide('suicideTwo',<?php echo json_encode($suicideItems[0]['HSC010'])?>,["Gedanken an den Tod und das Sterben ? (HSC010)"],<?php echo json_encode($graphindex);?>,[3,3,3],3,{0: ['überhaupt','nicht'], 1: 'ein bisschen', 2: 'ziemlich', 3: 'sehr'},false,"Wie sehr litten Sie in den letzten sieben Tagen unter ...",);
                    </script>
                    <?php $graphindex++; ?>		
				</div>
			</div>
            <?php endif; ?>
            <!-- Dieser Index dient zum Referenzieren der Fragebögen im Array-->
            
            <div id="accordion3">
                <?php foreach ($means as $table => $mean): ?>
                    <?php 
                    if(array_key_exists($instance, $mean[array_keys($mean)[0]])):
                            $names = array_keys($mean);                           
						?>
                    <div class="card">
                        <div class="card-header">
                            <h5><button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse<?php echo $table;?>"><?php echo $table;?></button></h5>
                        </div>
                        <div id="collapse<?php echo $table;?>" class="collapse" data-parent="#accordion3">
                        <div class="card-body">
                                    <p>
                                        <?php echo $infos[$table][array_keys($mean)[0]]->description;?>
                                    </p>
                                    <hr/>
                                    <canvas id="<?php echo $table; ?>" width="100" height="40"></canvas>
                                    <script>
                                        createStatusGraph('<?php echo $table;?>',<?php echo json_encode($mean) ?>,<?php echo json_encode($infos[$table]) ?>,'<?php echo $instance;?>', <?php echo $graphindex++;?>);
                                    </script>
                                    <table class="table table-bordered table-striped">
                                        <caption><b>Skalen</b></caption>
                                        <tbody>
                                        <?php foreach( $mean as $scale_name => $scale ): ?>
                                            <tr>
                                                <td><?php echo $scale_name; ?></td>
                                                <td><?php echo number_format( $scale[$instance][1], 2 ); ?></td>
                                            </tr>       
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <div>
                                        <!-- Button to trigger modal -->
                                        <button class="btn btn-info" data-toggle="modal" data-target="#high_items_modal<?php echo $table; ?>">Hohe Werte Anzeigen</button>
                                    </div><!-- end:.high_items -->

                                    <div class="modal fade" id="high_items_modal<?php echo $table; ?>" tabindex="-1" role="dialog" aria-labelledby="highItemsModalLabel<?php echo $table;?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    <h4 class="modal-title" id="highItemsModalLabel<?php echo $table;?>">Hohe Werte für <?php echo $table; ?></h4>
                                                </div>
                                                <div class="modal-body">
                                                    
                                                    <?php if( sizeof($high_items[$table]) == 0): ?>
                                                        Keine hohen Werte ermittelt.
                                                    <?php else: ?>
                                                        <table class="table table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <td>Nr.</td>
                                                                    <td>Frage</td>
                                                                    <td>Eintrag</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach( $high_items[$table] as $name => $item ): ?>
                                                                    <tr>
                                                                        <?php /* parse the number and echo to a own column */ ?>
                                                                        <td><?php echo $name?></td>
                                                                        <?php /* delete the number and space. So echo text to a own column */ ?>
                                                                        <td><?php echo $item[0]; ?></td>
                                                                        <td><?php echo $item[1]; ?></td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Schließen</button>
                                                </div><!-- end:.modal-footer -->
                                            </div><!-- end:.modal.content -->
                                        </div><!-- end:.modal-dialog -->
                                    </div><!-- end:.modal -->
                                    <br/>
                                    <div>
                                        <!-- Button to trigger modal -->
                                        <button class="btn btn-info" data-toggle="modal" data-target="#all_items_modal<?php echo $table; ?>">Alle Werte Anzeigen</button>
                                    </div><!-- end:.high_items -->

                                    <div class="modal fade" id="all_items_modal<?php echo $table; ?>" tabindex="-1" role="dialog" aria-labelledby="allItemsModalLabel<?php echo $table;?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    <h4 class="modal-title" id="allItemsModalLabel<?php echo $table;?>">Alle Werte für <?php echo $table; ?></h4>
                                                </div>
                                                <div class="modal-body">
                                                    
                                                    <?php if( sizeof($items[$table]) == 0): ?>
                                                        Keine Werte vorhanden
                                                    <?php else: ?>
                                                        <table class="table table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <td>Nr.</td>
                                                                    <td>Frage</td>
                                                                    <td>Eintrag</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach( $items[$table] as $name => $item ): ?>
                                                                    <?php if(isset($item[1]) && !empty($item[1])):?>
                                                                        <tr>
                                                                            <?php /* parse the number and echo to a own column */ ?>
                                                                            <td><?php echo $name?></td>
                                                                            <?php /* delete the number and space. So echo text to a own column */ ?>
                                                                            <td><?php echo $item[0]; ?></td>
                                                                            <td><?php echo $item[1]; ?></td>
                                                                        </tr>
                                                                    <?php endif;?>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Schließen</button>
                                                </div><!-- end:.modal-footer -->
                                            </div><!-- end:.modal.content -->
                                        </div><!-- end:.modal-dialog -->
                                    </div><!-- end:.modal -->
                                </div>
                        </div>
                    </div>
                    <?php $index++; ?>
                    <?php endif;endforeach ?>
            </div><!-- card-group -->
        </div><!-- col-sm-8 -->
    </div><!-- row -->
<?php endif ?>


            
