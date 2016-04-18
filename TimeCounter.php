<?php

class TimeCounter
{
    private $timeLines;

    // todo: think about variable delimiter
    public function __construct($spentTimeText, $maxWorkHours = 8)
    {
        $this->timeLines = explode(PHP_EOL, $spentTimeText);
    }

    public function getTotalTime()
    {
        $spentTimeText = '';

        $totalTime = [
            'hours' => 0,
            'minutes' => 0
        ];

        foreach ($this->timeLines as $timeLine) {
            $trimmedTimeLine = trim($timeLine);

            if ($trimmedTimeLine != '') {
                $timeLineParsed = $this->parseTimeLine($trimmedTimeLine);
                $spentTime = $timeLineParsed['end']->diff($timeLineParsed['start']);

                $totalTime = $this->accumulateResult($totalTime, $spentTime->format('%h.%i'));
                $spentTimeText = $spentTimeText.$this->formatTimeLine($timeLineParsed, $spentTime);
            }
        }

        return $spentTimeText.str_pad('----', 20, ' ', STR_PAD_LEFT).PHP_EOL
            .str_pad($totalTime['hours'].':'.str_pad($totalTime['minutes'], 2, '0', STR_PAD_LEFT), 20, ' ', STR_PAD_LEFT).PHP_EOL;
    }

    private function parseTimeLine($timeLine)
    {
        $timeParsed = $this->getStartEndTime($timeLine);

        $timeStart = new DateTime('now');
        $timeStart->setTime($timeParsed['start']['hours'], $timeParsed['start']['minutes']);

        $timeEnd = new DateTime('tomorrow');
        $timeEnd->setTime($timeParsed['end']['hours'], $timeParsed['end']['minutes']);

        return [
            'start' => $timeStart,
            'end' => $timeEnd
        ];
    }

    private function getStartEndTime($timeLine)
    {
        $pattern = '/^(\d+)[.|,](\d+)\s*-\s*(\d+)[.|,](\d+)$/';

        $isSuccess = preg_match($pattern, $timeLine, $timeParsed);

        if (!$isSuccess) {
            throw new InvalidArgumentException("Incorrect time string format: '".$timeLine."', terminating!");
        }

        return [
            'start' => [
                'hours' => $timeParsed[1],
                'minutes' => $timeParsed[2],
            ],

            'end' => [
                'hours' => $timeParsed[3],
                'minutes' => $timeParsed[4],
            ]
        ];
    }

    private function formatTimeLine($timeInterval, DateInterval $spentTime)
    {
        return $timeInterval['start']->format('H:i').' - '.
            $timeInterval['end']->format('H:i').' = '.$spentTime->format('%h:%I').PHP_EOL;
    }

    private function accumulateResult($totalTime, $diffToAdd)
    {
        $pattern = '/(\d+).(\d+)/';

        preg_match($pattern, $diffToAdd, $diffParsed);

        $totalTime['hours'] += $diffParsed[1];
        $totalTime['minutes'] += $diffParsed[2];

        while ($totalTime['minutes'] >= 60) {
            $totalTime['hours'] += 1;
            $totalTime['minutes'] -= 60;
        }

        return $totalTime;
    }
}