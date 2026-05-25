# Inshotel — Tanzania Hotel PMS

Hotel management system built with Laravel 12, Blade, Alpine.js, and Tailwind CSS for Tanzanian hotels. Live at [hotel.luxurywebs.com](https://hotel.luxurywebs.com).

## Features

- **3 Role Levels**: Creator (super admin), Manager, Receptionist
- **Dashboard**: Real-time guest stats, animated counters, monthly charts, today's overview
- **Booking Management**: Reservations, check-in/check-out, calendar view, public booking portal
- **Guest Management**: NIDA integration, duplicate detection, analytics, blacklist
- **Payments**: TZS currency, down payment (15%), invoice generation, payment tracking
- **Rooms**: Types, status (available/reserved/occupied/cleaning/maintenance), availability checking
- **POS System**: Room orders, walk-in sales, billing
- **Housekeeping**: Task tracking, assignment, status updates
- **Inventory**: Stock management, tracking
- **Maintenance**: Work order tracking, room maintenance scheduling
- **EFD**: Tanzania EFD receipt generation
- **Smart Keys**: Digital key management
- **CCTV**: Camera integration page
- **Sync Queue**: Offline data sync management
- **Activity Log**: Full audit trail with Spatie Activitylog

## Demo Login

| Role | Email | Password |
|------|-------|----------|
| Creator | creator@inshotel.com | creator123 |
| Manager | manager@inshotel.com | manager123 |
| Receptionist | reception@inshotel.com | reception123 |

## Installation

```bash
git clone git@github.com:abnormal-yi/hotel-system.git
cd hotel-system
cp .env.example .env
composer install
npm install
npm run build
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

## Tech Stack

- **Backend**: Laravel 12, PHP 8.2+, MySQL, Laravel Reverb
- **Frontend**: Tailwind CSS 3.4, Alpine.js, Blade, Chart.js, DataTables
- **Tools**: Vite, Laravel Pint

## License

MIT
