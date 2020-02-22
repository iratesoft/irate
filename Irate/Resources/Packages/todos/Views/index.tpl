{include 'base/header.tpl'}
  <script src="{$baseUrl}/assets/scripts/todo.class.js"></script>
  <section class="hero is-primary is-bold" style="margin-bottom: 25px;">
    <div class="hero-body">
      <div class="field has-addons has-text-centered">
        <div class="control form">
          <input placeholder="Enter Task" class="input" type="text" id="todo-name">
        </div>
        <div class="control">
          <a class="button" id="todo-add">Add a task</a>
        </div>
      </div>
    </div>
  </section>

  <div class="columns is-mobile is-centered">
    <div class="column" id="todo-list">

    </div>
  </div>
{include 'base/footer.tpl'}
