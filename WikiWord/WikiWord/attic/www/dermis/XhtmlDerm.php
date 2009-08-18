<?
require_once(dirname(__FILE__) . '/Dermis.php');

global $dermisHtmlFormats;
$dermisHtmlFormats['XHTML-1.0/Transitional'] = array(
	'id' => '-//W3C//DTD XHTML 1.0 Transitional//EN',
	'dtd' => 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd',
	'namespace' => 'http://www.w3.org/1999/xhtml',
	'mime' => 'text/html',
);

class XhtmlDerm extends DermisProcessor {
	function __construct() {
		DermisProcessor::__construct();

		$this->setFunction('page', array($this, 'page'), true, true, true);
		$this->setFunction('headtags', array($this, 'headtags'), true, true, true);
		$this->setFunction('bodytags', array($this, 'bodytags'), true, true, true);
		$this->setFunction('bodycontent', 'return $this->loremIpsum(5);', true, false, false);

		$this->setFunction('scriptlinkEntry', array($this, 'scriptlinkEntry'), true, false, true);
		$this->setFunction('stylelinkEntry', array($this, 'stylelinkEntry'), true, false, true);
		$this->setFunction('feedlinkEntry', array($this, 'feedlinkEntry'), true, false, true);
		$this->setFunction('scriptEntry', array($this, 'scriptEntry'), true, false, true);
		$this->setFunction('styleEntry', array($this, 'styleEntry'), true, false, true);

		$this->setFunction('selectBox', array($this, 'selectBox'), true, false, true);

		$this->setFormat('XHTML-1.0/Transitional');
		$this->setCharset('utf-8');
		$this->setLanguage('en');

		$this->setValue('sitetitle', 'Dermis Demo / XHTML');
		$this->setValue('pagetitle', 'Test Page');
		$this->setValue('footer', '', true, false, false);

		$this->setFunction('windowtitle', 'return $this->text("pagetitle") . " - " . $this->text("sitetitle");');
	}

	//---------------------------------------------------------
	function setFormat($format) {
		$this->setValue('format', 'XHTML-1.0/Transitional');
	}

	function setCharset($charset) {
		$this->setValue('charset', $charset);
	}

	function setLanguage($lang) {
		$this->setValue('language', $lang);
	}

	function jsEscape($s) {
		return str_replace(array('\\', '"', '\''), array('\\\\', '\"', '\\\"'), $s);
	}

	//---------------------------------------------------------
	function comment($txt) {
		return '<!-- ' . str_replace('--', '~~', $txt) . ' -->';
	}

	function meta($name, $cmp) {
		if (!$this->has($cmp)) return false;
		$t = $this->atext($cmp);
		return '<meta name="' . $name . '" content="' . $t . '"/>';
	}

	function style($media, $cmp) {
		if (!$this->has($cmp)) return false;
		$code = $this->text($cmp);
		
		return '<style type="text/css" media="'.$media.'">/*<![CDATA[*/'.$code.'/*]]>*/</style>';
	}

	function stylelink($media, $cmp) {
		if (!$this->has($cmp)) return false;
		$u = $this->escape($this->url($cmp));
		
		return '<link rel="stylesheet" type="text/css" media="'.$media.'" href="'.$u.'" />';
	}

	function link($rel, $cmp, $type=NULL, $title=NULL) {
		if (!$this->has($cmp)) return false;
		$u = $this->escape($this->url($cmp));
		
		return '<link rel="'.$rel.'" '.($type?'type="'.$type.'"':'').' '.($title?'type="'.$title.'"':'').' href="'.$u.'" />';
	}

	function script($cmp, $type= 'text/javascript') {
		if (!$this->has($cmp)) return false;
		$code = $this->text($cmp);
		
		return '<script type="'.$type.'" >/*<![CDATA[*/'.$code.'/*]]>*/</script>';
	}

	function scriptlink($cmp, $type= 'text/javascript') {
		if (!$this->has($cmp)) return false;
		$u = $this->escape($this->url($cmp));
		
		return '<script type="'.$type.'" src="'.$u.'"></style>';
	}

	function feedlink($cmp, $type='application/rss+xml', $title='RSS 2.0') {
		$this->link('alternate', $cmp, $type, $title);
	}

	//---------------------------------------------------------

	function scriptlinkEntry($proc, $args) {
		$u = $this->escape($args['entry']);
		$type = 'text/javascript'; //TODO...

		return '<script type="'.$type.'" src="'.$u.'"></style>';
	}

	function stylelinkEntry($proc, $args) {
		$u = $this->escape($args['entry']);
		$media = 'screen,print,emboss,handheld'; //TODO...!!!

		return '<link rel="stylesheet" type="text/css" media="'.$media.'" href="'.$u.'"/>';
	}

	function feedlinkEntry($proc, $args) {
		$u = $this->escape($args['entry']);
		$type = 'text/javascript'; //TODO...

		return '<link rel="alternate" type="'.$type.'" href="'.$u.'"/>';
	}

