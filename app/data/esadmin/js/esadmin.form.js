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
        var $selects = $('#panels .panel.active select[data-pids]');
        if($selects.length){
            Form.Select._init($selects);
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
        _init:function($selects){
            $selects.each(function(){
                var $container = $(this).parent('.select-js'),
                    name = $(this).data('name'),
                    pids = $(this).data('pids').toString().split('-');
                
                if( pids.length>1){
                    $container.data('name',name).data('ids',pids);
                    $(this).remove();
                    Form.Select._new(0,$container);

                }else{

                }
            })
        	
        },
        _change:function( $sel ){
            var name = $sel.data('name'),flag = name && name.indexOf('[]')==-1;
            if( flag && typeof($sel.attr('data-pids'))=='undefined' ){
                return false;
            }
            
            var $container = $sel.parent('.select-js');
            name = $sel.data('name');
            $sel.nextAll('select').remove();
            var id = $sel.val();
            if(id>0){
                $.get('/esadmin/category/catsbypid/'+$sel.val(),function(r){
                    if(!Number(r.err) && r.msg.toString().length){
                        var $sel = Form.Select._sel(name);
                        $.each(r.msg,function(ii,v){
                            $sel.append('<option value="'+ii+'"'+(ii==id?' selected':'')+'>'+v+'</option>');
                        });
                        $container.append($sel);
                    }
                },'json');
            }
        },
        /**
         * @param int i
         * @param JQuery $container
         * @param array ids
         * @param string name
         */
        _new:function(i,$container){// 新增一个select;使用递归避免显示顺序错误
           var ids = $container.data('ids');
           
           if(i >= ids.length){
               return false;
           }else{
               $.get('/esadmin/category/catsbypid/'+ids[i],function(r){
                    var sid = (i+1<ids.length)?ids[i+1]:-1;
                    if(!Number(r.err) && r.msg.toString().length){
                        var $sel = Form.Select._sel($container.data('name'));
                        $.each(r.msg,function(ii,v){
                            $sel.append('<option value="'+ii+'"'+(ii==sid?' selected':'')+'>'+v+'</option>');
                        });
                        $container.append($sel);
                    }
                    Form.Select._new(++i,$container)
               },'json');
           }
        },
        _sel:function(name){
            return $('<select name="'+name+'[]"><option value="0">请选择</option></select>');
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