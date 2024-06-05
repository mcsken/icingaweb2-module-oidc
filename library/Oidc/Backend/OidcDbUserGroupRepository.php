<?php
/* Icinga Web 2 | (c) 2014 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Oidc\Backend;

use Icinga\Exception\NotFoundError;
use Icinga\Repository\DbRepository;
use Icinga\Repository\RepositoryQuery;
use Icinga\User;

class OidcDbUserGroupRepository extends DbRepository
{
    /**
     * The query columns being provided
     *
     * @var array
     */
    protected $queryColumns = array(
        'group' => array(
            'group_id'      => 'g.id',
            'group'         => 'g.name COLLATE utf8mb4_general_ci',
            'group_name'    => 'g.name',
            'provider_id'   => 'g.provider_id',
            'parent'        => 'g.parent',
            'created_at'    => 'g.ctime',
            'last_modified' => 'g.mtime'
        ),
        'group_membership' => array(
            'group_id'      => 'gm.group_id',
            'user'          => 'gm.username COLLATE utf8mb4_general_ci',
            'user_name'     => 'gm.username',
            'created_at'    => 'gm.ctime',
            'provider_id'   => 'gm.provider_id',

            'last_modified' => 'gm.mtime'
        )
    );

    /**
     * The table aliases being applied
     *
     * @var array
     */
    protected $tableAliases = array(
        'group'             => 'g',
        'group_membership'  => 'gm'
    );

    protected function init()
    {
        $this->ds->setTablePrefix('tbl_');
    }

    /**
     * The statement columns being provided
     *
     * @var array
     */
    protected $statementColumns = array(
        'group' => array(
            'group_id'      => 'id',
            'group_name'    => 'name',
            'parent'        => 'parent',
            'created_at'    => 'ctime',
            'last_modified' => 'mtime'
        ),
        'group_membership' => array(
            'group_id'      => 'group_id',
            'group_name'    => 'group_id',
            'user_name'     => 'username',
            'created_at'    => 'ctime',
            'last_modified' => 'mtime'
        )
    );

    /**
     * The columns which are not permitted to be queried
     *
     * @var array
     */
    protected $blacklistedQueryColumns = array('group', 'user');

    /**
     * The search columns being provided
     *
     * @var array
     */
    protected $searchColumns = array('group', 'user');

    /**
     * The value conversion rules to apply on a query or statement
     *
     * @var array
     */
    protected $conversionRules = array(
        'group'             => array(
            'parent'        => 'group_id'
        ),
        'group_membership'  => array(
            'group_name'    => 'group_id'
        )
    );


    /**
     * Initialize this repository's filter columns
     *
     * @return  array
     */
    protected function initializeFilterColumns()
    {
        $userLabel = t('Username') . ' ' . t('(Case insensitive)');
        $groupLabel = t('User Group') . ' ' . t('(Case insensitive)');
        return array(
            $userLabel          => 'user',
            t('Username')       => 'user_name',
            $groupLabel         => 'group',
            t('User Group')     => 'group_name',
            t('Parent')         => 'parent',
            t('Created At')     => 'created_at',
            t('Last modified')  => 'last_modified'
        );
    }


    /**
     * Return the groups the given user is a member of
     *
     * @param   User    $user
     *
     * @return  array
     */
    public function getMemberships(User $user, $provider_id=null)
    {
        $groupQuery = $this->ds
            ->select()
            ->from(
                array('g' => $this->prependTablePrefix('group')),
                array(
                    'group_name'    => 'g.name',
                    'parent_name'   => 'gg.name'
                )
            )->joinLeft(
                array('gg' => $this->prependTablePrefix('group')),
                'g.parent = gg.id',
                array()
            );

        $groups = array();
        foreach ($groupQuery as $group) {
            $groups[$group->group_name] = $group->parent_name;
        }

        $membershipQuery = $this
            ->select()
            ->from('group_membership', array('group_name'))
            ->where('user_name', $user->getUsername());
        if($provider_id !== null){
            $membershipQuery = $membershipQuery->where('provider_id',$provider_id);
        }

        $memberships = array();
        foreach ($membershipQuery as $membership) {
            $memberships[] = $membership->group_name;
            $parent = $groups[$membership->group_name];
            while ($parent !== null) {
                $memberships[] = $parent;
                // Usually a parent is an existing group, but since we do not have a constraint on our table..
                $parent = isset($groups[$parent]) ? $groups[$parent] : null;
            }
        }

        return $memberships;
    }

    /**
     * Return the name of the backend that is providing the given user
     *
     * @param   string  $username   Currently unused
     *
     * @return  null|string     The name of the backend or null in case this information is not available
     */
    public function getUserBackendName($username)
    {
        return null; // TODO(10373): Store this to the database when inserting and fetch it here
    }

    /**
     * Join group into group_membership
     *
     * @param   RepositoryQuery     $query
     */
    protected function joinGroup(RepositoryQuery $query)
    {
        $query->getQuery()->join(
            $this->requireTable('group'),
            'gm.group_id = g.id',
            array()
        );
    }

    /**
     * Join group_membership into group
     *
     * @param   RepositoryQuery     $query
     */
    protected function joinGroupMembership(RepositoryQuery $query)
    {
        $query->getQuery()->joinLeft(
            $this->requireTable('group_membership'),
            'g.id = gm.group_id',
            array()
        )->group('g.id');
    }

    /**
     * Fetch and return the corresponding id for the given group's name
     *
     * @param   string|array    $groupName
     *
     * @return  int
     *
     * @throws  NotFoundError
     */
    protected function persistGroupId($groupName)
    {
        if (empty($groupName) || is_numeric($groupName)) {
            return $groupName;
        }

        if (is_array($groupName)) {
            if (is_numeric($groupName[0])) {
                return $groupName; // In case the array contains mixed types...
            }

            $groupIds = $this->ds
                ->select()
                ->from($this->prependTablePrefix('group'), array('id'))
                ->where('name', $groupName)
                ->fetchColumn();
            if (empty($groupIds)) {
                throw new NotFoundError('No groups found matching one of: %s', implode(', ', $groupName));
            }

            return $groupIds;
        }

        $groupId = $this->ds
            ->select()
            ->from($this->prependTablePrefix('group'), array('id'))
            ->where('name', $groupName)
            ->fetchOne();
        if ($groupId === false) {
            throw new NotFoundError('Group "%s" does not exist', $groupName);
        }

        return $groupId;
    }

}
