<?php

namespace Rhubarb\Scaffolds\Communications\Tests\Models\Communications;

use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Tests\Fixtures\CommunicationTestCase;
use Rhubarb\Stem\Exceptions\ModelConsistencyValidationException;

/**
 * Class CommunicationTests
 * @package Rhubarb\Scaffolds\Communications\Tests\Models\Communications
 */
class CommunicationTests extends CommunicationTestCase
{
    public function testCommunicationSent()
    {
        $communication = new Communication();
        $communication->Type = Communication::TYPE_EMAIL;
        $communication->ToRecipients = "test@test.com";
        $communication->FromSender = "sender@gcdtech.com";
        $communication->Body = "Test Message Body";

        $communication->save();

        $this->assertFalse($communication->Sent);

        try {
            $communication->DateSent = new RhubarbDateTime("now");
            $this->fail("Date Sent should not be modified");
        } catch (ModelConsistencyValidationException $ex) {

        }

        $communication->Sent = true;
        $communication->save();

        $this->assertNotEmpty($communication->DateSent, "Expected DateSent to be set");
    }
}
