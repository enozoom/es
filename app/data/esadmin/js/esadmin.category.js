$(function(){
    $('#panels .panel.active .eno-datagrid tbody tr').each(function(){
        $(this).find('td:eq(1)').html(function(){
            return '<a data-href="/esadmin/category/lists/'+$(this).prev().html()+'">'+$(this).html()+'</a>';
        });
    });
    
    var i = $('#tabs .tab.active').data('fromid'),tabs =EsAdmin.Dom.History.tab[i],
        url = tabs[tabs.length-1],pid = url.substring(url.lastIndexOf('/')+1),
        q = /^\d+$/.test(pid)?('?pid='+pid):'',
        $btn = $('<a data-href="/esadmin/category/id'+q+'" class="btn btn-smallx2 btn-blue">添加</a>'),
        $panel = $('#panels .panel.active'),
        $refresh = $panel.find('.refresh');
    if($refresh.length){
        $refresh.after($btn);
    }else{
        $btn.appendTo('<p></p>').prependTo($panel);
    }
});