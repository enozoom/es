<?php echo \es\helpers\generate_html5_head($title,$css);?>

<div class="row" id="top-container">
    <div class="col" id="nav">
        <div id="logo"><strong>ENOZOOM</strong><small>studio</small></div>
        <div id="es-search" class="fix-float">
            <input class="f-left" name="search-key" placeholder="需在对应功能下搜索">
            <span class="f-left"><i class="ion-ios-search-strong"></i></span>
        </div>
        <?php echo $menus?>
    </div>
    <div class="col" id="panel-container">
        <div id="tabs" class="fix-float">
          <div class="tab active" data-fromid="menu-0"><?php echo $title?></div>
        </div>
        <div id="panels">
            <div class="panel active">