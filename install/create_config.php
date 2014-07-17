<?php
/**
 * Created by PhpStorm.
 * User: khalil
 * Date: 17/07/14
 * Time: 16:43
 */

// Check existence of setup.php
if (!file_exists('../setup.php')) {

    if(!isset($_REQUEST['validateDbCredentials'])){
        /* Check write permissions */
        if(!$setupFile = fopen('../setup.php', 'w+'))
        {
            die("It is not possible to create the setup.php file automatically. Please check file permissions.");
        }

        /* Remove the created file */
        unlink('../setup.php');

        /* Display the setup form */
        include("inc/create_database.php");
    }
    else{
        $host = isset($_REQUEST['host']) ? $_REQUEST['host'] : '';
        $rootUsername = isset($_REQUEST['rootUsername']) ? $_REQUEST['rootUsername'] : '';
        $rootPassword = isset($_REQUEST['rootPassword']) ? $_REQUEST['rootPassword'] : '';
        $dbUsername = isset($_REQUEST['dbUsername']) ? $_REQUEST['dbUsername'] : '';
        $dbPassword = isset($_REQUEST['dbPassword']) ? $_REQUEST['dbPassword'] : '';
        $dbName = isset($_REQUEST['dbName']) ? $_REQUEST['dbName'] : '';

        /* Error codes returned
         * 0 : all is ok
         * 1 : connect exception : credentials problem
         * 2 : db creation exception
         * 3 : user creation/grant problem
         */

        /* Test host */

        if($rootUsername == '' && $rootPassword == ''){
            try{
                $dbh = new PDO("mysql:host=".$host, $dbUsername, $dbPassword);
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }catch(PDOException $e){
                echo '1';
                die();
            }
        }else{
            try{
                $dbh = new PDO("mysql:host=".$host, $rootUsername, $rootPassword);
                try{
                    $dbh->exec("CREATE USER '$dbUsername'@'$host' IDENTIFIED BY '$dbPassword';
                            GRANT ALL ON `$dbName`.* TO '$dbUsername'@'$host';
                            FLUSH PRIVILEGES;");
                } catch(PDOException $e){
                    echo 'An error occurred while creating the user. Please retry later or contact your administrator';
                    die();
                }
            }catch(PDOException $e){
                echo 'Unable to connect to server. Please verify your root credentials';
                die();
            }
        }


        try{
            $dbh->query("CREATE DATABASE IF NOT EXISTS `$dbName`");
        } catch(PDOException $e){
            echo 'An error occurred while creating the database. Please retry later or contact your administrator';
            die();
        }

        $setupFileHandle = fopen("../setup.php-dist", 'r');
        $setupFileContent = fread($setupFileHandle, filesize("../setup.php-dist"));

        $setupFileContent = str_replace("define(\"__CA_DB_HOST__\", \'localhost\');", "define(\"__CA_DB_HOST__\", \'{$host}\');", $setupFileContent);
        $setupFileContent = str_replace("my_database_user", $dbUsername, $setupFileContent);
        $setupFileContent = str_replace("my_database_password", $dbPassword, $setupFileContent);
        $setupFileContent = str_replace("name_of_my_database", $dbName, $setupFileContent);

        $setupFileHandle = fopen("../setup.php", 'w+');
        fwrite($setupFileHandle, $setupFileContent);
        fclose($setupFileHandle);

        echo '0';
    }
}