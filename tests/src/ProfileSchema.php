<?php

namespace CL\Luna\Test;

use CL\Luna\Model\Schema;
use CL\Luna\Model\SchemaTrait;
use CL\Luna\Field;
use CL\Luna\Rel;
use CL\Carpo\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class ProfileSchema extends Schema {

    use SchemaTrait;

    public function __construct()
    {
        parent::__construct('CL\Luna\Test\Profile');
    }

    public function initialize()
    {
        $this
            ->setFields([
                new Field\Integer('id'),
                new Field\String('firstName'),
                new Field\String('lastName'),
                new Field\Integer('userId'),
            ])
            ->setRels([
                new Rel\BelongsTo('user', $this, UserSchema::get()),
            ])
            ->setAsserts([
                new Assert\Present('name'),
            ]);
    }

}
