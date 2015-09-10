<?php namespace PortOneFive\Essentials\Eloquent;

use PortOneFive\Essentials\Eloquent\Relations\HasMeta;

trait HasMetaTrait {

    /**
     * Define a one-to-many has-meta relationship.
     *
     * @param  string $related
     * @param  string $foreignKey
     * @param  string $localKey
     *
     * @param string  $metaKeyColumn
     * @param string  $metaValueColumn
     *
     * @return HasMeta
     */
    public function hasMeta(
        $related,
        $foreignKey = null,
        $localKey = null,
        $metaKeyColumn = 'key',
        $metaValueColumn = 'value'
    ) {
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = new $related;

        $localKey = $localKey ?: $this->getKeyName();

        return new HasMeta(
            $instance->newQuery(),
            $this,
            $instance->getTable() . '.' . $foreignKey,
            $localKey,
            $metaKeyColumn,
            $metaValueColumn
        );
    }
}
