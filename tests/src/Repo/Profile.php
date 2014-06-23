<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\AbstractRepo;
use Harp\Harp\Field;
use Harp\Harp\Rel;
use Harp\Validate\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Profile extends AbstractRepo {

    public function initialize()
    {
        $this
            ->setModelClass('Harp\Harp\Test\Model\Profile')
            ->addRels([
                new Rel\BelongsTo('user', $this, User::get()),
            ])
            ->addAsserts([
                new Assert\Present('name'),
            ]);
    }

}
