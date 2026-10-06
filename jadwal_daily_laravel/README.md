<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Dynamic personal scheduler

The scheduler stores weekly templates, generated daily schedules, and weekly study logs in MySQL. Laravel's app timezone is set to `Asia/Makassar`, the IANA timezone covering Bali. Telegram webhooks require Telegram's secret-token header and the configured chat ID. Gemini schedule output is validated in code; unsafe or failed AI responses do not overwrite the schedule. Send `/fallback 30` to apply a deterministic manual delay to eligible future activities.

### Setup

1. Configure `.env` with MySQL (`DB_CONNECTION=mysql`, host, database, username, password), `APP_TIMEZONE=Asia/Makassar`, `GEMINI_API_KEY`, and `TELEGRAM_BOT_TOKEN`. Set a random `TELEGRAM_WEBHOOK_SECRET` (1–256 characters, letters/digits/underscore/hyphen), then run `php artisan config:clear`.
2. Run `php artisan migrate --force` and `php artisan schedule:seed-templates`.
3. Run `php artisan schedule:generate` once to create today's schedule. The dashboard and weekly agenda also create a missing schedule from that weekday's template when you view today or a future date. Subsequent schedules are generated at 00:01 Bali time.
4. In Telegram, open your bot and send `/start`. Run `php artisan telegram:discover-chat-id` and copy your private chat ID into `.env` as `TELEGRAM_CHAT_ID`, then run `php artisan config:clear`.
5. Point a public HTTPS domain to the app and set `APP_URL=https://your-host`. Run `php artisan config:clear`, then `php artisan telegram:set-webhook` to register the webhook.
6. For local development, keep `php artisan schedule:work` running. In production, run Laravel's scheduler every minute using `* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1` (or the equivalent process manager on your host).

The web dashboard includes the daily overview, weekly agenda, weekly progress log, date-based event reminders with create/read/update/delete, and integration/template status pages. Date reminders appear on their selected dashboard and agenda dates and are sent once through Telegram at their configured local time. No login is enabled, as requested. Progress entries can be added from the Progress page or by sending `/log <progress>` to the Telegram bot.

### Commands and Telegram

- `php artisan schedule:generate {--date=YYYY-MM-DD}` creates or replaces a date from its weekday template.
- `php artisan schedule:remind` sends each activity reminder once, 15 minutes before its start.
- `php artisan reminders:send-day-events` sends due date-based event reminders once through Telegram; Laravel Scheduler runs it every minute.
- `php artisan schedule:weekly-review` asks Gemini for study recommendations and sends them to Telegram on Sunday at 19:00.
- `php artisan telegram:discover-chat-id` lists IDs from messages sent to the bot before a webhook is registered; `php artisan telegram:set-webhook` registers the configured public HTTPS URL.
- `/today` prints today's schedule. Any other text requests a Gemini adjustment. `/fallback` tries a safe delay (maximum 180 minutes); if no collision-free placement exists, use `/move <activity name> HH:MM` to manually choose a valid time. Locked activities and the Work Shift cannot be moved.
- `/log <progress>` adds a progress entry for the current week. Gemini's Sunday review uses those entries and asks you to log progress if the week is empty.
- In `.env.example`, MySQL is the default. Keep your local `.env` on its actual working database until that MySQL server is listening and credentials are known.

Laravel 12 registers schedule definitions in `routes/console.php`; this repository does not use `app/Console/Kernel.php`.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
