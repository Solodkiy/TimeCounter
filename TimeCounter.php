<?php

class TimeCounter
{
    private $totalTime = [
        'hours' => 0,
        'minutes' => 0
    ];

    private $timeLines;

    private $spentTimeLinesList;

    public function __construct($spentTimeText)
    {
        $this->timeLines = explode(PHP_EOL, $spentTimeText);
    }

    public function getTotalTime()
    {
        $this->countSpentTime();

        $totalTimeText = $this->getTimeByLines();
        $totalTimeText = $totalTimeText.'Total: ';
        return $totalTimeText.$this->totalTime['hours'].'.'.$this->formatMinutesOutput($this->totalTime['minutes']);
    }

    private function countSpentTime()
    {
        foreach ($this->timeLines as $timeLine) {
            $trimmedTimeLine = trim($timeLine);

            if ($trimmedTimeLine != '') {
                $spentTime = $this->getSpentTime($trimmedTimeLine);
                $this->accumulateResult($spentTime->format('%h.%i'));
            }
        }
    }

    private function getSpentTime($timeLine)
    {
        $timeParsed = $this->getStartEndTime($timeLine);

        $timeStart = new DateTime('now');
        $timeStart->setTime($timeParsed['start']['hours'], $timeParsed['start']['minutes']);

        $timeEnd = new DateTime('tomorrow');
        $timeEnd->setTime($timeParsed['end']['hours'], $timeParsed['end']['minutes']);

        $spentTime = $timeStart->diff($timeEnd);
        $this->addSpentTimeToList($timeParsed, $spentTime);

        return $spentTime;
    }

    private function getStartEndTime($timeLine)
    {
        $pattern = '/^(\d+)[.|,](\d+) *- *(\d+)[.|,](\d+)$/';

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

    private function addSpentTimeToList($timeInterval, DateInterval $spentTime)
    {
        $this->spentTimeLinesList[] =
            $timeInterval['start']['hours'].'.'.$timeInterval['start']['minutes'].' - '.
            $timeInterval['end']['hours'].'.'.$timeInterval['end']['minutes'].' = '.$spentTime->format('%h.%I');
    }

    private function accumulateResult($diffToAdd)
    {
        $pattern = '/(\d+).(\d+)/';

        preg_match($pattern, $diffToAdd, $diffParsed);

        $this->totalTime['hours'] += $diffParsed[1];
        $this->totalTime['minutes'] += $diffParsed[2];

        while ($this->totalTime['minutes'] > 60) {
            $this->totalTime['hours'] += 1;
            $this->totalTime['minutes'] -= 60;
        }
    }

    private function getTimeByLines()
    {
        $output = '';

        foreach ($this->spentTimeLinesList as $spentTimeLine) {
            $output = $output.$spentTimeLine.PHP_EOL;
        }

        return $output;
    }

    private function formatMinutesOutput($minutes)
    {
        if ($minutes < 10) {
            return '0'.$minutes;
        }

        return $minutes;
    }
}