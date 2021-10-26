<?php
require_once "/var/www/html/assets/function.php";
initialize("GET, OPTIONS", "application/json", "*");
keyCheck(!查詢資料庫("web", "SELECT * FROM `web`.`APIkey` WHERE `key`='$_GET[key]' AND `service`='ALL'") && !查詢資料庫("web", "SELECT * FROM `web`.`APIkey` WHERE `key`='$_GET[key]' AND `service`='非正式用文章產生器'"));
function get_the_data(){
    global $_GET, $sentences_data, $input_keyword;
    $input_keyword=mb_split("\s",$_GET['query']); //處理關鍵字資料
    $sentences_data['json']=查詢資料庫("article_generator","SELECT * FROM `article_generator`.`content`","array");
    $sentences_data['前置']=查詢資料庫("article_generator","SELECT * FROM `article_generator`.`content` WHERE `type`='before'","array");
    $sentences_data['前連']=查詢資料庫("article_generator","SELECT * FROM `article_generator`.`content` WHERE `type`='front'","array");
    $sentences_data['後置']=查詢資料庫("article_generator","SELECT * FROM `article_generator`.`content` WHERE `type`='after'","array");
    $sentences_data['幹話']=查詢資料庫("article_generator","SELECT * FROM `article_generator`.`content` WHERE `type`='bullshit'","array");
    $sentences_data['名人']=array();
    $sentences_data['肥宅']=json_decode(file_get_contents("/var/www/html/api/v1/非正式用文章產生器/肥宅.json"));
};
function reset_the_data(){
    global $_GET, $randomized_sentences, $sentences_data, $input_keyword;
    if ($_GET['query']=="" || !$_GET['query']){
        $sentences_data['名人']=查詢資料庫("article_generator","SELECT * FROM `article_generator`.`content` WHERE `type`='famous'","array");
    }else{
        for ($i=0, $唬爛名人查詢數量=0; count($input_keyword)>$i; $i++){ //產生rows數量
            $唬爛名人查詢=執行資料庫("article_generator","SELECT * FROM `article_generator`.`content` WHERE `type`='famous' AND `content` REGEXP '$input_keyword[$i]'","array");
            $唬爛名人查詢數量=$唬爛名人查詢數量+mysqli_num_rows($唬爛名人查詢);
        };
        if ($唬爛名人查詢數量==0){
            $sentences_data['名人']=查詢資料庫("article_generator","SELECT * FROM `article_generator`.`content` WHERE `type`='famous'","array");
        }else{
            for ($i=0; count($input_keyword)>$i; $i++){
                $唬爛名人查詢=查詢資料庫("article_generator","SELECT * FROM `article_generator`.`content` WHERE `type`='famous' AND `content` REGEXP '$input_keyword[$i]'","array");
                if ($唬爛名人查詢){
                    for ($j=0; count($唬爛名人查詢)>$j; $j++){
                        $sentences_data['名人'][count($sentences_data['名人'])+1]=$唬爛名人查詢[$j];
                    };
                }else{
                    $sentences_data['名人']=查詢資料庫("article_generator","SELECT * FROM `article_generator`.`content` WHERE `type`='famous'","array");
                };
            };
        };
    };
    $randomized_sentences['前置']=$sentences_data['前置'][rand(0,count($sentences_data['前置']))]['content'];
    $randomized_sentences['前連']=$sentences_data['前連'][rand(0,count($sentences_data['前連']))]['content'];
    $randomized_sentences['後置']=$sentences_data['後置'][rand(0,count($sentences_data['後置']))]['content'];
    $randomized_sentences['幹話']=$sentences_data['幹話'][rand(0,count($sentences_data['幹話']))]['content'];
    $randomized_sentences['名人']=$sentences_data['名人'][rand(0,count($sentences_data['名人']))]['content'];
    $randomized_sentences['肥宅']['front']=$sentences_data['肥宅']->front[rand(0, count($sentences_data['肥宅']->front)-1)];
    $randomized_sentences['肥宅']['back']=$sentences_data['肥宅']->back[rand(0, count($sentences_data['肥宅']->back)-1)];
};
function generate(){
    global $randomized_sentences ,$generate, $generate_output, $fattie_long;
    if (!$generate_output){
        $generate_output['文字']="       ";
    };
    $generate['前置']=str_replace("a",$randomized_sentences['前置'],$randomized_sentences['名人']);
    $generate['後置']=str_replace("b",$randomized_sentences['幹話'],$generate['前置']);
    $generate['文字']=str_replace("x",$_GET['input'],$generate['後置']);
    if ($_GET['fattie']=="true"){
        $generate_output['文字']=$generate_output['文字'].$randomized_sentences['肥宅']['front']."，".$generate['文字'].$randomized_sentences['肥宅']['back'];
        $fattie_long=$fattie_long+mb_strlen($randomized_sentences['肥宅']['front'])+mb_strlen($randomized_sentences['肥宅']['back'])+1;
    }else{
        $generate_output['文字']=$generate_output['文字'].$randomized_sentences['前連'].$generate['文字'];
    };
    $generate_output['字數']=mb_strlen($generate_output['文字'], "utf-8");
};

