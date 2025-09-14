<?php
session_start();
include('db_connect.php');
require_once 'ai_functions.php';

// Ensure only evaluator has access
if ($_SESSION['login_type'] != 'evaluator') {
    header("Location: index.php");
    exit;
}

// Fetch all employees
$employees_qry = $conn->query("SELECT id, firstname, lastname, email FROM employee_list ORDER BY firstname ASC");

?>
<div class="container-fluid">
    <h3 class="mb-4"><i class="fas fa-robot"></i> AI Insights - Evaluator View</h3>

    <div class="card shadow-lg">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Employee</th>
                            <th>Email</th>
                            <th>Prediction (%)</th>
                            <th>Sentiment (%)</th>
                            <th>Last Analyzed</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($emp = $employees_qry->fetch_assoc()): ?>
                            <?php 
                                $ai_insights = getAIAnalysis($emp['id']); 
                            ?>
                            <tr>
                                <td><?php echo $emp['firstname'] . ' ' . $emp['lastname']; ?></td>
                                <td><?php echo $emp['email']; ?></td>
                                <td><strong><?php echo round($ai_insights['prediction'], 2); ?>%</strong></td>
                                <td><?php echo round($ai_insights['sentiment_score'] * 100, 1); ?>%</td>
                                <td><?php echo $ai_insights['last_analyzed']; ?></td>
                                <td>
                                    <a href="index.php?page=employee_detail&id=<?php echo $emp['id']; ?>" 
                                       class="btn btn-sm btn-primary">
                                       View Details
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 12px;
}
.table th, .table td {
    vertical-align: middle !important;
}
</style>
