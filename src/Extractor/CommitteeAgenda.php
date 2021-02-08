<?php
namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class CommitteeAgenda implements ExtractionInterface, IdentityInterface
{
    private string $id;
    private DOMElement $object;

    public function populate(DOMElement $object): self
    {
        $this->object = $object;
        return $this;
    }

    /**
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
    public function extract(): array
    {
        if (! $this->object->hasAttribute('númer')) {
            throw new Extractor\Exception('Missing [{númer}] value', $this->object);
        }

        $this->setIdentity((int) $this->object->getAttribute('númer'));

        $issueId = $this->object->getElementsByTagName('mál')?->item(0)?->getAttribute('málsnúmer');
        $title = $this->object->getElementsByTagName('heiti')?->item(0)?->nodeValue;

        return [
            'issue_id' => $issueId ? (int) $issueId : null,
            'title' => $title ? trim($title) : null
        ];
    }

    public function setIdentity(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getIdentity(): string
    {
        return $this->id;
    }
}