$ip=取得IP位址();
if ($_GET['query_type']=="list"){
    $result=執行資料庫("article_generator", "SELECT `content` FROM `article_generator`.`content` WHERE `type`='$_GET[type]'");
    while ($row=mysqli_fetch_all($result)){
        for ($i=0; $i<mysqli_num_rows($result); $i++){
            $return['request']=[
                "code"=>"200", 
                "info"=>"Successed.",
                "key"=>$_GET['key'],
                "ip"=>取得IP位址()
            ];
            $return['data']['full'][$i]['label']=$row[$i][0];
            $return['query']=[
                "query_type"=>$_GET['query_type'], 
                "query_num"=>mysqli_num_rows($result), 
                "type"=>$_GET['type']
            ];
            $return['data']['full'][$i]['value']=$row[$i][0];
        };
    };
    執行資料庫("article_generator", "INSERT INTO `article_generator`.`list_record` (`user`, `type`, `ip`, `APIkey`) VALUES ('$_GET[user]', '$_GET[type]', '$ip', '$_GET[key]');");
}elseif ($_GET['query_type']=="query"){
    $result=執行資料庫("article_generator", "SELECT `content` FROM `article_generator`.`content` WHERE `content` REGEXP '$_GET[query]' AND `type`='$_GET[type]'");
    while ($row=mysqli_fetch_all($result)){
        for ($i=0; $i<mysqli_num_rows($result); $i++){
            $return['request']=[
                "code"=>"200", 
                "info"=>"Successed.",
                "key"=>$_GET['key'],
                "ip"=>取得IP位址()
            ];
            $return['query']=[
                "query_type"=>$_GET['query_type'], 
                "query_num"=>mysqli_num_rows($result), 
                "query_text"=>$_GET['query'], 
                "type"=>$_GET['type']
            ];
        };
    };
    執行資料庫("article_generator", "INSERT INTO `article_generator`.`query_record` (`user`, `query_text`, `query_text`, `type`, `ip`, `APIkey`) VALUES ('$_GET[user]', '$_GET[query]', '$_GET[type]', '$ip', '$_GET[key]');");
}elseif ($_GET['query_type']=="generate"){
    //字數防護機制
    if ($_GET['long']>10000){
        $_GET['long']=10000;
    }elseif ($_GET['long']=="" || !$_GET['long']){
        $_GET['long']=600;
    };
    //分段系統
    if ($_GET['paragraph']=="" || !$_GET['paragraph'] && $_GET['paragraph']!=0){
        $每段字數=$_GET['long'];
        $固每段字數=$每段字數;
    }else{
        $每段字數=$_GET['long']/$_GET['paragraph'];
        $固每段字數=$每段字數;
    };
    get_the_data();
    reset_the_data();
    generate();
    while ($generate_output['字數']-22-15*$段數<$_GET['long']){
        reset_the_data();
        generate();
        if (mb_strlen($generate_output['文字'], "utf-8")-22-15*$段數-$fattie_long>$每段字數 && $generate_output['字數']<$_GET['long']){
            $generate_output['文字']=$generate_output['文字']."<br><br>       ";
            $每段字數+=$固每段字數;
            $段數=$段數+1;
        };
    };
    http_response_code(200);
    $return['request']=[
        "code"=>"200", 
        "info"=>"success",
        "key"=>$_GET['key'],
        "ip"=>取得IP位址()
    ];
    $return['data']=[
        "input_long"=>$_GET['long'],
        "input_text"=>$_GET['input'], 
        "output_text"=>$generate_output['文字'], 
        "output_long"=>$generate_output['字數']-22-15*$段數, 
        "paragraph"=>$段數+1
    ];
    執行資料庫("article_generator", "INSERT INTO `article_generator`.`generate_record` (`user`, `content`, `content_long`, `input_text`, `ip`, `APIkey`, `fattie`, `paragraph`) VALUES ('$_GET[user]', '$generate_output[文字]', '$generate_output[字數]', '$_GET[input]', '$ip', '$_GET[key]', '$_GET[fattie]', '$_GET[paragraph]');");
};
echo json_encode($return);
?>
