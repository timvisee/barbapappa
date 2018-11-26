<?php

namespace Tests\Feature;

use Tests\TestCase;

class LanguageParseTest extends TestCase {

    /**
     * Test whether the default language is English.
     *
     * @return void
     */
    public function testDefaultEnglish() {
        self::assertEquals(__('general.hello'), 'Hello');
    }

    /**
     * Test whether parsing the English language succeeds.
     *
     * @return void
     */
    public function testParseEnglish() {
        self::assertEquals(__('general.hello', [], 'en'), 'Hello');
    }

    /**
     * Test whether parsing the Dutch language succeeds.
     *
     * @return void
     */
    public function testParseDutch() {
        self::assertEquals(__('general.hello', [], 'nl'), 'Hallo');
    }

    /**
     * Test whether parsing the pirate language succeeds.
     *
     * @return void
     */
    public function testParsePirate() {
        self::assertEquals(__('general.hello', [], 'pirate'), 'Ahoy');
    }
}
