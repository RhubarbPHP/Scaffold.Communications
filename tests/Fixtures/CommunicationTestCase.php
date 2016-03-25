<?php

namespace Rhubarb\Scaffolds\Communications\Tests\Fixtures;

use Codeception\TestCase\Test;
use Rhubarb\Crown\Sendables\Email\SimpleEmail;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Scaffolds\Communications\CommunicationPackages\CommunicationPackage;
use Rhubarb\Scaffolds\Communications\CommunicationsModule;
use Rhubarb\Scaffolds\Communications\EmailProviders\CommunicationEmailProvider;
use Rhubarb\Scaffolds\Communications\Models\Communication;
use Rhubarb\Scaffolds\Communications\Models\CommunicationItem;

abstract class CommunicationTestCase extends RhubarbTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->application->registerModule(new CommunicationsModule());
        $this->application->initialiseModules();

        Communication::clearObjectCache();
        CommunicationItem::clearObjectCache();
    }

    /**
     * @return Communication
     * @throws \Rhubarb\Stem\Exceptions\RecordNotFoundException
     */
    public function createCommunicationForEmail()
    {
        $email = new SimpleEmail();
        $email->setSubject("The three billy goats");
        $email->addRecipientByEmail("john.smith@outlook.com", "John Smith");
        $email->setSender("jane.smith@outlook.com", "Jane Smith" );
        $email->setText("Michael went to mow, went to mow a meadow.");
        $email->setHtml("<p>Michael went to mow, went to mow a meadow.</p>");

        $package = new CommunicationPackage();
        $package->addSendable($email);
        $package->title = $email->getSubject();
        $package->send();

        return Communication::findLast();
    }
}
