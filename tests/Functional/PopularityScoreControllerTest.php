<?php

namespace App\Tests\Functional;

use App\Entity\PopularityResult;
use App\Services\FakeServiceProvider;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PopularityScoreControllerTest extends WebTestCase
{
    protected function setUp()
    {
        self::bootKernel();

        $this->truncateEntities();
    }

    /** @test */
    public function get_json_form_service_provider_and_save_to_db()
    {
        $client = static::createClient();
        $client->request('GET', 'score?term=php');
        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $responseData = json_decode($response->getContent(), true);
        $this->assertArraySubset([
            'term' => 'php',
            'score' => 3.33
        ], $responseData);

        $scoreInDb = $this->getEntityManager()
            ->getRepository(PopularityResult::class)
            ->findBy(['term' => 'php', 'score' => 3.33]);
        $this->assertCount(1, $scoreInDb);
    }

    /** @test */
    public function get_json_from_db_if_it_exists_in_db()
    {
        $entityManger = $this->getEntityManager();
        $score = new PopularityResult();
        $score->setTerm('php');
        $score->setScore('4.2');
        $entityManger->persist($score);
        $entityManger->flush();

        $client = static::createClient();
        $client->request('GET', '/score?term=php');

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $reponseData = json_decode($response->getContent(), true);
        $this->assertSame([
            'term' => 'php',
            'score' => 4.2
        ], $reponseData);

        $score_in_db = $this->getEntityManager()
            ->getRepository(PopularityResult::class)
            ->findBy([
                'term' => 'php',
            ]);

        $this->assertCount(1, $score_in_db);
    }

    /** @test */
    public function get_json_from_service_provider_and_save_to_db_for_version_2()
    {
        $client = static::createClient();
        $client->request('GET', '/score/v2?term=php');

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Accept', 'application/vnd.api+json'));
        $reponseData = json_decode($response->getContent(), true);
        $this->assertArraySubset([
            'data' => [
                'term' => 'php',
            ]
        ], $reponseData);

        $score_in_db = $this->getEntityManager()
            ->getRepository(PopularityResult::class)
            ->findBy([
                'term' => 'php',
            ]);

        $this->assertCount(1, $score_in_db);

    }

    /** @test */
    public function term_is_required_parametar()
    {
        $client = static::createClient();
        $client->request('GET', '/score');

        $response = $client->getResponse();
        $this->assertSame(422, $response->getStatusCode());
    }

    /** @test */
    public function term_must_have_word()
    {
        $client = static::createClient();
        $client->request('GET', '/score?term');

        $response = $client->getResponse();
        $this->assertSame(422, $response->getStatusCode());
    }

    private function truncateEntities()
    {
        $purger = new ORMPurger($this->getEntityManager());
        $purger->purge();
    }

    private function getEntityManager()
    {
        return self::$kernel->getContainer()
                ->get('doctrine')
                ->getManager();
    }
}
