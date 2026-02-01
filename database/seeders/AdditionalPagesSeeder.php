<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class AdditionalPagesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Gallery Page (Full)
        Page::updateOrCreate(
            ['slug' => 'gallery'],
            [
                'title' => 'Gallery',
                'is_system' => true,
                'is_active' => true,
                'meta_description' => 'View photos of our property, rooms, and amenities.',
                'content' => [
                    [
                        'type' => 'hero',
                        'data' => [
                            'badge' => 'Visual Tour',
                            'heading' => 'Experience the Beauty',
                            'subheading' => 'A glimpse into the luxury that awaits you.',
                            'image' => ''
                        ]
                    ],
                    [
                        'type' => 'gallery',
                        'data' => [
                            'title' => 'Accommodations',
                            'images' => [
                                'https://images.unsplash.com/photo-1590490360182-f33fb0d41022',
                                'https://images.unsplash.com/photo-1566665797739-1674de7a421a',
                                'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b'
                            ]
                        ]
                    ],
                    [
                        'type' => 'gallery',
                        'data' => [
                            'title' => 'Amenities & Dining',
                            'images' => [
                                'https://images.unsplash.com/photo-1544161515-4ab6ce6db874',
                                'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa',
                                'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9'
                            ]
                        ]
                    ]
                ]
            ]
        );

        // 2. Terms of Service
        Page::updateOrCreate(
            ['slug' => 'terms'],
            [
                'title' => 'Terms of Service',
                'is_system' => true,
                'is_active' => true,
                'meta_description' => 'Terms and conditions for staying at our hotel.',
                'content' => [
                    [
                        'type' => 'hero',
                        'data' => [
                            'heading' => 'Terms of Service',
                            'subheading' => 'Please read these terms carefully before booking.',
                        ]
                    ],
                    [
                        'type' => 'text_image',
                        'data' => [ // Text-only mode effectively
                            'title' => 'Booking Conditions',
                            'content' => '<p><strong>Check-in/Check-out:</strong> Check-in time is 3:00 PM and check-out time is 11:00 AM. Early check-in and late check-out are subject to availability.</p><p><strong>Cancellation Policy:</strong> Cancellations made within 48 hours of arrival will be charged the first night\'s room rate.</p>',
                            'align' => 'left'
                        ]
                    ],
                    [
                        'type' => 'faq',
                        'data' => [
                            'title' => 'Policy FAQ',
                            'items' => [
                                ['question' => 'Is smoking allowed?', 'answer' => 'Smoking is strictly prohibited in all indoor areas.'],
                                ['question' => 'Are pets allowed?', 'answer' => 'We are a pet-friendly hotel. A surcharge applies.']
                            ]
                        ]
                    ]
                ]
            ]
        );

        // 3. Privacy Policy
        Page::updateOrCreate(
            ['slug' => 'privacy'],
            [
                'title' => 'Privacy Policy',
                'is_system' => true,
                'is_active' => true,
                'meta_description' => 'How we handle your data.',
                'content' => [
                    [
                        'type' => 'text_image',
                        'data' => [
                            'title' => 'Data Collection',
                            'content' => '<p>We collect information necessary to process your booking and provide you with the best possible service. We do not sell your personal data to third parties.</p>',
                            'align' => 'left'
                        ]
                    ]
                ]
            ]
        );
    }
}
