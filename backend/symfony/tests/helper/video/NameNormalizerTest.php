<?php

namespace App\Tests\Helper\Video;

use App\Helper\Video\NameNormalizer;
use PHPUnit\Framework\TestCase;

class NameNormalizerTest extends TestCase
{
    public function testNormalizeRemovesDiacritics()
    {
        $name = 'Pán prstenů: Dvě věže';
        $expected = 'pan-prstenu-dve-veze';
        $this->assertEquals($expected, NameNormalizer::normalize($name));
    }

    public function testNormalizeConvertsToLowerCase()
    {
        $name = 'HELLO World';
        $expected = 'hello-world';
        $this->assertEquals($expected, NameNormalizer::normalize($name));
    }

    public function testNormalizeReplacesSpecialCharacters()
    {
        $name = 'This is a test: Hello World!';
        $expected = 'this-is-a-test-hello-world';
        $this->assertEquals($expected, NameNormalizer::normalize($name));
    }

    public function testNormalizeHandlesEmptyString()
    {
        $name = '';
        $expected = '';
        $this->assertEquals($expected, NameNormalizer::normalize($name));
    }

    public function testNormalizeHandlesSpecialCharacters()
    {
        $name = 'C++ Programming & Development';
        $expected = 'c-programming-development';
        $this->assertEquals($expected, NameNormalizer::normalize($name));
    }

    public function testNormalizeHandlesMultipleSpaces()
    {
        $name = '   Multiple   spaces   here   ';
        $expected = 'multiple-spaces-here';
        $this->assertEquals($expected, NameNormalizer::normalize($name));
    }

    public function testNormalizeReplacesHyphens()
    {
        $name = 'This - is - hyphenated';
        $expected = 'this-is-hyphenated';
        $this->assertEquals($expected, NameNormalizer::normalize($name));
    }
}

