<?php

namespace App\Http\Helpers;

class Helper
{


    public static function  sendOtpToPhone($phoneNumber, $otp)
    {
        // $apiUrl = 'https://sms.aanviwireless.com/api/sms/send';
        // $token = '3856647f5ada2251447b39acaa5beeec';  // Your API token
        // $sender = 'AVANA';  // Sender name
        // $templateId = '1707175014877153767';  // Template ID

        // // OTP message content
        // $message = "Your one-time password (OTP) for login in the Avana One is $otp. Please enter this OTP to complete your login in Avana One Portal. Thank you! - Avana One";

        // // Send the HTTP request to AANVI Wireless API
        // $response = \Illuminate\Support\Facades\Http::withHeaders([
        //     'Authorization' => 'Bearer ' . $token,
        //     'Accept' => 'application/json',
        // ])->post($apiUrl, [
        //     'api_key'     => $token,
        //     'sender_name' => $sender,
        //     'phone_number' => $phoneNumber,
        //     'message'     => $message,
        //     'template_id' => $templateId,
        // ]);

        return [
            'success' => true,
            'message' => 'OTP sent successfully (simulated)',
            'phone_number' => $phoneNumber,
            'otp' => $otp // Only include this in development, NEVER in production logs
        ];
    }
}
