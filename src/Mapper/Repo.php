<?php namespace CL\Luna\Mapper;

use SplObjectStorage;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Repo
{
    private static $repo;

    public static function get()
    {
        if (! self::$repo) {
            self::$repo = new Repo();
        }

        return self::$repo;
    }

    protected $identityMap;
    protected $linkMap;

    public function __construct()
    {
        $this->identityMap = new IdentityMap();
        $this->linkMap = new LinkMap();
        $this->persist = new PersistQueue($this->linkMap);
    }

    public function getIdentityMap()
    {
        return $this->identityMap;
    }

    public function getPersist()
    {
        return $this->persist;
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
            return $this->identityMap->get($node);
        }, $nodes);
    }

    public function persist(AbstractNode $node)
    {
        $nodes = $this->linkMap->getRecursive($node);

        $this->linkMap->updateNodes($nodes);

        $nodes->addAll($this->linkMap->getRecursive($node));

        $this->persist
            ->add($nodes)
            ->flush();
    }

    public function loadLink(AbstractNode $node, $linkName)
    {
        $links = $this->linkMap->get($node);

        if (! $links->has($linkName)) {
            $rel = $node->getSchema()->getRel($linkName);

            $this->loadRel($rel, [$node]);
        }

        return $links->get($linkName);
    }

    public function loadRel(RelInterface $rel, array $nodes)
    {
        $foreign = $rel->loadForeignNodes($nodes);

        $foreign = $this->getCanonicalArray($foreign);

        $rel->loadForeignLinks(
            $nodes,
            $foreign,
            function($model, $link) use ($rel) {
                $this->linkMap->get($model)->add($rel->getName(), $link);
            }
        );

        return $foreign;
    }
}
