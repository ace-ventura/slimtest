<?php
namespace SlimTest\Permission;

class Permission
{
    const PERM_LEVEL_NORMAL = 1;
    const PERM_LEVEL_ADMIN = 2;

    const PERM_ALLOWED_FOR_ALL = 1;
    const PERM_ALLOWED_FOR_NORMAL = 2;
    const PERM_ALLOWED_FOR_ADMIN = 4;

    // Policyusing not implemented yet
    const PERM_POLICY_READ_ALL = 8;
    const PERM_POLICY_READ_OWN = 16;
    const PERM_POLICY_WRITE_ALL = 32;
    const PERM_POLICY_WRITE_OWN = 64;

    static $permissionPolicy;
}