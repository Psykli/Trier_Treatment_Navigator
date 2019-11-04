</div>
<script src="<?php echo base_url(); ?>js/charts/hsclProcess.js"></script>
<script src="<?php echo base_url(); ?>js/charts/compare.js"></script>
<div class="container quest_container">
<div class="col-md-12">
<!--<h2><a href="<?php echo site_url('patient/sb_dynamic/index');?>"> Stundenbogen beenden </a></h2>-->
<?php $directory_number = 0; ?>
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
        $tables = $_GET['tables'][$table];
		foreach($tables as $key => $value) {
			if($key === $item)
				return $value;
		}
		return 'Fehler'; //besserer Umgang mit Fehlern?
	}
?>
<?php $counter = 0;?>
<?php foreach($feedback as $f):?>
    <div class="row">
    <?php switch($f->type){
        case 'text':
            echo '<h2>'.$f->data.'</h2>';
            break;
        case 'process':?>
            <div class="col-md-6">
                <canvas id="<?php echo $f->data?>"></canvas>
                <?php if(strtoupper($f->data) == 'HSCL-11'):?>
                <script>
                    createHsclChart("<?php echo $f->data;?>", "<?php echo $f->data;?>", <?php echo json_encode($hsclData['MEANS']);?>,
                                    <?php echo json_encode($hsclData['INSTANCES']);?>,<?php echo json_encode($hsclData['BOUNDARIES']);?>,
                                    <?php echo json_encode($hsclData['EXPECTED']);?>);
                </script>
                <?php else:?>
                    <?php 
                        $tables = array_keys($means[$f->data]);
                    ?>
                    <script>
                    createLineChart("<?php echo $f->data;?>","<?php echo $f->data;?>", <?php echo json_encode($means[$f->data][$tables[0]]);?>,
                                            <?php echo json_encode($means[$f->data][$tables[1]]);?>,<?php echo json_encode($infos[$f->data]);?>);
                    </script>
                <?php endif;?>
                </div>
            <?php break;
            
        case 'review': //Gib Fragebogen mit den ausgefüllten Werten aus
            
            if(isset($xml_directories[$f->feedback_order]['xml'])){
                $file = $xml_directories[$f->feedback_order]['xml'];
                $name = $xml_directories[$f->feedback_order]['name']; 
                
                $reader = new XMLReader;
                $reader->open($file);
                while($reader->read() && $reader->name !== "Questionnaire");
                $table = $reader->getAttribute('table');
                    $max_instance = sizeof($tables[$table]);
                while($reader->read() && $reader->name !== "Headline");
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
    </div>
<?php endforeach;?>
    
<h2><a href="../sb_dynamic/index"> Stundenbogen beenden </a></h2>
<?php endif;?>
</div>
</div>

