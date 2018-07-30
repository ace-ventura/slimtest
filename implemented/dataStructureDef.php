<?php
require dirname(__DIR__).'/src/EntityDataStructure.php';

use SlimTest\DSD\EntityDataStructureDefinition as EDSD;
use SlimTest\DSD\EntityDSManager;

$sessionDataDef = new EDSD(
    [
        'session_id' => EDSD::FIELD_ID,
        'user_id'   => EDSD::FIELD_TYPE_INT,
    ], [
        'session_id', 
        'user_id'
    ]
);
EntityDSManager::add('session', $sessionDataDef);

$recipeDataDef = new EDSD(
    [
        'recipe_id' => EDSD::FIELD_ID,
        'title'     => EDSD::FIELD_TYPE_STRING,
        'text'      => EDSD::FIELD_TYPE_TEXT,
        'picture'   => EDSD::FIELD_TYPE_FILE,
        'composition' => EDSD::FIELD_TYPE_JSON,
        'user_id'   => EDSD::FIELD_TYPE_INT,
    ], [
        'title'
    ]
);
EntityDSManager::add('recipe', $recipeDataDef);

$userDataDef = new EDSD(
    [
        'user_id'   => EDSD::FIELD_ID,
        'full_name' => EDSD::FIELD_TYPE_STRING,
        'login'     => EDSD::FIELD_TYPE_STRING,
        'pwd'       => EDSD::FIELD_TYPE_STRING,
    ], [
        'login', 
        'pwd'
    ]
);
EntityDSManager::add('user', $userDataDef);
