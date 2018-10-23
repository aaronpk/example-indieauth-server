Example IndieAuth Server
========================

This is a demonstration of what's required to build an IndieAuth server.

Please do not use this in production, it hasn't been audited.

[Selfauth](https://github.com/Inklings-io/selfauth) is a great self-hosted IndieAuth server that has gone through much more security review.

Configuration
-------------

Install dependencies

```
composer install
```

Copy `config.example.php` to `config.php` and enter your base URL and Redis configuration.

