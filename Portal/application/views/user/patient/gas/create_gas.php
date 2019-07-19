<div class="media bottom_spacer place_headline">
    <a class="pull-left" href="#">
        <img class="media-object" src="<?php echo base_url(); ?>/img/48x48/patient.png" data-src="holder.js/32x32">
    </a>
    <div class="media-body">
        <h1 class="media-heading">GAS-Tool <?php echo $patientcode; ?></h1>
    </div>
</div>

<ol class="breadcrumb">
    <li><a href="<?php echo base_url(); ?>index.php/<?php echo $userrole; ?>/dashboard">Meine Patientenübersicht</a></li>
    <li><?php $link = $userrole.'/patient/list_all' ?> <?php echo anchor($link, 'Patientenliste'); ?> </li>
    <li><a href="<?php echo base_url(); ?>index.php/user/patient/list/<?php echo $patientcode; ?>">Patientendetails</a></li>
    <li class="active">GAS-Tool</li>
</ol>

<div class="container">    


    <?php if(!empty($missing)): ?>
    <div class="alert alert-warning" role="alert">
        Es fehlen einige Einträge. Bitte korrigieren Sie die markierten Zielbereiche.
    </div>
    <?php endif; ?>

    <?php if($no_entries): ?>
    <div class="alert alert-warning" role="alert">
        Es muss mindestens ein Zielbereich eingegeben werden.
    </div>
    <?php endif; ?>
    <div class="col-sm-2">
        <?php echo anchor( 'user/gas_tool/download_gas/'.$patientcode, 'PDF erzeugen', array( 'class' => 'btn btn-block btn-success' ) ) ; ?>
    </div>
    <br/><br/><br/>
    <div id="table_size" class="col-md-12" style="margin-bottom:15px; margin-top:15px;">    

    <div>
        Bereiche wechseln: <button disabled id="section_left" class="toggle-vis btn btn-sm btn-primary" data-column="left"><span class="glyphicon glyphicon-backward"></span></button> - <button id="section_right" class="toggle-vis btn btn-sm btn-primary" data-column="right"><span class="glyphicon glyphicon-forward"></span></button>
    </div> 
    <?php echo form_open( 'user/gas_tool/set_gas/'.$patientcode,  array('role' => 'form' ) ); ?>
        <table class="table table-bordered" id="myTable" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th width="1%"></th>
                    <script>
                        function activate_section(i){
                            if($('#bereich'+i).val() !== ""){
                                $('#s_'+i+'_4').removeClass('hidden');
                                $('#s_'+i+'_0').removeClass('hidden');
                                $('#s_'+i+'_-2').removeClass('hidden');
                            } else {
                                $('#s_'+i+'_4').addClass('hidden');
                                $('#s_'+i+'_0').addClass('hidden');
                                $('#s_'+i+'_-2').addClass('hidden');
                            }
                            section_warning(i);
                        }

                        function section_warning(i){
                            var section_empty = true;
                            for (var k = 4; k >= -2; k--){
                                section_empty = $('#zielsetzung'+i+k).val() === "";
                                if(!section_empty)
                                    break;
                            }
                            if($('#bereich'+i).val() === "" && !section_empty){
                                $('#b_warning_'+i).removeClass('hidden');
                            } else {
                                $('#b_warning_'+i).addClass('hidden');
                            }
                        }
                    </script>
                    <?php for ($i=1; $i <= 10; $i++): ?>
                        <th>
                            Zielbereich <?php echo $i; ?>
                            <?php if($missing_bereiche[$i] == true): ?>
                                <span class="label label-warning"> In diesem Bereich fehlen Einträge </span>
                            <?php endif; ?>
                            <br/> 
                            <textarea <?php if($immutable) echo 'readonly'; ?> cols="40" rows="3" class="form-control" id="bereich<?php echo $i; ?>" name="bereich<?php echo $i; ?>" placeholder="Zielbereich" onchange="activate_section(<?php echo $i;?>);"><?php echo (isset($bereiche) AND isset($bereiche[$i-1]))?$bereiche[$i-1]:""; ?></textarea>
                            <span class="label label-warning hidden" id="b_warning_<?php echo $i; ?>">Der Zielbereich muss ausgefüllt werden</span>
                        </th>
                    <?php endfor;?>
                </tr>
            </thead>
            <tbody>
            <?php $count = 0; ?>
                <?php for ($k = 4; $k >= -2; $k--): ?>
                    <tr>
                    <td><b><?php echo $k>0 ? '+'.$k : $k; ?></b></td>
                    <?php for ($i=1; $i <= 10; $i++): ?>
                        <td>
                        <?php $index = $count + ($i-1)*7; ?>
                            <textarea <?php if($immutable) echo 'readonly'; ?> cols="50" rows="3" class="form-control" id="zielsetzung<?php echo $i; ?><?php echo $k; ?>" name="zielsetzung<?php echo $i; ?><?php echo $k; ?>" placeholder="Zielsetzung" onchange="section_warning(<?php echo $i;?>);"><?php echo (isset($stufen) AND isset($stufen[$index]))?$stufen[$index]:""; ?></textarea>
                            <?php if($missing['ziel_'.$i.'_'.$k] == true): ?>
                                </br>
                                <span class="label label-warning"> Bitte hier ein Ziel eingeben </span>
                            <?php elseif($k == 4 OR $k == 0 OR $k == -2): ?>
                                </br>
                                <span id="<?php echo 's_'.$i.'_'.$k; ?>" class="label label-info <?php if(empty($bereiche[$i-1])) echo 'hidden';?>"> Dieses Feld ist verpflichtend für einen Bereich</span>
                            <?php endif; ?>
                        </td>
                    <?php endfor;?>
                    <?php $count++;?>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
        <script>
            function reactivate_columns(){
                for(var j = 1; j <= 10; j++){
                    var column = table.column(j); 
                    column.visible( true );
                }
            }
        </script>
        <?php if(!$immutable): ?>
            <button type="submit" name="submit" value="mutable" class="btn btn-default" onclick="reactivate_columns();">Eintragen</button>
            <button type="submit" name="submit" value="immutable" class="btn btn-default" onclick="reactivate_columns();">Endgültig eintragen</button>           
            <span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="right" 
            title="Beim Klick auf 'Endgültig eintragen' wird es für Sie nicht mehr möglich sein die Einträge zu ändern. 
            'Eintragen' lässt spätere Änderungen zu. Um keine Diskrepanzen in den Daten zu erlauben,
             sollten Sie den Fragebogen bis zum ersten Ausfüllen endgültig eingetragen haben."></span>
        <?php endif; ?>
    </form>
