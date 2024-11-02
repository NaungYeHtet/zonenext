<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute အားလက်ခံရန် လိုအပ်ပါသည်။',
    'accepted_if' => ':other သည် :value အဖြစ်ထည့်သွင်းထားလျှင် :attribute အားလက်ခံရင် လိုအပ်ပါသည်။',
    'active_url' => ':attribute သည် မှန်ကန်သော လင့်ခ်ဖြစ်ရပါမည်။',
    'after' => ':attribute သည် :date ပြီးနောက် ရက်စွဲအချိန်တစ်ခု ဖြစ်ရပါမည်။',
    'after_or_equal' => ':attribute သည် ရက်စွဲပြီးနောက် သို့မဟုတ် :date နှင့် တူညီရပါမည်။',
    'alpha' => ':attribute တွင် စာလုံးများသာ ပါဝင်ရပါမည်။',
    'alpha_dash' => ':attribute တွင် စာလုံးများ၊ နံပါတ်များ၊ dashes(-) များနှင့် underscores(_) များသာ ပါဝင်ရပါမည်။',
    'alpha_num' => ':attribute တွင် စာလုံးများနှင့် နံပါတ်များသာ ပါဝင်ရပါမည်။',
    'array' => ':attribute သည် array တစ်ခု ဖြစ်ရမည်။',
    'ascii' => 'The :attribute field must only contain single-byte alphanumeric characters and symbols.',
    'before' => ':attribute သည် :date မတိုင်မီ ရက်စွဲတစ်ခု ဖြစ်ရမည်။',
    'before_or_equal' => ':attribute သည် :date မတိုင်မီ သို့မဟုတ် :date နဲ့တူညီသော ရက်စွဲတစ်ခု ဖြစ်ရမည်။',
    'between' => [
        'array' => ':attribute တွင် အရေအတွက် :min နှင့် :max ကြားသာပါဝင်ရမည်။',
        'file' => ':attribute သည် အရွယ်စား :min နှင့် :max ကီလိုဘိုက်ကြားရှိရမည်။',
        'numeric' => ':attribute သည် :min နှင့် :max အကြား ဖြစ်ရမည်။',
        'string' => ':attribute သည် စာလုံးရေ :min နှင့် :max လုံးကြားတွင်သာ ရှိရမည်။',
    ],
    'boolean' => ':attribute သည် မှန် သို့မဟုတ် အမှား ဖြစ်ရမည်။',
    'can' => 'The :attribute field contains an unauthorized value.',
    'confirmed' => ':attribute အတည်ပြုချက်သည် မကိုက်ညီပါ။',
    'current_password' => 'စကားဝှက် မမှန်ပါ။',
    'date' => ':attribute သည် မှန်ကန်သောရက်စွဲဖြစ်ရပါမည်။',
    'date_equals' => ':attribute သည် :date နှင့် တူညီသော ရက်စွဲတစ်ခု ဖြစ်ရမည်။',
    'date_format' => ':attribute သည် :format ပုံစံနှင့် ကိုက်ညီရမည်။',
    'decimal' => ':attribute သည် ဒဿမနေရာ :decimal လုံးရှိရပါမည်။',
    'declined' => 'The :attribute field must be declined.',
    'declined_if' => 'The :attribute field must be declined when :other is :value.',
    'different' => ':attribute နှင့် :other သည် မတူညီရပါ။',
    'digits' => ':attribute သည် ဂဏန်းအလုံးရေ :digits လုံးဖြစ်ရမည်။',
    'digits_between' => ':attribute သည် ဂဏန်းအလုံးရေ :min နှင့် :max အတွင်းဖြစ်ရမည်။',
    'dimensions' => 'The :attribute field has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'doesnt_end_with' => 'The :attribute field must not end with one of the following: :values.',
    'doesnt_start_with' => 'The :attribute field must not start with one of the following: :values.',
    'email' => ':attribute သည် မှန်ကန်သော အီးမေးလ်လိပ်စာဖြစ်ရမည်။',
    'ends_with' => 'The :attribute field must end with one of the following: :values.',
    'enum' => 'ရွေးချယ်ထားသော :attribute သည် မမှန်ကန်ပါ။',
    'exists' => 'ရွေးချယ်ထားသော :attribute သည် မမှန်ကန်ပါ။',
    'file' => ':attribute သည် ဖိုင်တစ်ခု ဖြစ်ရပါမည်။',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'array' => ':attribute တွင် :value ခုထက် ပို၍ များရပါမည်။',
        'file' => ':attribute သည် :value kilobytes အရွယ်အစားထက် ပိုကြီးရပါမည်။',
        'numeric' => ':attribute သည် :value ထက် ပိုကြီးရပါမည်။',
        'string' => ':attribute သည် စာလုံးရေ :value ထက် ပိုများရပါမည်။',
    ],
    'gte' => [
        'array' => 'The :attribute field must have :value items or more.',
        'file' => 'The :attribute field must be greater than or equal to :value kilobytes.',
        'numeric' => 'The :attribute field must be greater than or equal to :value.',
        'string' => 'The :attribute field must be greater than or equal to :value characters.',
    ],
    'image' => ':attribute သည် ပုံတစ်ပုံဖြစ်ရပါမည်။',
    'in' => 'ရွေးချယ်ထားသော :attribute သည် မမှန်ကန်ပါ။',
    'in_array' => 'The :attribute field must exist in :other.',
    'integer' => ':attribute သည် ကိန်းပြည့်ဖြစ်ရပါမည်။',
    'ip' => 'The :attribute field must be a valid IP address.',
    'ipv4' => 'The :attribute field must be a valid IPv4 address.',
    'ipv6' => 'The :attribute field must be a valid IPv6 address.',
    'json' => 'The :attribute field must be a valid JSON string.',
    'lowercase' => ':attribute သည် စာလုံးသေးဖြစ်ရပါမည်။',
    'lt' => [
        'array' => ':attribute တွင် :value ခုထက် နည်းနေရပါမည်။',
        'file' => ':attribute သည် :value kilobytes ထက်နည်းရမည်။',
        'numeric' => ':attribute သည် :value ထက်နည်းရမည်။',
        'string' => ':attribute သည် အလုံးရေ :value ထက် နည်းနေရပါမည်။',
    ],
    'lte' => [
        'array' => 'The :attribute field must not have more than :value items.',
        'file' => 'The :attribute field must be less than or equal to :value kilobytes.',
        'numeric' => 'The :attribute field must be less than or equal to :value.',
        'string' => 'The :attribute field must be less than or equal to :value characters.',
    ],
    'mac_address' => 'The :attribute field must be a valid MAC address.',
    'max' => [
        'array' => ':attribute တွင် :max ခု ထက် မပိုရပါ။',
        'file' => ':attribute သည် :max ကီလိုဘိုက် အရွယ်အစားထက် မပိုရပါ။',
        'numeric' => ':attribute သည် :max ထက်မပိုရပါ။',
        'string' => ':attribute သည် စာလုံးရေ :max လုံး ထက်မပိုရပါ။',
    ],
    'max_digits' => ':attribute သည် ဂဏန်းအလုံးရေ :max လုံး ထက်မပိုရပါ။',
    'mimes' => ':attribute ၏ ဖိုင်အမျိုးအစားသည် :values မှ တစ်ခုခုဖြစ်ရပါမည်။',
    'mimetypes' => 'The :attribute field must be a file of type: :values.',
    'min' => [
        'array' => ':attribute တွင် အနည်းဆုံး :min ခု ပါရပါမည်။',
        'file' => ':attribute သည် အနည်းဆုံး :min ကီလိုဘိုက် အရွယ်အစား ရှိရပါမည်။',
        'numeric' => ':attribute သည် အနည်းဆုံး :min ဖြစ်ရပါမည်',
        'string' => ':attribute သည် အနည်းဆုံး စာလုံးရေ :min လုံး ရှိရပါမည်။',
        'different_char' => ':attribute သည် အနည်းဆုံး စာလုံး :minခု ကွဲပြားရပါမည်။',
    ],
    'min_digits' => 'The :attribute field must have at least :min digits.',
    'missing' => 'The :attribute field must be missing.',
    'missing_if' => 'The :attribute field must be missing when :other is :value.',
    'missing_unless' => 'The :attribute field must be missing unless :other is :value.',
    'missing_with' => 'The :attribute field must be missing when :values is present.',
    'missing_with_all' => 'The :attribute field must be missing when :values are present.',
    'multiple_of' => 'The :attribute field must be a multiple of :value.',
    'not_in' => 'ရွေးချယ်ထားသော :attribute သည် မမှန်ကန်ပါ။',
    'not_regex' => ':attribute ပုံစံသည် မမှန်ကန်ပါ။',
    'numeric' => ':attribute သည် နံပါတ်တစ်ခု ဖြစ်ရပါမည်။',
    'password' => [
        'letters' => ':attribute တွင် အနည်းဆုံး စာလုံးတစ်လုံး ပါဝင်ရပါမည်။',
        'mixed' => ':attribute တွင် အနည်းဆုံး စာလုံးအကြီးတစ်လုံးနှင့် စာလုံးသေးတစ်လုံး ပါဝင်ရပါမည်။',
        'numbers' => ':attribute တွင် အနည်းဆုံး နံပါတ်တစ်ခု ပါဝင်ရပါမည်။',
        'symbols' => ':attribute တွင် အနည်းဆုံး သင်္ကေတတစ်ခု ပါဝင်ရပါမည်။',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.',
        'same_with_current' => ':attribute သည် လက်ရှိစကားဝှက်နှင့်မတူရပါ',
    ],
    'present' => 'The :attribute field must be present.',
    'present_if' => 'The :attribute field must be present when :other is :value.',
    'present_unless' => 'The :attribute field must be present unless :other is :value.',
    'present_with' => 'The :attribute field must be present when :values is present.',
    'present_with_all' => 'The :attribute field must be present when :values are present.',
    'prohibited' => 'The :attribute field is prohibited.',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'The :attribute field prohibits :other from being present.',
    'regex' => ':attribute ၏ ပုံစံသည် မမှန်ကန်ပါ။',
    'regex' => 'The :attribute field format is invalid.',
    'required' => ':attribute အားထည့်သွင်းရန် လိုအပ်ပါသည်။',
    'required_array_keys' => 'The :attribute field must contain entries for: :values.',
    'required_if' => ':other သည် :value ဖြစ်သောအခါ :attribute အားထည့်သွင်းရန်လိုအပ်ပါသည်။',
    'required_if_accepted' => 'The :attribute field is required when :other is accepted.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without' => ':values မပါလျှင် :attribute အားထည့်သွင်းရန် လိုအပ်ပါသည်။',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute field must match :other.',
    'size' => [
        'array' => ':attribute တွင် :size ခု ပါရပါမည်။',
        'file' => ':attribute သည် :size အရွယ်အစားဖြစ်ရပါမည်။',
        'numeric' => ':attribute သည် :size ဂဏန်းဖြစ်ရပါမည်။',
        'string' => ':attribute သည် စာလုံးရေ :size လုံးဖြစ်ရပါမည်။',
    ],
    'starts_with' => ':attribute သည် :values နှင့် စရပါမည်။',
    'string' => ':attribute သည် စာကြောင်းတစ်ခု ဖြစ်ရမည်။',
    'timezone' => 'The :attribute field must be a valid timezone.',
    'unique' => ':attribute အား ရယူထားပြီးသား ဖြစ်နေပါသည်။',
    'uploaded' => 'The :attribute failed to upload.',
    'uppercase' => ':attribute သည် စာလုံးအကြီးဖြစ်ရမည်။',
    'url' => ':attribute သည် မှန်ကန်သော URL ဖြစ်ရပါမည်။',
    'ulid' => 'The :attribute field must be a valid ULID.',
    'uuid' => 'The :attribute field must be a valid UUID.',
    'telegram_channel_id' => 'တယ်လီဂရမ်ချန်နယ်အိုင်ဒီမှာ မမှန်ကန်ပါ။',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'username' => [
            'regex' => ':attribute တွင် စာလုံးအသေးများနှင့် ဂဏန်းများသာ ပါဝင်ရမည်ဖြစ်ပြီး စာလုံးဖြင့်သာအစပြုရပါမည်',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'အမည်',
        'phone' => 'ဖုန်း',
        'email' => 'အီးမေးလ်',
        'phone_number' => 'ဖုန်းနံပါတ်',
        'password' => 'စကားဝှက်',
        'old_password' => 'စကားဝှက်အဟောင်း',
        'new_password' => 'စကားဝှက်အသစ်',
        'confirm_password' => 'အတည်ပြုစကား၀ှက်',
        'interest' => 'စိတ်ဝင်စားသည့်အကြောင်းအရာ',
        'property_type' => 'အိမ်ခြံမြေအမျိုးအစား',
        'first_name' => 'ပထမအမည်',
        'last_name' => 'နောက်ဆုံးအမည်',
        'last_name' => 'နောက်ဆုံးအမည်',
        'address' => 'လိပ်စာ',
        'preferred_contact_method' => 'ဆက်သွယ်ရန်နည်းလမ်း',
        'preferred_contact_time' => 'ဆက်သွယ်ရန်အချိန်',
        'max_price' => 'အမြင့်ဆုံးတန်ကြေး',
        'square_feet' => 'စတုရန်းပေ',
        'bedrooms' => 'အိပ်ခန်းများ',
        'bathrooms' => 'ရေချိုးခန်းများ',
        'send_updates' => 'အသစ်ပေးပို့ခြင်း',
    ],

];
