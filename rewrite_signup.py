import os

filepath = "resources/views/pages/auth/signup.blade.php"
with open(filepath, "r") as f:
    lines = f.readlines()

with open(filepath, "w") as f:
    # Keep lines 1-60 (0-indexed 0 to 59, but we can safely write first 60 items)
    f.writelines(lines[:60])
    
    # Write the new conditional structure
    f.write("""
@if ($portal === 'b2c')
    @include('pages.auth.partials.signup-b2c', get_defined_vars())
@else
    @include('pages.auth.partials.signup-b2b', get_defined_vars())
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const customerType = document.getElementById('customerType');
        const userType = document.getElementById('userType');
        const b2bType = document.getElementById('b2bType');
        const organizationField = document.getElementById('company_name');

        function syncType() {
            const portal = '{{ $portal }}';
            const selected = customerType ? customerType.value : 'retail';

            if (userType) userType.value = portal;

            if (portal === 'b2b') {
                if (b2bType) b2bType.value = selected || 'distributor';
                if (organizationField) organizationField.required = true;
            } else {
                if (b2bType) b2bType.value = '';
                if (organizationField) organizationField.required = false;
            }
        }

        if (customerType) {
            customerType.addEventListener('change', syncType);
            syncType();
        }

        const signupForm = document.getElementById('signupForm');
        if (signupForm) {
            signupForm.addEventListener('submit', function () {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('is-loading');
                }
            });
        }
    });
</script>
@endpush
""")
print("Successfully rewrote signup.blade.php")
