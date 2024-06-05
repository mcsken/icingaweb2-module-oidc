<?php
/* Icinga Web 2 | (c) 2015 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Oidc\Backend;

use Icinga\Repository\DbRepository;

class OidcDbUserRepository extends DbRepository
{
    /**
     * The query columns being provided
     *
     * @var array
     */
    protected $queryColumns = array(
        'user' => array(
            'user'          => 'name COLLATE utf8mb4_general_ci',
            'user_name'     => 'name',
            'provider_id'     => 'provider_id',
            'is_active'     => 'active',
            'created_at'    => 'ctime',
            'last_modified' => 'mtime'
        )
    );

    /**
     * The statement columns being provided
     *
     * @var array
     */
    protected $statementColumns = array(
        'user' => array(
            'created_at'    => 'ctime',
            'last_modified' => 'mtime'
        )
    );

    /**
     * The columns which are not permitted to be queried
     *
     * @var array
     */
    protected $blacklistedQueryColumns = array('user');

    /**
     * The search columns being provided
     *
     * @var array
     */
    protected $searchColumns = array('user');

    /**
     * The default sort rules to be applied on a query
     *
     * @var array
     */
    protected $sortRules = array(
        'user_name' => array(
            'columns'   => array(
                'is_active desc',
                'user_name'
            )
        )
    );


    /**
     * Initialize this database user backend
     */
    protected function init()
    {
        $this->ds->setTablePrefix('tbl_');
    }

    /**
     * Initialize this repository's filter columns
     *
     * @return  array
     */
    protected function initializeFilterColumns()
    {
        $userLabel = t('Username') . ' ' . t('(Case insensitive)');
        return array(
            $userLabel          => 'user',
            t('Username')       => 'user_name',
            t('Active')         => 'is_active',
            t('Created at')     => 'created_at',
            t('Last modified')  => 'last_modified'
        );
    }



}
