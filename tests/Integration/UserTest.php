<?php


namespace DocSDK\Tests\Integration;


use DocSDK\Models\User;


class UserTest extends TestCase
{

    public function testMe()
    {

        $user = $this->docSDK->users()->me();

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->getId());
        $this->assertNotNull($user->getUsername());
        $this->assertNotNull($user->getEmail());
        $this->assertNotNull($user->getCredits());


    }

}
