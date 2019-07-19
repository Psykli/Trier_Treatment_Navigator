<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <a class="pull-left" href="#"> <img class="media-object" src="<?php echo base_url(); ?>/img/48x48/identity.png"> </a>
            <div class="media-body">
                <h2 class="media-heading">Seitenbesuche des Benutzers: <?php echo $user; ?></h2>
                <br />
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url(); ?>/index.php/admin/user">Benutzer</a> </li>
                <li><a href="<?php echo base_url(); ?>/index.php/admin/user/list_all">Liste</a> </li>
                <li><a href="<?php echo base_url().'index.php/admin/user/edit_user/'.$id ?>">Benutzerprofil</a> </li>
                <li class="active">Benutzerstatistik</li>
            </ol>
        </div>
    </div>
</div>

<script>
    
    $(document).ready(function(){
        $('#patientTable').DataTable( {
            dom: 
                 '<"row"<"col-sm-4"l><"col-sm-5"B><"col-sm-3"f>>'+
                 '<t>'+
                 '<"row"<"col-sm-3"i><"col-sm-9"p>>',
            buttons: [{
                extend: 'excel',
                text: 'Excel',
                filename: 'Seitenbesuche_'+'<?php echo $user;?>'+'_auf_'+'<?php echo isset($patientcode) ? $patientcode : 'Portal';?>'
            },{
                extend: 'pdf',
                text: 'PDF',
                filename: 'Seitenbesuche_'+'<?php echo $user;?>'+'_auf_'+'<?php echo isset($patientcode) ? $patientcode : 'Portal';?>'
            },{
                extend: 'csv',
                text: 'CSV',
                filename: 'Seitenbesuche_'+'<?php echo $user;?>'+'_auf_'+'<?php echo isset($patientcode) ? $patientcode : 'Portal';?>'
            }
          ]
        });
    });
</script>


<div class="container">
    <div class="row">
        <div class="col-sm-12">
<?php 

    $patients = array();
    $index = 0;
    foreach ($piwik as $data) {
        $match = "";
        if(preg_match("/\d{4}(p|P)\d{2}/",$data->url, $match)){
            if(!in_array($match[0],$patients)){
                $patients[$index++] = $match[0];    
            }   
        }
    } 
?>

<div class="btn-group">
    <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        Ansicht
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
    <?php 
        echo '<li><a href="'.base_url().'index.php/admin/user/user_statistics/'.$id.'">Alle</a></li>';
        foreach ($patients as $patient) {
            echo '<li><a href="'.base_url().'index.php/admin/user/user_statistics/'.$id.'/'.$patient.'">'.$patient.'</a></li>';
        }
    ?>
    </ul>
</div>

<?php 
if(isset($patientcode)){
    echo '<h3> Seitenbesuche des Nutzers beim Patienten: '.$patientcode.'</h3>';
} else {
    echo '<h3> Seitenbesuche des Nutzers auf dem gesamten Portal</h3>';
}
    echo '<table id="patientTable" class="table table-bordered table-striped">
            <thead>
                        <tr>
                            <th>IdVisit</th>
                            <th style="width:15%;">Verbrachte Zeit in Sekunden</th>
                            <th>Datum</th>
                            <th style="width:40%;">URL</th>
                        </tr>
                    </thead>
                    <tbody>';

for ($i = 0; $i < sizeof($piwik); $i++) {
    
    if(!isset($patientcode) OR preg_match('/'.$patientcode.'/',$piwik[$i]->url)){
        echo '<tr>
                    <td>'.$piwik[$i]->idvisit.'</td>
                    <td>'.$piwik[$i]->action_time.'</td>
                    <td>'.$piwik[$i]->server_time.'</td>
                    <td>'.$piwik[$i]->url.'</td>
            </tr>';     
    }
}

echo '</tbody></table>';
 
?>
        </div>
    </div>
</div>

