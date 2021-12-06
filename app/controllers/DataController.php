<?php

class DataController
{

    function getSongData()
    {
        $data = ORM::for_table('')->raw_query('select * from tbl_pl_songtext')->find_array();
        $arr = [];
        foreach ($data as $d) {
            $index = $d['id'];
            unset($d['id']);
            unset($d['titel']);
            unset($d['impressions']);
            unset($d['title_char']);
            unset($d['user_id']);
            unset($d['status_id']);
            foreach ($d as $key => $value) {
                if (!$value) {
                    $d[$key] = 0.1;
                }
            }
            $arr[$index] = $d;
        }
        return $arr;
    }

    function getUserData()
    {
        $data = ORM::for_table('user_item_rating')
            ->find_array();
        return $data;
    }

    function get_Song_with_Rating()
    {
        $data = ORM::for_table('user_item_rating')->
        table_alias('ui')->
        left_outer_join('tbl_pl_songtext', 'ui.song = st.id', 'st')
            ->find_array();
        return $data;
    }

    function getSongText()
    {
        $data = ORM::for_table('')->raw_query('select * from tbl_pl_songtext')->find_array();
        return $data;
    }

}
