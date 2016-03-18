<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 23/05/15
 * Time: 7:42 PM
 */

namespace AlthingiAggregator\Controller;

use AlthingiAggregator\Lib\LoggerAwareInterface;
use AlthingiAggregator\Model\Dom\Session;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Parameters;
use AlthingiAggregator\Lib\Http\DomClient;
use AlthingiAggregator\Model\Dom\Congressman as CongressmanModel;

class CongressmanController extends AbstractActionController implements LoggerAwareInterface
{
    use ConsoleHelper;

    /**
     * Get Congressman.
     * If additional parameter is passed (--assembly=int) than only congressman
     * for given assembly will be fetched.
     *
     * @throws \Exception
     */
    public function findCongressmanAction()
    {
        $this->getLogger()->info("Find Congressman -- started");

        $assemblyNumber = $this->params('assembly');
        $congressmenDom = $this->queryForCongressmen($assemblyNumber);
        $congressmenElements = $congressmenDom->getElementsByTagName('þingmaður');

        foreach ($congressmenElements as $congressmanItem) {
            $congressmanId = $this->saveCongressman($congressmanItem);

            if (!$congressmanId) {
                continue;
            }

            try {
                $congressmanDom = (new DomClient())
                    ->setClient($this->getClient())
                    ->get("http://www.althingi.is/altext/xml/thingmenn/thingmadur/thingseta/?nr={$congressmanId}");

                $xpath = new \DOMXPath($congressmanDom);
                $congressmanSessionElements = $xpath->query('//þingmaður/þingsetur/þingseta');
                $this->saveCongressmanSession($congressmanId, $congressmanSessionElements);
            } catch (\Exception $e) {
                $this->getLogger()->error($e->getMessage());
            }
        }

        $this->getLogger()->info("Find Congressman -- ended");
    }

    /**
     * Save on Congressman to storage.
     * Return his/her ID or false if error.
     *
     * @param \DOMElement $congressmanElement
     * @return int|bool
     */
    private function saveCongressman(\DOMElement $congressmanElement)
    {
        $config = $this->getServiceLocator()->get('Config');

        $congressmanModel = (new CongressmanModel())
            ->extract($congressmanElement);

        $apiRequest = (new Request())
            ->setMethod('post')
            ->setHeaders((new Headers())->addHeaders(['X-HTTP-Method-Override' => 'PUT']))
            ->setUri(sprintf('%s/thingmenn/%s', $config['server']['host'], $congressmanModel['id']))
            ->setPost(new Parameters($congressmanModel));
        $apiResult = $this->getClient()->send($apiRequest);

        $this->getLogger()->info("Find Congressman -- [{$congressmanModel['id']}]");
        $this->getLogger()->info(
            $apiResult->getStatusCode(),
            [json_decode($apiResult->getContent())]
        );

        return $congressmanModel['id'];

    }

    /**
     * Save Congressman's sessions.
     *
     * @param int $id  Congressman ID
     * @param \DOMNodeList $sessions
     */
    private function saveCongressmanSession($id, \DOMNodeList $sessions)
    {
        $this->getLogger()->info("Congressman[{$id}] Session -- start");

        $config = $this->getServiceLocator()->get('Config');

        foreach ($sessions as $sessionElement) {
            $sessionModel = (new Session())->extract($sessionElement);
            $apiRequest = (new Request())
                ->setMethod('post')
                ->setUri(sprintf('%s/thingmenn/%s/thingseta', $config['server']['host'], $id))
                ->setPost(new Parameters($sessionModel));
            $apiResult = $this->getClient()->send($apiRequest);

            $this->getLogger()->info(
                $apiResult->getStatusCode(),
                [json_decode($apiResult->getContent())]
            );
        }

        $this->getLogger()->info("Congressman[{$id}] Session -- end");
    }

    /**
     * Go to remote server and get congressmen.
     *
     * If a number of assembly is passed, only request congressmen
     * from that assembly.
     *
     * @param int $assemblyNumber
     * @return \DOMDocument
     * @throws \Exception
     */
    private function queryForCongressmen($assemblyNumber)
    {
        return (new DomClient())
            ->setClient($this->getClient())
            ->get(($assemblyNumber)
                ? "http://www.althingi.is/altext/xml/thingmenn/?lthing={$assemblyNumber}"
                : "http://www.althingi.is/altext/xml/thingmenn/"
            );
    }
}
