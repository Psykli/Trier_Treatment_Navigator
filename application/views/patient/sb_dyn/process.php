</div>
<script src="<?php echo(base_url());?>js/charts/hsclProcess.js"></script>
<div class="container quest_container">
<div class="col-md-12">
<!--<h2><a href="<?php echo site_url('patient/sb_dynamic/index');?>"> Stundenbogen beenden </a></h2>-->
<?php $view_status = $this->Patient_model->get_view_status( $patientcode );
        $directory_number = 0; ?> 
<?php if( $view_status != 2 ): ?>
    <div class="text-center" style="margin-top:9%;">
        <h1>Vielen Dank!<h1>
        <br/><br/><br/>
        <a href="<?php echo site_url();?>"><h2>Klicken Sie hier um den Stundenbogen zu beenden</h2></a>
    </div>
<?php else:?>

<?php 
    $_GET['tables'] = $tables;
    function selected($table, $item) {
		foreach($_GET['tables'][$table] as $key => $value) {
			if(strcmp($key, $item) == 0)
				return $value;
		}
		return 'Fehler'; //besserer Umgang mit Fehlern?
	}
?>
<?php foreach($feedback as $f):?>
    <?php switch($f->type){
        case 'text':
            echo '<h2>'.$f->data.'</h2>';
            break;
        case 'process':

                // TODO: Offsets-> da feedbackOrder Indizes nicht mehr in $graphs vorhanden
                $graph = $graphs[$f->feedback_order];?>

            //foreach($graphs[$f->feedback_order] as $graph)?>
                <canvas id="<?php echo $graph['name']?>" width='680px' height='400px'></canvas>
                <script>
                createHsclGraph(<?php echo($graph['name']);?>, <?php echo($graph['title']);?>,<?php json_encode($graph['means']);?>,<?php json_encode($graph['instances']);?>,<?php json_encode($graph['boundaries']);?>, <?php json_encode($graph['expected_values']);?>);
                </script><?php
               break;
        case 'review': //Gib Fragebogen mit den ausgefüllten Werten aus
            
            if(isset($xml_directories[$f->feedback_order]['xml'])){
                $file = $xml_directories[$f->feedback_order]['xml'];
                $name = $xml_directories[$f->feedback_order]['name']; 
                
                $reader = new XMLReader;
                $reader->open($file);
                while($reader->read() && strcmp($reader->name, "Questionnaire") != 0);
                $table = $reader->getAttribute('table');
                    $max_instance = sizeof($tables[$table]);
                while($reader->read() && strcmp($reader->name, "Headline") != 0);
                    $alternate = $reader->getAttribute('alternate');

                if (!is_null($alternate)){
                    if(strlen($name > 0 ))
                        echo("<div style='text-align: center'><h1>".$name."</h1></div>");
                    $xml = new DOMDocument();
                    $xml->load($file);
                    $xsl = new DOMDocument();
                    $xsl->load($evaluationXSL);
                    $proc = new XSLTProcessor();
                    $proc->registerPHPFunctions();
                    $proc->importStyleSheet($xsl);
                    $proc->setParameter('', 'directory_number', $directory_number);
                    $proc->setParameter('', 'file_number', $directory_number);
                    $proc->setParameter('', 'exists', 1);
                    $proc->setParameter('', 'eval_everything', false);
                    $proc->setParameter('', 'language', 'de');
                    echo $proc->transformToXML($xml);
                    echo "<hr />";
                }   
            }
            break;
        
    }?>
<?php endforeach;?>
    
<h2><a href="../sb_dynamic/index"> Stundenbogen beenden </a></h2>
<?php endif;?>
</div>
</div>

