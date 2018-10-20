<?php
namespace AlthingiAggregator\Extractor;

use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor\Exception as ModelException;

class CommitteeAgenda implements ExtractionInterface, IdentityInterface
{
    private $id;

    /**
     * @param \DOMElement $object
     * @return array
     * @throws ModelException
     * @todo extract <Gestir />
     *
     *  <dagskrárliður númer="1">
     *      <mál málsnúmer="1" löggjafarþing="145" málsflokkur="A">...</mál>
     *     <heiti>
     *         <![CDATA[ fjárlög 2016 ]]>
     *     </heiti>
     *     <Gestir/>
     *  </dagskrárliður>
     *
     *
     */
    public function extract(\DOMElement $object)
    {
        if (! $object->hasAttribute('númer')) {
            throw new ModelException('Missing [{númer}] value', $object);
        }

        $this->setIdentity((int) $object->getAttribute('númer'));

        $issueId = ($object->getElementsByTagName('mál')->item(0))
            ? (int) $object->getElementsByTagName('mál')->item(0)->getAttribute('málsnúmer')
            : null ;

        $title = ($object->getElementsByTagName('heiti')->item(0))
            ? trim($object->getElementsByTagName('heiti')->item(0)->nodeValue)
            : null ;

        return [
            'issue_id' => $issueId,
            'title' => $title
        ];
    }

    public function setIdentity($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getIdentity()
    {
        return $this->id;
    }
}
