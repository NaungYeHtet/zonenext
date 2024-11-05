<x-mail::message>
# Congratulations, {{ $userName }}!

We’re excited to inform you that your property, **{{ $propertyTitle }}**, has been successfully bought/rented by an interested party. Here’s a summary of the transaction:

## Property Information

- **Title:** {{ $propertyTitle }}
- **Location:** {{ $propertyLocation }}
- **Final Price:** {{ $propertyPrice }}

## Lead Information

- **Lead Name:** {{ $leadName }}
- **Contact Email:** {{ $leadEmail }}
- **Contact Phone:** {{ $leadPhone }}
- **Purchase Date:** {{ $purchaseDate }}

## Next Steps

1. **Prepare the Property**: Please make sure the property is ready for the new owner or renter.
2. **Contact the Buyer/Renter**: You may want to reach out directly to discuss any handover details.

If you have any questions or need further assistance, feel free to contact our support team at [{{ $contactSupport }}](mailto:{{ $contactSupport }}).

Thank you for listing with us,  
{{ config('app.name') }} Team
</x-mail::message>
