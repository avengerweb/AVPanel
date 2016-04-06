#for {{ $site->url }}, user: {{ $site->user->email }}

location /{{ $site->url }} {
    root {{ config("panel.nginx.workdir") . $site->user->getNick() . "/www/"}};
    @if ($site->access_log)
    access_log {{ config("panel.nginx.workdir") . $site->user->getNick() }}/logs/{{ $site->url }}.access.log;
    @endif
    @if ($site->error_log)
    error_log {{ config("panel.nginx.workdir") . $site->user->getNick() }}/logs/{{ $site->url }}.error.log notice;
    @endif
}
