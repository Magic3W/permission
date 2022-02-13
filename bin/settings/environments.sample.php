<?php

use spitfire\core\Environment;

/*
 * Creates a test environment that can be used to store configuration that affects
 * the behavior of an application.
 */
$e = new Environment('dev');
// Database
$e->set('db', 'mysqlpdo://www:test@mysql:3306/testdb');
$e->set('db_table_prefix', 'test_');

// oAuth
$e->set('sso', 'http://todo:todo@host.docker.internal:8085');

// Spitfire
$e->set('server_name', 'localhost:8091');
$e->set('debug_mode', true);
