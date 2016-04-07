AVPanel.Views.Sites = Backbone.View.extend({
  template: _.template($('#tpl-sites').html()),

  renderOne: function(site) {
    var itemView = new AVPanel.Views.Site({model: site});
    this.$('.sites-container').append(itemView.render().$el);
  },

  render: function() {
    var html = this.template();
    this.$el.html(html);

    this.collection.each(this.renderOne, this);

    return this;
  }
});
