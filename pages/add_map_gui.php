<?php
if (!current_user_can('edit_pages') && !current_user_can('edit_posts')) {
  wp_die(__("You are not allowed to be here"));
}

$src = plugins_url(HereMapsCore::$pluginName . '/');

include(dirname(__FILE__) . '/load_language.php');
?><!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <script>
    (function(w){ // H is namespace for HERE Maps
      if("undefined"===typeof w.H){w.H={};}w.H.Auth={};w.H.places=[];
      w.H.Auth.id='<?=get_option('here_maps_app_id');?>';
      w.H.Auth.code='<?=get_option('here_maps_app_code');?>';
      w.H.LangWordpress='<?=str_replace('_', '-', get_locale());?>';
      w.H.MapObjects=[];w.H.MapObjectsCounter=0;w.H.MapBubbles=[];w.H.MapBubblesCounter=0;
      w.H.MapTempMarkers=[];w.H.MapTempMarkersCounter=0;w.H.MapTempBubbles=[];
      w.H.Lang={
        'here-maps-markers-new-marker-tooltip': '<?=__('here-maps-markers-new-marker-tooltip', 'here-maps');?>',
        'here-maps-markers-new-marker-title': '<?=__('here-maps-markers-new-marker-title', 'here-maps');?>',
        'here-maps-markers-label-changepin': '<?=__('here-maps-admin-gui-label-changepin', 'here-maps');?>',
        'here-maps-markers-label-restorepin': '<?=__('here-maps-admin-gui-label-restorepin', 'here-maps');?>',
        'here-maps-markers-label-seeonhere': '<?=__('here-maps-front-template-see', 'here-maps');?>',
        'here-maps-markers-label-getdirection': '<?=__('here-maps-front-template-get', 'here-maps');?>',
        'here-maps-markers-label-noresult': '<?=__('here-maps-search-result-nothing', 'here-maps');?>'
      };
    })(window);
  </script>
  <?php wp_head(); ?>
  <link rel="stylesheet" href="<?php echo here_maps_join_file('stylesheets/style.min.css'); ?>">
</head>
<body id="wrapper">
<div class="here-maps-admin-gui-title clearfix">
  <div class="pull-left here-maps-admin-title">
    <b><?=__('here-maps-admin-title', 'here-maps');?></b>
  </div>
  <div class="pull-right finish-btn">
    <a class="btn btn-primary pull-right btn-xs" id="insertAction"><?=__('here-maps-admin-gui-add', 'here-maps');?></a>
    <a class="btn btn-default pull-right btn-xs" id="cancelAction"><?=__('here-maps-admin-gui-cancel', 'here-maps');?></a>
  </div>
</div>

