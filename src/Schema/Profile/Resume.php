<?php declare(strict_types=1);

namespace DC23\Schema\Profile;

final class Resume {
    public function register():void {
    
        \add_filter( 'wpseo_schema_person', $this->enhance_person_with_resume(...), 11, 2 );
        
        \add_filter( 'wpseo_schema_graph_pieces', $this->add_resume_to_schema(...), 11, 2 );
    }

    private function should_add_resume_data(): bool {
        return is_home();
    }

    private function enhance_person_with_resume($personData, $context) {
        
        if( ! $this->should_add_resume_data() ) {
            return $personData;
        }

        assert($context instanceof \Yoast\WP\SEO\Context\Meta_Tags_Context);

        $user_data = \get_userdata( $context->site_user_id );
        // $user_meta = get_user_meta( $context->site_user_id );
        $first_name = get_user_meta( $context->site_user_id, 'first_name', true );
        $last_name = get_user_meta( $context->site_user_id, 'last_name', true );
        // var_dump($user_meta);

        $personData['givenName'] = $first_name;
        $personData['familyName'] = $last_name;

        $personData["jobTitle"] = "Lead developer";
        $personData["gender"] = "http://schema.org/Male";
        $personData["nationality"] = [
            "@type"=> "Country",
            "name"=> "Netherlands",
            "alternateName"=> "NL",
            "sameAs"=> "https://en.wikipedia.org/wiki/Netherlands"
        ];

        $personData["knowsLanguage"] = [
            [
                "@type"=>"http://schema.org/Language",
                "name"=>"Dutch",
                "alternateName"=>"nl",
                "sameAs"=>"https://en.wikipedia.org/wiki/Dutch_language"
            ],
            [
                "@type"=>"http://schema.org/Language",
                "name"=>"English",
                "alternateName"=>"en",
                "sameAs"=>"https://en.wikipedia.org/wiki/English_language"
            ]
        ];

        $personData["worksFor"] = [
            [
                "@type"=>"http://schema.org/EmployeeRole",
                "worksFor"=>[
                    "@id"=>"https://www.dennisclaassen.nl/#/schema/Organization/yoast"
                ],
                "roleName"=>"Lead developer",
                "startDate"=>"2021-10-01"
            ],
            [
                "@type"=>"http://schema.org/EmployeeRole",
                "worksFor"=>[
                    "@id"=>"https://www.dennisclaassen.nl/#/schema/Organization/yoast"
                ],
                "roleName"=>"Senior developer",
                "startDate"=>"2021-03-01",
                "endDate"=>"2021-10-01"
            ],
            [
                "@type"=>"http://schema.org/EmployeeRole",
                "worksFor"=>[
                    "@id"=>"https://www.dennisclaassen.nl/#/schema/Organization/youngcapital"
                ],
                "roleName"=>"Senior developer Uitzendbureau.nl & Jobbird.com",
                "startDate"=>"2020-01-01",
                "endDate"=>"2021-02-01"
            ],
            [
                "@type"=>"http://schema.org/EmployeeRole",
                "worksFor"=>[
                    "@id"=>"https://www.dennisclaassen.nl/#/schema/Organization/youngcapital"
                ],
                "roleName"=>"Developer Uitzendbureau.nl & Jobbird.com",
                "startDate"=>"2019-01-01",
                "endDate"=>"2019-12-01"
            ],
            [
                "@type"=>"http://schema.org/EmployeeRole",
                "worksFor"=>[
                    "@id"=>"https://www.dennisclaassen.nl/#/schema/Organization/youngcapital"
                ],
                "roleName"=>"Developer Uitzendbureau.nl",
                "startDate"=>"2017-01-01",
                "endDate"=>"2018-12-01"
            ],
            [
                "@type"=>"http://schema.org/EmployeeRole",
                "worksFor"=>[
                    "@id"=>"https://www.dennisclaassen.nl/#/schema/Organization/hippo-hr"
                ],
                "roleName"=>"Web developer Uitzendbureau.nl",
                "startDate"=>"2012-08-01",
                "endDate"=>"2016-12-01"
            ],
            [
                "@type"=>"http://schema.org/EmployeeRole",
                "worksFor"=>[
                    "@id"=>"https://www.dennisclaassen.nl/#/schema/Organization/hippo-hr"
                ],
                "roleName"=>"Graduate Uitzendbureau.nl",
                "startDate"=>"2012-01-01",
                "endDate"=>"2012-07-01"
            ],
            [
                "@type"=>"http://schema.org/EmployeeRole",
                "worksFor"=>[
                    "@id"=>"https://www.dennisclaassen.nl/#/schema/Organization/hippo-hr"
                ],
                "roleName"=>"Developer Uitzendbureau.nl",
                "startDate"=>"2011-05-01",
                "endDate"=>"2012-01-01"
            ],
            [
                "@type"=>"http://schema.org/EmployeeRole",
                "worksFor"=>[
                    "@id"=>"https://www.dennisclaassen.nl/#/schema/Organization/han"
                ],
                "roleName"=>"Student assistent",
                "startDate"=>"2011-07-01",
                "endDate"=>"2012-01-01"
            ],
            [
                "@type"=>"http://schema.org/EmployeeRole",
                "worksFor"=>[
                    "@id"=>"https://www.dennisclaassen.nl/#/schema/Organization/square-concepts"
                ],
                "roleName"=>"Intern",
                "startDate"=>"2010-09-01",
                "endDate"=>"2011-01-01"
            ]
        ];

        $personData["alumniOf"] = [
            [
                "@type"=>"http://schema.org/Role",
                "alumniOf"=>[
                    "@id"=>"https://www.dennisclaassen.nl/#/schema/Organization/han"
                ],
                "roleName"=>"Student Communication & Multimedia Design",
                "startDate"=>"2008-09-01",
                "endDate"=>"2012-06-01"
            ]
        ];
        
        $personData["knowsAbout"] = [
            [
                "@id"=>"https://www.dennisclaassen.nl/#/schema/Specialty/1"
            ],
            [
                "@id"=>"https://www.dennisclaassen.nl/#/schema/Specialty/2"
            ],
            [
                "@id"=>"https://www.dennisclaassen.nl/#/schema/Specialty/3"
            ],
            [
                "@id"=>"https://www.dennisclaassen.nl/#/schema/Specialty/4"
            ],
            [
                "@id"=>"https://www.dennisclaassen.nl/#/schema/Specialty/5"
            ]
        ];

        return $personData;
    } 

