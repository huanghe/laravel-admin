<?php

use Encore\Admin\Auth\Database\Administrator;

/**
 * Class UsersApiTest
 * method:vendor/phpunit/phpunit/phpunit tests/UsersApiTest.php
 */
class UsersApiTest extends TestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->user = Administrator::first();

        $this->be($this->user, 'admin');
    }

    public function testUsersIndex()
    {
        $this->json('GET', '/admin/auth/api/users', ['name' => 'Sally'])
            ->seeJson([
                'created' => true,
            ]);
    }
}
