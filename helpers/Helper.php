<?php

namespace app\helpers;

class Helper
{

public static function removeUrlGetItems($url,$exceptItems='')
{
    if(is_string($exceptItems)) $exceptItems=[$exceptItems];

    $result_query=[];

    $url=parse_url($url);
    $url_queries=$url['query'];
   
    if($url_queries!=''){
       $url_queries=explode('&',$url_queries);
       $query_data=[];
   
       foreach($url_queries as $query){
           $query=explode('=',$query);
           $query_data[]=[
               'query' => $query[0],
               'value' => $query[1],
           ];
       }
   
       foreach($exceptItems as $except){
           foreach($query_data as $data){
               if($except==$data['query']) $result_query[]=$data['query'].'='.$data['value'];
           }
       }
    }
   
    if(count($result_query)>0) $result_query=implode('&',$result_query);
    if(count($result_query)==0) $result_query='';

    $result_query=!empty($result_query) ? '?'.$result_query : '';

    $url['query']=$result_query;
    $result_url=$url['scheme'].'://'.$url['host'].$url['path'].$url['query'];

    return $result_url;
    
}

}

?>