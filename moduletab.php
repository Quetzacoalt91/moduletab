<?php

class ModuleTab extends Module
{
    public $tabs = array(
        array(
            'name' => 'Tab 1', // One name for all langs
            'class_name' => 'AdminTabOne',
            'visible' => true,
            'parent_class_name' => 'AdminThemesParent',
        ),
        array(
            'name' => 'Tab 2', // One name for all langs
            'class_name' => 'AdminTabTwo',
            'visible' => true,
            'parent_class_name' => 'AdminThemesParent',
    ));

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

    public function install()
    {
        return parent::install() && $this->fixTabOnInstall();
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->fixTabOnUninstall();
    }

    public function fixTabOnInstall()
    {
        if (version_compare(_PS_VERSION_, '1.7.4.0', '>=')) {
            return true;
        }

        // Duplicate existing Theme tab for sub tree
        $themesTab = Tab::getInstanceFromClassName('AdminThemes');
        $newTab = clone($themesTab);
        $newTab->id = 0;
        $newTab->id_parent = $themesTab->id_parent;
        $newTab->class_name = $themesTab->class_name.'Parent';
        $newTab->save();

        $themesTab->id_parent = $newTab->id;
        $themesTab->save();
        return true;
    }

    public function fixTabOnUninstall()
    {
        if (version_compare(_PS_VERSION_, '1.7.4.0', '>=')) {
            return true;
        }

        // Duplicate existing Theme tab for sub tree
        $themesTabParent = Tab::getInstanceFromClassName('AdminThemesParent');
        $themesTab = Tab::getInstanceFromClassName('AdminThemes');
        if (!$themesTabParent || !$themesTab) {
            return true;
        }
        $themesTab->id_parent = $themesTabParent->id_parent;

        $themesTab->save();
        $themesTabParent->delete();
        return true;
    }
}