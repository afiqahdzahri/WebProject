<div class="container-fluid">
    <div class="row">
        <!-- Department Formation -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <?php
                    $dept_form_count = $conn->query("SELECT id FROM department_formation")->num_rows;
                    ?>
                    <h3><?php echo $dept_form_count ?></h3>
                    <p>Department Formations</p>
                </div>
                <div class="icon">
                    <i class="fas fa-sitemap"></i>
                </div>
                <a href="index.php?page=department_formation" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- KPI Metrics -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <?php
                    $kpi_count = $conn->query("SELECT id FROM kpi_metrics")->num_rows;
                    ?>
                    <h3><?php echo $kpi_count ?></h3>
                    <p>KPI Metrics</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <a href="index.php?page=kpi_metrics" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Evaluation Reports -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <?php
                    $report_count = $conn->query("SELECT id FROM evaluation_reports")->num_rows;
                    ?>
                    <h3><?php echo $report_count ?></h3>
                    <p>Evaluation Reports</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <a href="index.php?page=evaluation_reports" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
</div>

<style>
.small-box {
    border-radius: 0.25rem;
    box-shadow: 0 0 1px rgba(0,0,0,0.125), 0 1px 3px rgba(0,0,0,0.2);
    display: flex;
    flex-direction: column;
    margin-bottom: 1rem;
    min-height: 150px;
    position: relative;
    background: #fff;
}

.small-box > .inner {
    padding: 10px;
    color: #fff;
}

.small-box > .small-box-footer {
    position: relative;
    text-align: center;
    padding: 3px 0;
    color: rgba(255, 255, 255, 0.8);
    display: block;
    z-index: 10;
    background: rgba(0, 0, 0, 0.1);
    text-decoration: none;
}

.small-box h3 {
    font-size: 2.2rem;
    font-weight: bold;
    margin: 0 0 10px 0;
    white-space: nowrap;
    padding: 0;
}

.small-box p {
    font-size: 1rem;
}

.small-box .icon {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 0;
    font-size: 70px;
    color: rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

.small-box:hover .icon {
    transform: scale(1.1);
}

.bg-info {
    background-color: #17a2b8 !important;
}

.bg-success {
    background-color: #28a745 !important;
}

.bg-warning {
    background-color: #ffc107 !important;
}

.bg-danger {
    background-color: #dc3545 !important;
}
</style>