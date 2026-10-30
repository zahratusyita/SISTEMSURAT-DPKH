<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();
        $connection = $app['config']['database.default'];
        $database = $app['config']['database.connections.'.$connection.'.database'];
        if ($connection !== 'mysql' || $database !== 'sipas_test') {
            throw new \RuntimeException('Pengujian hanya diizinkan pada MySQL database sipas_test.');
        }

        return $app;
    }
}
