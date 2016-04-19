<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 5/04/2016
 * Time: 12:28 PM
 */

namespace AlthingiAggregator\Controller;

use AlthingiAggregator\Extractor\Committee;
use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Lib\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Lib\Provider\ProviderAwareInterface;

class CommitteeController extends AbstractActionController implements ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

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
