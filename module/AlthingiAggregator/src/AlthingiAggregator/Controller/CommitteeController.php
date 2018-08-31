<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 5/04/2016
 * Time: 12:28 PM
 */

namespace AlthingiAggregator\Controller;

use AlthingiAggregator\Extractor\Committee;
use AlthingiAggregator\Extractor\CommitteeAgenda;
use AlthingiAggregator\Extractor\CommitteeMeeting;
use AlthingiAggregator\Extractor\NullExtractor;
use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Lib\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Lib\Provider\ProviderAwareInterface;

class CommitteeController extends AbstractActionController implements ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function findAssemblyCommitteeAction()
    {
        $assemblyNumber = $this->params('assembly');

        $meetings = $this->queryForNoteList("http://huginn.althingi.is/altext/xml/nefndarfundir/?lthing={$assemblyNumber}", '//nefndarfundir/nefndarfundur');

        foreach ($meetings as $meeting) {
            $meetingId = (int) $meeting->getAttribute('númer');
            $committeeId = (int) $meeting->getElementsByTagName('nefnd')->item(0)->getAttribute('id');

            $meetingElement = $this->queryForDocument("http://www.althingi.is/altext/xml/nefndarfundir/nefndarfundur/?dagskrarnumer={$meetingId}");

            $this->saveDomElement(
                $meetingElement->documentElement,
                "loggjafarthing/{$assemblyNumber}/nefndir/{$committeeId}/nefndarfundir", new CommitteeMeeting()
            );

            $this->saveDomNodeList(
                $meetingElement->getElementsByTagName('dagskrárliður'),
                "loggjafarthing/{$assemblyNumber}/nefndir/{$committeeId}/nefndarfundir/{$meetingId}/dagskralidir", new CommitteeAgenda()
            );
        }
    }

    public function findCommitteeAction()
    {
        $this->queryAndSave(
            'http://www.althingi.is/altext/xml/nefndir',
            'nefndir',
            '//nefndir/nefnd',
            new Committee()
        );
    }
}
