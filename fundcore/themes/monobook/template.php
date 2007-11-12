<?php

function monobook_regions() {
  return array(
    'left' => t('Left sidebar'),
    'footer' => t('Footer'),
    'below_content' => t('Below content'),
    'above_content' => t('Above content'),
  );
}

function monobook_direction($text) {
  if (function_exists('i18n_language_rtl') && i18n_language_rtl()) {
    return '<span dir="rtl">' . $text . '</span>';
  }
  return $text;
}

function phptemplate_body_class($sidebar_left, $sidebar_right) {
  if ($sidebar_left != '' && $sidebar_right != '') {
    $class = 'sidebars';
  }
  else {
    if ($sidebar_left != '') {
      $class = 'sidebar-left';
    }
    if ($sidebar_right != '') {
      $class = 'sidebar-right';
    }
  }

  print ' class="mediawiki ns-0 ltr '. $class .'"';
}

function monobook_menu_local_tasks() {
  $output = '';

  $primary = menu_primary_local_tasks();
  if (empty($primary)) {
    $primary = '<li class="selected"><a href="#">'. t('View') ."</a></li>\n";
  }

  $output .= "<ul>\n". $primary ."</ul>\n";

  if ($secondary = menu_secondary_local_tasks()) {
    $output .= "<ul class=\"tabs secondary\">\n". $secondary ."</ul>\n";
  }

  return $output;
}

function monobook_menu_local_task($mid, $active, $primary) {
  if ($active) {
    return '<li class="selected">'. menu_item_link($mid) ."</li>\n";
  }
  else {
    return '<li>'. menu_item_link($mid) ."</li>\n";
  }
}

function monobook_item_list($items = array(), $title = NULL, $type = 'ul', $attributes = NULL) {
  if (isset($title)) {
    $output .= '<h3>'. $title .'</h3>';
  }

  if (!empty($items)) {
    $output .= "<$type" . drupal_attributes($attributes) . '>';
    foreach ($items as $item) {
      $attributes = array();
      $children = array();
      if (is_array($item)) {
        foreach ($item as $key => $value) {
          if ($key == 'data') {
            $data = $value;
          }
          elseif ($key == 'children') {
            $children = $value;
          }
          else {
            $attributes[$key] = $value;
          }
        }
      }
      else {
        $data = $item;
      }
      if (count($children) > 0) {
        $data .= theme_item_list($children, NULL, $type, $attributes); // Render nested list
      }
      $output .= '<li' . drupal_attributes($attributes) . '>'. $data .'</li>';
    }
    $output .= "</$type>";
  }
  return $output;
}

function monobook_comment_wrapper($output) {
  if (arg(2) == 'talk') {
    return theme_comment_wrapper($output);
  }
}

function monobook_breadcrumb($breadcrumb) {
  if (count($breadcrumb) > 1) {
    return theme_breadcrumb($breadcrumb);
  }
} 