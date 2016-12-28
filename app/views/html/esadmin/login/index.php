<?php 
echo \es\helpers\generate_html5_head($title,$css,FALSE,FALSE,TRUE,TRUE).
    $F->input('user_name',['required','autofocus'])
      ->input('user_password',['required','type'=>'password'])
      ->display([],['type'=>'submit','class'=>'btn-blue','name'=>'sign','value'=>$sign],FALSE).
    \es\helpers\generate_html5_foot($js);
