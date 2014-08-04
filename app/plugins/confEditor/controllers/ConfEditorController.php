<?php

require_once(__CA_LIB_DIR__.'/core/TaskQueue.php');
require_once(__CA_LIB_DIR__.'/core/Configuration.php');
require_once(__CA_MODELS_DIR__.'/ca_lists.php');
require_once(__CA_MODELS_DIR__.'/ca_objects.php');
require_once(__CA_MODELS_DIR__.'/ca_object_representations.php');
require_once(__CA_MODELS_DIR__.'/ca_locales.php');


class ConfEditorController extends ActionController {
    # -------------------------------------------------------
    protected $opo_config;		// plugin configuration file
    protected $opa_locales;

    # -------------------------------------------------------
    # Constructor
    # -------------------------------------------------------

    public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
        global $allowed_universes;

        parent::__construct($po_request, $po_response, $pa_view_paths);

        if (!$this->request->user->canDoAction('can_use_conf_editor_plugin')) {
            $this->response->setRedirect($this->request->config->get('error_display_url').'/n/3000?r='.urlencode($this->request->getFullUrlPath()));
            return;
        }

        $this->opo_config = Configuration::load(__CA_APP_DIR__.'/plugins/confEditor/conf/confEditor.conf');


    }

    # -------------------------------------------------------
    # Functions to render views
    # -------------------------------------------------------
    public function Index($save=null) {
        $va_files_list = array();

        /* Listing all configuration files in app/conf */
        if ($handle = opendir(__CA_APP_DIR__."/conf/")) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." && strtolower(substr($entry, strrpos($entry, '.') + 1)) == 'conf')  {
                    $va_files_list[] = $entry;
                    //die($entry);
                }
            }
            closedir($handle);
        }
        sort($va_files_list);

        if(save!=null){
            switch($save){
                case true:
                    $this->view->setVar('saveMsg', _("File saved successfully"));
                    break;
                case 1:
                    $this->view->setVar('saveMsg', _("No file submitted"));
                    break;
                case 2:
                    $this->view->setVar('saveMsg', _("No write permissions"));
                    break;
                case 3:
                    $this->view->setVar('saveMsg', _("File could not be saved"));
                    break;
            }

        }
        $this->view->setVar('conf_files_list', $va_files_list);
        $this->render('conf_list_html.php');
        //die("ok");
    }

    public function Edit($file=""){
        $file = $this->request->getParameter('file', pString);

        $configFileHandle = fopen(__CA_APP_DIR__."/conf/".$file, 'r');
        $configFileContent = fread($configFileHandle, filesize(__CA_APP_DIR__."/conf/".$file));
        fclose($configFileHandle);

        $this->view->setVar('conf_file', $file);
        $this->view->setVar('conf_content', $configFileContent);
        $this->render('conf_edit_html.php');
    }

    /* Errors
     * - 1 : No file submitted
     * - 2 : No write permissions
     * - 3 : Write error
     * - true : file update ok
     */
    public function Save(){
        $file = $this->request->getParameter('conf_file', pString);
        $file_content = $this->request->getParameter('conf_content', pString);

        if($file != null && $file!=""){
            if($configFileHandle = fopen(__CA_APP_DIR__."/conf/".$file, 'w+')){

                if(fwrite($configFileHandle,$file_content)){
                    fclose($configFileHandle);
                    $this->Index(true);
                }
                else{
                    $this->Index(3);
                }
            }
            else{
                $this->Index(2);
            }
        }
        else{
            $this->Index(1);
        }
    }
}

?>