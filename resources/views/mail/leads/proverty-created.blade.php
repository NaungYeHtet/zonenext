<x-mail::message>
# Hi {{ $userName }},

Congratulations! The property you were interested in is now available. Below are the details:

## Property Information

- **Title:** {{ $propertyTitle }} <br>
- **Location:** {{ $propertyLocation }} <br>
- **Price:** {{ $propertyPrice }} <br>
- **Type:** {{ ucfirst($propertyType) }} <br>
- **Contact Name:** {{ $contactName }} <br>
- **Contact Number:** {{ $contactNumber }} <br>
- **Contact Email:** {{ $contactEmail }} <br>

<x-mail::button :url="$url">
View Property
</x-mail::button>

## Description

{{ $propertyDescription }}

We hope this property meets all your needs. If you have any questions, feel free to reach out to us.

Thank you, <br>
{{ config('app.name') }} Team
</x-mail::message>