<div id="mapContainer" class="here-maps-admin-gui here-maps-admin-gui-height">
  <ul class="option-box list-unstyled list-icons js-list-icons">
    <li class="active form-inline">
      <span class="map-tab js-icon glyphicon glyphicon-map-marker" title="<?=__('here-maps-admin-gui-title-pin-icon', 'here-maps');?>"></span>
      <div class="row map-option">
        <div class="form-group">
          <input type="text" class="form-control input-xs search-input" id="here-search-input" placeholder="<?=__('here-maps-admin-gui-input-placeholder', 'here-maps');?>">
          <span class="js-clear-input clear-input hidden" title="<?=__('here-maps-admin-gui-title-x-icon', 'here-maps');?>"></span>
          <button type="button" class="btn btn-success btn-xs btn-search" id="here-search-button"><span class="glyphicon glyphicon-search"></span></button>
        </div>
        <ul id="here-search-results" class="list-unstyled hidden"></ul>
      </div>
    </li>
    <li>
      <span class="map-tab js-icon glyphicon glyphicon-cog" title="<?=__('here-maps-admin-gui-title-setting-icon', 'here-maps');?>"></span>
      <div class="map-option optional-btns">
        <h2 class="opt-btn"><?=__('here-maps-admin-gui-block-title-optional', 'here-maps');?></h2>
        <div class="map-form map-btns">
          <div class="form-group checked">
            <div class="labInp">
              <label for="opt-full-screen">
                <span class="glyphicon glyphicon-resize-full"></span>
                <input type="checkbox" name="opt-full-screen" value="true" id="opt-full-screen" class="js-opt-icon" checked>
                <?=__('here-maps-admin-gui-label-fullscreen', 'here-maps');?>
              </label>
            </div>
          </div>
          <div class="form-group checked">
            <div class="labInp">
              <label for="opt-zoom">
                <span class="glyphicon glyphicon-plus"></span>
                <input type="checkbox" name="opt-map-zoom" value="true" id="opt-zoom" class="js-opt-icon" checked>
                <?=__('here-maps-admin-gui-label-zoom', 'here-maps');?>
              </label>
            </div>
          </div>
          <div class="form-group checked">
            <div class="labInp">
              <label for="opt-map-types">
                <div class="map-types">
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </div>
                <input type="checkbox" name="opt-map-types" value="true" id="opt-map-types" class="js-opt-icon" checked>
                <?=__('here-maps-admin-gui-label-maptypes', 'here-maps');?>
              </label>
            </div>
          </div>
        </div>

        <h2><?=__('here-maps-admin-gui-block-title-size', 'here-maps');?></h2>
        <div class="map-form">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="opt-map-width"><?=__('here-maps-admin-gui-label-width', 'here-maps');?></label>
                <input type="text" class="form-control input-xs" name="opt-map-width" value="100%" id="opt-map-width">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="opt-map-height"><?=__('here-maps-admin-gui-label-height', 'here-maps');?></label>
                <input type="text" class="form-control input-xs" name="opt-map-height" value="400px" id="opt-map-height">
              </div>
            </div>
          </div>
        </div>

        <h2><?=__('here-maps-admin-gui-block-title-choose', 'here-maps');?></h2>
        <ul class="map-labels list-inline js-map-labels">
          <li class="checked">
            <label for="here-template-fixed">
              <img class="not-selected" src="<?= $src; ?>/dist/images/box.png" alt="">
              <img class="selected" src="<?= $src; ?>/dist/images/box-active.png" alt="">
              <input type="radio" name="here-template" value="fixed" id="here-template-fixed" checked>
              <?=__('here-maps-admin-gui-label-box', 'here-maps');?>
            </label>
          </li>
          <li>
            <label for="here-template-tooltip">
              <img class="not-selected" src="<?= $src; ?>/dist/images/tooltip.png" alt="">
              <img class="selected" src="<?= $src; ?>/dist/images/tooltip-active.png" alt="">
              <input type="radio" name="here-template" value="tooltip" id="here-template-tooltip">
              <?=__('here-maps-admin-gui-label-tooltip', 'here-maps');?>
            </label>
          </li>
          <li>
            <label for="here-template-empty">
              <img class="not-selected" src="<?= $src; ?>/dist/images/none.png" alt="">
              <img class="selected" src="<?= $src; ?>/dist/images/none-active.png" alt="">
              <input type="radio" name="here-template" value="empty" id="here-template-empty">
              <?=__('here-maps-admin-gui-label-none', 'here-maps');?>
            </label>
          </li>
        </ul>
      </div>
    </li>
  </ul>

  <?php include(dirname(__FILE__) . '/include/map-select.html'); ?>
  <div class="here-maps-show-all-pins">
    <div class="js-here-maps-show-all-pins-icon here-maps-show-all-pins-icon">
      <span class="glyphicon glyphicon-a glyphicon-map-marker"></span>
      <span class="glyphicon glyphicon-b glyphicon-map-marker"></span>
      <span class="glyphicon glyphicon-c glyphicon-map-marker"></span>
    </div>
    <div class="js-here-maps-show-all-pins-tooltip here-maps-show-all-pins-tooltip"><?=__('here-maps-admin-gui-title-show-markers', 'here-maps');?></div>
  </div>
</div>

<?php wp_footer(); ?>
<script src="<?php echo here_maps_join_file('javascripts/api.min.js'); ?>"></script>
<script src="<?php echo here_maps_join_file('javascripts/admin.min.js'); ?>"></script>
</body>
</html>
