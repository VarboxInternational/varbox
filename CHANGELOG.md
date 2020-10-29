# Changelog

All notable changes to `Varbox` will be documented in this file

## 2.2.0 - 2020/10/29

### Fixed

- Multiple attribute for non-required form select elements
- Error displaying empty meta tags for model

## 2.1.0 - 2020/10/06

### Fixed

- Sorting by relation foreign keys (other than `id`)
- Duplicate fillable properties on `User` model from `varbox:install`

## Changed

- Factory trait makes use of the Laravel's `newFactory` method

## 2.0.1 - 2020/09/20

### Added

- `Varbox\Traits\HasFactory` trait
- Support for Varbox model factories inside Laravel app

## 2.0.0 - 2020/09/17

### Added

- Support for Laravel 8.x

### Changed

- PHPUnit deprecated methods
- Seeder namespaces
- Seeder directory name
- Models new directoryc

## 1.1.0 - 2020/10/06

### Fixed

- Sorting by relation foreign keys (other than `id`)
- Duplicate fillable properties on `User` model from `varbox:install`

## 1.0.0 - 2020/07/11

### Added
- Admin Panel
- Admin Crud
- Admin Menu
- Admin Forms
- Model Slugs
- Model Urls
- Model Revisions
- Draft Records
- Duplicate Records
- Filter Records
- Sort Records
- Order Records
- Preview Records
- Csv Exports
- Query Cache
- Meta Tags

**Modules & Components**

- Access Control
    - Users & Admins
    - Roles & Permissions
    - Activity Log
    - Notifications System
- Media Library
    - File Uploads
- Content Management
    - Content Pages
    - Content Blocks
    - Dynamic Menus
    - Custom Emails
- Multi Language
    - Translatable Models
    - Static Translations
    - Global Languages
- Geo Location
    - World Countries
    - World States
    - World Cities
- System Settings
    - Custom Configs
    - Dynamic Redirects
    - Application Errors
    - Full Backups

