<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\RateLimiter; 


class RateLimitingTest extends TestCase
{
    

    /**
     * @test
     */
    public function the_third_job_is_rate_limited_and_released_back_to_queue()
    {
        $key = 'sms-gateway';
        $maxAttempts = 2;
        $decaySeconds = 5; 
   
        RateLimiter::clear($key);
        
        RateLimiter::hit($key, $decaySeconds); 
        $this->assertTrue(RateLimiter::remaining($key, $maxAttempts) == 1, '1. hit sonrası kalan 1 olmalıydı.');

        RateLimiter::hit($key, $decaySeconds);
        $this->assertTrue(RateLimiter::remaining($key, $maxAttempts) == 0, '2. hit sonrası kalan 0 olmalıydı.');

        $this->assertTrue(RateLimiter::tooManyAttempts($key, $maxAttempts), '3. işlem kısıtlanmalıydı.');

        $this->assertFalse(RateLimiter::attempt($key, $maxAttempts, fn() => true), 'Üçüncü işlem Rate Limiter\'a takılmalıydı.');

        $this->travel(6)->seconds();

        $this->assertFalse(RateLimiter::tooManyAttempts($key, $maxAttempts), '6 saniye sonra kısıtlama kalkmalıydı.');
        $this->assertTrue(RateLimiter::remaining($key, $maxAttempts) == $maxAttempts, '6 saniye sonra kalan hak 2 olmalıydı.');
    }
}