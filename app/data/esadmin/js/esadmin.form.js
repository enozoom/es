/**
 * 被加载的页面有表单时自动载入
 */
;var Form = {
    _init:function(){
        this._select()
            ._button()
            ._textarea()
            ._fileUpload();
    },
    _fileUpload:function(){
        var $uploader = $('#panels .panel.active .uploader');
        if($uploader.length){
            $.getScript('/theme/dmuploader/src/dmuploader.min.js',function(){
                $uploader.each(function(){
                    Form.Upload._init(
                                        $(this),
                                        $(this).data('action'),
                                        $(this).find('input[type="file"]').attr('name')
                                     );
                })
            });
            $uploader.each(function(){
                var v = $(this).find('input[type="hidden"]').val();
                if(v.length){
                    Form.Upload._preview($(this),v);
                }
            })
        }
        return this;
    },
    _textarea:function(){// 一个表单只能存在textarea
        $textarea = $('#panels .panel.active textarea:not(.normal)')
        if($textarea.length){
            $textarea.each(function(i){
                var id = $(this).attr('id');
                if(id){
                    $('#'+id).css({width:920});
                    UE.delEditor(id);
                    UE.getEditor(id);
                };
            });
        }
        return this;
    },
    _button:function(){
        $('#panels .panel.active .es4-form button:not(.fileUpload)').click( 
          function(){Form.Button._click($(this))}
        );
        return this;
    },
    _select:function(){
        $('.select-js').on('change','select',function(){
            Form.Select._change( $(this) );
        });
        var $cats = $('#panels .panel.active select[data-pids]');
        if($cats.length){
            $cats.each(function(){
                var pids = $(this).data('pids').toString(),
                    $div = $(this).parent('.select-js'),
                    name = $(this).data('name');
                Form.Select._init($div,pids,name);
            })
        }
        return this;
    },
    Button:{
        _click:function($btn){
            var $form = $btn.parents('form'),
            action = $form.attr('action');
            $.post(action,$form.serialize(),function(r){
               if(Number(r.err)){
                   EsAdmin.Tool._resultErr();
               }else{
                   var url = (action+r.id+'/').replace(r.id+'/'+r.id,r.id);
                   $form.attr('action',url);
                   EsAdmin.Tool._resultOk();
               }
            },'json');
        }
    },
    Select:{// 级联用的select,仅限级联
        container:'',// 放置selects的容器，必须赋值
        name:'',// 所有select的name属性
        ids:[],// 需要变成select的id,由id及其同辈组成option
        _init:function($container,ids,name){
            this.container = $container;
            this.name = name;
            var $sel = $container.find('select');
            if(ids.length>0 && (this.ids=ids.split('-')).length>=1){
                $sel.remove();
                this._new(0);
            }else{
                if( !$sel.find('option[value="0"]').length ){
                    $sel.prepend('<option value="0" selected>请选择</option>');
                }
                $sel.attr('name',name+'[]');
                Form.Select._auto($sel);
            }
        },
        _change:function( $sel ){
            var sname = $sel.attr('name').replace('[]','');
            if( typeof($sel.attr('data-pids'))=='undefined' ){
                return false;
            }
            
            Form.Select.container = Form.Select.container || $sel.parent('.select-js');
            Form.Select.name = Form.Select.name || $sel.data('name');
            $sel.nextAll('select').remove();
            var id = $sel.val();
            if(id>0){
                $.get('/esadmin/category/catsbypid/'+$sel.val(),function(r){
                    if(!Number(r.err) && r.msg.toString().length){
                        var $sel = Form.Select._sel();
                        $.each(r.msg,function(ii,v){
                            $sel.append('<option value="'+ii+'"'+(ii==id?' selected':'')+'>'+v+'</option>');
                        });
                        Form.Select.container.append($sel);
                    }
                },'json');
            }
        },
        _new:function(i){// 新增一个select;使用递归避免显示顺序错误
           if(i >= this.ids.length){
               return false;
           }else{
               var id = this.ids[i]; sid = (i+1<this.ids.length)?this.ids[i+1]:-1;
               $.get('/esadmin/category/catsbypid/'+id,function(r){
                    if(!Number(r.err) && r.msg.toString().length){
                        var $sel = Form.Select._sel();
                        $.each(r.msg,function(ii,v){
                            $sel.append('<option value="'+ii+'"'+(ii==sid?' selected':'')+'>'+v+'</option>');
                        });
                        Form.Select.container.append($sel);
                    }
                    Form.Select._new(++i)
               },'json');
           }
        },
        _sel:function(){
            return $('<select name="'+Form.Select.name+'[]"><option value="0">请选择</option></select>');
        },
        _auto:function($sel){// 自动触发第一个菜单项,避免分类的自动死循环
            var isAuto = $sel.parents('form').data('selectauto');
            if( isAuto === undefined || isAuto === 1 ){
                $sel.trigger('change');
            }
        }
    },
    Upload:{
        _init:function($updBtn,upUrl,filename){
            $updBtn.dmUploader({
                url: upUrl,
                fileName:filename,
                dataType: 'json',
                allowedTypes: '*',
                extFilter: 'jpg;png;gif;mp4',
                onUploadSuccess: function(id, data){
                    var url = data[filename][0].url;
                    $updBtn.find('input[name="'+filename+'-tmp"]').val( url );
                    Form.Upload._preview($updBtn,url);
                },
                onUploadError:function(id, message){
                    console.log('上传失败');
                }
            });
        },
        _preview:function($updBtn,url){
            var flag = url.indexOf('.mp4')>0;
            var htm = flag?'<video src="'+url+'" height="65" autoplay controls="controls"></video>':'<img src="'+url+'" alt="">';
            $updBtn.next('.preview').html(htm);
        }
    }
}