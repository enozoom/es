var SearchForm =
{
    _init:function(){
        SearchForm.Datepicker._init();
        SearchForm._submit();
        SearchForm.A._init();
        SearchForm.Dialog._init();
    },
    _submit:function(){
        $('#statistics form').submit(function(){
            $(this).attr('action','/esadmin/search/'+$(this).data('action')+'/');
            return true;
        });
    },
    Datepicker:{
        _init:function(){
            $('.edatepicker').datepicker({language:'zh',maxDate:new Date()});
        }
    },
    A:{
        _init:function(){
            var action = $('#statistics form').data('action');
            if( action == 'broker' )
            $('#statistics .eno-datagrid tbody tr').each(function(i){
                 var $tds = $(this).find('td'),
                     broker_id = $(this).data('bid'),
                     $team = $tds.eq(8),
                     $venue = $tds.eq(9),
                     $brokerage = $tds.eq(10);
                 $team.html(function(){
                     var t = Number( $(this).html() );
                     return t?'<a href="/esadmin/broker/d3/'+broker_id+'/" target="_blank">'+t+'</a>':t;
                 });
                 $venue.html(function(){
                     var t = Number( $(this).html() );
                     return t?'<a data-opr="1" data-href="/esadmin/venue/lists/0/0/0/'+broker_id+'/">'+t+'</a>':t;
                 });
                 $brokerage.html(function(){
                     var t = Number( $(this).html() );
                     return t?'<a data-href="/esadmin/venue/finances/'+broker_id+'/">'+t+'</a>':t;
                 });
                 SearchForm.A._click();
            });
        },
        _click:function(){
            $('#statistics').on('click','tbody a',function(){
                SearchForm.Dialog._show( $(this).data('href'),$(this).data('opr') );
            })
        }
    },
    Dialog:{
        _init:function(){
            $('#dialog-close').click( SearchForm.Dialog._close );
            SearchForm.Dialog._click();
        },
        _close:function(){
            $('#dialog').fadeOut('fast');
        },
        _show:function(url,removeOpr){
            $('#dialog-main').load( url,function(){
                if(removeOpr){
                    $('#dialog-main tbody tr').each(function(){
                        $(this).find('td:last').html('');
                    });
                }
                $('#dialog').fadeIn('fast');
             });
        },
        _click:function(){
            $('#dialog-main').on('click','.pagination a',function(){
                SearchForm.Dialog._show( $(this).data('href') );
            })
        }
    }

};
SearchForm._init();
Util.Area._init('#areas');