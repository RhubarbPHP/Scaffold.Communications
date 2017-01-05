<?php

namespace Rhubarb\Scaffolds\Communications\Leaves\CommunicationItem;

use Rhubarb\Leaf\Controls\Common\DateTime\Date;
use Rhubarb\Leaf\Controls\Common\Text\TextBox;
use Rhubarb\Leaf\SearchPanel\Leaves\SearchPanel;
use Rhubarb\Stem\Filters\AnyWordsGroup;
use Rhubarb\Stem\Filters\Contains;
use Rhubarb\Stem\Filters\GreaterThan;
use Rhubarb\Stem\Filters\Group;
use Rhubarb\Stem\Filters\LessThan;
use Rhubarb\Stem\Filters\OneOf;
use Rhubarb\Pikaday\Pikaday;

class CommunicationItemSearchPanel extends SearchPanel
{
    protected function createSearchControls()
    {
        if (class_exists(Pikaday::class)) {
            $dateClass = Pikaday::class;
        } else {
            $dateClass = Date::class;
        }

        /** @var Date[] $dates */
        $dates[] = new $dateClass('CreatedAfter');
        $dates[] = new $dateClass('CreatedBefore');
        $dates[] = new $dateClass('SentBefore');
        $dates[] = new $dateClass('SentAfter');

        foreach ($dates as $date) {
            if ($date instanceof Date) {
                $date->setOptional();
            }
        }

        return array_merge(
            [
                new TextBox('Title'),
                new TextBox('Recipient')
            ],
            $dates
        );
    }

    public function populateFilterGroup(Group $filterGroup)
    {
        $searchValues = $this->model->searchValues;

        if ($searchValues['Title']) {
            $filterGroup->addFilters(new Contains('Title', $searchValues['Title']));
        }

        if ($searchValues['Recipient']) {
            $filterGroup->addFilters(new Contains('Recipient', $searchValues['Recipient']));
        }

        if ($searchValues['CreatedAfter']) {
            $filterGroup->addFilters(new GreaterThan('DateCreated', $searchValues['CreatedAfter']));
        }
        if ($searchValues['CreatedBefore']) {
            $filterGroup->addFilters(new LessThan('DateCreated', $searchValues['CreatedBefore']));
        }

        if ($searchValues['SentAfter']) {
            $filterGroup->addFilters(new GreaterThan('DateSent', $searchValues['SentAfter']));
        }
        if ($searchValues['SentBefore']) {
            $filterGroup->addFilters(new LessThan('DateSent', $searchValues['SentBefore']));
        }
    }
}
