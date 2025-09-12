<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Exceptions\CascadeSoftDeleteException;

trait CascadeSoftDeletes
{
    protected static function bootCascadeSoftDeletes()
    {
        static::deleting(function ($model) {

            $model->validateCascadingSoftDelete();

            $model->runCascadingDeletes();
        });
    }

    protected function validateCascadingSoftDelete()
    {

        if (!$this->implementsSoftDeletes()) {
            return throw CascadeSoftDeleteException::softDeleteNotImplemented(get_called_class());
        }
        else if ($invalidCascadingRelationships = $this->hasInvalidCascadingRelationships()) {
            return throw CascadeSoftDeleteException::invalidRelationships($invalidCascadingRelationships);
        }
    }

    protected function runCascadingDeletes()
    {
        foreach ($this->getActiveCascadingDeletes() as $relationship) {

            $this->cascadeSoftDeletes($relationship);
        }
    }

    protected function cascadeSoftDeletes($relationship)
    {
        $delete = $this->forceDeleting ? 'forceDelete' : 'delete';

        foreach ($this->{$relationship}()->get() as $model) {
            $model->{$delete}();
        }
    }

    protected function implementsSoftDeletes()
    {
        return method_exists($this, 'runSoftDelete');
    }

    protected function hasInvalidCascadingRelationships()
    {
        return array_filter($this->getCascadingDeletes(), function ($relationship) {
            return !method_exists($this, $relationship) || !$this->{$relationship}() instanceof Relation;
        });
    }

    protected function getCascadingDeletes()
    {
        return isset($this->cascadeDeletes) ? (array)$this->cascadeDeletes : [];
    }

    protected function getActiveCascadingDeletes()
    {
        return array_filter($this->getCascadingDeletes(), function ($relationship) {
            return $this->{$relationship}()->exists();
        });
    }
}
