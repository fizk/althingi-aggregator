<?php
namespace App\Extractor;

use App\Extractor\PlenaryAgenda;
use PHPUnit\Framework\TestCase;
use DOMDocument;
use DOMXPath;

class PlenaryAgendaTest extends TestCase
{
    public function testValidDocument()
    {
        $expectedData = [
            'plenary_id' => 13,
            'issue_id' => 5,
            'issue_name' => 'Forseti Íslands setur þingið',
            'issue_type' => 'þi',
            'issue_typename' => 'þingsetning',
            'category' => 'B',
            'assembly_id' => 148,
            'item_id' => 1,
            'iteration_type' => '*',
            'iteration_continue' => null,
            'iteration_comment' => null,
            'comment' => null,
            'comment_type' => null,
            'posed_id' => null,
            'posed' => null,
            'answerer_id' => null,
            'answerer' => null,
            'counter_answerer_id' => null,
            'counter_answerer' => null,
            'instigator_id' => null,
            'instigator' => null,
        ];
        $documentNodeList = $this->buildNodeList($this->getDocument());

        $documentData = (new PlenaryAgenda())->populate($documentNodeList->item(0))
            ->extract();

        $this->assertEquals($expectedData, $documentData);
    }
    public function testValidDocumentMinusOnePlenary()
    {
        $expectedData = [
            'plenary_id' => -1,
            'issue_id' => 5,
            'issue_name' => 'Forseti Íslands setur þingið',
            'issue_type' => 'þi',
            'issue_typename' => 'þingsetning',
            'category' => 'B',
            'assembly_id' => 148,
            'item_id' => 1,
            'iteration_type' => '*',
            'iteration_continue' => null,
            'iteration_comment' => null,
            'comment' => null,
            'comment_type' => null,
            'posed_id' => null,
            'posed' => null,
            'answerer_id' => null,
            'answerer' => null,
            'counter_answerer_id' => null,
            'counter_answerer' => null,
            'instigator_id' => null,
            'instigator' => null,
        ];
        $dom = new DOMDocument();
        $dom->loadXML('<?xml version="1.0" encoding="UTF-8"?>
            <dagskráþingfundar>
                <þingfundur númer="-1" þingnúmer="148">
                    <fundarheiti>þingsetning</fundarheiti>
                    <hefst>
                        <texti>14. desember, kl. 2:00 árdegis</texti>
                        <dagur>14.12.2017</dagur>
                        <timi>14:00</timi>
                        <dagurtími>2017-12-14T14:00:00</dagurtími>
                    </hefst>
                    <fundursettur>2017-12-14T14:08:01</fundursettur>
                    <fuslit>2017-12-14T14:31:34</fuslit>
                    <dagskrá>
                        <dagskrárliður númer=\'1\'>
                            <mál málsnúmer=\'5\' þingnúmer=\'148\' málsflokkur=\'B\'>
                                <málsheiti>Forseti Íslands setur þingið</málsheiti>
                                <málstegund id=\'þi\'>þingsetning</málstegund>
                            </mál>
                            <umræða tegund=\'*\' framhald=\'\'> </umræða>
                        </dagskrárliður>
                        <dagskrárliður númer="3">
                            <mál málsnúmer="63" þingnúmer="148" málsflokkur="A">
                                <málsheiti>kyrrsetning, lögbann o.fl.</málsheiti>
                                <efnisgreining>lögbann á miðlun fjölmiðils</efnisgreining>
                                <málstegund id="l">Frumvarp til laga</málstegund>
                            </mál>
                            <umræða tegund="1" framhald=""> 1. umræða</umræða>
                            <athugasemd tegund="U">
                                <dagskrártexti>Ef leyft verður</dagskrártexti>
                                <skýring>afbr. (of skammt liðið frá síðustu umræðu).</skýring>
                            </athugasemd>
                        </dagskrárliður>
                        <dagskrárliður númer="1">
                            <mál málsnúmer="90" þingnúmer="148" málsflokkur="B">
                                <málsheiti>óundirbúinn fyrirspurnatími</málsheiti>
                                <málstegund id="ft">óundirbúinn fyrirspurnatími</málstegund>
                                <fyrirspyrjandi id="1"/>
                                <til_svara id="2"/>
                            </mál>
                            <umræða tegund="*" framhald=""> </umræða>
                        </dagskrárliður>
                        <dagskrárliður númer="1">
                            <mál málsnúmer="90" þingnúmer="148" málsflokkur="B">
                                <málsheiti>óundirbúinn fyrirspurnatími</málsheiti>
                                <málstegund id="ft">óundirbúinn fyrirspurnatími</málstegund>
                                <fyrirspyrjandi id="1">Fyrirspyrjandi</fyrirspyrjandi>
                                <til_svara id="2">Til svara</til_svara>
                                <málshefjandi id="3">Malshefjandi</málshefjandi>
                                <til_andsvara id="4">Til andsvars</til_andsvara>
                            </mál>
                            <umræða tegund="*" framhald=""> </umræða>
                        </dagskrárliður>
                    </dagskrá>
                </þingfundur>
            </dagskráþingfundar>
        ');
        $documentsXPath = new DOMXPath($dom);
        $documentNodeList = $documentsXPath->query('//dagskráþingfundar/þingfundur/dagskrá/dagskrárliður');

        $documentData = (new PlenaryAgenda())
            ->populate($documentNodeList->item(0))
            ->extract();

        $this->assertEquals($expectedData, $documentData);
    }

    public function testValidDocumentTwo()
    {
        $expectedData = [
            'plenary_id' => 13,
            'issue_id' => 63,
            'issue_name' => 'kyrrsetning, lögbann o.fl.',
            'issue_type' => 'l',
            'issue_typename' => 'Frumvarp til laga',
            'category' => 'A',
            'assembly_id' => 148,
            'item_id' => 3,
            'iteration_type' => '1',
            'iteration_continue' => null,
            'iteration_comment' => '1. umræða',
            'comment' => 'Ef leyft verður  afbr. (of skammt liðið frá síðustu umræðu).',
            'comment_type' => 'U',
            'posed_id' => null,
            'posed' => null,
            'answerer_id' => null,
            'answerer' => null,
            'counter_answerer_id' => null,
            'counter_answerer' => null,
            'instigator_id' => null,
            'instigator' => null,
        ];
        $documentNodeList = $this->buildNodeList($this->getDocument());

        $documentData = (new PlenaryAgenda())->populate($documentNodeList->item(1))
            ->extract();

        $this->assertEquals($expectedData, $documentData);
    }

    public function testValidDocumentThree()
    {
        $expectedData = [
            'plenary_id' => 13,
            'issue_id' => 90,
            'issue_name' => 'óundirbúinn fyrirspurnatími',
            'issue_type' => 'ft',
            'issue_typename' => 'óundirbúinn fyrirspurnatími',
            'category' => 'B',
            'assembly_id' => 148,
            'item_id' => 1,
            'iteration_type' => '*',
            'iteration_continue' => null,
            'iteration_comment' => null,
            'comment' => null,
            'comment_type' => null,
            'posed_id' => 1,
            'posed' => null,
            'answerer_id' => 2,
            'answerer' => null,
            'counter_answerer_id' => null,
            'counter_answerer' => null,
            'instigator_id' => null,
            'instigator' => null,
        ];
        $documentNodeList = $this->buildNodeList($this->getDocument());

        $documentData = (new PlenaryAgenda())->populate($documentNodeList->item(2))
            ->extract();

        $this->assertEquals($expectedData, $documentData);
    }

    public function testValidDocumentFour()
    {
        $expectedData = [
            'plenary_id' => 13,
            'issue_id' => 90,
            'issue_name' => 'óundirbúinn fyrirspurnatími',
            'issue_type' => 'ft',
            'issue_typename' => 'óundirbúinn fyrirspurnatími',
            'category' => 'B',
            'assembly_id' => 148,
            'item_id' => 1,
            'iteration_type' => '*',
            'iteration_continue' => null,
            'iteration_comment' => null,
            'comment' => null,
            'comment_type' => null,
            'posed_id' => 1,
            'posed' => 'Fyrirspyrjandi',
            'answerer_id' => 2,
            'answerer' => 'Til svara',
            'counter_answerer_id' => 4,
            'counter_answerer' => 'Til andsvars',
            'instigator_id' => 3,
            'instigator' => 'Malshefjandi',
        ];
        $documentNodeList = $this->buildNodeList($this->getDocument());

        $documentData = (new PlenaryAgenda())->populate($documentNodeList->item(3))
            ->extract();

        $this->assertEquals($expectedData, $documentData);
    }

    private function buildNodeList($source)
    {
        $dom = new DOMDocument();
        $dom->loadXML($source);
        $documentsXPath = new DOMXPath($dom);
        return $documentsXPath->query('//dagskráþingfundar/þingfundur/dagskrá/dagskrárliður');
    }

    private function getDocument()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
            <dagskráþingfundar>
                <þingfundur númer=\'13\' þingnúmer=\'148\'>
                    <fundarheiti>þingsetning</fundarheiti>
                    <hefst>
                        <texti>14. desember, kl. 2:00 árdegis</texti>
                        <dagur>14.12.2017</dagur>
                        <timi>14:00</timi>
                        <dagurtími>2017-12-14T14:00:00</dagurtími>
                    </hefst>
                    <fundursettur>2017-12-14T14:08:01</fundursettur>
                    <fuslit>2017-12-14T14:31:34</fuslit>
                    <dagskrá>
                        <dagskrárliður númer=\'1\'>
                            <mál málsnúmer=\'5\' þingnúmer=\'148\' málsflokkur=\'B\'>
                                <málsheiti>Forseti Íslands setur þingið</málsheiti>
                                <málstegund id=\'þi\'>þingsetning</málstegund>
                            </mál>
                            <umræða tegund=\'*\' framhald=\'\'> </umræða>
                        </dagskrárliður>
                        <dagskrárliður númer="3">
                            <mál málsnúmer="63" þingnúmer="148" málsflokkur="A">
                                <málsheiti>kyrrsetning, lögbann o.fl.</málsheiti>
                                <efnisgreining>lögbann á miðlun fjölmiðils</efnisgreining>
                                <málstegund id="l">Frumvarp til laga</málstegund>
                            </mál>
                            <umræða tegund="1" framhald=""> 1. umræða</umræða>
                            <athugasemd tegund="U">
                                <dagskrártexti>Ef leyft verður</dagskrártexti>
                                <skýring>afbr. (of skammt liðið frá síðustu umræðu).</skýring>
                            </athugasemd>
                        </dagskrárliður>
                        <dagskrárliður númer="1">
                            <mál málsnúmer="90" þingnúmer="148" málsflokkur="B">
                                <málsheiti>óundirbúinn fyrirspurnatími</málsheiti>
                                <málstegund id="ft">óundirbúinn fyrirspurnatími</málstegund>
                                <fyrirspyrjandi id="1"/>
                                <til_svara id="2"/>
                            </mál>
                            <umræða tegund="*" framhald=""> </umræða>
                        </dagskrárliður>
                        <dagskrárliður númer="1">
                            <mál málsnúmer="90" þingnúmer="148" málsflokkur="B">
                                <málsheiti>óundirbúinn fyrirspurnatími</málsheiti>
                                <málstegund id="ft">óundirbúinn fyrirspurnatími</málstegund>
                                <fyrirspyrjandi id="1">Fyrirspyrjandi</fyrirspyrjandi>
                                <til_svara id="2">Til svara</til_svara>
                                <málshefjandi id="3">Malshefjandi</málshefjandi>
                                <til_andsvara id="4">Til andsvars</til_andsvara>
                            </mál>
                            <umræða tegund="*" framhald=""> </umræða>
                        </dagskrárliður>
                    </dagskrá>
                </þingfundur>
            </dagskráþingfundar>
            ';
    }
}
