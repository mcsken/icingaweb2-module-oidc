<?php

/* Originally from Icinga Web 2 Reporting Module (c) Icinga GmbH | GPLv2+ */
/* icingaweb2-module-scaffoldbuilder 2023 | GPLv2+ */

namespace Icinga\Module\Oidc\Controllers;


use Icinga\Application\Config;
use Icinga\Module\Oidc\Forms\ModuleconfigForm;
use Icinga\Web\Controller;

class ModuleconfigController extends Controller
{


    public function indexAction()
    {
        $this->assertPermission("config/oidc");
        $form = (new ModuleconfigForm())
            ->setIniConfig(Config::module('oidc', "config"));

        $form->handleRequest();

        $this->view->tabs = $this->Module()->getConfigTabs()->activate('config/moduleconfig');
        $this->view->form = $form;
    }


    public function createTabs()
    {
        $tabs = $this->getTabs();

        $tabs->add('oidc/config', [
            'label' => $this->translate('Configure Oidc'),
            'url' => 'oidc/config'
        ]);

        return $tabs;

    }

}