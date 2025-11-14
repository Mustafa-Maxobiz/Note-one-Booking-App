<?php
// Add this to your Booking model or create a service
class ZoomMeetingIdValidator {
    public static function validate($zoomId) {
        // Check if null or empty
        if (empty($zoomId)) {
            return ["valid" => false, "error" => "Zoom meeting ID is required"];
        }
        
        // Check if numeric
        if (!is_numeric($zoomId)) {
            return ["valid" => false, "error" => "Zoom meeting ID must be numeric"];
        }
        
        // Check if positive
        if ($zoomId < 0) {
            return ["valid" => false, "error" => "Zoom meeting ID cannot be negative"];
        }
        
        // Check minimum length (Zoom IDs are usually 8+ digits)
        if (strlen($zoomId) < 8) {
            return ["valid" => false, "error" => "Zoom meeting ID seems too short"];
        }
        
        return ["valid" => true, "error" => null];
    }
    
    public static function testAgainstZoom($zoomId) {
        $zoomService = new \App\Services\ZoomService();
        $recordings = $zoomService->getMeetingRecordings($zoomId);
        
        if ($recordings === null) {
            return ["valid" => false, "error" => "Zoom API call failed"];
        }
        
        if (isset($recordings["code"]) && $recordings["code"] == 3301) {
            return ["valid" => false, "error" => "Recording does not exist on Zoom"];
        }
        
        if (isset($recordings["recording_files"]) && count($recordings["recording_files"]) > 0) {
            return ["valid" => true, "error" => null];
        }
        
        return ["valid" => false, "error" => "No recordings found for this meeting"];
    }
}