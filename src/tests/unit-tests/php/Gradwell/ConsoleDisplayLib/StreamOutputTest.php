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

if (!function_exists('ncurses_init'))
{
        function ncurses_init()
        {
                // do nothing
        }

        function ncurses_newwin()
        {
                // do nothing
        }

        function ncurses_wrefresh()
        {
                // do nothing
        }

        function ncurses_getmaxyx($window, &$height, &$width)
        {
                $width = 80;
                $height = 24;
        }

        function ncurses_end()
        {
                // do nothing
        }
}

class StreamOutputTest extends \PHPUnit_Framework_TestCase
{
        public function testCanCreate()
        {
                $outputEngine = new StreamOutput('php://stdout');

                // did it work?
                $this->assertTrue ($outputEngine instanceof StreamOutput);
        }

        public function testImplementsConsoleOutputEngineInterface()
        {
                $outputEngine = new StreamOutput('php://stdout');

                // did it work?
                $this->assertTrue ($outputEngine instanceof ConsoleOutputEngine);
        }

        public function testCanWriteStrings()
        {
                // setup the test
                $filename = tempnam('/tmp', __CLASS__);
                $outputEngine = new StreamOutput($filename);
                $testString = 'a test string';

                // perform the test
                $outputEngine->writePartialLine($testString);

                // did it work?
                $this->assertTrue(file_exists($filename));
                if (!file_exists($filename))
                {
                        // no point in running the remaining tests
                        return;
                }

                $writtenString = file_get_contents($filename);
                unlink($filename);

                $this->assertEquals($testString, $writtenString);
        }

        public function testCanWriteBlankLines()
        {
                // setup the test
                $filename = tempnam('/tmp', __CLASS__);
                $outputEngine = new StreamOutput($filename);
                $expectedString = \PHP_EOL . \PHP_EOL;

                // perform the test
                $outputEngine->writeEmptyLines(2);

                // did it work?
                $this->assertTrue(file_exists($filename));
                if (!file_exists($filename))
                {
                        // no point in running the remaining tests
                        return;
                }

                $writtenString = file_get_contents($filename);
                unlink($filename);

                $this->assertEquals($expectedString, $writtenString);
        }

        public function testCanTestForColorSupport()
        {
                // setup the test
                $outputEngine = new StreamOutput('php://stdout');

                // perform the test
                $allowColors = $outputEngine->supportsColors();

                // check the results
                // we cannot guarantee whether stdin will support color
                // or not at the time we run the test, just to make
                // like a little more complicated
                $this->assertTrue(is_bool($allowColors));
        }

        public function testCanGetTerminalWidth()
        {
                //
                // what happens if there is no COLUMNS set?
                //

                $outputEngine = new StreamOutput('php://stdout');
                $outputEngine->forceTty();

                // perform the test
                $this->assertFalse(getenv('COLUMNS'));
                $this->assertEquals(78, $outputEngine->getColumnsHint());

                //
                // and if we set COLUMNS to something sensible?
                //
                
                putenv('COLUMNS=10');
                $outputEngine = new StreamOutput('php://stdout');
                $outputEngine->forceTty();

                // perform the test
                $this->assertEquals(10, getenv('COLUMNS'));
                $this->assertEquals(10, $outputEngine->getColumnsHint());

                //
                // and if we set COLUMNS to something stupid?
                //

                putenv('COLUMNS=');
                $outputEngine = new StreamOutput('php://stdout');
                $outputEngine->forceTty();

                // perform the test
                $this->assertEquals('', getenv('COLUMNS'));
                $this->assertEquals(78, $outputEngine->getColumnsHint());
        }
}