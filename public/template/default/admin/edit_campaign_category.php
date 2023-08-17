<?php
$page_title = "Edit Campaign Category";
include 'includes/header.php'; ?>


<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 mb-2">
                <?php include 'includes/breadcrumb.php'; ?>

                <h3 class="content-header-title mb-0">Edit Campaign Category <?= $category->DisplayStatus; ?></h3>
            </div>


            <div class="content-header-right col-md-6 col-12">
                <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
                    <div class="btn-group" role="group">
                        <!--   <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> Settings</button>
               <div class="dropdown-menu" aria-labelledby="btnGroupDrop1"><a class="dropdown-item" href="card-bootstrap.html">Bootstrap Cards</a><a class="dropdown-item" href="component-buttons-extended.html">Buttons Extended</a></div> -->
                    </div>
                    <!-- <a class="btn btn-outline-primary" href="full-calender-basic.html"><i class="ft-mail"></i></a> -->
                    <a class="btn btn-outline-primary" href="<?= domain; ?>/admin/campaigns_categories"> All Categories</a>
                </div>
            </div>
        </div>
        <div class="content-body">

            <section id="video-gallery" class="card">

                <div class="card-content">
                    <div class="card-body">

                        <form class="ajax_for" action="<?= domain; ?>/category_crud/update_category/<?= $category->id; ?>" method="post">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="" class="form-control" name="title" required="" value="<?= $category->title; ?>">
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" name="description" required=""><?= $category->description; ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Query</label>
                                <textarea class="form-control" name="sql_query" required=""><?= $category->sql_query; ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Bind</label>
                                <textarea class="form-control" name="bind" required=""><?php echo json_encode($category->BindsArray, JSON_PRETTY_PRINT); ?></textarea>
                            </div>


                            <div class="form-group">
                                <button class="btn btn-dark" type="submit">Save</button>
                            </div>
                        </form>



                        <script>
                            tinymce.init({
                                selector: '.editor1',
                                height: "580",
                                theme: "silver",
                                relative_urls: false,
                                remove_script_host: false,
                                convert_urls: true,
                                statusbar: false,
                                plugins: [
                                    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                                    "searchreplace wordcount visualblocks visualchars code fullscreen",
                                    "insertdatetime media nonbreaking save table contextmenu directionality",
                                    "emoticons template paste textcolor colorpicker textpattern responsivefilemanager"
                                ],
                                toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
                                toolbar2: "| responsivefilemanager print preview media | forecolor backcolor emoticons",
                                setup: function(editor) {
                                    editor.on('change', function(e) {
                                        editor.save();
                                    });
                                }

                            });


                            submit_campaign = function($data) {
                                $form = $('#campaign_form');
                                $form.attr('action', $data);
                                $('#submit_btn').click();
                            }
                        </script>

                    </div>
                </div>
            </section>


        </div>
    </div>
</div>
<!-- END: Content-->

<?php include 'includes/footer.php'; ?>
use CampaignCategory;