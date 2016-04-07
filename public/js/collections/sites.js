AVPanel.Collections.Sites = Backbone.Collection.extend({
  url: "/api/sites",
  model: AVPanel.Models.Site
});
