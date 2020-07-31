<?php

namespace Application\Assets;

/**
 * Default Asset Bundle
 */
class DefaultAssetBundle {

  const CACHE_BUST = true;

  const STYLES = [
    'https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css',
    '/assets/css/app.css'
  ];

  const SCRIPTS = [
    'https://code.jquery.com/jquery-3.4.1.min.js',
    'https://use.fontawesome.com/releases/v5.3.1/js/all.js',
    '/assets/scripts/irate.class.js',
    '/assets/scripts/app.js'
  ];

  const BUNDLE_SCRIPTS = false;
  const BUNDLE_STYLES  = false;
}
