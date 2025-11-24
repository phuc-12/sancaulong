<?php

namespace App\Services;

use App\Models\Bookings;

class NLUService
{
    public function analyze($text)
    {
        $text = mb_strtolower($text);

        // Extract entities once để tái sử dụng
        $extractedTime = $this->extractTime($text);
        $extractedDate = $this->extractDate($text);

        // Intent: Chào hỏi
        if (preg_match('/\b(chào|xin chào|hello|hi|hey)\b/u', $text)) {
            return [
                'intent' => 'greeting',
                'entities' => []
            ];
        }

        // Đặt sân - kiểm tra trước để ưu tiên
        if (preg_match('/(đặt.*sân|giữ.*sân|book|booking)/', $text)) {
            return [
                'intent' => 'booking_request',
                'entities' => [
                    'court' => $this->extractCourt($text),
                    'date' => $extractedDate,
                    'time' => $extractedTime,
                ]
            ];
        }

        // Kiểm tra giá 
        if (preg_match('/(giá|price|bao nhiêu|chi phí|giá cả|giá tiền)/', $text)) {
            return [
                'intent' => 'check_price',
                'entities' => [
                    'facility_name' => $this->extractFacilityName($text)
                ]
            ];
        }

        // Xem lịch sử đặt sân
        if (preg_match('/(lịch sử|lịch đặt|đơn đặt|booking.*của.*tôi|xem.*đặt)/', $text)) {
            return [
                'intent' => 'view_booking',
                'entities' => []
            ];
        }

        // Tìm cơ sở khác / sân khác
        if (preg_match('/(còn.*sân.*khác|cơ sở.*khác|sân.*khác|địa điểm.*khác|nơi.*khác|tìm.*cơ sở|còn.*chỗ.*khác|còn.*nơi.*nào|còn.*đâu|chỗ.*khác)/', $text)) {
            return [
                'intent' => 'find_other_facilities',
                'entities' => [
                    'date' => $extractedDate,
                    'time' => $extractedTime,
                ]
            ];
        }

        // Kiểm tra sân trống - với từ khóa rõ ràng
        if (preg_match('/(còn.*sân|sân.*trống|kiểm tra.*sân|sân.*còn|trống.*không|kiểm tra.*giờ.*trống|kiểm tra.*trống|giờ.*trống|sân.*còn.*không|check.*availability)/', $text)) {
            return [
                'intent' => 'check_availability',
                'entities' => [
                    'date' => $extractedDate,
                    'time' => $extractedTime,
                ]
            ];
        }

        // Kiểm tra sân trống - nhận diện khi có thời gian và ngày (ngầm định)
        if ($extractedTime && $extractedDate) {
            return [
                'intent' => 'check_availability',
                'entities' => [
                    'date' => $extractedDate,
                    'time' => $extractedTime,
                ]
            ];
        }

        // Nếu chỉ có thời gian (không có ngày) -> mặc định là kiểm tra sân hôm nay
        if ($extractedTime && !$extractedDate) {
            return [
                'intent' => 'check_availability',
                'entities' => [
                    'date' => date('Y-m-d'),
                    'time' => $extractedTime,
                ]
            ];
        }

        return [
            'intent' => 'unknown',
            'entities' => [
                'date' => $extractedDate,
                'time' => $extractedTime,
            ]
        ];
    }

    private function extractCourt($text)
    {
        if (preg_match('/sân\s*(\d+)/', $text, $m))
            return $m[1];
        return null;
    }

    private function extractTime($text)
    {
        if (preg_match('/(\d{1,2})h/', $text, $m))
            return sprintf("%02d:00:00", $m[1]);
        if (preg_match('/(\d{1,2}:\d{2})/', $text, $m))
            return $m[1] . ":00";
        return null;
    }

    private function extractDate($text)
    {
        if (str_contains($text, 'hôm nay'))
            return date('Y-m-d');
        if (str_contains($text, 'ngày mai'))
            return date('Y-m-d', strtotime('+1 day'));
        if (preg_match('/(thứ\s*\d)/', $text, $m))
            return $m[1];
        if (preg_match('/\d{1,2}\/\d{1,2}/', $text, $m)) {
            $parts = explode('/', $m[0]);
            return date('Y') . '-' . sprintf("%02d", $parts[1]) . '-' . sprintf("%02d", $parts[0]); // Format Y-m-d
        }
        return null;
    }

    // Extract tên cơ sở
    private function extractFacilityName($text)
    {
        // Loại bỏ các từ khóa phổ biến liên quan đến giá
        $text = preg_replace('/(giá|bao nhiêu|chi phí|price|giá cả|giá tiền|của|ở|tại|sân|cơ\s*sở)/iu', '', $text);

        // Chuẩn hóa khoảng trắng
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        // Kiểm tra độ dài tối thiểu (ít nhất 3 ký tự) và có chứa chữ cái
        if (strlen($text) >= 3 && preg_match('/[a-zA-ZÀ-ỹ]/u', $text)) {
            return $text;
        }

        return null;
    }

    public function extractDuration($text)
    {
        $text = mb_strtolower($text);

        // Trường hợp: 1.5 tiếng, 2 giờ, 1h
        if (preg_match('/(\d+([\.,]\d+)?)\s*(tiếng|giờ|h)/', $text, $m)) {
            return (float) str_replace(',', '.', $m[1]);
        }

        // Trường hợp: 1h30p, 1 giờ 30 phút
        if (preg_match('/(\d+)\s*(h|giờ|tiếng)\s*(\d+)/', $text, $m)) {
            return (float) $m[1] + ($m[3] / 60);
        }

        return null; // Mặc định không tìm thấy
    }
}