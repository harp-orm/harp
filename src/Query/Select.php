<?php

namespace Harp\Harp\Query;

use Harp\Harp\AbstractDbRepo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Select extends \Harp\Query\Select {

    use JoinRelTrait;

    /**
     * @var AbstractDbRepo
     */
    private $repo;

    public function __construct(AbstractDbRepo $repo)
    {
        $this->repo = $repo;
        $this
            ->from($repo->getTable())
            ->column($repo->getTable().'.*');

        parent::__construct($repo->getDbInstance());
    }

    /**
     * @return AbstractDbRepo
     */
    public function getRepo()
    {
        return $this->repo;
    }
}
