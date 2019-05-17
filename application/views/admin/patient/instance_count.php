<div id="member_area" class="instance_count">
    <div class="media bottom_spacer place_headline">
        <a class="pull-left">
            <img class="media-object" src="<?php echo base_url(); ?>/img/48x48/patients.png" data-src="holder.js/32x32">
        </a>
        <div class="media-body">
            <h1 class="media-heading">Erhebungsstatistik</h1>
        </div>
    </div>
    <div class="menu">
        <ul class="breadcrumb">
            <li><a href="../dashboard">Dashboard</a> </li>
        </ul>        
    </div><!-- end:.usermenu -->
    <div class="dashrow status">
        <div class="table_box">
            <h2>FEP2</h2>
            <?php if( is_null( $fep2 ) ): ?>
                <div class="alert alert-error">
                    Keine Daten vorhanden.
                </div>
            <?php else: ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <th>Zeitpunkt</th>
                        <th>Anzahl an Fälle</th>
                    </thead>
                    <tbody>
                        <?php foreach( $fep2 as $entry ): ?>
                            <tr>
                                <td><?php echo $entry['instance']; ?></td>
                                <td><?php echo $entry['count']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div><!-- end:.dashrow -->
</div>