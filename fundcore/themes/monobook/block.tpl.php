<?php
  $portlet = '';
  if ($block->region == 'left') {
    $portlet = 'portlet';
  }
?>

<?php if ($block->module != 'search') { ?>
<div id="p-<?php print $block->module .'-'. $block->delta; ?>" class="<?php echo $portlet; ?> p-<?php print $block->module ?>">

<?php if ($block->subject): ?>
  <h5><?php print $block->subject ?></h5>
<?php endif;?>

  <?php if ($block->region == 'left') { ?>
  <div class="pBody">
  <?php } ?>

    <?php print $block->content ?>
  <?php if ($block->region == 'left') { ?>
  </div>
  <?php } ?>
</div>
<?php } else { ?>
<div id="p-search" class="portlet">

<?php if ($block->subject): ?>
  <h5><?php print $block->subject ?></h5>
<?php endif;?>

  <div id="searchBody" class="pBody">
    <?php print $block->content ?>
  </div>
</div>
<?php } ?>
