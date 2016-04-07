/**
 * Created by AvengerWeb on 27.03.16.
 */
window.AVPanel = {
    Models: {},
    Collections: {},
    Views: {},

    start: function() {
        var sites = new AVPanel.Collections.Sites(),
            router = new AVPanel.Router();

        router.on('route:home', function() {
            router.navigate('sites', {
                trigger: true,
                replace: true
            });
        });

        router.on('route:showSites', function() {

            var sitesView = new AVPanel.Views.Sites({
                collection: sites
            });
            $('#page-content-wrapper').html(sitesView.render().$el);
        });

        router.on('route:newSite', function() {
            var newSiteForm = new AVPanel.Views.SiteForm({
                model: new AVPanel.Models.Site()
            });

            newSiteForm.on('form:submitted', function(attrs) {
                AVPanel.errors.clear(newSiteForm.$el);
                this.model.save(attrs, {
                    success: function (response) {
                        sites.add(response);
                        router.navigate('sites', true);
                    },
                    error: function (model, response) {
                        AVPanel.errors.handle(newSiteForm.$el, response.responseJSON);
                    }
                });
            });

            $('#page-content-wrapper').html(newSiteForm.render().$el);
        });

        router.on('route:editSite', function(id) {
            var site = sites.get(id),
                editSiteForm;

            if (site) {
                editSiteForm = new AVPanel.Views.SiteForm({
                    model: site
                });

                editSiteForm.on('form:submitted', function(attrs) {
                    site.set(attrs);
                    AVPanel.errors.clear(editSiteForm.$el);
                    this.model.save(attrs, {
                        success: function () {
                            router.navigate('sites', true);
                        },
                        error: function (model, response) {
                            AVPanel.errors.handle(editSiteForm.$el, response.responseJSON);
                        }
                    });

                });

                $('#page-content-wrapper').html(editSiteForm.render().$el);
            } else {
                router.navigate('sites', true);
            }
        });

        sites.fetch({success: function () {
            Backbone.history.start();
        }});


    },

    errors: {
        handle: function (form, errors) {
            for (var errKey in errors) {
                var error = errors[errKey][0];
                var input = form.find("input[name="+errKey+"]");
                // if (input.parent().hasClass("btn"))
                //     input = input.closest(".btn-group");
                input.tooltip({trigger:"click", title:error, hide:0}).tooltip("show").closest(".form-group").addClass("has-error");
            }
        },
        clear: function (form) {
            form.find(".has-error").removeClass("has-error").find("input").tooltip("hide");
        }
    }
};

$(document).ready(function() {
    var csrftoken = $('meta[name=_token]').attr('content');
    $.ajaxSetup({
        beforeSend: function (e, n) {
            /^(GET|HEAD|OPTIONS|TRACE)$/i.test(n.type) || e.setRequestHeader("X-XSRF-TOKEN", csrftoken)
        }
    });

    $(document).on("hidden.bs.tooltip", "input", function(){
        $(this).tooltip("destroy");
    });

    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });

    AVPanel.start();

});