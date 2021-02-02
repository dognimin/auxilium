<?php
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);
session_start();
$_SESSION['expire'] = time() + 15*60;

if(time() > $_SESSION['expire']){
    session_destroy();
    session_write_close();
    session_unset();
    $_SESSION = array();
}else {
    $_SESSION['expire'] = time() + 15*60;
}

date_default_timezone_set('Africa/Abidjan');

define('ACTIVE_URL', "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
define('URL', 'http://localhost/auxilium/');
define('DIR', $_SERVER['DOCUMENT_ROOT'].'/auxilium/');



define('CONFIGS', URL.'_configs/');
define('PUBLICS', URL.'_publics/');
define('NODE_MODULES', PUBLICS.'node_modules/');
define('CSS', PUBLICS.'css/');
define('JS', PUBLICS.'js/');
define('IMAGES', PUBLICS.'images/');
define('SERVEUR_ADRESSE_IP', $_SERVER['SERVER_ADDR']);
define('CLIENT_ADRESSE_IP', $_SERVER['REMOTE_ADDR']);


define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 465);
define('SMTP_USERNAME', 'techouseci@gmail.com');
define('SMTP_PASSWORD', 'TecH@use#2@!8');


class BDD
{

    private $host = "localhost:3307";
    private $user = "root";
    private $pass = "";
    private $dbname = "techouse_projet_auxilium_db";


    public function __construct($host = null, $user = null, $pass = null, $dbname = null)
    {
        if ($host != null) {
            $this->host = $host;
            $this->user = $user;
            $this->pass = $pass;
            $this->dbname = $dbname;
        }
        try {
            $this->bdd = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->dbname, $this->user, $this->pass, array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8',
                PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
                PDO::MYSQL_ATTR_LOCAL_INFILE => true
            ));
        } catch (PDOException $e) {
            echo 'Message: ' .$e->getMessage();
            //die('{"status": false,"message": "Impossible de se connecter à la base de données. <br />Veuillez contacter votre administrateur."}');
        }
    }

    public function query($sql, $data = array())
    {
        $req = $this->bdd->prepare($sql);
        $req->execute($data);
        return $req->fetchAll(PDO::FETCH_OBJ);
    }

    public function getBdd()
    {
        return $this->bdd;
    }
}

?>