<?php

namespace PinaCMS\SQL;

trait ResourceTrait
{

    abstract public function getTable();

    public function makeInsertTrigger($type, $parentIdField = null)
    {
        $fields = $this->getResourceFields();
        $setCondition = [];
        foreach ($fields as $field) {
            $setCondition[] = "$field=NEW.$field";
        }
        if ($parentIdField) {
            $setCondition[] = "parent_id=NEW.$parentIdField";
        }
        return [
            $this->getTable(),
            'before insert',
            "IF (NEW.id IS NOT NULL AND NEW.id > 0) THEN"
            ." INSERT INTO resource SET id=NEW.id, `type`='$type',".implode(',', $setCondition).";"
            ." ELSE"
            ." INSERT INTO resource SET `type`='$type',".implode(',', $setCondition).";SET NEW.id=LAST_INSERT_ID();"
            ." END IF;"
        ];
    }

    public function makeUpdateTrigger($parentIdField = null)
    {

        $fields = $this->getResourceFields();
        $ifCondition = [];
        $setCondition = [];
        foreach ($fields as $field) {
            $ifCondition[] = "(NEW.$field<>OLD.$field)";
            $setCondition[] = "$field=NEW.$field";
        }
        if ($parentIdField) {
            $ifCondition[] = "(NEW.$parentIdField <> OLD.$parentIdField)";
            $ifCondition[] = "(NEW.$parentIdField IS NOT NULL AND OLD.$parentIdField IS NULL)";
            $ifCondition[] = "(NEW.$parentIdField IS NULL AND OLD.$parentIdField IS NOT NULL)";
            $setCondition[] = "parent_id=NEW.$parentIdField";
        }

        return [
            $this->getTable(),
            'after update',
            "IF (".implode(' OR ', $ifCondition).") THEN UPDATE resource SET ".implode(',', $setCondition)." WHERE id=NEW.id;END IF;"
        ];
    }

    private function getResourceFields($parentIdField = null)
    {
        $fields = ['media_id', 'title', 'slug', 'enabled'];
        if ($parentIdField) {
            $fields[] = 'parent_id';
        }
        return $fields;
    }

}