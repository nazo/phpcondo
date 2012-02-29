<?php
/**
 * [PUBLISH] Facebook LikeBoxウィジェット
 *
 * @copyright		Copyright 2011, materializing.
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			fb_likebox.views
 * @since			Baser v 1.6.14
 * @version			1.1.1
 * @license			GPL
 */
$FbLikeboxConfig = ClassRegistry::init('FbLikebox.FbLikeboxConfig');
$config = $FbLikeboxConfig->findExpanded();
// CSV利用時に、0値がなぜか空文字で入ってくるので対処
$config['show_faces'] = $FbLikeboxConfig->checkEmpty($config['show_faces']);
$config['stream'] = $FbLikeboxConfig->checkEmpty($config['stream']);
$config['header'] = $FbLikeboxConfig->checkEmpty($config['header']);
$color_scheme = $FbLikeboxConfig->color_scheme;
$show_faces = $FbLikeboxConfig->show_faces;
$stream = $FbLikeboxConfig->stream;
$header = $FbLikeboxConfig->header;
$language = $FbLikeboxConfig->language;
?>

<div class="widget widget-fb_likebox widget-fb_likebox-<?php echo $id ?>">
<?php if($name && $use_title): ?>
<h2><?php echo $name ?></h2>
<?php endif ?>

<div id="fb-root"></div>
<script src="http://connect.facebook.net/<?php echo $language[$config['language']] ?>/all.js#xfbml=1"></script>
<fb:like-box href="<?php echo $config['page_url'] ?>" width="<?php echo $config['width'] ?>" colorscheme="<?php echo $color_scheme[$config['color_scheme']] ?>" show_faces="<?php echo $show_faces[$config['show_faces']] ?>" stream="<?php echo $stream[$config['stream']] ?>" header="<?php echo $header[$config['header']] ?>" border_color="<?php echo $config['border_color'] ?>">
</fb:like-box>

</div>
