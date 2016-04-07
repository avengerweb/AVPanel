AVPanel.Models.Site = Backbone.Model.extend({
  urlRoot: '/api/sites',
  defaults: {
    name: null,
    url: null,
    access_log: 0,
    error_log: 1
  },
  initialize: function() {
  }
});
