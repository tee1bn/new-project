<?php
$page_title = "Adverts";
include 'includes/header.php'; ?>


<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6">
                <?php include 'includes/breadcrumb.php'; ?>

                <h3 class="content-header-title mb-0">Adverts</h3>
            </div>

            <div class="content-header-right col-md-6">
                <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
                    <div class="btn-group" role="group">
                        <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> Settings</button>
                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1"><a class="dropdown-item" href="card-bootstrap.html">Bootstrap Cards</a><a class="dropdown-item" href="component-buttons-extended.html">Buttons Extended</a></div>
                    </div><a class="btn btn-outline-primary" href="full-calender-basic.html"><i class="ft-mail"></i></a><a class="btn btn-outline-primary" href="timeline-center.html"><i class="ft-pie-chart"></i></a>
                </div>
            </div>
        </div>
        <div class="content-body">

            <section id="video-gallery" class="card">
                <div class="card-header">
                    <h4 class="card-title">blank</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    <div class="heading-elements">
                        <ul class="list-inline mb-0">
                            <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                            <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">

                        <table id="" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#Ref</th>
                                    <th>User</th>
                                    <th>StartDate <br> End Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($adverts as $advert) : ?>
                                    <tr>
                                        <td><?= $advert->id; ?><br>
                                            <?= $advert->editor->DropSelfLink; ?>
                                        </td>
                                        <td><?= $advert->getmadeViewAttribute(false); ?></td>
                                        <td>
                                            <span class="badge badge-primary">
                                                <?= date("M j, Y h:iA", strtotime($advert->start_date)); ?></span><br>
                                            <span class="badge badge-primary">
                                                <?= date("M j, Y h:iA", strtotime($advert->end_date)); ?></span>
                                        </td>
                                        <td><?= $advert->PublishedStatus; ?><br><?= $advert->FailedStatus; ?></td>
                                        <td>

                                            <div class="dropdown">
                                                <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                                    <span class="caret"></span></button>


                                                <div class="dropdown-menu">
                                                    <a href="<?= domain; ?>" class="dropdown-item">
                                                        Open Stats
                                                        <!-- appearance.
                                total purchase
                                no of purchase each week of operation
                                those that purchase etc
                                 -->
                                                    </a>
                                                    <!-- <li><a href="#">JavaScript</a></li> -->

                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                            </tbody>
                        </table>


                    </div>
                </div>
            </section>

            <ul class="pagination">
                <?= $this->pagination_links($data, $per_page); ?>
            </ul>


        </div>
    </div>
</div>
<!-- END: Content-->

<?php include 'includes/footer.php'; ?>