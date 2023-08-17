<?php
$page_title = "Campaign Categories";
include 'includes/header.php'; ?>


<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <?php include 'includes/breadcrumb.php'; ?>

                <h3 class="content-header-title mb-0">Campaign Categories</h3>
            </div>


            <div class="content-header-right col-md-6 col-12">
                <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
                    <div class="btn-group" role="group">
                        <!--   <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> Settings</button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1"><a class="dropdown-item" href="card-bootstrap.html">Bootstrap Cards</a><a class="dropdown-item" href="component-buttons-extended.html">Buttons Extended</a></div> -->
                    </div>
                    <!-- <a class="btn btn-outline-primary" href="full-calender-basic.html"><i class="ft-mail"></i></a> -->
                    <a class="btn btn-outline-primary" href="<?= domain; ?>/admin/create_campaign_category">+ New Category</a>
                </div>
            </div>
        </div>
        <div class="content-body">

            <section id="video-gallery" class="card">
                <div class="card-header">
                    <?php include_once 'template/default/composed/filters/campaigns_categories.php'; ?>
                    <h4 class="card-title" style="display: inline;"></h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                </div>
                <div class="card-content">
                    <div class="card-body table-responsive">

                        <table class="table ">
                            <thead>
                                <th>SN</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Rows</th>
                                <th>By/Date</th>
                                <th>Action</th>
                            </thead>
                            <tbody>

                                <?php $i = 1;
                                foreach ($campaigns_categories as $key => $category) : ?>
                                    <tr>
                                        <td><?= $i++; ?></td>
                                        <td><?= $category->title; ?></td>
                                        <td><small><?= $category->description; ?></small></td>
                                        <td><?= $category->rows(); ?></td>
                                        <td>
                                            <?= $category->admin->fullname; ?><br>
                                            <small class="badge badge-dark"><?= date("M j Y h:iA", strtotime($category->created_at)); ?></small>
                                        </td>
                                        <td>

                                            <div class="btn-group btn-group-sm">
                                                <a href="javascript:void;" onclick="$confirm_dialog 
                                            = new ConfirmationDialog('<?= domain; ?>/category_crud/delete_category/<?= $category->id; ?>')" class="btn-xs btn btn-outline-dark">Delete</a>

                                                <a href="<?= domain; ?>/admin/edit_campaign_category/<?= $category->id; ?>" class="btn btn-sm btn-outline-dark">Edit</a>
                                            </div>

                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>


                        <ul class="pagination">
                            <?= $this->pagination_links($data, $per_page); ?>
                        </ul>


                    </div>
                </div>
            </section>


        </div>
    </div>
</div>
<!-- END: Content-->

<?php include 'includes/footer.php'; ?>