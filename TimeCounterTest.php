<?php

require_once 'TimeCounter.php';

class TimeCounterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testTimeCountProvider
     * @param array $input
     * @param $result
     */
    public function testTimeCount($input, $result)
    {
        $timeCounter = new TimeCounter($input);

        $this->assertEquals($result, $timeCounter->getTotalTime());
    }

    public function testTimeCountProvider()
    {
        return [
            [
                '14,00 - 15,45
                16.08 - 16.58',
                '14.00 - 15.45 = 1.45'.PHP_EOL
                .'16.08 - 16.58 = 0.50'.PHP_EOL
                .'----'.PHP_EOL
                .'2.35'
            ],

            [
                '23,01 - 03,01 ',
                '23.01 - 03.01 = 4.00'.PHP_EOL
                .'----'.PHP_EOL
                .'4.00',
            ],

            [
                '14.01-14.05
                     12,42  -  12.48


                23.24 - 01.13',
                '14.01 - 14.05 = 0.04'.PHP_EOL
                .'12.42 - 12.48 = 0.06'.PHP_EOL
                .'23.24 - 01.13 = 1.49'.PHP_EOL
                .'----'.PHP_EOL
                .'1.59'
            ],
        ];
    }

    /**
     * @throws InvalidArgumentException
     * @expectedException InvalidArgumentException
     */
    public function testInvalidInput()
    {
        $invalidInput = '11.40 - 13,30

                  14,18 -   16.34


                -->

                11.40 - 13,30 = 1.50
                14,18 - 16.34 = 2.16
                ----
                4.06';
        $timeCounter = new TimeCounter($invalidInput);

        $timeCounter->getTotalTime();
    }

    public function testLineOutput()
    {
        $input = '11.40 - 13,30

                  14,18 -   16.34

            ';
        $output = '11.40 - 13.30 = 1.50'.PHP_EOL
            .'14.18 - 16.34 = 2.16'.PHP_EOL
            .'----'.PHP_EOL
            .'4.06';

        $timeCounter = new TimeCounter($input);

        $this->assertEquals($output, $timeCounter->getTotalTime());
    }
}
