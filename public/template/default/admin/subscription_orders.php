<?php
$page_title = "subscriptions Orders";
include 'includes/header.php'; ?>


<!-- BEGIN: Content-->
<div class="app-content content">
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 mb-2">
        <?php include 'includes/breadcrumb.php'; ?>

        <h3 class="content-header-title mb-0">subscriptions Orders</h3>
      </div>


      <div class="content-header-right col-md-6 col-12">
        <div class="btn-group float-right" role="group" aria-label="Button group with nested dropdown">
          <a class="btn btn-outline-primary" href="Javascript:void(0);">
            Total:<?= $total_amount; ?>
          </a>
          <a class="btn btn-outline-primary" href="Javascript:void(0);">
            Sub Total :<?= $shown_total_amount; ?>
          </a>
        </div>
      </div>
    </div>
    <div class="content-body">

      <section id="video-gallery" class="card">
        <div class="card-header">
          <?php include_once 'template/default/composed/filters/subscription_orders.php'; ?>
          <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
          <div class="heading-elements">
            <small class="float-right"><?= $note; ?> </small>
          </div>
        </div>
        <div class="card-content table-responsive">


          <table id="" class="table table-striped">
            <thead>
              <tr>
                <th>#Ref</th>
                <th>User</th>
                <th style="width:230px;">Plan</th>
                <th>Status</th>
                <!-- <th>Action</th> -->
              </tr>
            </thead>
            <tbody>
              <?php foreach ($subscription_orders as $order) :
                $subscriber = $order->user;
              ?>
                <tr>
                  <td>
                    <?= $order->id; ?><br>
                    <?= $order->TransactionID; ?><br>
                    <span class="badge badge-primary">
                      <?= date("M j, Y h:iA", strtotime($order->created_at)); ?></span>
                    <br>
                    <?= $order->paymentstatus; ?>

                  </td>
                  <td><?= $subscriber->DropSelfLink; ?>
                    <br> units: <?= $order->units ?? 0; ?>
                    <br> usages: <?= $order->PlanUsages; ?>
                    <br> type: <?= $order->type; ?>
                    <br> expires at: <span class="badge badge-primary"><?= date("M j, Y h:iA", strtotime($order->expires_at)); ?></span>

                  </td>
                  <td>
                    <pre>
                    <?php echo json_encode($order->order_detail(), JSON_PRETTY_PRINT); ?>
                </pre>

                  </td>
                  <td>

                    <a href="javascript:void;" class="dropdown-ite btn-block btn btn-dark btn-sm" onclick="$confirm_dialog = 
                          new ConfirmationDialog('<?= domain; ?>/admin/confirm_payment/<?= $order->id; ?>')" class="btn btn-primary btn-xs">
                      Confirm Payment
                    </a>

                    <a href="javascript:void;" class="dropdown-ite btn-block btn btn-dark btn-sm" onclick="$confirm_dialog = 
                          new ConfirmationDialog('<?= domain; ?>/admin/mark_subscription_unpaid/<?= $order->id; ?>')" class="btn btn-primary btn-xs">
                      Mark unpaid
                    </a>

                    <a class="dropdown-ite btn-block btn btn-dark btn-sm" href="<?= domain; ?>/admin/conversion_logs?bill_id=<?= $order->id; ?>">View Usage</a>

                    <form id="payment_proof_form<?= $order->id; ?>" action="<?= domain; ?>/user/upload_payment_proof/<?= $order->id; ?>" method="post" enctype="multipart/form-data">
                      <input style="display: none" type="file" onchange="document.getElementById('payment_proof_form<?= $order->id; ?>').submit();" id="payment_proof_input<?= $order->id; ?>" name="payment_proof">
                      <input type="hidden" name="order_id" value="<?= $order->id; ?>">
                    </form>

                    <?php if ($order->payment_proof != null) : ?>
                      <a class="dropdown-ite btn-block btn btn-dark btn-sm" target="_blank" href="<?= domain; ?>/<?= $order->payment_proof; ?>">See Proof</a>
                    <?php endif; ?>

                    <br>
                    <?php if (!$order->is_paid()) : ?>
                      <form id="fo<?= $order->id; ?>" class="ajax_form" action="<?= $order->reverifyLink; ?>" method="post">
                        <button type="submit" class="dropdown-ite btn-block btn btn-dark btn-sm">
                          Query
                        </button>
                      </form>
                    <?php endif; ?>

                  </td>
                </tr>
              <?php endforeach; ?>

            </tbody>
          </table>

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