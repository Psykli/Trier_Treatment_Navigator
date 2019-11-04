<h2>Fragebogen-Tool</h2>
<hr/>
<p>
    Vielen Dank, dass Sie den Fragebogen ausgefüllt haben.<br/>
    Klicken Sie auf den unteren Button, um zum Hauptmenü zurück zu gelangen.
</p>
<?php if(!$is_sb):?>
<a href="<?php echo base_url(); ?>index.php/patient/dashboard" type="btn btn-outline-secondary" class="btn btn-outline-secondary">Hauptmenü</a>
<?php else: ?>
<a href="<?php echo base_url(); ?>index.php/patient/sb_dynamic/overview" type="btn btn-outline-secondary" class="btn btn-outline-secondary">Hauptmenü</a>
<?php endif; ?>