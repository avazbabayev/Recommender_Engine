<?php

class QFD
{
    protected $tagset = [];
    protected $alldata = [];
    protected $songdata = [];
    protected $composerset = [];

    /**
     * all existed tags
     * @var string[]
     */
    public $tags = ['jn_spr_deutsch', 'jn_spr_english', 'jn_spr_francais', 'jn_spr_espanol', 'jn_reim_kein',
        'jn_reim_aabb', 'jn_reim_abab', 'jn_reim_abba', 'jn_reim_aaaa', 'jn_reim_aabccb', 'jn_reim_ababcb',
        'jn_reim_andere', 'jn_mood_angry', 'jn_mood_dark', 'jn_mood_dreamy', 'jn_mood_eccentric',
        'jn_mood_euphoric', 'jn_mood_fear', 'jn_mood_funny', 'jn_mood_happy', 'jn_mood_hopeful', 'jn_mood_love',
        'jn_mood_mystical', 'jn_mood_pastoral', 'jn_mood_peaceful', 'jn_mood_quirky', 'jn_mood_romantic',
        'jn_mood_sad', 'jn_mood_sentimental', 'jn_mood_sexy', 'jn_mood_weird', 'jn_reim_rap',
        'jn_mood_wistful', 'jn_mood_contemplative', 'jn_mood_celebratory', 'jn_mood_religious'];


    public function __construct($alldata, $songdata)
    {
        $this->alldata = $alldata;
        $this->songdata = $songdata;
    }


    public function recommend()
    {
        $this->tag_prio();
        $this->composer_prio();
        $calc = $this->calculate();
        $recomendations = [];
        $i = 0;
        foreach ($calc as $key=>$value){
            array_push($recomendations,$key);
            $i ++;
            if($i==3){
                break;
            }
        }
        return $recomendations;
    }

    public function tag_prio()
    {
        foreach ($this->alldata as $d) {
            foreach ($this->tags as $tag) {
                if ($d[$tag] and $d[$tag] == 1) {
                    if (!isset($this->tagset[$tag]))
                        $this->tagset[$tag] = 0;
                    $this->tagset[$tag] += $d['rating'];
                }
            }
        }
        arsort($this->tagset);
        $this->tagset = $this->create_ranking($this->tagset);
    }

    public function composer_prio()
    {
        foreach ($this->alldata as $d) {
            $i = $d['user_id'];
            if (!isset($this->composerset[$i]))
                $this->composerset[$i] = 0;
            $this->composerset[$i] += $d['rating'];
        }
        $this->composerset = $this->create_ranking($this->composerset);
    }

    private function create_ranking($data)
    {
        $i = 0;
        $c = count($data);
        $r = [];
        foreach ($data as $key => $val) {
            if ($i < $c / 6) {
                $r[$key] = 9;
            } elseif ($i < 2) {
                $r[$key] = 3;
            } else {
                $r[$key] = 1;
            }
            $i++;
        }
        return $r;

    }

    public function calculate()
    {
        //tag = 50;
        $actionrating = 80;// sold , wishlist , visited
        $tagrating = 50;
        $composerrating = 20;
        $recommends = [];
        foreach ($this->songdata as $song) {
            $id = $song['id'];
            $uid = $song['user_id'];

            foreach ($this->tagset as $tag => $rating) {
                if ($song[$tag] == 1) {
                    if (!isset($recommends[$id])) {
                        $recommends[$id] = 0;
                    }
                    $recommends[$id] = $recommends[$id] + $tagrating * $rating;
                }
            }

            if (isset($this->composerset[$uid])) {
                if (!isset($recommends[$id])) {
                    $recommends[$id] = 0;
                }
                $recommends[$id] = $recommends[$id] + $composerrating * $this->composerset[$uid];
            }


            foreach ($this->alldata as $d) {
                if ($d['song'] == $id) {
                    $r = 1;
                    switch ($d['rating']) {
                        case 5:
                            $r = 9;
                            break;
                        case 3:
                            $r = 3;
                            break;
                        default :
                            $r = 1;
                            break;
                    }
                    if (!isset($recommends[$id])) {
                        $recommends[$id] = 0;
                    }
                    $recommends[$id] = $recommends[$id] + $actionrating * $r;
                }
            }
        }
        arsort($recommends);
        return $recommends;
    }
}
