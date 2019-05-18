<div id="lh-search-box"></div>

{literal}
<script type="text/javascript">
var LhDefault = {
    {/literal}
    'domain': '{$domain}',
    'pubKey': '{$pubKey}',
    'container': '{$container}',
    'fullscreen': {$fullscreen},
    'limit': {$limit},
    'categoryLimit': {$categoryLimit},
    'features': '{$features}',
    'startupQuery': '{$startupQuery}',
    'currency': '{$currency}'
    {literal}
};

window.addEventListener('load', function () {
    const demo6 = new LogsHubAutoComplete({
      domain: LhDefault.domain,
      pubKey: LhDefault.pubKey,
      container: LhDefault.container,
      classNames: {
        dataset: 'tt-dataset tt-dataset--aside',
        menu: 'tt-menu tt-menu--aside',
        suggestion: 'tt-suggestion--aside'
      },
      fullscreen: LhDefault.fullscreen,
      limit: LhDefault.limit,
      categoryLimit: LhDefault.categoryLimit,
      defaultCurrency: LhDefault.currency,
      features: LhDefault.features,
      startupQuery: LhDefault.startupQuery,
      onSubmit: function(event){
        var query = (event.currentTarget.getElementsByClassName('tt-input')[0] || {}).value;
        window.location.href = '/search?controller=search&s=' + query;
      },
      onSelect: function(event, suggestion, dataset){
        if (suggestion.url){
          window.location.href = suggestion.url;
        }
      },
      datasets: [{
        features: 'categories',
        templates: {
          suggestion: Handlebars.compile($('#template-categories-demo-6').html()),
          notFound: ''
        }
      }, {
        features: 'products',
        templates: {
          suggestion: Handlebars.compile($('#template-products-demo-6').html()),
          notFound: ''
        }
      }]
    });
    demo6.init();
}, false);
</script>

<script id="template-categories-demo-6" type="text/x-handlebars-template">
    <div class="lh-result-row">
        {{name}}
    </div>
</script>
<script id="template-products-demo-6" type="text/x-handlebars-template">
    <div class="lh-result-row">
        {{#if url_image}}
            <div class="image-container"><img class="image image--big" src="{{url_image}}" alt=""/></div>
        {{/if}}

        <div class="details details--big">
        <span class="name">{{name}}</span>

        {{#if price}}
            <span class="price">{{price}} {{currency}}</span>
        {{/if}}

        {{#each categories}}
            <span class="category">{{this}}</span>
        {{/each}}
        </div>
    </div>
</script>
<style type="text/css">
.search-widget form button[type="submit"] {
  position: static !important;
}
</style>
{/literal}
