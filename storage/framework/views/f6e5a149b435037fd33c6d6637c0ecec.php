

<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('page-header'); ?>
    <div class="page-pretitle">Overview</div>
    <h2 class="page-title">Dashboard</h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-actions'); ?>
    
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row row-deck row-cards">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Kas</div>
                    </div>
                    <div class="h1 mb-3" id="total-cash">Rp 0</div>
                    <div class="d-flex mb-2">
                        <div class="flex-fill">
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary" style="width: 75%" role="progressbar"></div>
                            </div>
                        </div>
                        <div class="ms-2">
                            <small class="text-muted">75%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Bank</div>
                    </div>
                    <div class="h1 mb-3" id="total-bank">Rp 0</div>
                    <div class="d-flex mb-2">
                        <div class="flex-fill">
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success" style="width: 60%" role="progressbar"></div>
                            </div>
                        </div>
                        <div class="ms-2">
                            <small class="text-muted">60%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Pendapatan Bulan Ini</div>
                    </div>
                    <div class="h1 mb-3" id="monthly-revenue">Rp 0</div>
                    <div class="d-flex mb-2">
                        <div class="flex-fill">
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-info" style="width: 45%" role="progressbar"></div>
                            </div>
                        </div>
                        <div class="ms-2">
                            <small class="text-muted">45%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Beban Bulan Ini</div>
                    </div>
                    <div class="h1 mb-3" id="monthly-expense">Rp 0</div>
                    <div class="d-flex mb-2">
                        <div class="flex-fill">
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-warning" style="width: 30%" role="progressbar"></div>
                            </div>
                        </div>
                        <div class="ms-2">
                            <small class="text-muted">30%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\akuntansi\resources\views/dashboard/index.blade.php ENDPATH**/ ?>