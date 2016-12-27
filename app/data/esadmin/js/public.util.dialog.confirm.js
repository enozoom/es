/**
 * 每个面板都可能会使用这个弹出框，且单独制定确认和取消事件，所以独立到整个body里。
 * @example
 * 
 */
var ConfirmDialog ={
  /**
   * 初始化点击事件,init()方法只能执行一次。
   * @param   function fn_ok    点击确认按钮执行的函数
   * @param   function fn_close 点击关闭按钮执行的函数
   * @returns ConfirmDialog
   */
  _init:function(fn_ok,fn_close){
    var $active = EsAdmin.Dom.Panel._active(),$mask = $active.find('.venue-mode-confirm-mask');
    if( !$mask.length ){
      var style = 'position:fixed;left:180px;top:0;right:0;bottom:0;background-color:rgba(0,0,0,.3);display:none;';
      $mask = $('<div style='+style+'></div>').addClass('venue-mode-confirm-mask');
          
      var style = 'position:fixed;width:500px;height:200px;background-color:#fff;top:50%;left:50%;margin-left:-160px;margin-top:-85px;border-radius:10px;box-sizing:border-box;padding:15px;box-shadow:0 0 10px rgba(0,0,0,.3)',
      $container = $('<div style="'+style+'"></div>').addClass('venue-mode-confirm'),
      $p = $('<p style="padding:10px;height:110px;line-height:22px;"></p>'),
      $btn = ConfirmDialog._buttons(fn_ok,fn_close);   
          $container.append($p,$btn);
          $mask.append($container).appendTo($active);
    }
    return this;
  },
  _buttons:function(fn_ok,fn_cancel){
    var $div = $('<div class="buttons t-center"></div>'),
        $ok = $('<button class="btn-green btn-small" style="margin-right:5px">确定</button>').click(function(){
            fn_ok && fn_ok();
            ConfirmDialog._close();
        }),
        $cancel = $('<button class="btn-red btn-small">取消</button>').click(function(){
            fn_cancel && fn_cancel();
            ConfirmDialog._close();
        }); 
    $div.append($ok,$cancel);
    return $div;
  },
  /**
   * 弹出框的主体部分
   * @param   string p          弹出框显示的HTML内容
   * @returns JQuery
   */
  _body:function(p){
    var $active = EsAdmin.Dom.Panel._active(),$mask = $active.find('.venue-mode-confirm-mask');
    p = p || '';
    if( $mask.length ){
      $mask.find('p').html(p);
    }
    return $mask;
  },
  _close:function(){
    ConfirmDialog._body().fadeOut();
  },
  _show:function(p,fn_ok,fn_close){
   ConfirmDialog._body(p).fadeIn();
  }
}