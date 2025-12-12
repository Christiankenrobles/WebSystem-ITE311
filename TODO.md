# TODO: Implement Full Name and Username Validation in CodeIgniter 4

## Completed Tasks
- [x] Add username field to app/Views/register.php with helper text
- [x] Update app/Controllers/Auth.php store method to validate full name and username using regex patterns ^[A-Za-z0-9 ñÑ]+$ for full name and ^[A-Za-z0-9_ñÑ]+$ for username
- [x] Update app/Controllers/Users.php create method to use the same regex patterns for consistency
- [x] Add validation error messages for invalid characters

## Followup Steps
- [ ] Test the validation by attempting registration with invalid characters (e.g., special characters like @, #, etc.)
- [ ] Test with valid characters including Ñ and ñ to ensure they are accepted
- [ ] Verify that the admin user creation via Users controller also enforces the same rules
