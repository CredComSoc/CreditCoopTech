<!-- Template: WelcomeSubject -->

Welcome to {{ org }}

<!-- Template: WelcomeBody -->

You are receiving this email because you have requested to join {{ org }}.
Please click the following link or paste it into a browser to complete the sign up process by setting up a password:
{{FRONTEND_URL}}/reset/{{token}}

Your login details are:
Email address: {{ email }}

Best wishes,
{{ org }}


<!-- Template: PasswordSubject -->

Reset your password for {{ org }}


<!-- Template: PasswordBody -->

You have received this email because you (or someone else) has requested that the password associated with this email address at {{ org }} be reset.
Please click the following link or paste it into your browser to complete the process:
{{FRONTEND_URL}}/reset/{{token}}

If you have not requested this reset, please ignore this email and your password will remain unchanged.

<!-- Template: PasswordResetConfirmationSubject -->

Your password for {{ org }} has been updated

<!-- Template: PasswordResetConfirmationBody -->

This is a confirmation that the password for your account {{user.profile.accountName}} with {{ org }} has updated.

<!-- Template: TestSubject -->

Welcome to {{ org }}

<!-- Template: TestBody -->

Test email service.

