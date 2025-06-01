<?php

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EpisodeListTest extends WebTestCase
{

    public function testEpisodeList()
    {
        $client = static::createClient([], [
            'HTTP_HOST'=>'localhost:84'
        ]);
        $client->request('POST', '/api/episodes/list');
        $this->assertResponseStatusCodeSame(200);
    }

}
