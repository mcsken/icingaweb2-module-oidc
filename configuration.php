<?php

/** @var \Icinga\Application\Modules\Module $this */


$section = $this->menuSection(N_('Oidc'), [
    'permission' => 'oidc',
    'url' => 'oidc/providers',
    'icon' => 'img/oidc/openid32.png',
    'priority' => 910
]);


?>

<?php

$this->provideConfigTab('backend', array(
    'title' => $this->translate('Configure the database backend'),
    'label' => $this->translate('Backend'),
    'url' => 'database/backend'
));

?>
<?php
$section->add(N_('Provider'))
    ->setUrl('oidc/providers')
    ->setPermission('oidc/provider')
    ->setPriority(10);

$this->providePermission(
    'oidc/filter/providers',
    $this->translate('Allow only access access to the Providers that match the filter')
);

$this->provideRestriction(
    'oidc/filter/providers',
    $this->translate('Restrict access to the Providers that match the filter')
);

$section->add(N_('User'))
    ->setUrl('oidc/users')
    ->setPermission('oidc/user')
    ->setPriority(20);


$section->add(N_('Group'))
    ->setUrl('oidc/groups')
    ->setPermission('oidc/group')
    ->setPriority(30);
?>

<?php

$section->add(N_('Files'))
    ->setUrl('oidc/file')
    ->setPermission('oidc/file')
    ->setPriority(30);


$this->providePermission(
    'oidc/file',
    $this->translate('Allow the user to list files')
);

$this->providePermission(
    'oidc/file/upload',
    $this->translate('Allow uploading files')
);

$this->providePermission(
    'oidc/file/view',
    $this->translate('Allow viewing files')
);

$this->providePermission(
    'oidc/file/download',
    $this->translate('Allow download files')
);

$this->providePermission(
    'oidc/file/delete',
    $this->translate('Allow deleting files')
);

$this->provideUserBackend('oidc',\Icinga\Module\Oidc\Backend\OidcUserBackend::class);
$this->provideUserGroupBackend('oidc',\Icinga\Module\Oidc\Backend\OidcUserGroupBackend::class);

?>
