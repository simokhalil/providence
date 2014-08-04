<?php

$va_files_list 	= $this->getVar('conf_files_list');
$vs_save = $this->getVar('saveMsg');


print "<h1>" ._("CONFIGURATION FILES EDITOR") ."</h1>";

if($vs_save != null){
    print "<div class=\"notification-info-box rounded\">
        <ul class=\"notification-info-box\">
            <li class=\"notification-info-box\">"
        . $vs_save .
            "</li>
        </ul>
        </div>";
}

print "<h3>" . _("Select file to be edited"). "</h3>";

foreach($va_files_list as $vs_file) {
    print "<a href=\"http://" . __CA_SITE_HOSTNAME__ .__CA_URL_ROOT__."/index.php/confEditor/ConfEditor/Edit/file/{$vs_file} \"> {$vs_file } </a><br/>";

}

print "<br/><div class=\"clear\"><!--empty--></div>\n".
"<div class=\"editorBottomPadding\"><!-- empty --></div>\n" .
"<div class=\"clear\"><!--empty--></div>\n";

?>