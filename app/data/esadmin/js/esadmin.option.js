;$(function(){
    var $btn = $('<a data-href="/esadmin/option/id" class="btn btn-smallx2 btn-blue">添加</a>'),
        $panel = $('#panels .panel.active'),
        $refresh = $panel.find('.refresh');
    if($refresh.length){
        $refresh.after($btn);
    }else{
        $btn.appendTo('<p></p>').prependTo($panel);
    }
    $('#panels .panel.active .eno-datagrid tbody tr').each(function(){
        $(this).find('td:eq(1)').html(function(){
            var v = $(this).find('span').html();
            return '<a data-href="/esadmin/option/lists/'+v+'">'+v+'</a>';
        });
    });
    
})