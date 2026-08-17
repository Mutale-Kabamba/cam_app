<?php

namespace App\Services;

class OfficialCategoriesService
{
    public const THEME = 'A YEAR OF WALKING TOGETHER IN DEEPER PASTORAL CARE';

    /**
     * Get all 8 official CAM Festival categories with full specifications, rules, and rubrics.
     *
     * @return array<string, array>
     */
    public static function getOfficialCategories(): array
    {
        return [
            'Choir' => [
                'name' => 'Choir',
                'slug' => 'choir',
                'type' => 'stage_performance',
                'allocated_minutes' => 30,
                'prep_minutes' => 5,
                'max_raw_score' => 100,
                'theme' => self::THEME,
                'description' => 'Choral music competition featuring 4 prescribed songs on stage: Kyrie, Gloria, Thanksgiving, and Social song.',
                'rules' => [
                    '4 songs on stage: Kyrie, Gloria, Thanksgiving, Social song.',
                    'Time: 30 Minutes on stage.',
                    'Unlimited number of participants on stage.',
                    'All songs to be in the language of the Diocese (English, Lozi or Tonga).',
                ],
                'judging_criteria' => [
                    [
                        'no' => 1,
                        'criterion' => 'Vocal Quality & Tone',
                        'description' => 'Richness, vocal blend, breath control, and tonal purity across all voice parts.',
                        'possible_score' => 20,
                    ],
                    [
                        'no' => 2,
                        'criterion' => 'Harmony & Part Singing',
                        'description' => 'Balance between SATB parts, pitch accuracy, chord execution, and harmonic cohesion.',
                        'possible_score' => 20,
                    ],
                    [
                        'no' => 3,
                        'criterion' => 'Diction & Language Expression',
                        'description' => 'Clarity of words, enunciation, and authentic expression in English, Lozi, or Tonga.',
                        'possible_score' => 15,
                    ],
                    [
                        'no' => 4,
                        'criterion' => 'Rhythm, Tempo & Dynamics',
                        'description' => 'Precision in tempo management, rhythm accuracy, dynamic contrasts, and expression.',
                        'possible_score' => 15,
                    ],
                    [
                        'no' => 5,
                        'criterion' => 'Repertoire Execution (4 Songs)',
                        'description' => 'Faithful execution and artistic transition between Kyrie, Gloria, Thanksgiving, and Social song.',
                        'possible_score' => 15,
                    ],
                    [
                        'no' => 6,
                        'criterion' => 'Conducting & Stage Presentation',
                        'description' => 'Conductor leadership, choir discipline, appropriate attire, and stage etiquette.',
                        'possible_score' => 15,
                    ],
                ],
            ],

            'Drama' => [
                'name' => 'Drama',
                'slug' => 'drama',
                'type' => 'stage_performance',
                'allocated_minutes' => 45,
                'prep_minutes' => 5,
                'max_raw_score' => 100,
                'theme' => self::THEME,
                'description' => 'Theatrical production depicting Catholic Christian values and pastoral care themes.',
                'rules' => [
                    'In line with the festival theme: "A YEAR OF WALKING TOGETHER IN DEEPER PASTORAL CARE".',
                    'Time: 45 minutes on stage.',
                    'No sharp or dangerous instruments on stage.',
                ],
                'judging_criteria' => [
                    [
                        'no' => 1,
                        'criterion' => 'Theme Relevance & Pastoral Message',
                        'description' => 'Depth, relevance, and clarity of the pastoral care message in line with the festival theme.',
                        'possible_score' => 20,
                    ],
                    [
                        'no' => 2,
                        'criterion' => 'Plot & Script Development',
                        'description' => 'Clarity of storyline, dialogue effectiveness, pacing, dramatic tension, and resolution.',
                        'possible_score' => 20,
                    ],
                    [
                        'no' => 3,
                        'criterion' => 'Acting & Characterization',
                        'description' => 'Believability of characters, emotional connection, body language, and facial expressions.',
                        'possible_score' => 20,
                    ],
                    [
                        'no' => 4,
                        'criterion' => 'Voice Projection & Diction',
                        'description' => 'Audibility, clarity of speech, pacing, and voice modulation across the stage.',
                        'possible_score' => 15,
                    ],
                    [
                        'no' => 5,
                        'criterion' => 'Stage Craft & Space Utilization',
                        'description' => 'Blocking, coordinated entrances and exits, effective use of stage area and props.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 6,
                        'criterion' => 'Costumes & Overall Presentation',
                        'description' => 'Appropriateness of costumes, creative innovation, discipline, and audience impact.',
                        'possible_score' => 15,
                    ],
                ],
            ],

            'Poetry' => [
                'name' => 'Poetry',
                'slug' => 'poetry',
                'type' => 'stage_performance',
                'allocated_minutes' => 15,
                'prep_minutes' => 5,
                'max_raw_score' => 100,
                'theme' => self::THEME,
                'description' => 'Poetic recitation and dramatic presentation in line with the festival theme.',
                'rules' => [
                    'In line with the festival theme: "A YEAR OF WALKING TOGETHER IN DEEPER PASTORAL CARE".',
                    'Time: 15 minutes on stage.',
                    '6 participants maximum on stage.',
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

            'Self composed Song' => [
                'name' => 'Self composed Song',
                'slug' => 'self-composed-song',
                'type' => 'stage_performance',
                'allocated_minutes' => 10,
                'prep_minutes' => 5,
                'max_raw_score' => 100,
                'theme' => self::THEME,
                'description' => 'Original musical composition composed and performed by parish youth.',
                'rules' => [
                    '10 minutes on stage.',
                    'In line with the festival theme: "A YEAR OF WALKING TOGETHER IN DEEPER PASTORAL CARE".',
                    'Unlimited participants on stage.',
                    'Any language of your choice.',
                    'Dress code should match the traditional attire of the song\'s language.',
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

            'Traditional Dance' => [
                'name' => 'Traditional Dance',
                'slug' => 'traditional-dance',
                'type' => 'stage_performance',
                'allocated_minutes' => 20,
                'prep_minutes' => 5,
                'max_raw_score' => 100,
                'theme' => self::THEME,
                'description' => 'Traditional Zambian cultural dance performance representing 3 different provinces.',
                'rules' => [
                    '3 Different Dances from 3 different provinces of Zambia.',
                    'Time: 20 minutes on stage.',
                    'No masquerade (Vinyau).',
                    'No hiring of dancers.',
                    '15 maximum participants on stage.',
                ],
                'judging_criteria' => [
                    [
                        'no' => 1,
                        'criterion' => 'Authenticity & Cultural Expression',
                        'description' => 'Accuracy of traditional drumming, steps, choreography, and provincial authenticity.',
                        'possible_score' => 25,
                    ],
                    [
                        'no' => 2,
                        'criterion' => '3-Province Dance Transition',
                        'description' => 'Smooth and distinct transitions between the 3 provincial dances and cultural rhythms.',
                        'possible_score' => 20,
                    ],
                    [
                        'no' => 3,
                        'criterion' => 'Rhythm, Drumming & Synchronization',
                        'description' => 'Coordination between dancers, drummers, and vocal chants in complex time signatures.',
                        'possible_score' => 20,
                    ],
                    [
                        'no' => 4,
                        'criterion' => 'Costumes & Cultural Regalia',
                        'description' => 'Authenticity and appropriateness of traditional attire representing the provinces.',
                        'possible_score' => 15,
                    ],
                    [
                        'no' => 5,
                        'criterion' => 'Energy & Stage Presence',
                        'description' => 'Stamina, excitement, facial expressions, and overall crowd engagement.',
                        'possible_score' => 10,
                    ],
                    [
                        'no' => 6,
                        'criterion' => 'Discipline & Regulation Compliance',
                        'description' => 'Compliance with rules (no masquerade, max 15 dancers), timing, and orderly entry/exit.',
                        'possible_score' => 10,
                    ],
                ],
            ],

            'Bible Quiz' => [
                'name' => 'Bible Quiz',
                'slug' => 'bible-quiz',
                'type' => 'quiz_written',
                'allocated_minutes' => 0,
                'prep_minutes' => 0,
                'max_raw_score' => 100,
                'theme' => 'Books of Exodus, Romans & Gospel according to John',
                'description' => 'Scripture knowledge competition based on Catholic canonical texts.',
                'rules' => [
                    'Religious leaders are not allowed to be panellists.',
                    '3 panellists per parish.',
                    'From the Books: Exodus, The letter to the Romans, Gospel according to John.',
                    '30 seconds to answer a question.',
                    'Maximum of three attempts per question.',
                    'Whispering or consulting with the audience is not allowed.',
                ],
                'judging_criteria' => [
                    [
                        'no' => 1,
                        'criterion' => 'Book of Exodus (Round 1)',
                        'description' => 'Historical narrative, laws, covenant, and theology in Exodus.',
                        'possible_score' => 30,
                    ],
                    [
                        'no' => 2,
                        'criterion' => 'Letter to the Romans (Round 2)',
                        'description' => 'Pauline theology, justification, faith, grace, and Christian life in Romans.',
                        'possible_score' => 35,
                    ],
                    [
                        'no' => 3,
                        'criterion' => 'Gospel according to John (Round 3)',
                        'description' => 'Signs, discourses, passion, resurrection narrative, and theology in John.',
                        'possible_score' => 35,
                    ],
                ],
            ],

            'DOCAT & YOUCAT' => [
                'name' => 'DOCAT & YOUCAT',
                'slug' => 'docat-youcat',
                'type' => 'quiz_written',
                'allocated_minutes' => 0,
                'prep_minutes' => 0,
                'max_raw_score' => 100,
                'theme' => 'Social Teaching of the Catholic Church (DOCAT) & Youth Catechism (YOUCAT)',
                'description' => 'Quiz on Catholic Social Teaching and Youth Catechism.',
                'rules' => [
                    'Religious leaders are not allowed to be panellists.',
                    '3 panellists per parish.',
                    'Based on DOCAT (Catholic Social Teaching) and YOUCAT.',
                    '30 seconds to answer a question.',
                    'Maximum of three attempts per question.',
                    'Whispering or consulting with the audience is not allowed.',
                ],
                'judging_criteria' => [
                    [
                        'no' => 1,
                        'criterion' => 'Human Dignity & Person (DOCAT)',
                        'description' => 'Foundational principles of human dignity, common good, and rights in DOCAT.',
                        'possible_score' => 35,
                    ],
                    [
                        'no' => 2,
                        'criterion' => 'Solidarity & Subsidiarity (DOCAT)',
                        'description' => 'Economic justice, environmental stewardship, peace, and family in Catholic social doctrine.',
                        'possible_score' => 35,
                    ],
                    [
                        'no' => 3,
                        'criterion' => 'YOUCAT Catholic Fundamentals',
                        'description' => 'Core Catholic doctrines, sacraments, commandments, and prayer life from YOUCAT.',
                        'possible_score' => 30,
                    ],
                ],
            ],

            'Academic Quiz' => [
                'name' => 'Academic Quiz',
                'slug' => 'academic-quiz',
                'type' => 'quiz_written',
                'allocated_minutes' => 0,
                'prep_minutes' => 0,
                'max_raw_score' => 100,
                'theme' => 'Mathematics, Physics, Chemistry, Biology & Civic Education',
                'description' => 'Academic examination and rapid-fire quiz on sciences and civic education.',
                'rules' => [
                    '4 panellists per parish.',
                    'Subjects: Mathematics and Science (Physics & Chemistry) - written exams; Biology and Civic Education.',
                    '30 seconds to answer a question.',
                    'Maximum of three attempts per question.',
                    'Whispering or consulting with the audience is not allowed.',
                    'Religious people are not allowed to participate as panellists.',
                ],
                'judging_criteria' => [
                    [
                        'no' => 1,
                        'criterion' => 'Mathematics & Physical Sciences (Written)',
                        'description' => 'Problem-solving speed, calculations, and analytical accuracy in Physics & Chemistry.',
                        'possible_score' => 35,
                    ],
                    [
                        'no' => 2,
                        'criterion' => 'Biology',
                        'description' => 'Biological systems, human physiology, ecology, genetics, and scientific concepts.',
                        'possible_score' => 35,
                    ],
                    [
                        'no' => 3,
                        'criterion' => 'Civic Education',
                        'description' => 'Constitution, governance, human rights, civic duties, and contemporary affairs.',
                        'possible_score' => 30,
                    ],
                ],
            ],
        ];
    }
}
