<?php 
require_once "/var/www/html/assets/function.php";
樣式();
if (is_user_logged_in()){
    $user=wp_get_current_user()->data;
    $username=wp_get_current_user()->data->user_login;
    $userquery=查詢資料庫("article_generator", "SELECT * FROM `generate_record` WHERE `user`='$username'", "array");
}else{
    $user=取得IP位址();
    $username=取得IP位址();
    $userquery=查詢資料庫("article_generator", "SELECT * FROM `generate_record` WHERE `ip`='$user'", "array");
};

?>
<p>「<?=$username?>」的歷史紀錄</p>
        <?php 
        if ($userquery){
            ?>
            <table class="table table-hover">
                <thead>
                    <td>編號</td><td>產生時間</td><td>文章主題</td><td>內容</td><td>文章字數</td><td>段落數</td><td>肥宅模式</td><td>複製</td>
                </thead>
                <tbody>
            <?php 
            for ($i=0;$i<count($userquery);$i++){
                ?>
                <tr>
                    <td class='col-auto'><?=$i?></td>
                    <td class='col-auto'><?=$userquery[$i]['time']?></td>
                    <td class='col-auto'><?=$userquery[$i]['input_text']?></td>
                    <td class='col-7' id='content_long-<?=$i?>'><?=$userquery[$i]['content']?></td>
                    <td class='col-auto'><?=$userquery[$i]['content_long']?></td>
                    <td class='col-auto'><?=$userquery[$i]['paragraph']?></td>
                    <td class='col-auto'><?=$userquery[$i]['fattie']?></td>
                    <td class='col-auto'>
                        <button type='button' class='btn btn-primary' onclick="
                                textArea = document.createElement('textarea');
                                textArea.value=document.getElementById('content_long-<?=$i?>').innerHTML; 
                                document.body.appendChild(textArea);
                                textArea.select(); 
                                document.execCommand('copy'); 
                                document.body.removeChild(textArea); 
                                alert('成功複製')
                            ">複製</button>
                    </td>
                </tr>

                <?php 
            };?>
                </tbody>
            </table>
            <?php 
        }else{
            ?>
            <h3>目前無查詢到關於「<?=$username?>」所產生的文章歷史紀錄。</h3>
            <?php 
        };
?>
