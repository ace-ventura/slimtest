<?php
namespace SlimTest\DM;

use SlimTest\DSD\EntityDataStructureDefinition as EDSD;

/**
 * Common interface for data manipulation
 */
interface IDataManager
{
    public function add($row);
    public function update($row, $id);
    public function delete($id);
    public function getList();
    public function getByID($id);
    public function setDataStructure(EDSD $ds);
}

/**
 * Abstract class for data manipulation with relational database
 */
abstract class RdbDataManager implements IDataManager
{
    protected $dbc;
    protected $table;
    protected $dataDef;
    protected $options;

    public function __construct($table, $dataDef, $options=null)
    {
        $this->table = $table;
        $this->dataDef = $dataDef;
        if ($options) {
            $this->options = $options;
        }
    }

    public function setDataStructure(EDSD $ds)
    {
        $this->dataDef = $ds;
    }    

    public abstract function add($row);
    public abstract function update($row, $id);
    public abstract function delete($id);
    public abstract function getList();
    public abstract function getByID($id);
}
