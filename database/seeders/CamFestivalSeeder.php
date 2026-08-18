<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parish;
use App\Models\Category;
use App\Models\ScheduleItem;

class CamFestivalSeeder extends Seeder
{
    public function run(): void
    {
        $festivalTheme = 'A YEAR OF WORKING TOGETHER IN DEEPER PASTORAL CARE';

        // -------------------------------------------------------------
        // 1. SEED PARISHES & DEANERIES (14 Parishes across 3 Deaneries)
        // -------------------------------------------------------------
        $parishesData = [
            // Livingstone Deanery (10 parishes)
            [
                'name'                 => 'St. Peter the Apostle Parish',
                'code'                 => 'SPA',
                'deanery'              => 'Livingstone Deanery',
                'patron_matron_name'   => 'Airport Parish Youth Patron',
                'patron_contact'       => '+260974112233',
                'camp_contingent_count' => 25,
                'camp_checked_in'      => false,
            ],
            [
                'name'                 => 'Our Lady of Angels Parish',
                'code'                 => 'OLA',
                'deanery'              => 'Livingstone Deanery',
                'patron_matron_name'   => 'OLA Youth Matron',
                'patron_contact'       => '+260975667788',
                'camp_contingent_count' => 25,
                'camp_checked_in'      => false,
            ],
            [
                'name'                 => 'St. Francis of Assisi Parish',
                'code'                 => 'SFA',
                'deanery'              => 'Livingstone Deanery',
                'patron_matron_name'   => 'St. Francis Youth Patron',
                'patron_contact'       => '+260972334455',
                'camp_contingent_count' => 27,
                'camp_checked_in'      => true,
            ],
            [
                'name'                 => 'St. Theresa Cathedral Parish',
                'code'                 => 'STC',
                'deanery'              => 'Livingstone Deanery',
                'patron_matron_name'   => 'Cathedral Youth Leader / Patron',
                'patron_contact'       => '+260970143065',
                'camp_contingent_count' => 30,
                'camp_checked_in'      => true,
            ],
            [
                'name'                 => "St. Paul's Parish",
                'code'                 => 'SPP',
                'deanery'              => 'Livingstone Deanery',
                'patron_matron_name'   => 'Ngwenya Youth Patron',
                'patron_contact'       => '+260979445566',
                'camp_contingent_count' => 25,
                'camp_checked_in'      => false,
            ],
            [
                'name'                 => 'St. Joseph the Worker Parish',
                'code'                 => 'SJW',
                'deanery'              => 'Livingstone Deanery',
                'patron_matron_name'   => 'Mukuni Youth Patron',
                'patron_contact'       => '+260977881122',
                'camp_contingent_count' => 28,
                'camp_checked_in'      => true,
            ],
            [
                'name'                 => 'Christ the King Parish',
                'code'                 => 'CTK',
                'deanery'              => 'Livingstone Deanery',
                'patron_matron_name'   => 'Christ the King Youth Patron',
                'patron_contact'       => '+260976554433',
                'camp_contingent_count' => 28,
                'camp_checked_in'      => true,
            ],
            [
                'name'                 => 'Maria Regina Parish',
                'code'                 => 'MRP',
                'deanery'              => 'Livingstone Deanery',
                'patron_matron_name'   => 'Maria Regina Youth Matron',
                'patron_contact'       => '+260978990011',
                'camp_contingent_count' => 26,
                'camp_checked_in'      => true,
            ],
            [
                'name'                 => 'St. Stephen Parish',
                'code'                 => 'SSP',
                'deanery'              => 'Livingstone Deanery',
                'patron_matron_name'   => 'St. Stephen Youth Patron',
                'patron_contact'       => '+260971223344',
                'camp_contingent_count' => 25,
                'camp_checked_in'      => false,
            ],

            // Sesheke Deanery (3 parishes)
            [
                'name'                 => 'St. Fidelis Parish',
                'code'                 => 'SFD',
                'deanery'              => 'Sesheke Deanery',
                'patron_matron_name'   => 'Sichili Youth Patron',
                'patron_contact'       => '+260971889900',
                'camp_contingent_count' => 25,
                'camp_checked_in'      => false,
            ],
            [
                'name'                 => 'St. Kizito Parish',
                'code'                 => 'SKP',
                'deanery'              => 'Sesheke Deanery',
                'patron_matron_name'   => 'Sesheke Youth Patron',
                'patron_contact'       => '+260973778899',
                'camp_contingent_count' => 30,
                'camp_checked_in'      => true,
            ],
            [
                'name'                 => 'St. Paul Parish',
                'code'                 => 'SPN',
                'deanery'              => 'Sesheke Deanery',
                'patron_matron_name'   => 'Nawinda Youth Matron',
                'patron_contact'       => '+260976112244',
                'camp_contingent_count' => 24,
                'camp_checked_in'      => false,
            ],

            // Sioma Deanery (2 parishes)
            [
                'name'                 => 'St. Joseph Parish',
                'code'                 => 'SJP',
                'deanery'              => 'Sioma Deanery',
                'patron_matron_name'   => 'Lusu Youth Matron',
                'patron_contact'       => '+260977332211',
                'camp_contingent_count' => 25,
                'camp_checked_in'      => false,
            ],
            [
                'name'                 => 'St. Anthony Parish',
                'code'                 => 'SAP',
                'deanery'              => 'Sioma Deanery',
                'patron_matron_name'   => 'Sioma Youth Patron',
                'patron_contact'       => '+260972667788',
                'camp_contingent_count' => 26,
                'camp_checked_in'      => true,
            ],
        ];

        $parishModels = [];
        foreach ($parishesData as $pData) {
            $parishModels[$pData['name']] = Parish::create($pData);
        }

        // -------------------------------------------------------------
        // 2. SEED CATEGORIES WITH DETAILED JUDGING SHEETS & CRITERIA
        // -------------------------------------------------------------
        $categoriesData = [
            [
                'name' => 'Choir Music (Melody)',
                'slug' => 'choir',
                'type' => 'stage_performance',
                'theme' => $festivalTheme,
                'description' => 'Choral presentation of 4 songs: Kyrie, Gloria, Thanksgiving, and Social Song.',
                'allocated_minutes' => 30,
                'prep_minutes' => 5,
                'max_raw_score' => 100,
                'rules' => [
                    'songs_required' => ['Social Song', 'Kyrie', 'Gloria', 'Thanksgiving'],
                    'participant_limit' => 'Unlimited number of participants on stage',
                    'languages_allowed' => ['English', 'Lozi', 'Tonga'],
                    'time_limit_minutes' => 30,
                    'prep_time_minutes' => 5,
                    'time_penalties' => [
                        'Up to 1 minute over' => '-2 marks',
                        '1 to 3 minutes over' => '-5 marks',
                        '3 to 5 minutes over' => '-10 marks',
                        'More than 5 minutes over' => '-15 marks',
                    ],
                ],
                'judging_criteria' => [
                    [
                        'no' => 1,
                        'criterion' => 'Entry and Exit',
                        'description' => 'The manner in which the choir enters and exits the stage; confidence, orderliness and organization.',
                        'possible_score' => 5,
                    ],
                    [
                        'no' => 2,
                        'criterion' => 'Stage Discipline / Craft',
                        'description' => 'Behavior on stage, positioning, posture, attentiveness and response to the conductor throughout the performance.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 3,
                        'criterion' => 'Authenticity',
                        'description' => 'Suitability of songs to the category, Catholic values, CAM Festival requirements and festival theme.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 4,
                        'criterion' => 'Originality',
                        'description' => 'Creativity in arrangement, presentation and interpretation of the selected songs.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 5,
                        'criterion' => 'Balance / Voice Control',
                        'description' => 'Harmony and proportion between different voice parts; control of volume, dynamics and breathing.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 6,
                        'criterion' => 'Vocal Quality and Blend',
                        'description' => 'Richness, clarity, sweetness of tone, pitch accuracy and the unified blending of voices.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 7,
                        'criterion' => 'Harmony and Part Singing',
                        'description' => 'Accuracy and stability of part singing (SATB), chord execution and musical coordination.',
                        'possible_score' => 15,
                    ],
                    [
                        'no' => 8,
                        'criterion' => 'Diction and Pronunciation',
                        'description' => 'Clarity of words, enunciation, articulation and audibility of lyrics.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 9,
                        'criterion' => 'Attire and Cultural Expression',
                        'description' => 'Appropriateness, neatness and decency of attire; expression reflecting Catholic/cultural identity.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 10,
                        'criterion' => "Conductor's Technique / Effectiveness",
                        'description' => 'Clarity of beat, dynamic leadership, communication with the choir and overall musical interpretation.',
                        'possible_score' => 10,
                    ],
                ],
            ],

            [
                'name' => 'Self-Composed Song',
                'slug' => 'self-composed-song',
                'type' => 'stage_performance',
                'theme' => $festivalTheme,
                'description' => 'Original musical composition reflecting the CAM festival theme in traditional attire.',
                'allocated_minutes' => 10,
                'prep_minutes' => 5,
                'max_raw_score' => 100,
                'rules' => [
                    'participant_limit' => 'Unlimited participants on stage',
                    'language' => 'Any language of your choice',
                    'dress_code' => "Dress code must match the traditional attire of the song's language",
                    'theme_alignment' => 'Must align with festival theme',
                    'time_limit_minutes' => 10,
                    'prep_time_minutes' => 5,
                    'time_penalties' => [
                        'Up to 1 minute over' => '-2 marks',
                        '1 to 3 minutes over' => '-5 marks',
                        '3 to 5 minutes over' => '-10 marks',
                        'More than 5 minutes over' => '-15 marks',
                    ],
                ],
                'judging_criteria' => [
                    [
                        'no' => 1,
                        'criterion' => 'Entry and Exit',
                        'description' => 'Creativity, organization, confidence and use of cultural elements during stage entry and exit.',
                        'possible_score' => 5,
                    ],
                    [
                        'no' => 2,
                        'criterion' => 'Theme Relevance',
                        'description' => 'Alignment of the composition with the CAM Festival theme.',
                        'possible_score' => 15,
                    ],
                    [
                        'no' => 3,
                        'criterion' => 'Original Composition',
                        'description' => 'Creativity and originality of both lyrics and melody.',
                        'possible_score' => 20,
                    ],
                    [
                        'no' => 4,
                        'criterion' => 'Message Content',
                        'description' => 'Clarity, depth, relevance and effectiveness of the message communicated.',
                        'possible_score' => 15,
                    ],
                    [
                        'no' => 5,
                        'criterion' => 'Vocal Performance',
                        'description' => 'Voice quality, projection, balance and control.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 6,
                        'criterion' => 'Harmony and Arrangement',
                        'description' => 'Quality of harmonization, coordination and musical structure.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 7,
                        'criterion' => 'Diction and Pronunciation',
                        'description' => 'Clarity of words, articulation and audibility of the message.',
                        'possible_score' => 5,
                    ],
                    [
                        'no' => 8,
                        'criterion' => 'Attire and Cultural Expression',
                        'description' => 'Appropriateness of traditional attire and reflection of the language/culture represented.',
                        'possible_score' => 5,
                    ],
                    [
                        'no' => 9,
                        'criterion' => 'Stage Presentation',
                        'description' => 'Confidence, coordination, discipline and audience engagement.',
                        'possible_score' => 5,
                    ],
                    [
                        'no' => 10,
                        'criterion' => 'Overall Impression',
                        'description' => 'Excellence and impact of the entire presentation.',
                        'possible_score' => 10,
                    ],
                ],
            ],

            [
                'name' => 'Poetry',
                'slug' => 'poetry',
                'type' => 'stage_performance',
                'theme' => $festivalTheme,
                'description' => 'Poetic stage performance in line with the festival theme with max 6 participants.',
                'allocated_minutes' => 15,
                'prep_minutes' => 5,
                'max_raw_score' => 100,
                'rules' => [
                    'participant_limit' => 'Maximum 6 participants on stage',
                    'theme_alignment' => 'In line with the festival theme',
                    'time_limit_minutes' => 15,
                    'prep_time_minutes' => 5,
                    'time_penalties' => [
                        'Up to 1 minute over' => '-2 marks',
                        '1 to 3 minutes over' => '-5 marks',
                        '3 to 5 minutes over' => '-10 marks',
                        'More than 5 minutes over' => '-15 marks',
                    ],
                ],
                'judging_criteria' => [
                    [
                        'no' => 1,
                        'criterion' => 'Movement (A. Performance)',
                        'description' => 'Gesture, use of body, rhythm, facial expression, choreography and use of space.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 2,
                        'criterion' => 'Teamwork (A. Performance)',
                        'description' => 'Coordination, unity of purpose, interaction and synchronization among performers.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 3,
                        'criterion' => 'Individual Performance (A. Performance)',
                        'description' => 'Expression, interpretation, creativity, confidence, imagination and delivery.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 4,
                        'criterion' => 'Use of Props or Mime (B. Production)',
                        'description' => 'Effective and appropriate use of props, mime and visual aids where applicable.',
                        'possible_score' => 5,
                    ],
                    [
                        'no' => 5,
                        'criterion' => 'Understanding of Theme (B. Production)',
                        'description' => 'Relevance to the festival theme and effectiveness in communicating the message.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 6,
                        'criterion' => 'Suitability of Costume (B. Production)',
                        'description' => 'Appropriateness of costume, appearance and presentation.',
                        'possible_score' => 5,
                    ],
                    [
                        'no' => 7,
                        'criterion' => 'Voice Control (C. Voice)',
                        'description' => 'Audibility, projection, rhythm, pace and effective voice modulation.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 8,
                        'criterion' => 'Articulation (C. Voice)',
                        'description' => 'Clarity of speech, pronunciation and diction.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 9,
                        'criterion' => 'Interpretation (C. Voice)',
                        'description' => 'Effective use of tone, emphasis, pauses and vocal variation to enhance meaning.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 10,
                        'criterion' => 'Suitability (D. Choice of Poem)',
                        'description' => 'Suitability for the festival theme, audience and age group.',
                        'possible_score' => 5,
                    ],
                    [
                        'no' => 11,
                        'criterion' => 'Overall Production (E. Overall Impression)',
                        'description' => 'The production as a whole, aesthetic appreciation, creativity and audience impact.',
                        'possible_score' => 15,
                    ],
                ],
            ],

            [
                'name' => 'Traditional Dance',
                'slug' => 'traditional-dance',
                'type' => 'stage_performance',
                'theme' => $festivalTheme,
                'description' => '3 different dances from 3 different provinces of Zambia. Maximum 15 participants on stage.',
                'allocated_minutes' => 20,
                'prep_minutes' => 5,
                'max_raw_score' => 100,
                'rules' => [
                    'dances_required' => '3 Different Dances from 3 different provinces of Zambia',
                    'participant_limit' => '15 maximum participants on stage',
                    'prohibitions' => 'No masquerade (Vinyau), No hiring of outside dancers',
                    'time_limit_minutes' => 20,
                    'prep_time_minutes' => 5,
                    'time_penalties' => [
                        'Up to 1 minute over' => '-2 marks',
                        '1 to 3 minutes over' => '-5 marks',
                        '3 to 5 minutes over' => '-10 marks',
                        'More than 5 minutes over' => '-15 marks',
                    ],
                ],
                'judging_criteria' => [
                    [
                        'no' => 1,
                        'criterion' => 'Entry',
                        'description' => 'Confidence, organization and cultural presentation during stage entry.',
                        'possible_score' => 5,
                    ],
                    [
                        'no' => 2,
                        'criterion' => 'Style / Stage Craft',
                        'description' => 'Distinguishing characteristics of the dances, expression, stage presence and representation.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 3,
                        'criterion' => 'Costume and Make-up',
                        'description' => 'Appropriateness, authenticity, neatness and cultural relevance of attire.',
                        'possible_score' => 15,
                    ],
                    [
                        'no' => 4,
                        'criterion' => 'Choreography / Creativity',
                        'description' => 'Design, sequencing, transitions, coordination and creativity of the dance presentation.',
                        'possible_score' => 25,
                    ],
                    [
                        'no' => 5,
                        'criterion' => 'Originality / Authenticity',
                        'description' => 'Accuracy, genuineness and preservation of traditional dance elements.',
                        'possible_score' => 25,
                    ],
                    [
                        'no' => 6,
                        'criterion' => 'General Impression',
                        'description' => 'Excellence, audience appeal and overall impact of the performance.',
                        'possible_score' => 15,
                    ],
                    [
                        'no' => 7,
                        'criterion' => 'Exit',
                        'description' => 'Orderliness, confidence and professionalism during stage exit.',
                        'possible_score' => 5,
                    ],
                ],
            ],

            [
                'name' => 'Drama',
                'slug' => 'drama',
                'type' => 'stage_performance',
                'theme' => $festivalTheme,
                'description' => 'Stage dramatic production based on the CAM festival theme. No sharp or dangerous instruments. 45 minutes allocated.',
                'allocated_minutes' => 45,
                'prep_minutes' => 5,
                'max_raw_score' => 120,
                'rules' => [
                    'theme_alignment' => 'In line with the festival pastoral theme',
                    'safety' => 'No sharp or dangerous instruments allowed on stage',
                    'time_limit_minutes' => 45,
                    'prep_time_minutes' => 5,
                    'time_penalties' => [
                        'Up to 1 minute over' => '-2 marks',
                        '1 to 3 minutes over' => '-5 marks',
                        '3 to 5 minutes over' => '-10 marks',
                        'More than 5 minutes over' => '-15 marks',
                    ],
                ],
                'judging_criteria' => [
                    [
                        'no' => 1,
                        'criterion' => 'Movement (A. Acting)',
                        'description' => 'Gesture, use of body, rhythm, facial expression, choreography and effective use of stage space.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 2,
                        'criterion' => 'Teamwork (A. Acting)',
                        'description' => 'Coordination, character relationships, interaction among actors and unity of purpose.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 3,
                        'criterion' => 'Individual Acting (A. Acting)',
                        'description' => 'Understanding and development of character, creativity, versatility and imagination.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 4,
                        'criterion' => 'Use of Props (B. Production)',
                        'description' => 'Effective and appropriate use of props, stage resources and visual elements.',
                        'possible_score' => 5,
                    ],
                    [
                        'no' => 5,
                        'criterion' => 'Understanding of Theme (B. Production)',
                        'description' => 'Relevance to the festival theme and effectiveness in communicating the intended message.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 6,
                        'criterion' => 'Suitability of Set and Costume (B. Production)',
                        'description' => 'Appropriateness, creativity and effectiveness of set design, costume, make-up and stage presentation.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 7,
                        'criterion' => 'Audibility and Projection (C. Voice)',
                        'description' => 'Ability to be heard clearly throughout the performance.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 8,
                        'criterion' => 'Articulation (C. Voice)',
                        'description' => 'Clarity of speech, pronunciation and diction.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 9,
                        'criterion' => 'Characterization (C. Voice)',
                        'description' => 'Suitable choice of accent, tone and imaginative use of voice for character portrayal.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 10,
                        'criterion' => 'Suitability (D. Choice of Play)',
                        'description' => 'Suitability of the play for the festival theme, age group and audience.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 11,
                        'criterion' => 'Entertainment Value (D. Choice of Play)',
                        'description' => 'Ability to present the message in an engaging, creative and interesting manner.',
                        'possible_score' => 5,
                    ],
                    [
                        'no' => 12,
                        'criterion' => 'Originality / Interpretation (D. Choice of Play)',
                        'description' => 'Creativity, imaginative staging, depth of interpretation and character portrayal.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 13,
                        'criterion' => 'Overall Production (E. Overall Impression)',
                        'description' => 'The production as a whole, including impact, creativity and audience engagement.',
                        'possible_score' => 10,
                    ],
                ],
            ],

            [
                'name' => 'Bible Quiz',
                'slug' => 'bible-quiz',
                'type' => 'oral_quiz',
                'theme' => 'Scripture Knowledge: Exodus, Romans, Gospel of John',
                'description' => 'Oral scriptural quiz competition testing knowledge on Exodus, Romans, and Gospel according to John.',
                'allocated_minutes' => 0,
                'prep_minutes' => 0,
                'max_raw_score' => 100,
                'rules' => [
                    'panellists' => '3 panellists per parish team',
                    'books_covered' => ['Book of Exodus', 'The Letter to the Romans', 'Gospel according to John'],
                    'time_per_question' => '30 seconds to answer a question',
                    'attempts_allowed' => 'Maximum of three attempts per question',
                    'restrictions' => 'Whispering or consulting with the audience is not allowed. Religious leaders/clergy are not allowed to participate as panellists.',
                ],
                'judging_criteria' => [
                    [
                        'no' => 1,
                        'criterion' => 'Book of Exodus Mastery',
                        'description' => 'Knowledge of Moses, plagues, Passover, Sinai Covenant, Commandments, and Tabernacle.',
                        'possible_score' => 30,
                    ],
                    [
                        'no' => 2,
                        'criterion' => 'Letter to the Romans Theology',
                        'description' => 'Understanding of Pauline theology on faith, justification, grace, and Christian ethics.',
                        'possible_score' => 30,
                    ],
                    [
                        'no' => 3,
                        'criterion' => 'Gospel of John Knowledge',
                        'description' => 'Signs, "I AM" statements, Last Supper discourse, Passion, and Resurrection accounts.',
                        'possible_score' => 30,
                    ],
                    [
                        'no' => 4,
                        'criterion' => 'Speed, Discipline & Direct Accuracy',
                        'description' => 'Answering directly within the 30-second window and team discipline.',
                        'possible_score' => 10,
                    ],
                ],
            ],

            [
                'name' => 'DOCAT & YOUCAT Quiz',
                'slug' => 'docat-quiz',
                'type' => 'oral_quiz',
                'theme' => 'Catholic Social Teaching & Youth Catechism',
                'description' => 'Oral catechism and social doctrine quiz based on DOCAT & YOUCAT publications.',
                'allocated_minutes' => 0,
                'prep_minutes' => 0,
                'max_raw_score' => 100,
                'rules' => [
                    'panellists' => '3 panellists per parish team',
                    'focus_material' => 'DOCAT (Catholic Social Teaching) and YOUCAT',
                    'time_per_question' => '30 seconds to answer a question',
                    'attempts_allowed' => 'Maximum of three attempts per question',
                    'restrictions' => 'Whispering or consulting with audience is prohibited. Religious leaders are not allowed as panellists.',
                ],
                'judging_criteria' => [
                    [
                        'no' => 1,
                        'criterion' => 'DOCAT Principles (Social Doctrine)',
                        'description' => 'Human dignity, Common Good, Subsidiarity, Solidarity, and Care for Creation.',
                        'possible_score' => 40,
                    ],
                    [
                        'no' => 2,
                        'criterion' => 'YOUCAT Catechism Knowledge',
                        'description' => 'Creed, Sacraments, Moral life in Christ, and Christian Prayer.',
                        'possible_score' => 30,
                    ],
                    [
                        'no' => 3,
                        'criterion' => 'Application to Pastoral Care Theme',
                        'description' => 'Practical application of Catholic social teachings to community and parish pastoral care.',
                        'possible_score' => 20,
                    ],
                    [
                        'no' => 4,
                        'criterion' => 'Team Coordination & Time Adherence',
                        'description' => 'Prompt and accurate answers delivered within the 30-second rule.',
                        'possible_score' => 10,
                    ],
                ],
            ],

            [
                'name' => 'Academic Quiz & Exam',
                'slug' => 'academic-quiz',
                'type' => 'written_and_oral',
                'theme' => 'Academic Excellence in STEM & Social Sciences',
                'description' => 'Written examinations in Mathematics & Physical Sciences, plus Biology & Civic Education quiz.',
                'allocated_minutes' => 120,
                'prep_minutes' => 0,
                'max_raw_score' => 100,
                'rules' => [
                    'panellists' => '4 panellists per parish team',
                    'written_exam_subjects' => 'Mathematics and Science (Physics & Chemistry)',
                    'oral_quiz_subjects' => 'Biology and Civic Education',
                    'time_allowed_written' => '120 minutes for written exams',
                    'quiz_time_per_question' => '30 seconds for oral questions',
                    'restrictions' => 'Consulting notes or audience is strictly prohibited.',
                ],
                'judging_criteria' => [
                    [
                        'no' => 1,
                        'criterion' => 'Mathematics Written Exam',
                        'description' => 'Algebraic computation, geometric proofs, statistics, and problem solving.',
                        'possible_score' => 25,
                    ],
                    [
                        'no' => 2,
                        'criterion' => 'Physical Sciences (Physics & Chemistry)',
                        'description' => 'Physics mechanics/electricity and Chemistry stoichiometry/chemical reactions.',
                        'possible_score' => 25,
                    ],
                    [
                        'no' => 3,
                        'criterion' => 'Biological Sciences',
                        'description' => 'Cell biology, genetics, ecology, physiology, and health sciences.',
                        'possible_score' => 25,
                    ],
                    [
                        'no' => 4,
                        'criterion' => 'Civic Education & Governance',
                        'description' => 'Zambian Constitution, human rights, civic responsibilities, and international governance.',
                        'possible_score' => 25,
                    ],
                ],
            ],
        ];

        $catModels = [];
        foreach ($categoriesData as $cData) {
            $catModels[$cData['slug']] = Category::create($cData);
        }

        // -------------------------------------------------------------
        // 3. SEED FESTIVAL TIMETABLE / SCHEDULE ITEMS
        // -------------------------------------------------------------
        
        // Monday Schedule: Arrival, Registration, Opening Mass & Ceremonies
        ScheduleItem::create([
            'event_date' => '2026-08-17',
            'day_name' => 'Monday',
            'scheduled_start_time' => '08:00:00',
            'scheduled_end_time' => '12:00:00',
            'venue' => 'Main Gate & Campsite Desk',
            'activity_title' => 'Parish Contingents Arrival, Accreditation & Campsite Check-In',
            'status' => 'completed',
        ]);

        ScheduleItem::create([
            'event_date' => '2026-08-17',
            'day_name' => 'Monday',
            'scheduled_start_time' => '12:00:00',
            'scheduled_end_time' => '13:30:00',
            'venue' => 'Dining Arena',
            'activity_title' => 'Contingents Lunch & Settlement in Assigned Hostels',
            'status' => 'completed',
        ]);

        ScheduleItem::create([
            'event_date' => '2026-08-17',
            'day_name' => 'Monday',
            'scheduled_start_time' => '14:00:00',
            'scheduled_end_time' => '15:30:00',
            'venue' => 'Adjudicators Conference Room',
            'activity_title' => 'Technical Briefing: Adjudicators, Patrons & Stage Managers',
            'status' => 'completed',
        ]);

        ScheduleItem::create([
            'event_date' => '2026-08-17',
            'day_name' => 'Monday',
            'scheduled_start_time' => '16:00:00',
            'scheduled_end_time' => '17:30:00',
            'venue' => 'Festival Grounds Pavilion',
            'activity_title' => 'Solemn Opening Holy Mass (Main Celebrant: Bishop Valentine Kalumba)',
            'status' => 'in_progress',
        ]);

        ScheduleItem::create([
            'event_date' => '2026-08-17',
            'day_name' => 'Monday',
            'scheduled_start_time' => '18:00:00',
            'scheduled_end_time' => '20:30:00',
            'venue' => 'Main Stage',
            'activity_title' => 'Official Opening Ceremony, Roll Call of Parishes & Welcome Social',
            'status' => 'scheduled',
        ]);

        // Tuesday Schedule: Poetry Competition (17 Parishes)
        $tuesdayTime = 9; $tuesdayMin = 0;
        $order = 1;
        foreach ($parishModels as $pName => $pModel) {
            if ($order == 10) {
                // Lunch break pause
                $startStr = "14:00:00";
                $endStr = "14:20:00";
                $tuesdayTime = 14; $tuesdayMin = 20;
            } else {
                $startStr = sprintf("%02d:%02d:00", $tuesdayTime, $tuesdayMin);
                $tuesdayMin += 20;
                if ($tuesdayMin >= 60) {
                    $tuesdayTime += 1;
                    $tuesdayMin -= 60;
                }
                $endStr = sprintf("%02d:%02d:00", $tuesdayTime, $tuesdayMin);
            }

            ScheduleItem::create([
                'event_date' => '2026-08-18',
                'day_name' => 'Tuesday',
                'scheduled_start_time' => $startStr,
                'scheduled_end_time' => $endStr,
                'venue' => 'Main Stage',
                'activity_title' => "Poetry: {$pName}",
                'category_id' => $catModels['poetry']->id,
                'parish_id' => $pModel->id,
                'performance_order' => $order,
                'status' => $order === 1 ? 'in_progress' : ($order < 1 ? 'completed' : 'scheduled'),
            ]);
            $order++;
        }

        // Wednesday Schedule: Self-Composed Song & Traditional Dance
        $wedTime = 9; $wedMin = 0;
        $wOrder = 1;
        foreach ($parishModels as $pName => $pModel) {
            $startStr = sprintf("%02d:%02d:00", $wedTime, $wedMin);
            $wedMin += 15;
            if ($wedMin >= 60) { $wedTime += 1; $wedMin -= 60; }
            $endStr = sprintf("%02d:%02d:00", $wedTime, $wedMin);

            ScheduleItem::create([
                'event_date' => '2026-08-19',
                'day_name' => 'Wednesday',
                'scheduled_start_time' => $startStr,
                'scheduled_end_time' => $endStr,
                'venue' => 'Cultural Stage',
                'activity_title' => "Self-Composed Song: {$pName}",
                'category_id' => $catModels['self-composed-song']->id,
                'parish_id' => $pModel->id,
                'performance_order' => $wOrder,
                'status' => 'scheduled',
            ]);
            $wOrder++;
        }

        // Thursday Schedule: Traditional Dance & Academic Written Exams
        $thursTime = 9; $thursMin = 0;
        $tOrder = 1;
        foreach ($parishModels as $pName => $pModel) {
            $startStr = sprintf("%02d:%02d:00", $thursTime, $thursMin);
            $thursMin += 25;
            if ($thursMin >= 60) { $thursTime += 1; $thursMin -= 60; }
            $endStr = sprintf("%02d:%02d:00", $thursTime, $thursMin);

            ScheduleItem::create([
                'event_date' => '2026-08-20',
                'day_name' => 'Thursday',
                'scheduled_start_time' => $startStr,
                'scheduled_end_time' => $endStr,
                'venue' => 'Main Arena',
                'activity_title' => "Traditional Dance: {$pName}",
                'category_id' => $catModels['traditional-dance']->id,
                'parish_id' => $pModel->id,
                'performance_order' => $tOrder,
                'status' => 'scheduled',
            ]);
            $tOrder++;
        }

        // Friday Schedule: Bible Quiz & DOCAT/YOUCAT Quiz
        ScheduleItem::create([
            'event_date' => '2026-08-21',
            'day_name' => 'Friday',
            'scheduled_start_time' => '09:00:00',
            'scheduled_end_time' => '12:30:00',
            'venue' => 'Assembly Hall',
            'activity_title' => 'Bible Quiz Prelims & Finals (Exodus, Romans, John)',
            'category_id' => $catModels['bible-quiz']->id,
            'parish_id' => null,
            'performance_order' => 1,
            'status' => 'scheduled',
        ]);

        ScheduleItem::create([
            'event_date' => '2026-08-21',
            'day_name' => 'Friday',
            'scheduled_start_time' => '14:00:00',
            'scheduled_end_time' => '17:00:00',
            'venue' => 'Assembly Hall',
            'activity_title' => 'DOCAT & YOUCAT Social Doctrine Quiz Finals',
            'category_id' => $catModels['docat-quiz']->id,
            'parish_id' => null,
            'performance_order' => 2,
            'status' => 'scheduled',
        ]);

        // Saturday Schedule: Choir Music (Melody) & Drama
        $satTime = 8; $satMin = 30;
        $sOrder = 1;
        foreach ($parishModels as $pName => $pModel) {
            if ($sOrder > 8) break; // Choir block 1
            $startStr = sprintf("%02d:%02d:00", $satTime, $satMin);
            $satMin += 35;
            if ($satMin >= 60) { $satTime += 1; $satMin -= 60; }
            $endStr = sprintf("%02d:%02d:00", $satTime, $satMin);

            ScheduleItem::create([
                'event_date' => '2026-08-22',
                'day_name' => 'Saturday',
                'scheduled_start_time' => $startStr,
                'scheduled_end_time' => $endStr,
                'venue' => 'Main Cathedral Hall',
                'activity_title' => "Choir Competition: {$pName}",
                'category_id' => $catModels['choir']->id,
                'parish_id' => $pModel->id,
                'performance_order' => $sOrder,
                'status' => 'scheduled',
            ]);
            $sOrder++;
        }

        // Sunday Schedule: Festival High Mass, Final Results & Awards
        ScheduleItem::create([
            'event_date' => '2026-08-23',
            'day_name' => 'Sunday',
            'scheduled_start_time' => '08:30:00',
            'scheduled_end_time' => '11:30:00',
            'venue' => 'St. Theresa Cathedral Parish',
            'activity_title' => 'CAM Festival Thanksgiving High Mass & Blessing',
            'category_id' => null,
            'parish_id' => $parishModels['St. Theresa Cathedral Parish']->id,
            'performance_order' => 1,
            'status' => 'scheduled',
        ]);

        ScheduleItem::create([
            'event_date' => '2026-08-23',
            'day_name' => 'Sunday',
            'scheduled_start_time' => '13:30:00',
            'scheduled_end_time' => '16:30:00',
            'venue' => 'Main Stage',
            'activity_title' => 'Official Awards Ceremony & Overall Championship Trophy Presentation',
            'category_id' => null,
            'parish_id' => null,
            'performance_order' => 2,
            'status' => 'scheduled',
        ]);
    }
}