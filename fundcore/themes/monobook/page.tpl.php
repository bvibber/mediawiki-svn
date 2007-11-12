<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language ?>" lang="<?php print $language ?>">
  <head>
    <title><?php print $head_title ?></title>
    <?php print $head ?>
    <!--<style type="text/css" media="all">@import "/sites/all/themes/monobook/common.css";</style>
    <style type="text/css" media="all">@import "/sites/all/themes/monobook/style.css";</style>-->
    <style>
      .float-right {float: right; width: 200px; border:3px solid; border-color:#339966; margin:5px; padding: 5px; font-size: 90%}
    </style>
    <?php print $styles ?>
    <?php if (function_exists('i18n_language_rtl') && i18n_language_rtl()) { ?>
      <style type="text/css" media="all">@import "/sites/all/themes/monobook/rtl.css";</style>
    <?php } ?>
    <?php print $scripts ?>
  </head>
  <body<?php print phptemplate_body_class($sidebar_left, $sidebar_right); ?> style="background: #f9f9f9 url(/sites/all/themes/monobook/headbg.jpg) 0 0 no-repeat;">
    <div id="globalWrapper">
      <div id="column-content">
        <div id="content">
          <?php if ($messages) { ?>  <div id="siteNotice"><?php echo $messages; ?></div> <?php } ?>
          <?php if ($breadcrumb) { ?>  <div style="float: right; padding-top:16px;"><?php echo $breadcrumb; ?></div> <?php } ?>
          <?php if ($title): print monobook_direction('<h1 class="firstHeading">'. $title .'</h1>'); endif; ?>
          <?php if (isset($tabs2)): print $tabs2; endif; ?>
          <?php print $above_content ?>
          <?php if ($mission): print '<div id="mission">'. $mission .'</div>'; endif; ?>
          <?php if ($help): print $help; endif; ?>
          <div id="bodyContent">
          <?php print $content ?>
          </div>
          <div class="visualClear"></div>
          <?php print $below_content ?>
          <?php print $feed_icons ?>
        </div>
      </div>
      <div id="column-one">
        <div id="p-cactions" class="portlet">
          <h5><?php echo t('Views'); ?></h5>
          <div class="pBody">
            
            <?php print $tabs; ?>
      
          </div>
        </div>
        <div class="portlet" id="p-personal">
          <h5>Personal tools</h5>
          <div class="pBody">
            <ul>
              <?php
                global $user;
                if ($user->uid == 0) {
              ?>
                <li id="pt-login" style="background: url(/sites/all/themes/monobook/user.gif) top left no-repeat;"><a href="/user" title="You are encouraged to log in, it is not mandatory however. [o]" accesskey="o">
                <?php if (variable_get('user_register', 2) > 0) {
                    echo t('Sign in / create account');
                  }
                  else {
                    echo t('Sign in');
                  }
                ?>
                </a></li>
              <?php } else { ?>
                <li id="pt-userpage"><a href="/user" title="<?php echo t('My user page [.]'); ?>" accesskey="."><?php echo $user->name; ?></a></li>
                <li id="pt-preferences"><a href="/user/<?php echo $user->uid; ?>/edit" title="<?php echo t('My preferences'); ?>"><?php echo t('My preferences'); ?></a></li>
                <?php if (module_exists('tracker')) { ?>
                <li id="pt-mycontris"><a href="/user/<?php echo $user->uid; ?>/track" title="<?php echo t('List of my contributions [y]'); ?>" accesskey="y">My contributions</a></li>
                <?php } ?>
                <li id="pt-logout"><a href="<?php echo url('logout'); ?>" title="Log out"><?php echo t('Log out'); ?></a></li>
              <?php } ?>
            </ul>
          </div>
        </div>
        <div class="portlet" id="p-logo">
      		<a style="background-image: url(<?php echo check_url($logo); ?>);" href="<?php echo check_url($base_path); ?>" title="<?php echo $site_title; ?>"></a>
      	</div>

        <!--<?php if ($search_box): ?><div class="block block-theme"><?php print $search_box ?></div><?php endif; ?>-->
        <?php print $sidebar_left ?>
      </div><!-- end of the left (by default at least) column -->
      <div class="visualClear"></div>
      <?php if ($footer_message) { ?>
      <div id="footer">
        <?php print $footer_message ?>
      </div>
      <?php } ?>
    </div>
  </div>
  <?php print $closure ?>
</body></html>