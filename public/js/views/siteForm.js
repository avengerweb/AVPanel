APPanel.Views.SiteForm = Backbone.View.extend({
  template: _.template($('#tpl-new-site').html()),

  events: {
    'submit .js-add-new': 'onFormSubmit'
  },

  render: function() {
    var html = this.template(_.extend(this.model.toJSON(), {
      isNew: this.model.isNew()
    }));
    this.$el.append(html);
    return this;
  },

  onFormSubmit: function(e) {
    e.preventDefault();

    this.trigger('form:submitted', this.$(".js-add-new").serializeObject());
  }
});
