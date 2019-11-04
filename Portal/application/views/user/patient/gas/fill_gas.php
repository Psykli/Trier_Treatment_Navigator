<?php if(!$sb): ?>
<div class="media bottom_spacer_50px place_headline">
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
    <li><a href="<?php echo base_url(); ?>index.php/user/gas_tool/enter_gas/<?php echo $patientcode; ?>">GAS Freischalten</a></li>
    <li class="active">GAS Ausfüllen</li>
</ol>
<?php endif; ?>

<div class="container">    
<?php if($immutable AND !$sb): ?>
        <div class="alert alert-info" role="alert">Der Fragebogen wurde bereits ausgefüllt. Hier sehen Sie die Ergebnisse.</div>
<?php endif; ?>
<div id="table_size" style="margin-bottom:15px; margin-top:15px;"class="col-md-12">
    
    <?php if($not_filled): ?>
        <div class="alert alert-warning" role="alert">Bitte füllen Sie alle Bereiche des Fragebogens aus</div>
    <?php endif; ?>

    <?php
    $u = 0;
    $o = 5;
    $section = floor($o / 6) + 1;
    if($not_filled){
        $keys = array_keys($filled_values);
        $oCol = $keys[sizeof($filled_values)-1];
        $oNum = intval(substr($oCol, -1));
        //oNum cant be zero, but since it only takes the last digit of the string a zero means 10
        $oNum = $oNum == 0 ? 10 : $oNum;
        //Find the lowest column that wasn't filled out
        for($i = 0; $i < sizeof($bereiche); $i++){
            if($keys[$i] != 'col'.($i+1)){
                $oNum = $i+1;
                break;
            }
        } 
        //Find the section of the column and set the upper($o) and lower($u) boundary
        $section = ceil($oNum / 3);
        //example: section = 1, 6 columns per section $o = 6, then substract 1 since array starts at 0. 19 is the last possible column
        $o = min($section * 6 - 1, sizeof($bereiche) * 2 - 1 );

        //we need the modulo here since the last section can have less than 6 columns
        $u = $o % 6 == 0 ? $o - 6 : $o - ($o % 6);
    }    
    $max_sections = ceil(sizeof($bereiche) / 3.0);
    $size = $section != $max_sections ? 3 : sizeof($bereiche) % 3;
    ?>
    <div>
        Bereiche wechseln: <button <?php if(!$not_filled OR $u == 0) echo 'disabled';?> id="section_left" class="toggle-vis btn btn-sm btn-primary" data-column="left"><span class="fas fa-backward"></span></button> - <button <?php if($section == $max_sections) echo 'disabled';?> id="section_right" class="toggle-vis btn btn-sm btn-primary" data-column="right"><span class="fas fa-forward"></span></button>
    </div>    
    <?php echo form_open( 'user/gas_tool/save_gas/'.$patientcode.'/'.$instance.'?bereiche='.sizeof($bereiche).'&sb='.$sb,  array('role' => 'form' ) ); ?>
        <table class="table table-bordered" id="myTable" cellspacing="0" width="<?php switch ($size) {
            case '2':
                echo '75%';
                break;
            case '1':
                echo '50%';
                break;
            default:
                echo '100%';
                break;
        }?>">
            <thead>
                <tr>
                    <?php for ($i=1; $i <= sizeof($bereiche); $i++): ?>
                        <th class="max <?php if($not_filled AND !isset($filled_values['col'.$i])) echo 'warning';?>" style="width:15%;">
                            <span><?php echo $bereiche[$i-1]; ?></span>
                        </th>
                        <th class="min <?php if($not_filled AND !isset($filled_values['col'.$i])) echo 'warning';?>" style="width:1%;"></th>
                    <?php endfor;?>
                </tr>
            </thead>
            <tbody>
            <?php $count = 0; ?>
                <?php for ($k = 4; $k >= -2; $k--): ?>
                    <tr>
                    <?php for ($i=1; $i <= sizeof($bereiche); $i++): ?>
                        <td>
                        <?php $index = $count + ($i-1)*7; ?>
                        <?php 
                              $length = 100;
                              $short = substr($stufen[$index],0,$length);
                              $lid = 'label_col'.$i.$k;
                        ?>
                            <label style="font-weight: normal !important;" id="<?php echo $lid; ?>" for="<?php echo 'col'.$i.$k; ?>" style="display:block;"><?php echo $short; if(strlen($stufen[$index]) > strlen($short)){ 
echo '...<a onclick=" displayAll(\''.$lid.'\',\''.str_replace(array("\r\n", "\r", "\n"), "<br />",addslashes($stufen[$index])).'\',\''.str_replace(array("\r\n", "\r", "\n"), "<br />",addslashes($short)).'\');"> mehr Anzeigen</a>';} ?> &#8192; </label>
                        </td>
                        <td>
                            <input <?php if($immutable AND !$sb) echo 'disabled'; ?> class="gas_buttons" type="radio" name="<?php echo 'col'.$i; ?>" id ="<?php echo 'col'.$i.$k; ?>" value="<?php echo $k;?>" <?php if(isset($filled_values['col'.$i]) AND $filled_values['col'.$i] == $k) echo 'checked'; ?>>
                            <label for="<?php echo 'col'.$i.$k; ?>"><?php echo $k>0 ? '+'.$k : $k; ?></label>
                        </td>
                        
                    <?php endfor;?>
                    <?php $count++;?>
                    </tr>
                    <?php if($k-0.5 > -2.5): ?>
                    <tr>
                    <?php for ($i=1; $i <= sizeof($bereiche); $i++): ?>
                        <td>
                            <label for="<?php echo 'col'.$i.($k-0.5); ?>" style="display:block;">&#8192;</label>
                        </td>
                        <td>
                            <input <?php if($immutable AND !$sb) echo 'disabled'; ?> class="gas_buttons" type="radio" name="<?php echo 'col'.$i; ?>" id ="<?php echo 'col'.$i.($k-0.5); ?>" value="<?php echo ($k-0.5);?>" <?php if(isset($filled_values['col'.$i]) AND $filled_values['col'.$i] == $k-0.5) echo 'checked'; ?>>
                            <label for="<?php echo 'col'.$i.($k-0.5); ?>"><?php echo ($k-0.5)>0 ? '+'.($k-0.5) : ($k-0.5); ?></label>
                        </td>
                        
                    <?php endfor;?>
                    </tr>
                    <?php endif; ?>
                <?php endfor; ?>
            </tbody>
        </table>
        <script>
            function reactivate_columns(){
                table.columns().every(function(index){
                    var column = table.column(index);
                    column.visible(true);
                });
            }
        </script>
        <?php if(!$immutable OR $sb): ?>
            <button type="submit" class="btn btn-outline-secondary" onclick="reactivate_columns();">Eintragen</button>
        <?php endif; ?>
    </form>
