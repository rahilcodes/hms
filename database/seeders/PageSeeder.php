<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            // ==========================
            // ABOUT US
            // ==========================
            [
                'title' => 'About Us',
                'slug' => 'about',
                'is_system' => true,
                'content' => [
                    [
                        'type' => 'hero',
                        'data' => [
                            'badge' => 'Our Legacy',
                            'heading' => 'Redefining Modern Luxury',
                            'subheading' => 'Founded in 1998, we have established ourselves as a sanctuary of refinement and impeccable service.',
                            'image' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?q=80&w=2070&auto=format&fit=crop'
                        ]
                    ],
                    [
                        'type' => 'text_image',
                        'data' => [
                            'title' => 'Our Philosophy',
                            'content' => '<p class="mb-4">We believe that true luxury lies in the details. From the thread count of our linens to the warmth of our welcome, every aspect of your stay is curated to provide an unforgettable experience.</p><p>Our dedicated team of professionals work tirelessly to ensure that your every need is met with precision and grace.</p>',
                            'image' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?q=80&w=2070&auto=format&fit=crop',
                            'align' => 'right'
                        ]
                    ],
                    [
                        'type' => 'stats',
                        'data' => [
                            'items' => [
                                ['value' => '25+', 'label' => 'Years of Excellence'],
                                ['value' => '150+', 'label' => 'Luxury Suites'],
                                ['value' => '4.9', 'label' => 'Guest Rating']
                            ]
                        ]
                    ],
                    [
                        'type' => 'features',
                        'data' => [
                            'title' => 'Why Choose Us',
                            'items' => [
                                ['title' => 'Prime Location', 'desc' => 'Situated in the heart of the city, close to all major attractions.'],
                                ['title' => 'Award-Winning Spa', 'desc' => 'Rejuvenate your senses in our world-class wellness center.'],
                                ['title' => 'Gourmet Dining', 'desc' => 'Experience culinary masterpieces at our Michelin-starred restaurant.']
                            ]
                        ]
                    ],
                    [
                        'type' => 'video',
                        'data' => [
                            'title' => 'Experience The Ambience',
                            'video' => 'https://static.videezy.com/system/resources/previews/000/043/770/original/200424_02_Nature_4k_016.mp4', // Placeholder stock video
                            'poster' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=2070&auto=format&fit=crop'
                        ]
                    ]
                ],
                'meta_description' => 'Learn about our rich history and commitment to luxury hospitality.'
            ],

            // ==========================
            // CONTACT US
            // ==========================
            [
                'title' => 'Contact Us',
                'slug' => 'contact',
                'is_system' => true,
                'content' => [
                    [
                        'type' => 'hero',
                        'data' => [
                            'badge' => '24/7 Support',
                            'heading' => 'Get in Touch',
                            'subheading' => 'Our team is here to assist you with bookings, inquiries, and special requests.',
                            'image' => 'https://images.unsplash.com/photo-1596522354195-e84ae3c98731?q=80&w=2070&auto=format&fit=crop'
                        ]
                    ],
                    [
                        'type' => 'text_image',
                        'data' => [
                            'title' => 'Visit Our Front Desk',
                            'content' => '<p><strong>Address:</strong><br>123 Luxury Boulevard, Metropolis, NY 10001</p><p class="mt-4"><strong>Phone:</strong><br>+1 (555) 123-4567</p><p class="mt-4"><strong>Email:</strong><br>concierge@luxestay.com</p>',
                            'image' => 'https://images.unsplash.com/photo-1561501900-3701fa6a0864?q=80&w=2070&auto=format&fit=crop',
                            'align' => 'left'
                        ]
                    ],
                    [
                        'type' => 'map',
                        'data' => [
                            'title' => 'Find Us On The Map',
                            'address' => '123 Luxury Boulevard, Metropolis'
                        ]
                    ],
                    [
                        'type' => 'faq',
                        'data' => [
                            'title' => 'Common Questions',
                            'items' => [
                                ['question' => 'What is check-in time?', 'answer' => 'Check-in begins at 3:00 PM.'],
                                ['question' => 'Do you offer airport transfer?', 'answer' => 'Yes, we provide luxury airport transfer services upon request.'],
                                ['question' => 'Is parking available?', 'answer' => 'Valet parking is available 24/7 for all guests.']
                            ]
                        ]
                    ]
                ],
                'meta_description' => 'Contact details, location map, and FAQs for our hotel.'
            ],

            // ==========================
            // DINING
            // ==========================
            [
                'title' => 'Dining',
                'slug' => 'dining',
                'is_system' => true,
                'content' => [
                    [
                        'type' => 'hero',
                        'data' => [
                            'badge' => 'Taste the Extraordinary',
                            'heading' => 'Culinary Excellence',
                            'subheading' => 'From casual bites to fine dining, indulge in flavors from around the world.',
                            'image' => 'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?q=80&w=2070&auto=format&fit=crop'
                        ]
                    ],
                    [
                        'type' => 'text_image',
                        'data' => [
                            'title' => 'The Azure Restaurant',
                            'content' => '<p class="mb-4">Our signature restaurant offers a blend of contemporary and traditional cuisines, prepared by world-renowned chefs using locally sourced ingredients.</p><p>Open for Breakfast, Lunch, and Dinner.</p>',
                            'image' => 'https://images.unsplash.com/photo-1550966871-3ed3c6227685?q=80&w=2070&auto=format&fit=crop',
                            'align' => 'right'
                        ]
                    ],
                    [
                        'type' => 'features',
                        'data' => [
                            'title' => 'Dining Highlights',
                            'items' => [
                                ['title' => 'Farm-to-Table', 'desc' => 'Fresh, organic ingredients from local farms.'],
                                ['title' => 'Ocean View', 'desc' => 'Dine with a breathtaking view of the coast.'],
                                ['title' => 'Private Dining', 'desc' => 'Exclusive rooms for intimate gatherings found only here.']
                            ]
                        ]
                    ],
                    [
                        'type' => 'gallery',
                        'data' => [
                            'title' => 'A Visual Feast',
                            'images' => [
                                'https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=2070&auto=format&fit=crop',
                                'https://images.unsplash.com/photo-1559339352-11d035aa65de?q=80&w=1974&auto=format&fit=crop',
                                'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?q=80&w=2070&auto=format&fit=crop',
                                'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=2070&auto=format&fit=crop'
                            ]
                        ]
                    ],
                    [
                        'type' => 'testimonials',
                        'data' => [
                            'title' => 'What Critics Say',
                            'items' => [
                                ['name' => 'James Foodie', 'location' => 'Culinary Monthly', 'text' => 'The tasting menu was an absolute revelation. A must-visit for any food lover.'],
                                ['name' => 'Sarah Taste', 'location' => 'Global Eats', 'text' => 'Impeccable service and flavors that dance on the palate. 5 stars.']
                            ]
                        ]
                    ]
                ],
                'meta_description' => 'Explore our award-winning restaurants and bars.'
            ],

            // ==========================
            // GALLERY
            // ==========================
            [
                'title' => 'Gallery',
                'slug' => 'gallery',
                'is_system' => true,
                'content' => [
                    [
                        'type' => 'hero',
                        'data' => [
                            'badge' => 'Visual Tour',
                            'heading' => 'Experience the Beauty',
                            'subheading' => 'Take a glimpse into the luxurious world that awaits you.',
                            'image' => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?q=80&w=2070&auto=format&fit=crop'
                        ]
                    ],
                    [
                        'type' => 'gallery',
                        'data' => [
                            'title' => 'Interiors & Suites',
                            'images' => [
                                'https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=2070&auto=format&fit=crop',
                                'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=2070&auto=format&fit=crop',
                                'https://images.unsplash.com/photo-1590490360182-f33fb0d41022?q=80&w=1974&auto=format&fit=crop'
                            ]
                        ]
                    ],
                    [
                        'type' => 'gallery',
                        'data' => [
                            'title' => 'Exterior & Amenities',
                            'images' => [
                                'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=2070&auto=format&fit=crop',
                                'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?q=80&w=2070&auto=format&fit=crop',
                                'https://images.unsplash.com/photo-1540541338287-41700207dee6?q=80&w=2070&auto=format&fit=crop'
                            ]
                        ]
                    ]
                ],
                'meta_description' => 'A photo gallery of our rooms, suites, and amenities.'
            ],

            // ==========================
            // PRIVACY POLICY
            // ==========================
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy',
                'is_system' => true,
                'content' => [
                    [
                        'type' => 'hero',
                        'data' => [
                            'badge' => 'Legal',
                            'heading' => 'Privacy Policy',
                            'subheading' => 'We value your privacy and are committed to protecting your personal data.',
                            'image' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?q=80&w=2070&auto=format&fit=crop'
                        ]
                    ],
                    [
                        'type' => 'text_image',
                        'data' => [
                            'title' => 'Data Collection',
                            'content' => '<p class="mb-4">We collect information to provide better services to all our users. This includes information you provide to us directly, such as when you make a reservation, and information we collect automatically from your use of our services.</p><h4>Information We Collect</h4><ul class="list-disc pl-5 mb-4"><li>Name and contact details</li><li>Payment information</li><li>Stay preferences</li></ul><p>We do not sell your personal data to third parties.</p>',
                            'align' => 'left'
                        ]
                    ]
                ],
                'meta_description' => 'Our commitment to protecting your privacy.'
            ],

            // ==========================
            // TERMS OF SERVICE
            // ==========================
            [
                'title' => 'Terms of Service',
                'slug' => 'terms',
                'is_system' => true,
                'content' => [
                    [
                        'type' => 'hero',
                        'data' => [
                            'badge' => 'Legal',
                            'heading' => 'Terms & Conditions',
                            'subheading' => 'Please read our terms and conditions carefully.',
                            'image' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=2072&auto=format&fit=crop'
                        ]
                    ],
                    [
                        'type' => 'faq',
                        'data' => [
                            'title' => 'Booking Policies',
                            'items' => [
                                ['question' => 'Cancellation Policy', 'answer' => 'Free (non-charged) cancellation is allowed up to 48 hours before the scheduled check-in time. Cancellations made within 48 hours will be charged 100% of the first night.'],
                                ['question' => 'Check-in & Check-out', 'answer' => 'Check-in is from 3:00 PM. Check-out is until 11:00 AM. Late check-out is subject to availability and may incur additional charges.'],
                                ['question' => 'Payment Methods', 'answer' => 'We accept all major credit cards including Visa, Mastercard, and Amex. Cash is accepted at the front desk upon check-in.'],
                                ['question' => 'Pet Policy', 'answer' => 'We are a pet-friendly hotel. A non-refundable cleaning fee of $50 per stay applies.']
                            ]
                        ]
                    ]
                ],
                'meta_description' => 'Terms and conditions for staying with us.'
            ],

            // ==========================
            // SPECIAL OFFERS
            // ==========================
            [
                'title' => 'Special Offers',
                'slug' => 'offers',
                'is_system' => false, // Can be deleted by user if they want
                'content' => [
                    [
                        'type' => 'hero',
                        'data' => [
                            'badge' => 'Limited Time',
                            'heading' => 'Exclusive Packages',
                            'subheading' => 'Enhance your stay with our curated special offers.',
                            'image' => 'https://images.unsplash.com/photo-1563911302283-d2bc129e7c1f?q=80&w=2070&auto=format&fit=crop'
                        ]
                    ],
                    [
                        'type' => 'features',
                        'data' => [
                            'title' => 'Current Promotions',
                            'items' => [
                                ['title' => 'Romantic Getaway', 'desc' => 'Includes champagne, chocolate-covered strawberries, and a late check-out.'],
                                ['title' => 'Spa Retreat', 'desc' => 'Enjoy a complimentary 60-minute massage and access to all wellness facilities.'],
                                ['title' => 'Family Fun', 'desc' => 'Kids eat free and receive a welcome toy pack upon arrival.']
                            ]
                        ]
                    ],
                    [
                        'type' => 'text_image',
                        'data' => [
                            'title' => 'Book Direct & Save',
                            'content' => '<p class="mb-4">Booking directly through our website ensures you get the best possible rate. Enjoy exclusive perks like priority room upgrades and flexible cancellation policies.</p>',
                            'image' => 'https://images.unsplash.com/photo-1579613832125-5d34a13ffe2a?q=80&w=2070&auto=format&fit=crop',
                            'align' => 'left'
                        ]
                    ]
                ],
                'meta_description' => 'View our latest promotions and special packages.'
            ]
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}
