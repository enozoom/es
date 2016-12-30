/**
 * 
 * 所有esadmin页面上的<a data-href="#"></a>均为注册click加载panel页面的事件。
 * 如果不需要注册事件，则设定class="no-bind-a"即<a data-href="#" class="no-bind-a"></a>
 * 
 */
;var EsAdmin = {
    Tool:{// 基本工具
        _addCls:function($t,cls,siblings){
            cls = cls||'active';
            $t.addClass(cls);
            var sib = siblings?$t.siblings(siblings):$t.siblings();
            sib.removeClass(cls);
        },
        _ajaxStart:function(){
            $(document).ajaxStart(function() {
                EsAdmin.Tool._addCls($('#loadding'),'active','.fix');
            });
            return this;
        },
        _ajaxComplete:function(){
            $(document).ajaxComplete(function() {
                $('#loadding').removeClass('active');
            });
            return this;
        },
        _ajaxError:function(){
            $(document).ajaxError(function() {
                $('#loadding').removeClass('active');
            });
            return this;
        },
        _height:function(tag){
            tag = tag||window;
            return $(tag).height();
        },
        _resultOk:function(m){
            this._result('ok','checkmark',m || '成功！')
        },
        _resultErr:function(m){
            this._result('err','close',m || '失败！')
        },
        _result:function(cls,ion,m){
            $m = $('<p class="result-'+cls+'"><i class="ion-'+ion+'-circled"></i>'+m+'</p>');
            $m.appendTo('#tabs .tab.active').delay(2000).fadeOut(2000);
        },
        _scrollTop:function(){// 滚回顶部
            $('body,html').animate({ scrollTop: 0 }, 300);
            return false;
        }
    },
    Dom:{// 系统后台的整体JS支撑
        _init:function(){
            this.Tab._init();
            this.Nav._init();
            this.Body._init();
            this.A._init();
        },
        Btn:{
            _add:function(url,q){// 为当前页面增加一个添加按钮
                var $tp = EsAdmin.Dom.Body._tabPanel();
                if(q){
                    var i = $tp[0].data('fromid'),tabs =EsAdmin.Dom.History.tab[i],
                    _url = tabs[tabs.length-1],pid = _url.substring(_url.lastIndexOf('/')+1),
                    _q = /^\d+$/.test(pid)?(q+pid):'';
                    url += _q;
                }
                var $btn = $('<a data-href="'+url+'" class="btn btn-smallx2 btn-blue">添加</a>'),
                    $refresh = $tp[1].find('.refresh');

                if($refresh.length){
                    $refresh.after($btn);
                }else{
                    $btn.appendTo('<p></p>').prependTo($tp[1]);
                }
            }
        },
        A:{
            _init:function(){
                $('#panels').on('click','a:not(.no-bind-a)',EsAdmin.Dom.A._click);
                $('#panels').on('click','.reback,.refresh',function(){
                    var tp = EsAdmin.Dom.Body._tabPanel();
                    if($(this).hasClass('reback')){
                        EsAdmin.Dom.History._back( tp[0].data('fromid'),tp[1] );
                    }else{
                        EsAdmin.Dom.History._refresh( tp[0].data('fromid'),tp[1] );
                    }
                });
            },
            _click:function(){
                var tp = EsAdmin.Dom.Body._tabPanel(),href=$(this).data('href');
                EsAdmin.Dom.Load._panel(tp[1],href,1);
            },
        },
        Body:{
            homeUrl:'/esadmin/home/welcome',
            _init:function(){
               $('#top-container').css( 'height', EsAdmin.Tool._height() );
               EsAdmin.Dom.Load._hook();
               //EsAdmin.Dom.Load._panel(EsAdmin.Dom.Panel._active(),this.homeUrl);
               this._show();
            },
            _show:function(){
               $('body').addClass('active');
               $('#loadding').removeClass('active');
            },
            _hide:function(){
                $('body').removeClass('active');
                $('#loadding').addClass('active');
            },
            // 当前处于活动状态的tab和其对应的模板
            _tabPanel:function(){
                return [$('#tabs .tab.active'),EsAdmin.Dom.Panel._active()];
            },
            _refresh:function(){
                EsAdmin.Dom.Panel._find('button.refresh.btn-smallx2').trigger('click');
            }
        },
        History:{// 记录url访问
            tab:[],
            _add:function(tabid,url){
                this.tab[tabid] = this.tab[tabid] || [];
                var flag = 1;
                
                $.each(this.tab[tabid],function(i,u){
                   if(u == url){
                       flag = 0;
                       return false;
                   };
                })
                if(flag) this.tab[tabid].push(url);
            },
            _back:function(tabid,$panel){
                this.tab[tabid].pop();
                var t = this.tab[tabid];
                EsAdmin.Dom.Load._panel( $panel,t[t.length-1],t.length>1 );
            },
            _refresh:function(tabid,$panel){
                var t = this.tab[tabid];
                EsAdmin.Dom.Load._panel( $panel,t[t.length-1],t.length>1 );
            }
        },
        Load:{
            _err:function(m){
                m = m || 'Error,请求失败。';
                return '<p class="err"><i class="ion-alert-circled"></i>'+m+'</p>';
            },
            /**
             * 更新一个面板
             * $panel jQuery 面板 
             * url    string 面板要加载的网址
             * reBtn  bool   是否加载返回按钮
             * tabid  string 当前tab标签formid属性与History对应
             * preHtm string 面板加载成功后，在载入的内容之前插入
             * nxtHtm string 面板加载成功后，在载入的内容后面插入
             */
            _panel:function($panel,url,reBtn,tabid,preHtm,nxtHtm){
                preHtm = preHtm || '';
                nxtHtm = nxtHtm || '';
                reBtn = '<p>'+(reBtn?'<button type="button" class="reback btn-smallx2 btn-green">后退</button>':'')+'<button type="button" class="refresh btn-smallx2 btn-orange">刷新</button></p>';
                //reBtn = reBtn?btnHtm:'';
                tabid = tabid || $('#tabs .tab.active').data('fromid');
                
                EsAdmin.Dom.History._add(tabid,url);
                $panel.load(url,function(html,status){
                    if( status == 'error' ){
                        $(this).html( EsAdmin.Dom.Load._err(url) );
                    }else{// 加载成功
                        $(this).html( reBtn+preHtm+html+nxtHtm );
                        EsAdmin.Tool._addCls($(this));
                        EsAdmin.Dom.Load._hook();
                    }
                });
            },
            /**
             * 面板内容加载完成后自动执行
             */
            _hook:function(){
                if( EsAdmin.Dom.Panel._find('.es4-form').length ){
                    if(typeof(Form)==="undefined"){
                        $.getScript('/min/esadmin.form.js',function(){
                            Form._init();
                        });
                    }else{
                        Form._init();
                    }
                }
                if( EsAdmin.Dom.Panel._find('.eno-datagrid').length ){
                    if(typeof(Grid)==="undefined"){
                        $.getScript('/min/esadmin.grid.js',function(){
                            Grid._init();
                        });
                    }else{
                        Grid._init();
                    }
                }
                var load_script = EsAdmin.Dom.Panel._find('.load-script');
                if( load_script.length ){
                    var src = load_script.data('src');
                    if(src.length){
                        $.getScript(src);
                    }
                }
            }
        },
        Nav:{
            len:9,//最大有几个标签页，超过会自动关闭非当前页和首页的第一个
            _init:function(){
                $('#nav span').each(function(){
                    EsAdmin.Dom.Nav._isHide( $(this) );
                }).click(function(){
                    $(this).next('div').toggle();
                    EsAdmin.Dom.Nav._isHide( $(this) );
                });
                $('#nav a').click(EsAdmin.Dom.Nav._a);
            },
            _isHide:function($span){
                $span.children('em').toggleClass('active',!$span.next('div').is(":hidden"));
            },
            _a_active:function($a){// 当前的菜单高亮
                $('#nav a').removeClass('active');
                $a.addClass('active');
            },
            _a:function(){
                $t = $(this);
                EsAdmin.Dom.Nav._a_active($t);
                if(!$('#loadding').is(":hidden")){
                    return false;
                }
                var id = $t.attr('id'),$tab_id = $('#tabs [data-fromid="'+id+'"]');
                if($tab_id.length){// 是否需要新打开标签
                    EsAdmin.Dom.Tab._switch( $tab_id );
                }else{
                    if( $('#tabs .tab').length == EsAdmin.Dom.Nav.len ){// 对标签数量进行限制
                        $('#tabs .tab:not(.active)').eq(1).find('i').trigger('click');
                    }
                    
                    var $_t = $t.clone(),$_span = $t.parents('div').siblings('span').clone();
                    $_t.find('i,sup').remove(),$_span.find('i,em').remove(),shtml=$_span.html();
                    
                    var tab_txt = (shtml.length>0?shtml+'-':'')+$_t.html(),
                        href = $t.data('href'),
                        $tab = $('<div class="tab" data-fromid="'+id+'">'+tab_txt+'</div>'),
                        $panel = $('<div class="panel"></div>');
                    
                    $tab.append($('<i class="ion-ios-close-outline"></i>'));
                    EsAdmin.Dom.Load._panel($panel,href,0,id);
                    $('#tabs').append($tab);
                    $('#panels').append($panel);
                    
                    $tab.trigger('click');
                }
                
                EsAdmin.Tool._scrollTop();
                return false;
            }
        },
        Tab:{
            _init:function(){
                $('#tabs').on('click','.tab',EsAdmin.Dom.Tab._switch)
                          .on('click','i', EsAdmin.Dom.Tab._close);
            },
            _close:function(){
                i = $(this).parent('.tab').index();
                $('#tabs .tab').eq(i).remove();
                EsAdmin.Dom.Panel._list().eq(i).remove();
                if( !$('#tabs .tab.active').length ){
                    $('#tabs .tab:last-child').trigger('click');
                }
                return false;
            },
            _switch:function($o){
                var i = $o instanceof jQuery ?$o.index():$(this).index(),
                   $j = $o instanceof jQuery ?$o : $(this),
                   $nav = $('#'+$j.data('fromid'));
                EsAdmin.Tool._addCls( $j );
                EsAdmin.Tool._addCls( EsAdmin.Dom.Panel._list().eq(i) );
                // 更改title标题
                var $tit = $j.clone();
                $tit.find('i').remove();
                $('title').html( $tit.html() );
                EsAdmin.Dom.Nav._a_active($nav);
            },
            
        },
        Panel:{
            _active:function(selector){
                var $active = $('#panels .panel.active');
                if(selector) return $active.find(selector);
                return $active;
            },
            _list:function(i){
                var $panels = $('#panels .panel'); 
                if(i) return $panels.eq(i);
                return $panels;
            },
            _find:function(selector){
                return EsAdmin.Dom.Panel._active(selector);
            }
        }
    },
    // 查询功能
    Search:{
        _init:function(){
            $('#es-search span').click(EsAdmin.Search._submit);
            $('#es-search input').keypress(function(e){
                if(e.keyCode == "13"){
                    EsAdmin.Search._submit();
                }
            });
        },
        _submit:function(){
            var ipt = $('#es-search input').val(),
                $a = $('#nav a.active'),
                url = $a.data('href'),
                f = $a.children('sup').length;
            if(ipt && url && f){
                EsAdmin.Dom.Load._panel( EsAdmin.Dom.Panel._active() ,url+'?search-key='+ipt,1);
            }
        }
    }
};
EsAdmin.Tool._ajaxStart()._ajaxComplete()._ajaxError();
EsAdmin.Dom._init();
EsAdmin.Search._init();