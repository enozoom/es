/**
 * 工具包
 * 2016年7月13日15:20:00
 */
;var Util={
    Number:{// 数字相关
        _format:function(n){// 格式化
            n = (n || 0).toString(), result = '';
            while (n.length > 3) {
              result = ',' + n.slice(-3)+result;
              n = n.slice(0, n.length - 3);
            }
            return n+result;
        }
    },
    Tab:{// Tab切换
        _init:function($index,event){
            event = event||'click';
            $index.bind(event,function(){
                var id = $(this).data('toggle');
                Util.Tab._active_cls($(this))._active_cls(id);
            })
        },
        _active_cls:function(id,cls){// 为元素添加一个class并移除同类的该class
            var $o;
            cls = cls||'active';
            $o = id instanceof jQuery ? id : $('#'+id);
            $o.addClass(cls).siblings().removeClass(cls);
            return this;
        }
    },
    Alert:{
        _init:function(f,m){// f必须是0,1 0:正确提示/1:错误提示
            var $p = '<p class="'+['ok','err'][f]+'"><i class="'+['ion-checkmark-circled','ion-close-circled'][f]+'"></i>'+m+'</p>'
            $alert = this._append().append($p);
            setTimeout(function(){
                $alert.fadeOut(500);
            },4000)
        },
        _err:function(m){
            this._init(1,m);
        },
        _ok:function(m){
            this._init(0,m);
        },
        _append:function(){
            var $alert = $('#es-alert');
            if($alert.length) $alert.find('p').remove();
            return $alert.length?$alert:$('<div id="es4-alert"/>').appendTo('body');
        }
    },
    /**
     * 使用方法
     * HTML: data-ids默认已选择
     * <div class="es4-area" id="myadr" data-ids="1,2,3"></div>
     * JAVASCRIPT:
     * Util.Area._init( '.es4-area' );
     * 
     * 取值
     * name=myadr[]
     */
    Area:{// 地区级联
        container:null,// select存放的容器,jQuery对象
        _init:function(container){// 入口
            $(container).each(function(){// 地区级联一个页面可能存在多个
                var $this = $(this),id = $this.attr('id');
                if(!id){
                    console.log(container+'必须存在id属性，该属性会转成select的name[]');
                    return false;
                }
                if( !$this.find('select').length ){
                    Util.Area._load(4,$this);
                }else{// 为保持一致性，清除可能存在的select
                    $this.html('');
                    Util.Area._init( container );
                }
                $this.on('change','select',function(){
                    $(this).nextAll('select').remove();
                    Util.Area._load( $(this).val(), $this );
                })
            })
        },
        _load:function(pid,$container){
            if(!Number(pid)) return false;
            $.get('/api/category/area/'+pid,function(r){
                if(!Number(r.err) && r.msg.toString().length){
                    Util.Area._select(r.msg,$container);
                }
            },'json');
        },
        _select:function(data,$container){
            var ids = $container.data('ids'),i = $container.find('select').length,id=0;
            if(ids){
                ids = ids.split(',');
                id = ids.length>=i?ids[i]:0;
            }
            var $sel = $('<select name="'+$container.attr('id')+'[]"><option value="0">请选择</option></select>');
            $.each(data,function(i,v){
                $sel.append('<option value="'+i+'"'+(id?(id==i?' selected':''):'')+'>'+v+'</option>');
            });
            $container.append($sel);
            if(id) $sel.trigger('change');
        }
    },
    /**
     * 一个会自动消失的提示框
     */
    Dialog:{
         _err:function(m){
            this._init(m);
         },
         _ok:function(m){
            this._init(m,1);
         },
         _close:function(){
            setTimeout(function(){
              $('#es-dialog').fadeOut();
            },5000);
         },
         _init:function(msg,ok){
           ok = ok || 0;
           var i_cls = ['ion-close-circled','ion-checkmark-circled'][ok],p_bg = ['rgba(220,37,1,.6)','rgba(5,202,83,.6)'][ok],
               $div=$('#es-dialog');
           if( $div.length ){
              $('#es-dialog i').attr('class',i_cls);
              $('#es-dialog p').css('background-color',p_bg);
              $('#es-dialog span').html(msg);
              
           }else{
               $div = $('<div id="es-dialog" style="position:fixed;bottom:100px;width:600px;z-index:9999;left:50%;margin-left:-300px;display:none;"/>');
               var $p = $('<p style="background-color:'+p_bg+';padding:15px 20px;border-radius:5px;"/>'),
                   $i = $('<i class="'+i_cls+'" style="margin-right:5px;"/>');
                   $span = $('<span/>').html(msg);
           
               $div.append( $p.append($i,$span) ).appendTo('body');
           }
           $div.fadeIn();
           this._close();
         }
    }
};