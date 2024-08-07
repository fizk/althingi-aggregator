<?php

namespace App\Extractor;

use App\Extractor;
use App\Lib\IdentityInterface;
use DOMElement;

class ParliamentarySessionAgenda implements ExtractionInterface, IdentityInterface
{
    private string $id;
    private DOMElement $object;

    public function populate(DOMElement $object): static
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @throws \App\Extractor\Exception
     * @todo This extractor goes out of the DOMElement to get the $parliamentary_session_id
     *      $object->ownerDocument->getElementsByTagName('þingfundur')->item(0)->getAttribute('númer');
     */
    public function extract(): array
    {
        if (! $this->object->hasAttribute('númer')) {
            throw new Extractor\Exception('Missing [{númer}] value', $this->object);
        }

        $this->setIdentity($this->object->getAttribute('númer'));

        $parliamentarySessionId = $this->object->ownerDocument->getElementsByTagName('þingfundur')?->item(0)?->getAttribute('númer');
        $parliamentarySessionId = $parliamentarySessionId === null
            ? -1
            : $parliamentarySessionId;

        $issue = $this->object->getElementsByTagName('mál')?->item(0);

        if (! $issue) {
            throw new Extractor\Exception('Missing [{mál}] element', $this->object);
        }

        $issueId = $issue->getAttribute('málsnúmer');
        $issueCategory = $issue->getAttribute('málsflokkur');
        $assemblyId = $issue->getAttribute('þingnúmer');

        if (empty($issueId) || empty($issueCategory) || empty($assemblyId)) {
            throw new Extractor\Exception('Missing [{málsnúmer}, {málsflokkur}, {þingnúmer}] value', $this->object);
        }

        $iterationType = $this->object->getElementsByTagName('umræða')?->item(0)?->getAttribute('tegund');
        $iterationContinue = $this->object->getElementsByTagName('umræða')?->item(0)?->getAttribute('framhald');
        $iterationComment = $this->object->getElementsByTagName('umræða')?->item(0)?->nodeValue;
        $comment = $this->object->getElementsByTagName('athugasemd')?->item(0)?->nodeValue;
        $commentType = $this->object->getElementsByTagName('athugasemd')?->item(0)?->getAttribute('tegund');
        $posedId = $this->object->getElementsByTagName('fyrirspyrjandi')?->item(0)?->getAttribute('id');
        $posed = $this->object->getElementsByTagName('fyrirspyrjandi')?->item(0)?->nodeValue;
        $answererId = $this->object->getElementsByTagName('til_svara')?->item(0)?->getAttribute('id');
        $answerer = $this->object->getElementsByTagName('til_svara')?->item(0)?->nodeValue;
        $instigatorId = $this->object->getElementsByTagName('málshefjandi')?->item(0)?->getAttribute('id');
        $instigator = $this->object->getElementsByTagName('málshefjandi')?->item(0)?->nodeValue;
        $counterAnswerId = $this->object->getElementsByTagName('til_andsvara')?->item(0)?->getAttribute('id');
        $counterAnswer = $this->object->getElementsByTagName('til_andsvara')?->item(0)?->nodeValue;

        $issueName = $this->object->getElementsByTagName('málsheiti')?->item(0)?->nodeValue;
        $issueType = $this->object->getElementsByTagName('málstegund')?->item(0)?->getAttribute('id');
        $issueTypeName = $this->object->getElementsByTagName('málstegund')?->item(0)?->nodeValue;

        return [
            'parliamentary_session_id' => (int) $parliamentarySessionId,
            'issue_id' => (int) $issueId,
            'issue_name' => $issueName,
            'issue_type' => $issueType,
            'issue_typename' => $issueTypeName,
            'kind' => $issueCategory,
            'assembly_id' => (int) $assemblyId,
            'item_id' => (int) $this->getIdentity(),
            'iteration_type' => empty($iterationType)
                ? null
                : $iterationType,
            'iteration_continue' => empty($iterationContinue)
                ? null
                : $iterationContinue,
            'iteration_comment' => empty(trim($iterationComment ?: ''))
                ? null
                : trim($iterationComment ?: ''),
            'comment' => empty(trim($comment ?: ''))
                ? null
                : preg_replace("/(\r|\n)|(\s+)/", " ", trim($comment ?: '')),
            'comment_type' => $commentType,
            'posed_id' => $posedId ? (int) $posedId : null,
            'posed' => empty(trim($posed ?: '')) ? null : trim($posed ?: ''),
            'answerer_id' => $answererId ? (int) $answererId : null,
            'answerer' => empty(trim($answerer ?: '')) ? null : trim($answerer ?: ''),
            'counter_answerer_id' => $counterAnswerId ? (int) $counterAnswerId : null,
            'counter_answerer' => empty(trim($counterAnswer ?: '')) ? null : trim($counterAnswer ?: ''),
            'instigator_id' => $instigatorId ? (int) $instigatorId : null,
            'instigator' => empty(trim($instigator ?: '')) ? null : trim($instigator ?: ''),
        ];
    }

    public function setIdentity(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getIdentity(): string
    {
        return $this->id;
    }
}
