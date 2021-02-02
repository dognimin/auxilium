<script type="application/javascript" src="<?= NODE_MODULES.'jquery/dist/jquery.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'jqueryui/jquery-ui.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'bootstrap/dist/js/bootstrap.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'bootstrap/dist/js/bootstrap.bundle.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'@fortawesome/fontawesome-free/js/all.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'@fortawesome/fontawesome-free/js/fontawesome.min.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'aos/dist/aos.js';?>"></script>
<script type="application/javascript" src="<?= JS.'functions.js';?>"></script>
<script type="application/javascript" src="<?= JS.'auxilium.js';?>"></script>
<?php
if(ACTIVE_URL == URL.'connexion') {echo "<script>display_login_page();</script>";}
if(ACTIVE_URL == URL) {echo "<script>display_index_page();</script>";}
/*


if(ACTIVE_URL == URL.'manuel') {echo "<script>display_manuel_page();</script>";}
if(ACTIVE_URL == URL.'maj-mot-de-passe') {echo "<script>display_mot_de_passe_page();</script>";}
*/
?>
</body>
<footer><?php if(date('Y',time()) != 2019){echo '2019 - '.date('Y',time());}else {echo date('Y',time());} ?> - Auxilium 2.0 <i>By</i> &copy; <a href="https://www.techouse.io" target="_blank">TecHouse</a></footer>
</html>