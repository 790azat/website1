<?php

/**
 * Site content data: sections, authors, and programs.
 *
 * Articles are not stored here: each article is a Markdown file in
 * resources/content/articles/<slug>.md. Everything is loaded through
 * App\Content\SiteContent (see app(\App\Content\SiteContent::class)->all()).
 */

return [

    'sections' => [
        'data-intelligence' => ['title' => 'Data Intelligence', 'order' => 1],
        'business-strategy' => ['title' => 'Business Strategy', 'order' => 2],
        'digital-horizons' => ['title' => 'Digital Horizons', 'order' => 3],
        'people-impact' => ['title' => 'People & Impact', 'order' => 4],
    ],

    'authors' => [
        'emily-carter' => [
            'name' => 'Emily Carter',
            'role' => 'Investment Consultant',
            'photo' => 'emily-carter.jpg',
        ],
        'james-mitchell' => [
            'name' => 'James Mitchell',
            'role' => 'Financial Specialist',
            'photo' => 'james-mitchell.jpg',
        ],
        'michael-anderson' => [
            'name' => 'Michael Anderson',
            'role' => 'Business and Data Analyst',
            'photo' => 'michael-anderson.jpg',
        ],
        'daniel-brooks' => [
            'name' => 'Daniel Brooks',
            'role' => 'Financial Specialist',
            'photo' => 'daniel-brooks.jpg',
        ],
    ],

    'programs' => [
        [
            'slug' => 'ama-digital-marketing-certification',
            'title' => 'AMA Digital Marketing Certification: The Complete Guide to the AMA + DMI Program',
            'section' => 'people-impact',
            'date' => '2026-09-24',
            'intro' => 'AMA Digital Marketing Certification combines professional marketing education from the American Marketing Association (AMA) with digital marketing training from the Digital Marketing Institute (DMI). The program is designed to help marketers build practical digital marketing skills while earning professional credentials from both organizations.',
            'cta_label' => 'Learn More',
            'cta_url' => 'https://www.ama.org/certifications/digital-marketing-certification/',
            'hero_icon' => 'megaphone',
            'hero_gradient' => 'bg-gradient-to-br from-orange-500 to-amber-600',
            'hero_image' => 'programs/ama-digital-marketing-certification.jpg',
            'hero_tagline' => 'Everything You Need to Know: AMA Digital Marketing Certification',
            'overview_heading' => 'AMA Digital Marketing Certification DMI: The Full Guide to Digital Marketing Training and Professional Qualifications',
            'overview_intro' => 'Choosing an AMA digital marketing course through the AMA + DMI partnership provides structured training across digital marketing disciplines, including search, social media, content, paid advertising, analytics and digital strategy.',
            'features' => [
                [
                    'icon' => 'shield-check',
                    'title' => 'Professional certification',
                    'body' => 'The AMA + DMI pathway provides professional credentials that can help demonstrate knowledge of modern digital marketing practices. The Digital Marketing Pro program combines an AMA professional credential with DMI certification.',
                    'list' => null,
                    'note' => null,
                ],
                [
                    'icon' => 'academic-cap',
                    'title' => 'Comprehensive digital marketing training',
                    'body' => 'The program covers important areas of DMI digital marketing, including:',
                    'list' => [
                        'Search engine optimization',
                        'Paid search and Google Ads',
                        'Social media marketing',
                        'Content marketing',
                        'Email marketing',
                        'Google Analytics',
                        'Website optimization',
                        'Digital marketing strategy',
                        'Artificial intelligence in marketing',
                    ],
                    'note' => null,
                ],
                [
                    'icon' => 'globe-alt',
                    'title' => 'Online learning',
                    'body' => 'The program is designed for flexible online learning, allowing professionals and students to develop digital marketing knowledge without attending traditional classroom sessions.',
                    'list' => null,
                    'note' => null,
                ],
                [
                    'icon' => 'sparkles',
                    'title' => 'Two professional credentials',
                    'body' => 'The AMA + DMI Digital Marketing Pro pathway can provide both AMA PCM® Digital Marketing and DMI Certified Digital Marketing Professional credentials after the applicable program and assessment requirements are completed.',
                    'list' => null,
                    'note' => null,
                ],
            ],
            'pros' => [
                [
                    'title' => 'Broad digital marketing curriculum',
                    'description' => 'The program covers multiple areas of DMI digital marketing, allowing learners to develop knowledge across search, paid media, social media, content, analytics and strategy.',
                ],
                [
                    'title' => 'Professional credentials',
                    'description' => 'The AMA + DMI pathway combines credentials from the American Marketing Association and Digital Marketing Institute DMI.',
                ],
                [
                    'title' => 'Flexible online training',
                    'description' => 'Online, self-paced learning can make the program easier to fit around existing professional or educational commitments.',
                ],
                [
                    'title' => 'Career-focused skills',
                    'description' => 'Training covers practical digital marketing disciplines that are relevant to roles involving SEO, paid advertising, social media, content, analytics and digital strategy.',
                ],
            ],
            'extra_sections' => [
                [
                    'heading' => 'AMA Digital Marketing Certification vs. Other Programs',
                    'paragraphs' => [
                        'The AMA + DMI program is one option among professional digital marketing courses, certifications and bootcamps. When comparing programs, prospective learners can consider the curriculum, certification requirements, course format, cost, assessment process and professional credentials included.',
                        'An AMA digital marketing bootcamp, specialized DMI course, university certificate or platform-specific certification may have a different structure and focus.',
                        "For professionals researching AMA digital marketing, DMI AMA, DMI digital marketing, or online marketing qualifications, comparing the program's curriculum and credential structure can help identify the option that matches their goals.",
                    ],
                    'cta' => true,
                ],
                [
                    'heading' => 'American Marketing Association Resources',
                    'paragraphs' => [
                        "The American Marketing Association also provides professional education, marketing resources, local chapters and opportunities for students and marketing professionals. Learners researching American Marketing Association chapters or American Marketing Association student membership can explore the organization's broader professional community separately from its certification programs.",
                        'The AMA also covers specialized marketing areas, including social media and influencer marketing, making its resources relevant to professionals developing broader marketing expertise.',
                    ],
                    'cta' => false,
                ],
            ],
            'related_slug' => 'digital-marketing-courses-what-to-look-for',
        ],
        [
            'slug' => 'lsu-online-mba-in-marketing',
            'title' => 'LSU Online MBA in Marketing: The Complete Guide to Tuition, Courses and Admissions',
            'section' => 'people-impact',
            'date' => '2026-09-24',
            'intro' => 'LSU Online MBA in Marketing provides graduate-level business education with a marketing-focused curriculum through LSU Shreveport. The program combines core MBA subjects with marketing, analytics and strategic business coursework in a flexible online format designed for working professionals.',
            'cta_label' => 'See More Details',
            'cta_url' => 'https://www.lsus.edu/online-mba',
            'hero_icon' => 'briefcase',
            'hero_gradient' => 'bg-gradient-to-br from-purple-600 to-indigo-700',
            'hero_image' => 'programs/lsu-online-mba-in-marketing.jpg',
            'hero_tagline' => 'Everything You Need to Know: LSU Online MBA in Marketing',
            'overview_heading' => 'LSU Online MBA Program: The Full Guide to Tuition, Curriculum, Requirements and Admissions',
            'overview_intro' => 'Choosing an LSU online MBA program gives students the opportunity to complete an MBA online while developing knowledge in business strategy, marketing, analytics, finance and management. The LSU Shreveport program consists of 30 credit hours and can be completed in as few as 10 months.',
            'features' => [
                [
                    'icon' => 'currency-dollar',
                    'title' => 'Competitive LSU Online MBA Tuition Cost',
                    'body' => 'The current LSU online MBA tuition cost for the LSU Shreveport program is listed at $484 per credit hour, with 30 credit hours required, resulting in $14,520 in total program tuition before applicable additional costs, transfer credits or financial aid.',
                    'list' => null,
                    'note' => null,
                ],
                [
                    'icon' => 'book-open',
                    'title' => 'LSU Online MBA Curriculum',
                    'body' => 'The LSU online MBA curriculum combines core business education with marketing-focused subjects. Coursework can include areas such as:',
                    'list' => [
                        'Marketing Strategy',
                        'Data-Driven Decision Making',
                        'Advanced Marketing Analytics',
                        'International Marketing',
                        'Financial Management',
                        'Economic Analysis for Management',
                        'Organizational Behavior',
                        'Management Information Systems',
                        'Organizational Strategy and Policies',
                    ],
                    'note' => 'The curriculum is designed to provide students with broad business knowledge while developing specialized marketing capabilities.',
                ],
                [
                    'icon' => 'clock',
                    'title' => 'Flexible LSU Online MBA Program',
                    'body' => 'The LSU online MBA program is delivered online and provides access to course materials 24/7. Students do not have required login times or mandatory live lectures, allowing professionals to organize coursework around existing work and personal commitments.',
                    'list' => null,
                    'note' => null,
                ],
                [
                    'icon' => 'building-library',
                    'title' => 'Accredited Business Education',
                    'body' => 'The LSU Shreveport College of Business is accredited by AACSB International, providing an established business-school accreditation framework for the program.',
                    'list' => null,
                    'note' => null,
                ],
            ],
            'pros' => [
                [
                    'title' => 'Competitive tuition',
                    'description' => 'The LSU online MBA cost is currently listed at $14,520 in total tuition for the 30-credit-hour program, making tuition a key consideration for prospective online MBA students.',
                ],
                [
                    'title' => 'Marketing-focused curriculum',
                    'description' => 'The LSU online MBA in Marketing combines core MBA education with subjects such as marketing strategy, international marketing and advanced marketing analytics.',
                ],
                [
                    'title' => 'Flexible online format',
                    'description' => 'The LSU Online MBA Shreveport program provides 24/7 access to course materials without required live lectures or fixed login times.',
                ],
                [
                    'title' => 'Multiple MBA options',
                    'description' => 'LSU Shreveport also offers online MBA pathways in areas such as accounting and project management, giving students different options depending on their professional interests.',
                ],
            ],
            'extra_sections' => [
                [
                    'heading' => 'LSU Online MBA Tuition Cost and Financial Aid',
                    'paragraphs' => [
                        'The published LSU online MBA tuition for the LSU Shreveport program is currently $484 per credit hour. With 30 required credit hours, the published total is $14,520. The university notes that the figure does not include financial aid, transfer credit and certain additional costs.',
                        'Eligible students may have access to financial aid and other education funding options. LSU Shreveport also states that students pay as they go for the courses in which they are enrolled rather than paying the entire program tuition upfront.',
                        'For students comparing LSU online MBA cost with other online MBA programs, total tuition, fees, financial aid eligibility and transfer credits should all be considered.',
                    ],
                    'cta' => true,
                ],
                [
                    'heading' => 'LSU Online MBA Requirements and Application',
                    'paragraphs' => [
                        "The LSU online MBA requirements depend on the applicant's academic background and admission pathway.",
                        "Applicants may qualify through different routes, including a bachelor's degree meeting the university's GPA requirements, qualifying graduate education or specified professional and testing criteria. A GMAT or GRE waiver is available for eligible applicants.",
                        'The LSU online MBA application process includes the graduate application, application fee and required academic documentation such as official transcripts.',
                        'Prospective students should review the current LSU Shreveport admissions requirements before applying because admission criteria and application requirements can change.',
                    ],
                    'cta' => false,
                ],
                [
                    'heading' => 'LSU Online MBA Acceptance Rate',
                    'paragraphs' => [
                        'LSU Shreveport publishes admission requirements for its online MBA programs, but an official LSU online MBA acceptance rate is not provided on the program page. Applicants should therefore evaluate the published requirements rather than relying on third-party acceptance-rate estimates.',
                    ],
                    'cta' => true,
                ],
                [
                    'heading' => 'LSU Online MBA Accounting and Project Management',
                    'paragraphs' => [
                        "Marketing is not the only option available through LSU Shreveport's online MBA portfolio.",
                        'Students researching LSU online MBA accounting can find a separate online MBA pathway focused on accounting, while the university also offers an LSU online MBA project management option. These programs provide alternative specialization paths for students whose career goals extend beyond marketing.',
                        'This makes it useful to compare the curriculum, specialization, tuition and career relevance of each LSU online MBA program before selecting a pathway.',
                    ],
                    'cta' => true,
                ],
                [
                    'heading' => 'LSU Shreveport Online MBA Reviews and Ranking',
                    'paragraphs' => [
                        'Prospective students researching LSU Shreveport online MBA reviews may find opinions across university directories, student-review platforms and other third-party sources. Reviews can provide insight into individual student experiences, but they should be considered alongside official information about curriculum, tuition, accreditation and admission requirements.',
                        'Similarly, LSU online MBA ranking information can vary depending on the organization conducting the ranking and the methodology used. Rankings should therefore be evaluated based on the criteria used rather than treated as a universal measure of program quality.',
                        'For a more direct comparison, prospective students can examine tuition, accreditation, curriculum, flexibility, admissions requirements and specialization options.',
                    ],
                    'cta' => true,
                ],
            ],
            'related_slug' => 'online-mba-programs-what-students-should-consider',
        ],
    ],

];
