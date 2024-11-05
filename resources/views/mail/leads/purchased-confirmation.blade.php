<x-mail::message>
# Congratulations, {{ $userName }}!

We’re excited to confirm that you’ve successfully purchased the property you were interested in. Here’s a summary of your purchase:

## Property Information

- **Title:** {{ $propertyTitle }}
- **Location:** {{ $propertyLocation }}
- **Price:** {{ $propertyPrice }}

## Purchase Details

- **Purchase Date:** {{ $purchaseDate }}

If you have any questions or need further assistance, feel free to contact our support team at [{{ $contactSupport }}](mailto:{{ $contactSupport }}).

Thank you for choosing us,  
{{ config('app.name') }} Team
</x-mail::message>
