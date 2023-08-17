<?php
$page_title = "Edit Testimonial";
include_once 'includes/header.php'; ?>
<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
    <div>

        <?php include_once 'includes/breadcrumb.php'; ?>

        <h4 class="mg-b-0 tx-spacing--1">Edit Testimonial</h4>
    </div>
  <!--   <div class="d-none d-md-block">
        <button class="btn btn-sm pd-x-15 btn-white btn-uppercase"><i data-feather="save" class="wd-10 mg-r-5"></i> Save</button>
        <button class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-l-5"><i data-feather="upload" class="wd-10 mg-r-5"></i> Export</button>
        <button class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-l-5"><i data-feather="share-2" class="wd-10 mg-r-5"></i> Share</button>
        <button class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5"><i data-feather="sliders" class="wd-10 mg-r-5"></i> Settings</button>
    </div> -->
</div>

<div class="row row-xs">
    <div class="col-12">
        
    <?php $accessor = 'user' ; $this->view('composed/edit_testimonial', compact('testimony','accessor'), null, true);?>
    </div>
</div><!-- row -->
<?php include_once 'includes/footer.php'; ?>