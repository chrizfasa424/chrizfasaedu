<?php

namespace App\Support;

use App\Models\School;

class PublicPageContent
{
    public static function forSchool(?School $school): array
    {
        if (!$school) {
            return self::defaults();
        }

        $stored = data_get($school->settings, 'public_page', []);
        if (!is_array($stored)) {
            $stored = [];
        }

        $content = array_replace_recursive(self::defaults($school), $stored);

        if (trim((string) ($content['testimonials_heading'] ?? '')) === 'What Parents and Learners Say') {
            $content['testimonials_heading'] = 'What Parents and Student Say';
        }
        if (trim((string) ($content['academics_label'] ?? '')) === 'Academics') {
            $content['academics_label'] = 'Academic Excellence';
        }
        if (trim((string) ($content['academics_intro'] ?? '')) === 'A balanced curriculum that supports strong outcomes and future readiness.') {
            $content['academics_intro'] = 'A Structured Learning Culture With Mentorship At The Center.';
        }

        return $content;
    }

    public static function defaults(?School $school = null): array
    {
        $name = $school?->name ?? 'ChrizFasa Academy';
        $motto = $school?->motto ?: 'A modern learning environment for KG, Primary, and Secondary students';

        return [
            'hero_badge_text' => 'Standard and Industry Professional School',
            'hero_title' => $motto,
            'hero_subtitle' => $name . ' combines academic excellence, character development, and modern technology to prepare learners for global opportunities.',
            'cta_primary_text' => 'Start Admission',
            'cta_secondary_text' => 'Explore Programs',
            'header_apply_text' => 'Apply',
            'header_portal_login_text' => 'Portal Login',
            'mobile_apply_text' => 'Apply Now',
            'mobile_portal_login_text' => 'Portal Login',
            'hero_slider_placeholder_text' => 'Upload hero slider images from Admin Settings to personalize this section.',
            'programs_label' => 'Programs',
            'admissions_label' => 'Admissions',
            'admissions_process_label' => 'Admissions Process',
            'academics_label' => 'Academic Excellence',
            'facilities_label' => 'Facilities',
            'about_label' => 'About Us',
            'student_life_label' => 'Student Life',
            'parents_label' => 'Parents',
            'contact_label' => 'Contact',
            'parents_portal_button_text' => 'Parent Portal Login',
            'testimonials_badge_text' => 'Testimonials',
            'testimonials_heading' => 'What Parents and Student Say',
            'testimonials_subheading' => 'We value authentic feedback from our school community. Submitted testimonials are reviewed by the admin before publication.',
            'testimonials_form_title' => 'Share Your Testimonial',
            'testimonials_form_name_label' => 'Full Name',
            'testimonials_form_name_placeholder' => 'Enter your full name',
            'testimonials_form_role_label' => 'Role or Context',
            'testimonials_form_role_placeholder' => 'Parent, student, alumni, guardian, etc.',
            'testimonials_form_rating_label' => 'Rating',
            'testimonials_form_message_label' => 'Your Testimonial',
            'testimonials_form_message_placeholder' => 'Write your experience with the school...',
            'testimonials_form_submit_text' => 'Submit Testimonial',
            'testimonials_slider_title' => 'Approved Testimonials',
            'testimonials_empty_text' => 'No testimonials have been approved yet. Be the first to share your experience.',
            'testimonials_success_text' => 'Thank you for your testimonial. It has been submitted for admin review.',
            'testimonials_error_text' => 'Unable to submit testimonial. Please try again.',
            'quick_contact_label' => 'Quick Contact',
            'contact_phone_label' => 'Phone',
            'contact_whatsapp_label' => 'WhatsApp',
            'contact_email_label' => 'Email',
            'contact_address_label' => 'Address',
            'visit_booking_button_text' => 'Visit Booking',
            'quick_apply_button_text' => 'Apply Now',
            'menu_overview_suffix' => 'Overview',
            'site_title_suffix' => 'KG, Primary and Secondary School',
            'mobile_menu_title' => 'Menu',
            'footer_quick_links_title' => 'Quick Links',
            'footer_resources_title' => 'Resources',
            'footer_contact_title' => 'Contact',
            'contact_page_browser_title' => 'Contact Us',
            'contact_page_badge_text' => 'Contact Us',
            'contact_page_heading' => 'We are here to help you',
            'contact_page_subheading' => 'Send us a message and our admissions or support team will respond as soon as possible.',
            'contact_form_title' => 'Contact Us Form',
            'contact_form_full_name_label' => 'Full Name',
            'contact_form_full_name_placeholder' => 'Enter your full name',
            'contact_form_email_label' => 'Email',
            'contact_form_email_placeholder' => 'you@example.com',
            'contact_form_phone_label' => 'Phone Number',
            'contact_form_phone_placeholder' => '+234...',
            'contact_form_subject_label' => 'Subject',
            'contact_form_subject_placeholder' => 'How can we help?',
            'contact_form_message_label' => 'Message',
            'contact_form_message_placeholder' => 'Write your message...',
            'contact_form_submit_text' => 'Send Message',
            'contact_info_title' => 'Contact Information',
            'contact_not_provided_text' => 'Not provided yet',
            'contact_more_details_title' => 'More Contact Details',
            'map_embed_title_text' => 'School map',
            'submenu_description_fallback_template' => 'The {title} area gives learners and families structured support, practical guidance, and a balanced learning experience.',
            'contact_status_unavailable_text' => 'Contact form is currently unavailable. Please try again later.',
            'contact_status_recipient_missing_text' => 'Contact recipient is not configured by admin yet.',
            'contact_status_send_error_text' => 'Message could not be sent right now. Please try again shortly.',
            'contact_status_success_text' => 'Thank you. Your message has been received. Our team will contact you shortly.',
            'submenu_highlight_one_title' => 'What Students Gain',
            'submenu_highlight_one_text' => 'Learners receive practical support, clear expectations, and measurable progress across this focus area.',
            'submenu_highlight_two_title' => 'How We Deliver',
            'submenu_highlight_two_text' => 'Delivery is structured to be balanced and moderate, so parents and students can follow the process with confidence.',
            'submenu_primary_button_text' => 'Start Admission',
            'submenu_back_button_prefix' => 'Back to',
            'submenu_more_in_prefix' => 'More In',
            'site_background_color' => '#F8FAFC',
            'primary_color' => '#2D1D5C',
            'secondary_color' => '#DFE753',
            'heading_text_color' => '#0F172A',
            'body_text_color' => '#475569',
            'surface_color' => '#FFFFFF',
            'soft_surface_color' => '#EEF6FF',
            'theme_style' => 'modern-grid',
            'header_bg_color' => '#2D1D5C',
            'footer_bg_color' => '#2D1D5C',
            'footer_separator_color' => '#DFE753',
            'metrics' => [
                ['value' => '18+', 'label' => 'Years of Service'],
                ['value' => '2,000+', 'label' => 'Active Students'],
                ['value' => '120+', 'label' => 'Certified Staff'],
                ['value' => '96%', 'label' => 'Exam Success'],
            ],
            'why_choose_us' => [
                'Structured curriculum aligned with national and international benchmarks.',
                'Safe, inclusive campus with supervised transport and health support.',
                'Strong digital learning culture with real-time parent communication.',
                'Balanced focus on academics, creativity, sports, and leadership.',
            ],
            'why_choose_us_label' => 'Why Choose Us',
            'why_choose_us_intro' => '',
            'why_choose_us_banners' => [
                ['image' => null, 'title' => 'Structured Curriculum', 'description' => 'Structured curriculum aligned with national and international benchmarks.'],
                ['image' => null, 'title' => 'Safe and Inclusive Campus', 'description' => 'Safe, inclusive campus with supervised transport and health support.'],
                ['image' => null, 'title' => 'Digital Learning Culture', 'description' => 'Strong digital learning culture with real-time parent communication.'],
                ['image' => null, 'title' => 'Balanced Development', 'description' => 'Balanced focus on academics, creativity, sports, and leadership.'],
            ],
            'programs_intro' => 'Learning pathways for every stage.',
            'programs' => [
                ['title' => 'Kindergarten (KG1-KG3)', 'description' => 'Early years foundation with play-based discovery, phonics, numeracy, and social development.'],
                ['title' => 'Primary (Primary 1-6)', 'description' => 'Strong literacy, numeracy, sciences, and values education with practical classroom engagement.'],
                ['title' => 'Junior Secondary (JSS 1-3)', 'description' => 'Broad general education that deepens critical thinking, teamwork, and academic readiness.'],
                ['title' => 'Senior Secondary (SSS 1-3)', 'description' => 'Exam-focused academics and career pathways for WAEC, NECO, and tertiary preparation.'],
                ['title' => 'After-School Programs', 'description' => 'Structured after-school support including homework clinics, clubs, and enrichment classes.'],
                ['title' => 'Holiday Camps / Summer School', 'description' => 'Seasonal programs for revision, creative projects, sports, and accelerated learning.'],
            ],
            'admissions_intro' => 'Simple and transparent enrollment process for new families.',
            'admissions' => [
                ['title' => 'How to Apply', 'description' => 'Apply online or at the admissions office and submit the required learner information.'],
                ['title' => 'Admission Requirements', 'description' => 'Birth certificate, passport photo, last report card, transfer letter (if applicable).'],
                ['title' => 'Fees and Payment', 'description' => 'Clear termly fee structure with approved payment channels and digital receipts.'],
                ['title' => 'Scholarship / Bursary', 'description' => 'Need- and merit-based support options available for eligible families.'],
                ['title' => 'School Calendar', 'description' => 'Access opening dates, mid-term breaks, exams, and special school events.'],
                ['title' => 'FAQs', 'description' => 'Answers to common questions on admission, academics, billing, and school operations.'],
            ],
            'academics_intro' => 'A Structured Learning Culture With Mentorship At The Center.',
            'academics_support_text' => 'Our school culture is built around consistent learning outcomes, high accountability, and teacher-student mentorship that develops confidence and character.',
            'academic_highlights' => [
                ['title' => 'STEM-First Curriculum', 'description' => 'Coding, robotics, and science labs integrated into junior and senior classes.'],
                ['title' => 'Student Leadership', 'description' => 'Public speaking, media, and entrepreneurship clubs with measurable outcomes.'],
            ],
            'academics_visuals' => [],
            'academics' => [
                ['title' => 'Curriculum Overview', 'description' => 'Blended national and global best-practice curriculum with measurable outcomes.'],
                ['title' => 'Subjects Offered', 'description' => 'Core and elective subjects across sciences, arts, business, and technology.'],
                ['title' => 'Timetable', 'description' => 'Well-structured daily and weekly schedules for classwork, practicals, and activities.'],
                ['title' => 'Examination and Grading', 'description' => 'Continuous assessment and exams with transparent grading and moderation.'],
                ['title' => 'Report Card Sample', 'description' => 'Parent-friendly report cards with subject scores, comments, and class performance.'],
                ['title' => 'E-Learning / Student Portal', 'description' => 'Digital learning resources, assignments, and progress tracking in one portal.'],
            ],
            'facilities_intro' => 'Safe and student-friendly learning spaces.',
            'facilities' => [
                'Classrooms and Labs',
                'Library',
                'ICT Centre',
                'Sports and Playground',
                'Hostel / Boarding (if applicable)',
                'Transport / Bus Service',
                'Clinic / Sick Bay',
            ],
            'about_intro' => 'Who we are and what we stand for.',
            'about' => [
                ['title' => 'Our Story', 'description' => 'Our journey, milestones, and commitment to quality education.'],
                ['title' => 'Vision and Mission', 'description' => 'Our educational philosophy and long-term direction.'],
                ['title' => 'Principal\'s Welcome', 'description' => 'A welcome message from school leadership to new and returning families.'],
                ['title' => 'Management Team', 'description' => 'Experienced administrators overseeing academics and school operations.'],
                ['title' => 'School Rules and Values', 'description' => 'Character, discipline, integrity, and a culture of excellence.'],
                ['title' => 'Achievements and Awards', 'description' => 'Academic, sports, and co-curricular recognitions earned by our students.'],
            ],
            'about_banners' => [
                ['image' => null, 'title' => 'Our Story', 'description' => 'Our journey, milestones, and commitment to quality education.'],
                ['image' => null, 'title' => 'Vision and Mission', 'description' => 'Our educational philosophy and long-term direction.'],
                ['image' => null, 'title' => 'Principal\'s Welcome', 'description' => 'A welcome message from school leadership to new and returning families.'],
                ['image' => null, 'title' => 'Management Team', 'description' => 'Experienced administrators overseeing academics and school operations.'],
                ['image' => null, 'title' => 'School Rules and Values', 'description' => 'Character, discipline, integrity, and a culture of excellence.'],
                ['image' => null, 'title' => 'Achievements and Awards', 'description' => 'Academic, sports, and co-curricular recognitions earned by our students.'],
            ],
            'student_life_intro' => 'A vibrant school experience beyond the classroom.',
            'student_life' => [
                ['title' => 'Clubs and Societies', 'description' => 'Creative, academic, leadership, and service clubs for all interests.'],
                ['title' => 'Events and Gallery', 'description' => 'Showcasing school activities, achievements, and memorable moments.'],
                ['title' => 'Uniform Guide', 'description' => 'Approved uniform standards for classes, events, and sports activities.'],
                ['title' => 'School Calendar', 'description' => 'Key student activities, inter-house sports, and co-curricular timelines.'],
                ['title' => 'Student Code of Conduct', 'description' => 'Behavioral expectations that promote safety and respectful learning.'],
            ],
            'parents_intro' => 'Support tools and policies for parents and guardians.',
            'parents' => [
                ['title' => 'Parent Portal Login', 'description' => 'Access learner performance, attendance, and announcements online.'],
                ['title' => 'Fees Payment', 'description' => 'Secure payment options with transaction records and downloadable receipts.'],
                ['title' => 'PTA Information', 'description' => 'Meeting dates, executive contacts, and parent engagement initiatives.'],
                ['title' => 'Communication Policy', 'description' => 'Official channels for updates, queries, and escalation procedures.'],
                ['title' => 'Pick-up and Safety Procedures', 'description' => 'Arrival, dismissal, and child protection protocols for families.'],
            ],
            'parents_banners' => [
                ['image' => null, 'title' => 'Parent Portal Login', 'description' => 'Access learner performance, attendance, and announcements online.'],
                ['image' => null, 'title' => 'Fees Payment', 'description' => 'Secure payment options with transaction records and downloadable receipts.'],
                ['image' => null, 'title' => 'PTA Information', 'description' => 'Meeting dates, executive contacts, and parent engagement initiatives.'],
                ['image' => null, 'title' => 'Communication Policy', 'description' => 'Official channels for updates, queries, and escalation procedures.'],
                ['image' => null, 'title' => 'Pick-up and Safety Procedures', 'description' => 'Arrival, dismissal, and child protection protocols for families.'],
            ],
            'contact_intro' => 'Reach us through our official channels.',
            'contact_items' => [
                ['title' => 'Location and Map', 'description' => $school?->address ?? 'School campus location details are available at the front office.'],
                ['title' => 'Phone / WhatsApp', 'description' => $school?->phone ?? 'Contact line will be provided by the school administration.'],
                ['title' => 'Email', 'description' => $school?->email ?? 'Official school email address.'],
                ['title' => 'Visit Booking', 'description' => 'Schedule a campus visit through admissions.'],
            ],
            'whatsapp' => '',
            'visit_booking_url' => '',
            'map_embed_url' => '',
            'admission_steps' => [
                'Submit online application and student details.',
                'Receive screening date and required documentation list.',
                'Attend assessment and parent interaction session.',
                'Confirm enrollment and complete onboarding.',
            ],
            'admission_session_text' => 'Apply for Current Session',
            'footer_logo' => '',
            'footer_description' => $name . ' is committed to academic excellence, character development, and future-ready learning for every child.',
            'footer_contact_address' => $school?->address ?? '',
            'footer_contact_phone' => $school?->phone ?? '',
            'footer_contact_email' => $school?->email ?? '',
            'footer_quick_links' => [
                ['title' => 'Programs', 'description' => '/#programs'],
                ['title' => 'Admissions', 'description' => '/#admissions'],
                ['title' => 'Academics', 'description' => '/#academics'],
                ['title' => 'Contact Us', 'description' => '/contact'],
            ],
            'footer_resources' => [
                ['title' => 'Apply Online', 'description' => '/apply'],
                ['title' => 'Parent Portal', 'description' => '/login'],
                ['title' => 'Student Life', 'description' => '/#student-life'],
                ['title' => 'About School', 'description' => '/#about'],
            ],
            'footer_social_links' => [
                ['title' => 'Facebook', 'description' => ''],
                ['title' => 'Instagram', 'description' => ''],
                ['title' => 'YouTube', 'description' => ''],
            ],
            'footer_note' => 'All rights reserved.',
            'hero_slides' => [],
        ];
    }

    public static function linesToArray(?string $text): array
    {
        if (!$text) {
            return [];
        }

        return collect(preg_split('/\r\n|\r|\n/', $text))
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->values()
            ->all();
    }

    public static function linesToItems(?string $text): array
    {
        if (!$text) {
            return [];
        }

        return collect(preg_split('/\r\n|\r|\n/', $text))
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->map(function (string $line) {
                $parts = array_map('trim', explode('|', $line, 2));

                return [
                    'title' => $parts[0] ?? '',
                    'description' => $parts[1] ?? '',
                ];
            })
            ->filter(fn (array $item) => $item['title'] !== '' || $item['description'] !== '')
            ->values()
            ->all();
    }

    public static function itemsToLines(array $items): string
    {
        return collect($items)
            ->map(function ($item) {
                $title = trim((string) data_get($item, 'title', ''));
                $description = trim((string) data_get($item, 'description', ''));

                if ($title === '' && $description === '') {
                    return null;
                }

                return $title . ' | ' . $description;
            })
            ->filter()
            ->implode(PHP_EOL);
    }
}


