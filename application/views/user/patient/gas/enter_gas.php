<div class="media bottom_spacer place_headline">
    <a class="pull-left" href="#">
        <img class="media-object" src="<?php echo base_url(); ?>/img/48x48/patient.png" data-src="holder.js/32x32">
    </a>
    <div class="media-body">
        <h1 class="media-heading">GAS freischalten für <?php echo $patientcode; ?></h1>
    </div>
</div>

<ol class="breadcrumb">
    <li><a href="<?php echo base_url(); ?>index.php/<?php echo $userrole; ?>/dashboard">Meine Patientenübersicht</a></li>
    <li><?php $link = $userrole.'/patient/list_all' ?> <?php echo anchor($link, 'Patientenliste'); ?> </li>
    <li><a href="<?php echo base_url(); ?>index.php/user/patient/list/<?php echo $patientcode; ?>">Patientendetails</a></li>
    <li class="active">GAS-Tool: Freischalten</li>
</ol>

<div class="container">   
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class=panel-title>Bereits freigeschaltete GAS</h3>
        </div>
        <div class="panel-body">
            <ul>
                <?php foreach ($gas as $key => $entry): ?>
                    <li><?php echo $entry->INSTANCE ?> <?php if($this -> Gas_Model -> is_predecessor_filled($patientcode, $entry -> INSTANCE, $username)): ?> <a href="<?php echo base_url(); ?>index.php/user/gas_tool/fill_gas/<?php echo $patientcode?>/<?php echo $entry->INSTANCE; ?>" class="btn btn-xs btn-primary glyphicon glyphicon-edit" data-toggle="tooltip" title="Fill GAS"></a>
                    <?php if(isset($entry->GASDAT)): ?>
                        <span class="label label-success"> Wurde am <?php echo $entry->GASDAT; ?> ausgefüllt. </span>
                    <?php else: ?>
                        <span class="label label-info"> Noch nicht ausgefüllt. </span>
                    <?php endif; ?>
                    <?php else:?> 
                        <span class="label label-default"> Bitte erst den Vorgänger ausfüllen. </span>
                    <?php endif; ?>
                        <span data-toggle="tooltip" title="Delete GAS">
                            <button type="button" class="btn btn-xs btn-danger glyphicon glyphicon-remove" data-toggle="modal" data-target="#delete<?php echo $key;?>"></button>
                        </span>
                    </li>

                    <div id="delete<?php echo $key;?>" class="modal fade" tabindex="-1" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Löschen des GAS-Eintrags</h4>
                            </div>
                                <div class="modal-body">														
                                    <p> Möchten Sie den Eintrag <?php echo $entry->INSTANCE; ?> wirklich löschen?                
                                </div>
                                
                                <div class="modal-footer">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                                        <a href="<?php echo base_url(); ?>index.php/user/gas_tool/delete_gas/<?php echo $patientcode?>/<?php echo $entry->INSTANCE; ?>" class="btn btn-danger">Löschen</a>
                                    </div>
                                </div>
                            </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                <?php endforeach;?>
            </ul>
        </div>
    </div>
    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#instance_confirm">Neuen GAS freischalten</button>

    <div id="instance_confirm" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Auswahl der Instanz</h4>
            </div>
            <?php echo form_open( 'user/gas_tool/activate_gas/'.$patientcode , array('role' => 'form', 'class' => 'instance_input', 'id' => 'modal_form') ); ?>
                <div class="modal-body">	
                    <div class="form-group">		
                            <label for="prefix"> Instanz Präfix: </label>
                            <input id="prefix" name="prefix" type="text" class="form-control" placeholder="Instanz" value="Z">
                    </div> 

                    <div class="form-group">		
                            <label for="instance"> Instanz Nummer: </label>
                            <input id="instance" name="instance" type="number" class="form-control" placeholder="Instanz" value="<?php echo $instanceZ; ?>">
                    </div>                       
                </div>
                
                <div class="modal-footer">
                    <div class="form-group">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                        <button type="submit" class="btn btn-primary">GAS speichern</button>
                    </div>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    

</div>