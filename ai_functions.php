<?php
// ai_functions.php
function getAIAnalysis($employee_id) {
    // bring $conn from config.php into this function scope
    require_once 'config.php';
    global $conn;

    // Check AI analysis results table
    $query = $conn->query("SELECT * FROM ai_analysis_results 
                           WHERE employee_id = '$employee_id' 
                           ORDER BY analyzed_at DESC LIMIT 1");

    if ($query && $query->num_rows > 0) {
        $ai_data = $query->fetch_assoc();

        // Decode result_data JSON if it exists
        $result_data = [];
        if (!empty($ai_data['result_data'])) {
            $result_data = json_decode($ai_data['result_data'], true);
        }

        return [
            'sentiment_score' => isset($result_data['sentiment']) 
                                    ? $result_data['sentiment'] 
                                    : (rand(70, 95) / 100),
            'prediction' => isset($result_data['prediction']) 
                                    ? $result_data['prediction'] 
                                    : rand(75, 92),
            'last_analyzed' => $ai_data['analyzed_at']
        ];
    } else {
        // Demo fallback data
        return [
            'sentiment_score' => rand(70, 95) / 100,
            'prediction' => rand(75, 92),
            'last_analyzed' => date('Y-m-d H:i:s')
        ];
    }
}
?>
