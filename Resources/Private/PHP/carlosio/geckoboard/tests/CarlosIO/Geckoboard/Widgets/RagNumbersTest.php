<?php

namespace CarlosIO\Geckoboard\Widgets;

use CarlosIO\Geckoboard\Data\Entry;
use CarlosIO\Geckoboard\Widgets\RagNumbers;

class RagNumbersTest extends \PHPUnit_Framework_TestCase
{
    public function testJsonForFullData()
    {
        $widget = new RagNumbers();
        $widget->setId('29473-d7ae87e3-ac3f-4911-95ce-ec91439a4170');

        $redData = new Entry();
        $redData->setValue(15)->setText('Errors in the last 5 minutes');
        $widget->setRedData($redData);

        $amberData = new Entry();
        $amberData->setValue(15)->setText('Errors in the last 15 minutes');
        $widget->setAmberData($amberData);

        $greenData = new Entry();
        $greenData->setValue(15)->setText('Errors in the last 60 minutes');
        $widget->setGreenData($greenData);

        $json = json_encode($widget->getData());
        $this->assertEquals('{"item":[{"text":"Errors in the last 5 minutes","value":15},{"text":"Errors in the last 15 minutes","value":15},{"text":"Errors in the last 60 minutes","value":15}]}', $json);
    }

    public function testJsonForGreenAndRedData()
    {
        $widget = new RagNumbers();
        $widget->setId('29473-d7ae87e3-ac3f-4911-95ce-ec91439a4170');

        $redData = new Entry();
        $redData->setValue(15)->setText('Errors in the last 5 minutes');
        $widget->setRedData($redData);

        $greenData = new Entry();
        $greenData->setValue(15)->setText('Errors in the last 60 minutes');
        $widget->setGreenData($greenData);

        $json = json_encode($widget->getData());
        $this->assertEquals('{"item":[{"text":"Errors in the last 5 minutes","value":15},{"text":"Errors in the last 60 minutes","value":15}]}', $json);
    }
}
