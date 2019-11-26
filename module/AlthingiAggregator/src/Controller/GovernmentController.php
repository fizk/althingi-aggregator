<?php
namespace AlthingiAggregator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Extractor;
use AlthingiAggregator\Callback\GovernmentDocumentCallback;
use AlthingiAggregator\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Provider\ProviderAwareInterface;

class GovernmentController extends AbstractActionController implements ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function findGovernmentsAction()
    {
        $list = $this->queryForNoteList(
            'https://www.stjornarradid.is/rikisstjorn/sogulegt-efni/rikisstjornartal/',
            '//root/item',
            new GovernmentDocumentCallback()
        );

        $this->saveDomNodeList($list, 'raduneyti', new Extractor\Government());
    }
}
