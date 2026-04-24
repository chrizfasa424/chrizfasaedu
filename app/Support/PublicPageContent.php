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
        $defaultPrivacyContent = <<<'HTML'
<h3>1. Who We Are</h3>
<p>We are the data controller for personal data processed through this website, admissions channels, and school operations.</p>
<h3>2. Data We Collect</h3>
<ul>
  <li>Identity and contact details submitted by parents, guardians, learners, and visitors.</li>
  <li>Communication records from forms, enquiries, and support requests.</li>
  <li>Technical data required for website security, performance, and abuse prevention.</li>
</ul>
<h3>3. Why We Process Data</h3>
<ul>
  <li>Admissions, student support, and school communication.</li>
  <li>Service delivery, security monitoring, and quality improvement.</li>
  <li>Compliance with legal and regulatory obligations.</li>
</ul>
<h3>4. Lawful Bases</h3>
<p>Where GDPR applies, we rely on lawful bases such as consent, contract, legal obligation, and legitimate interests. Where Nigeria data protection law applies, we process data under NDPA/NDPR lawful processing grounds.</p>
<h3>5. Data Sharing</h3>
<p>We may share data with trusted processors that support hosting, communications, and operations, subject to confidentiality and security safeguards.</p>
<h3>6. International Transfers</h3>
<p>If data is transferred across borders, we apply suitable safeguards to protect personal information.</p>
<h3>7. Data Retention</h3>
<p>We retain personal data only as long as necessary for educational, legal, and operational purposes.</p>
<h3>8. Security</h3>
<p>We maintain technical and organizational controls to protect personal data from unauthorized access, loss, or misuse.</p>
<h3>9. Your Rights</h3>
<p>Depending on your jurisdiction, you may request access, correction, deletion, restriction, objection, portability, and withdrawal of consent where consent applies.</p>
<h3>10. Contact and Complaints</h3>
<p>You may contact us for privacy requests and, where applicable, lodge complaints with relevant supervisory authorities including the Nigeria Data Protection Commission.</p>
HTML;
        $defaultCookiesContent = <<<'HTML'
<h3>1. What Cookies Are</h3>
<p>Cookies are small text files stored on your device to support website functionality, security, and user preferences.</p>
<h3>2. Cookies We Use</h3>
<ul>
  <li><strong>Necessary cookies</strong> for security, session handling, and essential page functions.</li>
  <li><strong>Preference cookies</strong> to remember user choices such as cookie consent.</li>
  <li><strong>Optional analytics/marketing cookies</strong> only where enabled and lawfully configured.</li>
