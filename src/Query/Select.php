<?php

namespace Harp\Harp\Query;

use Harp\Harp\AbstractRepo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Select extends \Harp\Query\Select {

    use JoinRelTrait;

    /**
     * @var AbstractRepo
     */
    private $repo;

    public function __construct(AbstractRepo $repo)
    {
        $this->repo = $repo;
        $this
            ->from($repo->getTable())
            ->column($repo->getTable().'.*');

        parent::__construct($repo->getDbInstance());
    }

    /**
     * @return AbstractRepo
     */
    public function getRepo()
    {
        return $this->repo;
    }
}
