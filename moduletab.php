<?php

class ModuleTab extends Module
{
    /**
     * Using the automatic creation of tab available from PS 1.7.1.0
     * @see http://doc.prestashop.com/display/PS17/Adding+module+links+in+the+back-office+side+menu
     * 
     * @var Array of tabs (= admin controllers) to add
     */
    public $tabs = array(
        array(
            'name' => 'Tab 1', // One name for all langs
            'class_name' => 'AdminTabOne',
            'visible' => true,
            'parent_class_name' => 'AdminThemes',
        ),
        array(
            'name' => 'Tab 2', // One name for all langs
            'class_name' => 'AdminTabTwo',
            'visible' => true,
            'parent_class_name' => 'AdminThemes',
    ));

    const psVersionWithFix = '1.7.3.2';

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->name                   = 'moduletab';
        $this->tab                    = 'administration';
        $this->version                = '2.0.0';
        $this->author                 = 'PrestaShop';
        $this->ps_versions_compliancy = array(
            'min' => '1.7.2.0',
        );

        parent::__construct();

        $this->displayName = $this->l('Module example for BO tabs');
        $this->description = $this->l('Want some tabs?');
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        return parent::install() && $this->fixTabOnInstall();
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall()
    {
        return parent::uninstall() && $this->fixTabOnUninstall();
    }

    /**
     * Hacky function duplicating the tab in which we want to add sub menus
     *
     * @return boolean
     */
    public function fixTabOnInstall()
    {
        if (version_compare(_PS_VERSION_, self::psVersionWithFix, '>=')) {
            return true;
        }

        // Duplicate existing Theme tab for sub tree
        $themesTab = Tab::getInstanceFromClassName('AdminThemes');
        $newTab = clone($themesTab);
        $newTab->id = 0;
        $newTab->id_parent = $themesTab->id_parent;
        $newTab->class_name = $themesTab->class_name.'Parent';
        $newTab->save();

        // Second save in order to get the proper position (add() resets it)
        $newTab->position = $themesTab->position;
        $newTab->save();

        $themesTab->id_parent = $newTab->id;
        $themesTab->save();
        return true;
    }

    /**
     * Hacky function deleting the tab previously added
     * 
     * @return boolean
     */
    public function fixTabOnUninstall()
    {
        if (version_compare(_PS_VERSION_, self::psVersionWithFix, '>=')) {
            return true;
        }

        // Duplicate existing Theme tab for sub tree
        $themesTabParent = Tab::getInstanceFromClassName('AdminThemesParent');
        $themesTab = Tab::getInstanceFromClassName('AdminThemes');
        if (!$themesTabParent || !$themesTab) {
            return true;
        }
        $themesTab->position = $themesTabParent->position;
        $themesTab->id_parent = $themesTabParent->id_parent;
        $themesTabParent->delete();

        $themesTab->save();
        return true;
    }
}