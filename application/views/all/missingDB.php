<!--Meldung über fehlerhaftes Eintrag in die Datenbank  -->
<?php if (isset($affects)): ?>
    <?php $affectsKey = array_keys($affects); ?>
    <?php foreach ($affectsKey as $affectKey): ?>
        <?php if ($affects[$affectKey] == -1): ?>
            <div class="alert alert-warning">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Warnung!</strong> Eintrag in die Tabelle <?php echo ($affectKey); ?> unvollständig.
                <br>Zur Verbesserung wurde bereits eine automatische Fehlermeldung erstellt. Für eine ausführlichere Fehlermeldung können Sie eine eigene erstellen. Dazu können Sie das Formular in der Navigationsleiste "Fehler melden" verwenden. 
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>