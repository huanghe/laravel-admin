<?php

use Encore\Admin\Auth\Database\Administrator;

class AuthTest extends TestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->user = Administrator::first();

//        $this->be($this->user, 'admin');
    }

    public function testLogin()
    {
        $credentials = ['username' => 'admin', 'password' => 'admin'];
        $this->json('POST', 'admin/login', $credentials)
            ->seeJson(['username' => 'admin', 'expires_in' => 7200]);
    }

}
