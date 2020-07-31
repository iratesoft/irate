<nav class="navbar" role="navigation" aria-label="main navigation">
  <div class="navbar-brand">
    <a class="navbar-item" href="{$baseUrl}">
      <b>{$app->param('siteTitle')}</b>
    </a>

    <a role="button"
       class="navbar-burger burger"
       aria-label="menu"
       aria-expanded="false"
       data-target="navigation"
       onclick="document.querySelector('.navbar-menu').classList.toggle('is-active');"
    >
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
    </a>
  </div>

  <div id="navigation" class="navbar-menu">
    <div class="navbar-start">
      <a class="navbar-item" href="{$baseUrl}">
        Home
      </a>
    </div>
  </div>
</nav>
