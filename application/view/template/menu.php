<div class="row-fluid" id="menu-container">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
        <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <div class="nav-collapse collapse">
          <ul class="nav">

            <?php 
                if(userSession('serialized_user_menu')) :

                  $user_menu = unserialize(userSession('serialized_user_menu'));

                  foreach($user_menu as $menu)
                  {
            ?>
                        <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=$menu['CAPTION']?><b class="caret"></b></a>
                        <ul class="dropdown-menu">
                          <?php foreach($menu['sub_menus'] as $submenu) : ?>
                            <li><a href="?_page=<?=$submenu['ITEMPAGE']?>&_action=<?=$submenu['ITEMACTION']?>"><?=$submenu['CAPTION']?></a></li>
                          <?php endforeach; ?>
                        </ul>
                      </li>
                    
            <?php
                  }
                endif;
            ?>
          </ul>
          <p class="pull-right" id="greeting">
              <?php
                  if(userSession('erequest')){
                    echo 'Welcome! '.userSession('userid');
                    echo '&nbsp; <a href="?_page=user&_action=logout">Logout</a>';
                  }
              ?> 
          </p>
        </div><!--/.nav-collapse -->
    </div>
  </div>
</div>