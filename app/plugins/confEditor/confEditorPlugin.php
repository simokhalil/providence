<?php
/**
 * Created by PhpStorm.
 * User: khalil
 * Date: 29/07/14
 * Time: 17:19
 */

//namespace plugins\confEditor;


class confEditorPlugin extends BaseApplicationPlugin {
    # -------------------------------------------------------
    protected $description = 'configuration files editor plugin for CollectiveAccess';
    # -------------------------------------------------------
    private $opo_config;
    private $ops_plugin_path;
    # -------------------------------------------------------
    public function __construct($ps_plugin_path) {
        $this->ops_plugin_path = $ps_plugin_path;
        $this->description = _t('Conf files Editor');
        parent::__construct();
        $this->opo_config = Configuration::load($ps_plugin_path.'/conf/confEditor.conf');
    }
    # -------------------------------------------------------
    /**
     * Override checkStatus() to return true - the providenceTourMLPlugin always initializes ok... (part to complete)
     */
    public function checkStatus() {
        return array(
            'description' => $this->getDescription(),
            'errors' => array(),
            'warnings' => array(),
            'available' => ((bool)$this->opo_config->get('enabled'))
        );
    }

    # -------------------------------------------------------
    /**
     * Insert activity menu
     */
    public function hookRenderMenuBar($pa_menu_bar) {
        if ($o_req = $this->getRequest()) {
            //if (!$o_req->user->canDoAction('can_use_media_import_plugin')) { return true; }


            if (isset($pa_menu_bar['manage'])) {
                $va_menu_item = array(
                    'displayName' => _t("Config Editor"),
                    "default" => array(
                        'module' => 'confEditor',
                        'controller' => 'ConfEditor',
                        'action' => 'Index'
                    )
                );

                $pa_menu_bar['manage']['navigation']["confeditor"] = $va_menu_item;
            }

            /*if (isset($pa_menu_bar['confEdit_menu'])) {
                $va_menu_items = $pa_menu_bar['confEdit_menu']['navigation'];
                if (!is_array($va_menu_items)) { $va_menu_items = array(); }
            } else {
                $va_menu_items = array();
            }

            $va_menu_items[0] = array(
                'displayName' => _t("Edit conf files"),
                "default" => array(
                    'module' => 'confEditor',
                    'controller' => 'ConfEditor',
                    'action' => 'Index'
                )
            );

            $pa_menu_bar['confEdit_menu'] = array(
                'displayName' => _t('Conf Editor'),
                'navigation' => $va_menu_items
            );*/

        }

        //print_r($pa_menu_bar);
        //die();
        return $pa_menu_bar;
    }

    # -------------------------------------------------------
    /**
     * Add plugin user actions
     */
    static function getRoleActionList() {
        return array(
            'can_use_conf_editor_plugin' => array(
                'label' => _t('Can use the confEditor Plugin'),
                'description' => _t('User can use the confEditor Plugin.')
            )
        );
    }
} 