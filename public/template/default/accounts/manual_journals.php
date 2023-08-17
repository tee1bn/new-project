<?php

 include 'inc/headers.php';?>

		<script src="<?=$this_folder;?>/assets/angularjs/manual_journals.js"></script>


	<div  ng-controller="ManualJournalController">		
		<div class="container" >


					<div class="col-xs-8">
							<h3>Manual Journals</h3>
					<?php if ($from !=null):?>
							<small>Showing <?=$journals->count();?> of <?=$total_journals;?> Journals</small>
								<br>

								<small><?=(date("M j, Y", strtotime($from)));?>  to <?=(date("M j, Y", strtotime($to)));?> </small>

					<?php else:?>
						<small>Showing All of <?=$total_journals;?> Journals</small>
						<br>
						 <!-- customise the report -->

					<?php endif;?>


					</div>
					<div id="customise_report" class="modal fade " style="display: ;" role="dialog">
						<div class="modal-dialog">

						    <!-- Modal content-->
						    <div class="modal-content">
						      <div class="modal-header">
						        <button type="button" class="close" data-dismiss="modal">&times;</button>
						        <h4 class="modal-title">Customise Report</h4>
						      </div>
						      <div class="modal-body">
						      	<form id="customise_report"
						      	 action="<?=domain;?>/journals/set_journals_list_filters">  
						      	
						      		<div class="form-group">
						      			<label>From</label>
						      			<input type="date" value="<?=$from;?>" name="from" class="form-control"> 
						      			<label>To</label>
						      			<input type="date" name="to" value="<?=$to;?>" class="form-control"> 
						      		</div>


						      		<div class="form-group">
						      			<label>Per Page</label>
						      			<input type="number" value="<?=$per_page;?>" name="per_page" class="form-control"> 
						      			
						      		</div>


								      <div class="modal-footer">
								        <button type="submit" class="btn btn-danger" >Submit</button>
								        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
								      </div>
						      	</form>
							  </div>
						  	</div>
						</div>
					</div>
					<div class="col-xs-4">
						<div class="btn-group">
							  <a href="<?=domain;?>/journals/new" class="btn btn-danger" >
							  + New Journal
								</a>


								 <a href="javascript:void;"  data-toggle="modal" data-target="#customise_report" class="btn btn-default" >
								  <i class="fa fa-cog"></i> Customise Report
								</a>


						
						</div>
					</div>

					<table id="charts_of_accounts_table" class="table table-hover">
					    <thead>
					      <tr>
					        <th>DATE</th>
					        <th>JOURNAL#</th>
					        <th>STATUS</th>
					        <th>REFERENCE NUMBER</th>
					        <th>AMOUNT (<?=$currency;?>)</th>
					        <th>NOTES</th>
					        <th>ATTACHMENTS</th>
					        <th>ACTIONS</th>
					      </tr>
					    </thead>
					    <tbody>


			 				<?php foreach ($journals as $journal) :?>
							    <tr>
							        <td><?=$journal->created_at->toFormattedDateString();?></td>
							        <td><?=$journal->id;?></td>
							        <td><?=$journal->publishedState;?></td>
							        <td><?=$journal->reference;?></td>
							        <td><?=$this->money_format($journal->amount);?></td>
							        <td><?=$journal->notes;?></td>
							        <td> 
							        	 <div class="btn-group">
					    					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					    						<i class="fa fa-paperclip"></i> <span class="caret"></span></button>
					    						<ul class="dropdown-menu" role="menu">
					      							<?php foreach ($journal->attachments as $file):
					      								$filename = end(explode('/', $file));
					      								?>
														<li><a target="_blank" href="<?=domain;?>/<?=$file;?>"><?=$filename;?></a></li>
													<?php endforeach ;?>
												
					    						</ul>
					  						</div>
					  				</td>
					  				<td>
							        	 <div class="btn-group">
					    					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					    						<span class="caret"></span></button>
					    						<ul class="dropdown-menu" role="menu">
					      							<?php if (! $journal->is_published()) :?>
														<li><a href="<?=$journal->editLink;?>">Edit</a></li>
					      							<?php endif ;?>
													<li><a href="<?=$journal->viewLink;?>">View</a></li>
												
					    						</ul>
					  						</div>
					  				</td>
						      	</tr>	

						    <?php endforeach ;?>

					    </tbody>
					</table>

					<div>
						<nav class="pagination">
							<?=$this->pagination_links($total_journals, $per_page);?>
						</nav>
					</div>




					<!-- Modal -->
					<div id="create_chart_of_account" class="modal fade " style="display: ;" role="dialog">
					  <div class="modal-dialog">

					    <!-- Modal content-->
					    <div class="modal-content">
					      <div class="modal-header">
					        <button type="button" class="close" data-dismiss="modal">&times;</button>
					        <h4 class="modal-title">Create Account</h4>
					      </div>
					      <div class="modal-body">
					      	<form id="create_chart_of_account_form">  
					      		<div class="form-group">
					      			<label>Account Type *</label>
					      			<select name="account_type" class="form-control">

					      				<?php foreach ($company_account_types as $basic_account_id => $custom_types) :
					      					$base_category = BasicAccountType::find($basic_account_id);
					      					?>

					      					<optgroup  label="<?=$base_category->name;?>">
					      						<?php foreach ($custom_types as $key => $custom_type) :?>

					      							<option value="<?=$custom_type['id'];?>"><?=$custom_type['name'];?></option>
							      				
					      						<?php endforeach;?>
					      					</optgroup>		      					
					      				<?php endforeach;?>

					      			</select>
					      		</div>

					      		<div class="form-group">
					      			<label>Account Name*</label>
					      			<input type="" name="account_name" required="" class="form-control"> 
					      		</div>


					      		<div class="form-group">
					      			<label>Make this a sub-account</label>
					      			<input type="checkbox" name="is_subaccount" class=""> 
					      		</div>

					      		<div class="form-group">
					      			<label>Account Code*</label>
					      			<input type="" name="account_code" class="form-control"> 
					      		</div>

					      		<div class="form-group">
					      			<label>Opening Balance*</label>
					      			<input type="number" step="0.01" name="opening_balance" required="" class="form-control"> 
					      		</div>

					      		<div class="form-group">
					      			<label>Description *</label>
					      			<textarea class="form-control" rows="3" name="description"></textarea>
					      		</div>


					      		<div class="form-group">
					      			<label>Add to the watchlist on my Dashboard</label>
					      			<input type="checkbox" name="add_to_watch_list" class=""> 
					      		</div>		      	


							      <div class="modal-footer">
							        <button type="submit" class="btn btn-danger" >Save</button>
							        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
							      </div>
					      	</form>
						  </div>
					  </div>
					</div>








				<script>

				 	$("body").on("submit", "#create_chart_of_account_form", function (e) {
				 	 e.preventDefault();


						dataString = $("#create_chart_of_account_form").serialize() ;

					        $.ajax({
					            type: "POST",
					            url: $base_url+"/accounts/create_chart_of_accounts/",
					            data: dataString,
					            cache: false,
					            success: function(data) {
									console.log(data);

									if (typeof(data) == 'object') {
										$scope = angular.element($('#charts_of_accounts_table')).scope();
										$scope.$charts_of_accounts.push(data);
										$scope.$apply();
									}
									window.notify();
					      
					            },
					            error: function (data) {
					            }
					        });
					});
				</script>

	</div>




















<?php include 'inc/footers.php';?>