</div>
</div>

<script>
var table = null;
var section = <?php echo $section;?>;
var max_sections = <?php echo $max_sections; ?>;
$(document).ready(function() {
    
    table = $('#myTable').DataTable( {
        "paging": false,
        dom: '<"row">'+'<"row"<t>>',"bSort" : false,
        
    } );
 
    for(var j = 0; j < <?php echo sizeof($bereiche)*2; ?>; j++){
        var column = table.column(j); 
        if(j >= <?php echo $u;?> && j <= <?php echo $o;?>){
            column.visible( true );
        } else {
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
        for (var i = 0; i < <?php echo sizeof($bereiche)*2; ?>; i+=2){
            // Toggle the visibility
            var column1 = table.column(i); 
            var column2 = table.column(i+1);
            var contained = false;
            if(Math.ceil((i+1.0) / 6.0) == section)
                contained = true;
            
            if(contained){
                column1.visible( true );
                column2.visible( true );
                visible_columns++;
            }
            else{
                column1.visible( false );
                column2.visible( false );
            }
        }

        if(section == max_sections){
            $('#section_right').prop('disabled', true);
        } else {
            $('#section_right').prop('disabled', false);
        }

        if(section == 1){
            $('#section_left').prop('disabled', true);
        } else {
            $('#section_left').prop('disabled', false);
        }
        
        switch(visible_columns){
            case 2:
                $('#myTable').width('75%')
                break;
            case 1:
                $('#myTable').width('50%');
                break;
            default:
                $('#myTable').width('100%');
                break;
        }
        $('th.max').width('15%');
        $('th.min').width('1%');
        
    } );
} );

function addslashes(string) {
    return string.replace(/\\/g, '\\\\').
        replace(/\u0008/g, '\\b').
        replace(/\t/g, '\\t').
        replace(/\n/g, '\\n').
        replace(/\f/g, '\\f').
        replace(/\r/g, '\\r').
        replace(/'/g, '\\\'').
        replace(/"/g, '\\"');
}

function displayAll(id,text,short){
    $('#'+id).html(text+' <a onclick="displayLess(\''+id+'\',\''+addslashes(text)+'\',\''+addslashes(short)+'\')"> weniger Anzeigen</a>');
}

function displayLess(id,text,short){
    $('#'+id).html(short+'... <a onclick="displayAll(\''+id+'\',\''+addslashes(text)+'\',\''+addslashes(short)+'\')"> mehr Anzeigen</a>');
}
</script>