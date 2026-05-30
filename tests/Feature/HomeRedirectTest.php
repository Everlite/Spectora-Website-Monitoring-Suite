<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomeRedirectTest extends TestCase
{
    public function test_home_redirects_guests_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }
}
