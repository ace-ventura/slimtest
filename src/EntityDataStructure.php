<?php
namespace SlimTest\DSD;

// Логика отвязанная от конкретной реализации хранения
class EntityDataStructureDefinition
{
    private $map;
    private $required_field_list;
    private $id_field;

    const FIELD_TYPE_INT = 1;
    const FIELD_TYPE_FLOAT = 2;
    const FIELD_TYPE_STRING = 3;
    const FIELD_TYPE_FILE = 4;
    const FIELD_TYPE_TEXT = 5;
    const FIELD_TYPE_JSON = 6;
    const FIELD_ID = 7;

    public function __construct($map, $required_field_list) 
    {
        if (!($this->id_field = array_search(self::FIELD_ID, $map))) {
            throw new Exception('Must be one field defined as ID');
        }

        $this->map = $map;
        $this->required_field_list = $required_field_list;
    }

    public function checkRequired(array $row): bool
    {
        $intersect_count = count(array_intersect(array_keys($row), $this->required_field_list));
        if ($intersect_count === count($this->required_field_list)) {
            return true;
        } else {
            return false;
        }
    }

    public function checkInputFields(array $row): bool
    {
        $diff_count = count(array_diff(array_keys($row), array_keys($this->map)));
        return $diff_count === 0;
    }

    public function getFieldType(string $field): int
    {
        return $this->map[$field] ?: 0;
    }

    public function getRequiredList()
    {
        return $this->required_field_list;
    }

    public function getIDField()
    {
        return $this->id_field;
    }

    public function checkDataAgainstMap(array $row): bool
    {
        $intersect_count = count(array_intersect($this->map, array_keys($data)));
        if ($intersect_count === count($data)) {
            return true;
        } else {
            return false;
        }
    }

    public function getFileField()
    {
        return array_search(self::FIELD_TYPE_FILE, $this->map);
    }
}

/**
 * Entity data structure manager
 */
class EntityDSManager
{
    private static $data=[];
    public static function add(string $name, EntityDataStructureDefinition $edsd)
    {
        if ($name == '') {
            throw new Exception('Name cannot be empty');
        }
        if (self::$data[$name]) {
            throw new Exception('Data structure with name '.$name.' already exists');
        }
        self::$data[$name] = $edsd;
    }

    public static function getByName(string $name): EntityDataStructureDefinition
    {
        if (!self::$data[$name]) {
            throw new Exception('Data structure with name '.$name.' not found');
        }
        return self::$data[$name];
    }
}