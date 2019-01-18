<?php

return [
    'slack' => [
        'token' => getenv('SLACK_TOKEN')
    ],
    'news' => [
        'key' => getenv('NEWS_API_KEY')
    ]
];
