<header class="navbar navbar-expand-lg navbar-light flex-column flex-md-row bd-navbar">
    <a class="navbar-brand" href="<?php if(empty($user['code_ogd'])){echo URL;}else {echo URL.'ogd/';} ?>">
        <img src="<?= IMAGES.'favicon.jpg';?>" width="30" height="30" alt="Logo auxilium">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav">
            <?php
            if(!$user['code_ogd']) {
                ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= URL.'ogd/';?>"><i class="fa fa-adjust"></i> OGD</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= URL.'statistiques/';?>"><i class="fa fa-chart-bar"></i> Statistiques</a>
                </li>
                <?php
            }else {
                ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="chargementsDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-exchange-alt"></i> Chargements
                    </a>
                    <div class="dropdown-menu" aria-labelledby="chargementsDropdownMenuLink">
                        <a class="dropdown-item" href="<?= URL.'ogd/imports';?>"><i class="fa fa-upload"></i> Impotrs</a>
                        <a class="dropdown-item" href="<?= URL.'ogd/exports';?>"><i class="fa fa-download"></i> Exports</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= URL.'ogd/populations';?>">
                        <i class="fa fa-user-shield"></i> Populations
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="facturesDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-file-medical-alt"></i> Factures
                    </a>
                    <div class="dropdown-menu" aria-labelledby="facturesDropdownMenuLink">
                        <a class="dropdown-item" href="<?= URL.'ogd/factures-recherche';?>"><i class="fa fa-search"></i> Recherche</a>
                        <a class="dropdown-item" href="<?= URL.'ogd/factures-verification';?>"><i class="fa fa-check"></i> Vérification</a>
                        <a class="dropdown-item" href="<?= URL.'ogd/factures-liquidation';?>"><i class="fa fa-flask"></i> Liquidation</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= URL.'ogd/statistiques';?>">
                        <i class="fa fa-chart-bar"></i> Statistiques
                    </a>
                </li>
                <?php
            }
            ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-danger" href="<?= URL.'parametres/';?>" id="ParamètresDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-cogs"></i> Paramètres
                </a>
                <div class="dropdown-menu" aria-labelledby="ParamètresDropdownMenuLink">
                    <?php
                    if(!$user['code_ogd']) {
                        ?>
                        <a class="dropdown-item text-danger" href="<?= URL.'parametres/ogd/';?>"><i class="fa fa-adjust"></i> Organismes</a>
                        <?php
                    }
                    ?>
                    <a class="dropdown-item text-danger" href="<?= URL.'parametres/utilisateurs/';?>"><i class="fa fa-users"></i> Utilisateurs</a>
                    <a class="dropdown-item text-danger" href="<?= URL.'parametres/referentiels/';?>"><i class="fa fa-list"></i> Référentiels</a>
                    <a class="dropdown-item text-danger" href="<?= URL.'parametres/scripts/';?>"><i class="fa fa-code"></i> Scripts</a>
                </div>
            </li>
        </ul>
        <div class="navbar-nav ml-md-auto">
            <a class="nav-link" href="<?= URL.'profil';?>" title="<?= $user['nom'].' '.$user['prenom'];?>"><i class="fa fa-user"></i></a>
            <a class="nav-link" href="<?= URL.'manuel.php';?>" target="_blank" title="Manuel d'utilisation"><i class="fa fa-question"></i></a>
            <button id="deconnexion_button" type="button" class="btn text-danger btn-sm btn-circle" title="Déconnexion"><i class="fa fa-power-off"></i></button>
        </div>

    </div>
</header>