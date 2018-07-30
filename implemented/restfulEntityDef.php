<?php
require_once dirname(__DIR__).'/src/EntityDataStructure.php';
require_once dirname(__DIR__).'/src/RestfulEntity.php';
require_once dirname(__DIR__).'/src/Permission.php';
require_once dirname(__DIR__).'/db/pdoDataManager.php';

require_once 'dataStructureDef.php';

use SlimTest\Entity\RestfulEntity;
use SlimTest\Entity\RestfulEntityManager;
use SlimTest\DM\PdoDataManager;
use SlimTest\Permission\Permission;
use SlimTest\Auth\Auth;

/**
 * Recipes management
 */
$recipeDataManagementOptions = [
    'beforeAdd' => function($row) {
        $row['user_id'] = Auth::getInstance()->getLoggedUID();
        return $row;
    }
];

RestfulEntityManager::addNew(new RestfulEntity(
    'recipes', 
    $recipeDataDef,
    new PdoDataManager('public."recipe"', $recipeDataDef, $recipeDataManagementOptions),
    Permission::PERM_ALLOWED_FOR_ALL
));


/**
 * User management
 */
$userDataManagementOptions = [
    'beforeSave' => function($row) {
        $row['pwd'] = md5($row['pwd']);
        return $row;
    }
];

RestfulEntityManager::addNew(new RestfulEntity(
    'users', 
    $userDataDef, 
    new PdoDataManager('public."user"', $userDataDef, $userDataManagementOptions),
    Permission::PERM_ALLOWED_FOR_ADMIN
));
