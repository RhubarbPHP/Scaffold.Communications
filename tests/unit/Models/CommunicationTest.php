<?php

namespace Rhubarb\Scaffolds\Communications\Tests\Models;

use Rhubarb\Crown\DateTime\RhubarbDate;
use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Models\CommunicationItem;
use Rhubarb\Scaffolds\Communications\Tests\Fixtures\CommunicationTestCase;
use Rhubarb\Stem\Exceptions\ModelConsistencyValidationException;

/**
 * Class CommunicationTests
 * @package Rhubarb\Scaffolds\Communications\Tests\Models\Communications
 */
class CommunicationTest extends CommunicationTestCase
{
    public function testCommunicationSent()
    {
        $communication = $this->createCommunicationForEmail();

        $this->assertTrue($communication->Status == "Sent");

        try {
            $communication->DateSent = new RhubarbDateTime("now");
            $this->fail("DateSent should not be modified");
        } catch (ModelConsistencyValidationException $ex) {

        }

        $communication->Status = "Sent";
        $communication->save();

        $this->assertNotEmpty($communication->DateSent, "Expected DateSent to be set");
    }

    public function testFindAllUnsentCommunications()
    {
        $this->assertCount(0, Communication::FindUnsentCommunications(), "Expected 0 Communications to be sent");

        $communication = $this->createCommunicationForEmail(true);

        $this->assertCount(1, Communication::FindUnsentCommunications(), "Expected 1 Communications to be sent");

        $communication->DateToSend = new RhubarbDateTime("0000-00-00 00:00:00");
        $communication->save();

        $this->assertCount(0, Communication::FindUnsentCommunications(), "Scheduled comms with no date to sent aren't to be sent.");

    }
}
