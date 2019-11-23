<?php
namespace AlthingiAggregator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use AlthingiAggregator\Extractor;
use AlthingiAggregator\Consumer\ConsumerAwareInterface;
use AlthingiAggregator\Provider\ProviderAwareInterface;

class InflationController extends AbstractActionController implements ConsumerAwareInterface, ProviderAwareInterface
{
    use ConsoleHelper;

    public function findInflationAction()
    {
        $date = $this->params('date', "1930-01-01");

        $list = $this->queryForNoteList(
            "https://www.sedlabanki.is/xmltimeseries/Default.aspx?GroupID=3&Type=xml&DagsFra={$date}&TimeSeriesID=2",
            '//Group/TimeSeries[2]/TimeSeriesData/Entry'
        );

        foreach ($list as $item) {
            $this->saveDomElement(
                $item,
                'verdbolga',
                new Extractor\Inflation()
            );
        }
    }
}
