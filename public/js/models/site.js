APPanel.Models.Site = Backbone.Model.extend({
  defaults: {
    name: null,
    url: null,
    access_log: false,
    error_log: true
  },

  url: "/api/sites",

  initialize: function() {
  }
});
