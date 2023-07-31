<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class AffiliateControllerTest extends TestCase
{
    use WithoutMiddleware;

    /** @test */
    public function it_can_process_affiliates_file_upload()
    {
        $fileContents = '{"latitude": "52.986375", "affiliate_id": 12, "name": "Yosef Giles", "longitude": "-6.043701"}
        {"latitude": "51.92893", "affiliate_id": 1, "name": "Lance Keith", "longitude": "-10.27699"}
        {"latitude": "51.8856167", "affiliate_id": 2, "name": "Mohamed Bradshaw", "longitude": "-10.4240951"}
        {"latitude": "52.3191841", "affiliate_id": 3, "name": "Rudi Palmer", "longitude": "-8.5072391"}';

        // Create a temporary file and save the contents
        $file = UploadedFile::fake()->createWithContent('affiliates.txt', $fileContents);

        // Make a POST request to the controller endpoint with the file attached
        $response = $this->post(route('processUpload'), [
            'affiliates_file' => $file,
        ], ['Content-Type' => 'multipart/form-data']);

        // Assert that the response has status 200 (OK)
        $response->assertStatus(200);

        // Assert that the matching affiliates are displayed in the view
        $response->assertViewIs('display_affiliates');

        // Assert that the view has the matching affiliate data
        $response->assertSee('Yosef Giles');
    }

    /** @test */
    public function it_shows_affiliates_within_100km()
    {
        // Create two affiliates within 100km of Dublin office
        $affiliate1 = [
            'latitude' => 53.3340285,
            'longitude' => -6.1535495,
            'affiliate_id' => 1,
            'name' => 'Affiliate One',
        ];
        $affiliate2 = [
            'latitude' => 53.2340285,
            'longitude' => -6.3535495,
            'affiliate_id' => 2,
            'name' => 'Affiliate Two',
        ];

        // Create two affiliates outside 100km of Dublin office
        $affiliate3 = [
            'latitude' => 52.3340285,
            'longitude' => -5.2535495,
            'affiliate_id' => 3,
            'name' => 'Affiliate Three',
        ];
        $affiliate4 = [
            'latitude' => 54.3340285,
            'longitude' => -7.2535495,
            'affiliate_id' => 4,
            'name' => 'Affiliate Four',
        ];

        // Combine all affiliates into a single array
        $affiliates = [$affiliate1, $affiliate2, $affiliate3, $affiliate4];
        $fileContents = implode("\n", array_map('json_encode', $affiliates));

        // Create a temporary file and save the contents
        $file = UploadedFile::fake()->createWithContent('affiliates.txt', $fileContents);

        // Make a POST request to the controller endpoint with the file attached
        $response = $this->post(route('processUpload'), [
            'affiliates_file' => $file,
        ]);

        // Assert that the response has status 200 (OK)
        $response->assertStatus(200);

        // Assert that the view has the matching affiliate data
        $response->assertSee('Affiliate One');
        $response->assertSee('Affiliate Two');

        // Assert that the view does not contain affiliates outside 100km
        $response->assertDontSee('Affiliate Three');
        $response->assertDontSee('Affiliate Four');
    }

    /** @test */
    public function it_does_not_show_affiliates_outside_100km()
    {
        // Create affiliates outside 100km of Dublin office
        $affiliate1 = [
            'latitude' => 52.3340285,
            'longitude' => -5.2535495,
            'affiliate_id' => 1,
            'name' => 'Affiliate One',
        ];
        $affiliate2 = [
            'latitude' => 54.3340285,
            'longitude' => -7.2535495,
            'affiliate_id' => 2,
            'name' => 'Affiliate Two',
        ];

        // Combine affiliates into a single array
        $affiliates = [$affiliate1, $affiliate2];
        $fileContents = implode("\n", array_map('json_encode', $affiliates));

        // Create a temporary file and save the contents
        $file = UploadedFile::fake()->createWithContent('affiliates.txt', $fileContents);

        // Make a POST request to the controller endpoint with the file attached
        $response = $this->post(route('processUpload'), [
            'affiliates_file' => $file,
        ]);

        // Assert that the response has status 200 (OK)
        $response->assertStatus(200);

        // Assert that the view does not contain affiliates within 100km
        $response->assertDontSee('Affiliate One');
        $response->assertDontSee('Affiliate Two');

        // Assert that the view has an error message
        $response->assertSee('No matching affiliates found within 100km.');
    }

    /** @test */
    public function it_redirects_back_with_error_if_file_not_found()
    {
        // Make a POST request without attaching the file
        $response = $this->post(route('processUpload'));

        // Assert that the response redirects back with an error message
        $response->assertStatus(302);
        redirect()->back()->with('error', 'File not found.');
    }
}
