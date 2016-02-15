<?php

namespace system\engine;

use \vendor\DB\DB;

abstract class HF_Model {

    public $id = null;
    public static function create($data) {

        $obj = new static();
        $function = new \ReflectionClass(get_called_class());
        $table = strtolower($function->getShortName());

        foreach(DB::getColumns($table) as $column) {
            if (isset($data[$column])) {
                $obj->$column = $data[$column];
            }
        }
        return $obj;
    }

    public function save() {
        $fieldMap = [];
        $function = new \ReflectionClass(get_called_class());
        $table = strtolower($function->getShortName());
        foreach(DB::getColumns($table) as $column) {
            $fieldMap[$column] = $this->$column;
        }
        if ($fieldMap["id"] == null) {
            DB::insert($table, $fieldMap);
        } else {
            $updateFields = $fieldMap;
            unset($updateFields["id"]);
            DB::update($table, $updateFields, $fieldMap["id"]);
        }
    }

    public function update($data) {
        $function = new \ReflectionClass(get_called_class());
        $table = strtolower($function->getShortName());
        foreach(DB::getColumns($table) as $column) {
            if ($column == "id" || strpos($column, "_id") !== false) {
                continue; // Don't allow to override id
            }
            if (isset($data[$column])) {
                $this->$column = $data[$column];
            }
        }
        return $this;
    }

    public function delete() {
        $function = new \ReflectionClass(get_called_class());
        $table = strtolower($function->getShortName());
        if ($this->id) {
            DB::query("DELETE FROM $table WHERE id = " . $this->id);
        }
    }

    public function deleteRelated($tables = []) {
        $function = new \ReflectionClass(get_called_class());
        $table = strtolower($function->getShortName());
        foreach($tables as $relatedTable) {
            DB::query("DELETE FROM $relatedTable WHERE $table" . "_id = " . $this->id);
        }
    }

    public static function getByField($field, $value) {
        $function = new \ReflectionClass(get_called_class());
        $table = strtolower($function->getShortName());
        $fields = implode(", ", DB::getColumns($table));
        return DB::fetchObject("SELECT $fields FROM $table WHERE $field = ?", get_called_class(), [$value]);
    }

}