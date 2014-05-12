<?php

namespace CL\Luna\MassAssign;

use CL\Luna\Mapper\AbstractNode;
use CL\Luna\Mapper\AbstractLink;
use CL\Luna\Mapper\LinkOne;
use CL\Luna\Mapper\LinkMany;
use Closure;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class UnsafeData
{
    protected $data;

    public function getData()
    {
        return $this->data;
    }

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function assignTo(AbstractNode $node)
    {
        $this->setDataNode($node, $this->data, function (AbstractLink $link, array $data) {

            $assign = function(AbstractNode $node, array $data) {
                $data = new UnsafeData($data);
                $data->assignTo($node);
            };

            if ($link instanceof LinkOne) {
                $this->setDataLinkOne($link, $data, $assign);
            } elseif ($link instanceof LinkMany) {
                $this->setDataLinkMany($link, $data, $assign);
            }
        });
    }

    public function setDataNode(AbstractNode $node, array $data, Closure $yield)
    {
        $rels = $node->getRepo()->getRels()->all();

        $relsData = array_intersect_key($data, $rels);
        $propertiesData = array_diff_key($data, $rels);

        $node->setProperties($propertiesData);

        foreach ($relsData as $relName => $relData) {
            $yield($node->getRepo()->loadLink($node, $relName), $relData);
        }
    }

    public function setDataLinkOne(LinkOne $link, array $data, Closure $yield)
    {
        $node = $link->getRel()->loadNodeFromData($data) ?: $link->get();

        $yield($node, $data);

        $link->set($node);
    }

    public function setDataLinkMany(LinkMany $link, array $data, Closure $yield)
    {
        $link->clear();

        foreach ($data as $itemData) {
            $node = $link->getRel()->loadNodeFromData($data) ?: $link->getRel()->getForeignRepo()->newInstance();

            $yield($node, $itemData);

            $link->add($node);
        }
    }
}