    private function add_resume_to_schema( $pieces, $context) {
        
        if( ! $this->should_add_resume_data() ) {
            return $pieces;
        }
        
        
        array_push(
            $pieces,
            new class([
            "@id"=> "https://www.dennisclaassen.nl/#/schema/Organization/han",
            "@type"=> "https://schema.org/CollegeOrUniversity",
            "name"=> "Hogeschool van Arnhem en Nijmegen",
            "sameAs"=> ["https://en.wikipedia.org/wiki/HAN_University_of_Applied_Sciences","https://www.han.nl"]
        ]) extends Piece {},new class([
            "@id"=> "https://www.dennisclaassen.nl/#/schema/Organization/square-concepts",
            "@type"=> "https://schema.org/Corporation",
            "name"=> "SQUARE Concepts",
            "sameAs"=> ["https://www.squareconcepts.nl"]
        ]) extends Piece {},new class([
            "@id"=> "https://www.dennisclaassen.nl/#/schema/Organization/hippo-hr",
            "@type"=> "https://schema.org/Corporation",
            "name"=> "Hippo HR",
            "parentOrganization"=> [
                "@type"=> "http://schema.org/OrganizationRole",
                "parentOrganization"=> [
                    "@id"=> "https://www.dennisclaassen.nl/#/schema/Organization/youngcapital"
                ],
                "startDate"=> "2017-01-01"
            ]
        ]) extends Piece {},new class([
            "@id"=> "https://www.dennisclaassen.nl/#/schema/Organization/youngcapital",
            "@type"=> "https://schema.org/Corporation",
            "name"=> "YoungCapital",
            "sameAs"=> ["https://www.youngcapital.nl"]
        ]) extends Piece {},new class([
            "@id"=> "https://www.dennisclaassen.nl/#/schema/Organization/yoast",
            "@type"=> "https://schema.org/Corporation",
            "name"=> "Yoast",
            "mainEntityOfPage"=> "https://yoast.com/about-us/",
            "sameAs"=> ["https://en.wikipedia.org/wiki/Yoast","https://yoast.com"]
        ]) extends Piece {},new class([
            "@id"=> "https://www.dennisclaassen.nl/#/schema/Specialty/1",
            "@type"=> "http://schema.org/Specialty",
            "name"=> "Web development",
            "description"=> "HTML, JavaScript (jQuery, Vue, React), CSS (Sass), PHP (Symfony, Doctrine), Git.",
            "sameAs"=> "https://en.wikipedia.org/wiki/Web_development"
        ]) extends Piece {},new class([
            "@id"=> "https://www.dennisclaassen.nl/#/schema/Specialty/2",
            "@type"=> "http://schema.org/Specialty",
            "name"=> "Database design",
            "description"=> "MySQL, Elasticsearch.",
            "sameAs"=> "https://en.wikipedia.org/wiki/Database_design"
        ]) extends Piece {},new class([
            "@id"=> "https://www.dennisclaassen.nl/#/schema/Specialty/3",
            "@type"=> "http://schema.org/Specialty",
            "name"=> "Test automation",
            "description"=> "Unit testing (PHPUnit, Jest), end-to-end testing (Selenium, Puppeteer), test driven development.",
            "sameAs"=> "https://en.wikipedia.org/wiki/Test_automation"
        ]) extends Piece {},
        new class([
            "@id"=> "https://www.dennisclaassen.nl/#/schema/Specialty/4",
            "@type"=> "http://schema.org/Specialty",
            "name"=> "Quality assurance",
            "description"=> "Linting (PHP_CodeSniffer, ESLint), static analysis (Psalm, PHPStan), code review (Phabricator, GitHub), continuous integration (Jenkins, GitHub Actions), SonarQube.",
            "sameAs"=> "https://en.wikipedia.org/wiki/Software_testing"
        ]) extends Piece {},
        new class([
            "@id"=> "https://www.dennisclaassen.nl/#/schema/Specialty/5",
            "@type"=> "http://schema.org/Specialty",
            "name"=> "Soft skills",
            "description"=> "Communication, coaching, prioritizing, proactive.",
            "sameAs"=> "https://en.wikipedia.org/wiki/Soft_skills"
        ]) extends Piece {}
        );
        return $pieces;
    }
}

class Piece extends \Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece {

    public function __construct( 
        private array $output
    ) {}

    public function is_needed(): bool {
        return true;
    }

    public function generate(): array {
        return $this->output;
    }
}