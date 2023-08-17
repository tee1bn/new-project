<?php include 'inc/headers.php'; ?>
<?php
$involved_accounts = $journal->involved_accounts;
$total_credit = $involved_accounts->sum('credit');
$total_debit = $involved_accounts->sum('debit');; ?>

<div class="container">
	<div class="row">

		<div class="col-md-12">

			<div class="col-md-12">
				<div class="btn-group">
					<?php if (!$journal->is_published()) : ?>
						<a href="<?= $journal->editLink; ?>" type="button" class="btn btn-default">
							<i class="fa fa-pencil"></i>
						</a>
					<?php endif; ?>
					<div class="btn-group">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							<i class="fa fa-paperclip"></i> <span class="caret"></span></button>
						<ul class="dropdown-menu" role="menu">
							<?php foreach ($journal->attachments as $file) :
								$filename = end(explode('/', $file));
							?>
								<li><a target="_blank" href="<?= domain; ?>/<?= $file; ?>"><?= $filename; ?></a></li>
							<?php endforeach; ?>

						</ul>
					</div>
				</div>
				<hr>
				<div class="row">

					<div class="col-xs-12 col-md-6 ">
						<div class="panel panel-default height">
							<div class="panel-heading"><?= $journal->publishedState; ?></div>
							<div class="panel-body">
								<strong>Created By:</strong> <?= $journal->accountant->fullname; ?> at <?= $journal->created_at->format('M j, Y h:iA'); ?><br>
								<strong>Notes:</strong> <?= $journal->notes; ?><br>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-md-6  pull-right">
						<div class="panel panel-default height">
							<div class="panel-heading">Journal #<?= $journal->id; ?></div>
							<div class="panel-body">
								<strong>Date:</strong> <?= $journal->journal_date; ?><br>
								<strong>Amount:</strong>
								<?= $currency; ?><?= $this->money_format($total_credit); ?>
								<br>
								<!-- <strong>Reference Number:</strong> <?= $journal->reference; ?><br> -->
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<!-- <h3 class="text-center"><strong>Order summary</strong></h3> -->
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<td><strong>Account</strong></td>
										<td class="text-center"><strong>Tax</strong></td>
										<td class="text-center"><strong>Debits</strong></td>
										<td class="text-right"><strong>Credits</strong></td>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($involved_accounts as  $account) : ?>
										<tr>
											<td><strong><?= $account->chart_of_account->account_name; ?></strong>
												<p><?= $account->description; ?></p>
											</td>
											<td class="text-center"></td>
											<td class="text-center"> <?= $this->money_format($account->debit); ?></td>
											<td class="text-right"> <?= $this->money_format($account->credit); ?></td>
										</tr>
									<?php endforeach; ?>

									<tr>
										<td class="highrow"></td>
										<td class="highrow text-center"><strong>Subtotal</strong></td>
										<td class="highrow text-center"><?= $this->money_format($total_debit); ?></td>
										<td class="highrow text-right"><?= $this->money_format($total_credit); ?></td>
									</tr>

									<tr>
										<td class="emptyrow"></td>
										<td class="emptyrow text-center"><strong>Total</strong></td>
										<td class="emptyrow text-center">
											<?= $currency; ?><?= $this->money_format($total_debit); ?></td>
										<td class="emptyrow text-right">
											<?= $currency; ?><?= $this->money_format($total_credit); ?></td>
									</tr>


								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<style>
				.height {
					min-height: 200px;
				}

				.icon {
					font-size: 47px;
					color: #5CB85C;
				}

				.iconbig {
					font-size: 77px;
					color: #5CB85C;
				}

				.table>tbody>tr>.emptyrow {
					border-top: none;
				}

				.table>thead>tr>.emptyrow {
					border-bottom: none;
				}

				.table>tbody>tr>.highrow {
					border-top: 3px solid;
				}
			</style>






		</div>
	</div>
</div>



<?php include 'inc/footers.php'; ?>