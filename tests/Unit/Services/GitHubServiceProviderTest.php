<?php

namespace App\Tests\Unit\Services;

use App\Services\GitHubServiceProvider;
use PHPUnit\Framework\TestCase;

class GitHubServiceProviderTest extends TestCase
{
    /** @test */
    public function json_result_must_have_field_total_count()
    {
        $gitHubResult = (new GitHubServiceProvider())->getResult('php rocks');

        $this->assertTrue(property_exists(json_decode($gitHubResult), 'total_count'));
    }

    /** @test */
    public function get_total_count_value_from_json_result()
    {
        $jsonResult = json_encode(['total_count' => 7]);

        $resultCount = (new GitHubServiceProvider())->getCount($jsonResult);

        $this->assertEquals(7, $resultCount);
    }

}
