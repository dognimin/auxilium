<script type="application/javascript" src="<?= NODE_MODULES.'jquery/dist/jquery.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'jqueryui/jquery-ui.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'bootstrap/dist/js/bootstrap.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'bootstrap/dist/js/bootstrap.bundle.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'@fortawesome/fontawesome-free/js/all.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'@fortawesome/fontawesome-free/js/fontawesome.min.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'datatables.net/js/jquery.dataTables.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'datatables.net-bs4/js/dataTables.bootstrap4.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'aos/dist/aos.js';?>"></script>
<script type="application/javascript" src="<?= JS.'functions.js';?>"></script>
<script type="application/javascript" src="<?= JS.'auxilium.js';?>"></script>
<?php
if(ACTIVE_URL == URL.'parametres/ogd/') {echo "<script>display_parametres_ogd_index_page();</script>";}
if(isset($_GET['code']) && ACTIVE_URL == URL.'parametres/ogd/details?code='.$_GET['code']) {echo "<script>display_parametres_ogd_details_page(getUrlVars()['code']);</script>";}
if(ACTIVE_URL == URL.'parametres/ogd/edition') {echo "<script>display_parametres_ogd_edition_page();</script>";}
if(ACTIVE_URL == URL.'parametres/utilisateurs/') {echo "<script>display_parametres_utilisateurs_index_page();</script>";}
if(ACTIVE_URL == URL.'parametres/utilisateurs/edition') {echo "<script>display_parametres_utilisateurs_edition_page();</script>";}
if(isset($_GET['id']) && ACTIVE_URL == URL.'parametres/utilisateurs/details?id='.$_GET['id']) {echo "<script>display_parametres_utilisateurs_details_page(getUrlVars()['id']);</script>";}
if(isset($_GET['id']) && ACTIVE_URL == URL.'parametres/utilisateurs/edition?id='.$_GET['id']) {echo "<script>display_parametres_utilisateurs_edition_page(getUrlVars()['id']);</script>";}
if(ACTIVE_URL == URL.'parametres/referentiels/') {echo "<script>display_parametres_referentiels_index_page();</script>";}
if(ACTIVE_URL == URL.'parametres/referentiels/generaux') {echo "<script>display_parametres_referentiels_generaux_page();</script>";}
if(ACTIVE_URL == URL.'parametres/referentiels/generaux-chargements') {echo "<script>display_parametres_referentiels_generaux_chargements_page();</script>";}
if(ACTIVE_URL == URL.'parametres/referentiels/centres-sante') {echo "<script>display_parametres_referentiels_centres_sante_page();</script>";}
if(isset($_GET['code']) && ACTIVE_URL == URL.'parametres/referentiels/centre-sante?code='.$_GET['code']) {echo "<script>display_parametres_referentiels_centre_sante_page(getUrlVars()['code']);</script>";}
if(ACTIVE_URL == URL.'parametres/referentiels/professionnels-sante') {echo "<script>display_parametres_referentiels_professionnels_sante_page();</script>";}
if(isset($_GET['code']) && ACTIVE_URL == URL.'parametres/referentiels/professionnel-sante?code='.$_GET['code']) {echo "<script>display_parametres_referentiels_professionnel_sante_page(getUrlVars()['code']);</script>";}
if(ACTIVE_URL == URL.'parametres/referentiels/lettres-cles') {echo "<script>display_parametres_referentiels_lettres_cles_page();</script>";}
if(isset($_GET['code']) && ACTIVE_URL == URL.'parametres/referentiels/lettre-cle?code='.$_GET['code']) {echo "<script>display_parametres_referentiels_lettre_cle_page(getUrlVars()['code']);</script>";}
if(ACTIVE_URL == URL.'parametres/referentiels/actes-medicaux') {echo "<script>display_parametres_referentiels_actes_medicaux_page();</script>";}
if(isset($_GET['code']) && ACTIVE_URL == URL.'parametres/referentiels/acte-medical?code='.$_GET['code']) {echo "<script>display_parametres_referentiels_acte_medical_page(getUrlVars()['code']);</script>";}
if(ACTIVE_URL == URL.'parametres/referentiels/tables-de-valeur') {echo "<script>display_parametres_referentiels_tables_de_valeur_page();</script>";}
if(ACTIVE_URL == URL.'parametres/scripts/') {echo "<script>display_parametres_scripts_index_page();</script>";}
?>
</body>
<footer><?php if(date('Y',time()) != 2019){echo '2019 - '.date('Y',time());}else {echo date('Y',time());} ?> - Auxilium 2.0 <i>By</i> &copy; <a href="https://www.techouse.io" target="_blank">TecHouse</a></footer>
</html>