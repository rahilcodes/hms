<?php

return [
    // Declared room tariff per night at or below this amount attracts the lower slab.
    'room_slab_threshold' => 7500,
    'room_rate_low' => 5.0,   // % GST for tariff <= threshold
    'room_rate_high' => 18.0, // % GST for tariff > threshold

    'food_rate' => 5.0,       // restaurant / room service food
    'service_rate' => 18.0,   // other hotel services (spa, laundry, addons)
    'banquet_hall_rate' => 18.0, // hall rent, decoration, misc
    'banquet_food_rate' => 5.0,  // catering (per-plate)

    'sac' => [
        'room' => '996311',
        'food' => '996331',
        'banquet' => '996334',
        'service' => '999799',
    ],
];
