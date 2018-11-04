<?php

namespace Laravel\Telescope\Tests\Http;

use Laravel\Telescope\Telescope;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\Tests\FeatureTestCase;
use Orchestra\Testbench\Http\Middleware\VerifyCsrfToken;

class AuthorizationTest extends FeatureTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->withoutMiddleware([VerifyCsrfToken::class]);
    }

    protected function tearDown()
    {
        parent::tearDown();

        Telescope::auth(null);
    }

    public function test_unauthorized_by_gate()
    {
        Gate::define('viewTelescope', function ($user) {
            return false;
        });

        $this->post('/telescope/telescope-api/requests')
            ->assertStatus(403);
    }

    public function test_unauthorized_requests()
    {
        Telescope::auth(function () {
            return false;
        });

        $this->get('/telescope/telescope-api/requests')
            ->assertStatus(403);
    }

    public function test_authorized_requests()
    {
        Telescope::auth(function () {
            return true;
        });

        $this->post('/telescope/telescope-api/requests')
            ->assertSuccessful();
    }
}
