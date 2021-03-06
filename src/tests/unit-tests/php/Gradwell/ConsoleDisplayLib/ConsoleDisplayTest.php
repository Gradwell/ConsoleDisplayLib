<?php

/**
 * Copyright (c) 2010 Gradwell dot com Ltd.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Gradwell dot com Ltd nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     Gradwell
 * @subpackage  ConsoleDisplayLib
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://gradwell.github.com
 * @version     @@PACKAGE_VERSION@@
 */

namespace Gradwell\ConsoleDisplayLib;

class ConsoleDisplayTest extends \PHPUnit_Framework_TestCase
{
        public function testCanGenerateSingleStyle()
        {
                $consoleDisplay = new DevString();
                $output = $consoleDisplay->style($consoleDisplay->bold);

                $expectedValue = "\033[1m";
                $this->assertEquals($expectedValue, $output);
        }

        public function testCanGenerateSeveralStyles()
        {
                $consoleDisplay = new DevString();
                $output = $consoleDisplay->style(array($consoleDisplay->bold, $consoleDisplay->fgRed));

                $expectedValue = "\033[1;31m";
                $this->assertEquals($expectedValue, $output);
        }

        public function testCanResetStyle()
        {
                $consoleDisplay = new DevString();
                $output = $consoleDisplay->resetStyle();

                $expectedValue = "\033[0m";
                $this->assertEquals($expectedValue, $output);
        }

        public function testCanOutputString()
        {
                $testString = 'test string';
                $consoleDisplay = new DevString();
                $consoleDisplay->output(null, $testString);
                $output = $consoleDisplay->_getOutput();

                $this->assertEquals($testString, $output);
        }

        public function testCanOutputStringAndForgetColor()
        {
                $testString = 'test string';
                $consoleDisplay = new DevString(false);
                $consoleDisplay->output($testString);
                $output = $consoleDisplay->_getOutput();

                $this->assertEquals($testString, $output);
        }

        public function testOutputsColorIfEngineSupportsColor()
        {
                $testString = 'test string';
                $outputEngine =
                $consoleDisplay = new DevString(true);

                $consoleDisplay->output($consoleDisplay->bgBlack, $testString);
                $output = $consoleDisplay->_getOutput();

                $this->assertEquals($consoleDisplay->bgBlack . $testString . $consoleDisplay->resetStyle(), $output);
        }

        public function testDoesNotOutputColorIfEngineDoesNotSupportColor()
        {
                $testString = 'test string';
                $consoleDisplay = new DevString(false);

                $consoleDisplay->output($consoleDisplay->bgBlack, $testString);
                $outputWithNoColour = $consoleDisplay->_getOutput();

                $this->assertEquals($testString, $outputWithNoColour);

                // to prove it is different, we will now generate the
                // output with color, and show that they are not the same

                $consoleDisplay = new DevString(true);

                $consoleDisplay->output($consoleDisplay->bgBlack, $testString);
                $outputWithColour = $consoleDisplay->_getOutput();

                $this->assertEquals($consoleDisplay->bgBlack . $testString . $consoleDisplay->resetStyle(), $outputWithColour);

                $this->assertNotEquals($outputWithColour, $outputWithNoColour);
        }

        public function testCanOutputTextWithLineEnding()
        {
                $testString = 'test string';
                $consoleDisplay = new DevString(false);

                $consoleDisplay->outputLine($consoleDisplay->bgBlack, $testString);
                $output = $consoleDisplay->_getOutput();

                $this->assertEquals($testString . \PHP_EOL, $output);
        }

        public function testCanOutputTextWithLineEndingAndForGetColor()
        {
                $testString = 'test string';
                $consoleDisplay = new DevString(false);

                $consoleDisplay->outputLine($testString);
                $output = $consoleDisplay->_getOutput();

                $this->assertEquals($testString . \PHP_EOL, $output);
        }

        public function testCanAppendStringsTogetherWithoutColour()
        {
                $testString1 = 'test string 1';
                $testString2 = ' + test string 2';
                $consoleDisplay = new DevString(false);

                $consoleDisplay->output($consoleDisplay->bgBlack, $testString1);
                $consoleDisplay->outputLine($consoleDisplay->bgBlack, $testString2);
                $output = $consoleDisplay->_getOutput();

                $this->assertEquals($testString1 . $testString2 . \PHP_EOL, $output);
        }

