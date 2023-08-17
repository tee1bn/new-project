<?php

 include 'inc/headers.php';?>
 <style>
 	textarea{
 		resize: vertical;
 	}
 </style>
 <script>
 	$journal_id = <?=$journal->id;?>
 </script>

		<script src="<?=$this_folder;?>/assets/angularjs/journal.js"></script>

		<div class="container" ng-controller="JournalController">			

			<div class="row" >

				<div class="col-xs-8">
						<h3>New Journals</h3>
				</div>
			</div>
			<hr>
			<form class="col-md-8">

				<div class="form-group ">
					<label>Date*</label>
					<input type="date" class="form-control" ng-model="$journal.$data.journal_date">
				</div>

				<div class="form-group ">
					<label>Journal#*</label>
					<input type="text" readonly="" class="form-control" ng-model="$journal.$data.id">
				</div>

				<!-- <div class="form-group ">
					<label>Reference#</label>
					<input type="text" class="form-control" ng-model="$journal.$data.reference">
				</div> -->

				<div class="form-group ">
					<label>Notes*</label>
					<textarea  class="form-control" ng-model="$journal.$data.notes"></textarea>
				</div>

				<div class="form-group ">
					<label>Journal Type*</label>
					<input type="checkbox"  ng-true-value="'Cash based journal'" ng-model="$journal.$data.journal_type"> Cash based journal
				</div>

			<!-- 	<div class="form-group ">
					<label>Currency</label>
					<select class="form-control">
						<option value="">NGN -Naira</option>
					</select>
				</div> -->


			</form>
			<div class="col-md-4">
				<ul>Attached File(s) <span class="badge"><?=count($journal->attachments);?></span>
						      		<?php foreach ($journal->attachments as $file):
	      								$filename = end(explode('/', $file));
	      								?>
										<li><a target="_blank" href="<?=domain;?>/<?=$file;?>"><?=$filename;?></a></li>
									<?php endforeach ;?>


			</ul>
			</div>

			<div class="col-md-12">
				<table class="table table-hover ">
					<thead>
						<th>Account</th>
						<th>Description</th>
						<!-- <th>Contact (NGN)</th> -->
						<th>Tax</th>
						<th>Debits</th>
						<th>Credits</th>
						<th>*</th>
					</thead>

					<tr ng-repeat="($index , $involved_account) in $journal.$involved_accounts.$lines">
						
						<td>
							<select ng-change="$journal.$involved_accounts.set_credit_and_debit_limit($involved_account);" 
							 ng-model="$involved_account.chart_of_account_id" class="form-control">
									<optgroup ng-repeat="(key, $optgroup) in $charts_of_account_options"  label="{{key}}">

											<option ng-repeat="($index, $option ) in $optgroup"
												value="{{$option.id}}" 
												ng-selected= "$involved_account.chart_of_account_id==$option.id">
												{{$option.account_name}}<br>
												{{$option.id}}<br>
												{{$involved_account.chart_of_account_id}}
											</option>

										
									</optgroup>
							</select>
						</td>



						<td>
							<textarea  rows="3" ng-model="$involved_account.description" class="form-control">{{$involved_account.description}}</textarea>
						</td>
						<!-- <td>
							<select class="form-control">
								<option value="">Select Contact</option>
							</select>
						</td> -->
						<td>
							<select class="form-control">
								<option value="">Select a Tax</option>
							</select>
						</td>
						
						<td>
							<input type="" ng-blur="$journal.$involved_accounts.sumCreditAndDebit();" ng-model="$involved_account.debit" class="form-control">
						</td>

						<td><input type="" ng-blur="$journal.$involved_accounts.sumCreditAndDebit();" ng-model="$involved_account.credit" class="form-control"></td>



						<td>
							<a ng-click="$journal.$involved_accounts.remove_line($involved_account);" href="javascript:void;" class="fa fa-times text-danger"></a>
						</td>
					</tr>

					<tr>
						<td colspan="2">
							<button ng-click="$journal.$involved_accounts.add_line();"
							 class="btn btn-default text-danger">+ Add Another Line</button>
						</td>
						
						<td colspan ="5">
							<table class="table table-hover">
								<tr>
									<td>Sub Total</td>
									<td>{{$journal.$involved_accounts.$subtotal_debit}}</td>
									<td>{{$journal.$involved_accounts.$subtotal_credit}}</td>
								</tr>

								<tr>
									<td>Total(NGN)</td>
									<td>{{$journal.$involved_accounts.$total_debit}}</td>
									<td>{{$journal.$involved_accounts.$total_credit}}</td>
								</tr>



							</table>

						
						</td>
				
					</tr>


				
				</table>

				<div>
					<input
					 onchange="angular.element(this).scope().$journal.add_files(this)" 
					 type="file" 
					 multiple=""
					 name="" id="journal_attached_files" style="display: none;">

					Attach File(s) 
					<button onclick="$('#journal_attached_files').click()">Upload File</button> 
					<span>{{$journal.$attached_files.length}} file(s) attached</span>
					<br>
					<small>You can upload a Maximum of 5 files, 5MB each</small>
				</div>

				<hr>
				<div class="row">
					<button ng-click="$journal.save(1)" class="btn btn-primary btn-sm">Save and Publish</button>
					<button ng-click="$journal.save(0)" class="btn btn-default btn-sm">Save as Draft</button>
					<button class="btn btn-default btn-sm">Cancel</button>
				</div>
			</div>

		</div>














<?php include 'inc/footers.php';?>


