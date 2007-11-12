<?php

function fundcore_regions() {
  return array(
    'last_line' => t('Last line'),
  );
}

function fundcore_direction($text) {
  if (function_exists('i18n_language_rtl') && i18n_language_rtl()) {
    return '<span dir="rtl">' . $text . '</span>';
  }
  return $text;
}

function fundcore_breadcrumb($breadcrumb) {
  if (count($breadcrumb) > 1) {
    return theme_breadcrumb($breadcrumb);
  }
} 