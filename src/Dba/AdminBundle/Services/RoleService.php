<?php

namespace Dba\AdminBundle\Services;

use Dba\GameBundle\Entity\Player;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class RoleService
{
    private $roleHierarchy;

    /**
     * Constructor
     *
     * @param RoleHierarchyInterface $roleHierarchy
     */
    public function __construct(RoleHierarchyInterface $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }

    /**
     * isGranted
     *
     * @param string $role
     * @param Player $player
     * @return bool
     */
    public function isGranted($role, $player)
    {
        $role = new Role($role);
        foreach ($player->getRoles() as $playerRole) {
            if (in_array($role, $this->roleHierarchy->getReachableRoles(array(new Role($playerRole))))) {
                return true;
            }
        }

        return false;
    }
}
