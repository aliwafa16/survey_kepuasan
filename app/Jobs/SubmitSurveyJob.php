<?php 

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SubmitSurveyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    public function handle(): void
{
    \Log::info('SubmitSurveyJob started', ['payload' => $this->payload]);

    try {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://talentdna.me/tdna/corporate_api/save_survey_corporate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_NOSIGNAL => true,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($this->payload),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Cookie: ci_session=6be22251ff26aac8f57f345d8484a5a0405a338e'
            ),
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            throw new \Exception('cURL error: ' . curl_error($curl));
        }

        curl_close($curl);
        \Log::info('Survey response:', ['response' => $response]);

    } catch (\Exception $e) {
        \Log::error('SubmitSurveyJob error: ' . $e->getMessage());
        throw $e; // penting agar Laravel tandai job ini gagal
    }
}
}

