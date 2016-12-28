<?php

namespace app\controllers\esadmin;

trait TraitMenu
{
    private $counts;// 仅仅用于菜单生成的计数
    private function menus(){
        return
        [
            [
                'tag'=>'设置','ico'=>'android-desktop ',
                'sub'=>[
                        ['tag'=>'网站设置','href'=>'home/setting/1/','ico'=>'gear-b'],
                        ['tag'=>'分类设置','href'=>'category/lists','ico'=>'navicon'],
                        ['tag'=>'管理员','href'=>'user/lists/','ico'=>'person'],
                ]
            ],
            [
                'tag'=>'文章','ico'=>'ios-paper',
                'sub'=>[
                    ['tag'=>'列表','href'=>'article/lists/','ico'=>'android-menu','search'=>1],
                    ['tag'=>'回收','href'=>'article/lists/0/23/','ico'=>'android-delete'],
                ]
            ],
            [
                'tag'=>'广告','ico'=>'social-twitter',
                'sub'=>[
                    ['tag'=>'在线','href'=>'ad/lists/1','ico'=>'android-menu'],
                    ['tag'=>'即将上线','href'=>'ad/lists/2','ico'=>'compass'],
                    ['tag'=>'即将过期','href'=>'ad/lists/3','ico'=>'android-time'],
                    ['tag'=>'过期','href'=>'ad/lists/4','ico'=>'ios-trash'],
                ]
            ],
            [// s=测试
                'tag'=>'开发者','ico'=>'code-working',
                'sub'=>[
                           ['tag'=>'开发测试','href'=>'debug/a','ico'=>'bug'],
                ]
            ],
        ];
    }
    /**
     * 生成前端菜单HTML
     */
    private function generateMenus($menus=[],$container='<div><ul>%s</ul></div>'){
        $lis = '';
        foreach($menus as $m)
        {
            $li = '<li>%s</li>';
            if(isset($m['sub']))
            {
                $span = sprintf('<span><em class="ion-ios-arrow-left f-right"></em><i class="ion-%s"></i>%s</span>',
                                $m['ico'],$m['tag']);
                $div = $this->generateMenus($m['sub']);
                $li = sprintf($li,$span.$div);
            }
            else
            {
                $a = sprintf('<a id="menu-%d" %shref="/esadmin/%s"><i class="ion-%s"></i>%s%s</a>',
                             ++$this->counts,isset($m['openw'])?'" ':'data-',
                                $m['href'],$m['ico'],$m['tag'],
                                isset($m['search'])?'<sup class="ion-ios-search-strong"></sup>':'');
                $li = sprintf($li,$a);
            }
            $lis .= $li;
        }
        return sprintf($container,$lis);
    }
}
