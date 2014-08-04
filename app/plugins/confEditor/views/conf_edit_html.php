<?php
$vs_file 	= $this->getVar('conf_file');
$vs_content = $this->getVar('conf_content');


print "<h1>EDITING <i>{$vs_file}</i> </h1>";

print caFormTag($this->request, 'Save', 'editForm', null, 'POST', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => false, 'noTimestamp' => true));

print $vs_control_box = caFormControlBox(
    caFormSubmitButton($this->request, __CA_NAV_BUTTON_SAVE__, _t("Save"), 'editForm').' '.
    caNavButton($this->request, __CA_NAV_BUTTON_CANCEL__, _t("Back"), $this->request->getModulePath(), $this->request->getController(), 'Index', array()),
    '',
    ''
);
print "<textarea name=\"conf_content\" style=\"width:100%;height:400px;\">{$vs_content}</textarea>";
print "<input type=\"hidden\" name=\"conf_file\" value=\"{$vs_file}\" />";

print $vs_control_box = caFormControlBox(
    caFormSubmitButton($this->request, __CA_NAV_BUTTON_SAVE__, _t("Save"), 'editForm').' '.
    caNavButton($this->request, __CA_NAV_BUTTON_CANCEL__, _t("Back"), $this->request->getModulePath(), $this->request->getController(), 'Index', array()),
    '',
    ''
);

print "</form>";
?>
