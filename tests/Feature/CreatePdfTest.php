<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatePdfTest extends TestCase
{
    /**
     * Create pdf.
     *
     * @return void
     */
    public function test_pdf_is_created_from_document_design()
    {
        $apiKey = '563c51b6ea419e06b4a814bf0b86a9f5';
        $documentId = '9337de77db85c9118b212cb126b8318f';
        $response = $this->post('/api/create-pdf/'.$documentId);

        $response->assertStatus(200);
    }
}
