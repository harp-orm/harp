<?php

namespace Harp\Harp\Query;

use Harp\Harp\Repo;
use Harp\Query\SQL\SQL;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Select extends \Harp\Query\Select {

    use JoinRelTrait;

    /**
     * @var Repo
     */
    private $repo;

    public function __construct(Repo $repo)
    {
        $this->repo = $repo;

        $table = $this->getDb()->escapeName($repo->getTable());

        $this
            ->from($repo->getTable())
            ->column(new SQL("{$table}.*"));

        parent::__construct($repo->getDbInstance());
    }

    /**
     * @return Repo
     */
    public function getRepo()
    {
        return $this->repo;
    }
}
