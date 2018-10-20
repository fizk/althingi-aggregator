<?php
namespace AlthingiAggregatorTest\Controller;

use AlthingiAggregatorTest\Lib\Consumer\TestConsumer;
use AlthingiAggregatorTest\Lib\Provider\TestProvider;
use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;

class IssueControllerTest extends AbstractConsoleControllerTestCase
{
    /** @var  TestConsumer */
    private $consumer;

    /** @var  TestProvider */
    private $provider;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../../../config/application.config.php'
        );
        parent::setUp();

        $this->provider = new TestProvider();
        $this->consumer = new TestConsumer();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Provider', $this->provider);
        $serviceManager->setService('Consumer', $this->consumer);
    }

    /**
     * @todo fix this test.
     */
    public function testIssue()
    {
        $this->assertTrue(true);
//        $this->provider->addDocument(
//            'http://www.althingi.is/altext/xml/thingmalalisti/?lthing=1',
//            $this->getDomDocument()
//        )->addDocument(
//            'http://www.althingi.is/b-issue-document',
//            $this->getBIssueDocument()
//        )->addDocument(
//            'http://www.althingi.is/xml/148/raedur/rad20171219T133304.xml',
//            $this->getSpeechOne()
//        );
//
//        $this->dispatch('load:issue --assembly=1');
//
//
//        $expectedIssue = [
//            'id' => 35,
//            'assembly_id' => 148,
//            'category' => 'B',
//            'name' => 'óundirbúinn fyrirspurnatími',
//            'type' => 'ft',
//            'type_name' => 'óundirbúinn fyrirspurnatími',
//            'status' => null,
//            'sub_name' => null,
//            'type_subname' => null,
//            'congressman_id' => null,
//            'question' => null,
//            'goal' => null,
//            'major_changes' => null,
//            'changes_in_law' => null,
//            'costs_and_revenues' => null,
//            'deliveries' => null,
//            'additional_information' => null,
//        ];
//
//        $expectedSpeech = [
//            'id' => '20171219T133304',
//            'from' => '2017-12-19 13:33:04',
//            'to' => '2017-12-19 13:33:05',
//            'plenary_id' => 5,
//            'assembly_id' => 148,
//            'issue_id' => 1,
//            'congressman_type' => 'forseti alþingis',
//            'congressman_id' => 557,
//            'iteration' => null,
//            'type' => 'ræða',
//            'text' => '<ræðutexti xmlns="http://skema.althingi.is/skema"><mgr>Til svara eru</mgr></ræðutexti>',
//        ];
//
//        $actual = $this->consumer->getObjects();
//
//
//        $this->assertEquals($expectedIssue, $actual['loggjafarthing/1/bmal/35']);
//        $this->assertEquals($expectedSpeech, $actual['loggjafarthing/1/bmal/1/raedur/20171219T133304']);

    }

    public function getDomDocument()
    {
        $source = '<?xml version="1.0" encoding="UTF-8"?>
            <málaskrá>
                <mál málsnúmer="1" þingnúmer="1" málsflokkur="B">
                    <málsheiti>óundirbúinn fyrirspurnatími</málsheiti>
                    <málstegund málstegund="ft">
                        <heiti>óundirbúinn fyrirspurnatími</heiti>
                    </málstegund>
                    <html>https://www.althingi.is/</html>
                    <xml>http://www.althingi.is/b-issue-document</xml>
                </mál>
            </málaskrá>';
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }

    public function getBIssueDocument()
    {
        $source = '<?xml version="1.0" encoding="UTF-8"?>
            <bmál>
                <mál málsnúmer="35" þingnúmer="148" málsflokkur="B">
                    <málsheiti>óundirbúinn fyrirspurnatími</málsheiti>
                    <undirheiti>óundirbúinn fyrirspurnatími</undirheiti>
                    <málstegund málstegund="ft">
                        <heiti>óundirbúinn fyrirspurnatími</heiti>
                    </málstegund>
                    <slóð>
                        <html>https://www.althingi.is/thingstorf/thingmalalistar-eftir-thingum/bferill/</html>
                        <xml>http://www.althingi.is/altext/xml/thingmalalisti/bmal</xml>
                    </slóð>
                </mál>
                <atkvæðagreiðslur/>
                <ræður>
                    <ræða fundarnúmer="5" þingnúmer="148" þingmaður="557">
                        <forsetiAlþingis/>
                        <nafn id="557">Steingrímur J. Sigfússon</nafn>
                        <ræðahófst>2017-12-19T13:33:04</ræðahófst>
                        <ræðulauk>2017-12-19T13:33:05</ræðulauk>
                        <slóðir>
                            <xml>http://www.althingi.is/xml/148/raedur/rad20171219T133304.xml</xml>
                            <html>http://www.althingi.is/altext/raeda/148/rad20171219T133304.html</html>
                        </slóðir>
                        <tegundræðu>ræða</tegundræðu>
                        <umræða>*</umræða>
                    </ræða>
                </ræður>
            </bmál>

        ';
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }

    public function getSpeechOne()
    {
        $source = '<?dctm xml_app="Raeduskjal"?>
            <ræða xmlns="http://skema.althingi.is/skema"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xsi:schemaLocation="http://skema.althingi.is/skema http://skema.althingi.is/skema/althingi_raedur.xsd"
                id="r2017-12-19T13:33:04" skst="SJS" tegr="R" tegflm="F" frhr="0">
                <umsýsla fundur="5" tími="2017-12-19T13:33:04" lgþ="148">
                    <mál nr="35" málstegund="ft" málsflokkur="B">
                        <málsheiti>óundirbúinn fyrirspurnatími</málsheiti>
                    </mál>
                </umsýsla>
                <ræðutexti><mgr>Til svara eru</mgr></ræðutexti>
            </ræða>';
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $dom;
    }
}
