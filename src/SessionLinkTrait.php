<?php

namespace Harp\Harp;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
trait SessionLinkTrait
{
    /**
     * @var string
     */
    private $sessionInstanceId;

    public function setSession(Session $session)
    {
        $this->sessionInstanceId = $session->getInstanceId();

        return $this;
    }

    public function getSession()
    {
        return Session::getInstance($this->sessionInstanceId);
    }
}
