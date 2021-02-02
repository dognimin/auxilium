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
if(ACTIVE_URL == URL.'ogd/') {echo "<script>display_ogd_index_page();</script>";}
if(isset($_GET['code']) && ACTIVE_URL == URL.'ogd/details?code='.$_GET['code']) {echo "<script>display_ogd_details_page(getUrlVars()['code']);</script>";}
if(isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/populations?code-ogd='.$_GET['code-ogd'] || ACTIVE_URL == URL.'ogd/populations') {echo "<script>display_ogd_populations_page(getUrlVars()['code-ogd']);</script>";}
if(
        isset($_GET['code-ogd']) && isset($_GET['num-secu']) && ACTIVE_URL == URL.'ogd/assure?code-ogd='.$_GET['code-ogd'].'&num-secu='.$_GET['num-secu'] ||
        isset($_GET['num-secu']) && ACTIVE_URL == URL.'ogd/assure?num-secu='.$_GET['num-secu']

) {
    echo "<script>display_ogd_assure_page(getUrlVars()['code-ogd'],getUrlVars()['num-secu']);</script>";
}
if((isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/chargements?code-ogd='.$_GET['code-ogd']) || ACTIVE_URL == URL.'ogd/chargements') {echo "<script>display_ogd_chargements_page(getUrlVars()['code-ogd']);</script>";}
if((isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/imports?code-ogd='.$_GET['code-ogd']) || ACTIVE_URL == URL.'ogd/imports') {echo "<script>display_ogd_imports_page(getUrlVars()['code-ogd']);</script>";}
if((isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/exports?code-ogd='.$_GET['code-ogd']) || ACTIVE_URL == URL.'ogd/exports') {echo "<script>display_ogd_exports_page(getUrlVars()['code-ogd']);</script>";}
if(isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/factures?code-ogd='.$_GET['code-ogd'] || ACTIVE_URL == URL.'ogd/factures') {echo "<script>display_ogd_factures_page(getUrlVars()['code-ogd']);</script>";}
if(isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/factures-verification?code-ogd='.$_GET['code-ogd'] || ACTIVE_URL == URL.'ogd/factures-verification') {echo "<script>display_ogd_factures_verification_page(getUrlVars()['code-ogd']);</script>";}
if(isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/factures-liquidation?code-ogd='.$_GET['code-ogd'] || ACTIVE_URL == URL.'ogd/factures-liquidation') {echo "<script>display_ogd_factures_liquidation_page(getUrlVars()['code-ogd']);</script>";}
if(isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/factures-recherche?code-ogd='.$_GET['code-ogd'] || ACTIVE_URL == URL.'ogd/factures-recherche') {echo "<script>display_ogd_factures_recherche_page(getUrlVars()['code-ogd']);</script>";}
if(isset($_GET['num']) && ACTIVE_URL == URL.'ogd/facture?num='.$_GET['num']) {echo "<script>display_ogd_facture_page(getUrlVars()['num']);</script>";}
if(isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/statistiques?code-ogd='.$_GET['code-ogd'] || ACTIVE_URL == URL.'ogd/statistiques') {echo "<script>display_ogd_statistiques_page(getUrlVars()['code-ogd']);</script>";}
if(ACTIVE_URL == URL.'statistiques/') {echo "<script>display_statistiques_index_page();</script>";}
if(ACTIVE_URL == URL.'parametres/') {echo "<script>display_parametres_index_page();</script>";}
?>
</body>
<footer><?php if(date('Y',time()) != 2019){echo '2019 - '.date('Y',time());}else {echo date('Y',time());} ?> - Auxilium 2.0 <i>By</i> &copy; <a href="https://www.techouse.io" target="_blank">TecHouse</a></footer>
</html>