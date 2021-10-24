var apikey = null //請聯絡作者以取得API金鑰

//提供預設value值
document.getElementById("input").value="女朋友";
document.getElementById("long").value="600";
document.getElementById("類型").value="愛 女";
document.getElementById("段落數").value="1";
$.ajax({ //查詢資料庫中相關語句
    type: "GET", 
    url: "https://www.teablack2008.com/api/v1/非正式用文章產生器/generate.php", 
    dataType: "json", 
    data: {
        "key": apikey, 
        "query_type":"list", 
        "type":"famous", 
        "user": "<?=$username?>"
    },
    success: function(req){
        var ajaxdata = JSON.parse(req);
        window.console.log(ajaxdata.data.full);
        const ac = new Autocomplete(document.getElementById('查詢資料庫中相關語句'), {
            matchContains: true, 
            maximumItems: 20, 
            treshold: 1, 
            data: ajaxdata.data.full, 
            highlightTyped: true,
            highlightClass: 'text-hard'
        });
    }
});
$("#send_content").click(function(){
    document.getElementById("send_content").value="產生中，請稍後...";
    document.getElementById("send_content").classList.add('disabled');
    $.ajax({
        type: 'GET', 
        url: "https://www.teablack2008.com/api/v1/非正式用文章產生器/generate.php", 
        async: true, 
        data: {
            "key": apikey, 
            "long": document.getElementById('long').value, 
            "input": document.getElementById('input').value, 
            "query_type": "generate", 
            "query": document.getElementById('類型').value, 
            "paragraph": document.getElementById('段落數').value, 
            "fattie": document.getElementById('fattie').checked, 
            "user": "<?=$username?>"
        }, 
        dataType: "json", 
        success: function(res){
            document.getElementById("send_content").classList.remove('disabled');
            document.getElementById("send_content").value="重新產生文章";
            document.getElementById("copy_article").classList.remove('disabled');
            $("#output").html(res.data.output_text);
            document.getElementById("output_value").value=res.data.output_text;
            $("#how_long").html("共"+res.data.output_long+" 字，"+res.data.paragraph+" 段");
            // document.getElementById("audio").src="https://translate.google.com/translate_tts?ie=UTF-8&tl=zh-tw&client=tw-ob&q="+res.data.output_text.substring(0, 200);
            
            // var postdata={"engine":"Google","data":{"text":res.data.output_text.substring(0, 200),"voice":"cmn-Hant-TW"}};
            // $.ajax({
            //     type: 'POST', 
            //     url: "https://api.soundoftext.com/sounds", 
            //     async: true, 
            //     dataType: "json", 
            //     contentType:"application/json", 
            //     data: JSON.stringify(postdata), 
            //     success: function(res){
            //         setTimeout(function(){
            //             var requrl="https://api.soundoftext.com/sounds/"+res.id
            //             $.ajax({
            //                 type: 'GET', 
            //                 url: requrl, 
            //                 async: true, 
            //                 success: function(res){
            //                     document.getElementById("audio").src=res.location;
            //                 }
            //             })
            //         },1000);
            //     }
            // })
        }
    });  
})
