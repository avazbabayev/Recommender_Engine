<?php
class Ranking
{
    protected $product = [];
    protected $other = [];
    protected $set = [];


    public function __construct($set)
    {
        $this->set = $set;
    }


    public function recommend($user)
    {
        $data = $this->addRating($user);
        return $this->filterRating($data);
    }

    private function similarUser($user)
    {
        $this->ratedProduct($user);
        $similar = [];
        $rank = [];
        foreach ($this->product as $myProduct) {
            foreach ($this->other as $item) {
                if ($myProduct['song'] == $item['song']) {
//                   if($myProduct['rating'] == $item['rating']){
                    if (!isset($similar[$item['user']]))
                        $similar[$item['user']] = 0; //
                    $similar[$item['user']] += 1; //assigning weight
//                   }
                }
            }
        }
        return $similar;
    }




    private function addRating($user)
    {
        $similar = $this->similarUser($user);
        $rank = [];
        foreach ($this->other as $item) {
            foreach ($similar as $value) {
                if ($item['user'] == key($similar)) {
                    if (!isset($rank[$item['song']]))
                        $rank[$item['song']] = 0;
                    $rank[$item['song']] += $value;
                }
                next($similar);
            }
            reset($similar);
        }
        return $rank;
    }


    public function filterRating($data)
    {
        $myRank = $data;
        $rank = $myRank;
        for ($i = 0; $i < count($myRank); $i++) {
            foreach ($this->product as $item) {
                if ($item['song'] == key($myRank))
                    unset($rank[key($myRank)]); // remove product
            }
            next($myRank);
        }
        arsort($rank);
        return $rank;
    }

    public function ratedProduct($user)
    {
        foreach ($this->set as $item) {
            if($item['user'] == $user){
                $this->product[] = $item;
            }else{
                $this->other[] = $item;
            }
        }
    }

}
