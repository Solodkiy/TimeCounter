<?php

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

        $this->assertEquals($result, trim($timeCounter->getTotalTime()));
    }

    public function testTimeCountProvider()
    {
        $result = [];
        foreach (glob(__DIR__.'/data/*.in') as $inputFilePath) {
            $result[$inputFilePath] = [
                (file_get_contents($inputFilePath)),
                (file_get_contents(str_replace('.in', '.out', $inputFilePath))),
            ];
        }
        return $result;
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
                4.06'.PHP_EOL;
        $timeCounter = new TimeCounter($invalidInput);

        $timeCounter->getTotalTime();
    }

}