	function scriptEntry($proc, $args) {
		$code = $args['entry'];
		$type = 'text/javascript'; //TODO...

		return '<script type="'.$type.'" >/*<![CDATA[*/'.$code.'/*]]>*/</script>';
	}

	function styleEntry($proc, $args) {
		$code = $args['entry'];
		$media = 'screen,print,emboss,handheld'; //TODO...!!!

		return '<style type="text/css" media="'.$media.'">/*<![CDATA[*/'.$code.'/*]]>*/</style>';
	}

	function selectBox($proc, $args) {
		$options = $args['options'];
		$attr = $args;
		
		if (isset($args['selected'])) {
			$selected = $args['selected'];
			unset($attr['selected']);
		}
		else if (isset($args['name'])) {
			$n = $args['name'];
			$selected = @$_REQUEST[$n];
		}
		else {
			$selected = NULL;
		}

		unset($attr['options']);
		$s = "\n\t<select ".$this->attributeList($attr).">\n";
		
		foreach ($options as $value => $text) {
			if (is_int($value)) $value = $text;
			
			if ($value == $selected) $sel=' selected="selected"';
			else $sel = "";
			
			$s.= "\t\t<option value=\"".$this->escape($value)."\"$sel>".$this->escape($text)."</option>\n";
		}
		
		$s.= "\t</select>\n";
		return $s;
	}

	function element($tag, $attr, $content = NULL) {
		$s = "<$tag".$this->attributeList($attr);
		
		if ($content === NULL || $content === false) $s.= "/>";
		else $s.= ">$content</$tag>";
		
		return $s;
	}

	function attributeList($attr) {
		$s = "";
		
		foreach ($attr as $k => $v) {
			if ($k=="style" && is_array($v)) {
				if (isset($v[0])) $v = implode("; ", $v);
				else {
					$style = "";
					foreach ($v as $sk => $sv) {
						$style .= "$sk; $sv;";
					}
					
					$v = $style;
				}
			}
			
			if ($k=="class" && is_array($v)) {
				$v = implode(" ", $v);
			}
			
			$s.= " $k=\"".$this->escape($v)."\"";
		}
		
		return $s;
	}

	function icon($name, $alt) {
		if (!$this->has($name)) return $this->escape($alt);

		$url = $this->url($name);
		return '<img src="'.$this->escape($url).'" alt="'.$this->escape($alt).'" title="'.$this->escape($alt).'" border="0"/>';
	}

	//---------------------------------------------------------

	function page( ) {
		global $dermisHtmlFormats;
		$format = $dermisHtmlFormats[$this->value('format')];
		$lang = $this->atext('language');
		$charset = $this->atext('charset');

?><!DOCTYPE html PUBLIC "<?= $format['id']?>" "<?= $format['dtd']?>">
<html xmlns="<?= $format['namespace']?>" xml:lang="<?= $lang ?>" lang="<?= $lang ?>">
	<head>
		<meta http-equiv="Content-Type" content="<?= $format['mime']?>; charset=<?= $charset ?>" />
		<? $this->execute('headtags') ?>
	</head>
	<body <?= $this->attribute('class', 'bodyclass')?> <?= $this->attribute('style', 'bodystyle')?>>
		<? $this->execute('bodytags') ?>
	</body>
</html>	
<?
	}

	function headtags( ) {
		?>
		<title><?= $this->atext('windowtitle')?></title>
		<?= $this->meta('robots', 'robotspolicy') ?>
		<?= $this->meta('keywords', 'keywords') ?>
		<?= $this->link('shortcut icon', 'favicon') ?>
		<?= $this->stylelink('screen', 'maincss') ?>
		<?= $this->stylelink('print', 'printcss') ?>
		<?= $this->stylelink('handheld', 'pdacss') ?>
		<?

		if ($this->has('scriptlinks')) $this->apply('scriptlinkEntry', 'scriptlinks');
		if ($this->has('stylelinks'))  $this->apply('stylelinkEntry', 'stylelinks');
		if ($this->has('feedlinks'))   $this->apply('feedlinkEntry', 'feedlinks');
		if ($this->has('headscripts')) $this->apply('scriptEntry', 'headscripts');
		if ($this->has('headstyles'))  $this->apply('styleEntry', 'headstyles');

		if ($this->has('customheadtags')) $this->execute('customheadtags');
	}

	function bodytags( ) {
		?>
		<? if ($this->has('bodystart')) $this->execute('bodystart') ?>
		<h1><? $this->execute('pagetitle') ?></h1>
		<div>
			<? $this->execute('bodycontent') ?>
		</div>
		<div class="error">
			<? $this->execute('errors') ?>
		</div>
		<div class="footer">
			<hr/>
			<? $this->execute('footer') ?>
		</div>
		<? if ($this->has('bodyend')) $this->execute('bodyend') ?>
		<?
	}

	function loremIpsum( $n ) {
		return str_repeat("<p>Lorem ipsum dolor sit <b>amet</b>, consectetur adipisici elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>", $n);
	}
}

/*
error_reporting(E_ALL);
$d = new XhtmlDerm();
$d->setValue('pagetitle', 'Test <Page>');
$d->execute('page');
*/
?>