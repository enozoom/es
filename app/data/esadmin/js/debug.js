$.ajaxSetup({dataType:'json'});
var Sel = $('#multiple-input').selectivity({
        multiple: true,
        placeholder: 'Type to search cities',
        ajax:{
             url:'/esadmin/debug/data',
             results:function(data){
                 return {
                    results:{
                        map:function(){
                            return data;
                        }
                    }
                 }
             }
        }
    });
    
//console.log(Sel.selectivity('val'));