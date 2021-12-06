<?php

class Pearson
{
    protected $set = [];

    public function __construct($set)
    {
        $this->set = $set;
    }


    public function recommend($for)
    {
        $recommends = [];
        try{
            $nearest = $this->findNearest($for);
            foreach ($nearest as $item){
                array_push($recommends,$item['key']);
            }
        }catch (Exception $e){

        }
        return $recommends;

    }


    public function findNearest($for)
    {
        $distances = [];

        foreach ($this->set as $key => $itemData) {
            if ($key == $for) {
                continue;
            }
            $distance = $this->run($itemData, $this->set[$for]);

            if ($distance === false) {

                continue;
            }
            $distances[] = ['key' => $key, 'value' => $distance];
        }
        if (!count($distances)) {

            return false;
        }

        $this->sort($distances, true);
        $arr = array_slice($distances, 0, 3);
        return $arr;
    }

    private function sort(&$distances, $ascending = true)
    {
        usort($distances, function ($first, $second) use ($ascending) {
            if ($first['value'] > $second['value']) {
                return ($ascending) ? 1 : -1;
            } elseif ($first['value'] < $second['value']) {
                return ($ascending) ? -1 : 1;
            }
            return 0;
        });
    }

    public function run($allsongs, $mysong)
    {
        $numCoRatedItems = 0;
        $dotProduct = 0;
        $rating1Sum = 0;
        $rating1SumSqr = 0;
        $rating2Sum = 0;
        $rating2SumSqr = 0;

        foreach ($allsongs as $item => $rating1) {
            if (!isset($mysong[$item])) {
                continue;
            }
            $numCoRatedItems += 1;
            $dotProduct += $rating1 * $mysong[$item];
            $rating1Sum += $rating1;
            $rating1SumSqr += pow($rating1, 2);
            $rating2Sum += $mysong[$item];
            $rating2SumSqr += pow($mysong[$item], 2);

        }

        if ($numCoRatedItems == 0) {
            return false;
        }


        $denom = sqrt(
            ($rating1SumSqr - (pow($rating1Sum, 2) / $numCoRatedItems)) *
            ($rating2SumSqr - (pow($rating2Sum, 2) / $numCoRatedItems))
        );

        if ($denom == 0) {
            return false;
        }

        $pearson = ($dotProduct - ($rating1Sum * $rating2Sum / $numCoRatedItems)) / $denom;
        $pearson2 = ($dotProduct - ($rating1Sum * $rating2Sum / $numCoRatedItems)) / $denom;

        return 1 - abs($pearson);;

    }
}