        public function testCanAppendStringsTogetherWithColour()
        {
                $testString1 = 'test string 1';
                $testString2 = ' + test string 2';

                $consoleDisplay = new DevString(true);

                $consoleDisplay->output($consoleDisplay->fgRed, $testString1);
                $consoleDisplay->outputLine($consoleDisplay->fgCyan, $testString2);
                $output = $consoleDisplay->_getOutput();

                $expectedResult = $consoleDisplay->fgRed . $testString1 . $consoleDisplay->resetStyle()
                                . $consoleDisplay->fgCyan . $testString2 . $consoleDisplay->resetStyle()
                                . \PHP_EOL;

                $this->assertEquals($expectedResult, $output);
        }

        public function testCanOutputBlankLine()
        {
                $consoleDisplay = new DevString(false);

                $consoleDisplay->outputBlankLine();
                $output = $consoleDisplay->_getOutput();

                $this->assertEquals(\PHP_EOL, $output);
        }

        public function testCanOutputBlankLineWithIncompleteLineBefore()
        {
                $testString = 'test string';
                $consoleDisplay = new DevString(false);

                $consoleDisplay->output($consoleDisplay->bgBlack, $testString);
                $consoleDisplay->outputBlankLine();
                $output = $consoleDisplay->_getOutput();

                $this->assertEquals($testString . \PHP_EOL . \PHP_EOL, $output);
        }

        public function testCanOutputMultipleLinesAtOnce()
        {
                $testString = 'test string 1' . \PHP_EOL . 'test string 2';
                $consoleDisplay = new DevString(false);

                $consoleDisplay->outputLine(null, $testString);
                $output = $consoleDisplay->_getOutput();

                $this->assertEquals($testString . \PHP_EOL, $output);
        }

        public function testDefaultIndentIsZero()
        {
                $consoleDisplay = new DevString();
                $this->assertEquals(0, $consoleDisplay->getIndent());
        }

        public function testCanSetIndent()
        {
                $consoleDisplay = new DevString();
                $this->assertEquals(0, $consoleDisplay->getIndent());

                // make the change
                $consoleDisplay->setIndent(4);
                $this->assertEquals(4, $consoleDisplay->getIndent());

                // prove it has an effect
                $testString = 'test string';
                $consoleDisplay->output(null, $testString);
                $output = $consoleDisplay->_getOutput();

                $this->assertEquals('    ' . $testString, $output);
        }

        public function testCanAddToIndent()
        {
                $consoleDisplay = new DevString();
                $this->assertEquals(0, $consoleDisplay->getIndent());

                // make the change
                $consoleDisplay->addIndent(4);
                $this->assertEquals(4, $consoleDisplay->getIndent());

                // prove it has an effect
                $testString = 'test string 1';
                $consoleDisplay->outputLine(null, $testString);
                $output = $consoleDisplay->_getOutput();

                $this->assertEquals('    ' . $testString . \PHP_EOL, $output);

                // change it again
                $consoleDisplay->addIndent(4);
                $this->assertEquals(8, $consoleDisplay->getIndent());

                // prove it has an effect
                $testString = 'test string 2';
                $consoleDisplay->_resetOutput();
                $consoleDisplay->output(null, $testString);
                $output = $consoleDisplay->_getOutput();

                $this->assertEquals('        ' . $testString, $output);
        }

        public function testIndentAffectsMultipleLines()
        {
                $consoleDisplay = new DevString();
                $this->assertEquals(0, $consoleDisplay->getIndent());

                // change the indent
                $consoleDisplay->setIndent(4);
                $this->assertEquals(4, $consoleDisplay->getIndent());

                // prove it has an effect
                $consoleDisplay->outputLine(null, 'test string 1' . \PHP_EOL . 'test string 2');
                $output = $consoleDisplay->_getOutput();
                $this->assertEquals('    test string 1' . \PHP_EOL . '    test string 2' . \PHP_EOL, $output);
        }

