<?php

return [
    '/user/phossa'  => 'handler1',
    '/user/view[/{id:d=23}]' => ['handler2', 'GET'],
    '/user/{action:xd}[/{id:d}]'   => [['controller', 'action'], 'GET,POST', ['id' => 2]],
];
