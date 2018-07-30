<?php
namespace SlimTest\DM;

use SlimTest\DSD\EntityDataStructureDefinition as EDSD;
use SlimTest\DM\PDO_Connection;

/**
 * Rest-style data management with PDO
 */
class PdoDataManager extends RdbDataManager
{
    public function __construct($table, $dataDef, $options=null)
    {
        parent::__construct($table, $dataDef, $options);
        $this->dbc = PDO_Connection::getConnection();
    }

    public function getList()
    {
        $q = "SELECT * FROM {$this->table}";
        $qp = $this->dbc->prepare($q);
        try {
            @$qp->execute();
        } catch (PDOException $e) {
            print "Query error: " . $e->getMessage() . "\n";
            return ['error'=>true, 'description'=>$e->getMessage()];
        }
        return $qp->fetchAll();
    }

    public function getByID($id)
    {
        $ds = $this->dataDef;
        $q = "SELECT * FROM {$this->table} WHERE ".$this->dataDef->getIDField()." = :id";

        $qp = $this->dbc->prepare($q);
        $qp->bindValue(":id", $id, \PDO::PARAM_STR);
        try {
            @$qp->execute();
        } catch (PDOException $e) {
            print "Query error: " . $e->getMessage() . "\n";
            return ['error'=>true, 'description'=>$e->getMessage()];
        }
        return $qp->fetchAll()[0];
    }

    public function getRow($condition)
    {
        $ds = $this->dataDef;
        $q = "SELECT * FROM {$this->table} WHERE ";
        $where = [];
        foreach ($condition as $field => $value) {
            $where[] = "{$field} = :{$field}";
        }
        $q .= implode(' AND ', $where);
        $qp = $this->dbc->prepare($q);

        foreach ($condition as $field => $value) {
            $pdo_param = $this->paramTypeMap($ds->getFieldType($field));
            $qp->bindValue(":{$field}", $value, $pdo_param);
        }

        try {
            $qp->execute();
        } catch (PDOException $e) {
            print "Query error: " . $e->getMessage() . "\n";
            return ['error'=>true, 'description'=>$e->getMessage()];
        }

        return $qp->fetchAll()[0];
    }

    public function add($row)
    {
        if ($this->options && $this->options['beforeSave']) {
            $row = $this->options['beforeSave']($row);
        }
        if ($this->options && $this->options['beforeAdd']) {
            $row = $this->options['beforeAdd']($row);
        }

        $field_list = implode(', ', array_keys($row));
        $q = "INSERT INTO {$this->table} ({$field_list}) VALUES (";
        $params = [];
        foreach ($row as $field => $value) {
            $params[] = ':'.$field;
        }
        $q .= implode(', ', $params).')';
        $qp = $this->dbc->prepare($q);

        foreach ($row as $field => $value) {
            $pdo_param = $this->paramTypeMap($this->dataDef->getFieldType($field));
            $qp->bindValue(":{$field}", $value, $pdo_param);
        }

        $qp->execute();
    }

    public function update($row, $id) 
    {
        if ($this->options && $this->options['beforeSave']) {
            $row = $this->options['beforeSave']($row);
        }

        if (!$row) {
            return false;
        }
        $ds = $this->dataDef;

        $field_list = implode(', ', array_keys($row));
        $q = "UPDATE {$this->table} SET ";
        $set = [];
        foreach ($row as $field => $value) {
            $set[] = $field.'= :'.$field;
        }
        $q .= implode(', ', $set);
        $q .= " WHERE ".$ds->getIDField()." = :id";
        
        $qp = $this->dbc->prepare($q);

        foreach ($row as $field => $value) {
            $pdo_param = $this->paramTypeMap($ds->getFieldType($field));
            $qp->bindValue(":{$field}", $value, $pdo_param);
        }
        $qp->bindValue(":id", $id, \PDO::PARAM_STR);

        $qp->execute();
    }

    public function delete($id) 
    {
        $q = "DELETE FROM {$this->table} WHERE ".$this->dataDef->getIDField()." = :id";
        $qp = $this->dbc->prepare($q);
        $qp->bindValue(":id", $id, \PDO::PARAM_STR);
        $qp->execute();
    }

    private function paramTypeMap($ddType)
    {
        switch ($ddType) {
            case EDSD::FIELD_TYPE_INT:
                return \PDO::PARAM_INT;

            case EDSD::FIELD_TYPE_STRING:
                return \PDO::PARAM_STR;

            case EDSD::FIELD_TYPE_JSON:
                return \PDO::PARAM_STR;

            case EDSD::FIELD_TYPE_TEXT:
                return \PDO::PARAM_STR;

            case EDSD::FIELD_TYPE_FLOAT:
                return \PDO::PARAM_STR;

            case EDSD::FIELD_TYPE_FILE:
                return \PDO::PARAM_STR;

            case EDSD::FIELD_ID:
                return \PDO::PARAM_STR;
        }
    }
}
