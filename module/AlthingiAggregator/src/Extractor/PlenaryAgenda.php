<?php
namespace AlthingiAggregator\Extractor;

use DOMElement;
use AlthingiAggregator\Lib\IdentityInterface;
use AlthingiAggregator\Extractor;

class PlenaryAgenda implements ExtractionInterface, IdentityInterface
{
    private $id;

    /**
     * Extract values from an object
     *
     * @param  DOMElement $object
     * @return array|null
     * @throws \AlthingiAggregator\Extractor\Exception
     * @todo This extractor goes out of the document to get the $plenaryId
     *      $object->ownerDocument->getElementsByTagName('þingfundur')->item(0)->getAttribute('númer');
     */
    public function extract(DOMElement $object)
    {
        if (! $object->hasAttribute('númer')) {
            throw new Extractor\Exception('Missing [{númer}] value', $object);
        }

        $this->setIdentity($object->getAttribute('númer'));

        $plenaryId = $object->ownerDocument->getElementsByTagName('þingfundur')->item(0)->getAttribute('númer');

        $issue = $object->getElementsByTagName('mál')->length
            ? $object->getElementsByTagName('mál')->item(0)
            : null;

        if (! $issue) {
            throw new Extractor\Exception('Missing [{mál}] element', $object);
        }

        $issueId = $issue->getAttribute('málsnúmer');
        $issueCategory = $issue->getAttribute('málsflokkur');
        $assemblyId = $issue->getAttribute('þingnúmer');

        if (empty($issueId) || empty($issueCategory) || empty($assemblyId)) {
            throw new Extractor\Exception('Missing [{málsnúmer}, {málsflokkur}, {þingnúmer}] value', $object);
        }

        $iterationType = $object->getElementsByTagName('umræða')->length
            ? $object->getElementsByTagName('umræða')->item(0)->getAttribute('tegund')
            : null;
        $iterationContinue = $object->getElementsByTagName('umræða')->length
            ? $object->getElementsByTagName('umræða')->item(0)->getAttribute('framhald')
            : null;
        $iterationComment = $object->getElementsByTagName('umræða')->length
            ? $object->getElementsByTagName('umræða')->item(0)->nodeValue
            : null;
        $comment = $object->getElementsByTagName('athugasemd')->length
            ? $object->getElementsByTagName('athugasemd')->item(0)->nodeValue
            : null;
        $commentType = $object->getElementsByTagName('athugasemd')->length
            ? $object->getElementsByTagName('athugasemd')->item(0)->getAttribute('tegund')
            : null;
        $posedId = $object->getElementsByTagName('fyrirspyrjandi')->length
            ? $object->getElementsByTagName('fyrirspyrjandi')->item(0)->getAttribute('id')
            : null;
        $posed = $object->getElementsByTagName('fyrirspyrjandi')->length
            ? $object->getElementsByTagName('fyrirspyrjandi')->item(0)->nodeValue
            : null;
        $answererId = $object->getElementsByTagName('til_svara')->length
            ? $object->getElementsByTagName('til_svara')->item(0)->getAttribute('id')
            : null;
        $answerer = $object->getElementsByTagName('til_svara')->length
            ? $object->getElementsByTagName('til_svara')->item(0)->nodeValue
            : null;
        $instigatorId = $object->getElementsByTagName('málshefjandi')->length
            ? $object->getElementsByTagName('málshefjandi')->item(0)->getAttribute('id')
            : null;
        $instigator = $object->getElementsByTagName('málshefjandi')->length
            ? $object->getElementsByTagName('málshefjandi')->item(0)->nodeValue
            : null;
        $counterAnswerId = $object->getElementsByTagName('til_andsvara')->length
            ? $object->getElementsByTagName('til_andsvara')->item(0)->getAttribute('id')
            : null;
        $counterAnswer = $object->getElementsByTagName('til_andsvara')->length
            ? $object->getElementsByTagName('til_andsvara')->item(0)->nodeValue
            : null;

        return [
            'plenary_id' => (int) $plenaryId,
            'issue_id' => (int) $issueId,
            'category' => $issueCategory,
            'assembly_id' => (int) $assemblyId,
            'item_id' => (int) $this->getIdentity(),
            'iteration_type' => empty($iterationType) ? null : $iterationType,
            'iteration_continue' => empty($iterationContinue) ? null : $iterationContinue,
            'iteration_comment' => empty(trim($iterationComment)) ? null : trim($iterationComment),
            'comment' => empty(trim($comment)) ? null : preg_replace("/(\r|\n)|(\s+)/", " ", trim($comment)),
            'comment_type' => $commentType,
            'posed_id' => $posedId ? (int) $posedId : null,
            'posed' => empty(trim($posed)) ? null : trim($posed),
            'answerer_id' => $answererId ? (int) $answererId : null,
            'answerer' => empty(trim($answerer)) ? null : trim($answerer),
            'counter_answerer_id' => $counterAnswerId ? (int) $counterAnswerId : null,
            'counter_answerer' => empty(trim($counterAnswer)) ? null : trim($counterAnswer),
            'instigator_id' => $instigatorId ? (int) $instigatorId : null,
            'instigator' => empty(trim($instigator)) ? null : trim($instigator),
        ];
    }

    public function setIdentity($id)
    {
        $this->id = $id;
    }

    public function getIdentity()
    {
        return $this->id;
    }
}
