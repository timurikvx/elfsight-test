<?php

namespace App\Tests;

use Zenstruck\Foundry\Test\Factories;
use Faker\Factory;

class EpisodeTest extends ApiTestCase
{

    use Factories;

    public function testListEpisodes()
    {
        $this->client->request('POST', '/api/episodes/list');
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseIsSuccessful();
        $answer = $this->getResponseData($this->client->getResponse());
        $this->assertIsArray($answer);
    }

    public function testImport()
    {
        $this->client->request('POST', '/api/episodes/import');
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseIsSuccessful();
        $answer = $this->getResponseData($this->client->getResponse());
        $this->assertIsArray($answer);
        $this->assertArrayHasKey('all', $answer);
        $this->assertArrayHasKey('updated', $answer);
    }


    public function testEpisodeReview(){

        for ($i = 0; $i < 10; $i++){
            $faker = Factory::create();
            $text = $faker->sentence(random_int(4, 20));
            $episode = random_int(1, 51);

            $data = [
                'id'=>$episode,
                'text'=>$text
            ];

            $this->client->request('POST', '/api/episode/review', [], [], [], json_encode($data));
            $answer = $this->getResponseData($this->client->getResponse());
            $this->assertResponseStatusCodeSame(200);
            $this->assertIsArray($answer);
            $this->assertArrayHasKey('rating', $answer);
        }

    }

    public function testEpisodeReviewByID(){

        for ($i = 0; $i < 10; $i++){
            $faker = Factory::create();
            $text = $faker->sentence(random_int(4, 20));
            $episode = random_int(1, 51);

            $data = [
                'text'=>$text
            ];

            $this->client->request('POST', '/api/episode/review/'.$episode, [], [], [], json_encode($data));
            $answer = $this->getResponseData($this->client->getResponse());
            $this->assertResponseStatusCodeSame(200);
            $this->assertIsArray($answer);
            $this->assertArrayHasKey('rating', $answer);
        }

    }

    public function testEpisodeSummary(){

        $episode = random_int(1, 51);
        $this->client->request('POST', '/api/episode/summary/'.$episode);
        $answer = $this->getResponseData($this->client->getResponse());
        $this->assertResponseStatusCodeSame(200);
        $this->assertIsArray($answer);
        $this->assertArrayHasKey('name', $answer);
        $this->assertArrayHasKey('date', $answer);
        $this->assertArrayHasKey('rate', $answer);
        $this->assertArrayHasKey('last_reviews', $answer);
    }

}
