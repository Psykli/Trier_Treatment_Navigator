</div>
<div class="container quest_container">
<div class="text-center" style="margin-top:9%;">
    <div>
        <h2 style="font-size:45px; margin-bottom:8%;"> Vielen Dank! </h1>

        <h2 style="font-size:45px; margin-bottom:8%;"> Bitte wenden Sie sich nun an Ihren Therapeuten. </h2>      

        <h2 style="font-size:45px; margin-bottom:3%;"><a href="<?php echo site_url('patient/sb_dynamic/overview');?>"> Zurück zur Hauptseite </a></h2>

        <?php
            $view_status = $this->Patient_model->get_view_status( $patientcode );
            if( isset( $suicidecolour ) AND $view_status == 2 ) {
                echo '<div style="background-color:' . $suicidecolour . ';width:45px;height:30px;margin-left:auto;margin-right:auto"></div>';
            }
        ?>
    <div>
</div>


