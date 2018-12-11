<?php
namespace AlthingiAggregator\Controller;

use AlthingiAggregator\Extractor\Government;
use AlthingiAggregator\Lib\GovernmentDocumentCallback;
use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Lib\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Lib\Provider\ProviderAwareInterface;

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

        $this->saveDomNodeList($list, 'raduneyti', new Government());
    }
}
