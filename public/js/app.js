/**
 * Created by AvengerWeb on 27.03.16.
 */
window.APPanel = {
    Models: {},
    Collections: {},
    Views: {},

    start: function() {
        var sites = new APPanel.Collections.Sites(),
            router = new APPanel.Router();

        router.on('route:home', function() {
            router.navigate('sites', {
                trigger: true,
                replace: true
            });
        });

        router.on('route:showSites', function() {

            var sitesView = new APPanel.Views.Sites({
                collection: sites
            });

            sites.fetch({success: function () {
                $('#page-content-wrapper').html(sitesView.render().$el);
            }});    


        });

        router.on('route:newSite', function() {
            var newSiteForm = new APPanel.Views.SiteForm({
                model: new APPanel.Models.Site()
            });

            newSiteForm.on('form:submitted', function(attrs) {
                console.log(attrs);
                APPanel.errors.clear(newSiteForm.$el);
                this.model.save(attrs, {
                    success: function () {
                        router.navigate('sites', true);
                    },
                    error: function (model, response) {
                        APPanel.errors.handle(newSiteForm.$el, response.responseJSON);
                    }
                });
                // attrs.id = sites.isEmpty() ? 1 : (_.max(sites.pluck('id')) + 1);
                // sites.add(attrs);
                // router.navigate('sites', true);
            });

            $('#page-content-wrapper').html(newSiteForm.render().$el);
        });

        router.on('route:editSite', function(id) {
            var site = sites.get(id),
                editSiteForm;

            if (site) {
                editSiteForm = new APPanel.Views.SiteForm({
                    model: site
                });

                editSiteForm.on('form:submitted', function(attrs) {
                    site.set(attrs);
                    router.navigate('sites', true);
                });

                $('#page-content-wrapper').html(editSiteForm.render().$el);
            } else {
                router.navigate('sites', true);
            }
        });

        Backbone.history.start();
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

    APPanel.start();

});