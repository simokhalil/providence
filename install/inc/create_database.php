<?php
/**
 * Created by PhpStorm.
 * User: khalil
 * Date: 17/07/14
 * Time: 10:20
 */

    $va_tmp = explode("/", str_replace("\\", "/", $_SERVER['SCRIPT_NAME']));
    array_pop($va_tmp);
    $vs_url_path = join("/", $va_tmp);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <title>CollectiveAccess: Installer</title>
    <link href="css/site.css" rel="stylesheet" type="text/css" />

    <script src='../js/jquery/jquery.js' type='text/javascript'></script>
    <script src='../js/jquery/jquery-ui/jquery-ui.min.js' type='text/javascript'></script></head>

<body>
<div class='content'>
    <div id='box'>
        <div id="logo"><img src="<?php print $vs_url_path; ?>/graphics/ca_logo.png"/></div><!-- end logo -->
        <div id="content">
            <H1>
                <?php echo('Installation configuration'); ?>
            </H1>

            <p><?php echo "The following settings allow CollectiveAccess to connect to its database. <br/>
                These settings should have been given to you by your system administrator or hosting provider."; ?>
            </p>

            <p id="errorMsg" style="color: red; text-align: center; font-weight: bold"></p>

            <div id="installForm">
                <form action="index.php" name="createDbForm" id="createDbForm">
                    <div class='formItem'>
                        <label></label><?php echo 'Mysql Host :' ?></label>
                        <input type="text" name="host" id="host" value="localhost" required />
                    </div>

                    <div class='formItem'>
                        <label></label><?php echo 'Database name (will be created if does not exist) :' ?></label>
                        <input type="text" name="dbName" id="dbName" required />
                    </div>

                    <p>
                        <?php /*echo "To insure your server's security, a new user with only privileges on CollectiveAccess's database
                            will be created. This is to prevent any access violation to your other databases. <br/>
                            Please provide a username and a password that will be specific to this database"; */?>
                    </p>

                    <div class='formItem'>
                        <label></label><?php echo 'Database user :' ?></label>
                        <input type="text" name="dbUsername" id="dbUsername" required />
                    </div>

                    <div class='formItem'>
                        <label></label><?php echo 'Database user password :' ?></label>
                        <input type="password" name="dbPassword" id="dbPassword" required />
                    </div>

                    <div id="rootCredentials" style="display: none;">
                        <p><?php echo 'The user you specified cannot connect to the database, please provide your root credentials,
                            so that this user will be created'; ?>
                        </p>
                        <div class='formItem'>
                            <label></label><?php echo 'Mysql Root username :' ?></label>
                            <input type="text" name="rootUsername" id="rootUsername" />
                        </div>

                        <div class="formItem">
                            <label></label><?php echo 'Mysql Root password :' ?></label>
                            <input type="password" name="rootPassword" id="rootPassword" />
                        </div>

                    </div>

                    <input type="hidden" name="page" value="setup" />

                    <div class="loginSubmitButton">
                        <a href='#' onclick="jQuery('#createDbForm').submit();" class='form-button'>
                            <span class='form-button'>
                                <img src='<?php print $vs_url_path; ?>/graphics/login.gif' border='0' class='form-button-left' style='padding-right: 10px;'/>
                                <?php echo 'Create setup file'; ?>
                            </span>
                        </a>
                        <img id="loader" src="inc/loader.gif" style="float: right; display: none;"/>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery('#createDbForm').on('submit', function(){
        jQuery('#loader').show();

        jQuery.ajax({
            type : 'POST',
            url: 'index.php?',
            data : {
                validateDbCredentials: true,
                host: jQuery('#host').val(),
                dbName: jQuery('#dbName').val(),
                rootUsername: jQuery('#rootUsername').val(),
                rootPassword: jQuery('#rootPassword').val(),
                dbUsername: jQuery('#dbUsername').val(),
                dbPassword: jQuery('#dbPassword').val()
            },
            success: function(data){
                if(data == '0'){
                    window.location.href = "index.php";
                }
                else{
                    if(data == '1'){
                        jQuery('#rootCredentials').slideDown('slow');
                    }
                    else{
                        jQuery('#errorMsg').html(data);
                    }
                }
                //alert(data);
                jQuery('#loader').hide();
            }
        });
        return false;
    });
</script>
</body>
</html>