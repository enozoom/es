$(function(){
    var bgs = ['1ABC9C','2ECC71','3498DB','F1C40F','E67E22','E74C3C','9B59B6','34495E'];
    $('#welcome-total-nums .f-left').each(function(i,$div){
       $(this).css({'background-color':'#'+bgs[i]});
    })
})