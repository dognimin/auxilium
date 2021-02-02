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
            if(empty($user['code_ogd'])) {
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
                    <a class="nav-link dropdown-toggle" href="#" id="populationDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-users-cog"></i> Populations
                    </a>
                    <div class="dropdown-menu" aria-labelledby="populationDropdownMenuLink">
                        <a class="dropdown-item" href="#"><i class="fa fa-search"></i> Recherche</a>
                        <a class="dropdown-item" href="#"><i class="fa fa-upload"></i> Chargements</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="statistiquesDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-file-medical-alt"></i> Factures
                    </a>
                    <div class="dropdown-menu" aria-labelledby="statistiquesDropdownMenuLink">
                        <a class="dropdown-item" href="#"><i class="fa fa-search"></i> Recherche</a>
                        <a class="dropdown-item" href="#"><i class="fa fa-upload"></i> Chargements</a>
                        <a class="dropdown-item" href="#"><i class="fa fa-check"></i> Vérification</a>
                        <a class="dropdown-item" href="#"><i class="fa fa-laravel"></i> Liquidation</a>
                        <a class="dropdown-item" href="#"><i class="fa fa-laravel"></i> Paiement</a>
                        <a class="dropdown-item" href="#"><i class="fa fa-gg-circle"></i> Rejet</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="statistiquesDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-chart-bar"></i> Statistiques
                    </a>
                    <div class="dropdown-menu" aria-labelledby="statistiquesDropdownMenuLink">
                        <a class="dropdown-item" href="#"><i class="fa fa-users"></i> Populations</a>
                        <a class="dropdown-item" href="#"><i class="fa fa-file"></i> Factures</a>
                    </div>
                </li>
                <?php
            }
            ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-danger" href="<?= URL.'parametres/';?>" id="ParamètresDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-cogs"></i> Paramètres
                </a>
                <div class="dropdown-menu" aria-labelledby="ParamètresDropdownMenuLink">
                    <a class="dropdown-item text-danger" href="<?= URL.'parametres/ogd/';?>"><i class="fa fa-adjust"></i> Organismes</a>
                    <a class="dropdown-item text-danger" href="<?= URL.'parametres/utilisateurs/';?>"><i class="fa fa-users"></i> Utilisateurs</a>
                    <a class="dropdown-item text-danger" href="<?= URL.'parametres/referentiels/';?>"><i class="fa fa-list"></i> Référentiels</a>
                </div>
            </li>
        </ul>
        <div class="navbar-nav ml-md-auto">
            <a class="nav-link" href="<?= URL.'profil.php';?>" title="<?= $user['nom'].' '.$user['prenom'];?>"><i class="fa fa-user"></i></a>
            <a class="nav-link" href="<?= URL.'manuel.php';?>" target="_blank" title="Manuel d'utilisation"><i class="fa fa-question"></i></a>
            <button id="deconnexion_button" type="button" class="btn text-danger btn-sm btn-circle" title="Déconnexion"><i class="fa fa-power-off"></i></button>
        </div>

    </div>
</header>