        public function testCanSetAndGetWrapPoint()
        {
                $consoleDisplay = new DevString();
                $this->assertEquals(78, $consoleDisplay->getWrapAt());

                // change the wrap point
                $consoleDisplay->setWrapAt(20);
                $this->assertEquals(20, $consoleDisplay->getWrapAt());

                // prove it has an effect
                $consoleDisplay->outputLine(null, '123456789012345678901234567890');
                $output = $consoleDisplay->_getOutput();
                $this->assertEquals('12345678901234567890' . \PHP_EOL . '1234567890' . \PHP_EOL, $output);

                // prove it is not a fluke
                $consoleDisplay->setWrapAt(10);
                $consoleDisplay->_resetOutput();
                $consoleDisplay->outputLine(null, '12345678901234567890');
                $output = $consoleDisplay->_getOutput();
                $this->assertEquals('1234567890' . \PHP_EOL . '1234567890' . \PHP_EOL, $output);
        }

        public function testCanWrapLongStrings()
        {
                $consoleDisplay = new DevString();
                $this->assertEquals(0, $consoleDisplay->getIndent());
                $this->assertEquals(78, $consoleDisplay->getWrapAt());

                // wrap the long string
                $consoleDisplay->outputLine(null, 'this is a very long string from the testCanWrapLongStrings() unit test method, to prove that ConsoleDisplay will wrap long strings properly.');
                $output = $consoleDisplay->_getOutput();
                $this->assertEquals('this is a very long string from the testCanWrapLongStrings() unit test method,' . \PHP_EOL . 'to prove that ConsoleDisplay will wrap long strings properly.' . \PHP_EOL, $output);
        }

        public function testWillWrapWhenAppendingStrings()
        {
                $consoleDisplay = new DevString();
                $this->assertEquals(0, $consoleDisplay->getIndent());
                $consoleDisplay->setWrapAt(10);

                // wrap the second string
                $consoleDisplay->output(null, '1234567890');
                $consoleDisplay->output(null, '1234567890');
                $output = $consoleDisplay->_getOutput();

                $this->assertEquals('1234567890' . \PHP_EOL . '1234567890', $output);
        }
        
        public function testWillOutputEolsAndWrapWhenAppendingStrings()
        {
                $consoleDisplay = new DevString();
                $this->assertEquals(0, $consoleDisplay->getIndent());
                $consoleDisplay->setWrapAt(10);

                // wrap the second string
                $consoleDisplay->output(null, '1234567890');
                $consoleDisplay->outputLine(null, '1234567890');
                $output = $consoleDisplay->_getOutput();

                $this->assertEquals('1234567890' . \PHP_EOL . '1234567890' . \PHP_EOL, $output);
        }

        public function testWillNotOutputWhitespaceAtEndOfWrappedLines()
        {
                $consoleDisplay = new DevString();
                $this->assertEquals(0, $consoleDisplay->getIndent());
                $consoleDisplay->setWrapAt(10);
                $expectedString = 'this is a' . \PHP_EOL . 'long' . \PHP_EOL . 'string to' . \PHP_EOL . 'be wrapped' . \PHP_EOL;

                // wrap a long string
                $consoleDisplay->outputLine(null, 'this is a long string to be wrapped');
                $output = $consoleDisplay->_getOutput();

                $this->assertEquals($expectedString, $output);

                // what happens if we try trickle-feed the input?
                $consoleDisplay = new DevString();
                $consoleDisplay->setWrapAt(10);

                $consoleDisplay->output(null, 'this is a ');
                $consoleDisplay->output(null, 'long ');
                $consoleDisplay->output(null, 'string to ');
                $consoleDisplay->outputLine(null, 'be wrapped');

                $output = $consoleDisplay->_getOutput();
                $this->assertEquals($expectedString, $output);

                // this problem has been seen with phix
                $expectedString = '    * Phix_Project\PhixExtensions\DummyCommandWithSwitches' . \PHP_EOL;
                $consoleDisplay = new DevString();
                $consoleDisplay->setWrapAt(78);

                $consoleDisplay->setIndent(4);
                $consoleDisplay->output(null, '* ');
                $consoleDisplay->outputLine(null, 'Phix_Project\PhixExtensions\DummyCommandWithSwitches');

                $output = $consoleDisplay->_getOutput();
                $this->assertEquals($expectedString, $output);
        }
}