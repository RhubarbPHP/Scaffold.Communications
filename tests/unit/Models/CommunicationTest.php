<?php

namespace Rhubarb\Scaffolds\Communications\Tests\Models;

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

        $this->assertTrue($communication->Completed);

        try {
            $communication->DateCompleted = new RhubarbDateTime("now");
            $this->fail("DateCompleted should not be modified");
        } catch (ModelConsistencyValidationException $ex) {

        }

        $communication->Completed = true;
        $communication->save();

        $this->assertNotEmpty($communication->DateCompleted, "Expected DateSent to be set");
    }

//    public function testFindAllUnsentCommunications()
//    {
//        $this->assertCount(0, Communication::FindUnsentCommunications(), "Expected 0 Communications to be sent");
//
//        $communication = $this->createCommunicationForEmail();
//
//        $this->assertCount(1, Communication::FindUnsentCommunications(), "Expected 1 Communications to be sent");
//    }
}
