<?php namespace CL\Luna\Rel;

use CL\Luna\Schema\Schema;
use CL\Luna\DB\SelectSchema;
use CL\Luna\Util\Arr;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractEagerLoaded extends AbstractRel
{
	public static function eagerLoad(Schema $schema, $parents, $rels)
	{
		if ($parents)
		{
			foreach ($rels as $relName => $childRelNames)
			{
				$rel = $schema->getRel($relName);

				if ( ! ($rel instanceof AbstractEagerLoaded))
				{
					throw \Exception(sprintf('Rel %s could not be eager loaded', $relName));
				}

				if (($children = $rel->eagerLoadedChildren($parents)))
				{
					$rel->setEagerLoaded($parents, $children);

					if ($childRelNames)
					{
						self::eagerLoad($rel->getForeignSchema(), $parents, $childRelNames);
					}
				}
			}
		}
	}

	public function eagerLoadedChildren($parents)
	{
		$query = new SelectSchema($this->getForeignSchema());

		if ($this->scopeEagerLoaded($query, $parents))
		{
			return $query->execute()->fetchAll();
		}
		else
		{
			return [];
		}
	}

	abstract public function scopeEagerLoaded(SelectSchema $select, array $parents);

	abstract public function setEagerLoaded(array $parents, array $children);

}
