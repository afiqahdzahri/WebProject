<?php
// ai_insights.php
require_once 'config.php';
require_once 'ai_functions.php';

// Ensure employee is logged in
require_login();

// Get logged-in employee ID
$emp_id = $_SESSION['login_id'];

// Fetch AI insights
$ai_insights = getAIAnalysis($emp_id);
?>

<div class="container-fluid">
    <h2 class="mb-4">AI Performance Insights</h2>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="card-title"></h4>
            <p><strong>Predicted Performance:</strong> 
                <?php echo round($ai_insights['prediction'], 2); ?>%
            </p>
            <p><strong>Sentiment Score:</strong> 
                <?php echo round($ai_insights['sentiment_score'] * 100, 1); ?>%
            </p>
            <p><strong>Last Analyzed:</strong> 
                <?php echo $ai_insights['last_analyzed']; ?>
            </p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="card-title">Detailed Analysis: </h4>
            <?php if (!empty($ai_insights['details'])): ?>
                <ul>
                    <?php foreach ($ai_insights['details'] as $key => $value): ?>
                        <li><strong><?php echo ucfirst($key); ?>:</strong> <?php echo htmlspecialchars($value); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No detailed insights available yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <a href="index.php" class="btn btn-primary mt-3">Back to Dashboard</a>
</div>
