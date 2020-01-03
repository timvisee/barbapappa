<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;

class ExampleTest extends TestCase {

    /**
     * Set up some used macro's for use in tests.
     */
    protected function setUp(): void {
        parent::setUp();

        // Make the test context available
        $test = $this;

        // Allow us to follow a redirect
        TestResponse::macro('followRedirects', function ($testCase = null) use($test) {
            $response = $this;
            $testCase = $testCase ?: $test;

            // Follow all redirects
            while ($response->isRedirect())
                $response = $testCase->get($response->headers->get('Location'));

            return $response;
        });
    }

    // // TODO: rework this test, test for redirection or automatic language selection
    // /**
    //  * Test whether the first request to the home page redirects properly to the language selection page,
    //  * because the user hasn't selected a language yet.
    //  *
    //  * @return void
    //  */
    // public function testHomeToLanguageSelect() {
    //     // Visit the home page
    //     $response = $this->get('/');

    //     // The user should see the language page, because the user hasn't selected a language yet
    //     $response->assertRedirect('language')
    //         ->followRedirects()
    //         ->assertViewIs('pages.language');
    // }

    /**
     * Test whether a proper 404 status code is returned on pages that don't exist.
     *
     * @return void
     */
    public function testNotFound() {
        $this->get('/some/page/that/does/not/exist')
            ->assertStatus(404);
    }
}
