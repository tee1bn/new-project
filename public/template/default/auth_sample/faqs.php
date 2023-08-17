<?php
$page_title = "Frequently Asked Questions";
include_once 'includes/header.php'; ?>


<?php include_once 'includes/sidebar.php'; ?>




<div class="content-w">
    <?php include_once 'includes/topbar.php'; ?>


    <!-- <div class="content-panel-toggler"><i class="os-icon os-icon-grid-squares-22"></i><span>Sidebar</span></div> -->
    <div class="content-i">
        <div class="content-box">
            <div class="row">
                <div class="col-sm-12">
                    <div class="element-wrapper">
                        <div class="element-actions">

                        </div>
                        <h6 class="element-header">Frequently Asked Questions</h6>
                        <div class="element-content">
                            <div class="row">


                                


                    <?php 

                    $faqs = SiteSettings::where('criteria','faqs')->first()->settingsArray;

                      $i=1;
                      foreach ($faqs as $key => $faq) :
                      if ((@$faq['question'] == '') || ($faq['answer'] == '') ) {continue;}
                      ?>


                    <div id="heading11" role="tab" class="element-box col-md-12 card-header border-bottom-blue-grey border-bottom-lighten-2">
                      <a data-toggle="collapse" data-parent="#accordionWrap1" href="#accordion1<?=$key;?>" aria-expanded="true" aria-controls="accordion1<?=$key;?>" class="h6 "> #<?=$i++;?> &nbsp;&nbsp;&nbsp; <?=$faq['question'];?> </a>
                    </div>
                    <div id="accordion1<?=$key;?>" role="tabpanel" aria-labelledby="heading11" class="collapse " aria-expanded="true">
                      <div class="card-body">
                        <p style="margin-left: 30px;" class="card-text"> <?=$faq['answer'];?></p>
                      </div>
                    </div>
                  <?php endforeach;?>

                                      
                            </div>
                        </div>
                    </div>
                </div>
            </div>
         

            <?php include_once 'includes/customiser.php'; ?>
            
        </div>
        
        <?php include_once 'includes/quick_links.php'; ?>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
