<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php $thp = base_path() . path_to_theme(); ?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php print $head_title ?></title>
    <?php print $head ?>
    <?php print $styles ?>
    <?php print $scripts ?>
		<link rel="stylesheet" href="<?php print "$thp/styles.css";?>" type="text/css">
		<style type="text/css">
			body {
				margin: 0px;
				padding: 0px;
				background-image:url(<?php print "$thp/images/background.gif";?>); background-repeat:repeat-y;
				background-position:center;
				background-color:#006699; 
				font-family: Verdana, Arial, Sans-Serif;
				font-size: 10pt;
			}
		</style>
		<script type="text/javascript" src="<?php print "$thp/fundcore.js"; ?>"></script>
	</head>

	<body>

		<center>

			<table border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed;">
				<tr>
					<td width="886" height="123" valign="middle" style="background-image:url(<?php print "$thp/images/header-logos.jpg";?>);">
						<div style="margin-left:160px; font-size:22px; font-weight:bold; color:#484848; text-align:left;">

							<?php echo t("You can help Wikipedia<br>change the world"); ?>

						</div>
					</td>
				</tr>
				<tr>
					<td width="886" height="15" style="background-image:url(<?php print "$thp/images/header-border.gif";?>); background-repeat:repeat-x;">
					</td>
				</tr>
			</table>

      <div id="content" style="width: 886px;">
        <?php if ($messages) { ?>  <div id="messages"><?php echo $messages; ?></div> <?php } ?>
        <?php if ($breadcrumb) { ?>  <div style="float: right; padding-top:16px;"><?php echo $breadcrumb; ?></div> <?php } ?>
        <?php if ($title): print fundcore_direction('<h1>'. $title .'</h1>'); endif; ?>
        <?php if (isset($tabs2)): print $tabs2; endif; ?>
        <?php if ($mission): print '<div id="mission">'. $mission .'</div>'; endif; ?>
        <?php if ($help): print $help; endif; ?>
        <div id="bodyContent">
        <?php print $content ?>
        </div>
        <div class="visualClear"></div>
        <?php print $below_content ?>
        <?php print $feed_icons ?>
      </div>

			<br><br>

			<div id="lastline">
				<?php echo $last_line; ?>
			</div>
			<br><br>


		</center>
	</body>

</html>

