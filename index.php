<?php
require_once "/var/www/html/assets/function.php";
樣式();
if (is_user_logged_in()){
    $user=wp_get_current_user();
    $username=$user->data->user_login;
    $login_tips="<p>您目前正在以「<a href='https://www.teablack2008.com/author/".$username."'>".$username."</a>」身分保留文章產生紀錄。</p>";
}else{
    $user=null;
    $username=取得IP位址();
    $login_tips="<p>您尚未登入，因此無法保留您的文章產生紀錄，<a href='https://www.teablack2008.com/wp-login.php'>立即登入</a>。</p>";
};
?>
<body>
<link rel="stylesheet" href="https://www.teablack2008.com/include/非正式用文章產生器/index.css">
<div class="modal fade" id="成功複製" tabindex="-2" aria-labelledby="success" data-bs-backdrop="true" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body" style="font-size: 100%;">
            <div class="swal2-icon swal2-success swal2-animate-success-icon" style="display: flex;">
            <div class="swal2-success-circular-line-left" style="background-color: rgb(255, 255, 255);"></div>
                <span class="swal2-success-line-tip"></span>
                <span class="swal2-success-line-long"></span>
                <div class="swal2-success-ring"></div> 
                <div class="swal2-success-fix" style="background-color: rgb(255, 255, 255);"></div>
            <div class="swal2-success-circular-line-right" style="background-color: rgb(255, 255, 255);"></div>
            </div><br>
            <center class="h3">文章複製成功</center>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="文章產生紀錄" tabindex="-1" aria-labelledby="文章產生紀錄Label" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"  id="文章產生紀錄Label">「<?=$username?>」的文章產生紀錄</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
              <?php include "/var/www/html/include/非正式用文章產生器/list.php"?>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
      </div>
    </div>
  </div>
</div>
<div class="container">
    <hr>
    <?=$login_tips?>
    <span class="text-danger">*為必填項目</span>
    <div class="form-floating mb-3">
        <input class="form-control" type="text" name="input" id="input" placeholder="輸入" required>
        <label for="input">* 請輸入文章主題</label>
    </div>
    <div class="form-floating mb-3">
        <input class="form-control" type="number" name="long" id="long" placeholder="字數" required>
        <label for="long">* 請輸入字數要求 (上限10,000字)</label>
    </div>
    <div id="進階選項">
        <button class="btn btn-toggle align-items-center rounded" data-bs-toggle="collapse" data-bs-target="#進階產生器選項">
        進階選項
        </button><br><br>
        <div class="collapse" id="進階產生器選項">
            <div class="fw-normal row">
                <div class="col-lg-6">
                    <label for="查詢資料庫中相關語句">資料庫查詢工具</label>
                    <input type="text" id="查詢資料庫中相關語句" class="form-control" width="50%" placeholder="查詢資料庫中相關語句，以產生出更加符合需求的文章。"/>
                    <label for="類型">文章類型 ( 以空格來分隔關鍵字，建議縮短字詞以擴大結果涵蓋率 )</label>
                    <input class="form-control" type="text" name="類型" id="類型" placeholder="輸入欲產生之文章類型以產生出更加符合需求的文章 (預設：愛、女)">
                </div>
                <div class="col-lg-6">
                    <label for="分段">文章段落數</label>
                    <input class="form-control" type="number" name="段落數" id="段落數" placeholder="文章產生的段落數。(預設不分段)">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="fattie" name="fattie">
                        <label class="form-check-label" for="fattie">肥宅模式</label>
                    </div>
                </div>
            </div>
        </div>
        <br>
    </div>
    <div class="btn-group-vertical">
        <div class="btn-group">
            <input type="reset" class="btn btn-warning btn-lg" onclick='document.getElementById("input").value="";document.getElementById("long").value="";document.getElementById("類型").value="";document.getElementById("段落數").value="";'></input>
            <input type="submit" value="產生文章" class="btn btn-success btn-lg center" id="send_content">
        </div>
        <button type="button" class="btn btn-primary btn-lg center" onclick="var 文章產生紀錄 = new bootstrap.Modal(document.getElementById('文章產生紀錄'));文章產生紀錄.show();">歷史紀錄</button>
    </div>
    <hr>
    產生的文章內容 <span id="how_long"></span>：
    <button class="btn btn-primary disabled" id="copy_article" onclick="document.getElementById('output_value').select();document.execCommand('copy');var 成功複製 = new bootstrap.Modal(document.getElementById('成功複製'));成功複製.show();setTimeout(function(){成功複製.hide()},1500);">複製文章</button>
    <br>
    <input id="output_value" type="text" value="1" readonly  onclick="document.getElementById('output_value').select();document.execCommand('copy');var 成功複製 = new bootstrap.Modal(document.getElementById('成功複製'));成功複製.show();setTimeout(function(){成功複製.hide()},1500);" style="position: absolute; left: -100000px;">
    <div class="container" id="output"></div>
        <p class="text-muted">
            本頁之所有內容(不包含生成結果之語句)，若須做其他用途、改編、散播者，請先取得同意。<br>
        </p>
    </div>
<script src="https://www.teablack2008.com/include/非正式用文章產生器/autocomplete.js"></script>
<script><?php include "index.js";?></script>
