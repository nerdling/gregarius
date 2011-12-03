<?php 
$GLOBALS['rss']->nav->appendNavItem(getPath()."?feeds",__('Feeds'));
$GLOBALS['rss']->nav->appendNavItem(getPath()."?cats",__('Categories'));
$GLOBALS['rss']->nav->appendNavItem(getPath()."update.php?mobile", __('Refresh'));
$GLOBALS['rss']->nav->appendNavItem("#top",'TOP of Page');

foreach ($GLOBALS['rss']->nav->items as $item) {
    if( !preg_match( '/update.php$/i', $item -> href ) && (stripos( $item->href, 'admin') === false) ) {
			$item -> render();
    }
}

?>
