<?php
$page_title = "Testimonials";
include_once 'includes/header.php'; ?>
<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
    <div>

        <?php include_once 'includes/breadcrumb.php'; ?>

        <h4 class="mg-b-0 tx-spacing--1">Testimonials</h4>
    </div>
    <div class="d-none d-md-block">
<!--         <button class="btn btn-sm pd-x-15 btn-white btn-uppercase"><i data-feather="save" class="wd-10 mg-r-5"></i> Save</button>
        <button class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-l-5"><i data-feather="upload" class="wd-10 mg-r-5"></i> Export</button>
        <button class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-l-5"><i data-feather="share-2" class="wd-10 mg-r-5"></i> Share</button>
 -->        

    <a  href="<?= domain; ?>/user/create_testimonial" class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-l-5"><i data-feather="bookmark" class="wd-10 mg-r-5"></i> Add Testimonial</a>
    </div>
</div>

<div class="row row-xs">



        <?php foreach ($testimonials as $testimony) :
        ?>

            <div class="ticket-thread col-12">
                <div class="ticket-reply">

                    <div class="ticket-reply-content">
                        <a title="Edit Testimonial" class="attachment" href="<?=domain;?>/user/edit_testimony/<?=$testimony->id;?>">
                        #<?=$testimony->id;?>
                         <!-- <?=$testimony->type;?> -->
                             
                         </a>
                        &nbsp;

                       <?=$testimony->content;?>
                       <a  href="<?=$testimony->video_link;?>"><?=$testimony->video_link;?></a>

                    </div>
                    <div class="ticket-attachments">
                    <small><?=date("M j Y H:iA", strtotime($testimony->created_at));?></small>
                    <a class="attachment" href="#">
                    <!-- <?=$testimony->DisplayStatus;?> -->
                    <!-- <?=$testimony->DisplayPublishedStatus;?> -->
                    
                    </a></div>

                    <hr>
                </div>

            </div>


        <?php endforeach; ?>



    </div>



    <?php if ($testimonials->isEmpty()) : ?>
        <center>Your testimonials will show here</center>
    <?php endif; ?>




</div><!-- row -->
<?php include_once 'includes/footer.php'; ?>