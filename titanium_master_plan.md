# Titanium Master Admin - Implementation Strategy

## Goal
Create a "Super Admin" (Titanium) dashboard to manage multiple hotels, toggle features, handle subscriptions, and perform shadow logins.

## Key Features
1.  **Multi-Tenancy Foundation**:
    *   Centralized management of `hotels` table.
    *   Separate authentication guard (`titanium`) for platform admins.
2.  **Titanium Dashboard**:
    *   URL: `/titanium/dashboard`
    *   List all hotels with status (Active/Suspended).
    *   Quick actions: Login as Admin, Manage Features, Send Payment Reminder.
3.  **Feature Management (Toggles)**:
    *   DB Table: `hotel_features` (hotel_id, feature_code, is_enabled).
    *   Middleware: `CheckFeature:feature_code` to restrict routes.
    *   Blade Directive: `@feature('code') ... @endfeature` to hide UI.
4.  **Shadow Login (Impersonation)**:
    *   Allow Titanium admins to log in as a specific Hotel Admin without credentials.
5.  **Subscriptions & Payment Reminders**:
    *   Track subscription status and next payment dates.
    *   Button to trigger email reminders.

## Architecture

### Database Schema
*   `platform_admins` (id, name, email, password, role)
*   `hotel_features` (id, hotel_id, feature_code, status) - *Features: 'spa', 'laundry', 'banquet', 'crm', 'mobile_app'*
*   `subscriptions` (id, hotel_id, plan_name, amount, start_date, next_due_date, status)

### Routes (`routes/titanium.php`)
*   `GET /titanium/login`
*   `GET /titanium/dashboard`
*   `POST /titanium/hotels/{hotel}/impersonate`
*   `POST /titanium/hotels/{hotel}/features`
*   `POST /titanium/hotels/{hotel}/remind-payment`

### New Models
*   `PlatformAdmin`
*   `HotelFeature`
*   `Subscription`

## Work Phases
1.  **Setup**: Create migrations, models, and auth guard.
2.  **Dashboard**: Build the Titanium layout and hotel list.
3.  **Feature Logic**: Implement the backend logic for toggling features.
4.  **Impersonation**: Implement the shadow login.
5.  **Subscription**: Add subscription tracking and reminder emails.