</ul>
<h3>3. Consent Choices</h3>
<p>On first visit, users can accept or reject optional cookies. Rejecting optional cookies does not block access to the website.</p>
<h3>4. Cookie Lifespan</h3>
<p>Cookie duration depends on purpose. Consent cookies are retained for a limited period and can be reset by clearing browser storage.</p>
<h3>5. Third-Party Cookies</h3>
<p>Where third-party tools are used, those providers may set cookies under their own policies.</p>
<h3>6. Managing Cookies</h3>
<p>Users can manage or delete cookies through browser settings. Some features may be affected when cookies are disabled.</p>
<h3>7. Legal Compliance</h3>
<p>This cookie framework is designed to align with transparency, choice, and accountability principles under GDPR and Nigeria data protection standards.</p>
<h3>8. Updates</h3>
<p>We may update this policy from time to time. The latest version is always published on this page.</p>
HTML;

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
            'legal_effective_date' => '',
            'privacy_policy_title' => 'Privacy Policy',
            'privacy_policy_intro' => 'This Privacy Policy explains how we collect, use, store, and protect personal information in line with GDPR principles and Nigeria data protection obligations.',
            'privacy_policy_content' => $defaultPrivacyContent,
            'cookies_policy_title' => 'Cookies Policy',
            'cookies_policy_intro' => 'This Cookies Policy explains what cookies are, how this website uses them, and how visitors can accept or reject optional cookies.',
            'cookies_policy_content' => $defaultCookiesContent,
            'cookie_banner_title' => 'Cookie Notice',
            'cookie_banner_text' => 'We use necessary cookies to keep this website secure and functional. You can accept or reject optional cookies. Rejecting optional cookies will not block access to the website.',
            'cookie_banner_accept_text' => 'Accept Cookies',
            'cookie_banner_reject_text' => 'Reject Optional',
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
            'teachers_marquee_label' => 'Our Teachers',
            'teachers_marquee_heading' => 'Meet Our Teaching Team',
            'teachers_marquee_intro' => 'Experienced teachers guiding learners with care, discipline, and excellence.',
            'why_choose_us_banners' => [
                ['image' => null, 'title' => 'Structured Curriculum', 'description' => 'Structured curriculum aligned with national and international benchmarks.'],
                ['image' => null, 'title' => 'Safe and Inclusive Campus', 'description' => 'Safe, inclusive campus with supervised transport and health support.'],
                ['image' => null, 'title' => 'Digital Learning Culture', 'description' => 'Strong digital learning culture with real-time parent communication.'],
                ['image' => null, 'title' => 'Balanced Development', 'description' => 'Balanced focus on academics, creativity, sports, and leadership.'],
            ],
            'teachers_marquee' => [],
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
                ['title' => 'Parent Portal', 'description' => '/admin-access'],
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
            'submenu_images' => [],
            'submenu_content' => [],
            'contact_hero_image' => '',
            'faqs' => [
                [
                    'id' => 'admissions',
                    'label' => 'Admissions',
                    'items' => [
                        ['q' => 'How do I apply for admission into the school?', 'a' => 'You can apply online through our admissions portal or visit our front office in person. Complete the application form, provide all required documents, and our admissions team will contact you to schedule a screening date.'],
                        ['q' => 'What documents are required for enrollment?', 'a' => 'Required documents include: birth certificate (original + photocopy), passport-size photographs (4 copies), last school report card or terminal result, transfer letter (for students from other schools), immunisation record (for KG/Primary applicants), and a Parent/Guardian ID card.'],
                        ['q' => 'What is the minimum age for entry into each level?', 'a' => 'Our age requirements are: KG1 — 3 years+, Primary 1 — 5 years+, JSS 1 — completion of Primary 6 or age 10+, SSS 1 — JSS 3 completion with BECE or equivalent.'],
                        ['q' => 'Is there an entrance examination?', 'a' => 'Yes. All applicants from Primary 3 upwards take a short assessment covering English Language and Mathematics. KG and Primary 1 applicants undergo an informal readiness assessment.'],
                        ['q' => 'When does the school year begin and how many terms are there?', 'a' => 'The academic session runs from September to July and is divided into three terms: First Term (Sept–Dec), Second Term (Jan–Apr), and Third Term (Apr–Jul).'],
                        ['q' => 'Can a student join mid-session?', 'a' => 'Yes, we accept mid-session transfers subject to availability of space in the desired class. Contact the admissions office for availability.'],
                    ],
                ],
                [
                    'id' => 'fees',
                    'label' => 'Fees & Payment',
                    'items' => [
                        ['q' => 'How is the school fees structured?', 'a' => 'Fees are charged on a termly basis and vary by class level (KG, Primary, Junior Secondary, Senior Secondary). A detailed breakdown is provided during the enrollment process or on request from the school office.'],
                        ['q' => 'What payment channels are accepted?', 'a' => 'We accept payments via direct bank transfer to our official school account, online payment through the parent/student portal, and POS at the school bursary. Always collect an official receipt.'],
                        ['q' => 'Is it possible to pay fees in instalments?', 'a' => 'Yes, an instalment plan is available for families who apply in advance. A minimum of 60% of the term\'s fee must be paid before resumption, with the balance cleared by mid-term.'],
                        ['q' => 'Are there any scholarships or bursaries available?', 'a' => 'The school offers a limited number of merit-based and need-based bursaries. Eligibility criteria include outstanding academic performance and demonstrated financial need. Contact the admin office for details.'],
                        ['q' => 'What happens if fees are not paid by the due date?', 'a' => 'Students with outstanding fees beyond the stated deadline may be asked to remain at home until the account is settled. The school will always communicate with parents before any such action.'],
                    ],
                ],
                [
                    'id' => 'academics',
                    'label' => 'Academics & Programs',
                    'items' => [
                        ['q' => 'What curriculum does the school follow?', 'a' => 'We follow the Nigerian National Curriculum as set by the Federal Ministry of Education, enriched with STEM-integrated learning, digital literacy, and character development programs. Senior Secondary students are prepared for WAEC, NECO, and JAMB examinations.'],
                        ['q' => 'What class levels are available?', 'a' => 'We offer: Kindergarten (KG1–KG3), Primary (Primary 1–6), Junior Secondary (JSS 1–3), and Senior Secondary (SSS 1–3). Each level has dedicated teachers, learning resources, and assessment structures.'],
                        ['q' => 'Does the school offer after-school support?', 'a' => 'Yes. Our After-School Program runs from 2:30 PM to 5:00 PM and includes supervised homework sessions, reading clinics, subject support classes, and co-curricular clubs.'],
                        ['q' => 'Are there special provisions for gifted or struggling students?', 'a' => 'Yes. We offer academic enrichment for high-achieving students and remedial support for those who need extra help. Class teachers and our academic coordinator work together to develop personalised support plans.'],
                    ],
                ],
                [
                    'id' => 'school-life',
                    'label' => 'School Life',
                    'items' => [
                        ['q' => 'What are the school hours?', 'a' => 'School hours are 7:45 AM to 2:30 PM Monday through Friday. Morning assembly begins at 8:00 AM sharp. Students remaining for after-school activities are dismissed at 5:00 PM.'],
                        ['q' => 'Does the school provide transport?', 'a' => 'Yes, we operate a school bus service covering select routes. The transport service is available at an additional termly fee. Contact the admin office for the current routes and pickup schedule.'],
                        ['q' => 'Does the school have extracurricular clubs and activities?', 'a' => 'Yes! We run a wide range of clubs including: Debate Club, Science Club, Drama & Arts, Football & Athletics, Leadership Council, Music Ensemble, and Chess Club. Participation is encouraged from Primary 4 upwards.'],
                        ['q' => 'What is the school uniform policy?', 'a' => 'All students are required to wear the approved school uniform. The full uniform guide including weekday, sports, and formal event attire is available from the school shop. Uniforms must be clean, ironed, and properly worn at all times.'],
                    ],
                ],
                [
                    'id' => 'results',
                    'label' => 'Results & Exams',
                    'items' => [
                        ['q' => 'How is student performance assessed?', 'a' => 'Assessment follows the Nigerian WAEC/NECO grading system: Continuous Assessment (CA) scores are collected throughout the term, and an End-of-Term Examination contributes to the final score. The combined result determines the final grade (A1–F9).'],
                        ['q' => 'When are report cards issued?', 'a' => 'Report cards are issued at the end of each term during the closing ceremony. Parents can also view results digitally via the Parent Portal. Results are released only after all outstanding fees are cleared.'],
                        ['q' => 'What happens if a student fails a subject?', 'a' => 'Students who fail to meet promotion requirements after all three terms receive a Retention Notice. Parents are invited for a meeting to discuss support options, including holiday tutoring and a re-assessment.'],
                    ],
                ],
                [
                    'id' => 'general',
                    'label' => 'General',
                    'items' => [
                        ['q' => 'How can parents communicate with the school?', 'a' => 'Parents can reach us through the Parent Portal, our official school email and phone line, scheduled Parent-Teacher Meetings (PTMs) each term, or walk-in visits during school hours. We respond to all enquiries within 24–48 working hours.'],
                        ['q' => 'Does the school have a health/medical facility?', 'a' => 'Yes, our sick bay is staffed by a qualified school nurse during school hours. In the event of illness or injury, parents are notified immediately. Students with known medical conditions must declare this during enrollment.'],
                        ['q' => 'What is the school\'s policy on bullying and student safety?', 'a' => 'The school operates a zero-tolerance policy on bullying, harassment, and any form of violence. Incidents are investigated promptly and handled in line with our Student Code of Conduct.'],
                    ],
                ],
            ],
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

    public static function arrayToLines(array $items): string
    {
        return collect($items)
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->implode(PHP_EOL);
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
