<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>APPanel</title>

    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet'
          type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/template.css" rel="stylesheet">
    <meta name="_token" content="{!! Crypt::encrypt(csrf_token()) !!}">
</head>
<body id="app-layout">

<div id="wrapper">

    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <ul class="sidebar-nav">
            <li class="sidebar-brand">
                <a href="/">
                    APPanel
                </a>
            </li>
            @if (\Auth::check())
                <li class="text-white">
                    Добро пожаловать, {{ \Auth::user()->name }}
                </li>
                <li>
                    <a href="/#sites">Список сайтов</a>
                </li>
                <li>
                    <a href="/logout">Выход</a>
                </li>
            @else
                <li>
                    <a href="/">Вход</a>
                </li>
            @endif
        </ul>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper">
        @yield('content')
    </div>
    <!-- /#page-content-wrapper -->
    @if (\Auth::check())
        <script type="text/template" id="tpl-sites">
            <h2 class="page-header text-center">List of sites</h2>
            <p class="text-center">
                <a href="#sites/new" class="btn btn-lg btn-success">Add website</a>
            </p>
            <table class="table table-hover table-responsive sites-container">
                <tr>
                    <th>Name</th>
                    <th>Url</th>
                    <th>Action</th>
                </tr>
            </table>
        </script>
        <script type="text/template" id="tpl-site">
            <td><%- name %></td>
            <td>
                <a href="http://{{ config("panel.sites.domain") }}/<%- url %>">http://{{ config("panel.sites.domain") }}/<%- url %></a>
            </td>
            <td>
                <a href="#sites/edit/<%- id %>"><span class="glyphicon glyphicon-pencil"></span></a>
                <a href="#sites/delete/<%- id %>" class="delete-contract"><span class="glyphicon glyphicon-remove"></span></a>
            </td>
        </script>
        <script type="text/template" id="tpl-new-site">
            <h2 class="page-header text-center"><%- isNew ? 'Create' : 'Edit' %> site</h2>
            <form role="form" class="js-add-new form-horizontal contract-form">
                <div class="form-group">
                    <label class="col-sm-4 control-label">Name:</label>
                    <div class="col-sm-6">
                        <input type="text" name="name" class="form-control site-name-input" value="<%- name %>">
                        <div class="help-block">For identification</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Url:</label>
                    <div class="col-sm-6">
                        <input type="text" name="url" class="form-control site-name-input" value="<%- url %>">
                        <div class="help-block">
                            For website accessing
                            <% if (!isNew) { %>
                            <a href="http://{{ config("panel.sites.domain") }}/<%- url %>" target="_blank">
                                http://{{ config("panel.sites.domain") }}/<%- url %></a>
                        <% } else { %>
                       "/url"
                        <% } %>
                        </div>

                    </div>
                </div>

                <div class="form-group">
                    <div class="btn-group  col-sm-offset-4 col-sm-6" data-toggle="buttons">
                        <label class="btn btn-primary <%- access_log == 1 ? 'active' : '' %>">
                        <input type="checkbox" name="access_log" <%- access_log == 1 ? 'checked' : '' %> > Enable access log
                    </label>
                    <label class="btn btn-primary <%-error_log == 1 ? 'active' : '' %>">
                        <input type="checkbox" name="error_log" <%-error_log == 1 ? 'checked' : '' %> > Enable error log
                    </label>
                </div>
                </div>

                <% if (!isNew) { %>
                    <div class="form-group">
                    <label class="col-sm-4 control-label">Path:</label>
                    <div class="col-sm-6">
                        <input type="text" disabled class="form-control site-name-input" value="/www/<%- directory %>">
                              <div class="help-block">Website root directory</div>
                    </div>
                </div>
                <% } %>

                <div class="form-group">
                    <div class="col-sm-offset-4 col-sm-3">
                        <button type="submit" class="btn btn-outline btn-lg btn-block">Submit</button>
                    </div>
                    <div class="col-sm-3">
                        <a href="#sites" class="btn btn-outline btn-lg btn-block">Cancel</a>
                    </div>
                </div>
            </form>
        </script>

    @endif

</div>
<!-- /#wrapper -->

<!-- JavaScripts -->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="/bower_components/underscore/underscore-min.js"></script>
<script src="/bower_components/backbone/backbone-min.js"></script>
<script src="/bower_components/jQuery.serializeObject/dist/jquery.serializeObject.min.js"></script>

<script src="/js/app.js"></script>
<script src="/js/models/site.js"></script>
<script src="/js/collections/sites.js"></script>
<script src="/js/views/site.js"></script>
<script src="/js/views/sites.js"></script>
<script src="/js/views/siteForm.js"></script>
<script src="/js/router.js"></script>
</body>
</html>
