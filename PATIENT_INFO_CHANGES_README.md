# Patient Information Booking System Changes

## Overview
This document describes the changes made to the patient information and booking system to store patient data directly in the `booking_details` table instead of using a separate `patient` table reference.

## Changes Summary

### 1. Database Changes
**File:** `booking_details_patient_fields_migration.sql`

Added the following columns to `booking_details` table:
- `patient_name` (VARCHAR(255), NULL) - Patient's full name
- `patient_age` (INTEGER, NULL) - Patient's age
- `patient_gender` (VARCHAR(50), NULL) - Patient's gender
- `patient_notes` (TEXT, NULL) - Additional notes about the patient

**Note:** These fields are ONLY populated for "Special Treatment for Stroke" services. For all other services, these fields remain NULL.

### 2. Patient Information Modal Changes
**File:** `views/modal/user_modal-patient-info.php`

**Removed:**
- Treatment start date field (no longer required)
- Database save operation (patient data is no longer saved to `patient` table immediately)

**Added:**
- Session storage caching of patient data
- Auto-loading of cached patient data when modal reopens
- Patient data is attached to the cart item for stroke services

**New Flow:**
1. User fills in patient information (name, age, gender, notes)
2. Data is cached in `sessionStorage` as `patient_cache`
3. Data is attached to the service in the cart
4. Data is sent to backend during booking submission

### 3. Regular Service Booking Modal Changes
**File:** `views/modal/user_modal-booking.php`

**Removed:**
- `patient_id` field from cart items
- `patient_id` duplicate checking logic

**Updated:**
- Simplified cart duplicate checking (removed patient_id comparison)

### 4. Checkout Modal Changes
**File:** `views/modal/user_modal-checkout.php`

**Updated:**
- `deletePatientInfoFromSession()` function now clears `patient_cache` from sessionStorage instead of making database calls
- Removed database deletion of patient records

### 5. Payment Modal Changes
**File:** `views/modal/user_modal-payment.php`

**Added:**
- Loading patient data from sessionStorage cache
- Attaching patient data to each service in the submission
- Only stroke services get patient data attached (others get NULL values)

**Updated:**
- `servicesForSubmission` now includes patient fields:
  - `patient_name`
  - `patient_age`
  - `patient_gender`
  - `patient_notes`
- Clear `patient_cache` from sessionStorage after successful booking

### 6. Booking Controller Changes
**File:** `controller/booking_contr.php`

**Removed:**
- `patient_id` field from booking table insertion
- `patient_id` handling logic

**Added:**
- Patient data extraction from service data
- Patient fields in `booking_details` insertion:
  - `patient_name`
  - `patient_age`
  - `patient_gender`
  - `patient_notes`
- Enhanced logging for patient data

**Updated:**
- `create_booking` action no longer accepts or uses `patient_id`
- Each service can have different patient data (stored in `booking_details`)

## Data Flow

### Old Flow (Before Changes):
1. User fills patient info modal → Saves to `patient` table → Gets `patient_id`
2. `patient_id` stored in sessionStorage
3. Service added to cart with `patient_id` reference
4. Booking created with `patient_id` in `booking` table
5. `booking_details` created with reference to booking

### New Flow (After Changes):
1. User fills patient info modal → Cached in sessionStorage
2. Service added to cart with cached patient data attached
3. At checkout, patient data flows through with service data
4. Booking created WITHOUT `patient_id`
5. `booking_details` created with patient data directly embedded
   - For stroke services: patient fields populated
   - For other services: patient fields are NULL

## Multiple Services Handling

When a user books multiple services:
- Each service creates a **separate row** in `booking_details`
- Each row can have **different patient data** (or NULL for non-stroke services)
- Example:
  ```
  booking_id | service_id | patient_name | patient_age | patient_gender | patient_notes
  ------------------------------------------------------------------------------------------
  123        | 5 (Stroke) | John Doe    | 65          | Male           | Recovering...
  123        | 3 (Massage)| NULL        | NULL        | NULL           | NULL
  ```

## Session Storage Cache Structure

**Key:** `patient_cache`

**Value:** JSON object
```json
{
  "full_name": "John Doe",
  "age": 65,
  "gender": "Male",
  "notes": "Patient recovering from stroke..."
}
```

**Lifecycle:**
- Created: When patient info modal is submitted
- Updated: Each time patient info modal is filled
- Cleared: After successful booking or when stroke service is removed from cart

## Database Migration

Run the following SQL file to update your database:
```bash
booking_details_patient_fields_migration.sql
```

This will add the necessary columns to the `booking_details` table.

## Backward Compatibility

**Note:** The `patient` table and `patient_id` column in `booking` table are not removed in this update for backward compatibility. However, they are no longer actively used in the new booking flow.

To fully deprecate the old system, you may:
1. Archive existing patient data
2. Drop the `patient_id` foreign key from `booking` table (optional)
3. Drop the `patient` table (optional, after archiving)

## Benefits of New Approach

1. ✅ **No treatment start date required** - Simplified user experience
2. ✅ **Cached data** - User doesn't need to re-enter patient info if modal is reopened
3. ✅ **Flexible multi-service bookings** - Different patient data per service
4. ✅ **Direct data storage** - Patient info stored with the specific service booking
5. ✅ **NULL for non-stroke services** - Clean data model, no unnecessary patient records

## Testing Checklist

- [ ] Book a stroke therapy service with patient info
- [ ] Verify patient data is cached in sessionStorage
- [ ] Reopen patient info modal and verify data is pre-filled
- [ ] Complete booking and verify patient data in `booking_details` table
- [ ] Book multiple services (stroke + non-stroke) and verify separate rows
- [ ] Book non-stroke service and verify patient fields are NULL
- [ ] Cancel booking with stroke service and verify cache is cleared
- [ ] Remove stroke service from cart and verify cache is cleared

## Files Modified

1. `views/modal/user_modal-patient-info.php` - Patient info modal
2. `views/modal/user_modal-booking.php` - Regular service booking modal
3. `views/modal/user_modal-checkout.php` - Checkout modal
4. `views/modal/user_modal-payment.php` - Payment modal
5. `controller/booking_contr.php` - Booking controller

## Files Created

1. `booking_details_patient_fields_migration.sql` - Database migration
2. `PATIENT_INFO_CHANGES_README.md` - This documentation file

---

**Date:** 2024
**Version:** 2.0
**Status:** Ready for Testing