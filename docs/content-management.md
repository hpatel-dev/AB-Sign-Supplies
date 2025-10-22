## Content management quick start

- **Hero banner:** In the Filament admin (`/admin`), open *Operations → Company Info*. The new "Hero" tab controls the homepage headline, subhead, CTAs, and stat blocks shown in the Nuxt hero banner.
- **Sister companies:** Manage the dynamic company directory under *Operations → Companies*. Upload logos, adjust contact details, and add services using the repeater. The Nuxt `/our-companies` pages consume this API automatically.
- **Seed data:** Run `php artisan migrate:fresh --seed` to bootstrap demo content, including sample hero messaging and three sister companies with services.

These changes sync the Laravel backend with the Nuxt frontend, so marketers can refresh copy without touching code.
- **Lead inbox:** Filament now captures website contact submissions (with optional phone/product) under *Operations ? Leads* for easy follow-up.
- Hero banner now supports uploading a background image/video and choosing stat icons�configure these in Filament under Company Info.