</div>

<script>
var table = null;
var section = 1;
var max_sections = 4;
$(document).ready(function() {
    
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    table = $('#myTable').DataTable( {
        "paging": false,
        dom: '<t>',"bSort" : false
        
    } );
 
    for(var j = 1; j <= 10; j++){
        var column = table.column(j); 
        if(j > 3){
            column.visible( false );
        }
    }

    $('button.toggle-vis').on( 'click', function (e) {
        e.preventDefault();
        
        
        // Get the column API object
        var direction = $(this).attr('data-column');
        if(direction === 'left')
            section--;
        else
            section++;
        
        var visible_columns = 0;
        for (var i = 1; i <= 10; i++){
            // Toggle the visibility
            var column = table.column(i); 
            var contained = false;
            if(Math.ceil((i+0.0) / 3.0) == section)
                contained = true;
            
            if(contained){
                column.visible( true );
                visible_columns++;
            }
            else{
                column.visible( false );
            }
        }

        if(section == max_sections){
            $('#section_right').prop('disabled', true);
            $('#myTable').width('50%');
        } else {
            $('#section_right').prop('disabled', false);
            $('#myTable').width('100%');
        }

        if(section == 1){
            $('#section_left').prop('disabled', true);
        } else {
            $('#section_left').prop('disabled', false);
        }
 
        
    } );
} );


</script>