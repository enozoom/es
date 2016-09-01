;
/**
 * 公共验证表单，需要jquery支持
 * 默认会将验证不通过的第一个表单元素项 addClass('error');
 * Joe
 * 2016年7月11日14:20:47
 * 
 * 
 * Demo:
 * ---------------
 * <!-- html -->
 * <form>
 *   <input data-validate='{"required":1,"length":"1-2","type":1}'>
 * </form>
 * <!--
 * requried:是否必须填写
 * length:长度范围，1-2表示≥1&&≤2，如果只填写数字，则表示长度=该数字
 * type:0-数字,1-中文,2-字母,3-字母数字,4-日期,5-手机
 * -->
 * 
 * // Js
 * //方式一：全自动，提交验证的按钮没有其他事件
 * VALIDATE._init($('.btn-validate'))
 *         ._done(function($b){
 *             //$b=发起这次验证的按钮
 *         })
 *         ._fail(function(e){ 
 *             //e=[验证未通过的表单项]; 
 *         });
 *         
 * //方式二：半自动，提交验证的按钮同时有其他click事件
 * $('button').click(function(){
 *   VALIDATE._done(function(){  })._inputs($(this))
 * })
 * ----------------
 */
var VALIDATE = 
    {
        // 自定义执行回调函数
        callback:{},
        // 验证不通过的表单项目
        illegal:[],
        // 表单的提交验证按钮
        button:'',
        /**
         * 入口
         * @param string|jQuery button 按钮jQuery对象或者按钮ID
         */ 
        _init:function(button){
            this._click(button);
            return this;
        },
        // 判断对象是否是jQuery对象，如果不是则认为是ID，并生成jQuery对象返回。
        _2jQuery:function(o){
            return o instanceof jQuery ? o : $('#'+o);;
        },
        // 获取验证表单的所有需要验证的元素
        _click:function(btn){
            var $btn = this._2jQuery(btn);
            $btn.click(function(){
                VALIDATE._inputs($(this));
            });
        },
        // 点击事件
        _inputs:function($btn){
            $form = $btn.parents('form');
            if($form.length){
              var flag = 0,$ipts = $form.find('[data-validate]');
              $form.find('.error').removeClass('error');
              
              if($ipts.length){
                  $ipts.each(function(){
                      flag = 0;
                      var val = $(this).val(),args = $(this).data('validate');
                      if(args.required){
                          $.each( args,function(k,v){
                              if(!VALIDATE._validate(val,k,v)){
                                  flag = 1;
                                  return false;
                              }
                          } );
                      }
                      if(flag){
                          VALIDATE.illegal = [$(this)];
                          return false;
                      }
                  });
              }
              
              if(!flag){
                  VALIDATE._callback_done($btn);
              }else{
                  VALIDATE._callback_fail();
              }
              
            }
            return this;
        },
        // 验证开始
        _validate:function(v,k,args){
            switch(k){
                case 'required':
                    return v.length>0;
                break;case 'type':
                    var preg = /^\d+$/;
                    switch(args){
                      //case 0:// 数字默认
                        case 1:// 中文
                            preg = /^[\u4e00-\u9fa5]+$/;
                        break;case 2:// 字母
                            preg = /^[a-zA-Z0-9]+$/;
                        break;case 3:// 字母数字
                            preg = /^\w+$/;
                        break;case 4:// 日期
                            preg = /^20\d{2}-\d{1,2}-\d{1,2}$/;
                        break;case 5:// 手机
                            preg = /^1\d{10}$/;
                        break;
                    }
                    return preg.test(v);
                break;case 'length':
                    if(!Number(args)){
                        var scope = args.split('-');
                        return v.length >= scope[0] && v.length <= scope[1];
                    }else{
                        return v.length == args;
                    }
                break;
            }
            return 0;
        },
        // 验证成功执行方法
        _callback_done:function($btn){
           if(this.callback.done){
               this.callback.done($btn);
           }else{
               console.log('success'); 
           }
           
        },
        // 验证失败执行方法
        _callback_fail:function(){
            if(this.callback.fail){
                this.callback.fail(VALIDATE.illegal);
            }else{
                $.each(VALIDATE.illegal,function(i,$v){
                    $v.addClass('error');
                })
            }
        },
        // 更改成功回调函数
        _done:function(func){
            if(func){
              this.callback.done = func;
            }
            return this;
        },
        // 更改失败回调函数
        _fail:function(func){
            if(func){
                this.callback.fail = func;
            }
            return this;
        }
        
    }