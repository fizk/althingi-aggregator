<?php

return [
    '/'                                     => App\Handler\Help::class,
    '/load:assembly'                        => App\Handler\Assembly\Find::class,
    '/load:assembly:current'                => App\Handler\Assembly\Current::class,
    '/load:party'                           => App\Handler\Party\Find::class,
    '/load:constituency'                    => App\Handler\Constituency\Find::class,
    '/load:congressman'                     => App\Handler\Congressman\Find::class,     //[--assembly=|-a],
    '/load:minister'                        => App\Handler\Congressman\Minister::class, //[--assembly=|-a],
    '/load:ministry'                        => App\Handler\Ministry\Find::class,
    '/load:parliamentary-session'           => App\Handler\ParliamentarySession\Find::class,         //[--assembly=|-a],
    '/load:parliamentary-session-agenda'    => App\Handler\ParliamentarySession\Agenda::class,       //[--assembly=|-a],
    '/load:issue'                           => App\Handler\Issue\Find::class,           //[--assembly=|-a],
    '/load:single-issue'                    => \App\Handler\Issue\Single::class,        //[--assembly=|-a]  [--issue=|-i]  [--category=|-c],
    '/load:committee'                       => App\Handler\Committee\Find::class,
    '/load:committee-assembly'              => App\Handler\Committee\Assembly::class,   //[--assembly=|-a],
    '/load:president'                       => App\Handler\President\Find::class,       //[--assembly=|-a],
    '/load:category'                        => App\Handler\Category\Find::class,
    '/load:inflation'                       => App\Handler\Inflation\Find::class,       //[--date=|-d],
    '/load:government'                      => App\Handler\Government\Find::class,
    '/load:tmp-speech'                      => App\Handler\Speech\Temporary::class,     //[--assembly=|-a],
];
