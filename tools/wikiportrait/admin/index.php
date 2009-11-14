<?php
	include '../inc/common.php';

	$admin_tasks = array(
		"good"          => ___( "ACTION_GOOD" ),
		"google"        => ___( "ACTION_GOOGLE" ),
		"bad_quality"   => ___( "ACTION_BAD_QUALITY" ),
		"invalid_email" => ___( "ACTION_INVALID_EMAIL" ),
		"not_relevant"  => ___( "ACTION_NOT_RELEVANT" )
	);

	$id = $_GET['id'];
	$secret = $_GET['secret'];
	$action = $_GET['action'];

	if ( !$id || !$secret ) {
		// bail("No id or secret given");
	}

	// Look up the id and see if it matches with the secret
	$image = $db->select( TB_IMAGES, $id );
	if ( !$image ) bail( "id not found" );

	// Security precaution
	unset( $image['email'] );

	/*
	$image_secret = md5(GE_SECRET.$image['id'].$image['timestamp']);

	if($image_secret != $secret) bail("Secret not correct");
	*/

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Wikiportret admin</title>
		<style type="text/css">
			#wrapper { width: 960px; }
			.clear { clear:both; }
			img { float: left; }
			.warning { color: red; text-decoration:underline; }
			#data {
				float:right;
			}
			#data li { margin-bottom: 10px; }
			#data strong {
				display: block;
			}
		</style>
	</head>
	<body>
	<div id="wrapper">
		<?php
			if ( $action ) :
		?>
		<textarea cols="80" rows="40"><?php echo admin_action( $action ); ?></textarea>
		<?php
			else :
		?>
		<h1><?php __( "RATE_THIS_IMAGE" ); ?></h1>

		<?php if ( $image['action'] != null ) echo '<h2 class="warning">' . ___( "IMAGE_ACTION_DONE" ) . '</h2>'; ?>

		<a href="<?php echo GE_URL . 'uploads/' . $image['filename']; ?>">
			<img src="<?php timthumb( GE_URL . 'uploads/' . $image['filename'], 640, 480 ); ?>" alt="<?php echo $image['title']; ?>" />
		</a>
		<ul id="data">
			<?php
				foreach ( $image as $key => $value ) {
					echo "<li><strong>$key</strong><span>$value</span></li>";
				}
			?>
		</ul>

		<br class="clear" />

		<h2><?php __( "ACTION_FOR_IMAGE" ); ?></h2>
			<ul id="tasks">
				<?php
					if ( $image['action'] == null ) {
						foreach ( $admin_tasks as $task => $text ) {
							$url = GE_URL . "admin/index.php?id=$id&secret=$secret&action=$task";
							echo '<li><a href="' . $url . '">' . $text . '</a></li>';
						}
					}
				?>
			</ul>
		<?php
			endif;
		?>
	</div>
	</body>
</html>
