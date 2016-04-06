<?php
    return [
        "sites" => [
            /*
             * Domain where we crate sites
             */
            "domain" => "localhost"
        ],

        /*
         * For managing config
         */
        "nginx" => [
            /*
             * Connection from remote config which can create configs
             */
            "connection" => "nginx",
            "vhosts" => "/etc/nginx/vhosts/",
            "workdir" => "/var/appanel/",
            /*
             * Nginx user group
             */
            "group" => "www-data"
        ]
    ];