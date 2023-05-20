<?php

namespace PinaCMS\SQL;

trait ResourceTrait
{

    abstract public function getTable();

    public function makeInsertTrigger($type, $parentIdField = null)
    {
        $fields = $this->getResourceFields($parentIdField);
        $values = $this->getResourceValues($parentIdField);
        return [
            $this->getTable(),
            'before insert',
            "IF (NEW.id IS NOT NULL AND NEW.id > 0) THEN"
            ." INSERT INTO resource (id, `type`,".implode(',', $fields).") SELECT NEW.id, '$type',".implode(',', $values).";"
            ." ELSE"
            ." INSERT INTO resource (`type`,".implode(',', $fields).") SELECT '$type',".implode(',', $values).";SET NEW.id=LAST_INSERT_ID();"
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
        $fields = ['title', 'slug', 'enabled'];
        if ($parentIdField) {
            $fields[] = 'parent_id';
        }
        return $fields;
    }

    private function getResourceValues($parentIdField = null)
    {
        $values = ['NEW.title', 'NEW.slug', 'NEW.enabled'];
        if ($parentIdField) {
            $values[] = 'NEW.' . $parentIdField;
        }
        return $values;
    }

}