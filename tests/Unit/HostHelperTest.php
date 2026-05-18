<?php

namespace Tests\Unit;

use App\Support\HostHelper;
use PHPUnit\Framework\TestCase;

class HostHelperTest extends TestCase
{
    public function test_normalize_strips_www_prefix(): void
    {
        $this->assertSame('example.com', HostHelper::normalize('www.Example.com'));
    }

    public function test_matches_ignores_www_difference(): void
    {
        $this->assertTrue(HostHelper::matches('www.example.com', 'example.com'));
    }

    public function test_from_url_normalizes_host(): void
    {
        $this->assertSame('example.com', HostHelper::fromUrl('https://www.example.com/path'));
    }
}
