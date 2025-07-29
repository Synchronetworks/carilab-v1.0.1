<script>
        flatpickr('.min-datetimepicker-time', {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i", // Format for time (24-hour format)
        time_24hr: true // Enable 24-hour format

    });

    flatpickr('.datetimepicker', {
        dateFormat: "Y-m-d", // Format for date (e.g., 2024-08-21)

    });
    flatpickr("#date_of_birth", {
            dateFormat: "Y-m-d", // Format as YYYY-MM-DD
            maxDate: "today", // Disable future dates
        });
        flatpickr("#accreditation_expiry_date", {
            dateFormat: "Y-m-d",
            minDate: "today" // Prevent past dates
        });

        flatpickr("#license_expiry_date", {
            dateFormat: "Y-m-d",
            minDate: "today"
        });
       </script>
@stack('before-scripts')
    <script src="{{ mix('js/backend.js') }}"></script>
    <script src="{{ asset('js/iqonic-script/utility.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ mix('js/intlTelInput.min.js') }}"  type="module"></script>
    <script src="{{ mix('js/utils.js') }}" type="module"></script>
    <link rel="stylesheet" href="{{ asset('css/intl-tel-input.css') }}">
    <script src="{{ asset('js/intl-tel-input.js') }}"  type="module"></script>

    <script>
        function initializeIntlTelInput(selector) {
            var input = document.querySelector(selector);
            if (input) {
                var iti = window.intlTelInput(input, {
                    initialCountry: "in", // Set initial country
                    separateDialCode: true, // Show country code separately
                    utilsScript: "{{ asset('js/utils.js') }}"
                });
    
                // Update input value properly
                function updateInputValue() {
                    let fullNumber = iti.getNumber();
                    if (iti.isValidNumber()) {
                        input.value = fullNumber; // Set formatted number only if valid
                    }
                }
    
                // Update on blur (when clicking outside the field)
                input.addEventListener('blur', updateInputValue);
    
                // Prevent number from clearing when switching fields
                input.addEventListener('change', function () {
                    if (iti.getNumber()) {
                        input.value = iti.getNumber();
                    }
                });
    
                // Handle country change correctly
                input.addEventListener("countrychange", function () {
                    let number = input.value.trim();
                    if (number) {
                        let newNumber = iti.getNumber();
                        input.value = newNumber;
                    }
                });
            }
        }
    
        // Initialize for both fields
        initializeIntlTelInput("#mobile");
        initializeIntlTelInput("#phone_number");
    </script> 
    <script>
        window.defaultCurrencySymbol = @json(\DB::table('countries')
            ->where('id', \App\Models\Setting::getSettings('default_currency'))
            ->value('symbol') ?? '$');
    
        const currencyFormat = (amount) => {
            const DEFAULT_CURRENCY = {
                no_of_decimal: @json(\App\Models\Setting::getSettings('digitafter_decimal_point') ?? 2),
                decimal_separator: '.',
                thousand_separator: ',',
                currency_position: @json(\App\Models\Setting::getSettings('currency_position') ?? 'left'),
                currency_symbol: window.defaultCurrencySymbol 
            };
    
            return formatCurrency(
                amount, 
                DEFAULT_CURRENCY.no_of_decimal, 
                DEFAULT_CURRENCY.decimal_separator, 
                DEFAULT_CURRENCY.thousand_separator, 
                DEFAULT_CURRENCY.currency_position, 
                DEFAULT_CURRENCY.currency_symbol
            );
        };
    
        window.currencyFormat = currencyFormat;
    </script>

