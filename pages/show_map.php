<?php
$places = array();
$params = isset($_GET['place']) ? $_GET['place'] : array();

foreach ($params as $item) {
  $matches = explode('|', $item);

  $places[] = array(
    'x' => (float) $matches[0],
    'y' => (float) $matches[1],
    'title' => json_encode(htmlspecialchars($matches[3])),
    'description' => json_encode(htmlspecialchars($matches[2])),
  );
}

$center = (isset($_GET['center'])) ? $_GET['center'] : null;

if(false === is_null($center)) {
  $center = explode('|', $_GET['center']);
}

include(dirname(__FILE__) . '/load_language.php');
?><!DOCTYPE html>
<html class="here-maps-root">
<head>
  <meta charset="UTF-8">
  <title><?=__('here-maps-front-title', 'here-maps');?></title>
  <meta name="viewport" content="initial-scale=1.0, width=device-width">
  <link rel="stylesheet" href="<?php echo here_maps_join_file('stylesheets/style.min.css'); ?>">
  <?php if(1 === count($places)) : ?>
    <?php $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>
    <meta property="og:type" content="place">
    <meta property="og:url" content="<?=$actual_link;?>">
    <meta property="place:location:latitude"  content="<?=$places[0]['y'];?>">
    <meta property="place:location:longitude" content="<?=$places[0]['x'];?>">
  <?php endif; ?>
  <script>
    (function(w){ // H is namespace for HERE Maps
      if("undefined"===typeof w.H){w.H={};}w.H.Auth={};w.H.places=[];w.H.MapBubbles=[];w.H.MapBubblesCounter=0;
      w.H.Auth.id='<?=get_option('here_maps_app_id');?>';w.H.Auth.code='<?=get_option('here_maps_app_code');?>';
      <?php foreach($places as $item) : ?>
        w.H.places.push({ x: <?=$item['x'];?>, y: <?=$item['y'];?>, description: <?=$item['description'];?>, title: <?=$item['title'];?>});
      <?php endforeach; ?>
      w.H.Lang={
        'here-maps-markers-new-marker-tooltip': '<?=__('here-maps-markers-new-marker-tooltip', 'here-maps');?>',
        'here-maps-markers-new-marker-title': '<?=__('here-maps-markers-new-marker-title', 'here-maps');?>',
        'here-maps-markers-label-changepin': '<?=__('here-maps-admin-gui-label-changepin', 'here-maps');?>',
        'here-maps-markers-label-restorepin': '<?=__('here-maps-admin-gui-label-restorepin', 'here-maps');?>',
        'here-maps-markers-label-seeonhere': '<?=__('here-maps-front-template-see', 'here-maps');?>',
        'here-maps-markers-label-getdirection': '<?=__('here-maps-front-template-get', 'here-maps');?>'
      };
      w.H.GetParams = {
        zoom: <?=json_encode($_GET['zoom']);?>,
        center: <?=json_encode($center);?>,
        template: <?=json_encode($_GET['template']);?>,
        hidden: <?=json_encode($_GET['hidden']);?>,
        map_mode: <?=json_encode($_GET['map_mode']);?>,
        placeid: <?=json_encode($_GET['placeid']);?>,
        title: <?=json_encode($_GET['title']);?>
      };
    })(window);
  </script>
</head>
<body class="container-fluid">
<div id="mapContainer">
<?php
  $hidden = isset($_GET['hidden']) ? $_GET['hidden'] : null;
  $template = isset($_GET['template']) ? $_GET['template'] : 'empty';

  if ('fixed' === $template) {
    include(dirname(__FILE__) . '/include/template-box.html');
  }

  if (false == preg_match('/map_mode/i', $_GET['hidden'])) {
    include(dirname(__FILE__) . '/include/map-select.html');
  }
?>
</div>

<script src="<?php echo here_maps_join_file('javascripts/api.min.js') ?>"></script>
<script src="<?php echo here_maps_join_file('javascripts/front.min.js') ?>"></script>
</body>
</html>
