<li class="nav-item">
    <a class="nav-link" href="<?php echo site_url('patient/sb_dynamic/index'); ?>">Stundenbögen</a>
</li>

<li class="nav-item dropdown">
    <div class="btn-group">
        <a class="nav-link" href="<?php echo site_url('admin/user'); ?>">Benutzer</a>
        <a class="nav-link dropdown-toggle dropdown-toggle-split" role="button" data-toggle="dropdown"></a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="<?php echo site_url('admin/user/list_all'); ?>">Benutzerliste</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/user/new_user'); ?>">Neuer Benutzer</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/user/list_all_delete'); ?>">Benutzer löschen</a>
        </div>
    </div>
</li>

<li class="nav-item dropdown">
    <div class="btn-group">
        <a class="nav-link" href="<?php echo site_url('admin/patient'); ?>">Patienten</a>
        <a class="nav-link dropdown-toggle dropdown-toggle-split" role="button" data-toggle="dropdown"></a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="<?php echo site_url('admin/patient/list_all'); ?>">Patientenliste</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/patient/new_patientlogin'); ?>">Patienten anlegen</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/patient/instance_count'); ?>">Erhebungsstatistik</a>
        </div>
    </div>
</li>