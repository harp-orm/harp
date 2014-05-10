<?php

namespace CL\Luna\Mapper;

use SplObjectStorage;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class MainRepo
{
    private static $repo;

    public static function get()
    {
        if (! self::$repo) {
            self::$repo = new MainRepo();
        }

        return self::$repo;
    }

    protected $identityMap;
    protected $linkMap;

    public function __construct()
    {
        $this->identityMap = new IdentityMap();
        $this->linkMap = new LinkMap();
    }

    public function getIdentityMap()
    {
        return $this->identityMap;
    }

    public function getLinkMap()
    {
        return $this->linkMap;
    }

    public function getCanonicalNode(AbstractNode $node)
    {
        return $this->identityMap->get($node);
    }

    public function getCanonicalArray(array $nodes)
    {
        return array_map(function($node){
            return $this->getCanonicalNode($node);
        }, $nodes);
    }

    public function persist(AbstractNode $node)
    {
        $nodes = new LinkedNodes($this->linkMap);
        $nodes->add($node);

        Persist::nodes($nodes);
    }

    public function loadLink(AbstractNode $node, $linkName)
    {
        $links = $this->linkMap->get($node);

        if (! $links->has($linkName)) {
            $rel = $node->getRepo()->getRel($linkName);

            $this->loadRel($rel, [$node]);
        }

        return $links->get($linkName);
    }

    public function loadRel(AbstractRel $rel, array $nodes)
    {
        $foreign = $rel->loadForeignForNodes($nodes);

        $linked = $rel->linkToForeign($nodes, $foreign);

        foreach ($nodes as $node) {
            if ($linked->contains($node)) {
                $link = $rel->newLink($linked[$node], $this->identityMap);
            } else {
                $link = $rel->newVoidLink();
            }

            $this->linkMap->get($node)->add($rel->getName(), $link);
        }

        return $foreign;
    }
}
