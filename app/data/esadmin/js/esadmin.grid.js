;var Grid = {
    _init:function(){
        $('.eno-datagrid .datagrid-opr-btn .no-bind-a').click( Grid.A._delete );
    },
    A:{
        _delete:function(){
            Grid.A._get( $(this).data('href') );
            return false;
        },
        _get:function(url){
            $.get(url,function(r){
                if( Number(r.err) ){
                    EsAdmin.Tool._resultErr(r.msg);
                }else{
                    EsAdmin.Tool._resultOk();
                    $('#panels .panel.active .refresh').trigger('click');
                }
            },'json');
        }
    }
}