(function(){
// 让.step垂直居中
  $('.step').each(function(i){
    $(this).css({'z-index':$('.step').size()-i,'margin-top':($(this).height()/2+50)*-1})
  }).eq(0).removeClass('d_h');
// 下一步
  $('.step button.next').click(function(){
    var data = $(this).parents('form').serialize();
    var $step = $(this).parents('.step');
    $.post('/common/install/ajax/'+($step.index()+1),data,function(r){
      if(r.err/1){
        $('#dialog p').html(r.msg).parents('#mask').show();
      }else{
        $step.addClass('d_h').next().removeClass('d_h');
      }
    },'json');
  });
  $('.step button.prev').click(function(){
    var $step = $(this).parents('.step');
    $step.addClass('d_h').prev('.step').removeClass('d_h');
  });
// 关闭遮罩
  $('#dialog small').click(function(){
    $('#mask').hide();
  })
})();