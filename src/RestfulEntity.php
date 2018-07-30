<?php
namespace SlimTest\Entity;

use SlimTest\DSD\EntityDataStructureDefinition as EDSD;
use SlimTest\DM\IDataManager;
use Slim\App;

class RestfulEntity
{
    private $name;
    private $dataDef;
    private $dataManager;
    private $permRequired;

    public function __construct(string $name, EDSD $dataDef, IDataManager $dataManager, int $permRequired) 
    {
        $this->name = $name;
        $this->dataDef = $dataDef;
        $this->dataManager = $dataManager;
        $this->permRequired = $permRequired;

        $dataManager->setDataStructure($dataDef);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getList()
    {
        return $this->dataManager->getList();
    }

    public function getByID($id)
    {
        return $this->dataManager->getByID($id);
    }

    public function add($input)
    {
        if ($this->dataDef->checkRequired($input)) {
            $this->dataManager->add($input);
            return ['status' => 'OK', 'message' => 'Record added'];
        } else {
            return ['status' => 'fail', 'message' => 'Some of required fields are empty'];
        }
        //return $this->dataManager->getByID($id);
    }

    public function delete($id)
    {
        $this->dataManager->delete($id);
        return ['status' => 'OK', 'message' => 'Record deleted'];
    }

    public function update($input, $id)
    {
        if ($this->dataDef->checkInputFields($input)) {
            $this->dataManager->update($input, $id);
            return ['status' => 'OK', 'message' => 'Record updated'];
        } else {
            return ['status' => 'fail', 'message' => 'Some of fields presented in input data are not presented in data structure definition'];
        }

    }

    public function getRequiredPermission()
    {
        return $this->permRequired;
    }

    public function getDataDef()
    {
        return $this->dataDef;
    }
}


class RestfulEntityManager
{
    private static $ents = [];

    public static function addNew(RestfulEntity $ent)
    {
        self::$ents[$ent->getName()] = $ent;
    }

    public static function getList()
    {
        return self::$ents;
    }

    public static function getByName($name)
    {
        return (self::$ents[$name] ?: false);
    }
}