<?php

namespace StingerSoft\PhpCommons\File;

use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase {
    public function testEscapeFilenameWithSlash() {
        $test = 'Filename with / Slash';
        $expected = 'Filename with _ Slash';
        $actual = Utils::escapeFilename($test);
        $this->assertEquals($expected, $actual);
    }

    public function testEscapeFilenameWithLesserThan() {
        $test = 'Filename with < lt';
        $expected = 'Filename with _ lt';
        $actual = Utils::escapeFilename($test);
        $this->assertEquals($expected, $actual);
    }

    public function testEscapeFilenameWithAllowedChars() {
        $test = 'Filename with $pecial ch#rs';
        $actual = Utils::escapeFilename($test);
        $this->assertEquals($test, $actual);
    }
}