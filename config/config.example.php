<?php
return [
    "slack_token" => "",
    "news_api" => "",
    "commands" => [
        \Commands\FakeNewsCommand::class,
        \Commands\StockCommand::class,
        \Commands\StonkCommand::class,
        \Commands\StonkNewsCommand::class,
        \Commands\UserStonksCommand::class,
        \Commands\AllTimeHighCommand::class,
        \Commands\WhatIfCommand::class,
        \Commands\KarmaCommand::class,
        \Commands\FUDCommand::class,
        \Commands\MillennialCapitalizationCommand::class,
        \Commands\UrbanDictionaryCommand::class
    ],
];