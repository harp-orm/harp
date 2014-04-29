<?php namespace CL\Luna\ModelQuery;

use CL\Luna\Model\Schema;
use CL\Luna\Util\Arr;
use CL\Luna\Util\Log;
use CL\Atlas\DB;
use InvalidArgumentException;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait ModelQueryTrait {

    protected $schema;

    public function setSchema(Schema $schema)
    {
        $this->schema = $schema;
        $this->db = DB::get($schema->getDb());

        return $this;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function getRel($name)
    {
        return $this->schema->getRel($name);
    }

    public function addToLog()
    {
        if (Log::getEnabled())
        {
            Log::add($this->humanize());
        }
    }

    public function whereKey($key)
    {
        return $this->where([$this->getSchema()->getPrimaryKey() => $key]);
    }

    public function whereKeys(array $keys)
    {
        return $this->where([$this->getSchema()->getPrimaryKey() => $keys]);
    }

    public function joinRels($rels)
    {
        $rels = Arr::toAssoc((array) $rels);

        $this->joinNestedRels($this->getSchema(), $rels, $this->getSchema()->getTable());

        return $this;
    }

    public function joinNestedRels($schema, array $rels, $parent)
    {
        foreach ($rels as $name => $childRels)
        {
            $rel = $schema->getRel($name);

            if (! $rel) {
                throw new InvalidArgumentException(
                    sprintf('Relation %s does not exist on %s when joining', $name, $schema->getName())
                );
            }

            $rel->joinRel($this, $parent);

            if ($childRels)
            {
                $this->joinNestedRels($rel->getForeignSchema(), $childRels, $name);
            }
        }
    }
}
