APPanel.Router = Backbone.Router.extend({
  routes: {
    '': 'home',
    'sites': 'showSites',
    'sites/new': 'newSite',
    'sites/edit/:id': 'editSite'
  }
});
