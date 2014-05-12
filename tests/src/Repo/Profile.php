<?php

namespace CL\Luna\Test\Repo;

use CL\Luna\Model\AbstractRepo;
use CL\Luna\Field;
use CL\Luna\Rel;
use CL\Carpo\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Profile extends AbstractRepo {

    private static $instance;

    /**
     * @return Profile
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new Profile('CL\Luna\Test\Model\Profile');
        }

        return self::$instance;
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
                new Rel\BelongsTo('user', $this, User::get()),
            ])
            ->setAsserts([
                new Assert\Present('name'),
            ]);
    }

}
